# Laura Quintero Spa — Contexto del proyecto

Notas de contexto para retomar el trabajo en este sitio en sesiones futuras. Sitio de una sola página (`index.html`) para un spa de belleza en Medellín.

## Negocio

- Nombre: **Laura Quintero Spa** (renombrado desde "Laura Quintero Casa Spa" — el título, meta tags y todos los `alt=""` del sitio ya usan el nombre corto; si se encuentra "Casa Spa" en algún lado es texto desactualizado)
- WhatsApp: +57 314 738 8239 — `https://wa.me/573147388239`
- Dirección: Cra 79 #44a-20, La América, Medellín, Antioquia
- Google Maps: `https://maps.app.goo.gl/iub6yLsYjbPrn9Ak6` (coords exactas: 6.2509277,-75.5973354, usadas en el iframe embebido de Contáctanos)
- Instagram: @lauraq_spa
- Horario: Lunes a viernes 10:00 a.m.–5:00 p.m. · Sábados 9:00 a.m.–2:00 p.m. · Domingos cerrado (**no se muestra en el sitio público**, solo vive en `Promp n8n.xml` para el chatbot)
- Dominio de producción: `lauraqspa.com` (usado en `robots.txt`, `sitemap.xml`, canonical y meta OG/Twitter)
- Ya no se muestra el teléfono fijo 591 2380 en ningún lado — se eliminó a pedido del cliente.

## Paleta de marca

Fuente: `Colores.txt` (raíz del proyecto) + el logo oficial de la marca.

- `--primary: #ff78a2` (color principal — el cliente probó `#ff97b0` como principal y luego pidió volver a `#ff78a2`, mantener así salvo que se indique lo contrario)
- `--primary-dark: #c93f68`, `--primary-darker: #a52f53` — variantes oscuras para texto/botones con buen contraste WCAG (el `--primary` puro es muy pastel y falla contraste como color de texto sobre fondo claro)
- `--primary-light: #ff97b0`, `--primary-soft: #ff98bd`, `--primary-pale: #ffe9ef`
- `--on-primary: #241019` — patrón usado en botones (`.btn-primary`), `#top-bar` y `.tab-btn.active`: fondo `--primary` puro + texto/ícono oscuro (`--on-primary`) en vez de blanco, porque el rosa es muy claro para texto blanco encima. Hover de esos elementos sí pasa a `--primary-dark` + texto blanco.
- `--gray: #808080`, `--dark: #241019` (fondos oscuros: footer, overlays del hero, loader ya NO es oscuro — ver abajo)

## Decisiones de diseño relevantes

- **Page loader**: fondo claro (gradiente blanco → `--primary-pale`), no oscuro — el logo tiene texto gris oscuro que se perdía sobre fondo oscuro. Bug corregido: cargaba `img/Baaaer.png` (no existía).
- **Logo en navbar**: sin recuadro oscuro forzado (se quitó, ya no aplica con la paleta clara).
- **Logo en footer** (`.footer-brand`): usa `img/logo full2.png` (PNG transparente). Fondo blanco + padding detrás porque el texto del logo es gris oscuro y necesita contraste sobre el footer oscuro. Tamaño final: `width: 190px` (se iteró varias veces: 130px muy grande → 64px muy chico → ancho igual al párrafo (280px) muy grande de nuevo → 190px quedó bien). Todo el bloque `.footer-brand` quedó `text-align:center` con el logo centrado (`margin: 0 auto`) para alinear con el párrafo y los íconos sociales.
- **Logo en CTA/footer sobre fondo oscuro**: card blanca detrás (`background:white`, `border-radius`, `box-shadow`) para legibilidad.
- **Servicios**: tabs por categoría con fotos reales (`img/CERA-HILO/`, `img/Corporal/`, `img/NAILS/`) mapeadas 1:1 a cada tratamiento vía clase `.service-card--photo`. Facial se quedó solo-ícono (no hay fotos reales de esa categoría).
- **Modal de precios** (`#price-modal`, botón "Ver todos los servicios y precios" en la sección Servicios): catálogo completo con 6 categorías (Facial, Cera | Hilo, Pestañas | Cejas, Micropigmentación, Corporal, Nails Spa). Los precios ahí **deben coincidir siempre** con los de `Promp n8n.xml` (son la misma fuente de verdad, mantenida en dos lugares porque uno es público/HTML y el otro es config interna del chatbot).
- **Secciones eliminadas** (a pedido del cliente, con su CSS/JS asociado ya limpiado): CTA band ("¿Lista para renovarte?") y Testimonios/Reseñas completos (carrusel, controles, `.t-dot`, etc. — también se quitó del navbar/footer/JS).
- **Scrollbar personalizado** con `--primary` (`::-webkit-scrollbar` + `scrollbar-color` para Firefox).
- **Responsive**: breakpoints en 1024px (tablet, grids a 2 columnas), 768px (menú hamburguesa, grids a 1 columna), 480px y 360px (teléfonos pequeños, tabs de servicios con scroll horizontal), más una media query para celulares en horizontal (`max-height: 480px and orientation: landscape`).
- **SEO/social**: `robots.txt`, `sitemap.xml`, `<link rel="canonical">`, favicon (`img/marca.png`), meta Open Graph + Twitter Card usando `img/og-image.png` (1200×630px, opaco — proporción ideal para tarjetas grandes) como imagen de vista previa al compartir el link. Es un diseño propio (logo + dominio + título + botón CTA), no autogenerado. Historial: se probó primero con `img/Baner.png` (1028×243, muy panorámico), luego con un flatten de `logo full2.png` sobre blanco (1099×824, sin transparencia pero proporción no ideal), y finalmente el cliente subió directamente esta versión de 1200×630 — si se vuelve a reemplazar `img/og-image.png`, actualizar también `og:image:width`/`og:image:height` en `index.html` con las dimensiones reales del archivo nuevo.

