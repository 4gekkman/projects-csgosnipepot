-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema m4
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema m4
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `m4` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `m4` ;

-- -----------------------------------------------------
-- Table `m4`.`md3_domains`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m4`.`md3_domains` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(1000) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m4`.`md4_protocols`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m4`.`md4_protocols` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(250) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m4`.`md5_subdomains`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m4`.`md5_subdomains` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(1000) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m4`.`md6_uris`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m4`.`md6_uris` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(1000) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m4`.`md2_types`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m4`.`md2_types` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(250) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m4`.`md1_routes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m4`.`md1_routes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_type` INT UNSIGNED NOT NULL,
  `description` TEXT NOT NULL,
  `ison` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  INDEX `fk_md1_routes_md2_types_idx` (`id_type` ASC),
  CONSTRAINT `fk_md1_routes_md2_types`
    FOREIGN KEY (`id_type`)
    REFERENCES `m4`.`md2_types` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m4`.`md1000`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m4`.`md1000` (
  `id_route` INT UNSIGNED NOT NULL,
  `id_domain` INT UNSIGNED NOT NULL,
  INDEX `fk_md1000_md3_domains1_idx` (`id_domain` ASC),
  INDEX `fk_md1000_md1_routes1_idx` (`id_route` ASC),
  CONSTRAINT `fk_md1000_md3_domains1`
    FOREIGN KEY (`id_domain`)
    REFERENCES `m4`.`md3_domains` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_md1000_md1_routes1`
    FOREIGN KEY (`id_route`)
    REFERENCES `m4`.`md1_routes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m4`.`md1001`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m4`.`md1001` (
  `id_route` INT UNSIGNED NOT NULL,
  `id_protocol` INT UNSIGNED NOT NULL,
  INDEX `fk_md1001_md4_protocols1_idx` (`id_protocol` ASC),
  INDEX `fk_md1001_md1_routes1_idx` (`id_route` ASC),
  CONSTRAINT `fk_md1001_md4_protocols1`
    FOREIGN KEY (`id_protocol`)
    REFERENCES `m4`.`md4_protocols` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_md1001_md1_routes1`
    FOREIGN KEY (`id_route`)
    REFERENCES `m4`.`md1_routes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m4`.`md1002`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m4`.`md1002` (
  `id_route` INT UNSIGNED NOT NULL,
  `id_subdomain` INT UNSIGNED NOT NULL,
  INDEX `fk_md1002_md5_subdomains1_idx` (`id_subdomain` ASC),
  INDEX `fk_md1002_md1_routes1_idx` (`id_route` ASC),
  CONSTRAINT `fk_md1002_md5_subdomains1`
    FOREIGN KEY (`id_subdomain`)
    REFERENCES `m4`.`md5_subdomains` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_md1002_md1_routes1`
    FOREIGN KEY (`id_route`)
    REFERENCES `m4`.`md1_routes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m4`.`md1003`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m4`.`md1003` (
  `id_route` INT UNSIGNED NOT NULL,
  `id_uri` INT UNSIGNED NOT NULL,
  INDEX `fk_md1003_md6_uris1_idx` (`id_uri` ASC),
  INDEX `fk_md1003_md1_routes1_idx` (`id_route` ASC),
  CONSTRAINT `fk_md1003_md6_uris1`
    FOREIGN KEY (`id_uri`)
    REFERENCES `m4`.`md6_uris` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_md1003_md1_routes1`
    FOREIGN KEY (`id_route`)
    REFERENCES `m4`.`md1_routes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `m4`.`md2000`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `m4`.`md2000` (
  `id_route` INT UNSIGNED NOT NULL,
  `id_package` INT UNSIGNED NOT NULL,
  INDEX `fk_md1004_md1_routes1_idx` (`id_route` ASC),
  CONSTRAINT `fk_md1004_md1_routes1`
    FOREIGN KEY (`id_route`)
    REFERENCES `m4`.`md1_routes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = '{\n  \"mpackid\":\"M1\",\n  \"table\":\"MD2_packages\",\n  \"version\":\"~1.0\"\n}';


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
