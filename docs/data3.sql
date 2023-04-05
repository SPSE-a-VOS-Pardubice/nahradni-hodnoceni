-- příznaky
INSERT INTO `Traits` (`name`) VALUES ('Adobe Software');
INSERT INTO `Traits` (`name`) VALUES ('Počítače');
INSERT INTO `Traits` (`name`) VALUES ('Tělocvična');
INSERT INTO `Traits` (`name`) VALUES ('Učebna Servisu');
INSERT INTO `Traits` (`name`) VALUES ('Visual Studio');
INSERT INTO `Traits` (`name`) VALUES ('Visual Studio Code');
-- učebny
INSERT INTO `Classrooms` (`label`) VALUES ('A000');
INSERT INTO `Classrooms` (`label`) VALUES ('B069');
INSERT INTO `Classrooms` (`label`) VALUES ('C128');
INSERT INTO `Classrooms` (`label`) VALUES ('D420');
-- předměty
INSERT INTO `Subjects` (`name`, `abbreviation`) VALUES ('Číslicová Technika',       'CT');
INSERT INTO `Subjects` (`name`, `abbreviation`) VALUES ('Servis PC',                'SR');
INSERT INTO `Subjects` (`name`, `abbreviation`) VALUES ('Webové Aplikace',          'WA');
INSERT INTO `Subjects` (`name`, `abbreviation`) VALUES ('Programování',             'PG');
INSERT INTO `Subjects` (`name`, `abbreviation`) VALUES ('Technická dokumentace',    'T');
INSERT INTO `Subjects` (`name`, `abbreviation`) VALUES ('Anglický jazyk',           'AJ');
INSERT INTO `Subjects` (`name`, `abbreviation`) VALUES ('Fyzika',                   'FY');
INSERT INTO `Subjects` (`name`, `abbreviation`) VALUES ('Matematika',               'MA');
-- učitelé
INSERT INTO `Teachers` (`name`, `surname`, `prefix`, `suffix`) VALUES ('Miroslav',  'Zapletal',     'Ing.',     '');
INSERT INTO `Teachers` (`name`, `surname`, `prefix`, `suffix`) VALUES ('Petr',      'Šeda',         'PaedDr.',  '');
INSERT INTO `Teachers` (`name`, `surname`, `prefix`, `suffix`) VALUES ('Jana',      'Reslová',      'RNDr.',    '');
INSERT INTO `Teachers` (`name`, `surname`, `prefix`, `suffix`) VALUES ('Libor',     'Bajer',        'Ing.',     '');
INSERT INTO `Teachers` (`name`, `surname`, `prefix`, `suffix`) VALUES ('Jiří',      'Rudolf',       'Ing.',     '');
INSERT INTO `Teachers` (`name`, `surname`, `prefix`, `suffix`) VALUES ('Česlav',    'Kverek',       'Mgr.',     '');
INSERT INTO `Teachers` (`name`, `surname`, `prefix`, `suffix`) VALUES ('Alena',     'Kvasničková',  'Mgr.',     '');
INSERT INTO `Teachers` (`name`, `surname`, `prefix`, `suffix`) VALUES ('Ladislav',  'Štěpánek',     'Ing.',     'MBA');
-- příznaky učeben
INSERT INTO `ClassroomsTraits` (`trait_id`, `classroom_id`) VALUES (1, 1);
INSERT INTO `ClassroomsTraits` (`trait_id`, `classroom_id`) VALUES (1, 2);
INSERT INTO `ClassroomsTraits` (`trait_id`, `classroom_id`) VALUES (2, 2);
INSERT INTO `ClassroomsTraits` (`trait_id`, `classroom_id`) VALUES (1, 3);
INSERT INTO `ClassroomsTraits` (`trait_id`, `classroom_id`) VALUES (2, 3);
INSERT INTO `ClassroomsTraits` (`trait_id`, `classroom_id`) VALUES (3, 3);
INSERT INTO `ClassroomsTraits` (`trait_id`, `classroom_id`) VALUES (1, 4);
INSERT INTO `ClassroomsTraits` (`trait_id`, `classroom_id`) VALUES (2, 4);
INSERT INTO `ClassroomsTraits` (`trait_id`, `classroom_id`) VALUES (3, 4);
INSERT INTO `ClassroomsTraits` (`trait_id`, `classroom_id`) VALUES (4, 4);
-- příznaky předmětu
INSERT INTO `SubjectsTraits` (`trait_id`, `subject_id`) VALUES (2, 3);
INSERT INTO `SubjectsTraits` (`trait_id`, `subject_id`) VALUES (2, 4);

INSERT INTO `SubjectsTraits` (`trait_id`, `subject_id`) VALUES (4, 2);

INSERT INTO `SubjectsTraits` (`trait_id`, `subject_id`) VALUES (5, 4);

INSERT INTO `SubjectsTraits` (`trait_id`, `subject_id`) VALUES (6, 3);
INSERT INTO `SubjectsTraits` (`trait_id`, `subject_id`) VALUES (6, 4);
-- vhodnost učitelů
INSERT INTO `TeacherSuitabilities` (`subject_id`, `teacher_id`) VALUES (1, 1);

INSERT INTO `TeacherSuitabilities` (`subject_id`, `teacher_id`) VALUES (2, 2);

INSERT INTO `TeacherSuitabilities` (`subject_id`, `teacher_id`) VALUES (3, 3);

INSERT INTO `TeacherSuitabilities` (`subject_id`, `teacher_id`) VALUES (4, 3);
INSERT INTO `TeacherSuitabilities` (`subject_id`, `teacher_id`) VALUES (4, 4);

INSERT INTO `TeacherSuitabilities` (`subject_id`, `teacher_id`) VALUES (5, 1);

INSERT INTO `TeacherSuitabilities` (`subject_id`, `teacher_id`) VALUES (6, 6);

INSERT INTO `TeacherSuitabilities` (`subject_id`, `teacher_id`) VALUES (7, 7);
INSERT INTO `TeacherSuitabilities` (`subject_id`, `teacher_id`) VALUES (7, 8);
