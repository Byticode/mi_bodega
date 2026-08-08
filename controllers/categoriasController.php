<?php

class CategoriasController extends BaseController
{
    private $categoriaModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->categoriaModel = new Categoria();
    }

    public function listar()
    {
        $categorias = $this->categoriaModel->listar();
        $this->render('categorias/categorias.php', compact('categorias'));
    }

    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->listar();
        }

        $categorias_nombre = $this->sanitizeNombre($_POST['nombre'] ?? '');

        $this->validateUnique(
            $this->categoriaModel->isDuplicateNombre($categorias_nombre),
            'Esta categoría ya existe',
            'categorias'
        );

        $success = $this->categoriaModel->crear($categorias_nombre);

        if ($success) {
            $this->setFlash('success', 'Categoría creada con éxito');
        } else {
            $this->setFlash('error', 'No se pudo crear la categoría');
        }

        $this->redirect('categorias');
    }

    public function editar()
    {
        $categorias_id = $this->validateId($_GET['id'] ?? '');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categorias_nombre = $this->sanitizeNombre($_POST['nombre'] ?? '');

            $this->validateUnique(
                $this->categoriaModel->isDuplicateNombreExceptId($categorias_nombre, $categorias_id),
                'Esta categoría ya existe',
                'categorias'
            );

            $success = $this->categoriaModel->editar($categorias_nombre, $categorias_id);

            if ($success) {
                $this->setFlash('success', 'Categoría editada con éxito');
            } else {
                $this->setFlash('error', 'No se pudo editar la categoría');
            }

            $this->redirect('categorias');
        }

        $dato = $this->categoriaModel->consultarPorId($categorias_id);

        if (!$dato) {
            $this->setFlash('error', 'Categoría no encontrada');
            $this->redirect('categorias');
        }

        $this->render('categorias/categorias-editar.php', compact('dato'));
    }

    private function sanitizeNombre(string $nombre): string
    {
        $nombre = trim($nombre);

        if (strlen($nombre) < 3) {
            $this->setFlash('error', 'El nombre debe tener mínimo 3 caracteres');
            $this->redirect('categorias');
        }

        return ucfirst($nombre);
    }

    public function borrar()
    {
        $categorias_id = $this->validateId($_GET['id'] ?? '');
        $success = $this->categoriaModel->borrar($categorias_id);

        $this->setFlash($success ? 'success' : 'error', $success ? 'Categoría eliminada con éxito' : 'No se pudo eliminar la categoría');
        $this->redirect('categorias');
    }

    public function status()
    {
        $categorias_id = $this->validateId($_GET['id'] ?? '');
        $status = trim($_GET['status'] ?? '');

        if ($status === '') {
            $this->setFlash('error', 'Estado no proporcionado');
            $this->redirect('categorias');
        }

        $success = $this->categoriaModel->changeStatus($categorias_id, $status);
        $this->setFlash($success ? 'success' : 'error', $success ? 'Status actualizado' : 'No se pudo cambiar el status');
        $this->redirect('categorias');
    }

    private function validateId($id): int
    {
        if (!is_numeric($id) || intval($id) <= 0) {
            $this->setFlash('error', 'ID no válido');
            $this->redirect('categorias');
        }

        $id = intval($id);

        if (!$this->categoriaModel->existsId($id)) {
            $this->setFlash('error', 'ID no encontrado');
            $this->redirect('categorias');
        }

        return $id;
    }
}