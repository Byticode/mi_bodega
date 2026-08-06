<?php

session_start();
define('RUTA_APP', __DIR__);

require_once RUTA_APP . '/config/app.php';
require_once RUTA_APP . '/includes/helpers.php';
require_once RUTA_APP . '/core/BaseController.php';
require_once RUTA_APP . '/core/BaseModel.php';

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

$action = isset($_GET['action']) ? trim($_GET['action']) : DEFAULT_ACTION;
$controllerKey = isset($_GET['controller']) ? trim($_GET['controller']) : DEFAULT_CONTROLLER;

$allowedControllers = [
    'categoriasController' => 'CategoriasController',
    'proveedoresController' => 'ProveedoresController',
    'clientesController' => 'ClientesController',
];

$allowedActions = ['listar', 'crear', 'editar', 'borrar', 'status'];

$controllerClass = $allowedControllers[$controllerKey] ?? $allowedControllers[DEFAULT_CONTROLLER];

if (!class_exists($controllerClass) || !in_array($action, $allowedActions, true)) {
    set_flash('error', 'Controlador o acción no válida');
    redirect('index.php?controller=' . DEFAULT_CONTROLLER . '&action=' . DEFAULT_ACTION);
}

$controller = new $controllerClass();

if (!method_exists($controller, $action)) {
    set_flash('error', 'Acción no válida');
    redirect('index.php?controller=' . DEFAULT_CONTROLLER . '&action=' . DEFAULT_ACTION);
}

$controller->$action();