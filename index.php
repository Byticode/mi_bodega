<?php

session_start();
define('RUTA_APP', __DIR__);
define('ruta', __DIR__);

require_once RUTA_APP . '/config/app.php';
require_once RUTA_APP . '/includes/helpers.php';
require_once RUTA_APP . '/core/BaseController.php';
require_once RUTA_APP . '/core/BaseModel.php';
require_once RUTA_APP . '/core/TasaService.php';

spl_autoload_register(function ($class) {
    $candidates = [
        RUTA_APP . '/controllers/' . $class . '.php',
        RUTA_APP . '/controllers/' . lcfirst($class) . '.php',
        RUTA_APP . '/models/' . $class . '.php',
        RUTA_APP . '/models/' . lcfirst($class) . '.php',
    ];

    foreach ($candidates as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

$action = isset($_GET['action']) ? trim($_GET['action']) : 'pos';
$controller = isset($_GET['controller']) ? trim($_GET['controller']) : 'ventasController';

switch ($controller) {
    case 'categoriasController':
        require_once './controllers/categoriasController.php';
        $controller = new categoriasController();
        break;
    case 'proveedoresController':
        require_once './controllers/proveedoresController.php';
        $controller = new proveedoresController();
        break;
    case 'clientesController':
        require_once './controllers/clientesController.php';
        $controller = new clientesController();
        break;
    case 'productosController':
        require_once './controllers/productosController.php';
        $controller = new productosController();
        break;
    case 'ventasController':
        require_once './controllers/ventasController.php';
        $controller = new ventasController();
        break;
    case 'surtidosController':
        require_once './controllers/surtidosController.php';
        $controller = new surtidosController();
        break;
    case 'unidadesController':
        require_once './controllers/unidadesController.php';
        $controller = new unidadesController();
        break;
    case 'tasaMonedaController':
        require_once './controllers/tasaMonedaController.php';
        $controller = new tasaMonedaController();
        break;
    case 'usuariosController':
        require_once './controllers/usuariosController.php';
        $controller = new usuariosController();
        break;
    default:
        header("Location: index.php?controller=categoriasController&action=listar");
        exit();
}

$actions_permitidos = ['listar', 'crear', 'borrar', 'editar', 'ver', 'pos', 'status', 'actualizar'];

if (!in_array($action, $actions_permitidos)) {
    $_SESSION['error'] = "action no valido";
    header("Location: index.php?action=listar");
    exit();
}

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    $_SESSION['error'] = "action no valido";
    header("Location: index.php?action=listar");
    exit();
}
?>
