<?php 

require_once './models/Usuario.php';

class usuariosController
{

    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    ////////////////////////////////////////////////////
    ////////             FUNCIONES                //////
    ////////             DEL CRUD                 //////
    ////////    (CREATE, READ, UPDATE, DELETE)    //////
    ////////////////////////////////////////////////////


    public function listar(){

        $usuarios = $this->usuarioModel->listar();

        include ruta . '/views/usuarios/usuarios.php';
        exit();
    }


    public function crear(){

        if ($_SERVER['REQUEST_METHOD'] == 'POST'){

            list($usuario_nombre, $usuario_username, $usuario_clave, $usuario_rol) = $this->limpiarPOST();

            $verfduplicado = $this->usuarioModel->verificarDuplicado($usuario_username);

            if ($verfduplicado){
                $resultado = $this->usuarioModel->crear($usuario_nombre, $usuario_username, $usuario_clave, $usuario_rol);
                if ($resultado){
                    $_SESSION['success'] = 'Usuario creado con exito';
                    header("Location:  ./index.php?controller=usuariosController&action=listar");
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Este nombre de usuario ya existe';
                header("Location:  ./index.php?controller=usuariosController&action=listar");
                exit();
            }
            
        } else {
            $this->listar();
        }
    }


    public function editar(){
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){

            $usuario_id = $this->limpiarVerificarId();

            list($usuario_nombre, $usuario_username, $usuario_clave, $usuario_rol) = $this->limpiarPOST();

            $verifduplicado = $this->usuarioModel->verificarDuplicadoId($usuario_username, $usuario_id);

            if ($verifduplicado){
                // Si se ingresó una nueva clave, actualizar con clave
                if (!empty($usuario_clave)) {
                    $resultado = $this->usuarioModel->editarConClave($usuario_nombre, $usuario_username, $usuario_clave, $usuario_rol, $usuario_id);
                } else {
                    $resultado = $this->usuarioModel->editar($usuario_nombre, $usuario_username, $usuario_rol, $usuario_id);
                }
                
                if ($resultado){
                    $_SESSION['success'] = 'Usuario editado con exito';
                    header("Location:  ./index.php?controller=usuariosController&action=listar");
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Este nombre de usuario ya existe';
                header("Location:  ./index.php?controller=usuariosController&action=listar");
                exit();
            }
        
        } else {
            $usuario_id = $this->limpiarVerificarId();
            $dato = $this->usuarioModel->consultarPorId($usuario_id);

            include_once './views/usuarios/usuarios-editar.php';
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
            
            $nombre_input = isset($_POST['nombre']) ? ucwords(trim($_POST['nombre'])) : '';
            $username_input = isset($_POST['username']) ? strtolower(trim($_POST['username'])) : '';
            $clave_input = isset($_POST['clave']) ? trim($_POST['clave']) : '';
            $rol_input = isset($_POST['rol']) ? trim($_POST['rol']) : 'vendedor';
    
            // Validación del nombre
            if (strlen($nombre_input) < 2){
                $_SESSION['error'] = 'El nombre debe tener mínimo 2 caracteres';
                header("Location:  ./index.php?controller=usuariosController&action=listar");
                exit();
            }

            // Validación del username
            if (strlen($username_input) < 3){
                $_SESSION['error'] = 'El nombre de usuario debe tener mínimo 3 caracteres';
                header("Location:  ./index.php?controller=usuariosController&action=listar");
                exit();
            }

            // Validación de la clave (solo en creación)
            $is_creacion = strpos($_SERVER['REQUEST_URI'], 'action=crear') !== false;
            if ($is_creacion && strlen($clave_input) < 4){
                $_SESSION['error'] = 'La contraseña debe tener mínimo 4 caracteres';
                header("Location:  ./index.php?controller=usuariosController&action=listar");
                exit();
            }

            // Validación del rol
            $roles_permitidos = ['admin', 'vendedor'];
            if (!in_array($rol_input, $roles_permitidos)) {
                $_SESSION['error'] = 'Rol no válido';
                header("Location:  ./index.php?controller=usuariosController&action=listar");
                exit();
            }

            return [$nombre_input, $username_input, $clave_input, $rol_input];
        }
    }


    public function limpiarVerificarId(){
        $usuario_id = isset($_GET['id']) ? trim($_GET['id']) : '?';

        if (!is_numeric($usuario_id) || $usuario_id === '0') {
            echo '<script>alert("id no valido")</script>';
            $this->listar();
            exit();
        }

        $resultado = $this->usuarioModel->limpiarVerificarId($usuario_id);

        if ($resultado){
            return $usuario_id;
        } else {
            echo '<script>alert("id no encontrado")</script>';
            $this->listar();
            exit();
        }
    }
}
?>