<?php 
	require_once 'core/init.php';                         // Must be present on all pages using settings and classes
	if(!Session::isLoggedIn()) {Session::redirect(404);}  // Use this on all pages requiring the user to be logged in
	$memb = Members::getInstance(); 			            // Use this on all pages using the database
	if(!$memb->isBoard()) {Session::redirect(404);}			// Non-members don't need to know this page exists. 

	$_SESSION["mailprogress"]=0;
	$_SESSION["mailprogtext"]=_("Preparing mass mailing...");
	session_write_close();


    // Find current period
    $feeperiods = $memb->getFeePeriods();
    foreach($feeperiods as $feeperiod) {
    	if($feeperiod['current']) {
    		$mycurper = $feeperiod['perID'];
    	}
    }
	// Generate recipient list based on target group
	
	if(Input::exists('post')){
	
	    switch (Input::getPost('target')) {
	        // 1 - All members
	        case "1":
	            $targetarray = $memb->db2array("SELECT membernum, username, firstname, middlename, lastname, email FROM ".Config::get('sqlprefix')."members WHERE active ORDER BY username");
	            break;
	        // 2 - All inactive (frozen) members
	        case "2":
	            $targetarray = $memb->db2array("SELECT membernum, username, firstname, middlename, lastname, email FROM ".Config::get('sqlprefix')."members WHERE (active IS NULL OR active = 0) ORDER BY username");
	            break;
	        // 3 - Members owing fee for current fee period
	        case "3":
	            $targetarray = $memb->db2array("SELECT membernum, username, firstname, middlename, lastname, email FROM (SELECT membernum, username, age, firstname, middlename, lastname, email, (SELECT COUNT(*) FROM ".Config::get('sqlprefix')."feepayments WHERE paymentMember = membernum AND paymentPeriod=".$mycurper.") AS paid FROM ".Config::get('sqlprefix')."viewages WHERE active AND parent = 0 AND age >= ".Config::get('feeagelimit').") AS duemembers WHERE NOT paid ORDER BY username");
	            break;
	        // 4 - All members excluding any submembers
	        case "4":
	            $targetarray = $memb->db2array("SELECT membernum, username, firstname, middlename, lastname, email FROM ".Config::get('sqlprefix')."members WHERE active AND (parent IS NULL OR parent = 0) ORDER BY username");
	            break;
	        // 5 - Primary members having submembers only
	        case "5":
	            $targetarray = $memb->db2array("SELECT membernum, username, firstname, middlename, lastname, email FROM ".Config::get('sqlprefix')."members WHERE active AND haschildren ORDER BY username");
	            break;
            // 6 - Submembers only (excluding their parents)
	        case "6":
	            $targetarray = $memb->db2array("SELECT membernum, username, firstname, middlename, lastname, email FROM ".Config::get('sqlprefix')."members WHERE active AND (parent IS NOT NULL AND parent !=0) ORDER BY username");
	            break;
	        // 7 - Members over the age of %s
	        case "7":
	            $targetarray = $memb->db2array("SELECT membernum, username, firstname, middlename, lastname, email FROM ".Config::get('sqlprefix')."viewages WHERE active AND age >= ".Config::get('feeagelimit')." ORDER BY username");
	            break;
	        // 8 - Members under age of %s
	        case "8":
	            $targetarray = $memb->db2array("SELECT membernum, username, firstname, middlename, lastname, email FROM ".Config::get('sqlprefix')."viewages WHERE active AND age < ".Config::get('feeagelimit')." ORDER BY username");
	            break;
	        // 9 - Members with incomplete member data
	        case "9":
	            $targetarray = $memb->db2array("SELECT membernum, username, firstname, middlename, lastname, email FROM ".Config::get('sqlprefix')."members WHERE firstname IS NULL OR firstname = '' OR lastname IS NULL OR lastname = '' OR birthdate IS NULL OR birthdate = '0000-00-00' OR address IS NULL OR address = '' OR email IS NULL OR email = '' OR phone IS NULL OR phone = ''");
	            break;
            // 10 - Yourself (for testing)
            case "10":
                $targetarray = $memb->db2array("SELECT membernum, username, firstname, middlename, lastname, email FROM ".Config::get('sqlprefix')."members WHERE membernum = ".Session::getSessionVar('userID'));
                break;
            // 11 - Single member
            case "11":
                $targetarray = $memb->db2array("SELECT membernum, username, firstname, middlename, lastname, email FROM ".Config::get('sqlprefix')."members WHERE username = '".Input::getPost('singlename')."'");
                break; 
	    }
	    $numrecips = count($targetarray);
	    $recipno = 0;
        $errlist = "";
	    foreach($targetarray as $recipient) {
			// Update progress bar displayed on the dosendmail.php page
			$recipno++;
			$pct = floor(($recipno/$numrecips)*100);
            if($pct==100) $pct=99;
			session_start();
			$_SESSION["mailprogress"]=$pct;
			$_SESSION["mailprogtext"]=_("Sending e-mail to ").$recipient["username"]._(" on ").$recipient["email"];
			session_write_close();
			// Do var substitution on subject and body
			$subj = $memb->replaceMailVars($recipient["membernum"],Input::getPost('subj'),false);
			$body = $memb->replaceMailVars($recipient["membernum"],Input::getPost('body'),true);
			// Prepare the names and addresses
			$nameparts = explode(" <",Input::getPost('sender'));
			$sendername = $nameparts[0];
			$senderemail = str_replace(">","",$nameparts[1]);
			$recipname = $recipient["firstname"]." ".$recipient["middlename"]." ".$recipient["lastname"];
			$recipname = str_replace("  "," ",$recipname);
			$recipemail = $recipient["email"];

			// Send the mail
			$result = $memb->mailUser($senderemail, $sendername, $recipemail, $recipname, $subj, $body, false);
			
            if(!$result[0]) {
            	// Do error handling, aggregating errors
				$errlist .= $result[1];
			}			
	    }
			sleep(1); // To improve chance it will arrive last
            $endmessage = _("Mass mailing complete");
            if($errlist) {
                $endmessage .= "<br/><br/>";
                $endmessage .= _("<div style=\"color:red;border-bottom: black solid 1px\">The following errors occurred during mailing:</div>");
                $endmessage .= $errlist;
            }
            session_start();
			$_SESSION["mailprogress"]=100;
			$_SESSION["mailprogtext"]=$endmessage;
			session_write_close();
	}
