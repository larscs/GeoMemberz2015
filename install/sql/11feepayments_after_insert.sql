CREATE TRIGGER `%sqlprefix%feepayments_after_insert` AFTER INSERT ON `%sqlprefix%feepayments` FOR EACH ROW BEGIN
IF @usrid IS NULL THEN
	SET @usrid = 0;
END IF;
IF @usrip IS NULL THEN
	SET @usrip = '';
END IF;
IF (NEW.paymentMember IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('feepayments', 'paymentMember', NEW.paymentID, "insert", NULL, NEW.paymentMember, @usrid, @usrip);
END IF;
IF (NEW.paymentPeriod IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('feepayments', 'paymentPeriod', NEW.paymentID, "insert", NULL, NEW.paymentPeriod, @usrid, @usrip);
END IF;
IF (NEW.paymentAmount IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('feepayments', 'paymentAmount', NEW.paymentID, "insert", NULL, NEW.paymentAmount, @usrid, @usrip);
END IF;
IF (NEW.paymentDate IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('feepayments', 'paymentDate', NEW.paymentID, "insert", NULL, NEW.paymentDate, @usrid, @usrip);
END IF;
END