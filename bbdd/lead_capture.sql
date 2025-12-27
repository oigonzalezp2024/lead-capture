-- TABLAS --

CREATE TABLE `lead_prospects` (
  `id_prospect` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `full_name` varchar(255) DEFAULT NULL,
  `email_whatsapp` varchar(255) DEFAULT NULL,
  `profile_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `lead_question_options` (
  `id_option` int(11) NOT NULL,
  `id_question` int(11) NOT NULL,
  `option_label` text NOT NULL,
  `option_value` varchar(50) NOT NULL,
  `next_route_map` varchar(10) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `lead_survey_answers` (
  `id_answer` int(11) NOT NULL,
  `id_prospect` int(11) NOT NULL,
  `question_key` varchar(10) NOT NULL,
  `answer_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `lead_survey_questions` (
  `id_question` int(11) NOT NULL,
  `codigo_pregunta` varchar(10) NOT NULL,
  `route` varchar(50) NOT NULL DEFAULT 'COMMON',
  `question_text` text NOT NULL,
  `question_type` enum('choice','text','textarea') NOT NULL,
  `orden` int(11) NOT NULL,
  `visible` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `lead_system_users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- INSERTS --

-- 1. PREGUNTAS (lead_survey_questions)
-- Se definen IDs fijos para garantizar que las opciones apunten al padre correcto.

INSERT INTO `lead_survey_questions` (`id_question`, `codigo_pregunta`, `route`, `question_text`, `question_type`, `orden`, `visible`) VALUES
(1,  'Q1', 'COMMON', '¿Cuál es tu rol en este proyecto?', 'choice', 1, 1),
(2,  'Q2', 'COMMON', '¿Cómo describirías tu conocimiento sobre el proyecto técnico?', 'choice', 2, 1),
-- Ruta A (Negocio)
(3,  'A1', 'RUTA_A', '¿Qué proceso quieres automatizar o mejorar?', 'textarea', 3, 1),
(4,  'A2', 'RUTA_A', 'Si hoy tuviéramos el software listo, ¿cuál sería el mayor beneficio para tu empresa?', 'textarea', 4, 1),
(5,  'A3', 'RUTA_A', '¿Tienes alguna referencia de una App o Web que te guste?', 'text', 5, 1),
(6,  'A4', 'RUTA_A', '¿Prefieres que nosotros nos encarguemos de toda la parte técnica (servidores, seguridad)?', 'choice', 6, 1),
(7,  'A5', 'RUTA_A', '¿Cuál es su ventana de tiempo ideal para el lanzamiento?', 'choice', 7, 1),
(8,  'A6', 'RUTA_A', '¿Cuentas con un presupuesto estimado para esta etapa?', 'choice', 8, 1),
(9,  'A7', 'RUTA_A', 'Describa brevemente el modelo de monetización o retorno esperado.', 'textarea', 9, 1),
-- Ruta C (Técnica)
(10, 'C1', 'RUTA_C', '¿Qué stack tecnológico tienen en mente o qué infraestructura ya utilizan?', 'text', 10, 1),
(11, 'C2', 'RUTA_C', '¿Tienen ya levantamiento de requerimientos o diseño en Figma?', 'choice', 11, 1),
(12, 'C3', 'RUTA_C', '¿Qué integraciones críticas (APIs, pasarelas, ERPs) debemos considerar?', 'textarea', 12, 1),
(13, 'C4', 'RUTA_C', '¿Cuál es el reto técnico principal que enfrentan actualmente?', 'textarea', 13, 1),
(14, 'C5', 'RUTA_C', '¿Requieren que el equipo trabaje bajo metodologías ágiles (Scrum)?', 'choice', 14, 1),
-- Cierre
(15, 'F1', 'COMMON', 'Nombre y Empresa.', 'text', 15, 1),
(16, 'F2', 'COMMON', 'WhatsApp / Email.', 'text', 16, 1);

-- 2. OPCIONES (lead_question_options)
-- Cada id_option es único y su id_question corresponde exactamente a la tabla anterior.

INSERT INTO `lead_question_options` (`id_option`, `id_question`, `option_label`, `option_value`, `next_route_map`, `visible`) VALUES
-- Opciones para Q1 (ID 1)
(1, 1, 'Soy dueño de negocio/emprendedor.', 'CEO_FOUNDER', 'RUTA_A', 1),
(2, 1, 'Soy líder técnico o ingeniero.', 'CTO_ENG', 'RUTA_C', 1),

-- Opciones para Q2 (ID 2)
(3, 2, 'Tengo una idea de negocio pero no sé de programación.', 'NO_TECNICO', 'RUTA_A', 1),
(4, 2, 'Tengo claros los procesos, pero necesito el desarrollo.', 'INTERMEDIO', 'RUTA_A', 1),
(5, 2, 'Soy técnico/ingeniero y busco un equipo externo.', 'TECNICO_SENIOR', 'RUTA_C', 1),
(6, 2, 'Ya tengo un software pero es obsoleto y quiero rehacerlo.', 'RE-ENGINEERING', 'RUTA_C', 1),

-- Opciones para A4 (ID 6)
(7, 6, 'Sí, delegación técnica total.', 'FULL_DELEGATE', 'RUTA_A', 1),
(8, 6, 'No, cuento con equipo técnico propio.', 'INTERNAL_TEAM', 'RUTA_C', 1),

-- Opciones para A5 (ID 7)
(9, 7, 'INMEDIATO (Menos de 3 meses).', 'SHORT_TERM', 'RUTA_A', 1),
(10, 7, 'MEDIANO (3 a 6 meses).', 'MID_TERM', 'RUTA_A', 1),
(11, 7, 'EXPLORANDO (Solo cotizando).', 'RESEARCH', 'RUTA_A', 1),

-- Opciones para A6 (ID 8)
(12, 8, 'STARTUP (MVP económico).', 'BUDGET_STARTUP', 'RUTA_A', 1),
(13, 8, 'CORPORATIVO (Calidad y escalabilidad).', 'BUDGET_CORP', 'RUTA_A', 1),
(14, 8, 'PENDIENTE (Necesito asesoría).', 'BUDGET_CONSULT', 'RUTA_A', 1),

-- Opciones para C2 (ID 11)
(15, 11, 'Proyecto cerrado (Llave en mano).', 'FIXED_PROJECT', 'RUTA_C', 1),
(16, 11, 'Aumento de equipo (Staff Augmentation).', 'STAFF_AUG', 'RUTA_C', 1),

-- Opciones para C5 (ID 14)
(17, 14, 'Sí (Ceremonias Scrum diarias).', 'AGILE_STRICT', 'F1', 1),
(18, 14, 'No, preferimos gestión por hitos.', 'MILESTONES', 'F1', 1);

-- LLAVES Y RELACIONES -- 

ALTER TABLE `lead_prospects`
  ADD PRIMARY KEY (`id_prospect`);

ALTER TABLE `lead_question_options`
  ADD PRIMARY KEY (`id_option`),
  ADD KEY `fk_question` (`id_question`);

ALTER TABLE `lead_survey_answers`
  ADD PRIMARY KEY (`id_answer`),
  ADD KEY `fk_prospect` (`id_prospect`);

ALTER TABLE `lead_survey_questions`
  ADD PRIMARY KEY (`id_question`),
  ADD UNIQUE KEY `codigo_pregunta` (`codigo_pregunta`);

ALTER TABLE `lead_system_users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `lead_prospects`
  MODIFY `id_prospect` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `lead_question_options`
  MODIFY `id_option` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

ALTER TABLE `lead_survey_answers`
  MODIFY `id_answer` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `lead_survey_questions`
  MODIFY `id_question` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

ALTER TABLE `lead_system_users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `lead_question_options`
  ADD CONSTRAINT `fk_lead_question_options` FOREIGN KEY (`id_question`) REFERENCES `lead_survey_questions` (`id_question`) ON DELETE CASCADE;

ALTER TABLE `lead_survey_answers`
  ADD CONSTRAINT `fk_lead_survey_answers` FOREIGN KEY (`id_prospect`) REFERENCES `lead_prospects` (`id_prospect`) ON DELETE CASCADE;
