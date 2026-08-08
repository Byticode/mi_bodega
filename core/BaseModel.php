<?php

require_once RUTA_APP . '/config/database.php';

class BaseModel
{
    protected $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    public function beginTransaction(): void
    {
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
        }
    }

    public function commit(): void
    {
        if ($this->db->inTransaction()) {
            $this->db->commit();
        }
    }

    public function rollBack(): void
    {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
    }

    protected function lastInsertId(): string
    {
        return $this->db->lastInsertId();
    }

    protected function query(string $sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
        return $result === false ? null : $result;
    }

    protected function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    protected function paginate(string $sql, string $countSql, array $params = [], int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $stmtCount = $this->db->prepare($countSql);
        $stmtCount->execute($params);
        $totalRows = (int) $stmtCount->fetchColumn();

        $sqlPaginada = $sql . " LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->db->prepare($sqlPaginada);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalPages = $totalRows > 0 ? (int) ceil($totalRows / $perPage) : 1;

        return [
            'data' => $data,
            'total' => $totalRows,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
        ];
    }

    public function deleteById(string $table, string $idColumn, int $id): bool
    {
        return $this->execute("DELETE FROM {$table} WHERE {$idColumn} = ?", [$id]);
    }

    public function updateStatusById(string $table, string $statusColumn, string $status, string $idColumn, int $id): bool
    {
        return $this->execute("UPDATE {$table} SET {$statusColumn} = ? WHERE {$idColumn} = ?", [$status, $id]);
    }


    protected function isDuplicateField(string $table, string $field, string $value, ?int $exceptId = null, string $idColumn = 'id'): bool
    {
        $sql = "SELECT 1 FROM {$table} WHERE {$field} = ?";
        $params = [$value];

        if ($exceptId !== null) {
            $sql .= " AND {$idColumn} != ?";
            $params[] = $exceptId;
        }

        $sql .= " LIMIT 1";
        return $this->exists($sql, $params);
    }

    public function exists(string $sql, array $params = []): bool
    {
        return (bool) $this->fetchOne($sql, $params);
    }
}

