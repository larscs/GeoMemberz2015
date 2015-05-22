<?php

	require_once $_SERVER["DOCUMENT_ROOT"].'/core/init.php';                         // Must be present on all pages using settings and classes
    $creationok = false;
    $errorencountered = false;
    if(Input::exists('post')) {
        $sqlhost = Input::getPost('sqlhost');
        $sqlport = Input::getPost('sqlport');
        $sqldb = Input::getPost('sqldb');
        $sqluser = Input::getPost('sqluser');
        $sqlpass = Input::getPost('sqlpass');
        $sqlprefix = Input::getPost('sqlprefix');       
		$dbh = new PDO("mysql:host={$sqlhost};".
                              "port={$sqlport};".
							  "dbname=$sqldb;".
							  "charset=utf8",
							   "{$sqluser}",
							   "{$sqlpass}");
        // Now enumerate the sql files and replace all %sqlprefix% with the provided prefix, before executing them.
        $sqlfiles = scandir('sql/');
        foreach($sqlfiles as $sqlfile) {
            if ($sqlfile != '.' && $sqlfile != '..') {
                $filecontents = file_get_contents('sql/'.$sqlfile);
                $filecontents = str_replace("%sqlprefix%",$sqlprefix,$filecontents);
                $result=$dbh->exec($filecontents);
                if($result) $errorencountered = true;
            }
        }
        
    }
    if(!$errorencountered) {
        $creationok = true;
    }
    
?><!DOCTYPE html>
<html lang="nb">
	<head>
	    <meta charset="utf-8"/>
	    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	    <meta name="description" content=""/>
	    <meta name="author" content=""/>
	    <meta name="robots" content="noindex,nofollow,noarchive,nosnippet,noodp,noydir,noimageindex,nomediaindex"/>
	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	    <meta http-equiv="Content-Language" content="nb-NO"/>
	    <link rel="shortcut icon" href="favicon.png"/>
	    <link href="../css/flags.css" rel="stylesheet"/>
	    <title><?=_("GeoMemberz Installer")?></title>
	    <!-- Bootstrap core CSS -->
	    <link href="../css/bootstrap.css" rel="stylesheet"/>
	    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	    <!--[if lt IE 9]>
	      <script src="js/html5shiv.js"></script>
	      <script src="js/respond.min.js"></script>
	    <![endif]-->
	    <link href="../css/styles.css" rel="stylesheet"/>
	    <link href="../css/bootstrap-datepicker.css" rel="stylesheet"/>
	    <script src="../js/misc.js"></script>
	    <script src="../js/jquery.js"></script>
	    <script src="../js/bootstrap.min.js"></script>
        <script src="../js/bootstrap-datepicker.js"></script>
        
	</head>
	<body onload="initStuff()">
<!-- Header -->
<div id="top-nav" class="navbar navbar-inverse navbar-static-top">
  <div class="container">
    <div class="navbar-header">
      <a href="../"><img src="../<?= Config::get('logo30')?>" alt="Logo" style="float:left;margin-top:11px;margin-right:10px"/></a>
      <p class="navbar-brand" style="margin-top:5px;cursor:default"><?=_("Installation")?></p>
    </div>

  </div><!-- /container -->
</div><!-- /Header -->

<!-- Main -->
<div class="container">
<div class="row">
	<div class="col-md-2">
      <!-- Left column -->

            <h4><?=_("Progress")?></h4>
            <ul class="list-unstyled collapse in" id="userMenu" style="font-size:0.96em">

                <li><i class="glyphicon glyphicon-ok" style="color:green"></i> <?=_("Connection")?></li>
                <li><i class="glyphicon glyphicon-ok" style="color:green"></i> <?=_("Database setup")?></li>
                <li><i class="glyphicon glyphicon-play" style="color:red"></i> <strong><?=_("General settings")?></strong></li>
                <li><i class="glyphicon glyphicon-play" style="color:transparent"></i> <?=_("E-mail settings")?></li>
            </ul>
      
  	</div><!-- /col-3 -->
    <div class="col-md-10">
    <div style="text-align:center"><img src="geomemberz.png" alt="GeoMemberz-logo"/></div>
    <br/>
