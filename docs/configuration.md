# ⚙️ Configuración y Entorno — mi_bodega

Este documento detalla las constantes de configuración, conexión a la base de datos MySQL, parámetros del servidor PHP y variables de entorno del proyecto **mi_bodega**.

---

## 📌 1. Variables Globales de la Aplicación (`config/app.php`)

El archivo `config/app.php` establece las constantes de ejecución del sistema:

```php
define('BASE_URL', '/mi_bodega/');
define('APP_NAME', 'mi_bodega');
define('DEFAULT_CONTROLLER', 'CategoriasController');
define('DEFAULT_ACTION', 'listar');
date_default_timezone_set('America/Caracas');
```

* **`BASE_URL`**: Define el prefijo de la URL usado por el helper `url()` para generar enlaces absolutos y redirecciones HTTP.
* **`DEFAULT_CONTROLLER` / `DEFAULT_ACTION`**: Indican la ruta fallback de la aplicación cuando no se especifica ninguna ruta en la petición.
* **`date_default_timezone_set`**: Sincroniza la hora del servidor con el huso horario de Venezuela (`America/Caracas`) para registrar fechas exactas en ventas, surtidos y auditorías.

---

## 🔌 2. Conexión a Base de Datos MySQL (`config/database.php` & `config/config_local.php`)

### Credenciales Locales (`config/config_local.php`)
Este archivo almacena los parámetros de acceso a la base de datos MySQL. Para entornos de desarrollo o producción, ajusta estas constantes según tu servidor:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mi_bodega_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Patron Singleton de Conexión (`config/database.php`)
La clase `Conexion` gestiona la instancia única de PDO en la aplicación:

```php
class Conexion
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$instance;
    }
}
```

#### Atributos Clave de Seguridad en PDO:
* **`PDO::ERRMODE_EXCEPTION`**: Lanza excepciones PDO que son capturadas por la aplicación en lugar de emitir advertencias no estructuradas.
* **`PDO::FETCH_ASSOC`**: Retorna arreglos asociativos ordenados por nombre de columna.
* **`PDO::ATTR_EMULATE_PREPARES => false`**: Obliga a MySQL a utilizar sentencias preparadas nativas verdaderas a nivel de motor de base de datos, maximizando la inmunidad contra SQL Injection.

---

## 🚀 3. Modos de Despliegue

### Servidor PHP de Desarrollo Local
Para iniciar el servidor integrado de desarrollo:
```bash
php -S localhost:8000
```

### Servidor Apache / Nginx en Producción
Asegúrate de que el módulo `mod_rewrite` esté activado en Apache para permitir el procesamiento de rutas semánticas mediante `.htaccess`.
