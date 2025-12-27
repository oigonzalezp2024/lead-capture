INSERT INTO `lead_question_options` (`id_option`, `id_question`, `option_label`, `option_value`, `next_route_map`, `visible`) VALUES
(1, 1, 'Tengo una idea de negocio pero no sé de programación.', 'NO_TECNICO', 'A', 1),
(2, 1, 'Tengo claros los procesos, pero necesito el desarrollo.', 'INTERMEDIO', 'A', 1),
(3, 1, 'Soy técnico/ingeniero y busco un equipo de desarrollo externo.', 'TECNICO_SENIOR', 'C', 1);

INSERT INTO `lead_survey_questions` (`id_question`, `codigo_pregunta`, `route`, `question_text`, `question_type`, `orden`, `visible`) VALUES
(1, 'Q1', 'COMMON', '¿Cómo describirías tu conocimiento sobre el proyecto técnico?', 'choice', 1, 1),
(2, 'A1', 'A', '¿Qué problema principal quieres resolver con este software?', 'textarea', 2, 1),
(3, 'A2', 'A', 'Si tuvieras la solución hoy, ¿cómo cambiaría tu día a día?', 'textarea', 3, 1),
(4, 'A3', 'A', '¿Tienes alguna referencia de una App o Web que te guste?', 'text', 4, 1),
(5, 'A4', 'A', '¿Cuál es tu prioridad?', 'text', 5, 1),
(6, 'C1', 'C', '¿Cuál es el Stack tecnológico preferido o actual?', 'text', 6, 1),
(7, 'C2', 'C', '¿Cuentas con documentación técnica o diagramas de arquitectura?', 'text', 7, 1),
(8, 'C3', 'C', '¿Qué tipo de servicios buscas?', 'text', 8, 1),
(9, 'C4', 'C', '¿Cuál es el nivel de urgencia para el despliegue/deploy?', 'text', 9, 1),
(10, 'F1', 'COMMON', 'Nombre y Empresa.', 'text', 10, 1),
(11, 'F2', 'COMMON', 'WhatsApp / Email.', 'text', 11, 1);
