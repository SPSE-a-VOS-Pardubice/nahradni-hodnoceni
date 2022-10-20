-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema zkousky
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema zkousky
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `zkousky` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci ;
USE `zkousky` ;

-- -----------------------------------------------------
-- Table `zkousky`.`Ucitele`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `zkousky`.`Ucitele` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `jmeno` VARCHAR(45) NOT NULL,
  `prijmeni` VARCHAR(45) NOT NULL,
  `prefix` VARCHAR(45) NULL,
  `suffix` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `zkousky`.`Tridy`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `zkousky`.`Tridy` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `rocnik` INT NOT NULL,
  `oznaceni` VARCHAR(45) NOT NULL,
  `tridni_ucitel_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_Třídy_Učitelé2_idx` (`tridni_ucitel_id` ASC),
  CONSTRAINT `fk_Třídy_Učitelé2`
    FOREIGN KEY (`tridni_ucitel_id`)
    REFERENCES `zkousky`.`Ucitele` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `zkousky`.`Studenti`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `zkousky`.`Studenti` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `jmeno` VARCHAR(45) NOT NULL,
  `prijmeni` VARCHAR(45) NOT NULL,
  `trida_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_Studenti_Třídy1_idx` (`trida_id` ASC),
  CONSTRAINT `fk_Studenti_Třídy1`
    FOREIGN KEY (`trida_id`)
    REFERENCES `zkousky`.`Tridy` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `zkousky`.`Ucebny`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `zkousky`.`Ucebny` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `oznaceni` VARCHAR(5) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `oznaceni_UNIQUE` (`oznaceni` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `zkousky`.`Predmety`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `zkousky`.`Predmety` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nazev` VARCHAR(45) NOT NULL,
  `zkratka` VARCHAR(3) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `zkousky`.`Zkousky`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `zkousky`.`Zkousky` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `student_id` INT NOT NULL,
  `predmet_id` INT NOT NULL,
  `ucebna_id` INT NULL,
  `puvodni_znamka` VARCHAR(45) NOT NULL,
  `vysledna_znamka` VARCHAR(45) NULL,
  `cas_konani` TIME NULL,
  `den_konani` DATE NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_Zkousky_Studenti1_idx` (`student_id` ASC),
  INDEX `fk_Zkousky_Ucebny1_idx` (`ucebna_id` ASC),
  INDEX `fk_Zkousky_Predmety1_idx` (`predmet_id` ASC),
  CONSTRAINT `fk_Zkousky_Studenti1`
    FOREIGN KEY (`student_id`)
    REFERENCES `zkousky`.`Studenti` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Zkousky_Ucebny1`
    FOREIGN KEY (`ucebna_id`)
    REFERENCES `zkousky`.`Ucebny` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Zkousky_Predmety1`
    FOREIGN KEY (`predmet_id`)
    REFERENCES `zkousky`.`Predmety` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `zkousky`.`UciteleUZkousek`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `zkousky`.`UciteleUZkousek` (
  `Zkousky_id` INT NOT NULL,
  `Ucitele_idUcitele` INT NOT NULL,
  `Role` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`Zkousky_id`, `Ucitele_idUcitele`),
  INDEX `fk_Zkousky_has_Ucitele_Ucitele1_idx` (`Ucitele_idUcitele` ASC),
  INDEX `fk_Zkousky_has_Ucitele_Zkousky1_idx` (`Zkousky_id` ASC),
  CONSTRAINT `fk_Zkousky_has_Ucitele_Zkousky1`
    FOREIGN KEY (`Zkousky_id`)
    REFERENCES `zkousky`.`Zkousky` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Zkousky_has_Ucitele_Ucitele1`
    FOREIGN KEY (`Ucitele_idUcitele`)
    REFERENCES `zkousky`.`Ucitele` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `zkousky`.`Priznaky`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `zkousky`.`Priznaky` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nazev` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `nazev_UNIQUE` (`nazev` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `zkousky`.`PriznakyPredmetu`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `zkousky`.`PriznakyPredmetu` (
  `priznak_id` INT NOT NULL,
  `predmet_id` INT NOT NULL,
  PRIMARY KEY (`priznak_id`, `predmet_id`),
  INDEX `fk_Priznaky_has_Predmety_Predmety1_idx` (`predmet_id` ASC),
  INDEX `fk_Priznaky_has_Predmety_Priznaky1_idx` (`priznak_id` ASC),
  CONSTRAINT `fk_Priznaky_has_Predmety_Priznaky1`
    FOREIGN KEY (`priznak_id`)
    REFERENCES `zkousky`.`Priznaky` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Priznaky_has_Predmety_Predmety1`
    FOREIGN KEY (`predmet_id`)
    REFERENCES `zkousky`.`Predmety` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `zkousky`.`PriznakyUceben`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `zkousky`.`PriznakyUceben` (
  `priznak_id` INT NOT NULL AUTO_INCREMENT,
  `ucebna_id` INT NOT NULL,
  PRIMARY KEY (`priznak_id`, `ucebna_id`),
  INDEX `fk_Priznaky_has_Ucebny_Ucebny1_idx` (`ucebna_id` ASC),
  INDEX `fk_Priznaky_has_Ucebny_Priznaky1_idx` (`priznak_id` ASC),
  CONSTRAINT `fk_Priznaky_has_Ucebny_Priznaky1`
    FOREIGN KEY (`priznak_id`)
    REFERENCES `zkousky`.`Priznaky` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Priznaky_has_Ucebny_Ucebny1`
    FOREIGN KEY (`ucebna_id`)
    REFERENCES `zkousky`.`Ucebny` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `zkousky`.`VhodnostUcitelu`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `zkousky`.`VhodnostUcitelu` (
  `predmet_id` INT NOT NULL,
  `ucitel_id` INT NOT NULL,
  PRIMARY KEY (`predmet_id`, `ucitel_id`),
  INDEX `fk_Predmety_has_Ucitele_Ucitele1_idx` (`ucitel_id` ASC),
  INDEX `fk_Predmety_has_Ucitele_Predmety1_idx` (`predmet_id` ASC),
  CONSTRAINT `fk_Predmety_has_Ucitele_Predmety1`
    FOREIGN KEY (`predmet_id`)
    REFERENCES `zkousky`.`Predmety` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Predmety_has_Ucitele_Ucitele1`
    FOREIGN KEY (`ucitel_id`)
    REFERENCES `zkousky`.`Ucitele` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
