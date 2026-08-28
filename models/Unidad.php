<?php 

class Unidad extends BaseModel
{
    public function crear($unidad_nombre, $unidad_abreviatura)
    {
        $sql = "INSERT INTO unidades (unidad_nombre, unidad_abreviatura) VALUES (?, ?)";
        return $this->execute($sql, [$unidad_nombre, $unidad_abreviatura]);
    }

    /**
     * Catálogo completo de unidades sin paginación para selects de productos/formularios.
     */
    public function listar(): array
    {
        $sql = "SELECT * FROM unidades ORDER BY unidad_nombre ASC";
        return $this->fetchAll($sql);
    }

    /**
     * Listado paginado de unidades con búsqueda por nombre y abreviatura.
     */
    public function listarPaginado(int $page = 1, int $perPage = 10, string $search = ''): array
    {
        $params = [];
        $where = '';
        if ($search !== '') {
            $where = " WHERE unidad_nombre LIKE ? OR unidad_abreviatura LIKE ?";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $sql = "SELECT * FROM unidades{$where} ORDER BY unidad_id ASC";
        $countSql = "SELECT COUNT(*) FROM unidades{$where}";

        return $this->paginate($sql, $countSql, $params, $page, $perPage);
    }


    public function editar($unidad_nombre, $unidad_abreviatura, $unidad_id)
    {
        $sql = "UPDATE unidades SET unidad_nombre = ?, unidad_abreviatura = ? WHERE unidad_id = ?";
        return $this->execute($sql, [$unidad_nombre, $unidad_abreviatura, $unidad_id]);
    }

    public function consultarPorId($unidad_id)
    {
        $sql = "SELECT * FROM unidades WHERE unidad_id = ?";
        return $this->fetchAll($sql, [$unidad_id]);
    }

    public function limpiarVerificarId($unidad_id)
    {
        return $this->exists("SELECT 1 FROM unidades WHERE unidad_id = ? LIMIT 1", [$unidad_id]);
    }

    public function verificarDuplicado($unidad_nombre)
    {
        return !$this->exists("SELECT 1 FROM unidades WHERE unidad_nombre = ? LIMIT 1", [$unidad_nombre]);
    }

    public function verificarDuplicadoId($unidad_nombre, $unidad_id)
    {
        return !$this->exists("SELECT 1 FROM unidades WHERE unidad_nombre = ? AND unidad_id != ? LIMIT 1", [$unidad_nombre, $unidad_id]);
    }

    public function verificarDuplicadoAbreviatura($unidad_abreviatura)
    {
        return !$this->exists("SELECT 1 FROM unidades WHERE unidad_abreviatura = ? LIMIT 1", [$unidad_abreviatura]);
    }

    public function verificarDuplicadoAbreviaturaId($unidad_abreviatura, $unidad_id)
    {
        return !$this->exists("SELECT 1 FROM unidades WHERE unidad_abreviatura = ? AND unidad_id != ? LIMIT 1", [$unidad_abreviatura, $unidad_id]);
    }
}