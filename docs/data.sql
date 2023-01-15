-- příznaky
INSERT INTO `Priznaky` (`nazev`) VALUES ('Visual Studio');
INSERT INTO `Priznaky` (`nazev`) VALUES ('Adobe Software');
-- učebny
INSERT INTO `Ucebny` (`oznaceni`) VALUES ('D253');
INSERT INTO `Ucebny` (`oznaceni`) VALUES ('D353');
INSERT INTO `Ucebny` (`oznaceni`) VALUES ('C317');
INSERT INTO `Ucebny` (`oznaceni`) VALUES ('A103');
-- předměty
INSERT INTO `Predmety` (`nazev`, `zkratka`) VALUES ('Programování', 'PG');
INSERT INTO `Predmety` (`nazev`, `zkratka`) VALUES ('Informatika', 'IN');
-- učitelé
INSERT INTO `Ucitele` (`jmeno`, `prijmeni`, `prefix`, `suffix`) VALUES ('Libor', 'Bajer','','');
INSERT INTO `Ucitele` (`jmeno`, `prijmeni`, `prefix`, `suffix`) VALUES ('Zdeněk', 'Šilar','','');
INSERT INTO `Ucitele` (`jmeno`, `prijmeni`, `prefix`, `suffix`) VALUES ('Tereza', 'Truncová','','');
INSERT INTO `Ucitele` (`jmeno`, `prijmeni`, `prefix`, `suffix`) VALUES ('Zdeňka', 'Sobolová','','');
INSERT INTO `Ucitele` (`jmeno`, `prijmeni`, `prefix`, `suffix`) VALUES ('Ladislav', 'Štěpánek','Ing.','MBA');
-- třídy
INSERT INTO `Tridy` (`rok`, `rocnik`, `oznaceni`, `tridni_ucitel_id`) VALUES (2023, '3', 'D', 4);
INSERT INTO `Tridy` (`rok`, `rocnik`, `oznaceni`, `tridni_ucitel_id`) VALUES (2023, '4', 'E', 1);
-- studenti
INSERT INTO `Studenti` (`jmeno`, `prijmeni`, `trida_id`) VALUES ('Jan', 'Rozek', 1);
INSERT INTO `Studenti` (`jmeno`, `prijmeni`, `trida_id`) VALUES ('Jan', 'Horák', 1);
INSERT INTO `Studenti` (`jmeno`, `prijmeni`, `trida_id`) VALUES ('Jan', 'Kolář', 1);
INSERT INTO `Studenti` (`jmeno`, `prijmeni`, `trida_id`) VALUES ('Jan', 'Sirůček', 1);
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
INSERT INTO `VhodnostUcitelu` (`predmet_id`, `ucitel_id`) VALUES (1, 1);
INSERT INTO `VhodnostUcitelu` (`predmet_id`, `ucitel_id`) VALUES (1, 2);
INSERT INTO `VhodnostUcitelu` (`predmet_id`, `ucitel_id`) VALUES (2, 3);
-- zkoušky
INSERT INTO `Zkousky` (`student_id`, `predmet_id`, `ucebna_id`, `puvodni_znamka`, `vysledna_znamka`, `termin_konani`) VALUES (1, 2, 3, 'N', '4', '2023-08-25 09:00:00');
-- učitelé u zkoušek
INSERT INTO `UciteleUZkousek` (`Zkousky_id`, `Ucitele_idUcitele`, `Role`) VALUES (1, '3','Zkoučející');
