<?php 

require_once './config/database.php';

class Usuario{

    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    public function crear($usuario_nombre, $usuario_username, $usuario_clave, $usuario_rol = 'vendedor')
    {
        $hash = password_hash($usuario_clave, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (usuario_nombre, usuario_username, usuario_clave, usuario_rol) VALUES (?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$usuario_nombre, $usuario_username, $hash, $usuario_rol]);

        return $resultado;
    }

    public function listar(){
        $sql = "SELECT usuario_id, usuario_nombre, usuario_username, usuario_rol, created_at FROM usuarios ORDER BY usuario_nombre ASC;";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }

    public function editar($usuario_nombre, $usuario_username, $usuario_rol, $usuario_id){
        
        $sql = "UPDATE usuarios SET usuario_nombre = ?, usuario_username = ?, usuario_rol = ? WHERE usuario_id = ?";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$usuario_nombre, $usuario_username, $usuario_rol, $usuario_id]);

        return $resultado;
    }

    public function editarConClave($usuario_nombre, $usuario_username, $usuario_clave, $usuario_rol, $usuario_id){
        
        $hash = password_hash($usuario_clave, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET usuario_nombre = ?, usuario_username = ?, usuario_clave = ?, usuario_rol = ? WHERE usuario_id = ?";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$usuario_nombre, $usuario_username, $hash, $usuario_rol, $usuario_id]);

        return $resultado;
    }

    public function consultarPorId($usuario_id){
        $sql = "SELECT usuario_id, usuario_nombre, usuario_username, usuario_rol, created_at FROM usuarios WHERE usuario_id = ?";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([$usuario_id]);

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }

    public function limpiarVerificarId($usuario_id){
        $statement = $this->db->prepare("SELECT * FROM usuarios WHERE usuario_id = ? LIMIT 1");
        $resultado = $statement->execute([$usuario_id]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = false;
        } else {
            $resultado = true;
        }

        return $resultado;
    }

    public function verificarDuplicado($usuario_username){
        $sql = "SELECT * FROM usuarios WHERE usuario_username = ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$usuario_username]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = true;
        } else {
            $resultado = false;
        }

        return $resultado;
    }

    public function verificarDuplicadoId($usuario_username, $usuario_id){
        $sql = "SELECT * FROM usuarios WHERE usuario_username = ? AND usuario_id != ? LIMIT 1";
        $statement = $this->db->prepare($sql);
        $resultado = $statement->execute([$usuario_username, $usuario_id]);
        $dato = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dato)){
            $resultado = true;
        } else {
            $resultado = false;
        }
        
        return $resultado;
    }

    public function verificarCredenciales($username, $password){
        $sql = "SELECT * FROM usuarios WHERE usuario_username = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['usuario_clave'])) {
            return $usuario;
        }
        return false;
    }
}
?>