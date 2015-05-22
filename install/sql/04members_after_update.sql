CREATE TRIGGER `%sqlprefix%members_after_update` AFTER UPDATE ON `%sqlprefix%members` FOR EACH ROW BEGIN
IF @usrid IS NULL THEN
	SET @usrid = 0;
END IF;
IF @usrip IS NULL THEN
	SET @usrip = '';
END IF;
IF (NEW.username != OLD.username) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'username', NEW.membernum, "update", OLD.username, NEW.username, @usrid, @usrip);
END IF;
IF (NEW.password != OLD.password) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'password', NEW.membernum, "update", OLD.password, NEW.password, @usrid, @usrip);
END IF;
IF (NEW.validationhash != OLD.validationhash) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'validationhash', NEW.membernum, "update", OLD.validationhash, NEW.validationhash, @usrid, @usrip);
END IF;
IF (NEW.validationok != OLD.validationok) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'validationok', NEW.membernum, "update", OLD.validationok, NEW.validationok, @usrid, @usrip);
END IF;
IF (NEW.newemail != OLD.newemail) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'newemail', NEW.membernum, "update", OLD.newemail, NEW.newemail, @usrid, @usrip);
END IF;
IF (NEW.passchangehash != OLD.passchangehash) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'passchangehash', NEW.membernum, "update", OLD.passchangehash, NEW.passchangehash, @usrid, @usrip);
END IF;
IF (NEW.passchangeok != OLD.passchangeok) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'passchangeok', NEW.membernum, "update", OLD.passchangeok, NEW.passchangeok, @usrid, @usrip);
END IF;
IF (NEW.forumid != OLD.forumid) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'forumid', NEW.membernum, "update", OLD.forumid, NEW.forumid, @usrid, @usrip);
END IF;
IF (NEW.gcnick != OLD.gcnick) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'gcnick', NEW.membernum, "update", OLD.gcnick, NEW.gcnick, @usrid, @usrip);
END IF;
IF (NEW.firstname != OLD.firstname) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'firstname', NEW.membernum, "update", OLD.firstname, NEW.firstname, @usrid, @usrip);
END IF;
IF (NEW.middlename != OLD.middlename) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'middlename', NEW.membernum, "update", OLD.middlename, NEW.middlename, @usrid, @usrip);
END IF;
IF (NEW.lastname != OLD.lastname) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'lastname', NEW.membernum, "update", OLD.lastname, NEW.lastname, @usrid, @usrip);
END IF;
IF (NEW.birthdate != OLD.birthdate) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'birthdate', NEW.membernum, "update", OLD.birthdate, NEW.birthdate, @usrid, @usrip);
END IF;
IF (NEW.membersince != OLD.membersince) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'membersince', NEW.membernum, "update", OLD.membersince, NEW.membersince, @usrid, @usrip);
END IF;
IF (NEW.position != OLD.position) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'position', NEW.membernum, "update", OLD.position, NEW.position, @usrid, @usrip);
END IF;
IF (NEW.address != OLD.address) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'address', NEW.membernum, "update", OLD.address, NEW.address, @usrid, @usrip);
END IF;
IF (NEW.email != OLD.email) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'email', NEW.membernum, "update", OLD.email, NEW.email, @usrid, @usrip);
END IF;
IF (NEW.phone != OLD.phone) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'phone', NEW.membernum, "update", OLD.phone, NEW.phone, @usrid, @usrip);
END IF;
IF (NEW.usercomment != OLD.usercomment) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'usercomment', NEW.membernum, "update", OLD.usercomment, NEW.usercomment, @usrid, @usrip);
END IF;
IF (NEW.boardcomment != OLD.boardcomment) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'boardcomment', NEW.membernum, "update", OLD.boardcomment, NEW.boardcomment, @usrid, @usrip);
END IF;
IF (NEW.access != OLD.access) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'access', NEW.membernum, "update", OLD.access, NEW.access, @usrid, @usrip);
END IF;
IF (NEW.active != OLD.active) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'active', NEW.membernum, "update", OLD.active, NEW.active, @usrid, @usrip);
END IF;
IF (NEW.boardmember != OLD.boardmember) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'boardmember', NEW.membernum, "update", OLD.boardmember, NEW.boardmember, @usrid, @usrip);
END IF;
IF (NEW.pubemail != OLD.pubemail) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'pubemail', NEW.membernum, "update", OLD.pubemail, NEW.pubemail, @usrid, @usrip);
END IF;
IF (NEW.pubfirstname != OLD.pubfirstname) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'pubfirstname', NEW.membernum, "update", OLD.pubfirstname, NEW.pubfirstname, @usrid, @usrip);
END IF;
IF (NEW.pubmiddlename != OLD.pubmiddlename) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'pubmiddlename', NEW.membernum, "update", OLD.pubmiddlename, NEW.pubmiddlename, @usrid, @usrip);
END IF;
IF (NEW.publastname != OLD.publastname) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'publastname', NEW.membernum, "update", OLD.publastname, NEW.publastname, @usrid, @usrip);
END IF;
IF (NEW.pubaddress != OLD.pubaddress) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'pubaddress', NEW.membernum, "update", OLD.pubaddress, NEW.pubaddress, @usrid, @usrip);
END IF;
IF (NEW.pubphone != OLD.pubphone) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'pubphone', NEW.membernum, "update", OLD.pubphone, NEW.pubphone, @usrid, @usrip);
END IF;
IF (NEW.pubbirthdate != OLD.pubbirthdate) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'pubbirthdate', NEW.membernum, "update", OLD.pubbirthdate, NEW.pubbirthdate, @usrid, @usrip);
END IF;
IF (NEW.parent != OLD.parent) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'parent', NEW.membernum, "update", OLD.parent, NEW.parent, @usrid, @usrip);
END IF;
IF (NEW.haschildren != OLD.haschildren) THEN
	INSERT INTO %sqlprefix%changelog
		(chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgBy, chgByIP)
		VALUES
		('members', 'haschildren', NEW.membernum, "update", OLD.haschildren, NEW.haschildren, @usrid, @usrip);
END IF;
END