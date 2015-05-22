CREATE TABLE `%sqlprefix%members` (
	`membernum` INT(11) NOT NULL AUTO_INCREMENT,
	`username` MEDIUMTEXT NOT NULL,
	`password` MEDIUMTEXT NOT NULL,
	`validationhash` MEDIUMTEXT NULL,
	`validationok` TINYINT(4) NULL DEFAULT '0',
	`newemail` MEDIUMTEXT NULL,
	`passchangehash` MEDIUMTEXT NULL,
	`passchangeok` TINYINT(4) NULL DEFAULT '0',
	`forumid` INT(11) NULL DEFAULT NULL,
	`gcnick` MEDIUMTEXT NOT NULL,
	`firstname` MEDIUMTEXT NOT NULL,
	`middlename` MEDIUMTEXT NULL,
	`lastname` MEDIUMTEXT NOT NULL,
	`birthdate` DATE NOT NULL,
	`membersince` DATE NOT NULL,
	`position` MEDIUMTEXT NULL,
	`address` MEDIUMTEXT NOT NULL,
	`email` MEDIUMTEXT NOT NULL,
	`phone` MEDIUMTEXT NULL,
	`usercomment` MEDIUMTEXT NULL,
	`boardcomment` MEDIUMTEXT NULL,
	`access` TINYINT(4) NULL DEFAULT '0',
	`modifieddate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`active` TINYINT(4) NULL DEFAULT NULL,
	`boardmember` TINYINT(4) NULL DEFAULT '0',
	`pubemail` TINYINT(4) NULL DEFAULT '0',
	`pubfirstname` TINYINT(4) NULL DEFAULT '0',
	`pubmiddlename` TINYINT(4) NULL DEFAULT '0',
	`publastname` TINYINT(4) NULL DEFAULT '0',
	`pubaddress` TINYINT(4) NULL DEFAULT '0',
	`pubphone` TINYINT(4) NULL DEFAULT '0',
	`pubbirthdate` TINYINT(4) NULL DEFAULT '0',
	`parent` INT(11) NULL DEFAULT NULL,
	`haschildren` TINYINT(4) NULL DEFAULT NULL,
	PRIMARY KEY (`membernum`)
)
COMMENT='This is the main member list. It should be pretty self-explanatory.'
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=0
;
