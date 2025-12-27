-- TABLAS --

CREATE TABLE `prospects` (
  `id_prospect` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `full_name` varchar(255) DEFAULT NULL,
  `email_whatsapp` varchar(255) DEFAULT NULL,
  `profile_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `question_options` (
  `id_option` int(11) NOT NULL,
  `id_question` int(11) NOT NULL,
  `option_label` text NOT NULL,
  `option_value` varchar(50) NOT NULL,
  `next_route_map` varchar(10) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `survey_answers` (
  `id_answer` int(11) NOT NULL,
  `id_prospect` int(11) NOT NULL,
  `question_key` varchar(10) NOT NULL,
  `answer_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `survey_questions` (
  `id_question` int(11) NOT NULL,
  `codigo_pregunta` varchar(10) NOT NULL,
  `route` varchar(50) NOT NULL DEFAULT 'COMMON',
  `question_text` text NOT NULL,
  `question_type` enum('choice','text','textarea') NOT NULL,
  `orden` int(11) NOT NULL,
  `visible` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `system_users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- INSERTS --

INSERT INTO `survey_questions` (`id_question`, `codigo_pregunta`, `route`, `question_text`, `question_type`, `orden`, `visible`) VALUES
(1, 'Q1', 'COMMON', '¿Cómo describirías tu conocimiento sobre el proyecto técnico?', 'choice', 1, 1);

INSERT INTO `question_options` (`id_option`, `id_question`, `option_label`, `option_value`, `next_route_map`, `visible`) VALUES
(1, 1, 'Tengo una idea de negocio pero no sé de programación.', 'NO_TECNICO', 'A', 1),
(2, 1, 'Tengo claros los procesos, pero necesito el desarrollo.', 'INTERMEDIO', 'A', 1),
(3, 1, 'Soy técnico/ingeniero y busco un equipo de desarrollo externo.', 'TECNICO_SENIOR', 'C', 1);

INSERT INTO `survey_questions` (`id_question`, `codigo_pregunta`, `route`, `question_text`, `question_type`, `orden`) VALUES
(2, 'Q2', 'COMMON', '¿Cuál es tu rol en este proyecto?', 'choice', 1);

INSERT INTO `question_options` (`id_option`, `id_question`, `option_label`, `option_value`, `next_route_map`) VALUES
(4, 2, 'Soy dueño de negocio/emprendedor y busco que alguien haga realidad mi idea.', 'NO_TECNICO', 'RUTA_A'),
(5, 2, 'Soy líder técnico o ingeniero y busco un equipo de desarrollo externo.', 'TECNICO_SENIOR', 'RUTA_C');

INSERT INTO `survey_questions` (`id_question`, `codigo_pregunta`, `route`, `question_text`, `question_type`, `orden`) VALUES
(3, 'A1', 'RUTA_A', '¿Qué proceso quieres automatizar o mejorar?', 'textarea', 1),
(4, 'A2', 'RUTA_A', 'Si hoy tuviéramos el software listo, ¿cuál sería el mayor beneficio para tu empresa?', 'textarea', 2),
(5, 'A3', 'RUTA_A', '¿Tienes alguna referencia de una App o Web que te guste?', 'text', 3),
(6, 'A4', 'RUTA_A', '¿Prefieres que nosotros nos encarguemos de toda la parte técnica (servidores, seguridad, mantenimiento)?', 'choice', 4);

INSERT INTO `question_options` (`id_option`, `id_question`, `option_label`, `option_value`, `next_route_map`) VALUES
(6, 6, 'Sí', 'NO_TECNICO', 'CONTACT'),
(7, 6, 'No', 'TECNICO', 'CONTACT');

INSERT INTO `survey_questions` (`id_question`, `codigo_pregunta`, `route`, `question_text`, `question_type`, `orden`) VALUES
(7, 'C1', 'RUTA_C', '¿Qué stack tecnológico tienen en mente o qué infraestructura ya utilizan?', 'text', 6);

INSERT INTO `survey_questions` (`id_question`, `codigo_pregunta`, `route`, `question_text`, `question_type`, `orden`) VALUES
(8, 'C2', 'RUTA_C', '¿Tienen ya levantamiento de requerimientos, historias de usuario o diseño en Figma?', 'choice', 7);

INSERT INTO `question_options` (`id_option`, `id_question`, `option_label`, `option_value`, `next_route_map`) VALUES
(8, 8, 'Proyecto cerrado (Llave en mano)', 'LLAVE_MANO', NULL),
(9, 8, 'Aumento de equipo (Staff Augmentation)', 'STAFF_AUG', NULL);

INSERT INTO `survey_questions` (`id_question`, `codigo_pregunta`, `route`, `question_text`, `question_type`, `orden`) VALUES
(9, 'C4', 'RUTA_C', '¿Qué integraciones críticas (APIs, pasarelas, ERPs) debemos considerar?', 'textarea', 9);

INSERT INTO `survey_questions` (`id_question`, `codigo_pregunta`, `route`, `question_text`, `question_type`, `orden`, `visible`) VALUES
(10, 'F1', 'COMMON', 'Nombre y Empresa.', 'text', 10, 1),
(11, 'F2', 'COMMON', 'WhatsApp / Email.', 'text', 11, 1);

-- LLAVES Y RELACIONES -- 

ALTER TABLE `prospects`
  ADD PRIMARY KEY (`id_prospect`);

ALTER TABLE `question_options`
  ADD PRIMARY KEY (`id_option`),
  ADD KEY `fk_question` (`id_question`);

ALTER TABLE `survey_answers`
  ADD PRIMARY KEY (`id_answer`),
  ADD KEY `fk_prospect` (`id_prospect`);

ALTER TABLE `survey_questions`
  ADD PRIMARY KEY (`id_question`),
  ADD UNIQUE KEY `codigo_pregunta` (`codigo_pregunta`);

ALTER TABLE `system_users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `prospects`
  MODIFY `id_prospect` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `question_options`
  MODIFY `id_option` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `survey_answers`
  MODIFY `id_answer` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `survey_questions`
  MODIFY `id_question` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `system_users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `question_options`
  ADD CONSTRAINT `fk_question` FOREIGN KEY (`id_question`) REFERENCES `survey_questions` (`id_question`) ON DELETE CASCADE;

ALTER TABLE `survey_answers`
  ADD CONSTRAINT `fk_prospect` FOREIGN KEY (`id_prospect`) REFERENCES `prospects` (`id_prospect`) ON DELETE CASCADE;
