
INSERT INTO `teacher` (`available`, `id`, `name`, `prefix`, `suffix`, `surname`) VALUES (b'1', '1', 'Libor',        'Ing.', '', 'Bajer');
INSERT INTO `teacher` (`available`, `id`, `name`, `prefix`, `suffix`, `surname`) VALUES (b'1', '2', 'František',    'Mgr.', '', 'Věcek');
INSERT INTO `teacher` (`available`, `id`, `name`, `prefix`, `suffix`, `surname`) VALUES (b'1', '3', 'Lea',          'Mgr.', '', 'Bednaříková');
INSERT INTO `teacher` (`available`, `id`, `name`, `prefix`, `suffix`, `surname`) VALUES (b'1', '4', 'Jana',         'Mgr.', '', 'Binarová');

INSERT INTO `subject` (`available`, `id`, `abbreviation`, `name`) VALUES (b'1', '1', 'M', 'Matematika');
INSERT INTO `subject` (`available`, `id`, `abbreviation`, `name`) VALUES (b'1', '2', 'PG', 'Programování');
INSERT INTO `subject` (`available`, `id`, `abbreviation`, `name`) VALUES (b'1', '3', 'ČJ', 'Český jazyk');

INSERT INTO `_class` (`id`, `available`, `label`, `year`, `teacher_id`) VALUES ('1', b'1', 'D', '2021', NULL);

INSERT INTO `student` (`available`, `_class_id`, `id`, `name`, `surname`) VALUES (b'1', '1', '1', 'Vojtěch', 'Fošnár');
INSERT INTO `student` (`available`, `_class_id`, `id`, `name`, `surname`) VALUES (b'1', NULL, '2', 'Andrej', 'Novák');
INSERT INTO `student` (`available`, `_class_id`, `id`, `name`, `surname`) VALUES (b'1', NULL, '3', 'Vítek', 'Vávra');

INSERT INTO `classroom` (`id`, `available`, `label`) VALUES ('1', b'1', 'BAZ');

INSERT INTO `exam` (`id`, `available`, `final_mark`, `original_mark`, `time`, `chairman_id`, `class_teacher_id`, `classroom_id`, `examiner_id`, `student_id`, `subject_id`) VALUES ('1', b'1', NULL, 'N', '2001-09-11 08:46:00.000000', NULL, NULL, NULL, '1', '1', '2');
INSERT INTO `exam` (`id`, `available`, `final_mark`, `original_mark`, `time`, `chairman_id`, `class_teacher_id`, `classroom_id`, `examiner_id`, `student_id`, `subject_id`) VALUES ('2', b'1', NULL, '5', NULL, NULL, NULL, '1', '3', '2', '3');
INSERT INTO `exam` (`id`, `available`, `final_mark`, `original_mark`, `time`, `chairman_id`, `class_teacher_id`, `classroom_id`, `examiner_id`, `student_id`, `subject_id`) VALUES ('3', b'1', NULL, '5', NULL, NULL, NULL, NULL, '4', '3', '1');
