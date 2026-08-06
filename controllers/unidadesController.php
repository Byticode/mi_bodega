<?php 

require_once './models/Unidad.php';

class unidadesController
{

    private $unidadModel;

    public function __construct()
    {
        $this->unidadModel = new Unidad();
    }

    ////////////////////////////////////////////////////
    ////////             FUNCIONES                //////
    ////////             DEL CRUD                 //////
    ////////    (CREATE, READ, UPDATE, DELETE)    //////
    ////////////////////////////////////////////////////


    public function listar(){

        $unidades = $this->unidadModel->listar();

        include ruta . '/views/unidades/unidades.php';
        exit();
    }


    public function crear(){

        if ($_SERVER['REQUEST_METHOD'] == 'POST'){

            list($unidad_nombre, $unidad_abreviatura) = $this->limpiarPOST();

            $verfduplicado = $this->unidadModel->verificarDuplicado($unidad_nombre);

            if ($verfduplicado){
                $verfduplicadoAbreviatura = $this->unidadModel->verificarDuplicadoAbreviatura($unidad_abreviatura);
                
                if ($verfduplicadoAbreviatura){
                    $resultado = $this->unidadModel->crear($unidad_nombre, $unidad_abreviatura);
                    if ($resultado){
                        $_SESSION['success'] = 'Unidad creada con exito';
                        header("Location:  ./index.php?controller=unidadesController&action=listar");
                        exit();
                    }
                } else {
                    $_SESSION['error'] = 'Esta abreviatura ya existe';
                    header("Location:  ./index.php?controller=unidadesController&action=listar");
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Esta unidad ya existe';
                header("Location:  ./index.php?controller=unidadesController&action=listar");
                exit();
            }
            
        } else {
            $this->listar();
        }
    }


    public function editar(){
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){

            $unidad_id = $this->limpiarVerificarId();

            list($unidad_nombre, $unidad_abreviatura) = $this->limpiarPOST();

            $verifduplicado = $this->unidadModel->verificarDuplicadoId($unidad_nombre, $unidad_id);

            if ($verifduplicado){
                $verifduplicadoAbreviatura = $this->unidadModel->verificarDuplicadoAbreviaturaId($unidad_abreviatura, $unidad_id);
                
                if ($verifduplicadoAbreviatura){
                    $resultado = $this->unidadModel->editar($unidad_nombre, $unidad_abreviatura, $unidad_id);
                    
                    if ($resultado){
                        $_SESSION['success'] = 'Unidad editada con exito';
                        header("Location:  ./index.php?controller=unidadesController&action=listar");
                        exit();
                    }
                } else {
                    $_SESSION['error'] = 'Esta abreviatura ya existe';
                    header("Location:  ./index.php?controller=unidadesController&action=listar");
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Esta unidad ya existe';
                header("Location:  ./index.php?controller=unidadesController&action=listar");
                exit();
            }
        
        } else {
            $unidad_id = $this->limpiarVerificarId();
            $dato = $this->unidadModel->consultarPorId($unidad_id);

            include_once './views/unidades/unidades-editar.php';
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
            
            $nombre_input = isset($_POST['nombre']) ? ucwords(trim($_POST['nombre'])) : '';
            $abreviatura_input = isset($_POST['abreviatura']) ? strtoupper(trim($_POST['abreviatura'])) : '';
    
            // Validación del nombre
            if (strlen($nombre_input) < 2){
                $_SESSION['error'] = 'El nombre debe tener mínimo 2 caracteres';
                header("Location:  ./index.php?controller=unidadesController&action=listar");
                exit();
            }

            // Validación de la abreviatura
            if (strlen($abreviatura_input) < 1){
                $_SESSION['error'] = 'La abreviatura debe tener al menos 1 caracter';
                header("Location:  ./index.php?controller=unidadesController&action=listar");
                exit();
            }

            return [$nombre_input, $abreviatura_input];
        }
    }


    public function limpiarVerificarId(){
        $unidad_id = isset($_GET['id']) ? trim($_GET['id']) : '?';

        if (!is_numeric($unidad_id) || $unidad_id === '0') {
            echo '<script>alert("id no valido")</script>';
            $this->listar();
            exit();
        }

        $resultado = $this->unidadModel->limpiarVerificarId($unidad_id);

        if ($resultado){
            return $unidad_id;
        } else {
            echo '<script>alert("id no encontrado")</script>';
            $this->listar();
            exit();
        }
    }
}
?>