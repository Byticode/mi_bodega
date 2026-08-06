<?php 

require_once './models/Surtido.php';
require_once './models/Producto.php';
require_once './models/Proveedor.php';

class surtidosController
{

    private $surtidoModel;
    private $productoModel;
    private $proveedorModel;

    public function __construct()
    {
        $this->surtidoModel = new Surtido();
        $this->productoModel = new Producto();
        $this->proveedorModel = new Proveedor();
    }

    ////////////////////////////////////////////////////
    ////////             FUNCIONES                //////
    ////////             DEL CRUD                 //////
    ////////    (CREATE, READ, UPDATE, DELETE)    //////
    ////////////////////////////////////////////////////


    public function listar(){
        $surtidos = $this->surtidoModel->listar();
        include ruta . '/views/surtidos/surtidos.php';
        exit();
    }


    public function crear(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $proveedor_id = $_POST['proveedor_id'] ?? null;
            
            // Obtener los arrays del formulario
            $productos_ids = $_POST['producto_id'] ?? [];
            $cantidades = $_POST['cantidad'] ?? [];
            $precios_costo = $_POST['precio_costo'] ?? [];
            
            // Validar que haya productos
            if (empty($productos_ids) || empty($cantidades) || empty($precios_costo)) {
                $_SESSION['error'] = 'Debe agregar al menos un producto';
                header("Location: ./index.php?controller=surtidosController&action=crear");
                exit();
            }

            // Validar proveedor
            if (empty($proveedor_id)) {
                $_SESSION['error'] = 'Debe seleccionar un proveedor';
                header("Location: ./index.php?controller=surtidosController&action=crear");
                exit();
            }

            // Construir array de productos
            $productos = [];
            $costo_total = 0;
            
            for ($i = 0; $i < count($productos_ids); $i++) {
                $producto_id = $productos_ids[$i];
                $cantidad = intval($cantidades[$i] ?? 0); // Convertir a entero
                $precio_costo = floatval($precios_costo[$i] ?? 0);
                
                if ($producto_id && $cantidad > 0 && $precio_costo > 0) {
                    $productos[] = [
                        'id' => $producto_id,
                        'cantidad' => $cantidad,
                        'precio_costo' => $precio_costo
                    ];
                    $costo_total += $cantidad * $precio_costo;
                }
            }

            // Validar que haya al menos un producto válido
            if (empty($productos)) {
                $_SESSION['error'] = 'Debe agregar al menos un producto con datos válidos';
                header("Location: ./index.php?controller=surtidosController&action=crear");
                exit();
            }

            // Crear el surtido
            $surtido_id = $this->surtidoModel->crear($proveedor_id, $costo_total);

            if ($surtido_id) {
                foreach ($productos as $item) {
                    $this->surtidoModel->agregarDetalle($surtido_id, $item['id'], $item['cantidad'], $item['precio_costo']);
                    // Actualizar stock
                    $this->productoModel->actualizarStock($item['id'], $item['cantidad']);
                }

                $_SESSION['success'] = 'Surtido creado con éxito';
                header("Location: ./index.php?controller=surtidosController&action=listar");
                exit();
            } else {
                $_SESSION['error'] = 'Error al crear el surtido';
                header("Location: ./index.php?controller=surtidosController&action=crear");
                exit();
            }
        } else {
            $proveedores = $this->proveedorModel->listar();
            $productos = $this->productoModel->obtenerTodosProductos();
            
            include ruta . '/views/surtidos/surtidos-crear.php';
            exit();
        }
    }


    public function ver(){
        $surtido_id = $this->limpiarVerificarId();
        $surtido = $this->surtidoModel->consultarPorId($surtido_id);
        $detalles = $this->surtidoModel->obtenerDetalles($surtido_id);
        
        include ruta . '/views/surtidos/surtidos-ver.php';
        exit();
    }


    public function limpiarVerificarId(){
        $surtido_id = isset($_GET['id']) ? trim($_GET['id']) : '?';

        if (!is_numeric($surtido_id) || $surtido_id === '0') {
            echo '<script>alert("id no valido")</script>';
            $this->listar();
            exit();
        }

        $resultado = $this->surtidoModel->limpiarVerificarId($surtido_id);

        if ($resultado){
            return $surtido_id;
        } else {
            echo '<script>alert("id no encontrado")</script>';
            $this->listar();
            exit();
        }
    }
}
?>