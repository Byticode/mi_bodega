<?php 

require_once './config/database.php';

class Cliente{

    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    public function crear($cliente_nombre, $cliente_apellido, $cliente_cedula, $cliente_telefono, $cliente_correo)
    {
        $sql = "INSERT INTO clientes (cliente_nombre, cliente_apellido, cliente_cedula, cliente_telefono, cliente_correo) VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$cliente_nombre, $cliente_apellido, $cliente_cedula, $cliente_telefono, $cliente_correo]);

        return $resultado;
    }

    public function listar(){
        $sql = "SELECT * FROM clientes ORDER BY cliente_id ASC;";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }

    public function editar($cliente_nombre, $cliente_apellido, $cliente_cedula, $cliente_telefono, $cliente_correo, $cliente_id){
        
        $sql = "UPDATE clientes SET cliente_nombre = ?, cliente_apellido = ?, cliente_cedula = ?, cliente_telefono = ?, cliente_correo = ? WHERE cliente_id = ?";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$cliente_nombre, $cliente_apellido, $cliente_cedula, $cliente_telefono, $cliente_correo, $cliente_id]);

        return $resultado;
    }

    public function consultarPorId($cliente_id){
        $sql = "SELECT * FROM clientes WHERE cliente_id = ?";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$cliente_id]);

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }

    public function limpiarVerificarId($cliente_id){
        //verificar que ese ID exista en la BD

        $statement = $this->db->prepare("SELECT * FROM clientes WHERE cliente_id = ? LIMIT 1");
        $resultado = $statement->execute([$cliente_id]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = false;
        } else {
            $resultado = true;
        }

        return $resultado;
    }

    public function verificarDuplicado($cliente_cedula){
        //verificar si esta cédula ya está registrada
        $sql = "SELECT * FROM clientes WHERE cliente_cedula = ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$cliente_cedula]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = true;
        } else {
            $resultado = false;
        }

        return $resultado;
    }

    public function verificarDuplicadoId($cliente_cedula, $cliente_id){
        //verificar si mi cédula la tiene otro aparte de mi.
        $sql = "SELECT * FROM clientes WHERE cliente_cedula = ? AND cliente_id != ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$cliente_cedula, $cliente_id]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = true;
        } else {
            $resultado = false;
        }
        
        return $resultado;
    }

    public function verificarDuplicadoCorreo($cliente_correo){
        // Si el correo es NULL o vacío, no se verifica duplicado
        if (empty($cliente_correo)) {
            return true;
        }
        
        //verificar si este correo ya está registrado
        $sql = "SELECT * FROM clientes WHERE cliente_correo = ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$cliente_correo]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = true;
        } else {
            $resultado = false;
        }

        return $resultado;
    }

    public function verificarDuplicadoCorreoId($cliente_correo, $cliente_id){
        // Si el correo es NULL o vacío, no se verifica duplicado
        if (empty($cliente_correo)) {
            return true;
        }
        
        //verificar si mi correo lo tiene otro aparte de mi.
        $sql = "SELECT * FROM clientes WHERE cliente_correo = ? AND cliente_id != ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$cliente_correo, $cliente_id]);
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