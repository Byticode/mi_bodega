<?php

class Conexion
{
    private static ?PDO $instancia = null;

    public static function conectar(): PDO
    {
        if (self::$instancia !== null) {
            return self::$instancia;
        }

        if (file_exists(__DIR__ . "/config_local.php")) {
            require_once __DIR__ . "/config_local.php";
        }

        $host = defined("DB_HOST") ? DB_HOST : "localhost";
        $dbName = defined("DB_NAME") ? DB_NAME : "mi_bodega";
        $username = defined("DB_USER") ? DB_USER : "root";
        $password = defined("DB_PASS") ? DB_PASS : "";

        try {
            $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";
            
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            self::$instancia = $pdo;
            return self::$instancia;
        } catch (PDOException $e) {
            die("❌ Error fatal en la conexión a la base de datos: " . $e->getMessage());
        }
    }
}

