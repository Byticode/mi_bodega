<?php 

class TasaMoneda extends BaseModel
{
    public function crear($moneda, $tasa_usd, $tasa_euro, $tasa_paralelo)
    {
        $sql = "INSERT INTO tasa_moneda (moneda, tasa_usd, tasa_euro, tasa_paralelo) VALUES (?, ?, ?, ?)";
        return $this->execute($sql, [$moneda, $tasa_usd, $tasa_euro, $tasa_paralelo]);
    }

    public function listar()
    {
        $sql = "SELECT * FROM tasa_moneda ORDER BY tasa_id DESC LIMIT 10";
        return $this->fetchAll($sql);
    }

    public function obtenerUltima()
    {
        $sql = "SELECT * FROM tasa_moneda ORDER BY tasa_id DESC LIMIT 1";
        return $this->fetchOne($sql);
    }

    public function consultarPorId($tasa_id)
    {
        $sql = "SELECT * FROM tasa_moneda WHERE tasa_id = ?";
        return $this->fetchOne($sql, [$tasa_id]);
    }
}