<?php

class ProductosController extends BaseController
{
    private $productoModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->productoModel = new Producto();
    }

    public function listar()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $paginacion = $this->productoModel->listarPaginado($page, 15);
        $productos = $paginacion['data'];
        $categorias = $this->productoModel->obtenerCategorias();
        $unidades = $this->productoModel->obtenerUnidades();

        include RUTA_APP . '/views/productos/productos.php';
        exit();
    }

    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            list($producto_codigo, $producto_nombre, $producto_peso, $categoria_id, $unidad_id, $producto_precio_costo, $producto_ganancia, $producto_iva, $producto_precio_venta, $producto_stock) = $this->limpiarPOST();

            $verfduplicado = $this->productoModel->verificarDuplicado($producto_nombre);

            if ($verfduplicado) {
                $verfduplicadoCodigo = $this->productoModel->verificarDuplicadoCodigo($producto_codigo);

                if ($verfduplicadoCodigo) {
                    $resultado = $this->productoModel->crear($producto_codigo, $producto_nombre, $producto_peso, $categoria_id, $unidad_id, $producto_precio_costo, $producto_ganancia, $producto_iva, $producto_precio_venta, $producto_stock);
                    if ($resultado) {
                        $this->setFlash('success', 'Producto creado con éxito');
                        $this->redirect('productos');
                    }
                } else {
                    $this->setFlash('error', 'Este código de barras ya está registrado');
                    $this->redirect('productos/crear');
                }
            } else {
                $this->setFlash('error', 'Este producto ya existe');
                $this->redirect('productos/crear');
            }
        } else {
            $categorias = $this->productoModel->obtenerCategorias();
            $unidades = $this->productoModel->obtenerUnidades();

            include RUTA_APP . '/views/productos/productos-crear.php';
            exit();
        }
    }

    public function editar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $producto_id = $this->limpiarVerificarId();

            $codigo_input = isset($_POST['codigo']) ? trim($_POST['codigo']) : null;
            $nombre_input = isset($_POST['nombre']) ? ucwords(trim($_POST['nombre'])) : '';
            $peso_input = isset($_POST['peso']) ? trim($_POST['peso']) : null;
            $categoria_input = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
            $unidad_input = isset($_POST['unidad']) ? trim($_POST['unidad']) : '';
            $costo_input = isset($_POST['precio_costo']) ? floatval(trim($_POST['precio_costo'])) : 0.00;
            $ganancia_input = isset($_POST['ganancia']) ? floatval(trim($_POST['ganancia'])) : 30.00;
            $iva_input = isset($_POST['iva']) ? floatval(trim($_POST['iva'])) : 16.00;
            $precio_input = isset($_POST['precio']) ? floatval(trim($_POST['precio'])) : 0.00;
            $stock_input = isset($_POST['stock']) ? intval(trim($_POST['stock'])) : 0;

            if (strlen($nombre_input) < 3) {
                $this->setFlash('error', 'El nombre debe tener mínimo 3 caracteres');
                $this->redirect("productos/editar/" . $producto_id);
            }

            if (empty($categoria_input) || !is_numeric($categoria_input)) {
                $this->setFlash('error', 'Debe seleccionar una categoría');
                $this->redirect("productos/editar/" . $producto_id);
            }

            if (empty($unidad_input) || !is_numeric($unidad_input)) {
                $this->setFlash('error', 'Debe seleccionar una unidad');
                $this->redirect("productos/editar/" . $producto_id);
            }

            if ($precio_input <= 0 && $costo_input > 0) {
                $precio_input = $costo_input * (1 + ($ganancia_input / 100)) * (1 + ($iva_input / 100));
            }

            if ($precio_input <= 0) {
                $this->setFlash('error', 'El precio debe ser un número válido mayor a 0');
                $this->redirect("productos/editar/" . $producto_id);
            }

            if (!empty($codigo_input) && strlen($codigo_input) < 5) {
                $this->setFlash('error', 'El código de barras debe tener mínimo 5 caracteres');
                $this->redirect("productos/editar/" . $producto_id);
            }

            if (!empty($peso_input) && !is_numeric($peso_input)) {
                $this->setFlash('error', 'El peso debe ser un número válido');
                $this->redirect("productos/editar/" . $producto_id);
            }

            $unidad_abreviatura = $this->getUnidadAbreviatura($unidad_input);

            $nombre_completo = $nombre_input;
            if (!empty($peso_input) && !empty($unidad_abreviatura)) {
                $peso_num = floatval($peso_input);
                $peso_formateado = ($peso_num == intval($peso_num)) ? intval($peso_num) : number_format($peso_num, 2);
                $nombre_completo .= ' ' . $peso_formateado . $unidad_abreviatura;
            } elseif (!empty($peso_input) && empty($unidad_abreviatura)) {
                $nombre_completo .= ' ' . $peso_input;
            }

            $verifduplicado = $this->productoModel->verificarDuplicadoId($nombre_completo, $producto_id);

            if ($verifduplicado) {
                $verifduplicadoCodigo = $this->productoModel->verificarDuplicadoCodigoId($codigo_input, $producto_id);

                if ($verifduplicadoCodigo) {
                    $resultado = $this->productoModel->editar($codigo_input, $nombre_completo, $peso_input, $categoria_input, $unidad_input, $costo_input, $ganancia_input, $iva_input, $precio_input, $stock_input, $producto_id);

                    if ($resultado) {
                        $this->setFlash('success', 'Producto editado con éxito');
                        $this->redirect('productos');
                    }
                } else {
                    $this->setFlash('error', 'Este código de barras ya está registrado');
                    $this->redirect("productos/editar/" . $producto_id);
                }
            } else {
                $this->setFlash('error', 'Este producto ya existe');
                $this->redirect("productos/editar/" . $producto_id);
            }
        } else {
            $producto_id = $this->limpiarVerificarId();
            $dato = $this->productoModel->consultarPorId($producto_id);
            $categorias = $this->productoModel->obtenerCategorias();
            $unidades = $this->productoModel->obtenerUnidades();

            include RUTA_APP . '/views/productos/productos-editar.php';
            exit();
        }
    }

    public function eliminar()
    {
        $producto_id = $this->limpiarVerificarId();
        $resultado = $this->productoModel->eliminar($producto_id);

        if ($resultado) {
            $this->setFlash('success', 'Producto eliminado con éxito');
        } else {
            $this->setFlash('error', 'No se pudo eliminar el producto');
        }

        $this->redirect('productos');
    }

    public function eliminarMasivo()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idsRaw = $_POST['ids'] ?? [];
            if (is_string($idsRaw)) {
                $idsRaw = json_decode($idsRaw, true) ?? [];
            }
            if (!is_array($idsRaw)) {
                $idsRaw = [];
            }

            $ids = array_map('intval', array_filter($idsRaw, fn($id) => is_numeric($id) && $id > 0));

            if (empty($ids)) {
                $this->setFlash('error', 'No se seleccionaron productos para eliminar');
                $this->redirect('productos');
            }

            $resultado = $this->productoModel->eliminarMasivo($ids);
            if ($resultado) {
                $conteo = count($ids);
                $this->setFlash('success', "Se eliminaron {$conteo} producto(s) correctamente");
            } else {
                $this->setFlash('error', 'No se pudieron eliminar los productos seleccionados');
            }
        }
        $this->redirect('productos');
    }

    public function ajustarPreciosMasivo()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idsRaw = $_POST['ids'] ?? [];
            if (is_string($idsRaw)) {
                $idsRaw = json_decode($idsRaw, true) ?? [];
            }
            if (!is_array($idsRaw)) {
                $idsRaw = [];
            }

            $ids = array_map('intval', array_filter($idsRaw, fn($id) => is_numeric($id) && $id > 0));
            $tipo = in_array($_POST['tipo'] ?? '', ['aumentar', 'disminuir']) ? $_POST['tipo'] : 'aumentar';
            $modo = in_array($_POST['modo'] ?? '', ['porcentaje', 'monto']) ? $_POST['modo'] : 'porcentaje';
            $campo = in_array($_POST['campo'] ?? '', ['costo', 'precio']) ? $_POST['campo'] : 'costo';
            $valor = isset($_POST['valor']) ? floatval($_POST['valor']) : 0.00;

            if (empty($ids)) {
                $this->setFlash('error', 'No se seleccionaron productos para ajustar precios');
                $this->redirect('productos');
            }

            if ($valor <= 0) {
                $this->setFlash('error', 'El valor del ajuste debe ser mayor a 0');
                $this->redirect('productos');
            }

            $resultado = $this->productoModel->actualizarPreciosMasivo($ids, $tipo, $modo, $valor, $campo);
            if ($resultado) {
                $conteo = count($ids);
                $simbolo = ($modo === 'porcentaje') ? '%' : '$';
                $accion = ($tipo === 'aumentar') ? 'incrementaron' : 'redujeron';
                $campoTexto = ($campo === 'costo') ? 'los costos y precios de venta' : 'los precios de venta';
                $this->setFlash('success', "Se {$accion} {$campoTexto} de {$conteo} producto(s) en {$valor}{$simbolo}");
            } else {
                $this->setFlash('error', 'No se pudieron actualizar los precios de los productos');
            }
        }
        $this->redirect('productos');
    }

    public function limpiarPOST()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codigo_input = isset($_POST['codigo']) ? trim($_POST['codigo']) : null;
            $nombre_input = isset($_POST['nombre']) ? ucwords(trim($_POST['nombre'])) : '';
            $peso_input = isset($_POST['peso']) ? trim($_POST['peso']) : null;
            $categoria_input = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
            $unidad_input = isset($_POST['unidad']) ? trim($_POST['unidad']) : '';
            $costo_input = isset($_POST['precio_costo']) ? floatval(trim($_POST['precio_costo'])) : 0.00;
            $ganancia_input = isset($_POST['ganancia']) ? floatval(trim($_POST['ganancia'])) : 30.00;
            $iva_input = isset($_POST['iva']) ? floatval(trim($_POST['iva'])) : 16.00;
            $precio_input = isset($_POST['precio']) ? floatval(trim($_POST['precio'])) : 0.00;
            $stock_input = isset($_POST['stock']) ? intval(trim($_POST['stock'])) : 0;

            if (strlen($nombre_input) < 3) {
                $this->setFlash('error', 'El nombre debe tener mínimo 3 caracteres');
                $this->redirect('productos/crear');
            }

            if (empty($categoria_input) || !is_numeric($categoria_input)) {
                $this->setFlash('error', 'Debe seleccionar una categoría');
                $this->redirect('productos/crear');
            }

            if (empty($unidad_input) || !is_numeric($unidad_input)) {
                $this->setFlash('error', 'Debe seleccionar una unidad');
                $this->redirect('productos/crear');
            }

            if ($precio_input <= 0 && $costo_input > 0) {
                $precio_input = $costo_input * (1 + ($ganancia_input / 100)) * (1 + ($iva_input / 100));
            }

            if ($precio_input <= 0) {
                $this->setFlash('error', 'El precio debe ser un número válido mayor a 0');
                $this->redirect('productos/crear');
            }

            if (!empty($codigo_input) && strlen($codigo_input) < 5) {
                $this->setFlash('error', 'El código de barras debe tener mínimo 5 caracteres');
                $this->redirect('productos/crear');
            }

            if (!empty($peso_input) && !is_numeric($peso_input)) {
                $this->setFlash('error', 'El peso debe ser un número válido');
                $this->redirect('productos/crear');
            }

            $unidad_abreviatura = $this->getUnidadAbreviatura($unidad_input);

            $nombre_completo = $nombre_input;
            if (!empty($peso_input) && !empty($unidad_abreviatura)) {
                $peso_num = floatval($peso_input);
                $peso_formateado = ($peso_num == intval($peso_num)) ? intval($peso_num) : number_format($peso_num, 2);
                $nombre_completo .= ' ' . $peso_formateado . $unidad_abreviatura;
            } elseif (!empty($peso_input) && empty($unidad_abreviatura)) {
                $nombre_completo .= ' ' . $peso_input;
            }

            if (empty($codigo_input)) {
                $codigo_input = null;
            }

            if (empty($peso_input)) {
                $peso_input = null;
            }

            $stock_input = intval($stock_input);

            return [$codigo_input, $nombre_completo, $peso_input, $categoria_input, $unidad_input, $costo_input, $ganancia_input, $iva_input, $precio_input, $stock_input];
        }
    }

    private function getUnidadAbreviatura($unidad_id)
    {
        $unidadModel = new Unidad();
        $unidad = $unidadModel->consultarPorId($unidad_id);

        if (!empty($unidad)) {
            return $unidad[0]['unidad_abreviatura'];
        }
        return '';
    }

    public function limpiarVerificarId(): int
    {
        $id = $_GET['id'] ?? null;
        $producto_id = $this->validateNumericId($id, 'productos');

        $existe = $this->productoModel->limpiarVerificarId($producto_id);

        if ($existe) {
            return $producto_id;
        } else {
            $this->setFlash('error', 'Producto no encontrado');
            $this->redirect('productos');
        }
    }
}


