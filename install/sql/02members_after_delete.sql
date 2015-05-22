CREATE TRIGGER `%sqlprefix%members_after_delete` AFTER DELETE ON `%sqlprefix%members` FOR EACH ROW BEGIN
IF @usrid IS NULL THEN
	SET @usrid = 0;
END IF;
IF @usrip IS NULL THEN
	SET @usrip = '';
END IF;
IF (OLD.username IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'username', OLD.membernum, "delete", OLD.username, NULL, @usrid, @usrip);
END IF;
IF (OLD.password IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'password', OLD.membernum, "delete", OLD.password, NULL, @usrid, @usrip);
END IF;
IF (OLD.validationhash IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'validationhash', OLD.membernum, "delete", OLD.validationhash, NULL, @usrid, @usrip);
END IF;
IF (OLD.validationok IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'validationok', OLD.membernum, "delete", OLD.validationok, NULL, @usrid, @usrip);
END IF;
IF (OLD.newemail IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'newemail', OLD.membernum, "delete", OLD.newemail, NULL, @usrid, @usrip);
END IF;
IF (OLD.passchangehash IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'passchangehash', OLD.membernum, "delete", OLD.passchangehash, NULL, @usrid, @usrip);
END IF;
IF (OLD.passchangeok IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'passchangeok', OLD.membernum, "delete", OLD.passchangeok, NULL, @usrid, @usrip);
END IF;
IF (OLD.forumid IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'forumid', OLD.membernum, "delete", OLD.forumid, NULL, @usrid, @usrip);
END IF;
IF (OLD.gcnick IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'gcnick', OLD.membernum, "delete", OLD.gcnick, NULL, @usrid, @usrip);
END IF;
IF (OLD.firstname IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'firstname', OLD.membernum, "delete", OLD.firstname, NULL, @usrid, @usrip);
END IF;
IF (OLD.middlename IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'middlename', OLD.membernum, "delete", OLD.middlename, NULL, @usrid, @usrip);
END IF;
IF (OLD.lastname IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'lastname', OLD.membernum, "delete", OLD.lastname, NULL, @usrid, @usrip);
END IF;
IF (OLD.birthdate IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'birthdate', OLD.membernum, "delete", OLD.birthdate, NULL, @usrid, @usrip);
END IF;
IF (OLD.membersince IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'membersince', OLD.membernum, "delete", OLD.membersince, NULL, @usrid, @usrip);
END IF;
IF (OLD.position IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'position', OLD.membernum, "delete", OLD.position, NULL, @usrid, @usrip);
END IF;
IF (OLD.address IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'address', OLD.membernum, "delete", OLD.address, NULL, @usrid, @usrip);
END IF;
IF (OLD.email IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'email', OLD.membernum, "delete", OLD.email, NULL, @usrid, @usrip);
END IF;
IF (OLD.phone IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'phone', OLD.membernum, "delete", OLD.phone, NULL, @usrid, @usrip);
END IF;
IF (OLD.usercomment IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'usercomment', OLD.membernum, "delete", OLD.usercomment, NULL, @usrid, @usrip);
END IF;
IF (OLD.boardcomment IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'boardcomment', OLD.membernum, "delete", OLD.boardcomment, NULL, @usrid, @usrip);
END IF;
IF (OLD.access IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'access', OLD.membernum, "delete", OLD.access, NULL, @usrid, @usrip);
END IF;
IF (OLD.active IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'active', OLD.membernum, "delete", OLD.active, NULL, @usrid, @usrip);
END IF;
IF (OLD.boardmember IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'boardmember', OLD.membernum, "delete", OLD.boardmember, NULL, @usrid, @usrip);
END IF;
IF (OLD.pubemail IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'pubemail', OLD.membernum, "delete", OLD.pubemail, NULL, @usrid, @usrip);
END IF;
IF (OLD.pubfirstname IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'pubfirstname', OLD.membernum, "delete", OLD.pubfirstname, NULL, @usrid, @usrip);
END IF;
IF (OLD.pubmiddlename IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'pubmiddlename', OLD.membernum, "delete", OLD.pubmiddlename, NULL, @usrid, @usrip);
END IF;
IF (OLD.publastname IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'publastname', OLD.membernum, "delete",OLD.publastname, NULL, @usrid, @usrip);
END IF;
IF (OLD.pubaddress IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'pubaddress', OLD.membernum, "delete", OLD.pubaddress, NULL, @usrid, @usrip);
END IF;
IF (OLD.pubphone IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'pubphone', OLD.membernum, "delete", OLD.pubphone, NULL, @usrid, @usrip);
END IF;
IF (OLD.pubbirthdate IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'pubbirthdate', OLD.membernum, "delete", OLD.pubbirthdate, NULL, @usrid, @usrip);
END IF;
IF (OLD.parent IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'parent', OLD.membernum, "delete", OLD.parent, NULL, @usrid, @usrip);
END IF;
IF (OLD.haschildren IS NOT NULL) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'haschildren', OLD.membernum, "delete", OLD.haschildren, NULL, @usrid, @usrip);
END IF;
END