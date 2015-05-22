<?php
	require_once 'core/init.php';                         // Must be present on all pages using settings and classes
	if(!Session::isLoggedIn()) {Session::redirect(404);}  // Use this on all pages requiring the user to be logged in
	$memb = Members::getInstance(); 			            // Use this on all pages using the database
	if(!$memb->isBoard()) {Session::redirect(404);}			// Non-members don't need to know this page exists.

    // Find current period
    $feeperiods = $memb->getFeePeriods();
    foreach($feeperiods as $feeperiod) {
    	if($feeperiod['current']) {
    		$mycurper = $feeperiod['perID'];
    	}
    }
    // This page is meant to be called via ajax, and should only return a comma-separated list of usernames OR an empty string, if filter has no hits.
switch (Input::getGet('id')) {
    // 1 - All members
    case "1":
        echo $memb->db2userlist("SELECT username,membernum FROM ".Config::get('sqlprefix')."members WHERE active ORDER BY username");
        break;
    // 2 - All inactive (frozen) members
    case "2":
        echo $memb->db2userlist("SELECT username,membernum FROM ".Config::get('sqlprefix')."members WHERE (active IS NULL OR active = 0) ORDER BY username");
        break;
    // 3 - Primary members owing fee for current fee period
    case "3":
        echo $memb->db2userlist("SELECT username,membernum FROM (SELECT membernum, username, age, (SELECT COUNT(*) FROM ".Config::get('sqlprefix')."feepayments WHERE paymentMember = membernum AND paymentPeriod=".$mycurper.") AS paid FROM ".Config::get('sqlprefix')."viewages WHERE active AND parent = 0 AND age >= ".Config::get('feeagelimit').") AS duemembers WHERE NOT paid ORDER BY username");
        break;
    // 4 - All members excluding any submembers
    case "4":
        echo $memb->db2userlist("SELECT username,membernum FROM ".Config::get('sqlprefix')."members WHERE active AND (parent IS NULL OR parent = 0) ORDER BY username");
        break;
    // 5 - Primary members having submembers only
    case "5":
        echo $memb->db2userlist("SELECT username,membernum FROM ".Config::get('sqlprefix')."members WHERE active AND haschildren ORDER BY username");
        break;
    // 6 - Submembers only (excluding their parents)
    case "6":
        echo $memb->db2userlist("SELECT username,membernum FROM ".Config::get('sqlprefix')."members WHERE active AND (parent IS NOT NULL AND parent !=0) ORDER BY username");
        break;
    // 7 - Members over the age of %s
    case "7":
        echo $memb->db2userlist("SELECT username,membernum FROM ".Config::get('sqlprefix')."viewages WHERE active AND age >= ".Config::get('feeagelimit')." ORDER BY username");
        break;
    // 8 - Members under age of %s
    case "8":
        echo $memb->db2userlist("SELECT username,membernum FROM ".Config::get('sqlprefix')."viewages WHERE active AND age < ".Config::get('feeagelimit')." ORDER BY username");
        break;
    // 9 - Members with incomplete member data
    case "9":
        echo $memb->db2userlist("SELECT * FROM ".Config::get('sqlprefix')."members WHERE firstname IS NULL OR firstname = '' OR lastname IS NULL OR lastname = '' OR birthdate IS NULL OR birthdate = '0000-00-00' OR address IS NULL OR address = '' OR email IS NULL OR email = '' OR phone IS NULL OR phone = ''");
        break; 
    // 10 - Yourself (for testing)
    case "10":
        echo $memb->db2userlist("SELECT username,membernum FROM ".Config::get('sqlprefix')."members WHERE membernum = ".Session::getSessionVar('userID'));
        //echo "<span onClick=\"getPreview(".Session::getSessionVar('userID').")\" onMouseOver=\"this.style.color='#ff0000'\" onMouseOut=\"this.style.color='#000000'\" style=\"cursor:pointer\">".$memb->getUserDetail(Session::getSessionVar('userID'),'username')."</span>";
        break;        
            
}



//echo "Hannkatten,".$_GET["id"].",Hunnkatten";
?>