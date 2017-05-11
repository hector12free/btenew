USE bte;

CREATE TABLE IF NOT EXISTS `overstock_change` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`order_date` DATE NOT NULL,
	`channel` VARCHAR(20) NOT NULL,
	`change` VARCHAR(40) NOT NULL,
	`order_id` VARCHAR(40) NOT NULL,
	`sku` VARCHAR(40) NOT NULL,
	`title` VARCHAR(200) NOT NULL,
	`cost` FLOAT(10,2) NOT NULL,
	`condition` VARCHAR(20) NOT NULL,
	`qty` INT(11) NOT NULL,
	`allocation` VARCHAR(10) NOT NULL,
	`mpn` VARCHAR(40) NOT NULL,
	`note` VARCHAR(200) NOT NULL,
	`upc` VARCHAR(20) NOT NULL,
	`weight` FLOAT(10,2) NOT NULL COMMENT 'lbs',
	`reserved` VARCHAR(20) NOT NULL,
	`createdon` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updatedon` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
