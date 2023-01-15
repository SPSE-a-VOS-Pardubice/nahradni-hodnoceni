-- příznaky
INSERT INTO `Traits` (`name`) VALUES ('Visual Studio');
INSERT INTO `Traits` (`name`) VALUES ('Adobe Software');
-- učebny
INSERT INTO `Classrooms` (`label`) VALUES ('D253');
INSERT INTO `Classrooms` (`label`) VALUES ('D353');
INSERT INTO `Classrooms` (`label`) VALUES ('C317');
INSERT INTO `Classrooms` (`label`) VALUES ('A103');
-- předměty
INSERT INTO `Subjects` (`name`, `abbreviation`) VALUES ('Programování', 'PG');
INSERT INTO `Subjects` (`name`, `abbreviation`) VALUES ('Informatika', 'IN');
-- učitelé
INSERT INTO `Teachers` (`name`, `surname`, `prefix`, `suffix`) VALUES ('Libor', 'Bajer','','');
INSERT INTO `Teachers` (`name`, `surname`, `prefix`, `suffix`) VALUES ('Zdeněk', 'Šilar','','');
INSERT INTO `Teachers` (`name`, `surname`, `prefix`, `suffix`) VALUES ('Tereza', 'Truncová','','');
INSERT INTO `Teachers` (`name`, `surname`, `prefix`, `suffix`) VALUES ('Zdeňka', 'Sobolová','','');
INSERT INTO `Teachers` (`name`, `surname`, `prefix`, `suffix`) VALUES ('Ladislav', 'Štěpánek','Ing.','MBA');
-- třídy
INSERT INTO `Classes` (`year`, `grade`, `label`, `class_teacher_id`) VALUES (2023, '3', 'D', 4);
INSERT INTO `Classes` (`year`, `grade`, `label`, `class_teacher_id`) VALUES (2023, '4', 'E', 1);
-- studenti
INSERT INTO `Students` (`name`, `surname`, `class_id`) VALUES ('Jan', 'Rozek', 1);
INSERT INTO `Students` (`name`, `surname`, `class_id`) VALUES ('Jan', 'Horák', 1);
INSERT INTO `Students` (`name`, `surname`, `class_id`) VALUES ('Jan', 'Kolář', 1);
INSERT INTO `Students` (`name`, `surname`, `class_id`) VALUES ('Jan', 'Sirůček', 1);
-- příznaky učeben
INSERT INTO `ClassroomsTraits` (`trait_id`, `classroom_id`) VALUES (1, 1);
INSERT INTO `ClassroomsTraits` (`trait_id`, `classroom_id`) VALUES (1, 2);
INSERT INTO `ClassroomsTraits` (`trait_id`, `classroom_id`) VALUES (1, 3);
INSERT INTO `ClassroomsTraits` (`trait_id`, `classroom_id`) VALUES (2, 3);
INSERT INTO `ClassroomsTraits` (`trait_id`, `classroom_id`) VALUES (2, 4);
-- příznaky předmětu
INSERT INTO `SubjectsTraits` (`trait_id`, `subject_id`) VALUES (1, 1);
INSERT INTO `SubjectsTraits` (`trait_id`, `subject_id`) VALUES (2, 2);
-- vhodnost učitelů
INSERT INTO `TeachersSuitability` (`subject_id`, `teacher_id`) VALUES (1, 1);
INSERT INTO `TeachersSuitability` (`subject_id`, `teacher_id`) VALUES (1, 2);
INSERT INTO `TeachersSuitability` (`subject_id`, `teacher_id`) VALUES (2, 3);
-- zkoušky
INSERT INTO `Exams` (`student_id`, `subject_id`, `classroom_id`, `original_mark`, `final_mark`, `time`) VALUES (1, 2, 3, 'N', '4', '2023-08-25 09:00:00');
-- učitelé u zkoušek
INSERT INTO `ExamsTeachers` (`exam_id`, `teacher_id`, `Role`) VALUES (1, '3','Zkoučející');
