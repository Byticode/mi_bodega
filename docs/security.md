# 🛡️ Manual de Seguridad y Hardening — mi_bodega

Este documento detalla la arquitectura de seguridad implementada en **mi_bodega** para proteger la información comercial, prevenir vulnerabilidades comunes (OWASP Top 10) y guiar al equipo de desarrollo sobre cómo mantener el código 100% seguro.

---

## 🔒 1. Inmunidad a Inyección SQL (PDO Prepared Statements)

### El Problema
La inyección SQL ocurre cuando datos no confiables introducidos por un usuario se concatenan directamente en una consulta SQL, permitiendo a un atacante alterar la lógica de la base de datos, saltarse la autenticación o extraer/borrar información.

### La Solución en `mi_bodega`
Toda interacción con MySQL se canaliza obligatoriamente a través de `BaseModel` utilizando la API de sentencias preparadas de `PDO` (`prepare` y `execute`).

```php
// ❌ INCORRECTO (Jamás hacer esto en el código):
$sql = "SELECT * FROM productos WHERE producto_nombre = '" . $_POST['nombre'] . "'";

// ✅ CORRECTO (Implementación en BaseModel.php):
protected function fetchAll(string $sql, array $params = []): array
{
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
```

### Por qué es seguro:
Al usar marcadores de posición (`?`), el motor de la base de datos compila la estructura de la consulta SQL **antes** de evaluar los datos. Cualquier valor enviado en `$params` es tratado estrictamente como un dato literal, inutilizando cualquier intento de inyección de código SQL.

---

## 🛡️ 2. Protección Anti-Fuerza Bruta (*Anti-Brute Force*)

### El Problema
Ataques automatizados donde un script prueba miles de combinaciones de usuario y contraseña hasta encontrar la correcta.

### La Solución en `LoginController`
El sistema implementa un mecanismo dinámico basado en sesión para limitar y bloquear intentos fallidos:

1. **Conteo de Fallos y Retroalimentación**: Cada intento con contraseña errónea incrementa `$_SESSION['login_attempts']` y notifica explícitamente al usuario el número de intentos restantes (*"Usuario o contraseña incorrectos. Te quedan X intento(s)."*).
2. **Bloqueo Temporal (5 minutos)**: Al alcanzar 5 intentos fallidos consecutivos, se activa el bloqueo por 300 segundos, notificando en interfaz la restricción.
3. **Rechazo Inmediato**: Cualquier intento de inicio de sesión durante el periodo de bloqueo es rechazado automáticamente antes de consultar la base de datos, protegiendo tanto las cuentas como el rendimiento del servidor.
4. **Rescisión en Éxito**: Un inicio de sesión exitoso reinicia los contadores de fallos y limpia datos temporales.

---

## 🔑 3. Encriptación de Contraseñas y Gestión de Sesiones

### Almacenamiento Criptográfico de Claves
* **Creación y Actualización**: Se utiliza la función nativa `password_hash($clave, PASSWORD_DEFAULT)`, que aplica el algoritmo **Bcrypt** con un *salt* criptográfico aleatorio generado automáticamente por versión de PHP.
* **Verificación de Login**: Se valida mediante `password_verify($clave_ingresada, $hash_almacenado)`. Ninguna contraseña se almacena en texto plano.

### Protección contra Fijación de Sesión (*Session Fixation*)
Al momento de completar la autenticación exitosa en `LoginController::login`, el sistema ejecuta:

```php
session_regenerate_id(true);
```

Esto destruye la cookie de sesión previa y emite un nuevo `PHPSESSID` al navegador, impidiendo que un atacante suplante la sesión de un usuario legítimo mediante IDs predeterminados.

---

## 🚫 4. Control de Acceso Basado en Roles (RBAC) y `requireAuth`

### Autenticación Global (`BaseController::requireAuth`)
Todos los controladores protegidos llaman a `$this->requireAuth()` en su constructor. Si el usuario no ha iniciado sesión (`$_SESSION['usuario']`), la petición es interrumpida y redirigida al formulario de `/login`.

### Autorización de Roles (`BaseController::requireRole`)
Módulos críticos (como la administración de usuarios del sistema) restringen la ejecución especificando el rol requerido:

```php
public function __construct()
{
    parent::__construct();
    $this->requireAuth();
    $this->requireRole('admin'); // Solo accesible por Administradores
}
```

Si un usuario con rol `vendedor` intenta ingresar a `/usuarios`, `requireRole` detiene la solicitud de inmediato y despliega la pantalla oficial de **Error 403 (Acceso Prohibido)**.

---

## 🧼 5. Tokens CSRF y Sanitización contra XSS

### Protección contra Falsificación de Petición en Sitio Cruzado (CSRF)
Para evitar que sitios maliciosos envíen formularios en nombre de un usuario autenticado:
1. **Generación del Token**: El helper `csrf_token()` crea una cadena aleatoria criptográfica mediante `random_bytes(32)`.
2. **Inyección en Formulario**: Todos los formularios HTML incorporan el campo oculto mediante `<?= csrf_field() ?>`.
3. **Validación en Controlador**: Antes de procesar solicitudes `POST`, los controladores comparan el token mediante `hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])`.

### Prevensión de Script de Sitios Cruzados (XSS)
Toda salida de datos dinámica enviada al navegador desde las vistas PHP pasa por el helper `sanitize()` o `htmlspecialchars()`:

```php
<td class="font-medium"><?= htmlspecialchars($producto['producto_nombre']) ?></td>
```

Esto convierte caracteres especiales (como `<script>`, `"`, `'`) en entidades HTML inofensivas (ej. `&lt;script&gt;`), evitando la ejecución de JavaScript malicioso en el navegador del usuario.

---

## ⚠️ 6. Manejo Centralizado de Errores (403, 404, 500)

El enrutador (`Router.php`) y el controlador base capturan las anomalías durante la ejecución:
* **HTTP 403 (Forbidden)**: Despachado cuando se intenta acceder a una sección restringida por rol.
* **HTTP 404 (Not Found)**: Despachado cuando la URL solicitada no coincide con ninguna ruta registrada.
* **HTTP 500 (Internal Error)**: Despachado en caso de excepciones no capturadas en controladores o modelos.

Las vistas de error (`views/errors/*.php`) están estandarizadas con Tailwind CSS para brindar retroalimentación clara sin exponer *stack traces* o información sensible del servidor al usuario final.

---

## 📋 Checklist de Seguridad para Desarrolladores

Al agregar nuevas funcionalidades al proyecto, todo desarrollador debe verificar:

- [ ] ¿El nuevo controlador invoca `$this->requireAuth()` en su constructor?
- [ ] Si la acción modifica datos, ¿es un método `POST` que valida el token CSRF?
- [ ] En la consulta SQL, ¿se usaron marcadores `?` y se pasaron los parámetros en un array `$params`?
- [ ] Al imprimir variables en las vistas, ¿se usó `htmlspecialchars()` o `sanitize()`?
- [ ] Si la función es solo para administradores, ¿se ejecutó `$this->requireRole('admin')`?
