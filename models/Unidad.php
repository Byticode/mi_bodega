<?php 

require_once './config/database.php';

class Unidad{

    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    public function crear($unidad_nombre, $unidad_abreviatura)
    {
        $sql = "INSERT INTO unidades (unidad_nombre, unidad_abreviatura) VALUES (?, ?)";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$unidad_nombre, $unidad_abreviatura]);

        return $resultado;
    }

    public function listar(){
        $sql = "SELECT * FROM unidades ORDER BY unidad_nombre ASC;";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }

    public function editar($unidad_nombre, $unidad_abreviatura, $unidad_id){
        
        $sql = "UPDATE unidades SET unidad_nombre = ?, unidad_abreviatura = ? WHERE unidad_id = ?";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$unidad_nombre, $unidad_abreviatura, $unidad_id]);

        return $resultado;
    }

    public function consultarPorId($unidad_id){
        $sql = "SELECT * FROM unidades WHERE unidad_id = ?";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$unidad_id]);

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }

    public function limpiarVerificarId($unidad_id){
        $statement = $this->db->prepare("SELECT * FROM unidades WHERE unidad_id = ? LIMIT 1");
        $resultado = $statement->execute([$unidad_id]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = false;
        } else {
            $resultado = true;
        }

        return $resultado;
    }

    public function verificarDuplicado($unidad_nombre){
        $sql = "SELECT * FROM unidades WHERE unidad_nombre = ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$unidad_nombre]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = true;
        } else {
            $resultado = false;
        }

        return $resultado;
    }

    public function verificarDuplicadoId($unidad_nombre, $unidad_id){
        $sql = "SELECT * FROM unidades WHERE unidad_nombre = ? AND unidad_id != ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$unidad_nombre, $unidad_id]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = true;
        } else {
            $resultado = false;
        }
        
        return $resultado;
    }

    public function verificarDuplicadoAbreviatura($unidad_abreviatura){
        $sql = "SELECT * FROM unidades WHERE unidad_abreviatura = ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$unidad_abreviatura]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = true;
        } else {
            $resultado = false;
        }

        return $resultado;
    }

    public function verificarDuplicadoAbreviaturaId($unidad_abreviatura, $unidad_id){
        $sql = "SELECT * FROM unidades WHERE unidad_abreviatura = ? AND unidad_id != ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$unidad_abreviatura, $unidad_id]);
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