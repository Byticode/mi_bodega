# 🛒 mi_bodega — Sistema MVC de Gestión de Inventario y Ventas

Aplicación web robusta desarrollada en **PHP 8 (Arquitectura MVC)** orientada a la gestión profesional de inventario, punto de venta (POS), surtido de proveedores, control de clientes, usuarios, tasas multimoneda y categorización.

---

## 🌟 Características Principales

* **Arquitectura MVC Limpia**: Separación estricta de responsabilidades entre Controladores, Modelos, Vistas y Enrutador amigable.
* **Rutas Semánticas (Clean URLs)**: Estructura de URLs amigables (`/login`, `/logout`, `/pos`, `/productos`, `/usuarios`, etc.) en lugar de parámetros heredados.
* **Seguridad de Grado Profesional**:
  * **Inmunidad a SQL Injection**: Uso exclusivo de sentencias preparadas de PDO (`prepare` / `execute`) centralizadas en `BaseModel`.
  * **Protección Anti-Fuerza Bruta**: Bloqueo automático temporal (5 minutos) tras 5 intentos fallidos consecutivas de inicio de sesión.
  * **Protección CSRF**: Tokens dinámicos validados en todos los formularios mediante `csrf_field()` y `hash_equals`.
  * **Control de Acceso Basado en Roles (RBAC)**: Restricción de funciones de administración (ej. Módulo Usuarios) mediante `requireRole('admin')`.
  * **Autenticación y Sesiones Protegidas**: Middleware `$this->requireAuth()` y regeneración de ID de sesión (`session_regenerate_id(true)`) al autenticarse.
  * **Manejo Centralizado de Errores**: Páginas de error personalizadas y estilizadas para estados HTTP 403, 404 y 500.
* **Transacciones Atómicas de Base de Datos**: Registro de ventas complejas y surtidos masivos con reversión en caso de fallos (`beginTransaction`, `commit`, `rollBack`).
* **Paginación Integrada a Nivel SQL**: Escalabilidad garantizada en módulos de alto volumen mediante consultas paginadas con `LIMIT` y `OFFSET`.
* **Interfaz de Usuario Responsiva**: Diseño refinado con Tailwind CSS v4, tipografía moderna y visualización adaptada a dispositivos móviles.

---

## 📁 Estructura del Proyecto

```text
mi_bodega/
├── assets/
│   ├── css/styles.css        # Estilos CSS compilados Tailwind CSS v4
│   ├── images/logo.png       # Logo oficial de la aplicación
│   └── scripts/ojito.js      # Script de visibilidad de contraseña y autocompletado
├── config/
│   ├── app.php               # Constantes globales (BASE_URL, DEFAULT_CONTROLLER: pos)
│   ├── config_local.php      # Credenciales locales de base de datos
│   └── database.php         # Singleton de conexión PDO
├── controllers/
│   ├── LoginController.php       # Gestión de acceso, fuerza bruta y redirección a POS
│   ├── CategoriasController.php  # Módulo de categorías
│   ├── ProductosController.php   # Módulo de inventario y productos
│   ├── VentasController.php      # Módulo POS y consulta de ventas
│   ├── SurtidosController.php    # Módulo de compras y surtido
│   ├── ClientesController.php    # Módulo de clientes
│   ├── ProveedoresController.php # Módulo de proveedores
│   ├── UsuariosController.php   # Gestión de usuarios del sistema
│   ├── UnidadesController.php    # Gestión de unidades de medida
│   └── TasaMonedaController.php  # Configuración de tasa de cambio
├── core/
│   ├── BaseController.php    # Métodos base (requireAuth, validateNumericId, setFlash)
│   ├── BaseModel.php         # Capa de datos PDO (paginate, fetchAll, execute)
│   └── Router.php            # Enrutador dinámico de peticiones (Default: /pos)
├── database/
│   └── mi_bodega_mvp.sql     # Esquema SQL estructurado
├── includes/
│   ├── flash.php             # Renderizado de mensajes flash de sesión
│   ├── head.php / footer.php # Plantillas compartidas de interfaz
│   ├── helpers.php           # Funciones globales (url, assets, redirect, sanitize, flash, csrf)
│   └── sidebar.php           # Navegación principal del panel
├── models/
│   ├── Usuario.php, Producto.php, Venta.php, Surtido.php... # Clases de acceso a datos
├── views/                    # Plantillas de vistas HTML/PHP por módulo
├── docs/                     # Documentación técnica extendida
└── index.php                 # Punto de entrada único y Autoloader
```

---

## 🚀 Inicio Rápido

### 1. Servidor Local
Para ejecutar el proyecto con el servidor embebido de PHP desde la raíz del repositorio:

```bash
php -S localhost:8000
```

### 2. Acceso Web
Abre tu navegador e ingresa a:
```text
http://localhost:8000/mi_bodega/
```

### 3. Credenciales Iniciales de Administrador
* **Usuario**: `admin`
* **Contraseña**: `admin123`

---

## 📚 Documentación Técnica Extendida

* [`docs/architecture.md`](file:///home/jp/Documentos/proyectos/Byticode/mi_bodega/docs/architecture.md): Arquitectura MVC, mapa exhaustivo de archivos y ciclo de vida de peticiones.
* [`docs/security.md`](file:///home/jp/Documentos/proyectos/Byticode/mi_bodega/docs/security.md): Manual de Seguridad, PDO, Anti-Fuerza Bruta, CSRF, RBAC y XSS.
* [`docs/controllers.md`](file:///home/jp/Documentos/proyectos/Byticode/mi_bodega/docs/controllers.md): Guía detallada de los 10 controladores y sus acciones.
* [`docs/models.md`](file:///home/jp/Documentos/proyectos/Byticode/mi_bodega/docs/models.md): Guía de modelos, paginación SQL y transacciones atómicas PDO.
* [`docs/configuration.md`](file:///home/jp/Documentos/proyectos/Byticode/mi_bodega/docs/configuration.md): Configuración global, patrón Singleton PDO y variables de entorno.
* [`docs/usage.md`](file:///home/jp/Documentos/proyectos/Byticode/mi_bodega/docs/usage.md): Manual de operación, flujo de ventas/surtidos y solución de problemas.
