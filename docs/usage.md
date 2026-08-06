# Uso del proyecto

## Acceso

Abre el navegador y visita:

`http://localhost/mi_bodega/index.php`

## Navegación

Puedes acceder a cada módulo usando estos parámetros en la URL:

- `controller=categoriasController`
- `controller=proveedoresController`
- `controller=clientesController`

Y estas acciones:

- `action=listar`
- `action=crear`
- `action=editar`

## Ejemplos

- Listar categorías:

  `index.php?controller=categoriasController&action=listar`

- Crear proveedor:

  `index.php?controller=proveedoresController&action=crear`

- Editar cliente:

  `index.php?controller=clientesController&action=editar&id=1`

## Mensajes

El proyecto usa flash messages guardados en sesión para mostrar éxito o error después de redirigir.
