<?php

	require_once $_SERVER["DOCUMENT_ROOT"].'/core/init.php';                         // Must be present on all pages using settings and classes
    if(Input::exists('post')) {
        $sqlhost = Input::getPost('sqlhost');
        $sqlport = Input::getPost('sqlport');
        $sqldb = Input::getPost('sqldb');
        $sqluser = Input::getPost('sqluser');
        $sqlpass = Input::getPost('sqlpass');
        $sqlprefix = Input::getPost('sqlprefix');
        
        $sessionprefix = Input::getPost('sessionprefix');
        $cookieprefix = Input::getPost('cookieprefix');
        $cookieexpiry = Input::getPost('cookieexpiry');
        $passwordexpiry = Input::getPost('passwordexpiry');
        $validationexpiry = Input::getPost('validationexpiry');
        $assocname = Input::getPost('assocname');
        $accountno = Input::getPost('accountno');
        $agelimit = Input::getPost('agelimit');

        // Determine the system's host name
        $cleanbaseaddr = $_SERVER['SERVER_NAME'];
        $baseaddr = "http://".$cleanbaseaddr."/";
        $dotpos = strpos($cleanbaseaddr,".")+1;
        $mailbase = substr($cleanbaseaddr,$dotpos);
        // Now build some default values for this page
        $defnotifyaddress=_("board")."@".$mailbase;
        $defnotifyname=sprintf(_("The %s Board"),$assocname);
        $defautoaddress=_("member")."@".$mailbase;
        $defautoname=sprintf(_("%s Member Pages"),$assocname);
        $defbccaddress=_("member")."@".$mailbase;
        
        $settingsok = true;
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
                <li><i class="glyphicon glyphicon-ok" style="color:green"></i> <?=_("General settings")?></li>
                <li><i class="glyphicon glyphicon-play" style="color:green"></i> <strong><?=_("E-mail settings")?></strong></li>
            </ul>
      
  	</div><!-- /col-3 -->
    <div class="col-md-10">
    <div style="text-align:center"><img src="geomemberz.png" alt="GeoMemberz-logo"/></div>
    <br/>
<?php
    if(!$settingsok) {
        echo _("<p><strong>Temporarily storing the settings failed.</strong><br/>Please contact us for help.</p>");
    } else {
        echo _("<p><strong>Settings stored temporarily.</strong></p><p>The last step is setting up everything to do with e-mail. When you clik <strong>Finish</strong>, a config wile will be written, and an admin user to log in to the system with, will be created.</p>");
?>
      <!-- column 2 -->	
		<div class="row">
           
            
                <form class="form-horizontal" role="form" action="install4.php" method="post">
                    <h5><?=_("SMTP settings")?></h5>
                  
                    <div class="form-group">
                        <label for="usesmtp" class="col-sm-3 control-label"><?=_("Use SMTP")?></label>
                        <div class="col-sm-9">
                            <label class="radio-inline">
                              <input onclick="document.getElementById('smtpgroup').style.display='block';" type="radio" name="usesmtp" id="usesmtpyes" value="1" checked="checked"/> <?=_("Yes")?> 
                            </label>
                            <label class="radio-inline">
                              <input onclick="document.getElementById('smtpgroup').style.display='none';" type="radio" name="usesmtp" id="usesmtpno" value="0"/> <?=_("No, use PHP's built-in Mail() function")?> 
                            </label>  
                        </div>
                    </div>
                    <span id="smtpgroup" style="display:block">
                        <div class="form-group">
                            <label for="smtphost" class="col-sm-3 control-label"><?=_("SMTP server host")?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="smtphost" name="smtphost" placeholder="<?=_("SMTP server host")?>" value=""
                                    data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                    data-content="<div class='popoverhead'><?=_("The SMTP server host name")?></div><?=_("This should be a server which will accept e-mail from this host.")?>"/>
                            </div>
                        </div>
                       <div class="form-group">
                            <label for="smtpport" class="col-sm-3 control-label"><?=_("SMTP server port")?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="smtpport" name="smtpport" placeholder="<?=_("SMTP server port")?>" value="25"
                                    data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                    data-content="<div class='popoverhead'><?=_("The SMTP server port number")?></div><?=_("The port number the above SMTP server is listening on. Normally, this is port 25.")?>"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="useauth" class="col-sm-3 control-label"><?=_("Use SMTP authentication")?></label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                  <input onclick="document.getElementById('authgroup').style.display='block';" type="radio" name="useauth" id="useauthyes" value="1" checked="checked"/> <?=_("Yes")?> 
                                </label>
                                <label class="radio-inline">
                                  <input onclick="document.getElementById('authgroup').style.display='none';" type="radio" name="useauth" id="useauthno" value="0"/> <?=_("No")?> 
                                </label>  
                            </div>
                        </div>
                        <span id="authgroup" style="display:block">
                            <div class="form-group">
                                <label for="authuser" class="col-sm-3 control-label"><?=_("SMTP username")?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="authuser" name="authuser" placeholder="<?=_("SMTP username")?>" value=""
                                        data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                        data-content="<div class='popoverhead'><?=_("The SMTP username")?></div><?=_("The username of a user with access to the above SMTP server.")?>"/>
                                </div>
                            </div>
                           <div class="form-group">
                                <label for="authpass" class="col-sm-3 control-label"><?=_("SMTP password")?></label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="authpass" name="authpass" placeholder="<?=_("SMTP password")?>" value=""
                                        data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                        data-content="<div class='popoverhead'><?=_("The SMTP password")?></div><?=_("The password for the above user.")?>"/>
                                </div>
                            </div>
                        </span>
                    </span>         
                    <h5><?=_("E-mail settings")?></h5>
                    <div class="form-group">
                        <label for="notifyaddress" class="col-sm-3 control-label"><?=_("Notification address")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="notifyaddress" name="notifyaddress" placeholder="<?=_("Notification address")?>" value="<?=$defnotifyaddress?>"
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("E-mail address for notifications")?></div><?=_("This address will be used for notifications about new members as well as other system messages.")?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="notifyname" class="col-sm-3 control-label"><?=_("Notification address name")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="notifyname" name="notifyname" placeholder="<?=_("Notification address name")?>" value="<?=$defnotifyname?>"
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("The notification address name")?></div><?=_("This is the name of the recipient associated with the above address. Most e-mail clients will show this name instead of or in addition to the e-mail address.")?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="autoaddress" class="col-sm-3 control-label"><?=_("System address")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="autoaddress" name="autoaddress" placeholder="<?=_("System address")?>" value="<?=$defautoaddress?>"
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("Sender e-mail address")?></div><?=_("This is the e-mail address from which system e-mail is sent and replied to.")?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="autoname" class="col-sm-3 control-label"><?=_("System address name")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="autoname" name="autoname" placeholder="<?=_("System address name")?>" value="<?=$defautoname?>"
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("The system address name")?></div><?=_("This is the name of the sender associated with the above address. Most e-mail clients will show this name instead of or in addition to the e-mail address.")?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                            <label for="usebcc" class="col-sm-3 control-label"><?=_("BCC all system mail")?></label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                  <input onclick="document.getElementById('bccgroup').style.display='block';" type="radio" name="usebcc" id="bccyes" value="1" checked="checked"/> <?=_("Yes")?> 
                                </label>
                                <label class="radio-inline">
                                  <input onclick="document.getElementById('bccgroup').style.display='none';" type="radio" name="usebcc" id="bccno" value="0"/> <?=_("No")?> 
                                </label>  
                            </div>
                    </div>
                    <div class="form-group" id="bccgroup" style="display:block">
                        <label for="bccaddress" class="col-sm-3 control-label"><?=_("BCC recipient address")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="bccaddress" name="bccaddress" placeholder="<?=_("BCC recipient address")?>" value="<?=$defbccaddress?>"
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("The BCC recipient address")?></div><?=_("Any system mail will have this address on BCC. This is useful for keeping an archive of what has been sent to members, in lieu of a sent mail folder.")?>"/>
                        </div>
                    </div>

                    <div class="form-group" style="float:right;margin-right:0px">
                        <input type="hidden" name="sqlhost" value="<?= $sqlhost ?>"/>
                        <input type="hidden" name="sqlport" value="<?= $sqlport ?>"/>
                        <input type="hidden" name="sqldb" value="<?= $sqldb ?>"/>
                        <input type="hidden" name="sqluser" value="<?= $sqluser ?>"/>
                        <input type="hidden" name="sqlpass" value="<?= $sqlpass ?>"/>
                        <input type="hidden" name="sqlprefix" value="<?= $sqlprefix ?>"/> 
                               
                        <input type="hidden" name="sessionprefix" value="<?= $sessionprefix ?>"/>
                        <input type="hidden" name="cookieprefix" value="<?= $cookieprefix ?>"/>
                        <input type="hidden" name="cookieexpiry" value="<?= $cookieexpiry ?>"/>
                        <input type="hidden" name="passwordexpiry" value="<?= $passwordexpiry ?>"/>
                        <input type="hidden" name="validationexpiry" value="<?= $validationexpiry ?>"/>
                        <input type="hidden" name="assocname" value="<?= $assocname ?>"/>
                        <input type="hidden" name="accountno" value="<?= $accountno ?>"/>
                        <input type="hidden" name="agelimit" value="<?= $agelimit ?>"/> 
                        <button type="submit" class="btn btn-primary"><?=_("Finish")?><i class="glyphicon glyphicon-triangle-right"></i></button>
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