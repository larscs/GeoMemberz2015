<?php
	require_once 'core/init.php';                         // Must be present on all pages using settings and classes
	if(!Session::isLoggedIn()) {Session::redirect(404);}  // Use this on all pages requiring the user to be logged in
	$memb = Members::getInstance(); 			            // Use this on all pages using the database
	// $viewer is the userID of the viewing member.
	$viewer = Session::getSessionVar('userID');
	// $userID is the userID of the member whose details the viewer requests to see
	$userID=0;
	// $isown is true if viewer and userid is the same
	$isown = false;
	// $ischild is true if viewer is the parent of userID
	$ischild = false;
	// $issubmit is true if the post data is a form submission with changes
	$issubmit = false;
	// Vars to show status message on top - used when changes are made
	$topmsg = "";
	$topmsgtype = "";
	if(!Input::exists('post') && !Input::exists('get')) {
		// No post or get data; page view is for user's own details
		$isown = true;
		$userID = Session::getSessionVar('userID');
		$memberdata = $memb->getUserData($userID);
	}
	if(Input::exists('get') || Input::exists('post')) {
		if(Input::getGet('membernum')) $userID = Input::getGet('membernum');
		// The ID should be the same between GET-ing and POST-ing, but we need to get it no matter where it is.
		if(Input::getPost('membernum')) $userID = Input::getPost('membernum');
		// Get data received, "membernum" must be passed
		if(!$userID) Session::errorPage(_("Error"),_("One or more arguments are invalid."));
		// If equal, viewing own record, which should be ok
		if($userID == $viewer) $isown = true;
		// If viewer is parent, viewing should also be ok
		$memberdata = $memb->getUserData($userID);
		if($memberdata['parent'] == $viewer) $ischild = true;
		// To continue from here, either $isown OR $ischild OR isBoard() must be true.
		// Or inversely, if $isown AND $ischild AND isBoard() are all false, no access. 
		if(!$isown && !$ischild && !$memb->isBoard()) {
			Session::errorPage(_("Access denied"),_("You must be a board member to access this page."));
		}
		// If we get here, we have a userid which is either self, a child or the viewer is a board member.
	}
	if(Input::exists('post')) {
		$memberdata = $memb->getUserData($userID);
		$issubmit = true;
	}

	if($issubmit) {
		// Process submission, validation first
        // Validate input
        $myVal = new Validation;
        $myVal->errors = Array();               // Clear the array
        $userdetails = $_POST;
		// First ensure all dates are in ISO format
		$userdetails['membersince'] = date('Y-m-d',strtotime($userdetails['membersince']));
        $userdetails['birthdate'] = date('Y-m-d',strtotime($userdetails['birthdate']));
        // Set local vars to the submitted and hidden e-mail address, and reset the submitted to the hidden one,
        $newemail = $userdetails['email'] ;
        $oldemail = $userdetails['emailin'];
        unset($userdetails['emailin']);

        if($myVal->checkFormData($userdetails,true)) {
			// Validation complete, make changes in member table
			$userdetails['email']=$oldemail;	// We wanted to validate the new one, but now reset to the old
			$return = $memb->updateUser($userdetails);
			if(isset($return['dberror'])) {
				Session::errorPage(_("Database error"),_("An error ocurred while updating a database record."));
			}
			if(isset($return['nochanges'])) {
				$topmsg = _("No changes.");	
				$topmsgtype = "warning";
			} else {
				$topmsg = _("Account details updated.");
				$topmsgtype = "success";
			}
			// If email has changed, trigger email validation
			if($oldemail != $newemail) {
				if(!$memb->isBoard()) { // If the logged in member is not a board member, validation is required
					// New validation required before email is actually changed.
					// Create hash
					$valhash = bin2hex(mcrypt_create_iv(8, MCRYPT_DEV_URANDOM));
					// Insert hash into DB
					if(!$memb->setFieldValue('validationhash',$valhash,'membernum',$memberdata['membernum'])) Session::errorPage(_("Database error"),_("An error ocurred while updating a database record."));
					// Insert new e-mail into DB
					if(!$memb->setFieldValue('newemail',$newemail,'membernum',$memberdata['membernum'])) Session::errorPage(_("Database error"),_("An error ocurred while updating a database record."));
					// Send validation mail to new e-mail
		            $userfullname = $memberdata['firstname']." ".$memberdata['middlename']." ".$memberdata['lastname'];
		            $userfullname = str_replace("  "," ",$userfullname); // In case middle name is not used
		            $altbody=sprintf(_("Hi, %s!"),$memberdata['username'])."\r\n\r\n".
		                     sprintf(_("In order to complete changing your e-mail address for your %s account,"),Config::get('assocname'))."\r\n".
		                     	  _("you need to validate it by clicking on the link below.")."\r\n\r\n".
		                          "{{LINK}}\r\n\r\n".
		                          sprintf(_("If you didn't change your password at %s, you can ignore this mail."),Config::get('assocname'))."\r\n".
		                          _("Your address will be deleted within 24 hours.")."\r\n\r\n".
		                          _("Best regards,")."\r\n".
		                          Config::get('assocname');
		            $body = str_replace("\r\n","<br/>",$altbody);
		            $altbody=str_replace("{{LINK}}",Config::get('baseaddr')."chgvalidate.php?h=".$valhash,$altbody);
		            $body = str_replace("{{LINK}}",'<a href="'.Config::get('baseaddr')."chgvalidate.php?h=".$valhash.'">'.Config::get('baseaddr')."chgvalidate.php?h=".$valhash.'</a>',$body);
		            if(!$memb->mailUser(Config::get('autoemail'), Config::get('autoemailname'), $newemail, $userfullname,sprintf(_("E-mail address change validation for %s member account"),Config::get('assocname')), $body, $altbody)) {
		                // Mailing failed, show error page
		                Session::errorPage(_("Mail error"),_("Error when sending mail.<br/>Please report this to ")."<a href=\"mailto:".Config::get('autoemail')."?subject="._("Error sending mail")."\">".Config::get('autoemail')."</a>.");   
		            }

					$topmsg = sprintf(_("E-mail address changed. A validation mail has been sent to the new address, <strong>%s</strong>, however the address change will not take effect and be reflected here before the validation is completed."),$newemail);	
					$topmsgtype = "warning";				
				} else {
					// The logged in member is a board member; the e-mail change should be done right away
					if(!$memb->setFieldValue('email',$newemail,'membernum',$memberdata['membernum'])) Session::errorPage(_("Database error"),_("An error ocurred while updating a database record."));
					$memberdata = $memb->getUserData($memberdata['membernum']);					// Refresh member data
					$topmsg = _("Account details updated.");
					$topmsgtype = "success";
				}
			}
			// ...and then let the page render normally
        }
        // Validation returned false. $myVal->errors now contains an array of error messages, keyed with the field name.
        $errors = $myVal->errors;
		// Replace $memberdata values with previously submitted values for display, so that the user won't have to retype
		// changes that did pass validation
		foreach($memberdata as $itemname=>$itemvalue) {
			if(!array_key_exists($itemname,$errors)) {		// We do not have an error for this key
				$memberdata[$itemname] = Input::getPost($itemname);		// Replace with posted value
			}
		}
		// Reset the submitted email to the hidden one, as the email must be validated first. But not if board member.
		if(!$memb->isBoard()) {
			$memberdata['email']=$oldemail;
		}
	}
	// At this point, all flow variables have been set, render the page.
        

