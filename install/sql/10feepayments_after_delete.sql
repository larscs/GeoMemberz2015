CREATE TRIGGER `%sqlprefix%feepayments_after_delete` AFTER DELETE ON `%sqlprefix%feepayments` FOR EACH ROW BEGIN
IF @usrid IS NULL THEN
	SET @usrid = 0;
END IF;
IF @usrip IS NULL THEN
	SET @usrip = '';
END IF;
IF (OLD.paymentMember IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('feepayments', 'paymentMember', OLD.paymentID, "delete", OLD.paymentMember, NULL, @usrid, @usrip);
END IF;
IF (OLD.paymentPeriod IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('feepayments', 'paymentPeriod', OLD.paymentID, "delete", OLD.paymentPeriod, NULL, @usrid, @usrip);
END IF;
IF (OLD.paymentAmount IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('feepayments', 'paymentAmount', OLD.paymentID, "delete", OLD.paymentAmount, NULL, @usrid, @usrip);
END IF;
IF (OLD.paymentDate IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('feepayments', 'paymentDate', OLD.paymentID, "delete", OLD.paymentDate, NULL, @usrid, @usrip);
END IF;
END