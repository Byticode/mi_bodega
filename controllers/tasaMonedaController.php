<?php 

require_once './models/TasaMoneda.php';

class tasaMonedaController
{

    private $tasaModel;

    public function __construct()
    {
        $this->tasaModel = new TasaMoneda();
    }

    ////////////////////////////////////////////////////
    ////////             FUNCIONES                //////
    ////////             DEL CRUD                 //////
    ////////    (CREATE, READ, UPDATE, DELETE)    //////
    ////////////////////////////////////////////////////


    public function listar(){

        // tasa_vigente() consulta la API (o su caché) y guarda el valor nuevo
        // si cambió, así que $ultima ya refleja lo que devolvió la API.
        $tasa = tasa_vigente();

        $tasas = $this->tasaModel->listar();
        $ultima = $this->tasaModel->obtenerUltima();

        include ruta . '/views/tasa-moneda/tasa-moneda.php';
        exit();
    }


    /** Fuerza una consulta a la API, saltándose la caché. */
    public function actualizar(){

        $tasa = (new TasaService())->refrescar();

        if (!empty($tasa['tasa_usd']) && $tasa['origen'] === 'api') {
            $_SESSION['success'] = 'Tasa actualizada desde la API: ' . money($tasa['tasa_usd']) . ' por dólar.';
        } else {
            $_SESSION['error'] = $tasa['error'] ?: 'No se pudo obtener la tasa desde la API.';
        }

        header("Location:  ./index.php?controller=tasaMonedaController&action=listar");
        exit();
    }


    public function crear(){

        if ($_SERVER['REQUEST_METHOD'] == 'POST'){

            list($moneda, $tasa_usd, $tasa_euro, $tasa_paralelo) = $this->limpiarPOST();

            $resultado = $this->tasaModel->crear($moneda, $tasa_usd, $tasa_euro, $tasa_paralelo);
            
            if ($resultado){
                $_SESSION['success'] = 'Tasa de cambio registrada con exito';
                header("Location:  ./index.php?controller=tasaMonedaController&action=listar");
                exit();
            }
            
        } else {
            $this->listar();
        }
    }


    ////////////////////////////////////////////////////
    ////////             FUNCIONES                //////
    ////////             DE PURIFICACION          //////
    ////////                                      //////
    ////////////////////////////////////////////////////


    public function limpiarPOST()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $moneda_input = isset($_POST['moneda']) ? trim($_POST['moneda']) : 'Bs';
            $tasa_usd_input = isset($_POST['tasa_usd']) ? trim($_POST['tasa_usd']) : null;
            $tasa_euro_input = isset($_POST['tasa_euro']) ? trim($_POST['tasa_euro']) : null;
            $tasa_paralelo_input = isset($_POST['tasa_paralelo']) ? trim($_POST['tasa_paralelo']) : null;
    
            // Validación de la moneda
            if (empty($moneda_input)){
                $_SESSION['error'] = 'La moneda es obligatoria';
                header("Location:  ./index.php?controller=tasaMonedaController&action=listar");
                exit();
            }

            // Validación de tasa USD
            if (!is_numeric($tasa_usd_input) || $tasa_usd_input <= 0){
                $_SESSION['error'] = 'La tasa USD debe ser un número válido mayor a 0';
                header("Location:  ./index.php?controller=tasaMonedaController&action=listar");
                exit();
            }

            // Validación de tasa Euro (opcional)
            if (!empty($tasa_euro_input) && (!is_numeric($tasa_euro_input) || $tasa_euro_input <= 0)){
                $_SESSION['error'] = 'La tasa Euro debe ser un número válido mayor a 0';
                header("Location:  ./index.php?controller=tasaMonedaController&action=listar");
                exit();
            }

            // Validación de tasa Paralelo (opcional)
            if (!empty($tasa_paralelo_input) && (!is_numeric($tasa_paralelo_input) || $tasa_paralelo_input <= 0)){
                $_SESSION['error'] = 'La tasa Paralelo debe ser un número válido mayor a 0';
                header("Location:  ./index.php?controller=tasaMonedaController&action=listar");
                exit();
            }

            // Si Euro está vacío, enviar NULL
            if (empty($tasa_euro_input)) {
                $tasa_euro_input = null;
            }

            // Si Paralelo está vacío, enviar NULL
            if (empty($tasa_paralelo_input)) {
                $tasa_paralelo_input = null;
            }

            return [$moneda_input, $tasa_usd_input, $tasa_euro_input, $tasa_paralelo_input];
        }
    }
}
?>