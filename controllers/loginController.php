<?php

class LoginController extends BaseController
{
    private $usuarioModel;
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_TIME = 300; 

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function login()
    {
        if (!empty($_SESSION['usuario'])) {
            $this->setFlash('error', 'Ya tienes una sesión activa como "' . htmlspecialchars($_SESSION['usuario']['usuario_nombre']) . '". Cierra sesión si deseas cambiar de cuenta.');
            $this->redirect('pos');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $token = $_POST['csrf_token'] ?? '';
            if (empty($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
                $this->setFlash('error', 'Token de seguridad inválido o sesión expirada. Intenta de nuevo.');
                $this->redirect('login');
            }

            $attemptsData = $_SESSION['login_attempts'] ?? ['count' => 0, 'time' => 0];
            if ($attemptsData['count'] >= self::MAX_ATTEMPTS) {
                $elapsed = time() - $attemptsData['time'];
                if ($elapsed < self::LOCKOUT_TIME) {
                    $remaining = self::LOCKOUT_TIME - $elapsed;
                    $this->setFlash('error', "Demasiados intentos fallidos. Por seguridad, espera {$remaining} segundos antes de intentar de nuevo.");
                    $this->redirect('login');
                } else {
                    $_SESSION['login_attempts'] = ['count' => 0, 'time' => 0];
                }
            }

            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $_SESSION['old']['username'] = $username;

            if (empty($username) || empty($password)) {
                $this->setFlash('error', 'Por favor ingresa tanto tu usuario como tu contraseña');
                $this->redirect('login');
            }

            $usuario = $this->usuarioModel->verificarCredenciales($username, $password);

            if ($usuario) {
                unset($_SESSION['login_attempts'], $_SESSION['old']);
                session_regenerate_id(true); 

                $_SESSION['usuario'] = [
                    'usuario_id'       => $usuario['usuario_id'],
                    'usuario_nombre'   => $usuario['usuario_nombre'],
                    'usuario_username' => $usuario['usuario_username'],
                    'usuario_rol'      => $usuario['usuario_rol'],
                ];

                $this->setFlash('success', '¡Bienvenido de nuevo, ' . htmlspecialchars($usuario['usuario_nombre']) . '!');
                $this->redirect('pos');
            } else {
                $count = ($attemptsData['count'] ?? 0) + 1;
                $_SESSION['login_attempts'] = [
                    'count' => $count,
                    'time'  => time()
                ];

                $remainingAttempts = self::MAX_ATTEMPTS - $count;
                if ($remainingAttempts > 0) {
                    $this->setFlash('error', "Usuario o contraseña incorrectos. Te quedan {$remainingAttempts} intento(s).");
                } else {
                    $this->setFlash('error', "Ha superado el límite de 5 intentos fallidos. Su acceso ha sido bloqueado por 5 minutos.");
                }

                $this->redirect('login');
            }
        }

        include RUTA_APP . '/views/login/login.php';
        exit();
    }

    public function logout()
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        $this->setFlash('success', 'Sesión cerrada correctamente');
        $this->redirect('login');
    }
}
