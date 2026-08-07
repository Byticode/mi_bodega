<?php

class Conexion
{
    public static function conectar()
    {
        // cargamos la configuración local si existe
        if (file_exists(__DIR__ . "/config_local.php")) {
            require_once __DIR__ . "/config_local.php";
        }

        // Definimos las credenciales usando constantes locales si existen,
        // de lo contrario, recurrimos a los valores por defecto.
        $host = defined("DB_HOST") ? DB_HOST : "localhost";
        $dbName = defined("DB_NAME") ? DB_NAME : "mi_bodega";
        $username = defined("DB_USER") ? DB_USER : "root";
        $password = defined("DB_PASS") ? DB_PASS : "";

        try {
            // Configurar el DSN (Data Source Name) especificando el charset utf8mb4
            $dsn =
                "mysql:host=" .
                $host .
                ";dbname=" .
                $dbName .
                ";charset=utf8mb4";

            // Crear la instancia de PDO
            $pdo = new PDO($dsn, $username, $password);

            // Configurar PDO para que lance excepciones en caso de errores de SQL
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Configurar para que por defecto devuelva los datos como arreglos asociativos
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $pdo;
        } catch (PDOException $e) {
            // Si algo falla, detenemos el sistema y mostramos el error
            die(
                "❌ Error fatal en la conexión a la base de datos: " .
                    $e->getMessage()
            );
        }
    }
}

?>
