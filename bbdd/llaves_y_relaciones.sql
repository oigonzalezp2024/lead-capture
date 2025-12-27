
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
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT;

ALTER TABLE `lead_question_options`
  ADD CONSTRAINT `fk_lead_question_options` FOREIGN KEY (`id_question`) REFERENCES `lead_survey_questions` (`id_question`) ON DELETE CASCADE;

ALTER TABLE `lead_survey_answers`
  ADD CONSTRAINT `fk_lead_survey_answers` FOREIGN KEY (`id_prospect`) REFERENCES `lead_prospects` (`id_prospect`) ON DELETE CASCADE;
