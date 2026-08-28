<?php 

class TasaMoneda extends BaseModel
{
    public function crear($moneda, $tasa_usd, $tasa_euro, $tasa_paralelo)
    {
        $sql = "INSERT INTO tasa_moneda (moneda, tasa_usd, tasa_euro, tasa_paralelo) VALUES (?, ?, ?, ?)";
        return $this->execute($sql, [$moneda, $tasa_usd, $tasa_euro, $tasa_paralelo]);
    }

    /**
     * Catálogo completo de tasas de cambio sin paginación (opcionalmente con límite).
     */
    public function listar(int $limite = 0): array
    {
        $sql = "SELECT * FROM tasa_moneda ORDER BY tasa_id DESC";
        if ($limite > 0) {
            $sql .= " LIMIT " . (int)$limite;
        }
        return $this->fetchAll($sql);
    }

    /**
     * Historial de tasas paginado con búsqueda por moneda, valor o fecha.
     */
    public function listarPaginado(int $page = 1, int $perPage = 10, string $search = ''): array
    {
        $params = [];
        $where = '';
        if ($search !== '') {
            $where = " WHERE moneda LIKE ? OR tasa_usd LIKE ? OR tasa_euro LIKE ? OR tasa_paralelo LIKE ? OR created_at LIKE ?";
            $term = '%' . $search . '%';
            $params = [$term, $term, $term, $term, $term];
        }

        $sql = "SELECT * FROM tasa_moneda{$where} ORDER BY tasa_id DESC";
        $countSql = "SELECT COUNT(*) FROM tasa_moneda{$where}";

        return $this->paginate($sql, $countSql, $params, $page, $perPage);
    }

    public function obtenerUltima()
    {
        $sql = "SELECT * FROM tasa_moneda ORDER BY tasa_id DESC LIMIT 1";
        return $this->fetchOne($sql);
    }

    public function consultarPorId($tasa_id)
    {
        $sql = "SELECT * FROM tasa_moneda WHERE tasa_id = ?";
        return $this->fetchOne($sql, [$tasa_id]);
    }
}