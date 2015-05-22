<?php
	require_once 'core/init.php';
    if(!Input::exists('get')) {
     	// No GET data at all, show 404 page
    	Session::redirect(404);
    }   	
	if(!$valhash=Input::getGet('h')) {
		// If the hash isn't there, display an error page
			Session::errorPage(_("Error"),_("Hash missing.<br/>If this error persists, please contact us on ")."<a href=\"mailto:".Config::get('autoemail')."?subject="._("Invalid hash when changing e-mail address")."\">".Config::get('autoemail')."</a>.");	
	}
	// Check valhash against DB.
	// GET data was a hash, check if hash is valid
	$memb = Members::getInstance();
	if(!$memb->getFieldValue('validationhash','validationhash',$valhash)) {
		// The hash either didn't exist in the DB or was in multiple places (statistically impossible)
		Session::errorPage(_("Error"),_("Invalid hash. The validation link may have been used already.<br/>If this error persists, please contact us on ")."<a href=\"mailto:".Config::get('autoemail')."?subject="._("Invalid hash when changing e-mail address")."\">".Config::get('autoemail')."</a>.");	
  }
	// Check that the new e-mail address is present
  if(!$newpw=$memb->getFieldValue('newemail','validationhash',$valhash)) {
    Session::errorPage(_("Error"),_("Missing e-mail address. Something may have gone wrong.<br/>Please contact us on ")."<a href=\"mailto:".Config::get('autoemail')."?subject="._("Missing e-mail address when trying to validate")."\">".Config::get('autoemail')."</a>."); 
  }		
	// Everything OK, so accept the e-mail change by setting the new e-mail address, clearing the newemail field
  // and finally removing the identifying validation hash.
  $memb->setFieldValue('email',$newpw,'validationhash',$valhash);
  $memb->setFieldValue('newemail',NULL,'validationhash',$valhash);
	$memb->setFieldValue('validationhash',NULL,'validationhash',$valhash);
	// ... and display the page:

?><!DOCTYPE html>
<html lang="<?= _("en")?>">
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
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </head>

  <body>
<!-- Header -->

<?php
 include_once "includes/headerbarbare.php";
?>
<!-- /Header -->
    <div class="container">
        <div class="row">
            <h1><?=_("Great!")?></h1>
            <?=sprintf(_("Your e-mail address has been validated. Now you can <a href=\"%slogin.php\">log in</a>."),Config::get('baseaddr'))?>            
        </div>
    </div>
<hr />
<?php include "includes/footer.php"; ?>
  </body>
</html>