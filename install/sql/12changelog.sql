CREATE TABLE `%sqlprefix%changelog` (
	`chgID` INT(11) NOT NULL AUTO_INCREMENT,
	`chgTime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`chgTable` TEXT NOT NULL,
	`chgField` TEXT NOT NULL,
	`chgRow` TEXT NOT NULL,
	`chgAction` TEXT NOT NULL,
	`chgOldValue` TEXT NULL,
	`chgNewValue` TEXT NULL,
	`chgBy` INT(11) NOT NULL,
	`chgByIP` TINYTEXT NULL,
	PRIMARY KEY (`chgID`)
)
COMMENT='This table holds a log of any database changes in the system.'
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;