?><!DOCTYPE html>
<html lang="<?=_("en")?>">
	<head>
	    <meta charset="utf-8"/>
	    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	    <meta name="description" content=""/>
	    <meta name="author" content=""/>
	    <meta name="robots" content="noindex,nofollow,noarchive,nosnippet,noodp,noydir,noimageindex,nomediaindex"/>
	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	    <meta http-equiv="Content-Language" content="<?=_("en-US")?>"/>
	    <link rel="shortcut icon" href="<?= Config::get('favicon') ?>"/>
	    <link href="css/flags.css" rel="stylesheet"/>
	    <title><?= sprintf(_("%s Member Pages"),Config::get('assocname')) ?></title>
	    <!-- Bootstrap core CSS -->
	    <link href="css/bootstrap.css" rel="stylesheet"/>
	    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	    <!--[if lt IE 9]>
	      <script src="js/html5shiv.js"></script>
	      <script src="js/respond.min.js"></script>
	    <![endif]-->
	    <link href="css/styles.css" rel="stylesheet"/>
	    <link href="css/bootstrap-datepicker.css" rel="stylesheet"/>
	    <script src="js/misc.js"></script>
	    <script src="js/jquery.js"></script>
	    <script src="js/bootstrap.min.js"></script>
        <script src="js/bootstrap-datepicker.js"></script>
        
	</head>
	<body onload="initStuff()">
