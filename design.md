# Design — mi_bodega

Sistema de diseño cerrado para esta app. Toda vista lee este archivo antes de
emitir código. No se regenera por página — se extiende o enmienda aquí.

## Genre

modern-minimal (app de gestión / dashboard), con calidez de marca.

## Macrostructure family

- **App pages (todas):** Workbench — sidebar rail fijo a la izquierda + área de
  trabajo. Cada página: page header (título display + descripción opcional +
  acción primaria a la derecha), luego superficie de trabajo (tabla / formulario /
  grid POS). Varía solo el contenido del workbench, nunca el shell.
- Marketing / content pages: no aplican en este proyecto.

## Theme

Marca preservada (paper cálido + olive), en OKLCH. Los valores canónicos viven
en `assets/css/input.css` (`@theme`); aquí la referencia:

- `--color-paper` oklch(98.6% 0.004 95) — fondo de app (antes warmBg)
- `--color-card` oklch(99.6% 0.002 95) — superficie de tarjetas
- `--color-card-2` oklch(96.3% 0.007 95) — superficie hundida (antes warmCard)
- `--color-ink` oklch(26% 0.012 130) — texto principal
- `--color-ink-2` oklch(46% 0.01 130) — texto secundario
- `--color-ink-3` oklch(58% 0.01 120) — texto terciario / placeholders
- `--color-rule` oklch(90.5% 0.008 100) — bordes hairline
- `--color-olive` oklch(45% 0.08 148) — acento (único)
- `--color-olive-hover` oklch(38% 0.07 148)
- `--color-olive-light` oklch(94.5% 0.016 148) — fondo de ítem activo / badges
- `--color-focus` = `--color-olive`
- `--color-danger` oklch(50% 0.16 25) — errores / acciones destructivas
- `--color-danger-bg` oklch(95% 0.02 25)
- `--color-success` oklch(48% 0.1 150) — confirmaciones
- `--color-success-bg` oklch(95% 0.02 150)

Regla de acento: olive ≤ 5 % del viewport — botones primarios, ítem activo del
sidebar, focus rings, montos destacados. Nunca fondos de sección.

## Typography

- Display: **Fraunces**, peso 600, estilo roman (jamás itálica en encabezados),
  tracking -0.015em. Solo: wordmark del sidebar y títulos de página (`h1`).
- Body: **Geist**, 400/500/600/700. Toda la UI.
- Outlier: **Geist Mono**, 500/600. Un solo rol: cifras de dinero y cantidades
  del POS / ticket. Nunca en labels ni botones.
- Escala: ratio 1.25. Anclas: `--text-xs` 0.64rem · `--text-sm` 0.8rem ·
  base 1rem · `--text-md` 1.25rem · `--text-lg` 1.56rem · `--text-xl` 1.95rem.
- Título de página: `var(--text-xl)` Fraunces 600. Mínimo de cuerpo: 14 px.
- Números en tablas y montos: `font-variant-numeric: tabular-nums`.
- Todas las páginas deben tener las mismas y tamaños de fuentes.

## Spacing

Escala de 4 pt con nombres: `--space-xs` 0.75rem · `--space-sm` 1rem ·
`--space-md` 1.5rem · `--space-lg` 2rem · `--space-xl` 3rem. En la práctica se
usan las utilidades de Tailwind (p-3 / p-4 / p-6), que ya siguen 4 pt.

## Motion

- Sin librerías de animación (motion-cut). Solo transiciones CSS de estado.
- `--ease-out`: cubic-bezier(0.16, 1, 0.3, 1) · `--dur-short`: 160ms.
- Se anima solo `background-color`, `border-color`, `color`, `transform`.
  Jamás `transition-all`, jamás bounce/overshoot, jamás `hover:scale-105`.
- Focus rings: aparecen al instante (sin transición).
- `prefers-reduced-motion: reduce` → transiciones a ≤ 1ms.

## Microinteractions stance

- Éxito silencioso: nada de toasts celebratorios; los mensajes flash se muestran
  inline arriba del contenido y se pueden cerrar.
- Sin diálogos de confirmación para acciones reversibles (los borrados actuales
  son GET directos; se mantiene la lógica, solo se mejora la presentación).
- Hover en tablas: cambio de fondo de fila, nada más.
- Iconos: un solo set — **Tabler Icons** como webfont autoalojada. Prohibido
  mezclar sets.

## CTA voice

- Primario: `.btn-primary` — relleno olive, texto blanco, radius 8px,
  `px-4 py-2`, texto `text-sm font-semibold`. Hover: olive-hover. Active:
  `translate-y-px`.
