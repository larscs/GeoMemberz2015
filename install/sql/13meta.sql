CREATE TABLE `%sqlprefix%meta` (
	`metaID` INT(11) NOT NULL AUTO_INCREMENT,
	`metaKey` TINYTEXT NOT NULL,
	`metaValue` TINYTEXT NOT NULL,
	PRIMARY KEY (`metaID`)
)
COMMENT='Metadata for GeoMemberz'
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
INSERT INTO `%sqlprefix%meta` (`metaID`, `metaKey`, `metaValue`) VALUES
	(1, 'fileversion', '1.1'),
	(2, 'dbversion', '1.1');