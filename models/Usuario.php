<?php 

class Usuario extends BaseModel
{
    public function crear($usuario_nombre, $usuario_username, $usuario_clave, $usuario_rol = 'vendedor')
    {
        $hash = password_hash($usuario_clave, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (usuario_nombre, usuario_username, usuario_clave, usuario_rol) VALUES (?, ?, ?, ?)";
        return $this->execute($sql, [$usuario_nombre, $usuario_username, $hash, $usuario_rol]);
    }

    public function listar()
    {
        $sql = "SELECT usuario_id, usuario_nombre, usuario_username, usuario_rol, created_at FROM usuarios ORDER BY usuario_nombre ASC";
        return $this->fetchAll($sql);
    }

    public function editar($usuario_nombre, $usuario_username, $usuario_rol, $usuario_id)
    {
        $sql = "UPDATE usuarios SET usuario_nombre = ?, usuario_username = ?, usuario_rol = ? WHERE usuario_id = ?";
        return $this->execute($sql, [$usuario_nombre, $usuario_username, $usuario_rol, $usuario_id]);
    }

    public function editarConClave($usuario_nombre, $usuario_username, $usuario_clave, $usuario_rol, $usuario_id)
    {
        $hash = password_hash($usuario_clave, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET usuario_nombre = ?, usuario_username = ?, usuario_clave = ?, usuario_rol = ? WHERE usuario_id = ?";
        return $this->execute($sql, [$usuario_nombre, $usuario_username, $hash, $usuario_rol, $usuario_id]);
    }

    public function consultarPorId($usuario_id)
    {
        $sql = "SELECT usuario_id, usuario_nombre, usuario_username, usuario_rol, created_at FROM usuarios WHERE usuario_id = ?";
        return $this->fetchAll($sql, [$usuario_id]);
    }

    public function limpiarVerificarId($usuario_id)
    {
        return $this->exists("SELECT 1 FROM usuarios WHERE usuario_id = ? LIMIT 1", [$usuario_id]);
    }

    public function verificarDuplicado($usuario_username)
    {
        return !$this->exists("SELECT 1 FROM usuarios WHERE usuario_username = ? LIMIT 1", [$usuario_username]);
    }

    public function verificarDuplicadoId($usuario_username, $usuario_id)
    {
        return !$this->exists("SELECT 1 FROM usuarios WHERE usuario_username = ? AND usuario_id != ? LIMIT 1", [$usuario_username, $usuario_id]);
    }

    public function verificarCredenciales($username, $password)
    {
        $sql = "SELECT * FROM usuarios WHERE usuario_username = ? LIMIT 1";
        $usuario = $this->fetchOne($sql, [$username]);

        if ($usuario && password_verify($password, $usuario['usuario_clave'])) {
            return $usuario;
        }
        return false;
    }
}