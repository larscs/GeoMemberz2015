CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `%sqlprefix%viewages` AS 
SELECT
    `%sqlprefix%members`.`membernum` AS `membernum`,
    `%sqlprefix%members`.`username` AS `username`,
	 ((YEAR(NOW()) - YEAR(`%sqlprefix%members`.`birthdate`)) - (DATE_FORMAT(NOW(),'%m%d') < DATE_FORMAT(`%sqlprefix%members`.`birthdate`,'%m%d'))) AS `age`,
    `%sqlprefix%members`.`active` AS `active`,
    `%sqlprefix%members`.`membersince` AS `membersince`,
    `%sqlprefix%members`.`firstname` AS `firstname`,
    `%sqlprefix%members`.`middlename` AS `middlename`,
    `%sqlprefix%members`.`lastname` AS `lastname`,
    `%sqlprefix%members`.`email` AS `email`,
    `%sqlprefix%members`.`parent` AS `parent`
FROM
    `%sqlprefix%members`