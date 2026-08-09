<?php 

class VentasController extends BaseController
{
    private $ventaModel;
    private $productoModel;
    private $clienteModel;
    private $tasaModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->ventaModel = new Venta();
        $this->productoModel = new Producto();
        $this->clienteModel = new Cliente();
        $this->tasaModel = new TasaMoneda();
    }

    public function listar()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $paginacion = $this->ventaModel->listarPaginado($page, 15);
        $ventas = $paginacion['data'];

        include RUTA_APP . '/views/ventas/ventas.php';
        exit();
    }

    public function pos()
    {
        $clientes = $this->clienteModel->listar();
        $productos = $this->productoModel->obtenerProductosConStock();
        // tasa_vigente() consulta la API (o su caché) y guarda el valor si cambió.
        $tasa = tasa_vigente();

        include RUTA_APP . '/views/pos/pos.php';
        exit();
    }

    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cliente_id = !empty($_POST['cliente_id']) ? (int) $_POST['cliente_id'] : null;
            $usuario_id = $this->getAuthUserId();

            // ventas.tasa_id es NOT NULL. Se bloquea la venta en vez de caer en
            // un id fijo: guardar un total contra una tasa que no es la usada
            // corrompe el histórico en silencio.
            $ultimaTasa = $this->tasaModel->obtenerUltima();

            if (empty($ultimaTasa['tasa_id'])) {
                $this->setFlash('error', 'No hay ninguna tasa de cambio registrada y la venta necesita una. Registra una en Ajustes › Tasa de cambio.');
                header('Location: ' . url('pos'));
                exit();
            }

            $tasa_id = $ultimaTasa['tasa_id'];
            
            $productos = isset($_POST['productos']) ? json_decode($_POST['productos'], true) : [];
            $estado = $_POST['estado'] ?? 'completada';
            $metodo_pago = !empty($_POST['metodo_pago']) ? $_POST['metodo_pago'] : null;
            $numero_pago = $_POST['numero_pago'] ?? null;
            
            if (empty($productos) || !is_array($productos)) {
                $this->setFlash('error', 'Debe agregar al menos un producto');
                $this->redirect('pos');
            }

            $total = 0;
            foreach ($productos as $item) {
                $total += $item['cantidad'] * $item['precio'];
            }

            $venta_id = $this->ventaModel->crearVentaCompleta(
                $cliente_id,
                $usuario_id,
                $tasa_id,
                $total,
                $numero_pago,
                $metodo_pago,
                $estado,
                $productos,
                $this->productoModel
            );

            if ($venta_id) {
                $this->setFlash('success', 'Venta registrada exitosamente con código ID: ' . $venta_id);
                $this->redirect('ventas');
            } else {
                $this->setFlash('error', 'Error crítico al procesar la venta. Transacción revertida.');
                $this->redirect('pos');
            }
        }
    }

    public function editar()
    {
        $venta_id = $this->limpiarVerificarId();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['estado']) && $_POST['estado'] === 'completada') {
                $metodo_pago = $_POST['metodo_pago'] ?? null;
                $numero_pago = $_POST['numero_pago'] ?? null;
                
                if ($metodo_pago) {
                    try {
                        $this->ventaModel->beginTransaction();

                        $detalles = $this->ventaModel->obtenerDetalles($venta_id);
                        foreach ($detalles as $detalle) {
                            $this->productoModel->descontarStock($detalle['producto_id'], $detalle['detalle_cantidad']);
                        }
                        
                        $this->ventaModel->completarVenta($venta_id, $metodo_pago, $numero_pago);
                        $this->ventaModel->commit();

                        $this->setFlash('success', 'Venta completada con éxito');
                    } catch (Throwable $e) {
                        $this->ventaModel->rollBack();
                        $this->setFlash('error', 'Error al completar la venta');
                    }
                } else {
                    $this->setFlash('error', 'Debe seleccionar un método de pago');
                }
            } else {
                $this->ventaModel->actualizarEstado($venta_id, $_POST['estado']);
                $this->setFlash('success', 'Estado de venta actualizado');
            }
            
            $this->redirect('ventas');
        } else {
            $venta = $this->ventaModel->consultarPorId($venta_id);
            $detalles = $this->ventaModel->obtenerDetalles($venta_id);
            $clientes = $this->clienteModel->listar();
            
            include RUTA_APP . '/views/ventas/ventas-editar.php';
            exit();
        }
    }

    public function ver()
    {
        $venta_id = $this->limpiarVerificarId();
        $venta = $this->ventaModel->consultarPorId($venta_id);
        $detalles = $this->ventaModel->obtenerDetalles($venta_id);
        
        include RUTA_APP . '/views/ventas/ventas-ver.php';
        exit();
    }

    public function limpiarVerificarId(): int
    {
        $id = $_GET['id'] ?? null;
        return $this->validateNumericId($id, 'ventas');
    }
}