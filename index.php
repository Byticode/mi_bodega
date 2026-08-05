<?php 

session_start();

define('ruta', __DIR__);


$action = isset($_GET['action']) ? trim($_GET['action']) : 'listar';
$controller = isset($_GET['controller']) ? trim($_GET['controller']) : 'categoriasController';

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
}


$actions_permitidos = ['listar', 'crear', 'borrar', 'editar'];

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


