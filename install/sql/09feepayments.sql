CREATE TABLE `%sqlprefix%feepayments` (
	`paymentID` INT(11) NOT NULL AUTO_INCREMENT,
	`paymentMember` INT(11) NULL DEFAULT NULL,
	`paymentPeriod` INT(11) NULL DEFAULT NULL,
	`paymentAmount` DECIMAL(10,2) NULL DEFAULT NULL,
	`paymentDate` DATE NULL DEFAULT NULL,
	PRIMARY KEY (`paymentID`),
	INDEX `FK_%sqlprefix%feepayments_%sqlprefix%members` (`paymentMember`),
	INDEX `FK_%sqlprefix%feepayments_%sqlprefix%feeperiods` (`paymentPeriod`),
	CONSTRAINT `FK_%sqlprefix%feepayments_%sqlprefix%feeperiods` FOREIGN KEY (`paymentPeriod`) REFERENCES `%sqlprefix%feeperiods` (`perID`),
	CONSTRAINT `FK_%sqlprefix%feepayments_%sqlprefix%members` FOREIGN KEY (`paymentMember`) REFERENCES `%sqlprefix%members` (`membernum`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
