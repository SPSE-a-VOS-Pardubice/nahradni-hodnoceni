-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema nahradni_hodnoceni
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema nahradni_hodnoceni
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `nahradni_hodnoceni` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci;
USE `nahradni_hodnoceni` ;

-- -----------------------------------------------------
-- Table `nahradni_hodnoceni`.`Teachers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `nahradni_hodnoceni`.`Teachers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `surname` VARCHAR(45) NOT NULL,
  `prefix` VARCHAR(45) NOT NULL,
  `suffix` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `nahradni_hodnoceni`.`Classes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `nahradni_hodnoceni`.`Classes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `year` INT UNSIGNED NOT NULL,
  `grade` INT NOT NULL,
  `label` VARCHAR(45) NOT NULL,
  `class_teacher_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_Třídy_Učitelé2_idx` (`class_teacher_id` ASC),
  CONSTRAINT `fk_Třídy_Učitelé2`
    FOREIGN KEY (`class_teacher_id`)
    REFERENCES `nahradni_hodnoceni`.`Teachers` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `nahradni_hodnoceni`.`Students`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `nahradni_hodnoceni`.`Students` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `surname` VARCHAR(45) NOT NULL,
  `class_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_Studenti_Třídy1_idx` (`class_id` ASC),
  CONSTRAINT `fk_Studenti_Třídy1`
    FOREIGN KEY (`class_id`)
    REFERENCES `nahradni_hodnoceni`.`Classes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `nahradni_hodnoceni`.`Classrooms`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `nahradni_hodnoceni`.`Classrooms` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(5) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `oznaceni_UNIQUE` (`label` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `nahradni_hodnoceni`.`Subjects`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `nahradni_hodnoceni`.`Subjects` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `abbreviation` VARCHAR(3) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `nahradni_hodnoceni`.`Exams`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `nahradni_hodnoceni`.`Exams` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `student_id` INT NOT NULL,
  `subject_id` INT NOT NULL,
  `classroom_id` INT NULL,
  `original_mark` VARCHAR(45) NOT NULL,
  `final_mark` VARCHAR(45) NULL,
  `time` DATETIME NULL,
  `chairman_id` INT NULL,
  `class_teacher_id` INT NULL,
  `examiner_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_Zkousky_Studenti1_idx` (`student_id` ASC),
  INDEX `fk_Zkousky_Ucebny1_idx` (`classroom_id` ASC),
  INDEX `fk_Zkousky_Predmety1_idx` (`subject_id` ASC),
  INDEX `fk_Exams_Teachers1_idx` (`chairman_id` ASC),
  INDEX `fk_Exams_Teachers2_idx` (`class_teacher_id` ASC),
  INDEX `fk_Exams_Teachers3_idx` (`examiner_id` ASC),
  CONSTRAINT `fk_Zkousky_Studenti1`
    FOREIGN KEY (`student_id`)
    REFERENCES `nahradni_hodnoceni`.`Students` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Zkousky_Ucebny1`
    FOREIGN KEY (`classroom_id`)
    REFERENCES `nahradni_hodnoceni`.`Classrooms` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Zkousky_Predmety1`
    FOREIGN KEY (`subject_id`)
    REFERENCES `nahradni_hodnoceni`.`Subjects` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Exams_Teachers1`
    FOREIGN KEY (`chairman_id`)
    REFERENCES `nahradni_hodnoceni`.`Teachers` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Exams_Teachers2`
    FOREIGN KEY (`class_teacher_id`)
    REFERENCES `nahradni_hodnoceni`.`Teachers` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Exams_Teachers3`
    FOREIGN KEY (`examiner_id`)
    REFERENCES `nahradni_hodnoceni`.`Teachers` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `nahradni_hodnoceni`.`Traits`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `nahradni_hodnoceni`.`Traits` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `nazev_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `nahradni_hodnoceni`.`SubjectsTraits`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `nahradni_hodnoceni`.`SubjectsTraits` (
  `trait_id` INT NOT NULL,
  `subject_id` INT NOT NULL,
  PRIMARY KEY (`trait_id`, `subject_id`),
  INDEX `fk_Priznaky_has_Predmety_Predmety1_idx` (`subject_id` ASC),
  INDEX `fk_Priznaky_has_Predmety_Priznaky1_idx` (`trait_id` ASC),
  CONSTRAINT `fk_Priznaky_has_Predmety_Priznaky1`
    FOREIGN KEY (`trait_id`)
    REFERENCES `nahradni_hodnoceni`.`Traits` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Priznaky_has_Predmety_Predmety1`
    FOREIGN KEY (`subject_id`)
    REFERENCES `nahradni_hodnoceni`.`Subjects` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `nahradni_hodnoceni`.`ClassroomsTraits`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `nahradni_hodnoceni`.`ClassroomsTraits` (
  `trait_id` INT NOT NULL AUTO_INCREMENT,
  `classroom_id` INT NOT NULL,
  PRIMARY KEY (`trait_id`, `classroom_id`),
  INDEX `fk_Priznaky_has_Ucebny_Ucebny1_idx` (`classroom_id` ASC),
  INDEX `fk_Priznaky_has_Ucebny_Priznaky1_idx` (`trait_id` ASC),
  CONSTRAINT `fk_Priznaky_has_Ucebny_Priznaky1`
    FOREIGN KEY (`trait_id`)
    REFERENCES `nahradni_hodnoceni`.`Traits` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Priznaky_has_Ucebny_Ucebny1`
    FOREIGN KEY (`classroom_id`)
    REFERENCES `nahradni_hodnoceni`.`Classrooms` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `nahradni_hodnoceni`.`TeachersSuitability`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `nahradni_hodnoceni`.`TeachersSuitability` (
  `subject_id` INT NOT NULL,
  `teacher_id` INT NOT NULL,
  PRIMARY KEY (`subject_id`, `teacher_id`),
  INDEX `fk_Predmety_has_Ucitele_Ucitele1_idx` (`teacher_id` ASC),
  INDEX `fk_Predmety_has_Ucitele_Predmety1_idx` (`subject_id` ASC),
  CONSTRAINT `fk_Predmety_has_Ucitele_Predmety1`
    FOREIGN KEY (`subject_id`)
    REFERENCES `nahradni_hodnoceni`.`Subjects` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Predmety_has_Ucitele_Ucitele1`
    FOREIGN KEY (`teacher_id`)
    REFERENCES `nahradni_hodnoceni`.`Teachers` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
