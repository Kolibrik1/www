-- MySQL Script generated by MySQL Workbench
-- Sun Feb  3 16:37:13 2019
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema InstantGramDB
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema InstantGramDB
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `InstantGramDB` DEFAULT CHARACTER SET utf8 ;
USE `InstantGramDB` ;

-- -----------------------------------------------------
-- Table `InstantGramDB`.`User`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `InstantGramDB`.`User` ;

CREATE TABLE IF NOT EXISTS `InstantGramDB`.`User` (
  `ID_user` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(45) NOT NULL,
  `password` VARCHAR(90) NOT NULL,
  `created` DATE NOT NULL,
  `isAdmin` TINYINT NOT NULL,
  PRIMARY KEY (`ID_user`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `InstantGramDB`.`Image`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `InstantGramDB`.`Image` ;

CREATE TABLE IF NOT EXISTS `InstantGramDB`.`Image` (
  `ID_image` INT NOT NULL AUTO_INCREMENT,
  `added` DATE NOT NULL,
  `description` LONGTEXT NULL,
  `User_ID_user` INT NOT NULL,
  `isPublic` TINYINT NOT NULL,
  `fileType` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`ID_image`),
  INDEX `fk_image_User_idx` (`User_ID_user` ASC) INVISIBLE,
  CONSTRAINT `fk_image_User`
    FOREIGN KEY (`User_ID_user`)
    REFERENCES `InstantGramDB`.`User` (`ID_user`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `InstantGramDB`.`User_aliances`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `InstantGramDB`.`User_aliances` ;

CREATE TABLE IF NOT EXISTS `InstantGramDB`.`User_aliances` (
  `User_ID_user` INT NOT NULL,
  `User_ID_user1` INT NOT NULL,
  INDEX `fk_User_has_User_User2_idx` (`User_ID_user1` ASC) VISIBLE,
  INDEX `fk_User_has_User_User1_idx` (`User_ID_user` ASC) VISIBLE,
  PRIMARY KEY (`User_ID_user1`, `User_ID_user`),
  CONSTRAINT `fk_User_has_User_User1`
    FOREIGN KEY (`User_ID_user`)
    REFERENCES `InstantGramDB`.`User` (`ID_user`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_User_has_User_User2`
    FOREIGN KEY (`User_ID_user1`)
    REFERENCES `InstantGramDB`.`User` (`ID_user`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `InstantGramDB`.`Approve`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `InstantGramDB`.`Approve` ;

CREATE TABLE IF NOT EXISTS `InstantGramDB`.`Approve` (
  `FK_Approver` INT NOT NULL,
  `FK_Image` INT NOT NULL,
  `approves` TINYINT NOT NULL,
  INDEX `fk_User_has_Image_Image1_idx` (`FK_Image` ASC) VISIBLE,
  INDEX `fk_User_has_Image_User1_idx` (`FK_Approver` ASC) VISIBLE,
  PRIMARY KEY (`FK_Approver`, `FK_Image`),
  CONSTRAINT `fk_User_has_Image_User1`
    FOREIGN KEY (`FK_Approver`)
    REFERENCES `InstantGramDB`.`User` (`ID_user`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_User_has_Image_Image1`
    FOREIGN KEY (`FK_Image`)
    REFERENCES `InstantGramDB`.`Image` (`ID_image`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `InstantGramDB`.`Comment`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `InstantGramDB`.`Comment` ;

CREATE TABLE IF NOT EXISTS `InstantGramDB`.`Comment` (
  `ID_Comment` INT NOT NULL AUTO_INCREMENT,
  `FK_Commenter` INT NOT NULL,
  `FK_Image` INT NOT NULL,
  `text` LONGTEXT NOT NULL,
  `added` DATE NOT NULL,
  PRIMARY KEY (`ID_Comment`, `FK_Image`, `FK_Commenter`),
  INDEX `fk_User_has_Image_Image2_idx` (`FK_Image` ASC) VISIBLE,
  INDEX `fk_User_has_Image_User2_idx` (`FK_Commenter` ASC) VISIBLE,
  CONSTRAINT `fk_User_has_Image_User2`
    FOREIGN KEY (`FK_Commenter`)
    REFERENCES `InstantGramDB`.`User` (`ID_user`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_User_has_Image_Image2`
    FOREIGN KEY (`FK_Image`)
    REFERENCES `InstantGramDB`.`Image` (`ID_image`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
