<?php

require_once './models/Producto.php';
require_once './models/Unidad.php';

class productosController
{

    private $productoModel;

    public function __construct()
    {
        $this->productoModel = new Producto();
    }

    ////////////////////////////////////////////////////
    ////////             FUNCIONES                //////
    ////////             DEL CRUD                 //////
    ////////    (CREATE, READ, UPDATE, DELETE)    //////
    ////////////////////////////////////////////////////


    public function listar()
    {
        $productos = $this->productoModel->listar();
        $categorias = $this->productoModel->obtenerCategorias();
        $unidades = $this->productoModel->obtenerUnidades();

        include ruta . '/views/productos/productos.php';
        exit();
    }


    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            list($producto_codigo, $producto_nombre, $producto_peso, $categoria_id, $unidad_id, $producto_precio_venta, $producto_stock) = $this->limpiarPOST();

            $verfduplicado = $this->productoModel->verificarDuplicado($producto_nombre);

            if ($verfduplicado) {
                $verfduplicadoCodigo = $this->productoModel->verificarDuplicadoCodigo($producto_codigo);

                if ($verfduplicadoCodigo) {
                    $resultado = $this->productoModel->crear($producto_codigo, $producto_nombre, $producto_peso, $categoria_id, $unidad_id, $producto_precio_venta, $producto_stock);
                    if ($resultado) {
                        $_SESSION['success'] = 'Producto creado con exito';
                        header("Location:  ./index.php?controller=productosController&action=listar");
                        exit();
                    }
                } else {
                    $_SESSION['error'] = 'Este código de barras ya está registrado';
                    header("Location:  ./index.php?controller=productosController&action=crear");
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Este producto ya existe';
                header("Location:  ./index.php?controller=productosController&action=crear");
                exit();
            }
        } else {
            $categorias = $this->productoModel->obtenerCategorias();
            $unidades = $this->productoModel->obtenerUnidades();

            include ruta . '/views/productos/productos-crear.php';
            exit();
        }
    }


    public function editar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $producto_id = $this->limpiarVerificarId();

            // Obtener los datos del formulario
            $codigo_input = isset($_POST['codigo']) ? trim($_POST['codigo']) : null;
            $nombre_input = isset($_POST['nombre']) ? ucwords(trim($_POST['nombre'])) : '';
            $peso_input = isset($_POST['peso']) ? trim($_POST['peso']) : null;
            $categoria_input = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
            $unidad_input = isset($_POST['unidad']) ? trim($_POST['unidad']) : '';
            $precio_input = isset($_POST['precio']) ? trim($_POST['precio']) : '';
            $stock_input = isset($_POST['stock']) ? intval(trim($_POST['stock'])) : 0;

            // Validaciones
            if (strlen($nombre_input) < 3) {
                $_SESSION['error'] = 'El nombre debe tener mínimo 3 caracteres';
                header("Location:  ./index.php?controller=productosController&action=editar&id=" . $producto_id);
                exit();
            }

            if (empty($categoria_input) || !is_numeric($categoria_input)) {
                $_SESSION['error'] = 'Debe seleccionar una categoría';
                header("Location:  ./index.php?controller=productosController&action=editar&id=" . $producto_id);
                exit();
            }

            if (empty($unidad_input) || !is_numeric($unidad_input)) {
                $_SESSION['error'] = 'Debe seleccionar una unidad';
                header("Location:  ./index.php?controller=productosController&action=editar&id=" . $producto_id);
                exit();
            }

            if (!is_numeric($precio_input) || $precio_input <= 0) {
                $_SESSION['error'] = 'El precio debe ser un número válido mayor a 0';
                header("Location:  ./index.php?controller=productosController&action=editar&id=" . $producto_id);
                exit();
            }

            if (!empty($codigo_input) && strlen($codigo_input) < 5) {
                $_SESSION['error'] = 'El código de barras debe tener mínimo 5 caracteres';
                header("Location:  ./index.php?controller=productosController&action=editar&id=" . $producto_id);
                exit();
            }

            if (!empty($peso_input) && !is_numeric($peso_input)) {
                $_SESSION['error'] = 'El peso debe ser un número válido';
                header("Location:  ./index.php?controller=productosController&action=editar&id=" . $producto_id);
                exit();
            }

            // Obtener la abreviatura de la unidad
            $unidad_abreviatura = $this->getUnidadAbreviatura($unidad_input);

            // Construir el nombre completo con peso y unidad
            $nombre_completo = $nombre_input;
            if (!empty($peso_input) && !empty($unidad_abreviatura)) {
                $peso_num = floatval($peso_input);
                $peso_formateado = ($peso_num == intval($peso_num)) ? intval($peso_num) : number_format($peso_num, 2);
                $nombre_completo .= ' ' . $peso_formateado . $unidad_abreviatura;
            } elseif (!empty($peso_input) && empty($unidad_abreviatura)) {
                $nombre_completo .= ' ' . $peso_input;
            }

            // Verificar duplicados
            $verifduplicado = $this->productoModel->verificarDuplicadoId($nombre_completo, $producto_id);

            if ($verifduplicado) {
                $verifduplicadoCodigo = $this->productoModel->verificarDuplicadoCodigoId($codigo_input, $producto_id);

                if ($verifduplicadoCodigo) {
                    $resultado = $this->productoModel->editar($codigo_input, $nombre_completo, $peso_input, $categoria_input, $unidad_input, $precio_input, $stock_input, $producto_id);

                    if ($resultado) {
                        $_SESSION['success'] = 'Producto editado con exito';
                        header("Location:  ./index.php?controller=productosController&action=listar");
                        exit();
                    }
                } else {
                    $_SESSION['error'] = 'Este código de barras ya está registrado';
                    header("Location:  ./index.php?controller=productosController&action=editar&id=" . $producto_id);
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Este producto ya existe';
                header("Location:  ./index.php?controller=productosController&action=editar&id=" . $producto_id);
                exit();
            }
        } else {
            $producto_id = $this->limpiarVerificarId();
            $dato = $this->productoModel->consultarPorId($producto_id);
            $categorias = $this->productoModel->obtenerCategorias();
            $unidades = $this->productoModel->obtenerUnidades();

            include_once './views/productos/productos-editar.php';
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
            $peso_input = isset($_POST['peso']) ? trim($_POST['peso']) : null;
            $categoria_input = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
            $unidad_input = isset($_POST['unidad']) ? trim($_POST['unidad']) : '';
            $precio_input = isset($_POST['precio']) ? trim($_POST['precio']) : '';
            $stock_input = isset($_POST['stock']) ? intval(trim($_POST['stock'])) : 0; // Convertir a entero

            // Validación del nombre (obligatorio)
            if (strlen($nombre_input) < 3) {
                $_SESSION['error'] = 'El nombre debe tener mínimo 3 caracteres';
                header("Location:  ./index.php?controller=productosController&action=crear");
                exit();
            }

            // Validación de la categoría
            if (empty($categoria_input) || !is_numeric($categoria_input)) {
                $_SESSION['error'] = 'Debe seleccionar una categoría';
                header("Location:  ./index.php?controller=productosController&action=crear");
                exit();
            }

            // Validación de la unidad
            if (empty($unidad_input) || !is_numeric($unidad_input)) {
                $_SESSION['error'] = 'Debe seleccionar una unidad';
                header("Location:  ./index.php?controller=productosController&action=crear");
                exit();
            }

            // Validación del precio
            if (!is_numeric($precio_input) || $precio_input <= 0) {
                $_SESSION['error'] = 'El precio debe ser un número válido mayor a 0';
                header("Location:  ./index.php?controller=productosController&action=crear");
                exit();
            }

            // Validación del código (opcional)
            if (!empty($codigo_input) && strlen($codigo_input) < 5) {
                $_SESSION['error'] = 'El código de barras debe tener mínimo 5 caracteres';
                header("Location:  ./index.php?controller=productosController&action=crear");
                exit();
            }

            // Validación del peso (opcional)
            if (!empty($peso_input) && !is_numeric($peso_input)) {
                $_SESSION['error'] = 'El peso debe ser un número válido';
                header("Location:  ./index.php?controller=productosController&action=crear");
                exit();
            }

            // Obtener la abreviatura de la unidad
            $unidad_abreviatura = $this->getUnidadAbreviatura($unidad_input);

            // Construir el nombre completo con peso y unidad
            $nombre_completo = $nombre_input;
            if (!empty($peso_input) && !empty($unidad_abreviatura)) {
                // Formatear el peso: si es entero, mostrar sin decimales
                $peso_num = floatval($peso_input);
                $peso_formateado = ($peso_num == intval($peso_num)) ? intval($peso_num) : number_format($peso_num, 2);
                $nombre_completo .= ' ' . $peso_formateado . $unidad_abreviatura;
            } elseif (!empty($peso_input) && empty($unidad_abreviatura)) {
                $nombre_completo .= ' ' . $peso_input;
            }

            // Si el código está vacío, lo enviamos como NULL
            if (empty($codigo_input)) {
                $codigo_input = null;
            }

            // Si el peso está vacío, lo enviamos como NULL
            if (empty($peso_input)) {
                $peso_input = null;
            }

            // Asegurar que el stock sea entero
            $stock_input = intval($stock_input);

            return [$codigo_input, $nombre_completo, $peso_input, $categoria_input, $unidad_input, $precio_input, $stock_input];
        }
    }

    private function getUnidadAbreviatura($unidad_id)
    {
        require_once './models/Unidad.php';
        $unidadModel = new Unidad();
        $unidad = $unidadModel->consultarPorId($unidad_id);

        if (!empty($unidad)) {
            return $unidad[0]['unidad_abreviatura'];
        }
        return '';
    }


    public function limpiarVerificarId()
    {
        $producto_id = isset($_GET['id']) ? trim($_GET['id']) : '?';

        if (!is_numeric($producto_id) || $producto_id === '0') {
            echo '<script>alert("id no valido")</script>';
            $this->listar();
            exit();
        }

        $resultado = $this->productoModel->limpiarVerificarId($producto_id);

        if ($resultado) {
            return $producto_id;
        } else {
            echo '<script>alert("id no encontrado")</script>';
            $this->listar();
            exit();
        }
    }
}
