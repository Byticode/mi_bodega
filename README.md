# mi_bodega

Proyecto PHP con arquitectura MVC para gestionar Ventas.

## Estructura principal

- `index.php`: punto de entrada único y dispatcher de controladores.
- `config/app.php`: configuración general de la aplicación.
- `config/database.php`: conexión a base de datos.
- `core/BaseController.php`: clase base para controladores.
- `core/BaseModel.php`: clase base para modelos con métodos PDO reutilizables.
- `controllers/`: contiene los controladores de la aplicación.
- `models/`: contiene los modelos de la aplicación.
- `includes/helpers.php`: funciones helper para URLs, redirección y flash messages.
- `docs/`: documentación segmentada del proyecto.

## Cómo usar

1. Asegúrate de tener Apache/PHP configurado y que el proyecto sirva desde la carpeta `mi_bodega`.
2. Accede en el navegador a `http://localhost/mi_bodega/index.php`.
3. Usa los parámetros `controller` y `action` en la URL para navegar.

Ejemplo:
- `index.php?controller=categoriasController&action=listar`
- `index.php?controller=proveedoresController&action=listar`
- `index.php?controller=clientesController&action=listar`

## Documentación

- `docs/architecture.md`: arquitectura del proyecto.
- `docs/controllers.md`: explicación de los controladores.
- `docs/models.md`: explicación de los modelos.
- `docs/configuration.md`: configuración y variables.
- `docs/usage.md`: uso del proyecto.

## CORS

El proyecto ya incluye configuración de CORS en `.htaccess`, para permitir orígenes externos y métodos HTTP. Si quieres más detalle, revisa la sección `docs/configuration.md`.

## Cambios principales aplicados

- Autoload simple en `index.php` para cargar clases de controladores y modelos.
- `BaseController` para centralizar renderizado y redirección.
- `BaseModel` para centralizar PDO y evitar lógica repetida.
- Helpers para `base_url`, `redirect`, flash messages y sanitización.

## Notas

- La documentación se encuentra en `docs/` con detalles de cada segmento.
