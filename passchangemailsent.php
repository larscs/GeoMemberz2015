<?php 
    require_once('core/init.php');
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
            <h1><?= _("Confirmation mail sent")?></h1>
            <?= _("An e-mail has been sent to the e-mail address associated with the account. ").
            _("Check your e-mail and follow the instructions in that e-mail to change ").
            _("your password.<br/><br/>").
            _("If you haven't received the e-mail within a few minutes, check your spam ").
            _("filter. If it's not there, please contact us at")?> <a href="mailto:<?= Config::get('autoemail') ?>"><?= Config::get('autoemail') ?></a>.
        </div>
    </div>
<hr>
<?php include "includes/footer.php"; ?>
  </body>
</html>
