CREATE TRIGGER `%sqlprefix%meta_after_update` AFTER UPDATE ON `%sqlprefix%meta` FOR EACH ROW BEGIN
IF @usrid IS NULL THEN
	SET @usrid = 0;
END IF;
IF @usrip IS NULL THEN
	SET @usrip = '';
END IF;
IF (NEW.metaKey != OLD.metaKey) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('meta', 'metaKey', NEW.metaID, "update", OLD.metaKey, NEW.metaKey, @usrid, @usrip);
END IF;
IF (NEW.metaValue != OLD.metaValue) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('meta', 'metaValue', NEW.metaID, "update", OLD.metaValue, NEW.metaValue, @usrid, @usrip);
END IF;
END