<!-- Header -->
<?php
 include_once "includes/headerbar.php";
?>
<!-- /Header -->

<!-- Main -->
<div class="container">
<div class="row">
	<div class="col-md-2">
      <!-- Left column -->
<?php
        include_once "includes/navmenu.php";
?>  
            <h4><?=_("Submembers")?></h4>
            <ul class="list-unstyled collapse in" id="userMenu">
<?php
    if(is_null($memberdata["parent"]) || $memberdata["parent"]==0) {    // Only show submembers if you're not one yourself   

        
        if($memberdata["haschildren"]) {
            $submembers = $memb->getSubmembers($memberdata["membernum"]);
        	foreach($submembers as $submember) {
?>
                <li> <a href="member.php?membernum=<?php echo $submember["membernum"] ?>"><i class="glyphicon glyphicon-user"></i> <?php echo $submember["username"] ?></a></li>
<?php
            }
        }
?>
            </ul>
            <hr style="margin-top:5px;margin-bottom:5px"/>
                <ul class="list-unstyled collapse in" id="newUserMenu">
                
                <li> <a href="newmember.php?id=<?php echo $memberdata["membernum"] ?>"><i class="glyphicon glyphicon-plus"></i> <?=_("New member")?></a></li>
            </ul>
<?php        
    } else {
        $parentnum = $memb->getUserDetail($memberdata['parent'],'membernum');
        $parentuser = $memb->getUserDetail($memberdata['parent'],'username');
        if($memberdata["membernum"]==Session::getSessionVar('userID')) {
?>
                <div class="alert alert-info"> <?=sprintf(_("You are a submember of %s (member no. %s), so you cannot have your own submembers."),$parentuser,$parentnum)?></div>
<?php
        } else {
?>
                <div class="alert alert-info"> <?=sprintf(_("This is a submember of %s (member no. %s), which cannot have its own submembers."),$parentuser,$parentnum)?></div>
<?php
        }
    }
?>

      
  	</div><!-- /col-3 -->
    <div class="col-md-10">
<?php
if($isown) {
?>
      	<h3><?=_("My details")?></h3>
<?php
} else {
?>
        <h3><?=_("Details for")?> <?php echo $memberdata["username"] ?> (<?php echo $memberdata["firstname"]." ".$memberdata["middlename"]." ".$memberdata["lastname"] ?>)</h3>
<?php
}
?>
      <!-- column 2 -->	
		<div class="row">
<?php
if($topmsg) {
?>
                <div class="alert alert-<?= $topmsgtype ?> alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only"><?=_("Close")?></span></button>
                  <?= $topmsg ?>
                </div>
<?php
}
?>           
            
                <form class="form-horizontal" role="form" action="member.php" method="post">
                    <div class="form-group">
                        <label for="membernum" class="col-sm-3 control-label"><?=_("Member number")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="membernum" name="membernum" placeholder="" value="<?php if(isset($memberdata["membernum"])) {echo $memberdata["membernum"];} ?>" readonly/>
                        </div>
                    </div>