## Chatbot n8n (`Promp n8n.xml`)

- **Está en `.gitignore`** — es config interna de negocio, no se despliega con el sitio, se pega manualmente en el nodo del chatbot en n8n.
- Formato XML válido (con declaración `<?xml version="1.0" encoding="UTF-8"?>`), estructura: `<role>`, `<tone>`, `<goal>`, `<negocio>` (datos operativos), `<catalogo>` (única fuente de precios, sin `...` ni datos incompletos), `<rules>`.
- Regla importante de UX: cuando preguntan por servicios en general, el bot **NO** debe volcar todo el catálogo de una vez — primero lista solo los nombres de categorías y pregunta cuál le interesa. Solo manda todo si el usuario lo pide explícitamente ("mándame todo", "catálogo completo").
- Tiene regla anti prompt-injection.
- Si se actualizan precios/horario/dirección, hay que actualizar en **tres lugares**: el modal de precios en `index.html`, `Promp n8n.xml`, y este archivo de contexto si el dato es relevante.

## Git / despliegue

- Repo: `https://github.com/servisolucionesaragon/Laura_Q_Spa`, rama `main`.
- El repo remoto ya tenía historial previo (una página placeholder "en mantenimiento" + rama `cloudflare/workers-autoconfig` con `wrangler.jsonc`, worker name `laura-q-spa`, sin dominio custom configurado ahí). Se fusionó con `--allow-unrelated-histories -X ours` en vez de forzar el push, para no perder ese historial.
- **No tocar la rama `cloudflare/workers-autoconfig`** salvo que el cliente lo pida explícitamente.
- `.gitignore` excluye: `Thumbs.db`, `desktop.ini`, `Laura Q Spa N8N.json`, `Laura Q Spa.json` (exports de workflows de n8n), `Laura Q Spa VS Code.code-workspace`, `Promp n8n.xml`.
- `mantenimiento.html` sigue en el repo como página de fallback (no enlazada desde `index.html`), y está bloqueada en `robots.txt` (`Disallow: /mantenimiento.html`).

## Archivos clave

```
index.html        ← Página principal (todo el markup, incluye modal de precios)
css/style.css      ← Único stylesheet, variables CSS al inicio (:root)
js/main.js         ← Slider, tabs, lightbox galería, modal de precios, FAQ, menú móvil
Colores.txt        ← Paleta original de referencia (no se despliega, es nota del cliente)
Promp n8n.xml      ← Prompt del chatbot (gitignored, config interna)
robots.txt, sitemap.xml ← SEO, dominio lauraqspa.com
img/               ← CERA-HILO/, Corporal/, NAILS/ tienen fotos reales de tratamientos
                     og-image.png es el diseño de vista previa social (ver sección SEO/social), logo full2.png es el logo del footer
.claude/memory/    ← Copia local versionada de la memoria de colaboración (espejo de la memoria externa del asistente)
```

## Estado de producción

- Dominio `lauraqspa.com` **ya está en línea** (verificado con curl: HTTP 200, tanto la home como `img/og-image.png` resuelven correctamente) al 2026-07-13.
