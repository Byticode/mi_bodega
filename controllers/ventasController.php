<?php 

require_once './models/Venta.php';
require_once './models/Producto.php';
require_once './models/Cliente.php';
require_once './models/TasaMoneda.php';

class ventasController
{

    private $ventaModel;
    private $productoModel;
    private $clienteModel;
    private $tasaModel;

    public function __construct()
    {
        $this->ventaModel = new Venta();
        $this->productoModel = new Producto();
        $this->clienteModel = new Cliente();
        $this->tasaModel = new TasaMoneda();
    }

    public function listar(){
        $ventas = $this->ventaModel->listar();
        include ruta . '/views/ventas/ventas.php';
        exit();
    }

    public function pos(){
        $clientes = $this->clienteModel->listar();
        $productos = $this->productoModel->obtenerTodosProductos();
        $tasa = tasa_vigente();

        include ruta . '/views/pos/pos.php';
        exit();
    }

    public function crear(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $cliente_id = isset($_POST['cliente_id']) && !empty($_POST['cliente_id']) ? $_POST['cliente_id'] : null;
            $usuario_id = 1; // Temporal, luego se usará sesión

            // ventas.tasa_id es NOT NULL: sin una tasa registrada la venta no
            // puede guardarse. tasa_vigente() normalmente ya insertó la de la
            // API, así que esto solo salta si la API falló y la tabla está vacía.
            $ultima_tasa = $this->tasaModel->obtenerUltima();

            if (empty($ultima_tasa['tasa_id'])) {
                $_SESSION['error'] = 'No hay ninguna tasa de cambio registrada y la venta necesita una. Registra una en Ajustes › Tasa de cambio.';
                header("Location: ./index.php?controller=ventasController&action=pos");
                exit();
            }

            $tasa_id = $ultima_tasa['tasa_id'];
            
            // Obtener productos del carrito
            $productos = isset($_POST['productos']) ? json_decode($_POST['productos'], true) : [];
            $estado = isset($_POST['estado']) ? $_POST['estado'] : 'completada';
            $metodo_pago = isset($_POST['metodo_pago']) && !empty($_POST['metodo_pago']) ? $_POST['metodo_pago'] : null;
            $numero_pago = isset($_POST['numero_pago']) ? $_POST['numero_pago'] : null;
            
            if (empty($productos)) {
                $_SESSION['error'] = 'Debe agregar al menos un producto';
                header("Location: ./index.php?controller=ventasController&action=pos");
                exit();
            }

            // Calcular total
            $total = 0;
            foreach ($productos as $item) {
                $total += $item['cantidad'] * $item['precio'];
            }

            // Crear la venta
            $venta_id = $this->ventaModel->crear($cliente_id, $usuario_id, $tasa_id, $total, $numero_pago, $metodo_pago, $estado);

            if ($venta_id) {
                // Agregar detalles y descontar stock
                foreach ($productos as $item) {
                    $this->ventaModel->agregarDetalle($venta_id, $item['id'], $item['cantidad'], $item['precio']);
                    // Solo descontar stock si la venta está completada
                    if ($estado == 'completada') {
                        $this->productoModel->descontarStock($item['id'], $item['cantidad']);
                    }
                }

                $_SESSION['success'] = 'Venta creada con éxito';
                header("Location: ./index.php?controller=ventasController&action=listar");
                exit();
            }
        }
    }

    public function editar(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $venta_id = $this->limpiarVerificarId();
            
            if (isset($_POST['estado']) && $_POST['estado'] == 'completada') {
                $metodo_pago = $_POST['metodo_pago'] ?? null;
                $numero_pago = $_POST['numero_pago'] ?? null;
                
                if ($metodo_pago) {
                    // Primero obtener los productos de la venta para descontar stock
                    $detalles = $this->ventaModel->obtenerDetalles($venta_id);
                    foreach ($detalles as $detalle) {
                        $this->productoModel->descontarStock($detalle['producto_id'], $detalle['detalle_cantidad']);
                    }
                    
                    $this->ventaModel->completarVenta($venta_id, $metodo_pago, $numero_pago);
                    $_SESSION['success'] = 'Venta completada con éxito';
                } else {
                    $_SESSION['error'] = 'Debe seleccionar un método de pago';
                }
            } else {
                $this->ventaModel->actualizarEstado($venta_id, $_POST['estado']);
                $_SESSION['success'] = 'Estado de venta actualizado';
            }
            
            header("Location: ./index.php?controller=ventasController&action=listar");
            exit();
        } else {
            $venta_id = $this->limpiarVerificarId();
            $venta = $this->ventaModel->consultarPorId($venta_id);
            $detalles = $this->ventaModel->obtenerDetalles($venta_id);
            $clientes = $this->clienteModel->listar();
            
            include ruta . '/views/ventas/ventas-editar.php';
            exit();
        }
    }

    public function ver(){
        $venta_id = $this->limpiarVerificarId();
        $venta = $this->ventaModel->consultarPorId($venta_id);
        $detalles = $this->ventaModel->obtenerDetalles($venta_id);
        
        include ruta . '/views/ventas/ventas-ver.php';
        exit();
    }

    public function limpiarVerificarId(){
        $venta_id = isset($_GET['id']) ? trim($_GET['id']) : '?';

        if (!is_numeric($venta_id) || $venta_id === '0') {
            echo '<script>alert("id no valido")</script>';
            $this->listar();
            exit();
        }

        return $venta_id;
    }
}
?>