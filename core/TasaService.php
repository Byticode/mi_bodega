<?php

/**
 * Tasas de cambio desde la API pública de DolarAPI Venezuela.
 *
 * Estrategia en tres capas, de la más rápida a la más lenta:
 *   1. Caché en disco con TTL — evita salir a la red en cada carga de página.
 *   2. La API — se consulta solo cuando la caché venció.
 *   3. La tabla `tasa_moneda` — respaldo si la API falla o no hay red.
 *
 * Cuando la API devuelve un valor distinto al último guardado se inserta una
 * fila nueva, de modo que el historial siga registrando cambios reales y no
 * una fila por visita.
 */
class TasaService
{
    /** Ninguna tasa razonable queda fuera de este rango; protege de respuestas basura. */
    private const MIN_RAZONABLE = 0.0001;
    private const MAX_RAZONABLE = 100000000;

    private $modelo;
    private $rutaCache;

    public function __construct($modelo = null)
    {
        // El modelo toca la base de datos; si no hay conexión seguimos con API + caché.
        if ($modelo === null) {
            try {
                $modelo = new TasaMoneda();
            } catch (Throwable $e) {
                $modelo = null;
            }
        }

        $this->modelo    = $modelo;
        $this->rutaCache = TASA_CACHE_DIR . '/tasas.json';
    }

    /**
     * Tasa que debe usar la aplicación. Nunca lanza excepciones: si todo falla
     * devuelve una estructura con `tasa_usd` en null y el motivo en `error`.
     */
    public function vigente(): array
    {
        $cache = $this->leerCache();
        if ($cache !== null) {
            return $cache;
        }

        return $this->refrescar();
    }

    /**
     * Consulta la API ignorando la caché. La usa el botón «Actualizar ahora»
     * y también `vigente()` cuando la caché venció.
     */
    public function refrescar(): array
    {
        $tasas = $this->consultarApi();

        if ($tasas !== null) {
            $this->persistir($tasas);
            $this->escribirCache($tasas, TASA_TTL);
            return $tasas;
        }

        // La API falló: respondemos con lo último guardado y marcamos el fallo.
        $respaldo = $this->desdeBd();

        // Caché negativa corta: sin esto, una API caída haría que cada carga de
        // página esperara el timeout completo.
        $this->escribirCache($respaldo, TASA_TTL_ERROR);

        return $respaldo;
    }

    // ─────────────────────────── API ───────────────────────────

    private function consultarApi(): ?array
    {
        $dolares = $this->traer(TASA_API_USD);
        if (!is_array($dolares)) {
            return null;
        }

        $usd_oficial  = $this->extraer($dolares, 'oficial');
        $usd_paralelo = $this->extraer($dolares, 'paralelo');

        // Sin la oficial no hay tasa base: la respuesta no sirve.
        if ($usd_oficial === null) {
            return null;
        }

        // El euro es opcional: si su petición falla, la tasa sigue siendo válida.
        $euros      = $this->traer(TASA_API_EUR);
        $eur_oficial = is_array($euros) ? $this->extraer($euros, 'oficial') : null;

        return [
            'moneda'        => 'Bs',
            'tasa_usd'      => $usd_oficial['valor'],
            'tasa_euro'     => $eur_oficial ? $eur_oficial['valor'] : null,
            'tasa_paralelo' => $usd_paralelo ? $usd_paralelo['valor'] : null,
            'vigente_desde' => $usd_oficial['fecha'],
            'consultada'    => date('c'),
            'origen'        => 'api',
            'error'         => null,
        ];
    }

    /** Saca una fuente ('oficial' / 'paralelo') del arreglo que devuelve la API. */
    private function extraer(array $respuesta, string $fuente): ?array
    {
        foreach ($respuesta as $item) {
            if (!is_array($item) || ($item['fuente'] ?? null) !== $fuente) {
                continue;
            }

            $valor = $item['promedio'] ?? $item['venta'] ?? null;

            if (!is_numeric($valor) || $valor < self::MIN_RAZONABLE || $valor > self::MAX_RAZONABLE) {
                return null;
            }

            return [
                'valor' => round((float) $valor, 6),
                'fecha' => isset($item['fechaActualizacion']) ? (string) $item['fechaActualizacion'] : null,
            ];
        }

        return null;
    }

