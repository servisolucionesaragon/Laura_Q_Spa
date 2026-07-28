---
name: feedback-no-permission-prompts
description: User wants zero permission prompts in this project — Edit/Write/Bash/PowerShell are allowlisted in .claude/settings.local.json
metadata: 
  node_type: memory
  type: feedback
  originSessionId: 55f0c8eb-3304-498d-a835-2cd3d57ac6c5
  modified: 2026-07-28T17:59:08.484Z
---

El usuario pidió repetidamente (2026-07-28) no ver más prompts de permisos ("Do you want to proceed?", "Do you want to make this edit...") en este proyecto.

**Por qué**: confía en el flujo de trabajo establecido (ver [[feedback-git-commit-push-pattern]]) y los prompts le interrumpen la iteración.

**Cómo aplicar**: `.claude/settings.local.json` ya tiene en `permissions.allow`: `Edit`, `Write`, `Bash`, `PowerShell` (herramienta completa, sin patrón). Si algún prompt reaparece con otra herramienta, agregarla al allowlist directamente sin preguntar. Esto NO cambia la disciplina de revisar `git status`/`git diff` antes de commitear ni el respeto al `.gitignore`.
