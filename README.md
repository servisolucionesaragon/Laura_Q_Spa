# Laura Quintero Casa Spa — Sitio Web

Sitio web de **Laura Quintero Casa Spa**, spa de belleza y bienestar ubicado en La América, Medellín.

## ✅ Funcionalidades

- **Logo oficial** de Laura Quintero Casa Spa integrado en: loader, navbar y footer
- **Paleta de marca** (`#ff78a2` / `#ff97b0` / `#ff98bd` / `#808080`) aplicada como sistema de variables CSS, incluyendo scrollbar personalizado
- **Hero slider** con 2 slides, transición suave, puntos de navegación y flechas
- **Trust bar** con badges de confianza (10 años, 5 estrellas, cosmiatra, 500+ clientas)
- **Sección Nosotros** con imagen y lista de valores diferenciales
- **Servicios** en tabs por categoría (Facial, Corporal, Cejas & Pestañas, Nails, Depilación), con fotos reales de tratamientos en cada tarjeta
- **Modal "Ver todos los servicios y precios"** con el catálogo completo agrupado por categoría (Facial, Cera | Hilo, Pestañas | Cejas, Micropigmentación, Corporal, Nails Spa)
- **Proceso en 3 pasos** (Agendar → Experiencia → Resultados)
- **Galería lightbox** con swipe táctil y navegación por teclado
- **FAQ** con acordeón animado
- **Sección Contacto** con info de contacto + mapa Google Maps (dirección enlazada a Google Maps en header, contacto, FAQ y footer)
- **Footer** completo con logo, links, social y CTA
- **Chatbot n8n** (Qchat) integrado reemplazando el botón de WhatsApp flotante
- **Navbar sticky** con efecto scroll + menú hamburguesa para móvil
- **Page loader** animado con logo
- Diseño 100% responsive: breakpoints para desktop grande, tablet (1024px/768px), teléfonos (480px/360px) y móviles en horizontal

## 📁 Estructura

```
index.html              ← Página principal
mantenimiento.html       ← Página de "en mantenimiento" (fallback)
css/style.css            ← Estilos con variables CSS (paleta, scrollbar, componentes)
js/main.js               ← Interactividad (slider, tabs, lightbox, FAQ)
img/
  logo.png                        ← Logo oficial (usado en loader, navbar, footer)
  carousel-1.jpg, carousel-2.jpg   ← Imágenes del hero
  about.jpg, team-2.jpg            ← Sección Nosotros / galería
  CERA-HILO/                       ← Fotos reales: cejas, pestañas, depilación
  Corporal/                        ← Fotos reales: masajes, bronceado
  NAILS/                           ← Fotos reales: manicura y pedicura
```

## 🎨 Paleta de colores

| Variable | Valor | Uso |
|---|---|---|
| `--primary` | `#ff78a2` | Color principal de marca (botones, header, tabs activos, scrollbar) |
| `--primary-dark` | `#c93f68` | Hover de botones y estados activos |
| `--primary-darker` | `#a52f53` | Estados presionados/activos intensos |
| `--primary-light` | `#ff97b0` | Acentos suaves |
| `--primary-soft` | `#ff98bd` | Variedad en gradientes/acentos decorativos |
| `--primary-pale` | `#ffe9ef` | Fondos de badges y tarjetas |
| `--on-primary` | `#241019` | Texto/íconos legibles sobre fondos `--primary` |
| `--gray` | `#808080` | Texto secundario |
| `--dark` | `#241019` | Texto y fondos oscuros (footer, overlays) |

Fuente de la paleta: `Colores.txt` y el logo oficial de la marca.

## 🤖 Chatbot n8n

Configurado en `index.html` con:
- Webhook: `https://n8n.ssaragon.com/webhook/c50f2eb4-3071-49e3-b598-943049f5252f/chat`
- Nombre: 𝑸𝒄𝒉𝒂𝒕 en línea 24/7
- Subtítulo: Bienvenid@ a 𝑳𝑨𝑼𝑹𝑨 𝑸 𝙎𝙋𝘼

## 📞 Contacto del negocio

- WhatsApp: +57 314 738 8239
- Ubicación: [Cra 79 #44a-20, La América, Medellín, Antioquia](https://maps.app.goo.gl/iub6yLsYjbPrn9Ak6)
- Instagram: @lauraq_spa
- Horario: Lunes a viernes 10:00 a.m.–5:00 p.m. · Sábados 9:00 a.m.–2:00 p.m. · Domingos cerrado (no se muestra en el sitio, solo en `Promp n8n.xml` para el chatbot)

## 🚀 Despliegue

Repositorio: [servisolucionesaragon/Laura_Q_Spa](https://github.com/servisolucionesaragon/Laura_Q_Spa) (rama `main`).
Footer del sitio: © 2026, desarrollado por [Servisoluciones Aragón](https://ssaragon.com/).
