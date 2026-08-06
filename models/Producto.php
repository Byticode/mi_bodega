<?php

require_once './config/database.php';

class Producto
{

    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    public function crear($producto_codigo, $producto_nombre, $producto_peso, $categoria_id, $unidad_id, $producto_precio_venta, $producto_stock = 0)
    {
        $sql = "INSERT INTO productos (producto_codigo, producto_nombre, producto_peso, categoria_id, unidad_id, producto_precio_venta, producto_stock) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$producto_codigo, $producto_nombre, $producto_peso, $categoria_id, $unidad_id, $producto_precio_venta, $producto_stock]);

        return $resultado;
    }

    public function listar()
    {
        $sql = "SELECT p.*, c.categorias_nombre, u.unidad_nombre, u.unidad_abreviatura 
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.categorias_id
                LEFT JOIN unidades u ON p.unidad_id = u.unidad_id
                ORDER BY p.producto_nombre ASC;";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
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

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$producto_codigo, $producto_nombre, $producto_peso, $categoria_id, $unidad_id, $producto_precio_venta, $producto_stock, $producto_id]);

        return $resultado;
    }

    public function consultarPorId($producto_id)
    {
        $sql = "SELECT p.*, c.categorias_nombre, u.unidad_nombre, u.unidad_abreviatura 
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.categorias_id
                LEFT JOIN unidades u ON p.unidad_id = u.unidad_id
                WHERE p.producto_id = ?";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$producto_id]);

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }

    public function limpiarVerificarId($producto_id)
    {
        $statement = $this->db->prepare("SELECT * FROM productos WHERE producto_id = ? LIMIT 1");
        $resultado = $statement->execute([$producto_id]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)) {
            $resultado = false;
        } else {
            $resultado = true;
        }

        return $resultado;
    }

    public function verificarDuplicado($producto_nombre)
    {
        $sql = "SELECT * FROM productos WHERE producto_nombre = ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$producto_nombre]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)) {
            $resultado = true;
        } else {
            $resultado = false;
        }

        return $resultado;
    }

    public function verificarDuplicadoId($producto_nombre, $producto_id)
    {
        $sql = "SELECT * FROM productos WHERE producto_nombre = ? AND producto_id != ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$producto_nombre, $producto_id]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)) {
            $resultado = true;
        } else {
            $resultado = false;
        }

        return $resultado;
    }

    public function verificarDuplicadoCodigo($producto_codigo)
    {
        if (empty($producto_codigo)) {
            return true;
        }

        $sql = "SELECT * FROM productos WHERE producto_codigo = ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$producto_codigo]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)) {
            $resultado = true;
        } else {
            $resultado = false;
        }

        return $resultado;
    }

    public function verificarDuplicadoCodigoId($producto_codigo, $producto_id)
    {
        if (empty($producto_codigo)) {
            return true;
        }

        $sql = "SELECT * FROM productos WHERE producto_codigo = ? AND producto_id != ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$producto_codigo, $producto_id]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)) {
            $resultado = true;
        } else {
            $resultado = false;
        }

        return $resultado;
    }

    public function obtenerCategorias()
    {
        $sql = "SELECT categorias_id, categorias_nombre FROM categorias ORDER BY categorias_nombre ASC";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }

    public function obtenerUnidades()
    {
        $sql = "SELECT unidad_id, unidad_nombre, unidad_abreviatura FROM unidades ORDER BY unidad_nombre ASC";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }

    public function actualizarStock($producto_id, $cantidad)
    {
        $sql = "UPDATE productos SET producto_stock = producto_stock + ? WHERE producto_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$cantidad, $producto_id]);
    }

    public function descontarStock($producto_id, $cantidad)
    {
        $sql = "UPDATE productos SET producto_stock = producto_stock - ? WHERE producto_id = ? AND producto_stock >= ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$cantidad, $producto_id, $cantidad]);
    }

    public function obtenerProductosConStock()
    {
        $sql = "SELECT p.*, c.categorias_nombre, u.unidad_nombre, u.unidad_abreviatura 
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.categorias_id
                LEFT JOIN unidades u ON p.unidad_id = u.unidad_id
                WHERE p.producto_stock > 0
                ORDER BY p.producto_nombre ASC;";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodosProductos()
    {
        $sql = "SELECT p.*, c.categorias_nombre, u.unidad_nombre, u.unidad_abreviatura 
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.categorias_id
                LEFT JOIN unidades u ON p.unidad_id = u.unidad_id
                ORDER BY p.producto_nombre ASC;";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
