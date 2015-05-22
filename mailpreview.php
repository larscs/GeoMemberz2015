<?php
	require_once 'core/init.php';                         // Must be present on all pages using settings and classes
	if(!Session::isLoggedIn()) {Session::redirect(404);}  // Use this on all pages requiring the user to be logged in
	$memb = Members::getInstance(); 			            // Use this on all pages using the database
	if(!$memb->isBoard()) {Session::redirect(404);}			// Non-members don't need to know this page exists.
	if(Input::exists('get')) {
		if(!Input::getGet('id')) {
			echo _("Error: No ID given<br/>");
		}
		if(!Input::getGet('subj') || !Input::getGet('body')) {
			echo "<center><em>"._("Subject and body must both contain something.")."</em></center>";
			exit;
		}
		// All present, continue
		$id = Input::getGet('id');
		$subj = Input::getGet('subj');
		$subj = $memb->replaceMailVars($id,$subj,false);
		$body = Input::getGet('body');
		$body = $memb->replaceMailVars($id,$body,true);
		$htmltop = file_get_contents('includes/mailtop_preview.html');
		$htmlbottom = file_get_contents('includes/mailbottom.html');

		echo "<strong>"._("Subject:")."</strong> ".$subj."<br/><br/>";
		echo "<strong>"._("Body:")."</strong><br/><div style=\"border-top:black solid 1px\"></div>";
		//echo "<pre style=\"border:none;padding:2px\">".$body."</pre>";
		echo $htmltop.$body.$htmlbottom;
	} else {
		echo _("Error: No input");
	}
?>