CREATE TABLE `%sqlprefix%feeperiods` (
	`perID` INT(11) NOT NULL AUTO_INCREMENT,
	`perName` MEDIUMTEXT NOT NULL,
	`perFrom` DATE NOT NULL,
	`perTo` DATE NOT NULL,
	`perAmount` DECIMAL(10,2) NULL DEFAULT NULL,
	PRIMARY KEY (`perID`)
)
COMMENT='This table lists the membership fee periods. They can be named arbitrarily, and need to have a from and a to date. Also, the records contain the current membership fee, and its currency. Leave as NULL if no membership fee applies.'
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
