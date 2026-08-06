# Configuración

## `config/app.php`

Define variables globales:

- `BASE_URL`: ruta base de la aplicación.
- `APP_NAME`: nombre de la aplicación.
- `DEFAULT_CONTROLLER`: controlador por defecto.
- `DEFAULT_ACTION`: acción por defecto.

## `config/database.php`

Contiene la clase `Conexion` con la conexión PDO.
Se usa en `BaseModel` para inicializar `$db`.

## `.htaccess`

El archivo `.htaccess` incluye reglas para manejar CORS y la reescritura de URL.

Ejemplo de CORS en el archivo actual:

- `Access-Control-Allow-Origin "*"`
- `Access-Control-Allow-Methods "GET,PUT,POST,DELETE"`
- `Access-Control-Allow-Headers "X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method"`

Estas líneas permiten peticiones desde cualquier origen y facilitan la comunicación con APIs o frontends externos durante el desarrollo.

