<?php 

require_once './config/database.php';

class ProductoBase{

    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    public function crear($producto_codigo, $producto_nombre, $categoria_id)
    {
        $sql = "INSERT INTO productos_base (producto_codigo, producto_nombre, categoria_id) VALUES (?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$producto_codigo, $producto_nombre, $categoria_id]);

        return $resultado;
    }

    public function listar(){
        $sql = "SELECT pb.*, c.categorias_nombre 
                FROM productos_base pb
                LEFT JOIN categorias c ON pb.categoria_id = c.categorias_id
                ORDER BY pb.producto_id ASC;";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }

    public function editar($producto_codigo, $producto_nombre, $categoria_id, $producto_id){
        
        $sql = "UPDATE productos_base SET producto_codigo = ?, producto_nombre = ?, categoria_id = ? WHERE producto_id = ?";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$producto_codigo, $producto_nombre, $categoria_id, $producto_id]);

        return $resultado;
    }

    public function consultarPorId($producto_id){
        $sql = "SELECT pb.*, c.categorias_nombre 
                FROM productos_base pb
                LEFT JOIN categorias c ON pb.categoria_id = c.categorias_id
                WHERE pb.producto_id = ?";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$producto_id]);

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }

    public function limpiarVerificarId($producto_id){
        //verificar que ese ID exista en la BD

        $statement = $this->db->prepare("SELECT * FROM productos_base WHERE producto_id = ? LIMIT 1");
        $resultado = $statement->execute([$producto_id]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = false;
        } else {
            $resultado = true;
        }

        return $resultado;
    }

    public function verificarDuplicado($producto_nombre){
        //verificar si este nombre ya está registrado
        $sql = "SELECT * FROM productos_base WHERE producto_nombre = ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$producto_nombre]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = true;
        } else {
            $resultado = false;
        }

        return $resultado;
    }

    public function verificarDuplicadoId($producto_nombre, $producto_id){
        //verificar si mi nombre lo tiene otro aparte de mi.
        $sql = "SELECT * FROM productos_base WHERE producto_nombre = ? AND producto_id != ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$producto_nombre, $producto_id]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = true;
        } else {
            $resultado = false;
        }
        
        return $resultado;
    }

    public function verificarDuplicadoCodigo($producto_codigo){
        // Si el código es NULL o vacío, no se verifica duplicado
        if (empty($producto_codigo)) {
            return true;
        }
        
        //verificar si este código ya está registrado
        $sql = "SELECT * FROM productos_base WHERE producto_codigo = ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$producto_codigo]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = true;
        } else {
            $resultado = false;
        }

        return $resultado;
    }

    public function verificarDuplicadoCodigoId($producto_codigo, $producto_id){
        // Si el código es NULL o vacío, no se verifica duplicado
        if (empty($producto_codigo)) {
            return true;
        }
        
        //verificar si mi código lo tiene otro aparte de mi.
        $sql = "SELECT * FROM productos_base WHERE producto_codigo = ? AND producto_id != ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$producto_codigo, $producto_id]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = true;
        } else {
            $resultado = false;
        }
        
        return $resultado;
    }

    public function obtenerCategorias(){
        $sql = "SELECT categorias_id, categorias_nombre FROM categorias ORDER BY categorias_nombre ASC";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }
}
?>