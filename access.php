<?php
	require_once "core/init.php";
	$memb = Members::getInstance();
    if(Input::exists('get')) {
		// We have input, check if ID is numeric
		if(is_numeric(Input::getGet('id'))) {
			// ID is numeric, switch on action arg
			switch(Input::getGet('action')) {
				case "add":
					if(Input::getGet('mail')==1) {
						// Send mail about access
						$memberdetails =  $memb->getUserData(Input::getGet('id'));
			            $userfullname = $memberdetails['firstname']." ".$memberdetails['middlename']." ".$memberdetails['lastname'];
						$userfullname = str_replace("  "," ",$userfullname); // In case middle name is not used
			            $altbody=sprintf(_("Hi, %s!"),$memberdetails['username'])."\r\n\r\n".
			                     sprintf(_("You now have access to the member forums in %s."),Config::get('assocname'))."\r\n".
			                      		 _("You'll find them under the subforum «Foreningen» on {{LINK}}.")."\r\n\r\n".
			                      		 _("Best regards,")."\r\n".
			                      		 Config::get('assocname');
						$body = str_replace("\r\n","<br/>",$altbody);
						$altbody=str_replace("{{LINK}}","http://forum.geobergen.no/",$altbody);
						$body = str_replace("{{LINK}}",'<a href="http://forum.geobergen.no/">http://forum.geobergen.no/</a>',$body);

						$memb->mailUser(Config::get('autoemail'), Config::get('autoemailname'), $memberdetails['email'], $userfullname,sprintf(_("Member access on the %s forum"),Config::get('assocname')), $body, $altbody);
					}
					//Update database
					$memb->setFieldValue('access',1,'membernum',Input::getGet('id'));
					break;

				case "remove":
					$memb->setFieldValue('access',0,'membernum',Input::getGet('id'));
					break;
			}
		}
    }
    // Redirect user to memberlist
    if(Input::getGet('scrollpos')) {
        Session::setSessionVar('scrollpos',Input::getGet('scrollpos'));
    } else {
    	Session::clearSessionVar('scrollpos');
    }
    if(Input::getGet('sortkey')) {
        Session::setSessionVar('sortkey',Input::getGet('sortkey'));
    } else {
    	Session::clearSessionVar('sortkey');
    }
    if(Input::getGet('inact')) {
        Session::setSessionVar('inact',Input::getGet('inact'));
    } else {
    	Session::clearSessionVar('inact');
    }
    Session::redirect('members.php');