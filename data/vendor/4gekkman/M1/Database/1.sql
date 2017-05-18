-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema m1
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema m1
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `m1` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `m1` ;

-- -----------------------------------------------------
-- Table `m1`.`md1_packtypes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m1`.`md1_packtypes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(250) NOT NULL,
  `description` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `deleted_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m1`.`md2_packages`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m1`.`md2_packages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_packtype` INT UNSIGNED NOT NULL,
  `id_inner` VARCHAR(250) NOT NULL,
  `aboutpack` TEXT NOT NULL,
  `lastversion` VARCHAR(250) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `deleted_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_md2_packages_md1_packtypes_idx` (`id_packtype` ASC),
  UNIQUE INDEX `id_inner_UNIQUE` (`id_inner` ASC),
  CONSTRAINT `fk_md2_packages_md1_packtypes`
    FOREIGN KEY (`id_packtype`)
    REFERENCES `m1`.`md1_packtypes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m1`.`md3_models`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m1`.`md3_models` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` VARCHAR(45) NOT NULL,
  `id_inner` VARCHAR(250) NOT NULL,
  `name` VARCHAR(250) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `uid_UNIQUE` (`uid` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m1`.`md1001`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m1`.`md1001` (
  `id_package` INT UNSIGNED NOT NULL,
  `id_model` INT UNSIGNED NOT NULL,
  INDEX `fk_md1001_md2_packages1_idx` (`id_package` ASC),
  INDEX `fk_md1001_md4_models1_idx` (`id_model` ASC),
  CONSTRAINT `fk_md1001_md2_packages1`
    FOREIGN KEY (`id_package`)
    REFERENCES `m1`.`md2_packages` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_md1001_md3_models1`
    FOREIGN KEY (`id_model`)
    REFERENCES `m1`.`md3_models` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m1`.`md4_locales`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m1`.`md4_locales` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(250) NOT NULL,
  `description` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `deleted_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m1`.`md1002`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m1`.`md1002` (
  `id_package` INT UNSIGNED NOT NULL,
  `id_locale` INT UNSIGNED NOT NULL,
  INDEX `fk_md1003_md2_packages1_idx` (`id_package` ASC),
  INDEX `fk_md1003_md6_locales1_idx` (`id_locale` ASC),
  CONSTRAINT `fk_md1002_md2_packages1`
    FOREIGN KEY (`id_package`)
    REFERENCES `m1`.`md2_packages` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_md1002_md4_locales1`
    FOREIGN KEY (`id_locale`)
    REFERENCES `m1`.`md4_locales` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m1`.`md5_commands`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m1`.`md5_commands` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` VARCHAR(250) NOT NULL,
  `id_inner` VARCHAR(250) NOT NULL,
  `name` VARCHAR(250) NOT NULL,
  `description` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `uid_UNIQUE` (`uid` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m1`.`md6_console`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m1`.`md6_console` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` VARCHAR(250) NOT NULL,
  `id_inner` VARCHAR(250) NOT NULL,
  `name` VARCHAR(250) NOT NULL,
  `description` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `uid_UNIQUE` (`uid` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m1`.`md7_handlers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m1`.`md7_handlers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` VARCHAR(250) NOT NULL,
  `id_inner` VARCHAR(250) NOT NULL,
  `name` VARCHAR(250) NOT NULL,
  `description` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `uid_UNIQUE` (`uid` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m1`.`md1003`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m1`.`md1003` (
  `id_package` INT UNSIGNED NOT NULL,
  `id_command` INT UNSIGNED NOT NULL,
  INDEX `fk_md1005_md8_commands1_idx` (`id_command` ASC),
  INDEX `fk_md1005_md2_packages1_idx` (`id_package` ASC),
  CONSTRAINT `fk_md1003_md5_commands1`
    FOREIGN KEY (`id_command`)
    REFERENCES `m1`.`md5_commands` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_md1003_md2_packages1`
    FOREIGN KEY (`id_package`)
    REFERENCES `m1`.`md2_packages` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m1`.`md1004`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m1`.`md1004` (
  `id_package` INT UNSIGNED NOT NULL,
  `id_console` INT UNSIGNED NOT NULL,
  INDEX `fk_md1006_md9_console1_idx` (`id_console` ASC),
  INDEX `fk_md1006_md2_packages1_idx` (`id_package` ASC),
  CONSTRAINT `fk_md1004_md6_console1`
    FOREIGN KEY (`id_console`)
    REFERENCES `m1`.`md6_console` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_md1004_md2_packages1`
    FOREIGN KEY (`id_package`)
    REFERENCES `m1`.`md2_packages` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m1`.`md1005`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m1`.`md1005` (
  `id_package` INT UNSIGNED NOT NULL,
  `id_handler` INT UNSIGNED NOT NULL,
  INDEX `fk_md1007_md10_handlers1_idx` (`id_handler` ASC),
  INDEX `fk_md1007_md2_packages1_idx` (`id_package` ASC),
  CONSTRAINT `fk_md1005_md7_handlers1`
    FOREIGN KEY (`id_handler`)
    REFERENCES `m1`.`md7_handlers` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_md1005_md2_packages1`
    FOREIGN KEY (`id_package`)
    REFERENCES `m1`.`md2_packages` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m1`.`md1000`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m1`.`md1000` (
  `id_package1` INT UNSIGNED NOT NULL,
  `id_package2` INT UNSIGNED NOT NULL,
  INDEX `fk_md1008_md2_packages1_idx` (`id_package1` ASC),
  INDEX `fk_md1008_md2_packages2_idx` (`id_package2` ASC),
  CONSTRAINT `fk_md1000_md2_packages1`
    FOREIGN KEY (`id_package1`)
    REFERENCES `m1`.`md2_packages` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_md1000_md2_packages2`
    FOREIGN KEY (`id_package2`)
    REFERENCES `m1`.`md2_packages` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m1`.`md9_reltypes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m1`.`md9_reltypes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(250) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m1`.`md8_relationships`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m1`.`md8_relationships` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_model` INT UNSIGNED NOT NULL,
  `id_reltype` INT UNSIGNED NOT NULL,
  `local` VARCHAR(250) NOT NULL,
  `foreign` VARCHAR(250) NOT NULL,
  `pivot` VARCHAR(250) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_md8_relationships_md3_models1_idx` (`id_model` ASC),
  INDEX `fk_md8_relationships_md9_reltypes1_idx` (`id_reltype` ASC),
  CONSTRAINT `fk_md8_relationships_md3_models1`
    FOREIGN KEY (`id_model`)
    REFERENCES `m1`.`md3_models` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_md8_relationships_md9_reltypes1`
    FOREIGN KEY (`id_reltype`)
    REFERENCES `m1`.`md9_reltypes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
