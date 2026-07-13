---
name: project-laura-q-spa
description: Active client project — Laura Quintero Spa website redesign and ongoing iteration, deployed to GitHub/Cloudflare
metadata:
  type: project
---

Sitio web para **Laura Quintero Spa** (spa en La América, Medellín — la marca se acortó de "Laura Quintero Casa Spa" a "Laura Quintero Spa" durante el proyecto, ya reflejado en todo el sitio), cliente de Servisoluciones Aragón (ver [[user-role-servisoluciones-aragon]]). Se hizo un rediseño completo (paleta, fotos reales, SEO, chatbot) durante julio 2026, con iteraciones continuas de contenido y ajustes visuales.

**Detalles técnicos completos** (paleta, decisiones de diseño, estructura de archivos, notas de git/despliegue) están documentados en `.claude/CONTEXT.md` dentro del propio repositorio — leer ese archivo primero al retomar este proyecto, ya que se mantiene actualizado en cada sesión y evita duplicar aquí información que cambia rápido.

**Estado al 2026-07-13**: sitio desplegado en `https://github.com/servisolucionesaragon/Laura_Q_Spa` rama `main` (ver [[reference-lqspa-repos-domains]]). Dominio `lauraqspa.com` **confirmado en producción** (verificado con curl: home e imágenes responden 200). El repo tiene una rama `cloudflare/workers-autoconfig` para despliegue en Cloudflare Workers que no se ha modificado.

**Por confirmar con el usuario en el futuro**:
- Si hace falta agregar el horario de atención también al sitio público (por ahora solo vive en el prompt del chatbot, `Promp n8n.xml`).
