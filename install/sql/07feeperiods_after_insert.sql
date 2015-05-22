CREATE TRIGGER `%sqlprefix%feeperiods_after_insert` AFTER INSERT ON `%sqlprefix%feeperiods` FOR EACH ROW BEGIN
IF @usrid IS NULL THEN
	SET @usrid = 0;
END IF;
IF @usrip IS NULL THEN
	SET @usrip = '';
END IF;
IF (NEW.perName IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('%sqlprefix%feeperiods', 'perName', NEW.perID, "insert", NULL, NEW.perName, @usrid, @usrip);
END IF;
IF (NEW.perFrom IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('%sqlprefix%feeperiods', 'perFrom', NEW.perID, "insert", NULL, NEW.perFrom, @usrid, @usrip);
END IF;
IF (NEW.perTo IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('%sqlprefix%feeperiods', 'perTo', NEW.perID, "insert", NULL, NEW.perTo, @usrid, @usrip);
END IF;
IF (NEW.perAmount IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('%sqlprefix%feeperiods', 'perAmount', NEW.perID, "insert", NULL, NEW.perAmount, @usrid, @usrip);
END IF;
END