    /** GET con timeout corto. Devuelve el JSON decodificado o null. */
    private function traer(string $url): ?array
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => TASA_TIMEOUT_CONEXION,
            CURLOPT_TIMEOUT        => TASA_TIMEOUT_TOTAL,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT      => 'mi_bodega/1.0',
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        ]);

        $cuerpo = curl_exec($ch);
        $codigo = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($cuerpo === false || $codigo !== 200) {
            return null;
        }

        $datos = json_decode($cuerpo, true);

        return is_array($datos) ? $datos : null;
    }

    // ─────────────────────────── Persistencia ───────────────────────────

    /** Inserta una fila solo si algún valor cambió respecto al último registro. */
    private function persistir(array $tasas): void
    {
        if ($this->modelo === null) {
            return;
        }

        try {
            $ultima = $this->modelo->obtenerUltima();

            if ($ultima && !$this->cambiaron($ultima, $tasas)) {
                return;
            }

            $this->modelo->crear(
                $tasas['moneda'],
                $tasas['tasa_usd'],
                $tasas['tasa_euro'],
                $tasas['tasa_paralelo']
            );
        } catch (Throwable $e) {
            // Guardar el historial es deseable, no crítico: la tasa ya está en caché.
        }
    }

    private function cambiaron(array $ultima, array $nueva): bool
    {
        foreach (['tasa_usd', 'tasa_euro', 'tasa_paralelo'] as $campo) {
            $antes  = $ultima[$campo] === null ? null : round((float) $ultima[$campo], 4);
            $ahora  = $nueva[$campo]  === null ? null : round((float) $nueva[$campo], 4);

            if ($antes !== $ahora) {
                return true;
            }
        }

        return false;
    }

    /** Última tasa conocida en base de datos, como respaldo. */
    private function desdeBd(): array
    {
        $vacia = [
            'moneda'        => 'Bs',
            'tasa_usd'      => null,
            'tasa_euro'     => null,
            'tasa_paralelo' => null,
            'vigente_desde' => null,
            'consultada'    => date('c'),
            'origen'        => 'ninguna',
            'error'         => 'No se pudo consultar la API de tasas.',
        ];

        if ($this->modelo === null) {
            return $vacia;
        }

        try {
            $fila = $this->modelo->obtenerUltima();
        } catch (Throwable $e) {
            return $vacia;
        }

        if (!$fila) {
            return $vacia;
        }

        return [
            'moneda'        => $fila['moneda'],
            'tasa_usd'      => $fila['tasa_usd'] === null ? null : (float) $fila['tasa_usd'],
            'tasa_euro'     => $fila['tasa_euro'] === null ? null : (float) $fila['tasa_euro'],
            'tasa_paralelo' => $fila['tasa_paralelo'] === null ? null : (float) $fila['tasa_paralelo'],
            'vigente_desde' => $fila['updated_at'] ?? $fila['created_at'] ?? null,
            'consultada'    => date('c'),
            'origen'        => 'bd',
            'error'         => 'No se pudo consultar la API; se muestra la última tasa guardada.',
        ];
    }

    // ─────────────────────────── Caché ───────────────────────────

    private function leerCache(): ?array
    {
        if (!is_readable($this->rutaCache)) {
            return null;
        }

        $datos = json_decode((string) file_get_contents($this->rutaCache), true);

        if (!is_array($datos) || !isset($datos['expira'], $datos['tasas'])) {
            return null;
        }

        if (time() >= $datos['expira']) {
            return null;
        }

        $tasas = $datos['tasas'];

        // El origen se marca como caché salvo que ya venga marcado un fallo.
        if (($tasas['origen'] ?? '') === 'api') {
            $tasas['origen'] = 'cache';
        }

        return $tasas;
    }

    private function escribirCache(array $tasas, int $ttl): void
    {
        $dir = dirname($this->rutaCache);

        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            return;
        }

        // Escritura atómica: un archivo a medio escribir sería JSON inválido
        // para la petición que lo lea al mismo tiempo.
        $temporal = $dir . '/.tasas-' . getmypid() . '.tmp';
        $carga    = json_encode(['expira' => time() + $ttl, 'tasas' => $tasas]);

        if ($carga !== false && @file_put_contents($temporal, $carga, LOCK_EX) !== false) {
            if (!@rename($temporal, $this->rutaCache)) {
                @unlink($temporal);
            }
        }
    }
}
