<?php 

require_once './config/database.php';

class Surtido{

    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    public function crear($proveedor_id, $costo_total)
    {
        $sql = "INSERT INTO surtidos (proveedor_id, surtido_costo_total) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $resultado = $stmt->execute([$proveedor_id, $costo_total]);
        
        if ($resultado) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function agregarDetalle($surtido_id, $producto_id, $cantidad, $precio_costo)
    {
        $subtotal = $cantidad * $precio_costo;
        $sql = "INSERT INTO surtido_detalles (surtido_id, producto_id, detalle_cantidad, detalle_precio_costo, detalle_subtotal) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$surtido_id, $producto_id, $cantidad, $precio_costo, $subtotal]);
    }

    public function listar(){
        $sql = "SELECT s.*, p.proveedor_nombre, 
                       (SELECT COUNT(*) FROM surtido_detalles WHERE surtido_id = s.surtido_id) as total_productos
                FROM surtidos s
                LEFT JOIN proveedores p ON s.proveedor_id = p.proveedor_id
                ORDER BY s.surtido_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function consultarPorId($surtido_id){
        $sql = "SELECT s.*, p.proveedor_nombre
                FROM surtidos s
                LEFT JOIN proveedores p ON s.proveedor_id = p.proveedor_id
                WHERE s.surtido_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$surtido_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalles($surtido_id){
        $sql = "SELECT sd.*, p.producto_nombre, p.producto_codigo, u.unidad_abreviatura
                FROM surtido_detalles sd
                LEFT JOIN productos p ON sd.producto_id = p.producto_id
                LEFT JOIN unidades u ON p.unidad_id = u.unidad_id
                WHERE sd.surtido_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$surtido_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function limpiarVerificarId($surtido_id){
        $statement = $this->db->prepare("SELECT * FROM surtidos WHERE surtido_id = ? LIMIT 1");
        $resultado = $statement->execute([$surtido_id]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            return false;
        } else {
            return true;
        }
    }
}
?>