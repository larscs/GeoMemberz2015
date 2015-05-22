<?php
    require_once('core/init.php');
    if(Input::exists('post')) {
        // We have POST data
        $rm = (Input::getPost('rememberme')) ? true : false ;
        $myVal = new Validation;
        if($myVal->checkUserPass(Input::getPost('username'),Input::getPost('password'))) {
            //We have both username and password, so try logging in
            //Instantiate session class so we can get the error message
            $mySession = new Session;
            if(!$mySession->login(Input::getPost('username'),Input::getPost('password'),$rm)) {
                // The login method will end with a redirection if successful, so we only get here
                // when the method returns an error. Make sure it is displayed.
                $error = $mySession->errormsg;
            }
        } else {
            // Validation didn't pass, get error message
            $error = $myVal->errormsg;
        }
    }

?>
<!DOCTYPE html>
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
                    <h3><?= _("Member pages") ?></h3>

                  <?= Session::getFlags($_GET) ?>
                    <hr/>
                </div>
                <form class="form" role="form" action="" method="post">
                    <div class="form-group">
                        <label for="username"><?= _("Username")?></label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" <?php if($usrname = Input::getPost('username')) {echo " value=\"".$usrname."\"";} else {echo " autofocus=\"autofocus\"";} ?>/>
                    </div>
                    <div class="form-group">
                        <label for="password"><?= _("Password")?></label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" <?php if($usrname = Input::getPost('username')) {echo " autofocus=\"autofocus\"";} ?>/>
                    </div>
                    <div class="form-group">
                        <div class="checkbox" style="float:left;margin-top:0px">
                        <label>
                            <input type="checkbox" id="rememberme" name="rememberme"/> <?= _("Remember me")?>
                        </label>
                        </div>
                        <div style="float:right;margin-top:0px">
                        <a href="forgotpass.php"><?= _("Forgot your password?")?></a>
                        </div>
                    </div>
<?php
    if(isset($error)) {
?>
                    <div class="alert alert-danger alert-dismissible" role="alert" style="clear:both">
                      <?php echo $error ?>
                    </div>
<?php
}

?>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary form-control"><?= _("Log in")?></button>
                    </div>
                    <div class="form-group">
                        <label for="username"><?= _("Not a member?")?></label>
                        <a href="register.php" class="btn btn-success form-control"><?= _("Register now")?></a>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <?php include "includes/footer.php"; ?>

  </body>
</html>
