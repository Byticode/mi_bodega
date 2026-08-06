<?php

class ClientesController extends BaseController
{
    private $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new Cliente();
    }

    public function listar()
    {
        $clientes = $this->clienteModel->listar();
        $this->render('clientes/clientes.php', compact('clientes'));
    }

    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->listar();
        }

        $data = $this->sanitizePost();

        $this->validateUnique(
            $this->clienteModel->isDuplicateCedula($data['cedula']),
            'Esta cédula ya está registrada',
            'index.php?controller=clientesController&action=listar'
        );

        $this->validateUnique(
            !$this->clienteModel->isDuplicateCorreo($data['correo']),
            'Este correo ya está registrado',
            'index.php?controller=clientesController&action=listar'
        );

        $success = $this->clienteModel->crear(
            $data['nombre'],
            $data['apellido'],
            $data['cedula'],
            $data['telefono'],
            $data['correo']
        );

        if ($success) {
            $this->setFlash('success', 'Cliente creado con éxito');
        } else {
            $this->setFlash('error', 'No se pudo crear el cliente');
        }

        $this->redirect('index.php?controller=clientesController&action=listar');
    }

    public function editar()
    {
        $cliente_id = $this->validateId($_GET['id'] ?? '');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->sanitizePost();

            $this->validateUnique(
                $this->clienteModel->isDuplicateCedulaExceptId($data['cedula'], $cliente_id),
                'Esta cédula ya está registrada',
                'index.php?controller=clientesController&action=listar'
            );

            $this->validateUnique(
                !$this->clienteModel->isDuplicateCorreoExceptId($data['correo'], $cliente_id),
                'Este correo ya está registrado',
                'index.php?controller=clientesController&action=listar'
            );

            $success = $this->clienteModel->editar(
                $data['nombre'],
                $data['apellido'],
                $data['cedula'],
                $data['telefono'],
                $data['correo'],
                $cliente_id
            );

            if ($success) {
                $this->setFlash('success', 'Cliente editado con éxito');
            } else {
                $this->setFlash('error', 'No se pudo editar el cliente');
            }

            $this->redirect('index.php?controller=clientesController&action=listar');
        }

        $dato = $this->clienteModel->consultarPorId($cliente_id);

        if (!$dato) {
            $this->setFlash('error', 'Cliente no encontrado');
            $this->redirect('index.php?controller=clientesController&action=listar');
        }

        $this->render('clientes/clientes-editar.php', compact('dato'));
    }

    private function sanitizePost(): array
    {
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $cedula = trim($_POST['cedula'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $correo = trim($_POST['correo'] ?? '');

        if (strlen($nombre) < 2) {
            $this->setFlash('error', 'El nombre debe tener mínimo 2 caracteres');
            $this->redirect('index.php?controller=clientesController&action=listar');
        }

        if (strlen($apellido) < 2) {
            $this->setFlash('error', 'El apellido debe tener mínimo 2 caracteres');
            $this->redirect('index.php?controller=clientesController&action=listar');
        }

        if (strlen($cedula) < 7) {
            $this->setFlash('error', 'La cédula debe tener mínimo 7 caracteres');
            $this->redirect('index.php?controller=clientesController&action=listar');
        }

        if ($telefono !== '' && strlen($telefono) < 7) {
            $this->setFlash('error', 'El teléfono debe tener mínimo 7 caracteres');
            $this->redirect('index.php?controller=clientesController&action=listar');
        }

        if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'El correo no es válido');
            $this->redirect('index.php?controller=clientesController&action=listar');
        }

        return [
            'nombre' => ucfirst($nombre),
            'apellido' => ucfirst($apellido),
            'cedula' => $cedula,
            'telefono' => $telefono ?: null,
            'correo' => $correo ?: null,
        ];
    }

    public function borrar()
    {
        $cliente_id = $this->validateId($_GET['id'] ?? '');
        $success = $this->clienteModel->borrar($cliente_id);

        $this->setFlash($success ? 'success' : 'error', $success ? 'Cliente eliminado con éxito' : 'No se pudo eliminar el cliente');
        $this->redirect('index.php?controller=clientesController&action=listar');
    }

    public function status()
    {
        $cliente_id = $this->validateId($_GET['id'] ?? '');
        $status = trim($_GET['status'] ?? '');

        if ($status === '') {
            $this->setFlash('error', 'Estado no proporcionado');
            $this->redirect('index.php?controller=clientesController&action=listar');
        }

        $success = $this->clienteModel->changeStatus($cliente_id, $status);
        $this->setFlash($success ? 'success' : 'error', $success ? 'Status actualizado' : 'No se pudo cambiar el status');
        $this->redirect('index.php?controller=clientesController&action=listar');
    }

    private function validateId($id): int
    {
        if (!is_numeric($id) || intval($id) <= 0) {
            $this->setFlash('error', 'ID no válido');
            $this->redirect('index.php?controller=clientesController&action=listar');
        }

        $id = intval($id);

        if (!$this->clienteModel->existsId($id)) {
            $this->setFlash('error', 'ID no encontrado');
            $this->redirect('index.php?controller=clientesController&action=listar');
        }

        return $id;
    }
}