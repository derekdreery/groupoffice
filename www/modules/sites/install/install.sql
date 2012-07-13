CREATE  TABLE IF NOT EXISTS `si_sites` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NOT NULL ,
  `domain` VARCHAR(100) NOT NULL ,
  `login_path` VARCHAR(255) NOT NULL ,
  `template` VARCHAR(100) NOT NULL ,
  `register_user_groups` VARCHAR(100) NULL ,
  `ssl` TINYINT(1) NULL ,
  `mod_rewrite` TINYINT(1) NULL ,
  `mtime` INT NOT NULL ,
  `ctime` INT NOT NULL ,
  `user_id` INT NOT NULL ,
  `language_id` INT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `si_content`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `si_content` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `user_id` INT NOT NULL ,
  `ctime` INT NOT NULL DEFAULT 0 ,
  `mtime` INT NOT NULL DEFAULT 0 ,
  `title` VARCHAR(100) NOT NULL ,
  `slug` VARCHAR(100) NOT NULL ,
  `meta_title` VARCHAR(100) NULL ,
  `meta_description` VARCHAR(255) NULL ,
  `meta_keywords` VARCHAR(255) NULL ,
  `content` TEXT NULL ,
  `status` INT NOT NULL DEFAULT 1 ,
  `parent_id` INT NULL ,
  `site_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `slug_UNIQUE` (`slug` ASC, `site_id` ASC) ,
  INDEX `fk_si_content_si_content1` (`parent_id` ASC) ,
  INDEX `fk_si_content_si_sites1` (`site_id` ASC) ,
  CONSTRAINT `fk_si_content_si_content1`
    FOREIGN KEY (`parent_id` )
    REFERENCES `si_content` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_content_si_sites1`
    FOREIGN KEY (`site_id` )
    REFERENCES `si_sites` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;