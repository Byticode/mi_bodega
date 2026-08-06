# Arquitectura del proyecto

Este proyecto sigue una estructura MVC simplificada con las siguientes capas:

- `index.php`: punto de entrada único. Lee `controller` y `action` desde la URL y ejecuta el método correspondiente.
- `config/`: archivos de configuración.
- `core/`: clases base para controladores y modelos.
- `controllers/`: lógica de aplicación y control del flujo.
- `models/`: acceso a la base de datos.
- `views/`: plantillas HTML que muestran datos al usuario.
- `includes/`: helpers y fragmentos reutilizables.

La separación permite que el código PHP se mantenga más limpio y evita repetir lógica de base de datos y de redirección.

## Uso de POO

Este proyecto aplica principios básicos de la programación orientada a objetos:

- **Herencia**: `CategoriasController`, `ProveedoresController` y `ClientesController` extienden `BaseController`; `Categoria`, `Proveedor` y `Cliente` extienden `BaseModel`.
- **Encapsulación**: los controladores y modelos mantienen funciones internas privadas y protegen la lógica compartida en `BaseController` y `BaseModel`.
- **Abstracción**: `BaseModel` oculta la lógica de PDO y expone métodos como `fetchOne`, `fetchAll`, `execute`, `deleteById` y `updateStatusById`.
- **Polimorfismo**: cada modelo redefine operaciones específicas (`crear`, `editar`, `consultarPorId`) usando la misma interfaz lógica del modelo base.

## Uso de PDO

La clase `Conexion` devuelve un objeto `PDO`, y `BaseModel` usa ese objeto con métodos orientados a objetos (`prepare`, `execute`, `fetch`, `fetchAll`). Esto provee:

- seguridad contra inyección SQL con consultas preparadas,
- abstracción de la capa de acceso a datos,
- reutilización de código en el modelo base.
