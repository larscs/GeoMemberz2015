CREATE TRIGGER `%sqlprefix%feeperiods_after_update` AFTER UPDATE ON `%sqlprefix%feeperiods` FOR EACH ROW BEGIN
IF @usrid IS NULL THEN
	SET @usrid = 0;
END IF;
IF @usrip IS NULL THEN
	SET @usrip = '';
END IF;
IF (NEW.perName != OLD.perName) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('%sqlprefix%feeperiods', 'perName', NEW.perID, "update", OLD.perName, NEW.perName, @usrid, @usrip);
END IF;
IF (NEW.perFrom != OLD.perFrom) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('%sqlprefix%feeperiods', 'perFrom', NEW.perID, "update", OLD.perFrom, NEW.perFrom, @usrid, @usrip);
END IF;
IF (NEW.perTo != OLD.perTo) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('%sqlprefix%feeperiods', 'perTo', NEW.perID, "update", OLD.perTo, NEW.perTo, @usrid, @usrip);
END IF;
IF (NEW.perAmount != OLD.perAmount) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('%sqlprefix%feeperiods', 'perAmount', NEW.perID, "update", OLD.perAmount, NEW.perAmount, @usrid, @usrip);
END IF;
END