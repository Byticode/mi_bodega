<?php 

require_once './config/database.php';

class TasaMoneda{

    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    public function crear($moneda, $tasa_usd, $tasa_euro, $tasa_paralelo)
    {
        $sql = "INSERT INTO tasa_moneda (moneda, tasa_usd, tasa_euro, tasa_paralelo) VALUES (?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$moneda, $tasa_usd, $tasa_euro, $tasa_paralelo]);

        return $resultado;
    }

    public function listar(){
        $sql = "SELECT * FROM tasa_moneda ORDER BY tasa_id DESC LIMIT 10;";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }

    public function obtenerUltima(){
        $sql = "SELECT * FROM tasa_moneda ORDER BY tasa_id DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function consultarPorId($tasa_id){
        $sql = "SELECT * FROM tasa_moneda WHERE tasa_id = ?";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$tasa_id]);

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }
}
?>