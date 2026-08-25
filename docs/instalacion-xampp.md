# Instalación en Windows con XAMPP

Guía para instalar y ejecutar **mi_bodega** en Windows usando XAMPP, Apache, MariaDB/MySQL, PHP, Composer y Node.js.

## 1. Requisitos

Instala XAMPP con Apache, MariaDB y phpMyAdmin, además de Composer y Node.js LTS. Git solo es necesario si vas a clonar el repositorio.

Comprueba desde PowerShell:

```powershell
php -v
composer --version
node --version
npm --version
```

Si `php` no se reconoce, agrega `C:\xampp\php` al `PATH` de Windows o usa `C:\xampp\php\php.exe`.

## 2. Copiar el proyecto

Apache sirve por defecto los proyectos ubicados dentro de `C:\xampp\htdocs`.

Con Git:

```powershell
cd C:\xampp\htdocs
git clone --depth 1 --single-branch -b NOMBRE_RAMA URL_DEL_REPOSITORIO
cd C:\xampp\htdocs\mi_bodega
```

Si recibiste un ZIP, descomprímelo como `C:\xampp\htdocs\mi_bodega`. El archivo `index.php` debe quedar directamente dentro de esa carpeta.

## 3. Configurar PHP de XAMPP

Edita `C:\xampp\php\php.ini`. Activa estas extensiones quitando el punto y coma inicial (`;`) si están comentadas:

```ini
extension=curl
extension=fileinfo
extension=gd
extension=mbstring
extension=openssl
extension=pdo_mysql
```

En XAMPP también puedes abrir el archivo mediante **XAMPP Control Panel > Apache > Config > PHP (php.ini)**. Después de modificarlo, reinicia Apache.

Verifica la configuración:

```powershell
php --ini
php -m
```

`gd` es necesaria para FPDF, `pdo_mysql` para MariaDB/MySQL y `curl` para consultar las tasas de cambio externas.

## 4. Configurar Apache

En el panel de XAMPP inicia **Apache** y **MySQL**.

El proyecto incluye `.htaccess`, por lo que Apache necesita `mod_rewrite`. En `C:\xampp\apache\conf\httpd.conf` verifica que esta línea no esté comentada:

```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

También verifica que el bloque de `htdocs` permita `.htaccess`:

```apache
<Directory "C:/xampp/htdocs">
    AllowOverride All
    Require all granted
</Directory>
```

Reinicia Apache después de cambiar `httpd.conf`.

Si el puerto 80 está ocupado, cambia `Listen 80` por `Listen 8080` y usa `http://localhost:8080/mi_bodega/`.

## 5. Crear e importar la base de datos

1. Abre [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Entra en **Nueva** y crea una base llamada `mi_bodega`.
3. Selecciónala y entra en **Importar**.
4. Selecciona `database/mi_bodega_mvp.sql`.
5. Pulsa **Importar** y espera el mensaje de éxito.

La configuración predeterminada es:

```text
Host:     localhost
Base:     mi_bodega
Usuario:  root
Clave:    (vacía)
```

Estos valores están en `config/config_local.php`. Si configuraste una contraseña para `root`, actualiza `DB_PASS`; si cambiaste el usuario o la base, actualiza también `DB_USER` o `DB_NAME`.

## 6. Instalar dependencias

Desde la raíz del proyecto ejecuta Composer:

```powershell
cd C:\xampp\htdocs\mi_bodega
composer install
```

Esto instala FPDF y crea `vendor/autoload.php`, requerido por `index.php`.

Instala las dependencias frontend y genera los estilos:

```powershell
npm install
npm run build:css
npm run sync:icons
```

`npm install` no reemplaza a `composer install`: son administradores de dependencias distintos.

## 7. Configurar el almacenamiento de tasas

El caché de tasas se guarda en `storage/tasas.json`. Crea la carpeta si no existe:

```powershell
New-Item -ItemType Directory -Force storage
```

Apache debe tener permiso de escritura sobre esa carpeta. En una instalación local normal, los permisos predeterminados de Windows suelen ser suficientes.

## 8. Abrir la aplicación

Con Apache y MySQL activos, visita:

```text
http://localhost/mi_bodega/
```

La aplicación detecta automáticamente el prefijo `/mi_bodega/`. Las rutas principales son `/login`, `/pos`, `/productos`, `/clientes`, `/proveedores`, `/surtidos`, `/ventas` y `/reportes`.

Credenciales iniciales importadas por el SQL:

```text
Usuario:    admin
Contraseña: admin123
```

Cambia la contraseña después del primer inicio de sesión.

## 9. Verificación rápida

Comprueba que:

1. `http://localhost/` muestra el panel de XAMPP.
2. `http://localhost/phpmyadmin/` abre phpMyAdmin.
3. `http://localhost/mi_bodega/` muestra el login.
4. `/pos` carga después de iniciar sesión.
5. Los estilos, iconos e imágenes aparecen correctamente.
6. Puedes consultar productos sin errores de conexión.

## 10. Errores frecuentes

### `Failed opening required vendor/autoload.php`

Ejecuta `composer install` desde la raíz del proyecto. Si Composer informa que falta GD, activa `extension=gd` en el `php.ini` que muestra `php --ini` y repite el comando.

### `could not find driver`

Activa `extension=pdo_mysql`, reinicia Apache y confirma:

```powershell
php -m | Select-String pdo_mysql
```

### Error de conexión a la base de datos

Confirma que MySQL/MariaDB esté iniciado, que exista la base `mi_bodega` y que `config/config_local.php` coincida con tus credenciales.

### Las rutas devuelven 404 o los estilos no cargan

Comprueba que el proyecto esté dentro de `htdocs`, que `mod_rewrite` esté habilitado y que `AllowOverride All` esté configurado. Reinicia Apache.

### El puerto 80 o 443 está ocupado

Revisa **Netstat** en XAMPP para identificar el proceso. Puedes detenerlo o cambiar Apache a otro puerto, como `8080`.

### Aparecen advertencias de SNMP al ejecutar PHP

Son advertencias de una extensión opcional y no pertenecen a `mi_bodega`. Puedes ignorarlas o comentar `extension=snmp` en el `php.ini` activo.

## 11. Servidor integrado de PHP

Como alternativa a Apache, desde la raíz del proyecto ejecuta:

```powershell
php -S localhost:3000 server.php
```

Luego abre `http://localhost:3000/`. MySQL de XAMPP puede seguir activo. Detén el servidor con `Ctrl+C`.

