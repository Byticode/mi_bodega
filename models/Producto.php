<?php

class Producto extends BaseModel
{
    public function crear($producto_codigo, $producto_nombre, $producto_peso, $categoria_id, $unidad_id, $producto_precio_costo, $producto_ganancia, $producto_iva, $producto_precio_venta, $producto_stock = 0)
    {
        $sql = "INSERT INTO productos (producto_codigo, producto_nombre, producto_peso, categoria_id, unidad_id, producto_precio_costo, producto_ganancia, producto_iva, producto_precio_venta, producto_stock) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->execute($sql, [$producto_codigo, $producto_nombre, $producto_peso, $categoria_id, $unidad_id, $producto_precio_costo, $producto_ganancia, $producto_iva, $producto_precio_venta, $producto_stock]);
    }

    public function listar()
    {
        $sql = "SELECT p.*, c.categorias_nombre, u.unidad_nombre, u.unidad_abreviatura 
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.categorias_id
                LEFT JOIN unidades u ON p.unidad_id = u.unidad_id
                ORDER BY p.producto_nombre ASC";
        return $this->fetchAll($sql);
    }

    public function listarPaginado(int $page = 1, int $perPage = 15): array
    {
        $sql = "SELECT p.*, c.categorias_nombre, u.unidad_nombre, u.unidad_abreviatura 
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.categorias_id
                LEFT JOIN unidades u ON p.unidad_id = u.unidad_id
                ORDER BY p.producto_nombre ASC";

        $countSql = "SELECT COUNT(*) FROM productos";

        return $this->paginate($sql, $countSql, [], $page, $perPage);
    }

    public function editar($producto_codigo, $producto_nombre, $producto_peso, $categoria_id, $unidad_id, $producto_precio_costo, $producto_ganancia, $producto_iva, $producto_precio_venta, $producto_stock, $producto_id)
    {
        $sql = "UPDATE productos SET 
                producto_codigo = ?, 
                producto_nombre = ?, 
                producto_peso = ?, 
                categoria_id = ?, 
                unidad_id = ?, 
                producto_precio_costo = ?, 
                producto_ganancia = ?, 
                producto_iva = ?, 
                producto_precio_venta = ?, 
                producto_stock = ? 
                WHERE producto_id = ?";
        return $this->execute($sql, [$producto_codigo, $producto_nombre, $producto_peso, $categoria_id, $unidad_id, $producto_precio_costo, $producto_ganancia, $producto_iva, $producto_precio_venta, $producto_stock, $producto_id]);
    }

    public function eliminar($producto_id)
    {
        $sql = "DELETE FROM productos WHERE producto_id = ?";
        return $this->execute($sql, [$producto_id]);
    }

