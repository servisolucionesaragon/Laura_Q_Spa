---
name: feedback-visual-iteration-style
description: User iterates on visual/sizing details with short qualitative feedback (no exact numbers); respond with concrete values and keep iterating quickly
metadata:
  type: feedback
---

Cuando el usuario da feedback visual (tamaños, espaciados, alineación), lo hace de forma muy corta y sin números exactos: "muy grande", "muy chico", "no está centrado", "quedó más pequeño que el texto". Nunca da valores en píxeles.

**Por qué**: el usuario prueba visualmente en su propio navegador (ver [[feedback-no-driving-browser]]) y comunica ajustes de forma iterativa y conversacional, no con specs detalladas.

**Cómo aplicar**:
- Responder cada ajuste con un valor concreto (no rangos ni "prueba A o B"), y dejar la puerta abierta a seguir afinando ("si sigue grande/chico, dime y lo ajusto").
- No sobre-preguntar por números exactos cuando el feedback es cualitativo — hacer el mejor ajuste de diseño posible con el contexto visual disponible (proporciones de la imagen, ancho del texto vecino, etc.) e iterar rápido si no acierta a la primera.
- Es normal necesitar 3-4 iteraciones en un mismo elemento (pasó con el tamaño y centrado del logo del footer) — no es señal de error, es el flujo de trabajo preferido de este usuario.
