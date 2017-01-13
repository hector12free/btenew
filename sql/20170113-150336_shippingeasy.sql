USE bte;

CREATE TABLE IF NOT EXISTS `shippingeasy` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`ShipDate` DATE NOT NULL,
	`User` VARCHAR(20) NOT NULL,
	`OrderDate` DATE NOT NULL,
	`OrderTotal` FLOAT(10,2) NOT NULL,
	`Store` VARCHAR(40) NOT NULL,
	`OrderNumber` VARCHAR(40) NOT NULL,
	`ShipFrom` VARCHAR(80) NOT NULL,
	`ShipFromAddress` VARCHAR(120) NOT NULL,
	`Recipient` VARCHAR(80) NOT NULL,
	`RecipientBillingAddress` VARCHAR(120) NOT NULL,
	`RecipientShippingAddress` VARCHAR(120) NOT NULL,
	`EmailAddress` VARCHAR(60) NOT NULL,
	`Carrier` VARCHAR(40) NOT NULL,
	`RateProvider` VARCHAR(40) NOT NULL,
	`ServiceType` VARCHAR(40) NOT NULL,
	`PackageType` VARCHAR(40) NOT NULL,
	`ConfirmationOption` VARCHAR(40) NOT NULL,
	`Quantity` INT(11) NOT NULL,
	`WeightOZ` FLOAT(10,2) NOT NULL,
	`Zone` VARCHAR(40) NOT NULL,
	`DestinationCountry` VARCHAR(40) NOT NULL,
	`DestinationCity` VARCHAR(40) NOT NULL,
	`DestinationStateProvince` VARCHAR(40) NOT NULL,
	`TrackingNumber` VARCHAR(40) NOT NULL,
	`ShippingPaidByCustomer` FLOAT(10,2) NOT NULL,
	`PostageCost` FLOAT(10,2) NOT NULL,
	`InsuranceCost` FLOAT(10,2) NOT NULL,
	`TotalShippingCost` FLOAT(10,2) NOT NULL,
	`ShippingMargin` VARCHAR(40) NOT NULL,
	`SKU` VARCHAR(40) NOT NULL,
	`ItemName` VARCHAR(200) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `OrderNumber` (`OrderNumber`),
	UNIQUE INDEX `TrackingNumber` (`TrackingNumber`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
