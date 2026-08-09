<?php 

class SurtidosController extends BaseController
{
    private $surtidoModel;
    private $productoModel;
    private $proveedorModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->surtidoModel = new Surtido();
        $this->productoModel = new Producto();
        $this->proveedorModel = new Proveedor();
    }

    public function listar()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $paginacion = $this->surtidoModel->listarPaginado($page, 15);
        $surtidos = $paginacion['data'];

        include RUTA_APP . '/views/surtidos/surtidos.php';
        exit();
    }

    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proveedor_id = $_POST['proveedor_id'] ?? null;
            
            $productos_ids = $_POST['producto_id'] ?? [];
            $cantidades = $_POST['cantidad'] ?? [];
            $precios_costo = $_POST['precio_costo'] ?? [];
            
            if (empty($productos_ids) || empty($cantidades) || empty($precios_costo)) {
                $this->setFlash('error', 'Debe agregar al menos un producto');
                $this->redirect('surtidos/crear');
            }

            if (empty($proveedor_id)) {
                $this->setFlash('error', 'Debe seleccionar un proveedor');
                $this->redirect('surtidos/crear');
            }

            $productos = [];
            $costo_total = 0;
            
            for ($i = 0; $i < count($productos_ids); $i++) {
                $producto_id = $productos_ids[$i];
                $cantidad = intval($cantidades[$i] ?? 0);
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

            if (empty($productos)) {
                $this->setFlash('error', 'Debe agregar al menos un producto con datos válidos');
                $this->redirect('surtidos/crear');
            }

            // Crear surtido mediante transacción atómica
            $surtido_id = $this->surtidoModel->crearSurtidoCompleto($proveedor_id, $costo_total, $productos, $this->productoModel);

            if ($surtido_id) {
                $this->setFlash('success', 'Surtido creado con éxito');
                $this->redirect('surtidos');
            } else {
                $this->setFlash('error', 'Error al crear el surtido. Transacción revertida');
                $this->redirect('surtidos/crear');
            }
        } else {
            $proveedores = $this->proveedorModel->listar();
            $productos = $this->productoModel->obtenerTodosProductos();

            
            include RUTA_APP . '/views/surtidos/surtidos-crear.php';
            exit();
        }
    }

    public function ver()
    {
        $surtido_id = $this->limpiarVerificarId();
        $surtido = $this->surtidoModel->consultarPorId($surtido_id);
        $detalles = $this->surtidoModel->obtenerDetalles($surtido_id);
        
        include RUTA_APP . '/views/surtidos/surtidos-ver.php';
        exit();
    }

    public function limpiarVerificarId(): int
    {
        $id = $_GET['id'] ?? null;
        $surtido_id = $this->validateNumericId($id, 'surtidos');

        $resultado = $this->surtidoModel->limpiarVerificarId($surtido_id);

        if ($resultado) {
            return $surtido_id;
        } else {
            $this->setFlash('error', 'Surtido no encontrado');
            $this->redirect('surtidos');
        }
    }
}