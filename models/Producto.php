<?php

class Producto extends BaseModel
{
    public function crear($producto_codigo, $producto_nombre, $producto_peso, $categoria_id, $unidad_id, $producto_precio_venta, $producto_stock = 0)
    {
        $sql = "INSERT INTO productos (producto_codigo, producto_nombre, producto_peso, categoria_id, unidad_id, producto_precio_venta, producto_stock) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        return $this->execute($sql, [$producto_codigo, $producto_nombre, $producto_peso, $categoria_id, $unidad_id, $producto_precio_venta, $producto_stock]);
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

    public function editar($producto_codigo, $producto_nombre, $producto_peso, $categoria_id, $unidad_id, $producto_precio_venta, $producto_stock, $producto_id)
    {
        $sql = "UPDATE productos SET 
                producto_codigo = ?, 
                producto_nombre = ?, 
                producto_peso = ?, 
                categoria_id = ?, 
                unidad_id = ?, 
                producto_precio_venta = ?, 
                producto_stock = ? 
                WHERE producto_id = ?";
        return $this->execute($sql, [$producto_codigo, $producto_nombre, $producto_peso, $categoria_id, $unidad_id, $producto_precio_venta, $producto_stock, $producto_id]);
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
        return $this->obtenerProductosConStock();
    }
}

