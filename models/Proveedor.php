<?php

class Proveedor extends BaseModel
{
    public function crear(string $proveedor_nombre, string $proveedor_telefono): bool
    {
        return $this->execute(
            "INSERT INTO proveedores (proveedor_nombre, proveedor_telefono) VALUES (?, ?)",
            [$proveedor_nombre, $proveedor_telefono]
        );
    }

    /**
     * Catálogo completo de proveedores sin paginación para selects de surtidos/formularios.
     */
    public function listar(): array
    {
        return $this->fetchAll("SELECT * FROM proveedores ORDER BY proveedor_nombre ASC");
    }

    /**
     * Listado paginado de proveedores con búsqueda por nombre y teléfono.
     */
    public function listarPaginado(int $page = 1, int $perPage = 10, string $search = ''): array
    {
        $params = [];
        $where = '';
        if ($search !== '') {
            $where = " WHERE proveedor_nombre LIKE ? OR proveedor_telefono LIKE ?";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $sql = "SELECT * FROM proveedores{$where} ORDER BY proveedor_id ASC";
        $countSql = "SELECT COUNT(*) FROM proveedores{$where}";

        return $this->paginate($sql, $countSql, $params, $page, $perPage);
    }



    public function editar(string $proveedor_nombre, string $proveedor_telefono, int $proveedor_id): bool
    {
        return $this->execute(
            "UPDATE proveedores SET proveedor_nombre = ?, proveedor_telefono = ? WHERE proveedor_id = ?",
            [$proveedor_nombre, $proveedor_telefono, $proveedor_id]
        );
    }

    public function consultarPorId(int $proveedor_id): ?array
    {
        return $this->fetchOne(
            "SELECT * FROM proveedores WHERE proveedor_id = ? LIMIT 1",
            [$proveedor_id]
        );
    }

    public function existsId(int $proveedor_id): bool
    {
        return $this->exists(
            "SELECT 1 FROM proveedores WHERE proveedor_id = ? LIMIT 1",
            [$proveedor_id]
        );
    }

    public function isDuplicateNombre(string $proveedor_nombre): bool
    {
        return $this->exists(
            "SELECT 1 FROM proveedores WHERE proveedor_nombre = ? LIMIT 1",
            [$proveedor_nombre]
        );
    }

    public function isDuplicateNombreExceptId(string $proveedor_nombre, int $proveedor_id): bool
    {
        return $this->isDuplicateField(
            'proveedores',
            'proveedor_nombre',
            $proveedor_nombre,
            $proveedor_id,
            'proveedor_id'
        );
    }

    public function borrar(int $proveedor_id): bool
    {
        return $this->deleteById('proveedores', 'proveedor_id', $proveedor_id);
    }

    public function changeStatus(int $proveedor_id, string $status): bool
    {
        return $this->updateStatusById('proveedores', 'status', $status, 'proveedor_id', $proveedor_id);
    }
}