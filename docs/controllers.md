# 🎮 Controladores del Sistema — mi_bodega

Todos los controladores se ubican en la carpeta `controllers/` y heredan de `BaseController`. Actúan como el orquestador entre las peticiones HTTP del navegador, la capa de datos (Modelos) y la capa de representación (Vistas).

---

## 🛠️ Métodos Heredados de `BaseController` (`core/BaseController.php`)

Todos los controladores tienen acceso inmediato a las siguientes herramientas de infraestructura:

| Método Base | Descripción | Uso típico |
| :--- | :--- | :--- |
| `requireAuth()` | Verifica la existencia de `$_SESSION['usuario']`. Si no existe, redirige a `/login`. | En el constructor de controladores protegidos. |
| `requireRole(string $role)` | Verifica que el rol del usuario en sesión coincida. Si no coincide, renderiza la vista `403.php`. | En acciones restringidas a Administradores. |
| `validateNumericId($id, $redirectUrl)` | Valida que `$id` sea un entero mayor a 0. En caso negativo, emite error flash y redirige. | En acciones de edición, eliminación o consulta por ID. |
| `setFlash(string $type, string $message)` | Almacena un mensaje (`'success'` o `'error'`) en la sesión para mostrarlo en la siguiente página. | Tras procesar un formulario POST. |
| `redirect(string $path)` | Redirige HTTP agregando el prefijo de la aplicación `BASE_URL`. | Al finalizar operaciones exitosas o fallidas. |
| `renderError(int $code, string $message)` | Despacha la vista de error correspondiente (`403`, `404` o `500`) estableciendo la cabecera HTTP adecuada. | Cuando se detectan accesos denegados o fallos. |
| `getAuthUserId(): ?int` | Retorna el ID numérico del usuario en sesión. | Al registrar ventas o surtidos para auditoría. |

---

## 📋 Catálogo Completo de Controladores y Acciones

### 1. `LoginController` (`controllers/loginController.php`)
* **Propósito**: Autenticación de usuarios, gestión de cierres de sesión, redirección automática al POS y bloqueo anti-fuerza bruta.
* **Acciones**:
  - `login()` (`GET` / `POST`): Muestra el formulario de ingreso. Si el usuario ya tiene sesión activa, emite un aviso flash y redirige directamente a `/pos`. En `POST`, verifica si la cuenta está bloqueada (máx. 5 intentos), retiene el usuario ingresado (`$_SESSION['old']['username']`), valida la clave con `password_verify()`, regenera el ID de sesión (`session_regenerate_id(true)`) y redirige al Punto de Venta (`/pos`).
  - `logout()` (`GET`): Invalida y destruye la sesión del usuario (`session_destroy()`), redirigiendo a `/login`.

### 2. `CategoriasController` (`controllers/categoriasController.php`)
* **Propósito**: Administración del catálogo de categorías de productos.
* **Ruta amigable base**: `/categorias`
* **Acciones**:
  - `listar()` (`GET`): Muestra la lista completa de categorías activas.
  - `crear()` (`POST`): Valida el nombre de la categoría e inserta el nuevo registro.
  - `editar()` (`GET` / `POST`): Carga el formulario de edición y actualiza los datos.
  - `borrar()` (`GET`): Elimina una categoría por su ID.
  - `status()` (`GET`): Alterna el estado activo/inactivo de la categoría.

### 3. `ProductosController` (`controllers/productosController.php`)
* **Propósito**: Inventario general, cálculo dinámico de precios (Costo + Ganancia 30% + IVA 16%), conversión multidivisa (USD / Bs.), alertas de stock, eliminación masiva y ajustes masivos de precios.
* **Ruta amigable base**: `/productos`
* **Acciones**:
  - `listar()` (`GET`): Carga el catálogo de productos paginados con precios en dólares ($) y su equivalencia dinámica en bolívares (Bs.) según la tasa BCV del día.
  - `crear()` (`GET` / `POST`): Renderiza el formulario con vista previa en tiempo real del desglose de precios (Costo Neto → % Ganancia → IVA → Precio Final USD → Bs.) y procesa la creación del producto.
  - `editar()` (`GET` / `POST`): Modifica costos, porcentaje de ganancia, IVA, stock y datos descriptivos del producto.
  - `eliminarMasivo()` (`POST`): Recibe un arreglo JSON de IDs de productos seleccionados desde la tabla del catálogo, los valida como enteros positivos y ejecuta la eliminación en lote mediante `Producto::eliminarMasivo()`.
  - `ajustarPreciosMasivo()` (`POST`): Aplica ajustes masivos de **Precio de Costo Neto** o **Precio de Venta Final** (aumento/disminución) por **porcentaje (%)** o **monto fijo ($ USD)**. Al modificar el costo, el sistema recalcula automáticamente el precio de venta final respetando el margen de ganancia e IVA de cada producto.
  - `inventario()` (`GET`): Presenta un reporte de auditoría de existencias.
  - `borrar()` (`GET`): Gestión individual de baja de un producto por ID.
  - `status()` (`GET`): Cambia el estado activo/inactivo de un producto.

