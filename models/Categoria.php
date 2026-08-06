<?php

class Categoria extends BaseModel
{
    public function crear(string $categorias_nombre): bool
    {
        return $this->execute(
            "INSERT INTO categorias (categorias_nombre) VALUES (?)",
            [$categorias_nombre]
        );
    }

    public function listar(): array
    {
        return $this->fetchAll("SELECT * FROM categorias ORDER BY categorias_id ASC");
    }

    public function editar(string $categorias_nombre, int $categorias_id): bool
    {
        return $this->execute(
            "UPDATE categorias SET categorias_nombre = ? WHERE categorias_id = ?",
            [$categorias_nombre, $categorias_id]
        );
    }

    public function consultarPorId(int $categorias_id): ?array
    {
        return $this->fetchOne(
            "SELECT * FROM categorias WHERE categorias_id = ? LIMIT 1",
            [$categorias_id]
        );
    }

    public function existsId(int $categorias_id): bool
    {
        return $this->exists(
            "SELECT 1 FROM categorias WHERE categorias_id = ? LIMIT 1",
            [$categorias_id]
        );
    }

    public function isDuplicateNombre(string $categorias_nombre): bool
    {
        return $this->exists(
            "SELECT 1 FROM categorias WHERE categorias_nombre = ? LIMIT 1",
            [$categorias_nombre]
        );
    }

    public function isDuplicateNombreExceptId(string $categorias_nombre, int $categorias_id): bool
    {
        return $this->isDuplicateField(
            'categorias',
            'categorias_nombre',
            $categorias_nombre,
            $categorias_id,
            'categorias_id'
        );
    }

    public function borrar(int $categorias_id): bool
    {
        return $this->deleteById('categorias', 'categorias_id', $categorias_id);
    }

    public function changeStatus(int $categorias_id, string $status): bool
    {
        return $this->updateStatusById('categorias', 'status', $status, 'categorias_id', $categorias_id);
    }
}