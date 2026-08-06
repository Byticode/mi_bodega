<?php 

class Conexion
{
    // Definimos las credenciales de la base de datos como propiedades privadas y estáticas
    private static $host = 'localhost';
    private static $dbName = 'mi_bodega';
    private static $username = 'root'; // Cambia esto si tu usuario de phpMyAdmin es diferente
    private static $password = '';     // Cambia esto si tienes contraseña en tu servidor local

    public static function conectar()
    {
        try {
            // Configurar el DSN (Data Source Name) especificando el charset utf8mb4
            $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$dbName . ";charset=utf8mb4";

            // Crear la instancia de PDO
            $pdo = new PDO($dsn, self::$username, self::$password);

            // Configurar PDO para que lance excepciones en caso de errores de SQL
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Configurar para que por defecto devuelva los datos como arreglos asociativos
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $pdo;
        } catch (PDOException $e) {
            // Si algo falla, detenemos el sistema y mostramos el error
            die("❌ Error fatal en la conexión a la base de datos: " . $e->getMessage());
        }
    }
}


?>