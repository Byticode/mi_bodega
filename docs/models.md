# 🗄️ Modelos y Capa de Datos — mi_bodega

Los modelos se ubican en el directorio `models/` y extienden de `BaseModel`. Representan la capa de abstracción de datos, encapsulando las consultas SQL a MySQL y asegurando la integridad referencial.

---

## 🛠️ Métodos de Infraestructura en `BaseModel` (`core/BaseModel.php`)

`BaseModel` abstrae la conexión PDO Singleton e implementa la API de acceso a datos segura:

```php
abstract class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Conexion::getInstance();
    }
}
```

### Funciones Principales:

1. **`fetchAll(string $sql, array $params = []): array`**
   - Prepara y ejecuta la consulta SQL con `$params`.
   - Retorna un array con todas las filas asociativas encontradas.
2. **`fetchOne(string $sql, array $params = []): ?array`**
   - Retorna la primera fila asociativa coincidente o `null` si no hay resultados.
3. **`execute(string $sql, array $params = []): bool`**
   - Ejecuta sentencias `INSERT`, `UPDATE` o `DELETE` seguras.
4. **`lastInsertId(): string`**
   - Retorna el ID autoincremental asignado por MySQL en el último `INSERT`.
5. **`exists(string $sql, array $params = []): bool`**
   - Comprueba si existe al menos un registro que cumpla con la condición especificada.
6. **`paginate(string $sql, string $countSql, array $params = [], int $page = 1, int $perPage = 15): array`**
   - Aplica paginación a nivel de base de datos (`LIMIT` y `OFFSET`).
   - Retorna una estructura conteniendo: `items`, `total`, `page`, `perPage` y `totalPages`.
7. **`beginTransaction()`, `commit()`, `rollBack()`**
   - Métodos de control para **Transacciones Atómicas** PDO.

---

## 🗃️ Catálogo de Modelos del Sistema

### 1. `Usuario.php` (`Usuario`)
* **Tabla**: `usuarios`
* **Métodos**:
  - `crear($data)`: Hashea la clave con `PASSWORD_DEFAULT` e inserta el usuario.
  - `verificarCredenciales($username, $password)`: Busca el usuario por su `usuario_nombre` y valida la clave mediante `password_verify()`.
  - `editar($id, $data)` / `editarConClave($id, $data)`: Actualiza perfil o contraseña.

### 2. `Producto.php` (`Producto`)
* **Tabla**: `productos` (Unida con `categorias` y `unidades`)
* **Métodos**:
  - `listarPaginado($page, $perPage, $search)`: Retorna productos paginados con el nombre de su categoría y abreviatura de unidad.
  - `descontarStock($producto_id, $cantidad)`: Descuenta existencias tras una venta.
  - `actualizarStock($producto_id, $cantidad)`: Incrementa existencias tras un surtido.

### 3. `Venta.php` (`Venta`)
* **Tablas**: `ventas` y `venta_detalles`
* **Transacción Atómica de Venta**:
  ```php
  public function crearVentaCompleta(array $ventaData, array $detallesData): int
  {
      $this->beginTransaction();
      try {
          // 1. Insertar cabecera de la venta
          $ventaId = $this->crearVenta($ventaData);
          
          // 2. Insertar renglones y descontar stock
          foreach ($detallesData as $item) {
              $this->crearDetalle($ventaId, $item);
              $productoModel->descontarStock($item['producto_id'], $item['cantidad']);
          }
          
          $this->commit();
          return $ventaId;
      } catch (Throwable $e) {
          $this->rollBack();
          throw $e;
      }
  }
  ```

### 4. `Surtido.php` (`Surtido`)
* **Tablas**: `surtidos` y `surtido_detalles`
* **Transacción Atómica de Surtido**:
  Semántica análoga a ventas, pero realiza el incremento de inventario (`actualizarStock`) para cada producto recibido del proveedor dentro de un bloque `try/catch/rollBack`.

### 5. `Cliente.php` (`Cliente`)
* **Tabla**: `clientes`
* **Métodos**: `crear()`, `listarPaginado()`, `editar()`, `isDuplicateCedula()`, `borrar()`, `changeStatus()`.

### 6. `Proveedor.php` (`Proveedor`)
* **Tabla**: `proveedores`
* **Métodos**: `crear()`, `listarPaginado()`, `editar()`, `isDuplicateNombre()`, `borrar()`, `changeStatus()`.

### 7. `Categoria.php` (`Categoria`)
* **Tabla**: `categorias`
* **Métodos**: `crear()`, `listar()`, `editar()`, `borrar()`, `changeStatus()`.

### 8. `Unidad.php` (`Unidad`)
* **Tabla**: `unidades`
* **Métodos**: `crear()`, `listar()`, `editar()`, `borrar()`, `changeStatus()`.

### 9. `TasaMoneda.php` (`TasaMoneda`)
* **Tabla**: `tasa_moneda`
* **Métodos**:
  - `obtenerUltima()`: Obtiene el último registro de tasa activa para cálculos de precios en moneda local y divisas.
  - `crear($data)`: Registra la nueva tasa del día.
