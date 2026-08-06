<?php 

require_once './config/database.php';

class Venta{

    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    public function crear($cliente_id, $usuario_id, $tasa_id, $total, $numero_pago = null, $metodo_pago = null, $estado = 'pendiente'){
        $sql = "INSERT INTO ventas (cliente_id, usuario_id, tasa_id, venta_total, venta_numero_pago, venta_metodo_pago, venta_estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $resultado = $stmt->execute([$cliente_id, $usuario_id, $tasa_id, $total, $numero_pago, $metodo_pago, $estado]);
        
        if ($resultado) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function agregarDetalle($venta_id, $producto_id, $cantidad, $precio_unitario){
        $subtotal = $cantidad * $precio_unitario;
        $sql = "INSERT INTO venta_detalles (venta_id, producto_id, detalle_cantidad, detalle_precio_unitario, detalle_subtotal) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$venta_id, $producto_id, $cantidad, $precio_unitario, $subtotal]);
    }

    public function listar(){
        $sql = "SELECT v.*, c.cliente_nombre, c.cliente_apellido, u.usuario_nombre, tm.moneda, tm.tasa_usd,
                       (SELECT COUNT(*) FROM venta_detalles WHERE venta_id = v.venta_id) as total_productos
                FROM ventas v
                LEFT JOIN clientes c ON v.cliente_id = c.cliente_id
                LEFT JOIN usuarios u ON v.usuario_id = u.usuario_id
                LEFT JOIN tasa_moneda tm ON v.tasa_id = tm.tasa_id
                ORDER BY v.venta_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function consultarPorId($venta_id){
        $sql = "SELECT v.*, c.cliente_nombre, c.cliente_apellido, u.usuario_nombre, tm.moneda, tm.tasa_usd
                FROM ventas v
                LEFT JOIN clientes c ON v.cliente_id = c.cliente_id
                LEFT JOIN usuarios u ON v.usuario_id = u.usuario_id
                LEFT JOIN tasa_moneda tm ON v.tasa_id = tm.tasa_id
                WHERE v.venta_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$venta_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalles($venta_id){
        $sql = "SELECT vd.*, p.producto_nombre, p.producto_codigo, u.unidad_abreviatura
                FROM venta_detalles vd
                LEFT JOIN productos p ON vd.producto_id = p.producto_id
                LEFT JOIN unidades u ON p.unidad_id = u.unidad_id
                WHERE vd.venta_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$venta_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarEstado($venta_id, $estado){
        $sql = "UPDATE ventas SET venta_estado = ? WHERE venta_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$estado, $venta_id]);
    }

    public function completarVenta($venta_id, $metodo_pago, $numero_pago = null){
        $sql = "UPDATE ventas SET venta_estado = 'completada', venta_metodo_pago = ?, venta_numero_pago = ? WHERE venta_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$metodo_pago, $numero_pago, $venta_id]);
    }
}
?>