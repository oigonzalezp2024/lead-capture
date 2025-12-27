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
  MODIFY `id_option` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `survey_answers`
  MODIFY `id_answer` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `survey_questions`
  MODIFY `id_question` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `system_users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `question_options`
  ADD CONSTRAINT `fk_question` FOREIGN KEY (`id_question`) REFERENCES `survey_questions` (`id_question`) ON DELETE CASCADE;

ALTER TABLE `survey_answers`
  ADD CONSTRAINT `fk_prospect` FOREIGN KEY (`id_prospect`) REFERENCES `prospects` (`id_prospect`) ON DELETE CASCADE;
