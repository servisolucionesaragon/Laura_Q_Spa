---
name: feedback-no-driving-browser
description: User prefers to test/verify the site themselves in their own browser rather than have Claude drive browser automation tools
metadata:
  type: feedback
---

El usuario, repetidamente, rechazó llamadas a herramientas de automatización de navegador (mcp__claude-in-chrome__*) diciendo "yo lo pruebo/reviso en el navegador" o "yo pruebo el sitio y te aviso".

**Por qué**: prefiere verificar visualmente él mismo en su propio entorno en vez de que se tomen capturas o se controle el navegador por él.

**Cómo aplicar**: para verificar cambios visuales en este proyecto, preferir validaciones de código (balance de tags/llaves, grep de referencias rotas, contraste calculado a mano) y dejar que el usuario confirme visualmente por su cuenta, en vez de iniciar sesiones de Chrome automatizadas sin que lo pida explícitamente. Está bien ofrecer revisar en navegador, pero no asumirlo por defecto.
