CREATE TRIGGER `%sqlprefix%feeperiods_after_delete` AFTER DELETE ON `%sqlprefix%feeperiods` FOR EACH ROW BEGIN
IF @usrid IS NULL THEN
	SET @usrid = 0;
END IF;
IF @usrip IS NULL THEN
	SET @usrip = '';
END IF;
IF (OLD.perName IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('%sqlprefix%feeperiods', 'perName', OLD.perID, "delete", OLD.perName, NULL, @usrid, @usrip);
END IF;
IF (OLD.perFrom IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('%sqlprefix%feeperiods', 'perFrom', OLD.perID, "delete", OLD.perFrom, NULL, @usrid, @usrip);
END IF;
IF (OLD.perTo IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('%sqlprefix%feeperiods', 'perTo', OLD.perID, "delete", OLD.perTo, NULL, @usrid, @usrip);
END IF;
IF (OLD.perAmount IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('%sqlprefix%feeperiods', 'perAmount', OLD.perID, "delete", OLD.perAmount, NULL, @usrid, @usrip);
END IF;
END