# Modelos

Los modelos usan `BaseModel` para interactuar con la base de datos mediante PDO.

## Métodos de `BaseModel`

- `query($sql, $params)`: prepara y ejecuta la consulta.
- `fetchAll($sql, $params)`: obtiene todos los resultados.
- `fetchOne($sql, $params)`: obtiene un único registro.
- `execute($sql, $params)`: ejecuta una consulta de modificación.
- `deleteById($table, $idColumn, $id)`: elimina un registro por ID.
- `updateStatusById($table, $statusColumn, $status, $idColumn, $id)`: actualiza el estado de un registro.
- `isDuplicateField($table, $field, $value, $exceptId, $idColumn)`: valida duplicados genéricos.
- `exists($sql, $params)`: verifica si existe un registro.

## OOP y PDO en modelos

- **Herencia**: cada modelo extiende `BaseModel` y reutiliza la conexión PDO.
- **Polimorfismo**: los modelos redefinen métodos como `editar`, `crear` y `consultarPorId`.
- **Encapsulación**: `BaseModel` oculta la conexión y el manejo de consultas, dejando la lógica específica del modelo en cada clase.
- **Abstracción**: los métodos de `BaseModel` brindan una API simplificada sobre PDO.

## Modelos existentes

### `Categoria`
- `crear($nombre)`
- `listar()`
- `editar($nombre, $id)`
- `consultarPorId($id)`
- `existsId($id)`
- `isDuplicateNombre($nombre)`
- `isDuplicateNombreExceptId($nombre, $id)`

### `Proveedor`
- `crear($nombre, $telefono)`
- `listar()`
- `editar($nombre, $telefono, $id)`
- `consultarPorId($id)`
- `existsId($id)`
- `isDuplicateNombre($nombre)`
- `isDuplicateNombreExceptId($nombre, $id)`

### `Cliente`
- `crear($nombre, $apellido, $cedula, $telefono, $correo)`
- `listar()`
- `editar($nombre, $apellido, $cedula, $telefono, $correo, $id)`
- `consultarPorId($id)`
- `existsId($id)`
- `isDuplicateCedula($cedula)`
- `isDuplicateCedulaExceptId($cedula, $id)`
- `isDuplicateCorreo($correo)`
- `isDuplicateCorreoExceptId($correo, $id)`
