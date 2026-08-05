<?php 

require_once './models/Categoria.php';

class categoriasController
{

    private $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new Categoria();
    }

    ////////////////////////////////////////////////////
    ////////             FUNCIONES                //////
    ////////             DEL CRUD                 //////
    ////////    (CREATE, READ, UPDATE, DELETE)    //////
    ////////////////////////////////////////////////////


    public function listar(){

            $categorias = $this->categoriaModel->listar();

            include ruta . '/views/categorias/categorias.php';
            exit();

    }


    public function crear(){

        if ($_SERVER['REQUEST_METHOD'] == 'POST'){

            $categorias_nombre = $this->limpiarPOST();

            $verfduplicado = $this->categoriaModel->verificarDuplicado($categorias_nombre);

            if ($verfduplicado){
                $resultado = $this->categoriaModel->crear($categorias_nombre);
                if ($resultado){
                $_SESSION['success'] = 'Categoria creada con exito';
                header("Location:  ./index.php?controller=categoriasController&action=listar");
                exit();
                }

            } else {
                $_SESSION['error'] = 'Esta categoria ya existe';
                header("Location:  ./index.php?controller=categoriasController&action=listar");
                exit();
            }

           
            
        } else {
            $this->listar();
        }

    }


    public function editar(){
        
        if ($_SERVER['REQUEST_METHOD']=== 'POST'){

            $categorias_id = $this->limpiarVerificarId();

            $categorias_nombre = $this->limpiarPOST();

            $verifduplicado = $this->categoriaModel->verificarDuplicadoId($categorias_nombre, $categorias_id);

            

            if ($verifduplicado){
                $resultado = $this->categoriaModel->editar($categorias_nombre, $categorias_id);

                
                if ($resultado){
                $_SESSION['success'] = 'Categoria editada con exito';
                header("Location:  ./index.php?controller=categoriasController&action=listar");
                exit();
                }
            } else {
                $_SESSION['error'] = 'Esta categoria ya existe';
                header("Location:  ./index.php?controller=categoriasController&action=listar");
                exit();
            }
        
        } else {
            $categorias_id = $this->limpiarVerificarId();
            $dato = $this->categoriaModel->consultarPorId($categorias_id);

           include_once './views/categorias/categorias-editar.php';

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
    

            //validacion del nombre
            
            if (strlen($nombre_input) < 3){
                $_SESSION['error'] = 'El nombre debe tener minimo 3 caracteres';
                header("Location:  ./index.php?controller=categoriasController&action=listar");
                exit();
            }

            return $nombre_input;
            
        }
        
    }


    public function limpiarVerificarId(){
        $categorias_id = isset($_GET['id']) ? trim($_GET['id']) : '?';

        if (!is_numeric($categorias_id) || $categorias_id === '0') {
            echo '<script>alert("id no valido")</script>';
            $this->listar();
            exit();
        }

        $resultado = $this->categoriaModel->limpiarVerificarId($categorias_id);


        if ($resultado){
            return $categorias_id;
        } else {
            echo '<script>alert("id no encontrado")</script>';
            $this->listar();
            exit();
        }

    }
}