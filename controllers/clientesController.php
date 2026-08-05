<?php 

require_once './models/Cliente.php';

class clientesController
{

    private $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new Cliente();
    }

    ////////////////////////////////////////////////////
    ////////             FUNCIONES                //////
    ////////             DEL CRUD                 //////
    ////////    (CREATE, READ, UPDATE, DELETE)    //////
    ////////////////////////////////////////////////////


    public function listar(){

        $clientes = $this->clienteModel->listar();

        include ruta . '/views/clientes/clientes.php';
        exit();
    }


    public function crear(){

        if ($_SERVER['REQUEST_METHOD'] == 'POST'){

            list($cliente_nombre, $cliente_apellido, $cliente_cedula, $cliente_telefono, $cliente_correo) = $this->limpiarPOST();

            // Verificar duplicado por cédula (obligatorio)
            $verfduplicado = $this->clienteModel->verificarDuplicado($cliente_cedula);

            if ($verfduplicado){
                // Verificar duplicado por correo (solo si se proporcionó)
                $verfduplicadoCorreo = $this->clienteModel->verificarDuplicadoCorreo($cliente_correo);
                
                if ($verfduplicadoCorreo){
                    $resultado = $this->clienteModel->crear($cliente_nombre, $cliente_apellido, $cliente_cedula, $cliente_telefono, $cliente_correo);
                    if ($resultado){
                        $_SESSION['success'] = 'Cliente creado con exito';
                        header("Location:  ./index.php?controller=clientesController&action=listar");
                        exit();
                    }
                } else {
                    $_SESSION['error'] = 'Este correo ya está registrado';
                    header("Location:  ./index.php?controller=clientesController&action=listar");
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Esta cédula ya está registrada';
                header("Location:  ./index.php?controller=clientesController&action=listar");
                exit();
            }
            
        } else {
            $this->listar();
        }
    }


    public function editar(){
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){

            $cliente_id = $this->limpiarVerificarId();

            list($cliente_nombre, $cliente_apellido, $cliente_cedula, $cliente_telefono, $cliente_correo) = $this->limpiarPOST();

            // Verificar duplicado por cédula (obligatorio)
            $verifduplicado = $this->clienteModel->verificarDuplicadoId($cliente_cedula, $cliente_id);

            if ($verifduplicado){
                // Verificar duplicado por correo (solo si se proporcionó)
                $verifduplicadoCorreo = $this->clienteModel->verificarDuplicadoCorreoId($cliente_correo, $cliente_id);
                
                if ($verifduplicadoCorreo){
                    $resultado = $this->clienteModel->editar($cliente_nombre, $cliente_apellido, $cliente_cedula, $cliente_telefono, $cliente_correo, $cliente_id);
                    
                    if ($resultado){
                        $_SESSION['success'] = 'Cliente editado con exito';
                        header("Location:  ./index.php?controller=clientesController&action=listar");
                        exit();
                    }
                } else {
                    $_SESSION['error'] = 'Este correo ya está registrado';
                    header("Location:  ./index.php?controller=clientesController&action=listar");
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Esta cédula ya está registrada';
                header("Location:  ./index.php?controller=clientesController&action=listar");
                exit();
            }
        
        } else {
            $cliente_id = $this->limpiarVerificarId();
            $dato = $this->clienteModel->consultarPorId($cliente_id);

            include_once './views/clientes/clientes-editar.php';
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
            
            $nombre_input = isset($_POST['nombre']) ? ucfirst(trim($_POST['nombre'])) : '';
            $apellido_input = isset($_POST['apellido']) ? ucfirst(trim($_POST['apellido'])) : '';
            $cedula_input = isset($_POST['cedula']) ? trim($_POST['cedula']) : '';
            $telefono_input = isset($_POST['telefono']) ? trim($_POST['telefono']) : null;
            $correo_input = isset($_POST['correo']) ? trim($_POST['correo']) : null;
    
            // Validación del nombre (obligatorio)
            if (strlen($nombre_input) < 2){
                $_SESSION['error'] = 'El nombre debe tener mínimo 2 caracteres';
                header("Location:  ./index.php?controller=clientesController&action=listar");
                exit();
            }

            // Validación del apellido (obligatorio)
            if (strlen($apellido_input) < 2){
                $_SESSION['error'] = 'El apellido debe tener mínimo 2 caracteres';
                header("Location:  ./index.php?controller=clientesController&action=listar");
                exit();
            }

            // Validación de la cédula (obligatorio)
            if (strlen($cedula_input) < 7){
                $_SESSION['error'] = 'La cédula debe tener mínimo 7 caracteres';
                header("Location:  ./index.php?controller=clientesController&action=listar");
                exit();
            }

            // Validación del teléfono (opcional)
            if (!empty($telefono_input) && strlen($telefono_input) < 7){
                $_SESSION['error'] = 'El teléfono debe tener mínimo 7 caracteres';
                header("Location:  ./index.php?controller=clientesController&action=listar");
                exit();
            }

            // Validación del correo (opcional)
            if (!empty($correo_input) && !filter_var($correo_input, FILTER_VALIDATE_EMAIL)){
                $_SESSION['error'] = 'El correo no es válido';
                header("Location:  ./index.php?controller=clientesController&action=listar");
                exit();
            }

            // Si el teléfono está vacío, lo enviamos como NULL
            if (empty($telefono_input)) {
                $telefono_input = null;
            }

            // Si el correo está vacío, lo enviamos como NULL
            if (empty($correo_input)) {
                $correo_input = null;
            }

            return [$nombre_input, $apellido_input, $cedula_input, $telefono_input, $correo_input];
        }
    }


    public function limpiarVerificarId(){
        $cliente_id = isset($_GET['id']) ? trim($_GET['id']) : '?';

        if (!is_numeric($cliente_id) || $cliente_id === '0') {
            echo '<script>alert("id no valido")</script>';
            $this->listar();
            exit();
        }

        $resultado = $this->clienteModel->limpiarVerificarId($cliente_id);

        if ($resultado){
            return $cliente_id;
        } else {
            echo '<script>alert("id no encontrado")</script>';
            $this->listar();
            exit();
        }
    }
}
?>