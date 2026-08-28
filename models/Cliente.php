<?php

class Cliente extends BaseModel
{
    public function crear(
        string $cliente_nombre,
        string $cliente_apellido,
        string $cliente_cedula,
        ?string $cliente_telefono,
        ?string $cliente_correo
    ): bool {
        return $this->execute(
            "INSERT INTO clientes (cliente_nombre, cliente_apellido, cliente_cedula, cliente_telefono, cliente_correo) VALUES (?, ?, ?, ?, ?)",
            [$cliente_nombre, $cliente_apellido, $cliente_cedula, $cliente_telefono, $cliente_correo]
        );
    }

    /**
     * Catálogo completo de clientes sin paginación para selects de ventas/formularios.
     */
    public function listar(): array
    {
        return $this->fetchAll("SELECT * FROM clientes ORDER BY cliente_nombre ASC, cliente_apellido ASC");
    }

    /**
     * Listado paginado de clientes con búsqueda por nombre, apellido, cédula, teléfono o correo.
     */
    public function listarPaginado(int $page = 1, int $perPage = 10, string $search = ''): array
    {
        $params = [];
        $where = '';
        if ($search !== '') {
            $where = " WHERE cliente_nombre LIKE ? OR cliente_apellido LIKE ? OR cliente_cedula LIKE ? OR cliente_telefono LIKE ? OR cliente_correo LIKE ?";
            $term = '%' . $search . '%';
            $params = [$term, $term, $term, $term, $term];
        }

        $sql = "SELECT * FROM clientes{$where} ORDER BY cliente_id ASC";
        $countSql = "SELECT COUNT(*) FROM clientes{$where}";

        return $this->paginate($sql, $countSql, $params, $page, $perPage);
    }



    public function editar(
        string $cliente_nombre,
        string $cliente_apellido,
        string $cliente_cedula,
        ?string $cliente_telefono,
        ?string $cliente_correo,
        int $cliente_id
    ): bool {
        return $this->execute(
            "UPDATE clientes SET cliente_nombre = ?, cliente_apellido = ?, cliente_cedula = ?, cliente_telefono = ?, cliente_correo = ? WHERE cliente_id = ?",
            [$cliente_nombre, $cliente_apellido, $cliente_cedula, $cliente_telefono, $cliente_correo, $cliente_id]
        );
    }

    public function consultarPorId(int $cliente_id): ?array
    {
        return $this->fetchOne(
            "SELECT * FROM clientes WHERE cliente_id = ? LIMIT 1",
            [$cliente_id]
        );
    }

    public function existsId(int $cliente_id): bool
    {
        return $this->exists(
            "SELECT 1 FROM clientes WHERE cliente_id = ? LIMIT 1",
            [$cliente_id]
        );
    }

    public function isDuplicateCedula(string $cliente_cedula): bool
    {
        return $this->exists(
            "SELECT 1 FROM clientes WHERE cliente_cedula = ? LIMIT 1",
            [$cliente_cedula]
        );
    }

    public function isDuplicateCedulaExceptId(string $cliente_cedula, int $cliente_id): bool
    {
        return $this->exists(
            "SELECT 1 FROM clientes WHERE cliente_cedula = ? AND cliente_id != ? LIMIT 1",
            [$cliente_cedula, $cliente_id]
        );
    }

    public function isDuplicateCorreo(?string $cliente_correo): bool
    {
        if (empty($cliente_correo)) {
            return true;
        }

        return !$this->exists(
            "SELECT 1 FROM clientes WHERE cliente_correo = ? LIMIT 1",
            [$cliente_correo]
        );
    }

    public function isDuplicateCorreoExceptId(?string $cliente_correo, int $cliente_id): bool
    {
        if (empty($cliente_correo)) {
            return true;
        }

        return !$this->isDuplicateField(
            'clientes',
            'cliente_correo',
            $cliente_correo,
            $cliente_id,
            'cliente_id'
        );
    }

    public function borrar(int $cliente_id): bool
    {
        return $this->deleteById('clientes', 'cliente_id', $cliente_id);
    }

    public function changeStatus(int $cliente_id, string $status): bool
    {
        return $this->updateStatusById('clientes', 'status', $status, 'cliente_id', $cliente_id);
    }
}