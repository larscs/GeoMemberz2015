<?php
	require_once 'core/init.php';
	if(!Session::isLoggedIn()) {Session::redirect(404);}  	// Use this on all pages requiring the user to be logged in
	$memb = Members::getInstance(); 			            // Use this on all pages using the database
	if(!$memb->isBoard()) {Session::redirect(404);}			// If not a board member, this page should not be known to exist.
	if(!Input::exists('get')) {Session::errorPage(_("Error"),_("One or more arguments are required, none found."));}
	if(!Input::getGet('id')) {Session::errorPage(_("Error"),_("One or more arguments are invalid."));}
	if(!is_numeric(Input::getGet('id'))) {Session::errorPage(_("Error"),_("An argument that should be numeric, wasn't."));}
	if(!$memb->setFieldValue('active',0,'membernum',Input::getGet('id'))) {Session::errorPage(_("Database error"),_("An error ocurred while updating a database record."));}
	// No errors, send back to members page
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