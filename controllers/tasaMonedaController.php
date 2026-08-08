<?php 

class TasaMonedaController extends BaseController
{
    private $tasaModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->tasaModel = new TasaMoneda();
    }

    public function listar()
    {
        // tasa_vigente() consulta la API (o su caché) y guarda el valor nuevo
        // si cambió, así que $ultima ya refleja lo que devolvió la API.
        $tasa = tasa_vigente();

        $tasas = $this->tasaModel->listar();
        $ultima = $this->tasaModel->obtenerUltima();

        include RUTA_APP . '/views/tasa-moneda/tasa-moneda.php';
        exit();
    }

    /** Fuerza una consulta a la API, saltándose la caché. */
    public function actualizar()
    {
        $tasa = (new TasaService())->refrescar();

        if (!empty($tasa['tasa_usd']) && $tasa['origen'] === 'api') {
            $this->setFlash('success', 'Tasa actualizada desde la API: ' . money($tasa['tasa_usd']) . ' por dólar.');
        } else {
            $this->setFlash('error', $tasa['error'] ?: 'No se pudo obtener la tasa desde la API.');
        }

        header('Location: ' . url('tasa-moneda'));
        exit();
    }

    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            list($moneda, $tasa_usd, $tasa_euro, $tasa_paralelo) = $this->limpiarPOST();

            $resultado = $this->tasaModel->crear($moneda, $tasa_usd, $tasa_euro, $tasa_paralelo);
            
            if ($resultado) {
                $this->setFlash('success', 'Tasa de cambio registrada con éxito');
                $this->redirect('tasa-moneda');
            }
        } else {
            $this->listar();
        }
    }

    public function limpiarPOST()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $moneda_input = isset($_POST['moneda']) ? trim($_POST['moneda']) : 'Bs';
            $tasa_usd_input = isset($_POST['tasa_usd']) ? trim($_POST['tasa_usd']) : null;
            $tasa_euro_input = isset($_POST['tasa_euro']) ? trim($_POST['tasa_euro']) : null;
            $tasa_paralelo_input = isset($_POST['tasa_paralelo']) ? trim($_POST['tasa_paralelo']) : null;

            if (empty($moneda_input)) {
                $this->setFlash('error', 'La moneda es obligatoria');
                $this->redirect('tasa-moneda');
            }

            if (!is_numeric($tasa_usd_input) || $tasa_usd_input <= 0) {
                $this->setFlash('error', 'La tasa USD debe ser un número válido mayor a 0');
                $this->redirect('tasa-moneda');
            }

            if (!empty($tasa_euro_input) && (!is_numeric($tasa_euro_input) || $tasa_euro_input <= 0)) {
                $this->setFlash('error', 'La tasa Euro debe ser un número válido mayor a 0');
                $this->redirect('tasa-moneda');
            }

            if (!empty($tasa_paralelo_input) && (!is_numeric($tasa_paralelo_input) || $tasa_paralelo_input <= 0)) {
                $this->setFlash('error', 'La tasa Paralelo debe ser un número válido mayor a 0');
                $this->redirect('tasa-moneda');
            }

            if (empty($tasa_euro_input)) {
                $tasa_euro_input = null;
            }

            if (empty($tasa_paralelo_input)) {
                $tasa_paralelo_input = null;
            }

            return [$moneda_input, $tasa_usd_input, $tasa_euro_input, $tasa_paralelo_input];
        }
    }
}