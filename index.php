<?php

session_start();

define('RUTA_APP', __DIR__);
define('ruta', __DIR__);

require_once RUTA_APP . '/config/app.php';
require_once RUTA_APP . '/includes/helpers.php';
require_once RUTA_APP . '/core/BaseController.php';
require_once RUTA_APP . '/core/BaseModel.php';
require_once RUTA_APP . '/core/TasaService.php';
require_once RUTA_APP . '/core/Router.php';

spl_autoload_register(function ($class) {
    $candidates = [
        RUTA_APP . '/controllers/' . $class . '.php',
        RUTA_APP . '/controllers/' . lcfirst($class) . '.php',
        RUTA_APP . '/controllers/' . ucfirst($class) . '.php',
        RUTA_APP . '/models/' . $class . '.php',
        RUTA_APP . '/models/' . lcfirst($class) . '.php',
        RUTA_APP . '/models/' . ucfirst($class) . '.php',
        RUTA_APP . '/core/' . $class . '.php',
    ];

    foreach ($candidates as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

$router = new Router();
$router->dispatch();

