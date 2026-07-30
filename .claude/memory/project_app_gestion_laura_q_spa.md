---
name: project-app-gestion-laura-q-spa
description: "App de gestión (TPV, citas, caja, clientes) en app/ de este repo, desplegada en app.lauraqspa.com — módulos, features de WhatsApp/caja/colores y bugs reales corregidos en la ronda del 2026-07-29/30."
metadata:
  type: project
---

Instancia dedicada de **TPV Estética y SPA** (app del portal SSA) para el
cliente real Laura Q Spa, viviendo en la carpeta **`app/`** de este mismo
repo (monorepo — la raíz sigue siendo el sitio estático, ver
[[project-laura-q-spa]]) y desplegada en `https://app.lauraqspa.com`
(VPS Oracle Cloud del portal SSA, dominio propio, fuera de Cloudflare).

**Detalle técnico completo** (todos los módulos, decisiones de diseño y los
2 bugs reales encontrados y corregidos) vive en `.claude/CONTEXT.md` de este
repo, sección "App de gestión en app/" → "Ronda de features (2026-07-30)" —
no duplicado aquí a propósito, ese archivo se mantiene actualizado en cada
sesión.

**Resumen de módulos**: Dashboard, Agenda (vistas mes/semana/día), Clientes
(búsqueda en vivo, alertas de cumpleaños por WhatsApp), Servicios (antes
"Tratamientos", solo texto renombrado), Punto de Venta (exige caja abierta),
Caja diaria (apertura/cierre, gastos/ingresos, reporte de cierre
imprimible/PDF/WhatsApp), Ventas (tique con logo, envío por WhatsApp,
editar/anular solo admin), Métodos de pago (catálogo configurable, antes
ENUM fijo), Productos/Proveedores/Bonos/Empleados/Cabinas, Configuración
(colores extensivos + PWA instalable).

**Dos bugs reales de esta ronda, con lección reutilizable para cualquier
app Laravel del portal**:
1. `Route::resource('nombre-con-guion', ...)` sin `->parameters([...])`
   explícito puede romper editar/eliminar **en silencio** (sin error, sin
   persistir el cambio) si el controlador usa un parámetro camelCase — ver
   [[project_route_binding_kebab_case]] en la memoria del portal (NAS
   `Aplicaciones web/`).
2. Un `@php` con closure/array multilínea dentro de `@push('scripts')` puede
   corromper la compilación de Blade de todo el bloque (incluidos `@json`
   anteriores), y `php artisan view:cache` **no lo detecta** (solo compila,
   no ejecuta). Verificar cambios de Blade no triviales renderizando la
   vista contra datos reales, no solo con `view:cache`.

**Por qué importa**: es la app de gestión más completa construida hasta
ahora sobre un derivado de TPV Estética y SPA — sirve de referencia de
patrones (WhatsApp, caja, colores configurables, búsqueda en vivo) para
futuras réplicas de otras apps TPV del portal para otros clientes.
