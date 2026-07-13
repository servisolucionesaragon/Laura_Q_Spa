---
name: feedback-git-commit-push-pattern
description: User wanted explicit confirmation before pushing early on, then switched to direct "hacer commit/push a git" instructions once trust was established
metadata:
  type: feedback
---

Al principio del trabajo en este repo, se preguntó explícitamente antes de hacer `git push` (acción visible/compartida) porque el repo remoto ya tenía historial previo del cliente. Tras varias rondas de trabajo correcto, el usuario empezó a pedir directamente "hacer commit/push a git" sin más detalle, confiando en que se revisa lo que se va a subir antes de commitear.

**Cómo aplicar**: en este repo específico, cuando el usuario diga "commit/push" de forma directa, proceder sin pedir confirmación adicional — pero seguir revisando `git status`/`git diff` primero (confirmar que no hay archivos sensibles ni cambios inesperados) antes de subir, tal como se ha hecho hasta ahora. Los archivos internos de negocio (prompt del chatbot, exports de workflows de n8n) deben seguir excluidos vía `.gitignore` — ver `.claude/CONTEXT.md` en el repo para la lista actual y por qué.
