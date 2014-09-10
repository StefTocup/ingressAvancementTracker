SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

GRANT USAGE ON *.* TO 'ingress'@'localhost' IDENTIFIED BY 'iunsgerress';
GRANT SELECT, INSERT, UPDATE, DELETE ON `ingress`.* TO 'ingress'@'localhost';

CREATE SCHEMA IF NOT EXISTS `ingress` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `ingress` ;

-- -----------------------------------------------------
-- Table `ingress`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ingress`.`users` (
  `id_joueur` INT NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(45) NOT NULL,
  `passwd` VARCHAR(45) NOT NULL,
  `date_derniere_maj` DATETIME NOT NULL,
  `niveau` SMALLINT NULL DEFAULT 1,
  `admin` TINYINT NULL DEFAULT 0,
  PRIMARY KEY (`id_joueur`))
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `ingress`.`niveaux`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ingress`.`niveaux` (
  `niveau` SMALLINT NOT NULL,
  `AP` VARCHAR(45) NOT NULL,
  `nb_ag` TINYINT NOT NULL DEFAULT 0,
  `nb_au` TINYINT NOT NULL DEFAULT 0,
  `nb_pt` TINYINT NOT NULL DEFAULT 0,
  `nb_black` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`niveau`))
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `ingress`.`couleur_medaille`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ingress`.`couleur_medaille` (
  `id_couleur_medaille` INT NOT NULL,
  `lib_couleur_medaille` VARCHAR(45) NOT NULL,
  `url_couleur` VARCHAR(45) NULL,
  PRIMARY KEY (`id_couleur_medaille`))
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `ingress`.`compteurs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ingress`.`compteurs` (
  `id_compteur` INT NOT NULL,
  `lib_champ` VARCHAR(45) NOT NULL,
  `lib_medaille` VARCHAR(45) NULL,
  PRIMARY KEY (`id_compteur`))
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `ingress`.`palliers_medailles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ingress`.`palliers_medailles` (
  `id_compteur` INT NOT NULL,
  `id_couleur_medaille` INT NOT NULL,
  `nb_min` INT NOT NULL,
  PRIMARY KEY (`id_compteur`, `id_couleur_medaille`),
  INDEX `fk_couleur_medaille_has_compteurs_compteurs1_idx` (`id_compteur` ASC),
  INDEX `fk_couleur_medaille_has_compteurs_couleur_medaille_idx` (`id_couleur_medaille` ASC))
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `ingress`.`historique`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ingress`.`historique` (
  `date` DATETIME NOT NULL,
  `id_joueur` INT NOT NULL,
  `id_compteur` INT NOT NULL,
  `valeur` VARCHAR(45) NULL,
  PRIMARY KEY (`date`, `id_joueur`, `id_compteur`),
  INDEX `fk_historique_users1_idx` (`id_joueur` ASC),
  INDEX `fk_historique_compteurs1_idx` (`id_compteur` ASC))
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `ingress`.`historique_avancement_medailles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ingress`.`historique_avancement_medailles` (
  `id_joueur` INT NOT NULL,
  `date` DATETIME NOT NULL,
  `id_couleur_medaille` INT NOT NULL,
  `id_compteur` INT NOT NULL,
  PRIMARY KEY (`id_joueur`, `date`, `id_couleur_medaille`, `id_compteur`),
  INDEX `fk_historique_avancement_palliers_medailles1_idx` (`id_couleur_medaille` ASC, `id_compteur` ASC))
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `ingress`.`historique_avancement_niveau`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ingress`.`historique_avancement_niveau` (
  `date` DATETIME NOT NULL,
  `id_joueur` INT NOT NULL,
  `niveau` INT NULL,
  PRIMARY KEY (`date`, `id_joueur`),
  INDEX `fk_historique_avancement_niveau_users1_idx` (`id_joueur` ASC))
ENGINE = MyISAM;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `ingress`.`niveaux`
-- -----------------------------------------------------
START TRANSACTION;
USE `ingress`;
INSERT INTO `ingress`.`niveaux` (`niveau`, `AP`, `nb_ag`, `nb_au`, `nb_pt`, `nb_black`) VALUES (2, '2500', 0, 0, 0, 0);
INSERT INTO `ingress`.`niveaux` (`niveau`, `AP`, `nb_ag`, `nb_au`, `nb_pt`, `nb_black`) VALUES (3, '20000', 0, 0, 0, 0);
INSERT INTO `ingress`.`niveaux` (`niveau`, `AP`, `nb_ag`, `nb_au`, `nb_pt`, `nb_black`) VALUES (4, '70000', 0, 0, 0, 0);
INSERT INTO `ingress`.`niveaux` (`niveau`, `AP`, `nb_ag`, `nb_au`, `nb_pt`, `nb_black`) VALUES (5, '150000', 0, 0, 0, 0);
INSERT INTO `ingress`.`niveaux` (`niveau`, `AP`, `nb_ag`, `nb_au`, `nb_pt`, `nb_black`) VALUES (6, '300000', 0, 0, 0, 0);
INSERT INTO `ingress`.`niveaux` (`niveau`, `AP`, `nb_ag`, `nb_au`, `nb_pt`, `nb_black`) VALUES (7, '600000', 0, 0, 0, 0);
INSERT INTO `ingress`.`niveaux` (`niveau`, `AP`, `nb_ag`, `nb_au`, `nb_pt`, `nb_black`) VALUES (8, '1200000', 0, 0, 0, 0);
INSERT INTO `ingress`.`niveaux` (`niveau`, `AP`, `nb_ag`, `nb_au`, `nb_pt`, `nb_black`) VALUES (9, '2400000', 4, 1, 0, 0);
INSERT INTO `ingress`.`niveaux` (`niveau`, `AP`, `nb_ag`, `nb_au`, `nb_pt`, `nb_black`) VALUES (10, '4000000', 5, 2, 0, 0);
INSERT INTO `ingress`.`niveaux` (`niveau`, `AP`, `nb_ag`, `nb_au`, `nb_pt`, `nb_black`) VALUES (11, '6000000', 6, 4, 0, 0);
INSERT INTO `ingress`.`niveaux` (`niveau`, `AP`, `nb_ag`, `nb_au`, `nb_pt`, `nb_black`) VALUES (12, '8400000', 7, 6, 0, 0);
INSERT INTO `ingress`.`niveaux` (`niveau`, `AP`, `nb_ag`, `nb_au`, `nb_pt`, `nb_black`) VALUES (13, '12000000', 0, 7, 1, 0);
INSERT INTO `ingress`.`niveaux` (`niveau`, `AP`, `nb_ag`, `nb_au`, `nb_pt`, `nb_black`) VALUES (14, '17000000', 0, 0, 2, 0);
INSERT INTO `ingress`.`niveaux` (`niveau`, `AP`, `nb_ag`, `nb_au`, `nb_pt`, `nb_black`) VALUES (15, '24000000', 0, 0, 3, 0);
INSERT INTO `ingress`.`niveaux` (`niveau`, `AP`, `nb_ag`, `nb_au`, `nb_pt`, `nb_black`) VALUES (16, '40000000', 0, 0, 4, 2);

COMMIT;


-- -----------------------------------------------------
-- Data for table `ingress`.`couleur_medaille`
-- -----------------------------------------------------
START TRANSACTION;
USE `ingress`;
INSERT INTO `ingress`.`couleur_medaille` (`id_couleur_medaille`, `lib_couleur_medaille`, `url_couleur`) VALUES (0, 'aucune', '');
INSERT INTO `ingress`.`couleur_medaille` (`id_couleur_medaille`, `lib_couleur_medaille`, `url_couleur`) VALUES (1, 'bronze', 'images/bronze.png');
INSERT INTO `ingress`.`couleur_medaille` (`id_couleur_medaille`, `lib_couleur_medaille`, `url_couleur`) VALUES (2, 'argent', 'images/silver.png');
INSERT INTO `ingress`.`couleur_medaille` (`id_couleur_medaille`, `lib_couleur_medaille`, `url_couleur`) VALUES (3, 'or', 'images/gold.png');
INSERT INTO `ingress`.`couleur_medaille` (`id_couleur_medaille`, `lib_couleur_medaille`, `url_couleur`) VALUES (4, 'platine', 'images/platinum.png');
INSERT INTO `ingress`.`couleur_medaille` (`id_couleur_medaille`, `lib_couleur_medaille`, `url_couleur`) VALUES (5, 'noire', 'images/black.png');

COMMIT;


-- -----------------------------------------------------
-- Data for table `ingress`.`compteurs`
-- -----------------------------------------------------
START TRANSACTION;
USE `ingress`;
INSERT INTO `ingress`.`compteurs` (`id_compteur`, `lib_champ`, `lib_medaille`) VALUES (0, 'AP', 'AP');
INSERT INTO `ingress`.`compteurs` (`id_compteur`, `lib_champ`, `lib_medaille`) VALUES (1, 'Unique Portals Visited', 'Explorer');
INSERT INTO `ingress`.`compteurs` (`id_compteur`, `lib_champ`, `lib_medaille`) VALUES (2, 'Portal Discovereds', 'Seer');
INSERT INTO `ingress`.`compteurs` (`id_compteur`, `lib_champ`, `lib_medaille`) VALUES (3, 'Hacks', 'Hacker');
INSERT INTO `ingress`.`compteurs` (`id_compteur`, `lib_champ`, `lib_medaille`) VALUES (4, 'Resonators Deployed', 'Builder');
INSERT INTO `ingress`.`compteurs` (`id_compteur`, `lib_champ`, `lib_medaille`) VALUES (5, 'Links Created', 'Connector');
INSERT INTO `ingress`.`compteurs` (`id_compteur`, `lib_champ`, `lib_medaille`) VALUES (6, 'Control Fields Created', 'Mind-Controller');
INSERT INTO `ingress`.`compteurs` (`id_compteur`, `lib_champ`, `lib_medaille`) VALUES (7, 'XM Recherged', 'Recharger');
INSERT INTO `ingress`.`compteurs` (`id_compteur`, `lib_champ`, `lib_medaille`) VALUES (8, 'Portals Captured', 'Liberator');
INSERT INTO `ingress`.`compteurs` (`id_compteur`, `lib_champ`, `lib_medaille`) VALUES (9, 'Unique Portals Captured', 'Pioneer');
INSERT INTO `ingress`.`compteurs` (`id_compteur`, `lib_champ`, `lib_medaille`) VALUES (10, 'Resonators Destroyed', 'Purifier');
INSERT INTO `ingress`.`compteurs` (`id_compteur`, `lib_champ`, `lib_medaille`) VALUES (11, 'Max Time Portal Held', 'Guardian');

COMMIT;


-- -----------------------------------------------------
-- Data for table `ingress`.`palliers_medailles`
-- -----------------------------------------------------
START TRANSACTION;
USE `ingress`;
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (1, 1, 100);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (1, 2, 1000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (1, 3, 2000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (1, 4, 10000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (1, 5, 30000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (2, 1, 10);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (2, 2, 50);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (2, 3, 200);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (2, 4, 500);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (2, 5, 5000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (3, 1, 2000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (3, 2, 10000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (3, 3, 30000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (3, 4, 100000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (3, 5, 200000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (4, 1, 2000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (4, 2, 10000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (4, 3, 30000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (4, 4, 100000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (4, 5, 200000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (5, 1, 50);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (5, 2, 1000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (5, 3, 5000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (5, 4, 25000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (5, 5, 100000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (6, 1, 100);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (6, 2, 500);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (6, 3, 2000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (6, 4, 10000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (6, 5, 40000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (7, 1, 100000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (7, 2, 1000000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (7, 3, 3000000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (7, 4, 10000000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (7, 5, 25000000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (8, 1, 100);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (8, 2, 1000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (8, 3, 5000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (8, 4, 15000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (8, 5, 40000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (9, 1, 20);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (9, 2, 200);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (9, 3, 1000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (9, 4, 5000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (9, 5, 20000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (10, 1, 2000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (10, 2, 10000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (10, 3, 30000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (10, 4, 100000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (10, 5, 300000);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (11, 1, 3);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (11, 2, 10);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (11, 3, 20);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (11, 4, 90);
INSERT INTO `ingress`.`palliers_medailles` (`id_compteur`, `id_couleur_medaille`, `nb_min`) VALUES (11, 5, 150);

COMMIT;

