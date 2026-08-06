<?php 

require_once './models/ProductoBase.php';

class productosBaseController
{

    private $productoBaseModel;

    public function __construct()
    {
        $this->productoBaseModel = new ProductoBase();
    }

    ////////////////////////////////////////////////////
    ////////             FUNCIONES                //////
    ////////             DEL CRUD                 //////
    ////////    (CREATE, READ, UPDATE, DELETE)    //////
    ////////////////////////////////////////////////////


    public function listar(){

        $productos = $this->productoBaseModel->listar();
        $categorias = $this->productoBaseModel->obtenerCategorias();

        include ruta . '/views/productos-base/productos-base.php';
        exit();
    }


    public function crear(){

        if ($_SERVER['REQUEST_METHOD'] == 'POST'){

            list($producto_codigo, $producto_nombre, $categoria_id) = $this->limpiarPOST();

            // Verificar duplicado por nombre (obligatorio)
            $verfduplicado = $this->productoBaseModel->verificarDuplicado($producto_nombre);

            if ($verfduplicado){
                // Verificar duplicado por código (solo si se proporcionó)
                $verfduplicadoCodigo = $this->productoBaseModel->verificarDuplicadoCodigo($producto_codigo);
                
                if ($verfduplicadoCodigo){
                    $resultado = $this->productoBaseModel->crear($producto_codigo, $producto_nombre, $categoria_id);
                    if ($resultado){
                        $_SESSION['success'] = 'Producto base creado con exito';
                        header("Location:  ./index.php?controller=productosBaseController&action=listar");
                        exit();
                    }
                } else {
                    $_SESSION['error'] = 'Este código de barras ya está registrado';
                    header("Location:  ./index.php?controller=productosBaseController&action=listar");
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Este producto ya existe';
                header("Location:  ./index.php?controller=productosBaseController&action=listar");
                exit();
            }
            
        } else {
            $this->listar();
        }
    }


    public function editar(){
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){

            $producto_id = $this->limpiarVerificarId();

            list($producto_codigo, $producto_nombre, $categoria_id) = $this->limpiarPOST();

            // Verificar duplicado por nombre
            $verifduplicado = $this->productoBaseModel->verificarDuplicadoId($producto_nombre, $producto_id);

            if ($verifduplicado){
                // Verificar duplicado por código (solo si se proporcionó)
                $verifduplicadoCodigo = $this->productoBaseModel->verificarDuplicadoCodigoId($producto_codigo, $producto_id);
                
                if ($verifduplicadoCodigo){
                    $resultado = $this->productoBaseModel->editar($producto_codigo, $producto_nombre, $categoria_id, $producto_id);
                    
                    if ($resultado){
                        $_SESSION['success'] = 'Producto base editado con exito';
                        header("Location:  ./index.php?controller=productosBaseController&action=listar");
                        exit();
                    }
                } else {
                    $_SESSION['error'] = 'Este código de barras ya está registrado';
                    header("Location:  ./index.php?controller=productosBaseController&action=listar");
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Este producto ya existe';
                header("Location:  ./index.php?controller=productosBaseController&action=listar");
                exit();
            }
        
        } else {
            $producto_id = $this->limpiarVerificarId();
            $dato = $this->productoBaseModel->consultarPorId($producto_id);
            $categorias = $this->productoBaseModel->obtenerCategorias();

            include_once './views/productos-base/productos-base-editar.php';
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
            
            $codigo_input = isset($_POST['codigo']) ? trim($_POST['codigo']) : null;
            $nombre_input = isset($_POST['nombre']) ? ucwords(trim($_POST['nombre'])) : '';
            $categoria_input = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
    
            // Validación del nombre (obligatorio)
            if (strlen($nombre_input) < 3){
                $_SESSION['error'] = 'El nombre debe tener mínimo 3 caracteres';
                header("Location:  ./index.php?controller=productosBaseController&action=listar");
                exit();
            }

            // Validación de la categoría (obligatorio)
            if (empty($categoria_input) || !is_numeric($categoria_input)){
                $_SESSION['error'] = 'Debe seleccionar una categoría';
                header("Location:  ./index.php?controller=productosBaseController&action=listar");
                exit();
            }

            // Validación del código (opcional)
            if (!empty($codigo_input) && strlen($codigo_input) < 5){
                $_SESSION['error'] = 'El código de barras debe tener mínimo 5 caracteres';
                header("Location:  ./index.php?controller=productosBaseController&action=listar");
                exit();
            }

            // Si el código está vacío, lo enviamos como NULL
            if (empty($codigo_input)) {
                $codigo_input = null;
            }

            return [$codigo_input, $nombre_input, $categoria_input];
        }
    }


    public function limpiarVerificarId(){
        $producto_id = isset($_GET['id']) ? trim($_GET['id']) : '?';

        if (!is_numeric($producto_id) || $producto_id === '0') {
            echo '<script>alert("id no valido")</script>';
            $this->listar();
            exit();
        }

        $resultado = $this->productoBaseModel->limpiarVerificarId($producto_id);

        if ($resultado){
            return $producto_id;
        } else {
            echo '<script>alert("id no encontrado")</script>';
            $this->listar();
            exit();
        }
    }
}
?>