<?php
    if(!$creationok) {
        echo _("<p><strong>Creating the database content failed.</strong><br/>Please contact us for help.</p>");
    } else {
        echo _("<p><strong>Database content successfully created.</strong></p><p>The next step is configuring GeoMemberz to suit your particular needs. Please note that these settings are not validated, and will be written to the config file exactly as you type them, after the next page. Make sure you use the correct data type if you change from the default values.</p>");
?>
      <!-- column 2 -->	
		<div class="row">
           
            
                <form class="form-horizontal" role="form" action="install3.php" method="post">
                    <h5><?=_("Prefixes")?></h5>
                    <div class="form-group">
                        <label for="sessionprefix" class="col-sm-3 control-label"><?=_("Session prefix")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="sessionprefix" name="sessionprefix" placeholder="<?=_("Session prefix")?>" value="<?=$sqlprefix?>"
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("The prefix for session variables")?></div><?=_("This will be used as a prefix for all session variables. It must be unique for each GeoMemberz instance running on the same host.")?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cookieprefix" class="col-sm-3 control-label"><?=_("Cookie prefix")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="cookieprefix" name="cookieprefix" placeholder="<?=_("Cookie prefix")?>" value="<?=$sqlprefix?>"
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("The prefix for cookies")?></div><?=_("This will be used as a prefix for all cookies. It must be unique for each GeoMemberz instance running on the same host.")?>"/>
                        </div>
                    </div>
                    
                    <h5><?=_("Timeouts")?></h5>
                    <div class="form-group">
                        <label for="cookieexpiry" class="col-sm-3 control-label"><?=_("Cookie expiry")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="cookieexpiry" name="cookieexpiry" placeholder="<?=_("Cookie expiry")?>" value="31622400"
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("The expiry time for cookies")?></div><?=_("This is the cookie expiry time, in seconds. One year is 31622400 seconds. After this time, users using the <em>Remember me</em> feature will have to log in again.")?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="passwordexpiry" class="col-sm-3 control-label"><?=_("Password change timeout")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="passwordexpiry" name="passwordexpiry" placeholder="<?=_("Password change timeout")?>" value="86400"
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("The timeout for password change")?></div><?=_("After this amount of time, in seconds, the authorization for a password change will expire, and the link in the password change e-mail will no longer work. One day is 86400 seconds.")?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="validationexpiry" class="col-sm-3 control-label"><?=_("Validation timeout")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="validationexpiry" name="validationexpiry" placeholder="<?=_("Validation timeout")?>" value="86400"
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("The timeout for validation")?></div><?=_("After this amount of time, in seconds, the validation token will expire, and the link in the validation e-mail will no longer work. One day is 86400 seconds.")?>"/>
                        </div>
                    </div>

                   <h5><?=_("Organization details")?></h5>
                    <div class="form-group">
                        <label for="assocname" class="col-sm-3 control-label"><?=_("Organization name")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="assocname" name="assocname" placeholder="<?=_("Organization name")?>" value=""
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("The organization name")?></div><?=_("This is the name of your membership organization. It is used throughout the system, including in e-mails.")?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="accountno" class="col-sm-3 control-label"><?=_("Bank account number")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="accountno" name="accountno" placeholder="<?=_("Bank account number")?>" value=""
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("The bank account number")?></div><?=_("This is the organizations bank account number. It is used anywhere the members are asked to pay their membership fees, including in e-mails.")?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="agelimit" class="col-sm-3 control-label"><?=_("Fee age limit")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="agelimit" name="agelimit" placeholder="<?=_("Fee age limit")?>" value="16"
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("The fee age limit")?></div><?=_("This is the minimum age from which members must pay a membership fee. If you don't have an age limit, simply specify 0.")?>"/>
                        </div>
                    </div>                                        
                    <div class="form-group" style="float:right;margin-right:0px">
                        <input type="hidden" name="sqlhost" value="<?= $sqlhost ?>"/>
                        <input type="hidden" name="sqlport" value="<?= $sqlport ?>"/>
                        <input type="hidden" name="sqldb" value="<?= $sqldb ?>"/>
                        <input type="hidden" name="sqluser" value="<?= $sqluser ?>"/>
                        <input type="hidden" name="sqlpass" value="<?= $sqlpass ?>"/>
                        <input type="hidden" name="sqlprefix" value="<?= $sqlprefix ?>"/>
                        <button type="submit" class="btn btn-primary"><?=_("Next >")?><i class="glyphicon glyphicon-triangle-right"></i></button>
                    </div>
                </form>
      </div><!--/row-->
<?php
    }
?>
  	</div><!--/col-span-9-->
</div>
</div>
<!-- /Main -->
<hr>
	<footer class="text-center" style="margin-right:40px">
		<img src="../img/geomemberz150w.png" alt="GeoMemberz" style="margin-bottom:15px"/>
		<span style=""><em><?=_("Version")?> 1.1</em> &bullet; &copy; 2015 <a href="http://www.geobergen.no/">GeoBergen</a></span><br />
		<a href="tou.php"><?=_("Terms of Use")?></a> | <a href="privacy.php"><?=_("Privacy")?></a>
		&bullet; <?=_("Built using")?>
		<a href="http://getbootstrap.com/">Bootstrap</a>, 
		<a href="https://github.com/eternicode/bootstrap-datepicker">Bootstrap Datepicker</a>,
		<a href="http://jquery.com/">jQuery</a>,
		<a href="http://www.openwall.com/phpass/">phpass</a> <?=_("and")?> 
		<a href="http://phpmailer.worxware.com/">PHPMailer</a>
	</footer>    <!-- script references -->
	</body>
</html>