- Secundario: `.btn-secondary` — fondo card, borde rule, tinta ink.
- Peligro: `.btn-danger` — borde danger, texto danger.
- Copy: verbo corto en infinitivo o imperativo ("Agregar", "Guardar cambios").

## Accesibilidad (no negociable)

Toda vista debe cumplir esto; hay un arnés de verificación en la sección final.

- **Un solo `<h1>` por página**: el título de la página. Los encabezados de
  tarjeta o sección son `<h2>` / `<h3>`.
- **Todo control tiene `<label for>`** asociado. El `placeholder` nunca sustituye
  a la etiqueta: desaparece al escribir.
- **Campos obligatorios** marcados con `<span class="req" aria-hidden="true">*</span>`
  y `required`; el asterisco se explica en la etiqueta, no solo con color.
- **Iconos**: `aria-hidden="true"` en **todos** — son decorativos y un glifo de
  área privada se leería como basura. Los enlaces y botones solo-icono llevan
  `aria-label` que nombra el objeto concreto ("Editar la categoría Víveres"),
  no genérico ("Editar").
- **Tablas**: `<th scope="col">` y un `<caption class="sr-only">` que describe
  el contenido.
- **Estado nunca solo por color**: los badges combinan tinte + `.badge-dot` +
  texto; el ítem activo del sidebar suma una barra de posición.
- **Foco visible siempre**, y sin transición: el anillo aparece al instante.
- **Objetivos táctiles** de 44 px mínimo en punteros gruesos.
- **Salto al contenido** (`.skip-link` → `#contenido`) como primer foco.
- **Cambios dinámicos anunciados** con `role="status"` + `aria-live="polite"`
  (carrito del POS, contadores de filtro, totales).
- **Contraste**: todo par texto/fondo ≥ 4.5:1; bordes de control ≥ 1.4:1.

## Componentes (clases en assets/css/input.css)

- **Layout** — `.app-main` · `.app-wrap` (+`--narrow` / `--mid` / `--wide`) ·
  `.topbar` (barra móvil) · `.page-head` · `.page-title` / `.page-sub` ·
  `.breadcrumb` · `.section-title` / `.section-sub`
- **Superficies** — `.card` · `.card-head` / `.card-foot` · `.stat` (+`-label` /
  `-value` / `-note`; modificadores `--money` / `--accent` / `--warn` /
  `--danger`) · `.dl-item` / `.dl-label` / `.dl-value` · `.empty` (+`-icon` /
  `-title` / `-sub`)
- **Acción** — `.btn` + `.btn-primary` / `-secondary` / `-ghost` / `-danger`,
  tamaños `.btn-sm` / `.btn-lg` / `.btn-block` · `.btn-icon` (+`--danger`) ·
  `.link`
- **Formulario** — `.field` · `.label` · `.req` · `.hint` · `.input` /
  `.select` / `.textarea` · `.input--num` · `.search` / `.search-icon` ·
  `.toolbar`
- **Datos** — `.table-wrap` / `.table` (+`.num` / `.col-actions`) · `.badge`
  (+`-success` / `-warn` / `-danger` / `-info` / `-neutral` / `-olive`) ·
  `.badge-dot` · `.tnum` / `.money`
- **Avisos** — `.alert` + `-error` / `-success` / `-warn` / `-info`
- **Rail** — `.sidebar` / `.sidebar-overlay` / `.sidebar-brand` / `.sidebar-mark`
  / `.sidebar-wordmark` / `.sidebar-nav` / `.sidebar-section` / `.sidebar-foot` ·
  `.nav-link` / `.nav-disclosure` / `.nav-caret` / `.nav-submenu` / `.nav-sublink`
- **POS** — `.pos-tile` (+`-name` / `-meta` / `-price`) · `.pos-ticket` ·
  `.pos-line` (+`-name` / `-unit` / `-total`) · `.qty` · `.pos-total` (+`-label`
  / `-value`)
- **Accesibilidad** — `.sr-only` · `.skip-link`

Las vistas usan estas clases para lo repetido y utilidades Tailwind para layout.
Tras tocar `input.css`, recompila: `npm run build:css`.

## Iconos

**Tabler Icons** (`@tabler/icons-webfont`), autoalojada en
`assets/vendor/tabler/`. Tras `npm install` o al actualizar el paquete:
`npm run sync:icons` (copia CSS y fuentes desde `node_modules`). Nunca se
enlaza a un CDN ni se sirve desde `node_modules`.

