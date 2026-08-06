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

    public function listar(): array
    {
        return $this->fetchAll("SELECT * FROM proveedores ORDER BY proveedor_id ASC");
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