<?php
    $helptext =_("<div class='popoverhead'>Username in the member system.</div>Normally, it makes most sense if this is the same as your Geocaching name. You might however have reasons for choosing a different username.");
    if(isset($errors['username'])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors['username']."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="username" class="col-sm-3 control-label"><?=_("Username")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="username" name="username" placeholder="<?=_("Username")?>" value="<?php if(isset($memberdata["username"])) {echo $memberdata["username"];} ?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"/>
                            <?php echo $errormsg ?>
                        </div>
                    </div>
<?php
    $helptext = _("<div class='popoverhead'>Your e-mail address.</div>This is our primary communication channel, and required.");
    if(isset($errors['email'])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors['email']."</div>";} else {$errorstyle = ""; $errormsg = "";}
    // if(!is_null($memberdata["parent"]) && $memberdata["parent"]!=0) {
    //     // If this is a submember, the email field should be readonly
    //     $readonly = "readonly";
    // } else {
    //     $readonly = "";
    // }
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="email" class="col-sm-3 control-label"><?=_("E-mail address")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="email" name="email" placeholder="<?=_("E-mail address")?>" value="<?php if(isset($memberdata["email"])) {echo $memberdata["email"];} ?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"/>
                            <input type="hidden" id="emailin" name="emailin" value="<?php if(isset($memberdata["email"])) {echo $memberdata["email"];} ?>"/>
                            <?php echo $errormsg ?>
                        </div>
                    </div>
<?php
    $helptext = _("<div class='popoverhead'>Your geocaching name.</div>If you are a team, you have two options: Either register them later as &quot;belonging&quot; to this account, or create separate accounts for them. In either case, you can register either the same or different geocaching names for all members, or a combination like two people sharing one geocaching name, plus one having a different geocaching name.");
    if(isset($errors['gcname'])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors['gcname']."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="gcnick" class="col-sm-3 control-label"><?=_("Geocaching name")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="gcnick" name="gcnick" placeholder="<?=_("Geocaching name")?>" value="<?php if(isset($memberdata["gcnick"])) {echo $memberdata["gcnick"];} ?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"/>
                            <?php echo $errormsg ?>
                        </div>
                    </div>
<?php
    $helptext = _("<div class='popoverhead'>Your first name.</div>If your have more than one first name, all should go here.");
    if(isset($errors['firstname'])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors['firstname']."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="firstname" class="col-sm-3 control-label"><?=_("First name")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="firstname" name="firstname" placeholder="<?=_("First name")?>" value="<?php if(isset($memberdata["firstname"])) {echo $memberdata["firstname"];} ?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"/>
                            <?php echo $errormsg ?>
                        </div>
                    </div>
<?php
    $helptext = _("<div class='popoverhead'>Your middle name.</div>If you don&apos;t have one, leave this empty. This is displayed between your first and last names where full name is used.");
    if(isset($errors['middlename'])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors['middlename']."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="middlename" class="col-sm-3 control-label"><?=_("Middle name")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="middlename" name="middlename" placeholder="<?=_("Middle name")?>" value="<?php if(isset($memberdata["middlename"])) {echo $memberdata["middlename"];} ?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"/>
                            <?php echo $errormsg ?>
                        </div>
                    </div>                    
<?php
    $helptext = _("<div class='popoverhead'>Your last name.</div>If you have more than one last name, the one that the name should be sorted by, should go here, and the others under &quot;Middle name&quot;.");
    if(isset($errors['lastname'])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors['lastname']."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="lastname" class="col-sm-3 control-label"><?=_("Last name")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="lastname" name="lastname" placeholder="<?=_("Last name")?>" value="<?php if(isset($memberdata["lastname"])) {echo $memberdata["lastname"];} ?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"/>
                            <?php echo $errormsg ?>
                        </div>
                    </div>                    
<?php
    $helptext = _("<div class='popoverhead'>Your address.</div>We need this in situations where we need to send ordered products or giveaways to the members. You can use the enter key to type multiple lines.");
    if(isset($errors['address'])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors['address']."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="address" class="col-sm-3 control-label"><?=_("Address")?></label>
                        <div class="col-sm-9">
                            <textarea style="height:75px" class="form-control" id="address" name="address" placeholder="<?=_("Address")?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"><?php if(isset($memberdata["address"])) {echo $memberdata["address"];} ?></textarea>
                            <?php echo $errormsg ?>
                        </div>
                    </div> 
<?php
    $helptext = _("<div class='popoverhead'>Your phone number.</div>We need this as backup for the e-mail, if e-mail doesn&apos;t get through.");
    if(isset($errors['phone'])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors['phone']."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="phone" class="col-sm-3 control-label"><?=_("Phone number")?></label>
                        <div class="col-sm-9">
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="<?=_("Phone number")?>" value="<?php if(isset($memberdata["phone"])) {echo $memberdata["phone"];} ?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"/>
                            <?php echo $errormsg ?>
                        </div>
                    </div> 
<?php
    $helptext = _("<div class='popoverhead'>Your birth date.</div>We need this for two reasons: Knowing how many members we have in each age segment, which is needed for some kinds of funding. Also, there is an age limit for membership fees.");
    if(isset($errors['birthdate'])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors['birthdate']."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="birthdate" class="col-sm-3 control-label"><?=_("Birth date")?></label>
                        <div class="col-sm-4">
                            <div class="input-group date" id="dp1" data-date="<?php if(isset($memberdata["birthdate"])) {echo Misc::formatDate($memberdata["birthdate"]);} ?>" data-date-format="<?= Config::get('altdateformat') ?>">
                                <input class="form-control" type="text" readonly="readonly" id="birthdate" name="birthdate" value="<?php if(isset($memberdata["birthdate"])) {echo Misc::formatDate($memberdata["birthdate"]);} ?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"/>
                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            </div>
                            <?php echo $errormsg ?>
                        </div>
                    </div>
<?php
    $helptext = _("<div class='popoverhead'>Your position.</div>If you have any position in the association, it will be indicated here.");
?>
                    <div class="form-group">
                        <label for="position" class="col-sm-3 control-label"><?=_("Position")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="position" name="position" placeholder="<?=_("Position")?>" value="<?php if(isset($memberdata["position"])) {echo $memberdata["position"];} ?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>" <?php if(!$memb->isBoard()) {echo "readonly";} ?>/>
                        </div>
                    </div>
<?php
    $helptext = _("<div class='popoverhead'>The date you registered.</div>For your information only.");
?>
                    <div class="form-group">
                        <label for="position" class="col-sm-3 control-label"><?=_("Member since")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="membersince" name="membersince" placeholder="<?=_("Member since")?>" value="<?php if(isset($memberdata["membersince"])) {echo Misc::formatDate($memberdata["membersince"]);} ?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>" readonly/>
                        </div>
                    </div>
<?php
    $helptext = sprintf(_("<div class='popoverhead'>Comments.</div>Type in any messages to %s here. For example, details about who is included in your nick. Only visible to you and the board members."),Config::get('assocname'));
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="address" class="col-sm-3 control-label"><?=_("User comment")?></label>
                        <div class="col-sm-9">
                            <textarea style="height:75px" class="form-control" id="usercomment" name="usercomment" placeholder="<?=_("User comment")?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"><?php if(isset($memberdata["usercomment"])) {echo $memberdata["usercomment"];} ?></textarea>
                        </div>
                    </div> 
<?php
    if($memb->isBoard()) {
    $helptext = _("<div class='popoverhead'>Board comment.</div>This is only visible to board members - not even the member can see this. Can be used to hold important info about the member, such as Ã‚Â«has paid fee for 2014-2016Ã‚Â»");
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="address" class="col-sm-3 control-label" style="color:red"><?=_("Board comment")?></label>
                        <div class="col-sm-9">
                            <textarea style="height:75px;background-color:#FFF0F0" class="form-control" id="boardcomment" name="boardcomment" placeholder="<?=_("Board comment")?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"><?php if(isset($memberdata["boardcomment"])) {echo $memberdata["boardcomment"];} ?></textarea>
                        </div>
                    </div> 

                    <div class="form-group">
                        <label for="position" class="col-sm-3 control-label"></label>
                        <div class="col-sm-9">
                            <input type="checkbox"  id="boardmember" name="boardmember" <?php if(isset($memberdata["boardmember"])) { if($memberdata["boardmember"]) {echo "checked";}} ?>/> &nbsp;&nbsp;<label for="boardmember" style="font-weight:normal;margin:0px"><?=_("Board member")?></label>
                        </div>
                    </div>
<?php
    }
?>
                   <div class="form-group">
                        <label for="pubemail" class="col-sm-3 control-label"><?=_("Privacy")?></label>
                        
                        <div class="col-sm-9">
                            <strong><?=_("Only the details with a check mark will be visible to other members.")?></strong><br/>
                            <input type="checkbox"  id="pubemail" name="pubemail" <?php if(isset($memberdata["pubemail"])) {if($memberdata["pubemail"]=="on"||$memberdata["pubemail"]==1) {echo "checked";}} ?> /> &nbsp;&nbsp;<label for="pubemail" style="font-weight:normal;margin:0px"><?=_("E-mail address")?></label><br/>
                            <input type="checkbox"  id="pubfirstname" name="pubfirstname" <?php if(isset($memberdata["pubfirstname"])) {if($memberdata["pubfirstname"]=="on"||$memberdata["pubfirstname"]==1) {echo "checked";}} ?>/> &nbsp;&nbsp;<label for="pubfirstname" style="font-weight:normal;margin:0px"><?=_("First name")?></label><br/>
                            <input type="checkbox"  id="pubmiddlename" name="pubmiddlename" <?php if(isset($memberdata["pubmiddlename"])) {if($memberdata["pubmiddlename"]=="on"||$memberdata["pubmiddlename"]==1) {echo "checked";}} ?>/> &nbsp;&nbsp;<label for="pubmiddlename" style="font-weight:normal;margin:0px"><?=_("Middle name")?></label><br/>
                            <input type="checkbox"  id="publastname" name="publastname" <?php if(isset($memberdata["publastname"])) {if($memberdata["publastname"]=="on"||$memberdata["publastname"]==1) {echo "checked";}} ?>/> &nbsp;&nbsp;<label for="publastname" style="font-weight:normal;margin:0px"><?=_("Last name")?></label><br/>
                            <input type="checkbox"  id="pubaddress" name="pubaddress" <?php if(isset($memberdata["pubaddress"])) {if($memberdata["pubaddress"]=="on"||$memberdata["pubaddress"]==1) {echo "checked";}} ?>/> &nbsp;&nbsp;<label for="pubaddress" style="font-weight:normal;margin:0px"><?=_("Address")?></label><br/>
                            <input type="checkbox"  id="pubphone" name="pubphone" <?php if(isset($memberdata["pubphone"])) {if($memberdata["pubphone"]=="on"||$memberdata["pubphone"]==1) {echo "checked";}} ?>/> &nbsp;&nbsp;<label for="pubphone" style="font-weight:normal;margin:0px"><?=_("Phone number")?></label><br/>
                            <input type="checkbox"  id="pubbirthdate" name="pubbirthdate" <?php if(isset($memberdata["pubbirthdate"])) {if($memberdata["pubbirthdate"]=="on"||$memberdata["pubbirthdate"]==1) {echo "checked";}} ?>/> &nbsp;&nbsp;<label for="pubbirthdate" style="font-weight:normal;margin:0px"><?=_("Birth date")?></label><br/>
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="hidden" id="parent" name="parent" value="<?php if(isset($memberdata["parent"])) { echo $memberdata["parent"];} else {echo "0";} // When updating details, parent is not set ?>"/>
                        <input type="hidden" id="source" name="source" value="member.php"/>
                        <button type="submit" class="btn btn-primary form-control"><?=_("Update account details")?></button>
                    </div>
                </form>
          

     
      </div><!--/row-->
  	</div><!--/col-span-9-->
</div>
</div>
<!-- /Main -->
<hr>
<?php include "includes/footer.php"; ?>
    <!-- script references -->
		

    <script>

$.fn.datepicker.dates['nb'] = {days:["søndag","mandag","tirsdag","onsdag","torsdag","fredag","lørdag"],
            daysShort:["søn","man","tir","ons","tor","fre","lør"],daysMin:["sø","ma","ti","on","to","fr","lø"],
            months:["januar","februar","mars","april","mai","juni","juli","august","september","oktober","november","desember"],
            monthsShort:["jan","feb","mar","apr","mai","jun","jul","aug","sep","okt","nov","des"],
            today:"I dag",
            clear:"Nullstill",
            weekStart:1,
            format:"dd.mm.yyyy"};
$('#dp1').datepicker({
    weekStart: 1,
    startView: 2,
    language: "<?= _("en") ?>",
    autoclose: true
});

    </script>
	</body>
</html>