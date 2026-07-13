# Laura Quintero Spa — Sitio Web

Sitio web de **Laura Quintero Spa**, spa de belleza y bienestar en La América, Medellín. Página única (`index.html`) con paleta de marca, catálogo de servicios con precios, galería y chatbot de WhatsApp vía n8n.

- **Producción**: [lauraqspa.com](https://lauraqspa.com/)
- **Repositorio**: [servisolucionesaragon/Laura_Q_Spa](https://github.com/servisolucionesaragon/Laura_Q_Spa) (rama `main`)

## ✅ Funcionalidades

- **Hero slider** con 2 slides, transición suave, flechas y puntos de navegación
- **Trust bar** con badges de confianza (10 años, 5 estrellas, cosmiatra, 500+ clientas)
- **Sección Nosotros** con imagen y lista de valores diferenciales
- **Servicios** en tabs por categoría (Facial, Corporal, Cejas & Pestañas, Nails, Depilación), con fotos reales de tratamientos en cada tarjeta (clase `.service-card--photo`)
- **Modal "Ver todos los servicios y precios"** (`#price-modal`) con el catálogo completo agrupado por categoría: Facial, Cera | Hilo, Pestañas | Cejas, Micropigmentación, Corporal, Nails Spa
- **Proceso en 3 pasos** (Agendar → Experiencia → Resultados)
- **Galería con lightbox**, swipe táctil y navegación por teclado
- **FAQ** con acordeón animado
- **Sección Contacto** con info de contacto + mapa de Google embebido
- **Footer** con logo, links de navegación/servicios, redes sociales y CTA
- **Chatbot n8n** (Qchat) integrado, reemplaza el botón flotante de WhatsApp
- **Navbar sticky** con efecto scroll + menú hamburguesa en móvil
- **Page loader** animado con logo
- **SEO**: `robots.txt`, `sitemap.xml`, `<link rel="canonical">`, favicon (`img/marca.png`)
- **Open Graph / Twitter Card** con imagen de vista previa para compartir en redes (`img/og-image.png`)
- **Scrollbar personalizado** con el color de marca
- **100% responsive**: breakpoints en 1024px (tablet), 768px (menú hamburguesa), 480px y 360px (teléfonos pequeños), más una media query para celulares en horizontal

## 📁 Estructura

```
index.html               ← Página principal (incluye el modal de precios)
mantenimiento.html        ← Página de "en mantenimiento" (fallback, no enlazada; bloqueada en robots.txt)
css/style.css              ← Único stylesheet — variables de marca en :root, resto organizado por sección
js/main.js                 ← Slider, tabs de servicios, lightbox de galería, modal de precios, FAQ, menú móvil
robots.txt, sitemap.xml    ← SEO (dominio lauraqspa.com)
Colores.txt                ← Paleta de referencia original del cliente (no se despliega)
Promp n8n.xml               ← Prompt del chatbot (⚠️ ignorado por git — ver sección Chatbot)
.claude/CONTEXT.md          ← Notas de contexto técnico/diseño para retomar el proyecto
.claude/memory/             ← Copia local de la memoria de colaboración (perfil de usuario, feedback, referencias)
img/
  logo.png                          ← Logo para navbar y loader (fondo transparente)
  logo full2.png                    ← Logo para el footer (fondo transparente, con texto)
  marca.png                         ← Ícono de marca — usado como favicon
  og-image.png                      ← logo full2.png aplanado sobre blanco, para Open Graph/Twitter
  carousel-1.jpg, carousel-2.jpg     ← Imágenes del hero
  about.jpg, team-2.jpg              ← Sección Nosotros / galería
  CERA-HILO/                         ← Fotos reales: cejas, pestañas, depilación
  Corporal/                          ← Fotos reales: masajes, bronceado
  NAILS/                             ← Fotos reales: manicura y pedicura
```

## 🎨 Paleta de colores

Fuente: `Colores.txt` y el logo oficial de la marca.

| Variable | Valor | Uso |
|---|---|---|
| `--primary` | `#ff78a2` | Color principal de marca (botones, top-bar, tabs activos, scrollbar) |
| `--primary-dark` | `#c93f68` | Hover de botones y estados activos |
| `--primary-darker` | `#a52f53` | Estados presionados/activos intensos |
| `--primary-light` | `#ff97b0` | Acentos suaves |
| `--primary-soft` | `#ff98bd` | Variedad en gradientes/acentos decorativos |
| `--primary-pale` | `#ffe9ef` | Fondos de badges y tarjetas |
| `--on-primary` | `#241019` | Texto/íconos legibles sobre fondos `--primary` (el rosa de marca es muy claro para texto blanco) |
| `--gray` | `#808080` | Texto secundario |
| `--dark` | `#241019` | Texto y fondos oscuros (footer, overlays del hero) |

> El logo tiene texto en gris oscuro sin fondo propio (excepto `logo full2.png`, transparente). Por eso el logo del footer/CTA lleva una tarjeta blanca detrás (`background: var(--white)`) para mantener legibilidad sobre fondos oscuros.

## 💰 Catálogo de precios

El modal "Ver todos los servicios y precios" (`#price-modal` en `index.html`) es la **única fuente pública** de precios del sitio. Debe mantenerse sincronizado manualmente con el `<catalogo>` de `Promp n8n.xml` (el chatbot tiene su propia copia porque ese archivo no se despliega con el sitio). Si cambian precios o servicios, actualizar **ambos** archivos.

Categorías actuales: Facial, Cera | Hilo, Pestañas | Cejas, Micropigmentación, Corporal, Nails Spa.

## 🤖 Chatbot n8n

Configurado en `index.html` (al final del `<body>`) con:
- Webhook: `https://n8n.ssaragon.com/webhook/c50f2eb4-3071-49e3-b598-943049f5252f/chat`
- Nombre: 𝑸𝒄𝒉𝒂𝒕 en línea 24/7
- Subtítulo: Bienvenid@ a 𝑳𝑨𝑼𝑹𝑨 𝑸 𝙎𝙋𝘼

Las instrucciones del bot viven en **`Promp n8n.xml`** (raíz del proyecto), en formato XML válido. **No se sube a git** (está en `.gitignore`) porque es configuración interna de negocio — se pega manualmente en el nodo del chatbot en n8n. Contiene:
- `<negocio>`: dirección, Google Maps, WhatsApp, Instagram, horario de atención, métodos de pago, política de reserva, duración de sesiones.
- `<catalogo>`: la misma lista de precios del modal del sitio (única fuente de verdad, sin datos incompletos).
- `<rules>`: comportamiento — incluye una regla para no volcar todo el catálogo de una vez (primero pregunta por categoría) y una regla anti prompt-injection.

**Horario de atención** (solo vive en el prompt del chatbot, no se muestra en el sitio público): Lunes a viernes 10:00 a.m.–5:00 p.m. · Sábados 9:00 a.m.–2:00 p.m. · Domingos cerrado.

## 📞 Contacto del negocio

- WhatsApp: +57 314 738 8239 — `https://wa.me/573147388239`
- Ubicación: [Cra 79 #44a-20, La América, Medellín, Antioquia](https://maps.app.goo.gl/iub6yLsYjbPrn9Ak6)
- Instagram: [@lauraq_spa](https://www.instagram.com/lauraq_spa/)
- Facebook, TikTok: enlazados en top-bar, footer y sección Contacto

> El teléfono fijo antes listado (591 2380) se eliminó del sitio a pedido del cliente — no debe reaparecer.

## 🔍 SEO y redes sociales

- `robots.txt` permite todo excepto `/mantenimiento.html`, y apunta a `sitemap.xml`.
- `sitemap.xml` lista la home (`https://lauraqspa.com/`).
- `<link rel="canonical">` fija la URL canónica en `https://lauraqspa.com/`.
- Open Graph y Twitter Card usan `img/og-image.png` (1099×824, fondo blanco sólido — se generó aplanando `logo full2.png`, que es transparente y se rompía en modo oscuro).
- Para validar la vista previa al compartir el link: [opengraph.xyz](https://www.opengraph.xyz/) o el Sharing Debugger de Facebook (ojo: cachea, hay que forzar "Scrape Again" si ya se compartió antes).

## 🚀 Despliegue / Git

- Repo: `https://github.com/servisolucionesaragon/Laura_Q_Spa`, rama `main`.
- Hay una rama `cloudflare/workers-autoconfig` (con `wrangler.jsonc`, worker `laura-q-spa`) para despliegue en Cloudflare Workers — **no modificar** salvo pedido explícito.
- `.gitignore` excluye archivos internos de negocio: `Thumbs.db`, `desktop.ini`, `Laura Q Spa N8N.json`, `Laura Q Spa.json` (exports de workflows n8n), `Laura Q Spa VS Code.code-workspace`, `Promp n8n.xml`.

## 🧠 Contexto para retomar el proyecto

Ver `.claude/CONTEXT.md` para el historial de decisiones de diseño (por qué el logo del footer mide lo que mide, por qué los botones usan texto oscuro sobre el rosa de marca, etc.) y `.claude/memory/` para notas de colaboración con el cliente/usuario.
