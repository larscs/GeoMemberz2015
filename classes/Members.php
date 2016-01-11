<?php

class Members {
	private static $_instance = null;
    private $_pdo,
			$_query,
			$_results,
			$_count = 0;
	private function __construct() {
		// Try connecting to the mysql server
		try {
			$this->_pdo = new PDO("mysql:host=".Config::get('sqlhost').";".
								  "dbname=".Config::get('sqldb').";".
								  "charset=utf8",
								   Config::get('sqluser'),
								   Config::get('sqlpw'));
		} catch(PDOException $e) {
			// Error, terminate and show error message
            throw new Exception(_('Cannot connect: ').$e->getMessage());
		}
		// DB Connection succeeded, do some housekeeping tasks
		// Reset requested password changes - for all records where passchangeok = 1 and modifieddate < now minus passchangetimeout,
		// set passchangeok to 0 and set passchangehash to NULL.
		$table = Config::get('sqlprefix')."members";
		$pctimeout = Config::get('passchangetimeout');
		$valtimeout = Config::get('validationtimeout');
		$this->_query = $this->_pdo->prepare("SELECT * FROM {$table} WHERE passchangeok = 1 AND UNIX_TIMESTAMP(modifieddate) < UNIX_TIMESTAMP(NOW())-{$pctimeout}");
		if($this->_query->execute()) {
			if($this->_query->rowCount()>0) {
				// There are records to clean
				$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
				$userID = $this->_results[0]["membernum"];
				$this->setFieldValue('passchangeok',0,'membernum',$userID);
				$this->setFieldValue('passchangehash',NULL,'membernum',$userID);
			}
		}
		// Remove unvalidated users - delete all records where validationok = 0 and modifieddate < now minus validationtimeout.
		$this->prepSqlVars();
        $this->_query = $this->_pdo->prepare("DELETE FROM {$table} WHERE validationok = 0 AND UNIX_TIMESTAMP(modifieddate) < UNIX_TIMESTAMP(NOW())-{$valtimeout}");
		if($this->_query->execute()) {
			if($this->_query->rowCount()>0) {
				// If this was the last record (quite likely, but if another has registered in between, there will be a membernumber "hole"),
				// reset the autoincrement to max existing plus one
				$this->_query = $this->_pdo->prepare("SELECT MAX(membernum) AS MaxNo FROM {$table}");
				if($this->_query->execute()) {
					//$this->_results 
					$test = $this->_query->fetchAll(PDO::FETCH_ASSOC);
					$maxID = $this->_results[0]["MaxNo"]+1;
					$this->_query = $this->_pdo->prepare("ALTER TABLE {$table} AUTO_INCREMENT = {$maxID}");
					$this->_query->execute();

				} 
			} 
		}

	}