### 4. `VentasController` (`controllers/ventasController.php`)
* **Propósito**: Punto de Venta (POS), procesamiento de tickets y control de facturación.
* **Ruta amigable base**: `/ventas` / `/pos`
* **Acciones**:
  - `pos()` (`GET`): Carga la interfaz interactiva de punto de venta con buscador de productos y carrito dinámico.
  - `crear()` (`POST`): **Transacción atómica**. Procesa el carrito, registra la venta en cabecera, inserta los detalles y descuenta el stock de cada producto.
  - `listar()` (`GET`): Presenta el historial de ventas paginado con métricas del día.
  - `ver()` (`GET`): Carga el detalle completo y desglose de un ticket de venta.
  - `editar()` (`GET` / `POST`): Permite completar ventas en estado pendiente.

### 5. `SurtidosController` (`controllers/surtidosController.php`)
* **Propósito**: Registro de compras a proveedores e incremento de stock de inventario.
* **Ruta amigable base**: `/surtidos`
* **Acciones**:
  - `listar()` (`GET`): Muestra la lista de surtidos realizados.
  - `crear()` (`GET` / `POST`): **Transacción atómica**. Registra el surtido a un proveedor, inserta los ítems e incrementa el stock de los productos.
  - `ver()` (`GET`): Muestra el desglose de productos y costos de un surtido específico.

### 6. `ClientesController` (`controllers/clientesController.php`)
* **Propósito**: Gestión de clientes frecuentes y datos de facturación.
* **Ruta amigable base**: `/clientes`
* **Acciones**:
  - `listar()` (`GET`): Lista de clientes con paginación.
  - `crear()` (`POST`): Registro de nuevo cliente con validación de cédula/ID único.
  - `editar()` (`GET` / `POST`): Modificación de datos personales y de contacto.
  - `borrar()` (`GET`) / `status()` (`GET`): Eliminación o cambio de estado.

### 7. `ProveedoresController` (`controllers/proveedoresController.php`)
* **Propósito**: Directorio de proveedores para compras y surtidos.
* **Ruta amigable base**: `/proveedores`
* **Acciones**:
  - `listar()` (`GET`): Muestra la lista de proveedores registrados.
  - `crear()` (`POST`): Alta de nuevo proveedor comercial.
  - `editar()` (`GET` / `POST`): Modificación de teléfono y datos de contacto.
  - `borrar()` (`GET`) / `status()` (`GET`): Gestión de estado del proveedor.

### 8. `UsuariosController` (`controllers/usuariosController.php`)
* **Propósito**: Administración de cuentas del sistema y roles de acceso.
* **Seguridad Estricta**: **Restringido únicamente a rol `admin`** mediante `requireRole('admin')`.
* **Ruta amigable base**: `/usuarios`
* **Acciones**:
  - `listar()` (`GET`): Muestra los usuarios del sistema.
  - `crear()` (`POST`): Registra un nuevo usuario encriptando su clave con Bcrypt.
  - `editar()` (`GET` / `POST`): Permite cambiar nombre, usuario, rol y actualizar clave opcionalmente.
  - `status()` (`GET`): Alterna el estado activo/inactivo del usuario. Los usuarios inactivos son rechazados por el `LoginController` al intentar iniciar sesión.

### 9. `UnidadesController` (`controllers/unidadesController.php`)
* **Propósito**: Unidades de medida para el inventario (Kg, Litros, Paquetes, etc.).
* **Ruta amigable base**: `/unidades`
* **Acciones**: `listar()`, `crear()`, `editar()`, `borrar()`, `status()`.

### 10. `TasaMonedaController` (`controllers/tasaMonedaController.php`)
* **Propósito**: Control multi-moneda y valor de cambio del día (USD Oficial, Euro, Paralelo). La tasa activa se utiliza globalmente para convertir precios de dólares a bolívares.
* **Ruta amigable base**: `/tasa-moneda`
* **Acciones**: `listar()`, `crear()`.
