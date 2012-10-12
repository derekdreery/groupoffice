-- -----------------------------------------------------
-- Table `sm_user_prices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sm_user_prices` (
  `max_users` INT NOT NULL ,
  `price_per_month` DOUBLE NOT NULL ,
  PRIMARY KEY (`max_users`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sm_installations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sm_installations` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NOT NULL ,
  `ctime` INT NOT NULL ,
  `mtime` INT NOT NULL ,
  `max_users` INT NOT NULL ,
  `trial_days` INT NOT NULL DEFAULT 30 ,
  `lastlogin` INT NULL ,
  `comment` TEXT NULL ,
  `features` VARCHAR(255) NULL ,
  `mail_domains` VARCHAR(255) NULL ,
  `admin_email` VARCHAR(100) NULL ,
  `admin_name` VARCHAR(100) NULL ,
  `status` VARCHAR(50) NOT NULL DEFAULT 'ignore' ,
  `token` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sm_usage_history`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sm_usage_history` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `ctime` INT NOT NULL ,
  `count_users` INT NOT NULL ,
  `database_usage` DOUBLE NOT NULL ,
  `file_storage_usage` DOUBLE NOT NULL ,
  `mailbox_usage` DOUBLE NOT NULL ,
  `total_logins` INT NOT NULL DEFAULT 0 ,
  `installation_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_sm_usage_history_sm_installations1` (`installation_id` ASC) ,
  CONSTRAINT `fk_sm_usage_history_sm_installations1`
    FOREIGN KEY (`installation_id` )
    REFERENCES `sm_installations` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sm_module_prices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sm_module_prices` (
  `module_name` VARCHAR(45) NOT NULL ,
  `price_per_month` DOUBLE NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`module_name`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sm_installation_users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sm_installation_users` (
  `user_id` INT NOT NULL ,
	`username` VARCHAR(100) NOT NULL,
  `installation_id` INT NOT NULL ,
  `used_modules` TEXT NOT NULL ,
  `ctime` INT NOT NULL ,
  `lastlogin` INT NULL ,
  `enabled` TINYINT(1) NULL ,
  PRIMARY KEY (`user_id`, `installation_id`) ,
  INDEX `fk_sm_installation_users_sm_installations1` (`installation_id` ASC) ,
  CONSTRAINT `fk_sm_installation_users_sm_installations1`
    FOREIGN KEY (`installation_id` )
    REFERENCES `sm_installations` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sm_automatic_invoices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sm_automatic_invoices` (
  `id` INT NOT NULL ,
  `enable_invoicing` TINYINT(1) NOT NULL DEFAULT 0 ,
  `discount_price` DOUBLE NOT NULL DEFAULT 0 ,
  `discount_description` VARCHAR(255) NULL DEFAULT 'Discount' ,
  `discount_percentage` DOUBLE NOT NULL DEFAULT 0 ,
  `invoice_timespan` INT NOT NULL DEFAULT 1 ,
  `next_invoice_time` INT NOT NULL ,
  `customer_name` VARCHAR(255) NOT NULL ,
  `customer_address` VARCHAR(255) NOT NULL ,
  `customer_address_no` VARCHAR(10) NOT NULL ,
  `customer_zip` VARCHAR(45) NOT NULL ,
  `customer_state` VARCHAR(255) NULL ,
  `customer_country` VARCHAR(45) NOT NULL ,
  `customer_vat` VARCHAR(255) NULL ,
  `customer_city` VARCHAR(255) NOT NULL ,
  `installation_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_sm_automatic_invoices_sm_installations1` (`installation_id` ASC) ,
  CONSTRAINT `fk_sm_automatic_invoices_sm_installations1`
    FOREIGN KEY (`installation_id` )
    REFERENCES `sm_installations` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sm_auto_email`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sm_auto_email` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `days` INT NOT NULL DEFAULT 0 ,
  `mime` TEXT NULL ,
  `active` TINYINT(1) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sm_installation_modules`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sm_installation_modules` (
  `name` VARCHAR(100) NOT NULL ,
  `installation_id` INT NOT NULL ,
  `ctime` INT NOT NULL ,
  `mtime` INT NOT NULL ,
  `enabled` TINYINT(1) NOT NULL DEFAULT 1 ,
  INDEX `fk_sm_installation_modules_sm_installations1` (`installation_id` ASC) ,
  PRIMARY KEY (`name`, `installation_id`) ,
  CONSTRAINT `fk_sm_installation_modules_sm_installations1`
    FOREIGN KEY (`installation_id` )
    REFERENCES `sm_installations` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `sm_new_trial` (was never changes)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sm_new_trials`;
CREATE TABLE IF NOT EXISTS `sm_new_trials` (
  `name` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(100) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(20) DEFAULT NULL,
  `key` varchar(100) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`name`),
  KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `sm_user_prices` (`max_users`, `price_per_month`) VALUES
	(1, 10),
	(2, 19),
	(3, 28),
	(4, 36),
	(5, 43),
	(6, 50),
	(7, 57),
	(8, 63),
	(9, 69),
	(10, 74),
	(15, 95),
	(20, 109),
	(25, 123)
	(30, 134),
	(35, 141),
	(40, 146),
	(45, 148),
	(50, 150);

INSERT INTO `sm_module_prices` (`module_name` ,`price_per_month`) VALUES 
	('billing', '20');