-- příznaky
INSERT INTO `priznaky`(`id`, `nazev`) VALUES (0, 'Visual Studio');
INSERT INTO `priznaky`(`id`, `nazev`) VALUES (0, 'Adobe Software');
-- učebny
INSERT INTO `ucebny`(`id`, `oznaceni`) VALUES (0, 'D253');
INSERT INTO `ucebny`(`id`, `oznaceni`) VALUES (0, 'D353');
INSERT INTO `ucebny`(`id`, `oznaceni`) VALUES (0, 'C317');
INSERT INTO `ucebny`(`id`, `oznaceni`) VALUES (0, 'A103');
-- předměty
INSERT INTO `predmety`(`id`, `nazev`, `zkratka`) VALUES (0, 'Programování', 'PG');
INSERT INTO `predmety`(`id`, `nazev`, `zkratka`) VALUES (0, 'Informatika', 'IN');
-- učitelé
INSERT INTO `ucitele`(`id`, `jmeno`, `prijmeni`, `prefix`, `suffix`) VALUES (0 ,'Libor', 'Bajer','','');
INSERT INTO `ucitele`(`id`, `jmeno`, `prijmeni`, `prefix`, `suffix`) VALUES (0 ,'Zdeněk', 'Šilar','','');
INSERT INTO `ucitele`(`id`, `jmeno`, `prijmeni`, `prefix`, `suffix`) VALUES (0 ,'Tereza', 'Truncová','','');
INSERT INTO `ucitele`(`id`, `jmeno`, `prijmeni`, `prefix`, `suffix`) VALUES (0 ,'Zdeňka', 'Sobolová','','');
-- třídy
INSERT INTO `tridy`(`id`, `rocnik`, `oznaceni`, `tridni_ucitel_id`) VALUES (0, '3', '.D', 4);
INSERT INTO `tridy`(`id`, `rocnik`, `oznaceni`, `tridni_ucitel_id`) VALUES (0, '4', '.E', 1);
-- studenti
INSERT INTO `studenti`(`id`, `jmeno`, `prijmeni`, `trida_id`) VALUES (0 , 'Jan', 'Rozek', 1);
INSERT INTO `studenti`(`id`, `jmeno`, `prijmeni`, `trida_id`) VALUES (0 , 'Jan', 'Horák', 1);
INSERT INTO `studenti`(`id`, `jmeno`, `prijmeni`, `trida_id`) VALUES (0 , 'Jan', 'Kolář', 1);
INSERT INTO `studenti`(`id`, `jmeno`, `prijmeni`, `trida_id`) VALUES (0 , 'Jan', 'Sirůček', 1);
-- příznaky učeben 
INSERT INTO `priznakyuceben`(`priznak_id`, `ucebna_id`) VALUES (1, 1);
INSERT INTO `priznakyuceben`(`priznak_id`, `ucebna_id`) VALUES (1, 2);
INSERT INTO `priznakyuceben`(`priznak_id`, `ucebna_id`) VALUES (2, 3);
INSERT INTO `priznakyuceben`(`priznak_id`, `ucebna_id`) VALUES (2, 4);
-- příznaky předmětu 
INSERT INTO `priznakypredmetu`(`priznak_id`, `predmet_id`) VALUES (1, 1);
INSERT INTO `priznakypredmetu`(`priznak_id`, `predmet_id`) VALUES (2, 2);
-- vhodnost učitelů
INSERT INTO `vhodnostucitelu`(`predmet_id`, `ucitel_id`) VALUES (1, 1);
INSERT INTO `vhodnostucitelu`(`predmet_id`, `ucitel_id`) VALUES (1, 2);
INSERT INTO `vhodnostucitelu`(`predmet_id`, `ucitel_id`) VALUES (2, 3);
-- zkoušky
INSERT INTO `zkousky`(`id`, `student_id`, `predmet_id`, `ucebna_id`, `puvodni_znamka`, `vysledna_znamka`, `termin_konani`) VALUES (0, 1, 2, 3, 'N','4','2023-08-25 09:00:00');
-- učitelé u zkoušek
INSERT INTO `uciteleuzkousek`(`Zkousky_id`, `Ucitele_idUcitele`, `Role`) VALUES (1, '3','Zkoučející');