- Uso: `<i class="ti ti-nombre" aria-hidden="true"></i>`. Siempre `aria-hidden`.
- **El tamaño se controla con `font-size`, no con `width`/`height`.** Cada
  componente fija el suyo (`.btn .ti`, `.btn-icon .ti`, `.nav-link .ti`…);
  para casos sueltos van las utilidades `text-*` de Tailwind. Un `w-4 h-4`
  sobre un `<i>` no hace nada.
- Nombres en <https://tabler.io/icons>. Si el nombre no existe, el icono sale
  **invisible** y sin error: verificar contra
  `assets/vendor/tabler/tabler-icons.min.css` antes de dar por bueno uno nuevo.
- La flecha del `.select` es un data-URI con la misma geometría que
  `ti-chevron-down`, porque un background no puede ser una fuente.

## Formato de cifras

Todo monto pasa por `money()` (helpers.php) → `Bs 1.234,56`. Las cantidades
pasan por `qty()` → sin decimales sobrantes. Nunca `number_format()` suelto en
una vista: es lo que hacía que cada pantalla mostrara las cifras distinto.

## Tasa de cambio

La tasa se obtiene sola desde **DolarAPI Venezuela** (`ve.dolarapi.com`, pública,
sin clave). La lógica vive en `core/TasaService.php`; la configuración en
`config/app.php` (`TASA_*`).

- **Tasa base de la app: el dólar oficial del BCV.** El paralelo y el euro se
  guardan y se muestran, pero no convierten nada.
- Tres capas: caché en disco (`storage/tasas.json`, TTL 30 min) → API →
  tabla `tasa_moneda` como respaldo. Nunca lanza excepciones.
- Si la API falla se aplica una **caché negativa de 5 min**, para que una API
  caída no haga esperar el timeout en cada carga de página.
- Solo se inserta una fila en `tasa_moneda` **cuando el valor cambia**: el
  historial registra cambios reales, no visitas.
- En las vistas se usan dos helpers: `tasa_vigente()` (memorizado, una sola
  consulta por petición) y `usd($bs)`.
- **Sin tasa no se inventa nada.** `usd()` devuelve cadena vacía y la vista
  omite el equivalente; nunca muestra «$ 0,00» como si fuera un dato.
- La tasa se muestra en: pie del rail, POS, Inventario, Ventas y Surtido.

## Estructura de includes (todas las vistas)

1. `<?php $page_title = '…'; $page_desc = '…'; include ruta . '/includes/head.php'; ?>`
   — doctype, `<head>` completo (fuentes + styles.css), `<body>` y skip-link.
2. `include ruta . '/includes/sidebar.php';` — barra móvil + rail con estado
   activo dinámico (controller **y** action).
3. `<main id="contenido" class="app-main">` → `<div class="app-wrap">` → page
   header (`<h1 class="page-title">`) → `include ruta . '/includes/flash.php';`
   → contenido.
4. `include ruta . '/includes/footer.php';` — cierre + sidebar.js.

## What pages MUST share

- El wordmark "mi_bodega" (Fraunces 600) y el monograma MB olive.
- El acento olive y su disciplina de ≤ 5 %.
- Fraunces + Geist + Geist Mono, en sus roles.
- La voz de CTA (shape, radius, padding rhythm).
- El patrón de page header (título + acción primaria derecha).
- Sidebar rail: misma anchura (w-64), mismo comportamiento responsive
  (off-canvas < md, fijo ≥ md).

## What pages MAY differ on

- Contenido del workbench: tabla, formulario, grid de productos, ticket.
- Densidad: POS es denso por naturaleza; formularios respiran más.

## Per-page allowances

- App pages MUST NOT use enrichment — function carries the page.
- Sin métricas inventadas: solo datos reales de la base de datos.

## Deuda conocida

- `views/inventario/inventario.php` es una maqueta con datos inventados
  («124 productos», «Bs 14.250,00») y ningún controlador la enruta — la ruta
  real de Inventario es `productosController&action=listar`. Viola la regla de
  «sin métricas inventadas». Borrar o conectar a datos reales.
- `views/credenciales/credenciales.php` tampoco está enrutada en `index.php` y
  su formulario no envía nada (`onsubmit="event.preventDefault()"`).
- Los borrados siguen siendo GET directos, sin confirmación ni CSRF.
- `ventas.tasa_id` es `NOT NULL`: si la API falla y `tasa_moneda` está vacía,
  el POS bloquea la venta con un aviso. Vale la pena decidir si ese campo
  debería aceptar null.
- El refresco de la tasa ocurre durante una petición GET. Con varias visitas
  simultáneas justo al vencer la caché podrían insertarse filas duplicadas.
  Irrelevante para una bodega de un solo mostrador; a considerar si crece.
