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
            <h1><?=_("Privacy")?></h1>
<?php
$bodytext = sprintf(_("Any personal details collected by this website, is solely for the use by the board of %s, except for details "),Config::get('assocname')).
            _("the user itself has decided to make public.<br/><br/>").
            sprintf(_("%s will not use this information for any other purpose than managing your membership."),Config::get('assocname')).
            _("If you choose to use the &quot;Remember me&quot; functionality, a cookie will be used to facilitate that ").
            _("functionality. This cookie will contain: ").
            "<ul>".
            _("<li>Your username</li>").
            _("<li>A hash representing your password (your password cannot be deduced from this hash)</li>").
            "</ul>";
                
                echo $bodytext;
?>
        </div>
    </div>
<hr>
<?php include "includes/footer.php"; ?>
  </body>
</html>