    public function eliminarMasivo(array $ids)
    {
        $ids = array_map('intval', array_filter($ids, fn($id) => is_numeric($id) && $id > 0));
        if (empty($ids)) {
            return false;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "DELETE FROM productos WHERE producto_id IN ({$placeholders})";
        return $this->execute($sql, $ids);
    }

    public function actualizarPreciosMasivo(array $ids, string $tipo = 'aumentar', string $modo = 'porcentaje', float $valor = 0.00, string $campo = 'costo')
    {
        $ids = array_map('intval', array_filter($ids, fn($id) => is_numeric($id) && $id > 0));
        if (empty($ids) || $valor <= 0) {
            return false;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT producto_id, producto_precio_costo, producto_ganancia, producto_iva, producto_precio_venta FROM productos WHERE producto_id IN ({$placeholders})";
        $productos = $this->fetchAll($sql, $ids);

        if (empty($productos)) {
            return false;
        }

        $factor = ($tipo === 'disminuir') ? -1 : 1;

        foreach ($productos as $p) {
            $costo_actual = (float) $p['producto_precio_costo'];
            $ganancia = (float) $p['producto_ganancia'];
            $iva = (float) $p['producto_iva'];
            $precio_actual = (float) $p['producto_precio_venta'];

            if ($campo === 'costo') {
                if ($modo === 'porcentaje') {
                    $nuevo_costo = $costo_actual + ($costo_actual * ($valor / 100) * $factor);
                } else {
                    $nuevo_costo = $costo_actual + ($valor * $factor);
                }
                $nuevo_costo = max(0, round($nuevo_costo, 2));

                if ($nuevo_costo > 0) {
                    $nuevo_precio = $nuevo_costo * (1 + ($ganancia / 100)) * (1 + ($iva / 100));
                } else {
                    $nuevo_precio = $precio_actual;
                }
                $nuevo_precio = max(0.01, round($nuevo_precio, 2));
            } else {
                if ($modo === 'porcentaje') {
                    $nuevo_precio = $precio_actual + ($precio_actual * ($valor / 100) * $factor);
                } else {
                    $nuevo_precio = $precio_actual + ($valor * $factor);
                }
                $nuevo_precio = max(0.01, round($nuevo_precio, 2));
                $nuevo_costo = $costo_actual;
            }

            $updateSql = "UPDATE productos SET producto_precio_costo = ?, producto_precio_venta = ? WHERE producto_id = ?";
            $this->execute($updateSql, [$nuevo_costo, $nuevo_precio, $p['producto_id']]);
        }

        return true;
    }

    public function consultarPorId($producto_id)
    {
        $sql = "SELECT p.*, c.categorias_nombre, u.unidad_nombre, u.unidad_abreviatura 
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.categorias_id
                LEFT JOIN unidades u ON p.unidad_id = u.unidad_id
                WHERE p.producto_id = ?";
        return $this->fetchAll($sql, [$producto_id]);
    }

    public function limpiarVerificarId($producto_id)
    {
        return $this->exists("SELECT 1 FROM productos WHERE producto_id = ? LIMIT 1", [$producto_id]);
    }

    public function verificarDuplicado($producto_nombre)
    {
        return !$this->exists("SELECT 1 FROM productos WHERE producto_nombre = ? LIMIT 1", [$producto_nombre]);
    }

    public function verificarDuplicadoId($producto_nombre, $producto_id)
    {
        return !$this->exists("SELECT 1 FROM productos WHERE producto_nombre = ? AND producto_id != ? LIMIT 1", [$producto_nombre, $producto_id]);
    }

    public function verificarDuplicadoCodigo($producto_codigo)
    {
        if (empty($producto_codigo)) {
            return true;
        }
        return !$this->exists("SELECT 1 FROM productos WHERE producto_codigo = ? LIMIT 1", [$producto_codigo]);
    }

    public function verificarDuplicadoCodigoId($producto_codigo, $producto_id)
    {
        if (empty($producto_codigo)) {
            return true;
        }
        return !$this->exists("SELECT 1 FROM productos WHERE producto_codigo = ? AND producto_id != ? LIMIT 1", [$producto_codigo, $producto_id]);
    }

    public function obtenerCategorias()
    {
        return $this->fetchAll("SELECT categorias_id, categorias_nombre FROM categorias ORDER BY categorias_nombre ASC");
    }

    public function obtenerUnidades()
    {
        return $this->fetchAll("SELECT unidad_id, unidad_nombre, unidad_abreviatura FROM unidades ORDER BY unidad_nombre ASC");
    }

    public function actualizarStock($producto_id, $cantidad)
    {
        $sql = "UPDATE productos SET producto_stock = producto_stock + ? WHERE producto_id = ?";
        return $this->execute($sql, [$cantidad, $producto_id]);
    }

    public function descontarStock($producto_id, $cantidad)
    {
        $sql = "UPDATE productos SET producto_stock = producto_stock - ? WHERE producto_id = ? AND producto_stock >= ?";
        return $this->execute($sql, [$cantidad, $producto_id, $cantidad]);
    }

    public function obtenerProductosConStock()
    {
        $sql = "SELECT p.*, c.categorias_nombre, u.unidad_nombre, u.unidad_abreviatura 
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.categorias_id
                LEFT JOIN unidades u ON p.unidad_id = u.unidad_id
                WHERE p.producto_stock > 0
                ORDER BY p.producto_nombre ASC";
        return $this->fetchAll($sql);
    }

    public function obtenerTodosProductos()
    {
        return $this->listar();
    }

    public function obtenerProductosStockBajo(int $limite = 10): array
    {
        $sql = "SELECT p.*, c.categorias_nombre, u.unidad_nombre, u.unidad_abreviatura
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.categorias_id
                LEFT JOIN unidades u ON p.unidad_id = u.unidad_id
                WHERE p.producto_stock <= 5
                ORDER BY p.producto_stock ASC
                LIMIT ?";
        return $this->fetchAll($sql, [$limite]);
    }
}

