<?php
    require_once('core/init.php');
    if(Input::exists('post')) {
    	// New password submission. We checked the hash when displaying the form, so now we only need to
    	// check that we have two identical passwords before hashing that and updating DB.
    	if(!$pchash=Input::getPost('hash')) {
    		// If the hash isn't there, display an error page
    			Session::errorPage(_("Error"),_("Hash missing.<br/>If this error persists, please contact us on ")."<a href=\"mailto:".Config::get('autoemail')."?subject="._("Invalid hash when changing password")."\">".Config::get('autoemail')."</a>.");	
    	}
    	$error = false;
    	if(!Input::getPost('password')) {
    		// Password not provided
			$error = true;
    		$errmsg = _("<strong>Error:</strong> No password entered.");
    	}
    	if(!Input::getPost('reppassword')) {
    		// Repeat password not provided
			$error = true;
			if(!isset($errmsg)){$errmsg = "";} else {$errmsg .= "<br/>";}
    		$errmsg .= _("<strong>Error:</strong> No repeat password entered.");
    	}
    	if(Input::getPost('password') != Input::getPost('reppassword') && !$error) {
    		// Passwords don't match (don't add this to message about missing fields, obviously they don't match then, thus !$error)
			$error = true;
			if(!isset($errmsg)){$errmsg = "";} else {$errmsg .= "<br/>";}
    		$errmsg .= _("<strong>Error:</strong> Passwords don't match.");
    	}
    	if(!$error) {
    		// Input validated and passwords equal; set new PW hash, set passchangehash to NULL, passchangeok to 0
    		// First make the hash
    		$t_hasher = new PasswordHash(6, true);
    		$newpwhash = $t_hasher->HashPassword(Input::getPost('password'));
    		$memb = Members::getInstance();
    		$memb->setFieldValue('password',$newpwhash,'passchangehash',$pchash);
    		$memb->setFieldValue('passchangeok',0,'passchangehash',$pchash);
    		$memb->setFieldValue('passchangehash',NULL,'passchangehash',$pchash);
    		Session::redirect('passchanged.php');
    	}

    }
    if(Input::exists('get')) {
    	// We have GET data, find out if it is a hash
    	if($pchash = Input::getGet('h')) {
    		// GET data was a hash, check if hash is valid
    		$memb = Members::getInstance();
    		if($memb->getFieldValue('passchangehash','passchangehash',$pchash)) {
    			// The hash existed once in the DB, so OK to proceed.
    			if($memb->getFieldValue('passchangeok','passchangehash',$pchash)) {
    				// The passchangeOK flag was set in DB, so OK to proceed.
    			} else {
    				// passchangeOK flag was not set in DB. Show error message
    			Session::errorPage(_("Error"),_("Invalid hash context.<br/>If this error persists, please contact us on ")."<a href=\"mailto:".Config::get('autoemail')."?subject="._("Invalid hash when changing password")."\">".Config::get('autoemail')."</a>.");	
    			}
    		} else {
    			// The hash either didn't exist in the DB or was in multiple places (statistically impossible)
    			Session::errorPage(_("Error"),_("Invalid hash. The confirmation link may have been used already.<br/>If this error persists, please contact us on ")."<a href=\"mailto:".Config::get('autoemail')."?subject="._("Invalid hash when changing password")."\">".Config::get('autoemail')."</a>.");	
    		}
    	} else {
    		// GET data was not a hash, display error message
    		Session::errorPage(_("Error"),_("No hash provided"));
    	}
    	// If we get here, script finishes and renders page with $pchash
    }


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
    <link href="css/loginpage.css" rel="stylesheet"/>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/misc.js"></script>
    <script src="js/sorttable.js"></script>    
  </head>

  <body>

    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 loginbox">
                <div style="text-align:center">
                    <img src="<?= Config::get('logo50') ?>" alt="<?= Config::get('assocname')._(" logo")?>"/><br/>
                    <h3><?=_("Member pages")?></h3>
                    <h4><?=_("Reset password")?></h4>

                  <?= Session::getFlags($_GET) ?>
                    <hr/>
                </div>
                <form role="form" action="setpass.php" method="post">
                    <div class="alert alert-success" role="alert" style="clear:both">
                      <?=_("Please type your new password twice.")?>
                    </div>
                    <div class="form-group">
                        <label for="password"><?=_("Password")?></label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="<?=_("Password")?>" autofocus="autofocus"/>                    </div>
                    <div class="form-group">
                        <label for="reppassword"><?=_("Repeat password")?></label>
                        <input type="password" class="form-control" id="reppassword" name="reppassword" placeholder="<?=_("Repeat password")?>"/>
                        <input type="hidden" name="hash" id="hash" value="<?= $pchash ?>"/>
                    </div>

<?php

    if(isset($error)) {
?>
                    <div class="alert alert-danger" role="alert" style="clear:both">
                      <?php echo $errmsg ?>
                    </div>
<?php
}
?>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary form-control"><?=_("Reset password")?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<hr />
<?php include "includes/footer.php"; ?>
  </body>
</html>