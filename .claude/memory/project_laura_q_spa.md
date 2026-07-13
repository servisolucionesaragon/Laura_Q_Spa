---
name: project-laura-q-spa
description: Active client project — Laura Quintero Casa Spa website redesign and ongoing iteration, deployed to GitHub/Cloudflare
metadata:
  type: project
---

Sitio web para **Laura Quintero Casa Spa** (spa en La América, Medellín), cliente de Servisoluciones Aragón (ver [[user-role-servisoluciones-aragon]]). Se hizo un rediseño completo (paleta, fotos reales, SEO, chatbot) durante julio 2026, con iteraciones continuas de contenido y ajustes visuales.

**Detalles técnicos completos** (paleta, decisiones de diseño, estructura de archivos, notas de git/despliegue) están documentados en `.claude/CONTEXT.md` dentro del propio repositorio — leer ese archivo primero al retomar este proyecto, ya que se mantiene actualizado en cada sesión y evita duplicar aquí información que cambia rápido.

**Estado al 2026-07-13**: sitio desplegado en `https://github.com/servisolucionesaragon/Laura_Q_Spa` rama `main` (ver [[reference-lqspa-repos-domains]]), dominio objetivo `lauraqspa.com` (aún por confirmar si ya está apuntado/en producción). El repo tiene una rama `cloudflare/workers-autoconfig` para despliegue en Cloudflare Workers que no se ha modificado.

**Por confirmar con el usuario en el futuro**:
- Si el dominio `lauraqspa.com` ya está activo/apuntado.
- Si hace falta agregar el horario de atención también al sitio público (por ahora solo vive en el prompt del chatbot, `Promp n8n.xml`).
