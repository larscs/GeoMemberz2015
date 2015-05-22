<?php
    require_once('core/init.php');
    if(Input::exists('post')) {
        // We have POST data
        $myVal = new Validation;
        if($myVal->checkUserMail(Input::getPost('username'),Input::getPost('email'))) {
        	// We have a valid username and a e-mail
        	$memb = Members::getInstance();
        	if($userID = $memb->checkUserMail(Input::getPost('username'),Input::getPost('email'))) {
        		// Generate hash and e-mail user	
        		$memb = Members::getInstance();
        		if($memb->startPassChange($userID)){
        			Session::redirect('passchangemailsent.php');
        		}
        		$error = _("<strong>Error:</strong> Sending verification mail failed.");
        	} else {
        		echo "userid: $userID";
        		// User doesn't exist
        		$error = _("<strong>Error:</strong> Username or e-mail is incorrect.");
        	}

        } else {
            // Validation didn't pass, get error message
            $error = $myVal->errormsg;
        }
    }

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
                    <img src="img/logo.png" alt="<?= sprintf(_("%s logo"),Config::get('assocname'))?>"/><br/>
                    <h3><?= _("Member pages")?></h3>
                    <h4><?= _("Reset password")?></h4>

                  <?= Session::getFlags($_GET) ?>
                    <hr/>
                </div>
                <form role="form" action="" method="post">
                    <div class="alert alert-success" role="alert" style="clear:both">
                    <?= _("To reset your password, you must know both the username and the e-mail address associated with the account. You will receive an e-mail to that address containing a link and instructions on how to change your password.")?>
                    </div>
                    <div class="form-group">
                        <label for="email"><?= _("Username")?></label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="<?= _("Username")?>" value="<?php if(filter_input(INPUT_POST,"username")) {echo filter_input(INPUT_POST,"username");} ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="email"><?= _("E-mail address")?></label>
                        <input type="text" class="form-control" id="email" name="email" placeholder="<?= _("E-mail address")?>" value="<?php if(filter_input(INPUT_POST,"email")) {echo filter_input(INPUT_POST,"email");} ?>"/>
                    </div>

<?php
    if(isset($error)) {
?>
                    <div class="alert alert-danger" role="alert" style="clear:both">
                      <?php echo $error ?>
                    </div>
<?php
}
?>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary form-control"><?= _("Request password change e-mail")?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
	<?php include "includes/footer.php"; ?>

  </body>
</html>