	public static function getInstance() {
        if(!isset(self::$_instance)) {
			// Instance not created yet, create one
			try {
                self::$_instance = new Members();
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
		}
		return self::$_instance;
	}
	
	public function isBoard() {
		// Checks whether the logged in user is a board member.
		// Always run after Session::isLoggedIn, so it's safe to
		// assume that the session var userID is set.
		if($this->getUserDetail($_SESSION[Config::get('sessionprefix')."userID"],'boardmember')) {
			return true;
		}
		return false;
	}

	public function checkUserMail($username, $mailaddr) {
		// Check that the username/e-mail combo exists. Return userID or false.
		$table = Config::get('sqlprefix')."members";
		$this->_query = $this->_pdo->prepare("SELECT membernum FROM {$table} WHERE username = ? AND email = ?");
		$this->_query->bindValue(1, $username);
		$this->_query->bindValue(2, $mailaddr);
		if($this->_query->execute()) {
			$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
			$this->_count = $this->_query->rowCount();
			if($this->_count == 1) {
				return $this->_results[0]["membernum"];
			}
		}
		return false;
	}
	public function checkUserPass($username,$password) {
		// Checks that the password matches the username. Returns array with userID and hash if successful, 
		// FALSE if not.
		$t_hasher = new PasswordHash(6, true);
		$table = Config::get('sqlprefix')."members";
		$this->_query = $this->_pdo->prepare("SELECT * FROM {$table} WHERE username = ?");
		$this->_query->bindValue(1, $username);
		if($this->_query->execute()) {
			// Query succeeded, fetch the results
			$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
			$this->_count = $this->_query->rowCount();
			switch($this->_count) {
				case 0:
					// No records; unknown username
					return false;
					break;
				case 1:
					// One record, check that hash matches password
					$check = $t_hasher->CheckPassword($password,$this->_results[0]["password"]);
					if($check) {
						// Hash matches, return userID, hash active and the validationok field
						return Array("userID"=>$this->_results[0]["membernum"],"userhash"=>$this->_results[0]["password"],"validationok"=>$this->_results[0]["validationok"],"active"=>$this->_results[0]["active"]);
					}
					return false;
					break;
				default:
					// Multiple records, database integrity compromised
					die(_("Fatal error: Duplicate member number found"));
					break;
			}
			// We should never get here, but return false just in case
			return false;
		}
		// Query failed, return false
		echo "Query failed with the following error message: ";
		echo "<pre>";
		$errmsg = $this->_query->errorInfo();
        echo $errmsg[2];
		echo "</pre>";
		exit();
		return false;
	}
	public function verifyHash($userID,$userhash) {
		$table = Config::get('sqlprefix')."members";
		$this->_query = $this->_pdo->prepare("SELECT * FROM {$table} WHERE membernum = ?");
		$this->_query->bindValue(1, $userID);
		if($this->_query->execute()) {
			// Query succeeded, fetch the results
			$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
			$this->_count = $this->_query->rowCount();

			switch($this->_count) {
				case 0:
					// No records; unknown userID
					return false;
					break;
				case 1:
					// One record, check that hash matches
					if($this->_results[0]["password"]==$userhash) {
						// Hash matches, return success

						return true;
					}
					return false;
					break;
				default:
					// Multiple records, database integrity compromised
					die(_("Fatal error: Duplicate member number found"));
					break;
			}
			// We should never get here, but return false just in case
			return false;
		}
		// Query failed, return false
		echo "Query failed with the following error message: ";
		echo "<pre>";
		$errmsg = $this->_query->errorInfo();
        echo $errmsg[2];
		echo "</pre>";
		exit();
		return false;
	}

	public function startPassChange($userID) {
		// This function facilitates password changes.
		// First, a hash is generated and stored on the user record along with a flag indicating that
		// password change is OK. The flag presence plus the hash makes the setpass.php page accept submission.
		// The hash alone is not enough.
		// Then, a mail with a link to setpass.php containing the hash is sent to the user.

		// Create hash
		$pchash = bin2hex(mcrypt_create_iv(8, MCRYPT_DEV_URANDOM));
		// Update DB with hash and flag
		$userdetails = Array("membernum"=>$userID,
							 "passchangehash"=>$pchash,
							 "passchangeok"=>true);
		if($this->updateUser($userdetails)) {
			// DB updated successfully, get e-mail and name
			$userdetails = $this->getUserData($userID);
            $userfullname = $userdetails['firstname']." ".$userdetails['middlename']." ".$userdetails['lastname'];
			$userfullname = str_replace("  "," ",$userfullname); // In case middle name is not used
            $altbody=sprintf(_("A password change has been requested from the %s member site."),Config::get('assocname'))."\r\n\r\n".
                      		 _("Click the link below or copy it into the address field in your browser")."\r\n".
                      		 _("to change your password.")."\r\n\r\n".
                      		 "{{LINK}}\r\n\r\n".
                      		 _("If you didn't request this password change, you can safely ignore this mail.")."\r\n\r\n".
                      		 _("Best regards,")."\r\n".
                      		 Config::get('assocname');
			$body = str_replace("\r\n","<br/>",$altbody);
			$altbody=str_replace("{{LINK}}",Config::get('baseaddr')."setpass.php?h=".$pchash,$altbody);
			$body = str_replace("{{LINK}}",'<a href="'.Config::get('baseaddr')."setpass.php?h=".$pchash.'">'.Config::get('baseaddr')."setpass.php?h=".$pchash.'</a>',$body);

			if($this->mailUser(Config::get('autoemail'), Config::get('autoemailname'), $userdetails['email'], $userfullname,sprintf(_("Password change requested for %s member account"),Config::get('assocname')), $body, $altbody)) {
				return true;
			}
			return false;
		}
		return false;
		// Send mail with link
	}
	public function mailUser($from, $fromname, $to, $toname, $subject, $body, $altbody) {
        try{		
            // Get HTML mail template
    		$htmltop = file_get_contents('includes/mailtop.html');
    		$htmlbottom = file_get_contents('includes/mailbottom.html');
    		$mail = new PHPMailer(true);
    
            // If the SMTP setting is true, the mailserver and mailport are used, otherwise not.
            // If the SMTP AND mailauth settings are true, mailuser and mailpass settings are used for SMTP sending.
    		if(Config::get('mailSMTP')) {
                $mail->isSMTP();
                $mail->Host = Config::get('mailserver');
    	        $mail->Port = Config::get('mailport');
                if(Config::get('mailauth')) {
                    $mail->SMTPAuth = true;
                    $mail->Username = Config::get('mailuser');
                    $mail->Password = Config::get('mailpass');
                }
    		}
    		
    		$mail->AddEmbeddedImage('img/logo_banner_hori_lite_153x30.png','logo_banner.png','logo_banner.png','base64','image/png');
    
    		$mail->From = $from;
    		$mail->FromName = $fromname;
    		$mail->addAddress($to,$toname);
            if(Config::get('autoreplyassoc') && $from == Config::get('autoemail')) { // If set and the sender is autoemail, use assoc as reply-to
                $mail->addReplyTo(Config::get('assocemail'),Config::get('assocemailname'));
            } else {
    		  $mail->addReplyTo($from,$fromname);
            }
    		if(Config::get('automailcopy')) {
    			$mail->addBCC(Config::get('automailcopyaddr'));
    		}
    		$mail->isHTML(true);
    		$mail->CharSet = 'UTF-8';
    
    		$mail->Subject = $subject;
    		if(!$altbody) {
    			$mail->MsgHTML($htmltop.$body.$htmlbottom); // Let PHPMailer make the AltBody if not provided explicitly
    		} else {
    			$mail->Body = $htmltop.$body.$htmlbottom;
    			$mail->AltBody = $altbody;
    		}
    		if($mail->send()) {
    			return Array(true,true);
    		}
    		return Array(false,"Mail sending error");
        } catch (phpmailerException $e) {
            return Array(false,$e->errorMessage());
        } catch (Exception $e) {
            return Array(false,$e->getMessage());
        }
	}

	public function addUser(array $details) {
		// Adds a user record with the provided user details.
		$table = Config::get('sqlprefix')."members";
		// Now, build querystring and execute it.
		$this->prepSqlVars();
        if(!empty($details)) {
			$sql = "INSERT INTO {$table} (";
			$sqlvalues = "";
			$first=true;
			foreach($details as $itemkey=>$itemvalue) {
				if(!$first) {$sql .= ", "; $sqlvalues .= ", ";} else {$first=false;} // Add a comma before all except the first
				$sql .= "{$itemkey}";
				$sqlvalues .= "?";
			}
			$sql .= ") VALUES ({$sqlvalues})";
			$this->_query = $this->_pdo->prepare($sql);
			$valcnt = 1;
			foreach($details as $itemkey=>$itemvalue) {
				$this->_query->bindValue($valcnt,$itemvalue);
				$valcnt++;
			}
			if($this->_query->execute()) {
				// Execution succeeded; return ID of inserted user
				return $this->_pdo->lastInsertId();
			}
		}

		return false;
	}

	public function updateUser(array $newdetails) {
		// Updates a user record with the provided user details. One of the details MUST be named "membernum"
		// and contain the userID.
		$table = Config::get('sqlprefix')."members";
		$changeditems = Array();
		// Check that userID is provided
		if(array_key_exists("membernum",$newdetails)) {
			// User exists - get all user data
			$membernum = $newdetails['membernum'];
			$userdata = $this->getUserData($membernum);
            // For all boolean values, absence OR "0" means 0 and presence (with "on") OR "1" means 1. All bool fields must be checked here
            // Make keys in $newdetails that don't exist, so that the comparison works.
            if(!array_key_exists('source',$newdetails)) {$newdetails['source']="";}
            if($newdetails['source']=="member.php") {
                // If the changes come from the member.php page, absence means zero.
                if(!array_key_exists('boardmember',$newdetails)) {$newdetails['boardmember']=0;}
                if(!array_key_exists('pubemail',$newdetails)) {$newdetails['pubemail']=0;}
                if(!array_key_exists('pubfirstname',$newdetails)) {$newdetails['pubfirstname']=0;}
                if(!array_key_exists('pubmiddlename',$newdetails)) {$newdetails['pubmiddlename']=0;}
                if(!array_key_exists('publastname',$newdetails)) {$newdetails['publastname']=0;}
                if(!array_key_exists('pubaddress',$newdetails)) {$newdetails['pubaddress']=0;}
                if(!array_key_exists('pubphone',$newdetails)) {$newdetails['pubphone']=0;}
                if(!array_key_exists('pubbirthdate',$newdetails)) {$newdetails['pubbirthdate']=0;}
                /// And remove the identifier from the array.
                unset($newdetails['source']);
                
            } else {
                // If the changes come from elsewhere, they simply aren't changed, so set to DB value.
                if(!array_key_exists('boardmember',$newdetails)) {$newdetails['boardmember']=$userdata['boardmember'];}
                if(!array_key_exists('pubemail',$newdetails)) {$newdetails['pubemail']=$userdata['pubemail'];}
                if(!array_key_exists('pubfirstname',$newdetails)) {$newdetails['pubfirstname']=$userdata['pubfirstname'];}
                if(!array_key_exists('pubmiddlename',$newdetails)) {$newdetails['pubmiddlename']=$userdata['pubmiddlename'];}
                if(!array_key_exists('publastname',$newdetails)) {$newdetails['publastname']=$userdata['publastname'];}
                if(!array_key_exists('pubaddress',$newdetails)) {$newdetails['pubaddress']=$userdata['pubaddress'];}
                if(!array_key_exists('pubphone',$newdetails)) {$newdetails['pubphone']=$userdata['pubphone'];}
                if(!array_key_exists('pubbirthdate',$newdetails)) {$newdetails['pubbirthdate']=$userdata['pubbirthdate'];}
            }
			// Now go through all the new details. Make an array of only the changed ones.
			foreach($newdetails as $detailname => $detailvalue) {
			     
				if($detailvalue==="on") {$detailvalue=1; $newdetails[$detailname]=$detailvalue;}
                if($this->getUserDetail($membernum,$detailname)!=$detailvalue) {
					$changeditems[$detailname]=$detailvalue;
				} 
			}
			// Now, if $changeditems is not empty, build querystring and execute it.
			if(!empty($changeditems)) {
                $this->prepSqlVars();
				$sql = "UPDATE {$table} SET ";
				$first=true;
				foreach($changeditems as $itemkey=>$itemvalue) {
					if(!$first) {$sql .= ", ";} else {$first=false;} // Add a comma before all except the first
					$sql .= "{$itemkey} = ?";
				}
				$sql .= " WHERE membernum = ?";
				$this->_query = $this->_pdo->prepare($sql);
				$valcnt = 1;
				foreach($changeditems as $itemkey=>$itemvalue) {
					$this->_query->bindValue($valcnt,$itemvalue);
					$valcnt++;
				}
				$this->_query->bindValue($valcnt, $membernum);
				if(!$this->_query->execute()) {
					// Execution failed
					return Array("dberror"=>true);
				}
				return true;
			} else {
				// No changed items, return message
				return Array("nochanges"=>true);
			}
		}
		return Array("nouserid"=>true);
	}

	public function getSubmembers($userID) {
		// Returns an array with all submembers for the given member number.
		// SELECT membernum, username FROM ".$dbtableprefix."members WHERE active AND parent = ".$memberdata["membernum"]
		$table = Config::get('sqlprefix')."members";
		$this->_query = $this->_pdo->prepare("SELECT membernum, username FROM {$table} WHERE active AND parent = {$userID}");
		if($this->_query->execute()) {
			$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
            return $this->_results;
		}
        // This function should only be run after checking haschildren, if it returns no submembers, set haschildren to 0
        //$this->setFieldValue('haschildren',0,'membernum',$userID);
		return false;
	}
    public function isSubmember($subID,$parentID) {
        //Misc::dump("Got here",false);
        // Checks if $subID is a submember of $parentID and returns true if so, otherwise false.
        $table = Config::get('sqlprefix')."members";
        $rows=$this->_pdo->query("SELECT COUNT(*) FROM {$table} WHERE membernum = {$subID} AND parent = {$parentID} ")->fetchColumn();
        //Misc::dump("SELECT membernum FROM {$table} WHERE membernum = {$subID} AND parent = {$parentID}<br/>" ,false);
        if($rows>0) {
            // If query returns records, it is a submember
            return true;
        } else {
            return false;
        }
    }
	public function getUserData($userID) {
		// Returns an array with all user details given a userID.
		$table = Config::get('sqlprefix')."members";
		$this->_query = $this->_pdo->prepare("SELECT * FROM {$table} WHERE membernum = ?");
		$this->_query->bindValue(1, $userID);
		if($this->_query->execute()) {
			$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
			$this->_count = $this->_query->rowCount();
			if($this->_count == 1) {
				return $this->_results[0];
			}
		}
		return false;
	}
	public function getUserDetail($userID, $detailname) {
		// Returns one detail (DB field) about the userID
		$table = Config::get('sqlprefix')."members";
		$this->_query = $this->_pdo->prepare("SELECT {$detailname} FROM {$table} WHERE membernum = ?");
		$this->_query->bindValue(1, $userID);
		if($this->_query->execute()) {
			$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
			$this->_count = $this->_query->rowCount();
			if($this->_count == 1) {
				return $this->_results[0][$detailname];
			}
		}
		return false;
	}
	public function getFieldValue($getField,$givenField,$givenValue) {
		// Returns the $getField field value from the only record matching 
		// $givenField = $givenValue (returns false if multiple matches)
		$table = Config::get('sqlprefix')."members";
		$this->_query = $this->_pdo->prepare("SELECT {$getField} FROM {$table} WHERE {$givenField} = ?");
		$this->_query->bindValue(1, $givenValue);
		if($this->_query->execute()) {
			$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
			$this->_count = $this->_query->rowCount();
			if($this->_count == 1) {
				return $this->_results[0][$getField];
			}
		}
		return false;
	}
	public function setFieldValue($setField,$setValue,$givenField,$givenValue) {
		// Sets the $setField field value to $setValue on all records matching 
		// $givenField = $givenValue. Returns true on success, false on failure.
        $this->prepSqlVars();
		$table = Config::get('sqlprefix')."members";
		$this->_query = $this->_pdo->prepare("UPDATE {$table} SET {$setField} = ? WHERE {$givenField} = ?");
		$this->_query->bindValue(1, $setValue);
		$this->_query->bindValue(2, $givenValue);
		if($this->_query->execute()) {
			return true;
		}
		return false;
	}
	public function getNewest($topnum) {
		$table = Config::get('sqlprefix')."members";
		$this->_query = $this->_pdo->prepare("SELECT * FROM {$table} ORDER BY membersince DESC LIMIT 5");
		if($this->_query->execute()) {
			$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
			return $this->_results;
		}
		return false;
	}

	public function getAges() {
		$resultset = Array();
		$view = Config::get('sqlprefix')."viewages";
		$this->_query = $this->_pdo->prepare("SELECT COUNT(*) as members FROM {$view} WHERE active");
		if(!$this->_query->execute()) { return false; }
		$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
		$nummembers = $this->_results[0]["members"];

		$this->_query = $this->_pdo->prepare("SELECT COUNT(*) as members FROM {$view} WHERE active AND age >= 16");
		if(!$this->_query->execute()) { return false; }
		$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
		$num16plus = $this->_results[0]["members"];

		$this->_query = $this->_pdo->prepare("SELECT COUNT(*) as members FROM {$view} WHERE active AND age >= 8 AND age < 16");
		if(!$this->_query->execute()) { return false; }
		$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
		$num8to15 = $this->_results[0]["members"];

		$this->_query = $this->_pdo->prepare("SELECT COUNT(*) as members FROM {$view} WHERE active AND age < 8");
		if(!$this->_query->execute()) { return false; }
		$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
		$num0to7 = $this->_results[0]["members"];

	    // Add percentages
	    $resultset['nummembers'] = $nummembers;
        if($nummembers>0) {
	       $resultset['num16plus']  = $num16plus ." (".number_format(($num16plus/$nummembers)*100)._("%)");
	       $resultset['num8to15']   = $num8to15  ." (".number_format(($num8to15/$nummembers)*100)._("%)");
	       $resultset['num0to7']    = $num0to7   ." (".number_format(($num0to7/$nummembers)*100)._("%)");
        } else {
            $resultset['num16plus'] = 0;
            $resultset['num8to15'] = 0;
            $resultset['num0to7'] = 0;
        }
		return $resultset;
	}

	public function getFeeStats() {
		// Return figures for the dashboard panel about fees
		$retarray = Array();
		$retarray['noperiods'] = true;
		$table = Config::get('sqlprefix')."feeperiods";
		$this->_query = $this->_pdo->prepare("SELECT * FROM {$table}");
		$this->_query->execute();
        $rc = $this->_query->rowCount();
        if($rc==0) { return $retarray; }
		$retarray['noperiods'] = false;
		$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
		foreach($this->_results as $period) {
			$fromdate = date_create($period["perFrom"]);
			$todate = date_create($period["perTo"]);
			$today = date_create(date("Y-m-d"));
            $mycurper = 1; // If we are within none of the periods, just pick the first
            if($fromdate <= $today && $todate > $today) {
                $mycurper = $period["perID"];
                $mypername = $period["perName"];
                $myamount = $period["perAmount"];
                $mytodate = $period["perTo"];
            }
		}
        // Now find the potential amount. That is, all member being membersince before the end date of this period (in case of future periods)
		/*$memtable = Config::get('sqlprefix')."members";
		$feetable = Config::get('sqlprefix')."feepayments";
		$this->_query = $this->_pdo->prepare("SELECT COUNT(*) AS members FROM {$memtable} LEFT JOIN {$feetable} ON paymentMember=membernum WHERE YEAR(paymentDate)+1 = '".date("Y",strtotime($mytodate))."'");
		if(!$this->_query->execute()) { return false; }
		$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
		$potamount = $this->_results[0]["members"]*$myamount;*/

        $view = Config::get('sqlprefix')."viewages";
		$this->_query = $this->_pdo->prepare("SELECT COUNT(*) AS members FROM {$view} WHERE active AND age >= 16 AND membersince < '".$mytodate."'");
		if(!$this->_query->execute()) { return false; }
		$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
		$potamount = $this->_results[0]["members"]*$myamount;

        // And the actually paid amount
		$feetable = Config::get('sqlprefix')."feepayments";
		$this->_query = $this->_pdo->prepare("SELECT SUM(paymentAmount) AS paid FROM {$feetable} WHERE paymentPeriod = ".$mycurper);
		if(!$this->_query->execute()) { return false; }
		$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
		$actamount = $this->_results[0]["paid"];

        // Calculate the percentage
        if($potamount > 0) {
            $myperc = number_format(($actamount/$potamount)*100,0,Config::get('dp'),Config::get('ts'));
        } else {
            $myperc = 0;
        }
        // If less than 10, display outside bar, otherwise inside
        if($myperc<8) {
            $insideperc = "";
            $outsideperc = "<span class=\"progress-bar\" style=\"color:black;margin-left:10px;\">".$myperc."%</span>";
        } else {
            $insideperc = "<span>".$myperc._("%</span>");
            $outsideperc = "";        
        }
        if($myperc>100){$myperc=100;}
        // Fill in the return array and return it
		$retarray["mypername"]=$mypername;
		$retarray["myamount"]=$myamount;
		$retarray["myperc"]=$myperc;
		$retarray["insideperc"]=$insideperc;
		$retarray["outsideperc"]=$outsideperc;
		$retarray["actamount"]=$actamount;
		$retarray["potamount"]=$potamount;
		return $retarray;
	}

	public function getMemberList($showinactive) {
		// Returns an array containg all data of all users, with or without inactive users.
		$table = Config::get('sqlprefix')."members";
		$q = "SELECT * FROM {$table}";
		if(!$showinactive) $q .= " WHERE active";
		$this->_query = $this->_pdo->prepare($q);
		if(!$this->_query->execute()) { return false; }
		$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
		return $this->_results;

	}

	public function getPaymentsList($perID,$showinactive) {
		$members = Config::get('sqlprefix')."members";
		$payments = Config::get('sqlprefix')."feepayments";	
		$tempvar = $this->getFeePeriod($perID);
        $perenddate = $tempvar['perTo'];
		$q = "SELECT * FROM {$members} LEFT JOIN {$payments} ON membernum=paymentMember AND paymentPeriod={$perID} WHERE membersince < '{$perenddate}'";
		if(!$showinactive) $q .= " AND active";
		$this->_query = $this->_pdo->prepare($q);
		if(!$this->_query->execute()) { return false; }
		$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
		return $this->_results;
	}

	public function getCurrentPayingMembers() {
		//Find current payment period
        $tempvar = $this->getCurrentFeePeriodData();
        $perID = $tempvar["perID"];
        $members = Config::get('sqlprefix')."members";
		$payments = Config::get('sqlprefix')."feepayments";	
		$tempvar = $this->getFeePeriod($perID);
        $perenddate = $tempvar['perTo'];
		//$q = "SELECT * FROM {$members} LEFT JOIN {$payments} ON membernum=paymentMember AND paymentPeriod={$perID} WHERE membersince < '{$perenddate}' AND active AND paymentID ORDER BY lastname";
        $q = "SELECT * FROM {$members} LEFT JOIN {$payments} ON membernum=paymentMember AND paymentPeriod=3 WHERE membersince < '2016-12-31' AND active AND paymentID ORDER BY lastname";
        $this->_query = $this->_pdo->prepare($q);
		if(!$this->_query->execute()) { return false; }
		$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
		return $this->_results;
	}

	public function memberPaymentsList($membernum) {
		$periods = Config::get('sqlprefix')."feeperiods";
		$payments = Config::get('sqlprefix')."feepayments";
		// Build fee status string in a table. One row per period, in the format: 2013-2014: Paid (29.03.2014)<br/>2012-2013: Not paid
		$this->_query = $this->_pdo->prepare("SELECT * FROM (SELECT perID, perName, perTo, paymentID, paymentDate, paymentAmount FROM {$periods} LEFT JOIN {$payments} ON paymentPeriod=perID AND paymentMember={$membernum} ORDER BY perFrom DESC) AS B WHERE perTo > '".$this->getUserDetail($membernum, 'membersince')."'");
		if(!$this->_query->execute()) { return false; }
		$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
		$feestatus = "<table>";
		foreach($this->_results as $row) {
			if(!is_null($row["paymentDate"])) {
				$feestatus .= "<tr><th>".$row["perName"]."</th><td style=\"color:green;padding-left:10px\">"._("Paid")._(" $").number_format($row["paymentAmount"],2,Config::get('dp'),Config::get('ts'))." (".Misc::formatDate($row["paymentDate"]).")</td></tr>";
			} else {
				$feestatus .= "<tr><th>".$row["perName"]."</th><td style=\"color:red;padding-left:10px\">"._("Not paid")."</td></tr>";
			}
		}
		$feestatus .= "</table>";
		return $feestatus;
	}
	public function getFeePeriod($perID) {
		// Returns the record representing the perID period.
		$periods = Config::get('sqlprefix')."feeperiods";
		$this->_query = $this->_pdo->prepare("SELECT * FROM {$periods} WHERE perID = {$perID}");
		if(!$this->_query->execute()) { return false; }
		$feeperiods=$this->_query->fetchAll(PDO::FETCH_ASSOC);
		return $feeperiods[0];
	}
	public function getCurrentFeePeriodData() {
		// Return the period details for the current fee period.
		$feeperiods = $this->getFeePeriods();
		$lastperiod = 0;
        foreach($feeperiods as $feeperiod) {
			if($feeperiod["current"]) return $feeperiod; // If a period is marked as current, return the first one (the oldest that still applies)
			$lastperiod=$feeperiod;
		}
		// If none of the periods are marked as current, return the period most recently added.
		return $lastperiod;
	}
	public function getFeePeriods() {
		// Returns the fee periods, in descending chronological order. An extra field "current" is added for 
		// boolean indication of whether the row represent a period today's date is within.
		$periods = Config::get('sqlprefix')."feeperiods";
		$this->_query = $this->_pdo->prepare("SELECT * FROM {$periods} ORDER BY perFrom DESC");
		if(!$this->_query->execute()) { return false; }
		$feeperiods=$this->_query->fetchAll(PDO::FETCH_ASSOC);
        $cnt = 0;
        foreach($feeperiods as $feeperiod) {
        	$feeperiods[$cnt]['perpaymembers'] = $this->getFeePeriodNumMembers($feeperiod['perTo']);
        	$feeperiods[$cnt]['perfeepayers'] = $this->getFeePeriodPayers($feeperiod['perID']);
        	$feeperiods[$cnt]['perfeepaid'] = $this->getFeePeriodPaid($feeperiod['perID']);
            $feeperiods[$cnt]['perpotential'] = number_format($feeperiods[$cnt]['perpaymembers']*$feeperiod["perAmount"],2,Config::get('dp'),Config::get('ts'));
            $feeperiods[$cnt]['peramountpaid'] = number_format($feeperiod["perAmount"]*$feeperiods[$cnt]['perfeepayers'],2,Config::get('dp'),Config::get('ts'));
            $feeperiods[$cnt]['peramountnotpaid'] = number_format($feeperiod["perAmount"]*$feeperiods[$cnt]['perpaymembers']-($feeperiod["perAmount"]*$feeperiods[$cnt]['perfeepayers']),2,Config::get('dp'),Config::get('ts'));
            if($feeperiods[$cnt]['perpaymembers']==0) {
                $feeperiods[$cnt]['perpercentpaid']=0;
            } else {
                $feeperiods[$cnt]['perpercentpaid'] = number_format(($feeperiods[$cnt]['perfeepaid']/($feeperiods[$cnt]['perpaymembers']*$feeperiod["perAmount"]))*100,0,Config::get('dp'),Config::get('ts'));        	
	        }
            $fromdate = date_create($feeperiod["perFrom"]);
	        $todate = date_create($feeperiod["perTo"]);
	        $today = date_create(date("Y-m-d"));
	        if($fromdate <= $today && $todate >= $today) {
	            $feeperiods[$cnt]["current"]=true;
	        } else {
	        	$feeperiods[$cnt]["current"]=false;
	        }
	        $cnt++;
        }
        return $feeperiods;
	}

	private function getFeePeriodNumMembers($perTo) {
		$agelim = Config::get('feeagelimit');
		$view = Config::get('sqlprefix')."viewages";
		$this->_query = $this->_pdo->prepare("SELECT COUNT(*) AS members FROM {$view} WHERE membersince <= '{$perTo}' AND age > {$agelim} AND active");
		if(!$this->_query->execute()) { return false; }
		$this->_results=$this->_query->fetchAll(PDO::FETCH_ASSOC);
		return $this->_results[0]["members"];
	}

	private function getFeePeriodPayers($perID) {
		$feepayments = Config::get('sqlprefix')."feepayments";
		$this->_query = $this->_pdo->prepare("SELECT COUNT(DISTINCT paymentMember) AS feepayers FROM {$feepayments} WHERE paymentPeriod = {$perID}");
		if(!$this->_query->execute()) { return false; }
		$this->_results=$this->_query->fetchAll(PDO::FETCH_ASSOC);
		return $this->_results[0]["feepayers"];
	}

	private function getFeePeriodPaid($perID) {
		$feepayments = Config::get('sqlprefix')."feepayments";
		$this->_query = $this->_pdo->prepare("SELECT SUM(paymentAmount) AS feepaid FROM {$feepayments} WHERE paymentPeriod = {$perID}");
		if(!$this->_query->execute()) { return false; }
		$this->_results=$this->_query->fetchAll(PDO::FETCH_ASSOC);
		return $this->_results[0]["feepaid"];

	}
	public function deleteFeePeriod($perID) {
		// Delete a fee period
        $this->prepSqlVars();
		$table = Config::get('sqlprefix')."feeperiods";
		$this->_query = $this->_pdo->prepare("DELETE FROM {$table} WHERE perID = {$perID}");
		if(!$this->_query->execute()) { return false; }
		return true;		
	}
	public function addFeePeriod($perName, $perFrom, $perTo, $perAmount) {
		// Add a new fee period
        $this->prepSqlVars();
		$table = Config::get('sqlprefix')."feeperiods";
		$this->_query = $this->_pdo->prepare("INSERT INTO {$table} (perName, perFrom, perTo, perAmount) VALUES (?, ?, ?, ?) ");
		$this->_query->bindValue(1, $perName);
		$this->_query->bindValue(2, $perFrom);
		$this->_query->bindValue(3, $perTo);
		$this->_query->bindValue(4, $perAmount);
		if(!$this->_query->execute()) { return false; }
		return true;
	}
	public function editFeePeriod($perID, $perName, $perFrom, $perTo, $perAmount) {
		// Edit an existing fee period
		$this->prepSqlVars();
        $table = Config::get('sqlprefix')."feeperiods";
		$this->_query = $this->_pdo->prepare("UPDATE {$table} SET perName=?, perFrom=?, perTo=?, perAmount=? WHERE perID = {$perID}");
		$this->_query->bindValue(1, $perName);
		$this->_query->bindValue(2, $perFrom);
		$this->_query->bindValue(3, $perTo);
		$this->_query->bindValue(4, $perAmount);
		if(!$this->_query->execute()) { return false; }
		return true;		
	}
    public function getMetaValue($metakey) {
        // This function takes a meta key name and returns its value or false if not found.
        $metarows = $this->customQuery("SELECT * FROM ".Config::get('sqlprefix')."meta");
        foreach($metarows as $metarow) {
            if($metarow['metaKey']==$metakey) {
                return $metarow['metaValue'];
            }
        }
        return false;
    }
	public function runSearchQuery($field,$operator,$query) {
		$table = Config::get('sqlprefix')."members";
		$this->_query = $this->_pdo->prepare("SELECT membernum, username, gcnick, firstname, middlename, lastname, active FROM {$table} WHERE {$field} {$operator} ?");
		$this->_query->bindValue(1, $query);
		if(!$this->_query->execute()) { return false; }
		$this->_results=$this->_query->fetchAll(PDO::FETCH_ASSOC);
        return $this->_results;
	}    
	public function customQuery($sql) {
		// Returns the result of a custom query in an array, or false.
		$this->_query = $this->_pdo->prepare($sql);
		if(!$this->_query->execute()) { return false; }
		$this->_results=$this->_query->fetchAll(PDO::FETCH_ASSOC);
		return $this->_results;
	}
	public function customQueryExec($sql) {
		// Returns whether the execution of a query succeeded.
		$this->_query = $this->_pdo->prepare($sql);
		if(!$this->_query->execute()) { return false; }
		return true;
	}
	public function customQueryCount($sql) {
		// Returns the number of results of a custom query in an array, or false.
		$this->_query = $this->_pdo->prepare($sql);
		if(!$this->_query->execute()) { return false; }
		return $this->_query->rowCount();
	}
	public function db2userList($query) {
		// Takes a single-column sql query and returns the result in the form of a comma-separated list
		if(!$users = $this->customQuery($query)) { return false;}
		$userlist = "";
		foreach($users as $user) {
			if(strlen($userlist)>0) {$userlist .= ", ";}
			$userlist .= "<span onClick=\"getPreview(".$user['membernum'].")\" onMouseOver=\"this.style.color='#ff0000'\" onMouseOut=\"this.style.color='#000000'\" style=\"cursor:pointer\">".$user['username']."</span>";
		}
		return $userlist;
	}
	function db2membernolist($query) {
	    $membernolist = "";
	    if(!$users = $this->customQuery($query)) { return false;}
	    foreach($users as $user) {
	    	$membernolist .= "+".$user['membernum'];
	    }
	    return $membernolist;
	}	
	function db2array($query) {
		if(!$users = $this->customQuery($query)) { return false;}
		return $users;
    }  
	public function replaceMailVars($membernum,$string,$multiline) {
		// Takes a member number and a string with %these% placeholders, and returns the string with the 
		// placeholders replaced. If multiline is true, the address and signature is also processed.
 		$viewingmember = $this->getUserData(Session::getSessionVar('userID'));
		//Misc::dump(,false);
		$recipient = $this->getUserData($membernum);

        // Get values for the variables
        $varusername     = html_entity_decode($recipient["username"]);
        $vargcname       = html_entity_decode($recipient["gcnick"]);
        $varfirstname    = html_entity_decode($recipient["firstname"]);
        $varmiddlename   = html_entity_decode($recipient["middlename"]);
        $varlastname     = html_entity_decode($recipient["lastname"]);
        $varfullname     = str_replace("  "," ",$recipient["firstname"]." ".$recipient["middlename"]." ".$recipient["lastname"]);
        $varbirthdate    = Misc::formatDate($recipient["birthdate"]);
        $varmembersince  = Misc::formatDate($recipient["membersince"]);
        $varaddress      = $recipient["address"];
        $varemail        = $recipient["email"];
        $varphone        = $recipient["phone"];
        $varposition     = $recipient["position"];

        if(!$varposition) {$varposition = _("(None)");}
        // Find the parent's username
        $parentmember = $this->getUserData($recipient["parent"]);
        $varparent       = $parentmember["username"]." (".$parentmember["firstname"]." ".$parentmember["lastname"].")";
        if($varparent == " ( )") {$varparent = _("(None)");}
        $varmembernum    = $recipient["membernum"];
        // Get list of submember usernames, comma separated: "User1, User2"
        $varsubmembers   = $this->db2userlist("SELECT username, membernum FROM ".Config::get('sqlprefix')."viewages WHERE parent = ".$recipient["membernum"]);
        if(!$varsubmembers) {$varsubmembers = _("(None)");}            
        // Get list of submember member numbers, plus separated: "+45+46"
        $varsubmembernos = $this->db2membernolist("SELECT membernum FROM ".Config::get('sqlprefix')."viewages WHERE parent = ".$recipient["membernum"]);
        if(!$varsubmembernos) {$varsubmembernos = "";}            
        // Get sum of fees due for main member plus all submembers over 16 * $mycuramount
        $tempvar = $this->getCurrentFeePeriodData();
        $mycuramount = $tempvar["perAmount"];
        $numsubs = $this->customQueryCount("SELECT * FROM ".Config::get('sqlprefix')."viewages WHERE age >= ".Config::get('feeagelimit')." AND parent = ".$recipient["membernum"]);
        $varamountdue    = number_format($mycuramount*(1+$numsubs),2,Config::get('dp'),Config::get('ts'));
        
        $varsignature    = str_replace("  "," ",$viewingmember["firstname"]." ".$viewingmember["middlename"]." ".$viewingmember["lastname"]."<br/>".$viewingmember["position"]."<br/>".Config::get('assocname'));



        // Substitute the variables
        $string = str_replace("%assocname%",Config::get('assocname'),$string);
        $string = str_replace("%assocbank%",Config::get('assocbank'),$string);
        $string = str_replace("%username%",$varusername,$string);
        $string = str_replace("%gcname%",$vargcname,$string);
        $string = str_replace("%firstname%",$varfirstname,$string);
        $string = str_replace("%middlename%",$varmiddlename,$string);
        $string = str_replace("%lastname%",$varlastname,$string);
        $string = str_replace("%fullname%",$varfullname,$string);
        $string = str_replace("%birthdate%",$varbirthdate,$string);
        $string = str_replace("%membersince%",$varmembersince,$string);
        if($multiline) $string = str_replace("%address%",$varaddress,$string);
        $string = str_replace("%email%",$varemail,$string);
        $string = str_replace("%phone%",$varphone,$string);
        $string = str_replace("%position%",$varposition,$string);
        $string = str_replace("%parent%",$varparent,$string);
        $string = str_replace("%membernum%",$varmembernum,$string);
        $string = str_replace("%submembers%",$varsubmembers,$string);
        $string = str_replace("%submembernos%",$varsubmembernos,$string);
        $string = str_replace("%amountdue%",$varamountdue,$string);
        if($multiline) $string = str_replace("%signature%",$varsignature,$string);
		return $string;
	}
    public function getChangeLog($amount) {
        $sql ="SELECT chgTime, chgTable, chgField, chgRow, chgAction, chgOldValue, chgNewValue, chgByIP, username FROM ".Config::get('sqlprefix')."changelog
               LEFT JOIN ".Config::get('sqlprefix')."members ON chgBy=membernum ORDER BY chgTime DESC LIMIT {$amount}";
        if($changes = $this->customQuery($sql)) {
            // Loop through results and add formatting
            foreach($changes as &$change) {
                if(is_null($change["username"])) $change["username"] = _("<span style=\"color:silver\">(Unregistered)</span>");
                if($change["chgByIP"]=="") $change["chgByIP"] = _("(Not determined)");
                // Filter out hashes
                if($change["chgField"] == "password" && $change["chgOldValue"] != "") $change["chgOldValue"] = _("<span style=\"color:silver\">(Hash value)</span>");
                if($change["chgField"] == "password" && $change["chgNewValue"] != "") $change["chgNewValue"] = _("<span style=\"color:silver\">(Hash value)</span>");
                if($change["chgField"] == "passchangehash" && $change["chgOldValue"] != "") $change["chgOldValue"] = _("<span style=\"color:silver\">(Hash value)</span>");
                if($change["chgField"] == "passchangehash" && $change["chgNewValue"] != "") $change["chgNewValue"] = _("<span style=\"color:silver\">(Hash value)</span>");
                if($change["chgField"] == "validationhash" && $change["chgOldValue"] != "") $change["chgOldValue"] = _("<span style=\"color:silver\">(Hash value)</span>");
                if($change["chgField"] == "validationhash" && $change["chgNewValue"] != "") $change["chgNewValue"] = _("<span style=\"color:silver\">(Hash value)</span>");
                switch($change["chgAction"]) {
                    case "update":
                        $change["chgAction"] = _("Update");
                        $change["rowcol"] = "warning";
                        break;
                    case "insert":
                        $change["chgAction"] = _("Insert");
                        $change["rowcol"] = "success";
                        break;
                    case "delete":
                        $change["chgAction"] = _("Delete");
                        $change["rowcol"] = "danger";
                        break;
                    default:
                        $change["rowcol"] = "info";
                }
            }
            // Return the changelog
            return $changes;    
        }
        return false;
    }
    public function prepSqlVars() {
        
        if(Session::getSessionVar("userID")) {   
            $q = "SET @usrid=".Session::getSessionVar("userID").";";
            $this->customQueryExec($q);
            $q = "SET @usrip='".Misc::getIP()."';";
            $this->customQueryExec($q);
            return true;
        } else {
            return false;
        }        
    }
}
