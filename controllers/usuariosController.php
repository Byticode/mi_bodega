<?php 

class UsuariosController extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->requireRole('admin');
        $this->usuarioModel = new Usuario();
    }


    public function listar()
    {
        $usuarios = $this->usuarioModel->listar();
        include RUTA_APP . '/views/usuarios/usuarios.php';
        exit();
    }

    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            list($usuario_nombre, $usuario_username, $usuario_clave, $usuario_rol) = $this->limpiarPOST();

            $verfduplicado = $this->usuarioModel->verificarDuplicado($usuario_username);

            if ($verfduplicado) {
                $resultado = $this->usuarioModel->crear($usuario_nombre, $usuario_username, $usuario_clave, $usuario_rol);
                if ($resultado) {
                    $this->setFlash('success', 'Usuario creado con éxito');
                    $this->redirect('usuarios');
                }
            } else {
                $this->setFlash('error', 'Este nombre de usuario ya existe');
                $this->redirect('usuarios');
            }
        } else {
            $this->listar();
        }
    }

    public function editar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario_id = $this->limpiarVerificarId();

            list($usuario_nombre, $usuario_username, $usuario_clave, $usuario_rol) = $this->limpiarPOST();

            $verifduplicado = $this->usuarioModel->verificarDuplicadoId($usuario_username, $usuario_id);

            if ($verifduplicado) {
                if (!empty($usuario_clave)) {
                    $resultado = $this->usuarioModel->editarConClave($usuario_nombre, $usuario_username, $usuario_clave, $usuario_rol, $usuario_id);
                } else {
                    $resultado = $this->usuarioModel->editar($usuario_nombre, $usuario_username, $usuario_rol, $usuario_id);
                }
                
                if ($resultado) {
                    $this->setFlash('success', 'Usuario editado con éxito');
                    $this->redirect('usuarios');
                }
            } else {
                $this->setFlash('error', 'Este nombre de usuario ya existe');
                $this->redirect('usuarios');
            }
        } else {
            $usuario_id = $this->limpiarVerificarId();
            $dato = $this->usuarioModel->consultarPorId($usuario_id);

            include RUTA_APP . '/views/usuarios/usuarios-editar.php';
            exit();
        }
    }

    public function limpiarPOST()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre_input = isset($_POST['nombre']) ? ucwords(trim($_POST['nombre'])) : '';
            $username_input = isset($_POST['username']) ? strtolower(trim($_POST['username'])) : '';
            $clave_input = isset($_POST['clave']) ? trim($_POST['clave']) : '';
            $rol_input = isset($_POST['rol']) ? trim($_POST['rol']) : 'vendedor';

            if (strlen($nombre_input) < 2) {
                $this->setFlash('error', 'El nombre debe tener mínimo 2 caracteres');
                $this->redirect('usuarios');
            }

            if (strlen($username_input) < 3) {
                $this->setFlash('error', 'El nombre de usuario debe tener mínimo 3 caracteres');
                $this->redirect('usuarios');
            }

            $is_creacion = strpos($_SERVER['REQUEST_URI'], 'action=crear') !== false;
            if ($is_creacion && strlen($clave_input) < 4) {
                $this->setFlash('error', 'La contraseña debe tener mínimo 4 caracteres');
                $this->redirect('usuarios');
            }

            $roles_permitidos = ['admin', 'vendedor'];
            if (!in_array($rol_input, $roles_permitidos)) {
                $this->setFlash('error', 'Rol no válido');
                $this->redirect('usuarios');
            }

            return [$nombre_input, $username_input, $clave_input, $rol_input];
        }
    }

    public function limpiarVerificarId(): int
    {
        $id = $_GET['id'] ?? null;
        $usuario_id = $this->validateNumericId($id, 'usuarios');

        $resultado = $this->usuarioModel->limpiarVerificarId($usuario_id);

        if ($resultado) {
            return $usuario_id;
        } else {
            $this->setFlash('error', 'Usuario no encontrado');
            $this->redirect('usuarios');
        }
    }
}