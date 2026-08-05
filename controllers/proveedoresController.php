<?php 

require_once './models/Proveedor.php';

class proveedoresController
{

    private $proveedorModel;

    public function __construct()
    {
        $this->proveedorModel = new Proveedor();
    }

    ////////////////////////////////////////////////////
    ////////             FUNCIONES                //////
    ////////             DEL CRUD                 //////
    ////////    (CREATE, READ, UPDATE, DELETE)    //////
    ////////////////////////////////////////////////////


    public function listar(){

        $proveedores = $this->proveedorModel->listar();

        include ruta . '/views/proveedores/proveedores.php';
        exit();
    }


    public function crear(){

        if ($_SERVER['REQUEST_METHOD'] == 'POST'){

            list($proveedor_nombre, $proveedor_telefono) = $this->limpiarPOST();

            $verfduplicado = $this->proveedorModel->verificarDuplicado($proveedor_nombre);

            if ($verfduplicado){
                $resultado = $this->proveedorModel->crear($proveedor_nombre, $proveedor_telefono);
                if ($resultado){
                    $_SESSION['success'] = 'Proveedor creado con exito';
                    header("Location:  ./index.php?controller=proveedoresController&action=listar");
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Este proveedor ya existe';
                header("Location:  ./index.php?controller=proveedoresController&action=listar");
                exit();
            }
            
        } else {
            $this->listar();
        }
    }


    public function editar(){
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){

            $proveedor_id = $this->limpiarVerificarId();

            list($proveedor_nombre, $proveedor_telefono) = $this->limpiarPOST();

            $verifduplicado = $this->proveedorModel->verificarDuplicadoId($proveedor_nombre, $proveedor_id);

            if ($verifduplicado){
                $resultado = $this->proveedorModel->editar($proveedor_nombre, $proveedor_telefono, $proveedor_id);
                
                if ($resultado){
                    $_SESSION['success'] = 'Proveedor editado con exito';
                    header("Location:  ./index.php?controller=proveedoresController&action=listar");
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Este proveedor ya existe';
                header("Location:  ./index.php?controller=proveedoresController&action=listar");
                exit();
            }
        
        } else {
            $proveedor_id = $this->limpiarVerificarId();
            $dato = $this->proveedorModel->consultarPorId($proveedor_id);

            include_once './views/proveedores/proveedores-editar.php';
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
            
            $nombre_input = isset($_POST['nombre']) ? ucfirst(trim($_POST['nombre'])) : '';
            $telefono_input = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    
            //validacion del nombre
            if (strlen($nombre_input) < 3){
                $_SESSION['error'] = 'El nombre debe tener minimo 3 caracteres';
                header("Location:  ./index.php?controller=proveedoresController&action=listar");
                exit();
            }

            //validacion del telefono
            if (strlen($telefono_input) < 7){
                $_SESSION['error'] = 'El telefono debe tener minimo 7 caracteres';
                header("Location:  ./index.php?controller=proveedoresController&action=listar");
                exit();
            }

            return [$nombre_input, $telefono_input];
        }
    }


    public function limpiarVerificarId(){
        $proveedor_id = isset($_GET['id']) ? trim($_GET['id']) : '?';

        if (!is_numeric($proveedor_id) || $proveedor_id === '0') {
            echo '<script>alert("id no valido")</script>';
            $this->listar();
            exit();
        }

        $resultado = $this->proveedorModel->limpiarVerificarId($proveedor_id);

        if ($resultado){
            return $proveedor_id;
        } else {
            echo '<script>alert("id no encontrado")</script>';
            $this->listar();
            exit();
        }
    }
}
?>