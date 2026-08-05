<?php 

require_once './config/database.php';



class Categoria{

    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    public function crear($categorias_nombre)
    {
        $sql = "INSERT INTO categorias (categorias_nombre) VALUES (?)";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$categorias_nombre]);

        return $resultado;
    }

    public function listar(){
        $sql = "SELECT * FROM categorias  ORDER BY categorias_id ASC;";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }

    

    public function editar($categorias_nombre, $categorias_id){

        
        
        $sql = "UPDATE categorias SET categorias_nombre= ? WHERE categorias_id = ?";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$categorias_nombre, $categorias_id]);


        return $resultado;

    }

    public function consultarPorId($categorias_id){
        $sql = "SELECT * FROM categorias where categorias_id= ?";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$categorias_id]);

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }

    public function limpiarVerificarId($categorias_id){
        //verificar que ese ID exista en la BD

        $statment = $this->db->prepare("SELECT * FROM categorias Where categorias_id = ? LIMIT 1");
        $resultado = $statment->execute([$categorias_id]);
        $dato = $statment->fetchAll(PDO::FETCH_ASSOC);

        

        if (empty($dato)){
            $resultado = false;
        } else {
            $resultado = true;
        }

        return $resultado;

    }

    public function verificarDuplicado($categorias_nombre){
        //verificar si este nombre ya esta registrado
        $sql = "SELECT * FROM categorias WHERE categorias_nombre = ? LIMIT 1";
        $statment = $this->db->prepare($sql);
        $resultado = $statment->execute([$categorias_nombre]);
        $dato = $statment->fetchAll(PDO::FETCH_ASSOC);


        if (empty($dato)){
            $resultado = true;
        } else {
            $resultado = false;
        }

        return $resultado;
    }

    public function verificarDuplicadoId($categorias_nombre, $categorias_id){
        //verificar si mi nombre lo tiene otro aparte de mi.
        $sql = "SELECT * FROM categorias WHERE categorias_nombre = ? AND categorias_id != ? LIMIT 1";
        $statment = $this->db->prepare($sql);
        $resultado = $statment->execute([$categorias_nombre, $categorias_id]);
        $dato = $statment->fetchAll(PDO::FETCH_ASSOC);

        


        if (empty($dato)){
            $resultado = true;
        } else {
            $resultado = false;
        }
        
        return $resultado;


    }

   

}