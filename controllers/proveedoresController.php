<?php

class ProveedoresController extends BaseController
{
    private $proveedorModel;

    public function __construct()
    {
        $this->proveedorModel = new Proveedor();
    }

    public function listar()
    {
        $proveedores = $this->proveedorModel->listar();
        $this->render('proveedores/proveedores.php', compact('proveedores'));
    }

    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->listar();
        }

        $data = $this->sanitizePost([
            'nombre' => 3,
            'telefono' => 7,
        ]);

        $this->validateUnique(
            $this->proveedorModel->isDuplicateNombre($data['nombre']),
            'Este proveedor ya existe',
            'index.php?controller=proveedoresController&action=listar'
        );

        $success = $this->proveedorModel->crear($data['nombre'], $data['telefono']);

        if ($success) {
            $this->setFlash('success', 'Proveedor creado con éxito');
        } else {
            $this->setFlash('error', 'No se pudo crear el proveedor');
        }

        $this->redirect('index.php?controller=proveedoresController&action=listar');
    }

    public function editar()
    {
        $proveedor_id = $this->validateId($_GET['id'] ?? '');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->sanitizePost([
                'nombre' => 3,
                'telefono' => 7,
            ]);

            $this->validateUnique(
                $this->proveedorModel->isDuplicateNombreExceptId($data['nombre'], $proveedor_id),
                'Este proveedor ya existe',
                'index.php?controller=proveedoresController&action=listar'
            );

            $success = $this->proveedorModel->editar($data['nombre'], $data['telefono'], $proveedor_id);

            if ($success) {
                $this->setFlash('success', 'Proveedor editado con éxito');
            } else {
                $this->setFlash('error', 'No se pudo editar el proveedor');
            }

            $this->redirect('index.php?controller=proveedoresController&action=listar');
        }

        $dato = $this->proveedorModel->consultarPorId($proveedor_id);

        if (!$dato) {
            $this->setFlash('error', 'Proveedor no encontrado');
            $this->redirect('index.php?controller=proveedoresController&action=listar');
        }

        $this->render('proveedores/proveedores-editar.php', compact('dato'));
    }

    private function sanitizePost(array $rules): array
    {
        $data = [];

        foreach ($rules as $field => $minLength) {
            $value = trim($_POST[$field] ?? '');

            if (strlen($value) < $minLength) {
                $this->setFlash('error', ucfirst($field) . ' debe tener mínimo ' . $minLength . ' caracteres');
                $this->redirect('index.php?controller=proveedoresController&action=listar');
            }

            $data[$field] = $value;
        }

        return $data;
    }

    public function borrar()
    {
        $proveedor_id = $this->validateId($_GET['id'] ?? '');
        $success = $this->proveedorModel->borrar($proveedor_id);

        $this->setFlash($success ? 'success' : 'error', $success ? 'Proveedor eliminado con éxito' : 'No se pudo eliminar el proveedor');
        $this->redirect('index.php?controller=proveedoresController&action=listar');
    }

    public function status()
    {
        $proveedor_id = $this->validateId($_GET['id'] ?? '');
        $status = trim($_GET['status'] ?? '');

        if ($status === '') {
            $this->setFlash('error', 'Estado no proporcionado');
            $this->redirect('index.php?controller=proveedoresController&action=listar');
        }

        $success = $this->proveedorModel->changeStatus($proveedor_id, $status);
        $this->setFlash($success ? 'success' : 'error', $success ? 'Status actualizado' : 'No se pudo cambiar el status');
        $this->redirect('index.php?controller=proveedoresController&action=listar');
    }

    private function validateId($id): int
    {
        if (!is_numeric($id) || intval($id) <= 0) {
            $this->setFlash('error', 'ID no válido');
            $this->redirect('index.php?controller=proveedoresController&action=listar');
        }

        $id = intval($id);

        if (!$this->proveedorModel->existsId($id)) {
            $this->setFlash('error', 'ID no encontrado');
            $this->redirect('index.php?controller=proveedoresController&action=listar');
        }

        return $id;
    }
}