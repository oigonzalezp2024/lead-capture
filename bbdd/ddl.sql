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
