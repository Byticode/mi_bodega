# Controladores

Los controladores disponibles son:

- `CategoriasController`
- `ProveedoresController`
- `ClientesController`

Cada controlador extiende `BaseController` y contiene métodos CRUD mínimos:

- `listar()`: obtiene los datos desde el modelo y llama a la vista.
- `crear()`: procesa el formulario de creación.
- `editar()`: procesa el formulario de edición.
- `borrar()`: elimina un registro.
- `status()`: actualiza el estado de un registro.

### OOP en controladores

- **Herencia**: los controladores heredan métodos comunes de `BaseController`.
- **Encapsulación**: se usan métodos privados para validaciones y limpieza de datos.
- **Polimorfismo**: cada controlador implementa sus propias reglas de negocio sobre la misma estructura básica del controlador base.

### Funciones comunes en controladores

- `render($view, $data)`: carga la vista y le pasa datos.
- `redirect($path)`: redirige usando `base_url()`.
- `setFlash($type, $message)`: guarda mensajes en sesión.

### Validaciones

Cada controlador valida el `id` y los campos del formulario antes de llamar al modelo.
