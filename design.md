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

- `--color-paper`        oklch(98.6% 0.004 95)   — fondo de app (antes warmBg)
- `--color-card`         oklch(99.6% 0.002 95)   — superficie de tarjetas
- `--color-card-2`       oklch(96.3% 0.007 95)   — superficie hundida (antes warmCard)
- `--color-ink`          oklch(26% 0.012 130)    — texto principal
- `--color-ink-2`        oklch(46% 0.01 130)     — texto secundario
- `--color-ink-3`        oklch(58% 0.01 120)     — texto terciario / placeholders
- `--color-rule`         oklch(90.5% 0.008 100)  — bordes hairline
- `--color-olive`        oklch(45% 0.08 148)     — acento (único)
- `--color-olive-hover`  oklch(38% 0.07 148)
- `--color-olive-light`  oklch(94.5% 0.016 148)  — fondo de ítem activo / badges
- `--color-focus`        = `--color-olive`
- `--color-danger`       oklch(50% 0.16 25)      — errores / acciones destructivas
- `--color-danger-bg`    oklch(95% 0.02 25)
- `--color-success`      oklch(48% 0.1 150)      — confirmaciones
- `--color-success-bg`   oklch(95% 0.02 150)

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
- Iconos: un solo set — Heroicons outline (SVG inline, stroke 1.5). Prohibido
  mezclar sets; los `data-lucide` se eliminaron.

## CTA voice

- Primario: `.btn-primary` — relleno olive, texto blanco, radius 8px,
  `px-4 py-2`, texto `text-sm font-semibold`. Hover: olive-hover. Active:
  `translate-y-px`.
- Secundario: `.btn-secondary` — fondo card, borde rule, tinta ink.
- Peligro: `.btn-danger` — borde danger, texto danger.
- Copy: verbo corto en infinitivo o imperativo ("Agregar", "Guardar cambios").

## Componentes (clases en assets/css/input.css)

`.card` · `.btn` / `.btn-primary` / `.btn-secondary` / `.btn-danger` /
`.btn-icon` · `.input` / `.select` / `.label` · `.table` · `.badge` (+`-success`
/ `-warn` / `-danger` / `-neutral`) · `.alert` / `.alert-error` /
`.alert-success` · `.page-title` / `.page-sub` · `.tnum`

Las vistas usan estas clases para lo repetido y utilidades Tailwind para layout.

## Estructura de includes (todas las vistas)

1. `<?php $page_title = '…'; include ruta . '/includes/head.php'; ?>` — doctype,
   `<head>` completo (fuentes + styles.css), apertura de `<body>`.
2. `include ruta . '/includes/sidebar.php';` — rail con estado activo dinámico.
3. `<main class="app-main">` → page header → `include ruta . '/includes/flash.php';`
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
