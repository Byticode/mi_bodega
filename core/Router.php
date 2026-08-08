<?php

class Router
{
    private array $routeAliases = [
        'login'         => 'LoginController',
        'logout'        => 'LoginController',
        'pos'           => 'VentasController',
        'ventas'        => 'VentasController',
        'productos'     => 'ProductosController',
        'surtidos'      => 'SurtidosController',
        'usuarios'      => 'UsuariosController',
        'clientes'      => 'ClientesController',
        'proveedores'   => 'ProveedoresController',
        'categorias'    => 'CategoriasController',
        'unidades'      => 'UnidadesController',
        'tasa-moneda'   => 'TasaMonedaController',
        'tasamoneda'    => 'TasaMonedaController',
    ];

    public function dispatch(): void
    {
        try {
            $controllerName = null;
            $action = null;
            $params = [];

            if (isset($_GET['controller'])) {
                $controllerName = trim($_GET['controller']);
                $action = isset($_GET['action']) ? trim($_GET['action']) : 'listar';
            } else {
                $urlPath = isset($_GET['url']) ? trim($_GET['url'], '/') : '';

                if (empty($urlPath)) {
                    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
                    $baseUrl = trim(BASE_URL, '/');
                    if (!empty($baseUrl) && strpos($uri, '/' . $baseUrl) === 0) {
                        $uri = substr($uri, strlen('/' . $baseUrl));
                    }
                    $urlPath = trim($uri, '/');
                }

                if (strpos($urlPath, 'index.php') === 0) {
                    $urlPath = trim(substr($urlPath, strlen('index.php')), '/');
                }

                $segments = array_values(array_filter(explode('/', $urlPath)));

                $resource = !empty($segments[0]) ? strtolower($segments[0]) : 'pos';
                
                // Mapear rutas directas especiales como /login o /logout o /pos
                if ($resource === 'login') {
                    $controllerName = 'LoginController';
                    $action = 'login';
                } elseif ($resource === 'logout') {
                    $controllerName = 'LoginController';
                    $action = 'logout';
                } elseif ($resource === 'pos') {
                    $controllerName = 'VentasController';
                    $action = 'pos';
                } else {
                    $action = !empty($segments[1]) ? strtolower($segments[1]) : 'listar';

                    if (isset($segments[2])) {
                        $_GET['id'] = $segments[2];
                        $params[] = $segments[2];
                    }

                    if (isset($this->routeAliases[$resource])) {
                        $controllerName = $this->routeAliases[$resource];
                    } else {
                        $controllerName = ucfirst($resource) . 'Controller';
                    }
                }
            }

            $controllerName = ucfirst($controllerName);

            if (!class_exists($controllerName)) {
                $lcController = lcfirst($controllerName);
                if (class_exists($lcController)) {
                    $controllerName = $lcController;
                }
            }

            if (!class_exists($controllerName)) {
                $this->renderErrorPage(404, "El módulo o controlador '{$controllerName}' no fue encontrado.");
                return;
            }

            $controllerInstance = new $controllerName();

            if (!method_exists($controllerInstance, $action)) {
                $this->renderErrorPage(404, "La acción '{$action}' no existe en este controlador.");
                return;
            }

            call_user_func_array([$controllerInstance, $action], $params);

        } catch (Throwable $e) {
            error_log("Unhandled Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
            $this->renderErrorPage(500, "Error interno en la aplicación. Por favor contacta al administrador.");
        }
    }

    private function renderErrorPage(int $code, string $message): void
    {
        http_response_code($code);
        $viewFile = RUTA_APP . "/views/errors/{$code}.php";
        if (!file_exists($viewFile)) {
            $viewFile = RUTA_APP . "/views/errors/500.php";
        }
        $errorMessage = $message;
        include $viewFile;
        exit();
    }
}
