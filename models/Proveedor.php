<?php 

require_once './config/database.php';

class Proveedor{

    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    public function crear($proveedor_nombre, $proveedor_telefono)
    {
        $sql = "INSERT INTO proveedores (proveedor_nombre, proveedor_telefono) VALUES (?, ?)";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$proveedor_nombre, $proveedor_telefono]);

        return $resultado;
    }

    public function listar(){
        $sql = "SELECT * FROM proveedores ORDER BY proveedor_id ASC;";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }

    public function editar($proveedor_nombre, $proveedor_telefono, $proveedor_id){
        
        $sql = "UPDATE proveedores SET proveedor_nombre = ?, proveedor_telefono = ? WHERE proveedor_id = ?";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$proveedor_nombre, $proveedor_telefono, $proveedor_id]);

        return $resultado;
    }

    public function consultarPorId($proveedor_id){
        $sql = "SELECT * FROM proveedores WHERE proveedor_id = ?";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$proveedor_id]);

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }

    public function limpiarVerificarId($proveedor_id){
        //verificar que ese ID exista en la BD

        $statement = $this->db->prepare("SELECT * FROM proveedores WHERE proveedor_id = ? LIMIT 1");
        $resultado = $statement->execute([$proveedor_id]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = false;
        } else {
            $resultado = true;
        }

        return $resultado;
    }

    public function verificarDuplicado($proveedor_nombre){
        //verificar si este nombre ya esta registrado
        $sql = "SELECT * FROM proveedores WHERE proveedor_nombre = ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$proveedor_nombre]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = true;
        } else {
            $resultado = false;
        }

        return $resultado;
    }

    public function verificarDuplicadoId($proveedor_nombre, $proveedor_id){
        //verificar si mi nombre lo tiene otro aparte de mi.
        $sql = "SELECT * FROM proveedores WHERE proveedor_nombre = ? AND proveedor_id != ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$proveedor_nombre, $proveedor_id]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = true;
        } else {
            $resultado = false;
        }
        
        return $resultado;
    }
}
?>