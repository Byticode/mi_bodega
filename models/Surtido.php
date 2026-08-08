<?php 

class Surtido extends BaseModel
{
    public function crear($proveedor_id, $costo_total)
    {
        $sql = "INSERT INTO surtidos (proveedor_id, surtido_costo_total) VALUES (?, ?)";
        $resultado = $this->execute($sql, [$proveedor_id, $costo_total]);
        
        if ($resultado) {
            return $this->lastInsertId();
        }
        return false;
    }

    public function crearSurtidoCompleto($proveedor_id, $costo_total, array $productos, $productoModel)
    {
        try {
            $this->beginTransaction();

            $surtido_id = $this->crear($proveedor_id, $costo_total);
            if (!$surtido_id) {
                throw new Exception("Error al insertar surtido");
            }

            foreach ($productos as $item) {
                $this->agregarDetalle($surtido_id, $item['id'], $item['cantidad'], $item['precio_costo']);
                $productoModel->actualizarStock($item['id'], $item['cantidad']);
            }

            $this->commit();
            return $surtido_id;
        } catch (Throwable $e) {
            $this->rollBack();
            return false;
        }
    }

    public function agregarDetalle($surtido_id, $producto_id, $cantidad, $precio_costo)
    {
        $subtotal = $cantidad * $precio_costo;
        $sql = "INSERT INTO surtido_detalles (surtido_id, producto_id, detalle_cantidad, detalle_precio_costo, detalle_subtotal) 
                VALUES (?, ?, ?, ?, ?)";
        return $this->execute($sql, [$surtido_id, $producto_id, $cantidad, $precio_costo, $subtotal]);
    }

    public function listar()
    {
        $sql = "SELECT s.*, p.proveedor_nombre, 
                       (SELECT COUNT(*) FROM surtido_detalles WHERE surtido_id = s.surtido_id) as total_productos
                FROM surtidos s
                LEFT JOIN proveedores p ON s.proveedor_id = p.proveedor_id
                ORDER BY s.surtido_id DESC";
        return $this->fetchAll($sql);
    }

    public function listarPaginado(int $page = 1, int $perPage = 15): array
    {
        $sql = "SELECT s.*, p.proveedor_nombre, 
                       (SELECT COUNT(*) FROM surtido_detalles WHERE surtido_id = s.surtido_id) as total_productos
                FROM surtidos s
                LEFT JOIN proveedores p ON s.proveedor_id = p.proveedor_id
                ORDER BY s.surtido_id DESC";

        $countSql = "SELECT COUNT(*) FROM surtidos";

        return $this->paginate($sql, $countSql, [], $page, $perPage);
    }

    public function consultarPorId($surtido_id)
    {
        $sql = "SELECT s.*, p.proveedor_nombre
                FROM surtidos s
                LEFT JOIN proveedores p ON s.proveedor_id = p.proveedor_id
                WHERE s.surtido_id = ?";
        return $this->fetchOne($sql, [$surtido_id]);
    }

    public function obtenerDetalles($surtido_id)
    {
        $sql = "SELECT sd.*, p.producto_nombre, p.producto_codigo, u.unidad_abreviatura
                FROM surtido_detalles sd
                LEFT JOIN productos p ON sd.producto_id = p.producto_id
                LEFT JOIN unidades u ON p.unidad_id = u.unidad_id
                WHERE sd.surtido_id = ?";
        return $this->fetchAll($sql, [$surtido_id]);
    }

    public function limpiarVerificarId($surtido_id)
    {
        return $this->exists("SELECT 1 FROM surtidos WHERE surtido_id = ? LIMIT 1", [$surtido_id]);
    }
}