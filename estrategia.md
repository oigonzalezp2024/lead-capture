### 1. Nuevas Preguntas para RUTA_A (Enfoque a Negocio/ROI)

Estas preguntas buscan identificar qué tan urgente es el proyecto y si hay presupuesto asignado.

| Código | Ruta | Pregunta | Tipo | Propósito |
| --- | --- | --- | --- | --- |
| **A5** | RUTA_A | ¿En cuánto tiempo esperas tener el producto en el mercado? | choice | Medir urgencia (Time-to-market). |
| **A6** | RUTA_A | ¿Cuentas con un presupuesto estimado para esta etapa del desarrollo? | choice | Calificar solvencia. |

**Opciones para estas preguntas:**

* **Para A5:**
* `INMEDIATO` (Menos de 3 meses) -> `next_route_map: CONTACT`
* `MEDIANO` (3 a 6 meses) -> `next_route_map: CONTACT`
* `EXPLORANDO` (Solo estoy cotizando) -> `next_route_map: CONTACT`


* **Para A6:**
* `STARTUP` (Buscando MVP económico)
* `CORPORATIVO` (Presupuesto definido para calidad/escalabilidad)
* `PENDIENTE` (Necesito asesoría para definirlo)



---

### 2. Nuevas Preguntas para RUTA_C (Enfoque Técnico/Estructural)

Para los perfiles técnicos, necesitamos saber qué tan "limpio" recibiremos el proyecto.

| Código | Ruta | Pregunta | Tipo | Propósito |
| --- | --- | --- | --- | --- |
| **C5** | RUTA_C | ¿Cuál es el reto técnico principal que enfrentan actualmente? | textarea | Identificar dificultad técnica. |
| **C6** | RUTA_C | ¿Requieren que el equipo trabaje bajo metodologías ágiles (Scrum/Kanban)? | choice | Alineación operativa. |

**Opciones para C6:**

* `AGILE_FULL` (Sí, con ceremonias y reporte diario).
* `MIXTO` (Reportes semanales, gestión flexible).
* `SIN_PREFERENCIA` (Nos adaptamos a su forma de trabajo).

---

### 3. Optimización de Opciones Existentes (Q1 y Q2)

Para mejorar la segmentación inicial sin cambiar la lógica:

* **En Q1 (Conocimiento):** Podríamos añadir una opción para detectar si es un **re-desarrollo**:
* `RE-ENGINEERING`: "Ya tengo un software pero es lento/obsoleto y quiero rehacerlo." -> `next_route_map: C` (esto los envía a la ruta técnica para analizar el legado).



---

### Estructura de Inserts Sugeridos

Puedes ejecutar estos INSERTS para nutrir tu base de datos actual sin romper nada:

```sql
-- Nuevas preguntas para Negocio
INSERT INTO `lead_survey_questions` (`codigo_pregunta`, `route`, `question_text`, `question_type`, `orden`) VALUES
('A5', 'RUTA_A', '¿Cuál es su ventana de tiempo ideal para el lanzamiento?', 'choice', 5),
('A6', 'RUTA_A', 'Describa brevemente el modelo de monetización o retorno esperado.', 'textarea', 6);

-- Opciones para la ventana de tiempo (A5)
INSERT INTO `lead_question_options` (`id_question`, `option_label`, `option_value`, `next_route_map`) VALUES
((SELECT id_question FROM lead_survey_questions WHERE codigo_pregunta='A5'), 'Crítico (Menos de 2 meses)', 'URGENTE', 'CONTACT'),
((SELECT id_question FROM lead_survey_questions WHERE codigo_pregunta='A5'), 'Planificado (3 a 6 meses)', 'ESTANDAR', 'CONTACT');

-- Nuevas preguntas para Técnicos
INSERT INTO `lead_survey_questions` (`codigo_pregunta`, `route`, `question_text`, `question_type`, `orden`) VALUES
('C5', 'RUTA_C', '¿En qué entorno de nube prefieren trabajar? (AWS, Azure, GCP, On-premise)', 'text', 10);

```

---

### Beneficio de esta mejora

Al añadir preguntas de tipo `textarea` (como la A6 o C5), obtienes **contexto semántico**. Esto te permite, en el futuro, pasar esas respuestas por una IA para etiquetar automáticamente si el lead es "Caliente", "Tibio" o "Frío" antes de que un humano lo lea.

¿Te gustaría que redacte los `INSERT` completos para todas las opciones que mencioné para que solo tengas que copiarlos y pegarlos?
