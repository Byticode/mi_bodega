<?php 

class Venta extends BaseModel
{
    public function crear($cliente_id, $usuario_id, $tasa_id, $total, $numero_pago = null, $metodo_pago = null, $estado = 'pendiente')
    {
        $sql = "INSERT INTO ventas (cliente_id, usuario_id, tasa_id, venta_total, venta_numero_pago, venta_metodo_pago, venta_estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $resultado = $this->execute($sql, [$cliente_id, $usuario_id, $tasa_id, $total, $numero_pago, $metodo_pago, $estado]);
        
        if ($resultado) {
            return $this->lastInsertId();
        }
        return false;
    }

    public function crearVentaCompleta($cliente_id, $usuario_id, $tasa_id, $total, $numero_pago, $metodo_pago, $estado, array $productos, $productoModel)
    {
        try {
            $this->beginTransaction();

            $venta_id = $this->crear($cliente_id, $usuario_id, $tasa_id, $total, $numero_pago, $metodo_pago, $estado);

            if (!$venta_id) {
                throw new Exception("No se pudo crear el registro de la venta");
            }

            foreach ($productos as $item) {
                $this->agregarDetalle($venta_id, $item['id'], $item['cantidad'], $item['precio']);
                if ($estado === 'completada') {
                    $productoModel->descontarStock($item['id'], $item['cantidad']);
                }
            }

            $this->commit();
            return $venta_id;
        } catch (Throwable $e) {
            $this->rollBack();
            return false;
        }
    }

    public function agregarDetalle($venta_id, $producto_id, $cantidad, $precio_unitario)
    {
        $subtotal = $cantidad * $precio_unitario;
        $sql = "INSERT INTO venta_detalles (venta_id, producto_id, detalle_cantidad, detalle_precio_unitario, detalle_subtotal) 
                VALUES (?, ?, ?, ?, ?)";
        return $this->execute($sql, [$venta_id, $producto_id, $cantidad, $precio_unitario, $subtotal]);
    }

    public function listar()
    {
        $sql = "SELECT v.*, c.cliente_nombre, c.cliente_apellido, u.usuario_nombre, tm.moneda, tm.tasa_usd,
                       (SELECT COUNT(*) FROM venta_detalles WHERE venta_id = v.venta_id) as total_productos
                FROM ventas v
                LEFT JOIN clientes c ON v.cliente_id = c.cliente_id
                LEFT JOIN usuarios u ON v.usuario_id = u.usuario_id
                LEFT JOIN tasa_moneda tm ON v.tasa_id = tm.tasa_id
                ORDER BY v.venta_id DESC";
        return $this->fetchAll($sql);
    }

    public function listarPaginado(int $page = 1, int $perPage = 15): array
    {
        $sql = "SELECT v.*, c.cliente_nombre, c.cliente_apellido, u.usuario_nombre, tm.moneda, tm.tasa_usd,
                       (SELECT COUNT(*) FROM venta_detalles WHERE venta_id = v.venta_id) as total_productos
                FROM ventas v
                LEFT JOIN clientes c ON v.cliente_id = c.cliente_id
                LEFT JOIN usuarios u ON v.usuario_id = u.usuario_id
                LEFT JOIN tasa_moneda tm ON v.tasa_id = tm.tasa_id
                ORDER BY v.venta_id DESC";

        $countSql = "SELECT COUNT(*) FROM ventas";

        return $this->paginate($sql, $countSql, [], $page, $perPage);
    }

    public function consultarPorId($venta_id)
    {
        $sql = "SELECT v.*, c.cliente_nombre, c.cliente_apellido, u.usuario_nombre, tm.moneda, tm.tasa_usd
                FROM ventas v
                LEFT JOIN clientes c ON v.cliente_id = c.cliente_id
                LEFT JOIN usuarios u ON v.usuario_id = u.usuario_id
                LEFT JOIN tasa_moneda tm ON v.tasa_id = tm.tasa_id
                WHERE v.venta_id = ?";
        return $this->fetchOne($sql, [$venta_id]);
    }

    public function obtenerDetalles($venta_id)
    {
        $sql = "SELECT vd.*, p.producto_nombre, p.producto_codigo, u.unidad_abreviatura
                FROM venta_detalles vd
                LEFT JOIN productos p ON vd.producto_id = p.producto_id
                LEFT JOIN unidades u ON p.unidad_id = u.unidad_id
                WHERE vd.venta_id = ?";
        return $this->fetchAll($sql, [$venta_id]);
    }

    public function actualizarEstado($venta_id, $estado)
    {
        $sql = "UPDATE ventas SET venta_estado = ? WHERE venta_id = ?";
        return $this->execute($sql, [$estado, $venta_id]);
    }

    public function completarVenta($venta_id, $metodo_pago, $numero_pago = null)
    {
        $sql = "UPDATE ventas SET venta_estado = 'completada', venta_metodo_pago = ?, venta_numero_pago = ? WHERE venta_id = ?";
        return $this->execute($sql, [$metodo_pago, $numero_pago, $venta_id]);
    }

    public function obtenerReporteVentas(string $fecha_desde, string $fecha_hasta): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_ventas,
                    COALESCE(SUM(CASE WHEN venta_estado = 'completada' THEN venta_total ELSE 0 END), 0) as total_completadas,
                    COALESCE(SUM(CASE WHEN venta_estado = 'pendiente' THEN venta_total ELSE 0 END), 0) as total_pendientes,
                    COALESCE(SUM(CASE WHEN venta_estado = 'cancelada' THEN venta_total ELSE 0 END), 0) as total_canceladas
                FROM ventas
                WHERE DATE(venta_fecha) BETWEEN ? AND ?";
        return $this->fetchOne($sql, [$fecha_desde, $fecha_hasta]);
    }

    public function obtenerTopProductos(string $fecha_desde, string $fecha_hasta, int $limite = 10): array
    {
        $sql = "SELECT 
                    p.producto_nombre,
                    p.producto_codigo,
                    SUM(vd.detalle_cantidad) as cantidad_vendida,
                    SUM(vd.detalle_subtotal) as total_vendido
                FROM venta_detalles vd
                INNER JOIN ventas v ON vd.venta_id = v.venta_id
                INNER JOIN productos p ON vd.producto_id = p.producto_id
                WHERE DATE(v.venta_fecha) BETWEEN ? AND ?
                    AND v.venta_estado = 'completada'
                GROUP BY p.producto_id, p.producto_nombre, p.producto_codigo
                ORDER BY cantidad_vendida DESC
                LIMIT ?";
        return $this->fetchAll($sql, [$fecha_desde, $fecha_hasta, $limite]);
    }

    public function obtenerVentasPorMetodoPago(string $fecha_desde, string $fecha_hasta): array
    {
        $sql = "SELECT 
                    COALESCE(venta_metodo_pago, 'Sin especificar') as metodo_pago,
                    COUNT(*) as cantidad_ventas,
                    SUM(venta_total) as total_ventas
                FROM ventas
                WHERE DATE(venta_fecha) BETWEEN ? AND ?
                    AND venta_estado = 'completada'
                GROUP BY venta_metodo_pago
                ORDER BY total_ventas DESC";
        return $this->fetchAll($sql, [$fecha_desde, $fecha_hasta]);
    }
}