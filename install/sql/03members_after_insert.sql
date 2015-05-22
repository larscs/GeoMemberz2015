CREATE TRIGGER `%sqlprefix%members_after_insert` AFTER INSERT ON `%sqlprefix%members` FOR EACH ROW BEGIN
IF @usrid IS NULL THEN
	SET @usrid = 0;
END IF;
IF @usrip IS NULL THEN
	SET @usrip = '';
END IF;
IF (NEW.username IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'username', NEW.membernum, "insert", NULL, NEW.username, @usrid, @usrip);
END IF;
IF (NEW.password IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'password', NEW.membernum, "insert", NULL, NEW.password, @usrid, @usrip);
END IF;
IF (NEW.validationhash IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'validationhash', NEW.membernum, "insert", NULL, NEW.validationhash, @usrid, @usrip);
END IF;
IF (NEW.validationok IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'validationok', NEW.membernum, "insert", NULL, NEW.validationok, @usrid, @usrip);
END IF;
IF (NEW.newemail IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'newemail', NEW.membernum, "insert", NULL, NEW.newemail, @usrid, @usrip);
END IF;
IF (NEW.passchangehash IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'passchangehash', NEW.membernum, "insert", NULL, NEW.passchangehash, @usrid, @usrip);
END IF;
IF (NEW.passchangeok IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'passchangeok', NEW.membernum, "insert", NULL, NEW.passchangeok, @usrid, @usrip);
END IF;
IF (NEW.forumid IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'forumid', NEW.membernum, "insert", NULL, NEW.forumid, @usrid, @usrip);
END IF;
IF (NEW.gcnick IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'gcnick', NEW.membernum, "insert", NULL, NEW.gcnick, @usrid, @usrip);
END IF;
IF (NEW.firstname IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'firstname', NEW.membernum, "insert", NULL, NEW.firstname, @usrid, @usrip);
END IF;
IF (NEW.middlename IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'middlename', NEW.membernum, "insert", NULL, NEW.middlename, @usrid, @usrip);
END IF;
IF (NEW.lastname IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'lastname', NEW.membernum, "insert", NULL, NEW.lastname, @usrid, @usrip);
END IF;
IF (NEW.birthdate IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'birthdate', NEW.membernum, "insert", NULL, NEW.birthdate, @usrid, @usrip);
END IF;
IF (NEW.membersince IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'membersince', NEW.membernum, "insert", NULL, NEW.membersince, @usrid, @usrip);
END IF;
IF (NEW.position IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'position', NEW.membernum, "insert", NULL, NEW.position, @usrid, @usrip);
END IF;
IF (NEW.address IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'address', NEW.membernum, "insert", NULL, NEW.address, @usrid, @usrip);
END IF;
IF (NEW.email IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'email', NEW.membernum, "insert", NULL, NEW.email, @usrid, @usrip);
END IF;
IF (NEW.phone IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'phone', NEW.membernum, "insert", NULL, NEW.phone, @usrid, @usrip);
END IF;
IF (NEW.usercomment IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'usercomment', NEW.membernum, "insert", NULL, NEW.usercomment, @usrid, @usrip);
END IF;
IF (NEW.boardcomment IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'boardcomment', NEW.membernum, "insert", NULL, NEW.boardcomment, @usrid, @usrip);
END IF;
IF (NEW.access IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'access', NEW.membernum, "insert", NULL, NEW.access, @usrid, @usrip);
END IF;
IF (NEW.active IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'active', NEW.membernum, "insert", NULL, NEW.active, @usrid, @usrip);
END IF;
IF (NEW.boardmember IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'boardmember', NEW.membernum, "insert", NULL, NEW.boardmember, @usrid, @usrip);
END IF;
IF (NEW.pubemail IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'pubemail', NEW.membernum, "insert", NULL, NEW.pubemail, @usrid, @usrip);
END IF;
IF (NEW.pubfirstname IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'pubfirstname', NEW.membernum, "insert", NULL, NEW.pubfirstname, @usrid, @usrip);
END IF;
IF (NEW.pubmiddlename IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'pubmiddlename', NEW.membernum, "insert", NULL, NEW.pubmiddlename, @usrid, @usrip);
END IF;
IF (NEW.publastname IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'publastname', NEW.membernum, "insert",NULL, NEW.publastname, @usrid, @usrip);
END IF;
IF (NEW.pubaddress IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'pubaddress', NEW.membernum, "insert", NULL, NEW.pubaddress, @usrid, @usrip);
END IF;
IF (NEW.pubphone IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'pubphone', NEW.membernum, "insert", NULL, NEW.pubphone, @usrid, @usrip);
END IF;
IF (NEW.pubbirthdate IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'pubbirthdate', NEW.membernum, "insert", NULL, NEW.pubbirthdate, @usrid, @usrip);
END IF;
IF (NEW.parent IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'parent', NEW.membernum, "insert", NULL, NEW.parent, @usrid, @usrip);
END IF;
IF (NEW.haschildren IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'haschildren', NEW.membernum, "insert", NULL, NEW.haschildren, @usrid, @usrip);
END IF;
END