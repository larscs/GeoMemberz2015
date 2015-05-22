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

        $usesmtp = Input::getPost('usesmtp');
        $smtphost = Input::getPost('smtphost');
        $smtpport = Input::getPost('smtpport');
        $useauth = Input::getPost('useauth');
        $authuser = Input::getPost('authuser');
        $authpass = Input::getPost('authpass');
        $notifyaddress = Input::getPost('notifyaddress');
        $notifyname = Input::getPost('notifyname');
        $autoaddress = Input::getPost('autoaddress');
        $autoname = Input::getPost('autoname');
        $usebcc = Input::getPost('usebcc');
        $bccaddress = Input::getPost('bccaddress');
        $usebcc = ($usebcc=="1") ? "true" : "false";
        $usesmtp = ($usesmtp=="1") ? "true" : "false";
        $useauth = ($useauth=="1") ? "true" : "false";
        $cleanbaseaddr = $_SERVER['SERVER_NAME'];
        $baseaddr = "http://".$cleanbaseaddr."/";

        // Now, create core/config.php 

$configfile = <<<EOCF
<?php 
\$GLOBALS['config'] = array(
	'sqlhost' => '{$sqlhost}',
    'sqlport' => '{$sqlport}',
	'sqluser' => '{$sqluser}',
	'sqlpw'   => '{$sqlpass}',
	'sqldb'	  => '{$sqldb}',
	'sqlprefix'=> '{$sqlprefix}',

	'passchangetimeout' => {$passwordexpiry},   // The timeout for password changes, in seconds
	'validationtimeout' => {$validationexpiry},	// The timeout for validation, in seconds

	'sessionprefix' => '{$sessionprefix}',
	'cookieprefix' => '{$cookieprefix}',
	'cookieexpiry' => {$cookieexpiry},               // The cookie expiry, in seconds

	'assocname' 	=> '{$assocname}',
	'assocbank'		=> '{$accountno}',
	'assocemail'	=> '{$notifyaddress}',		// E-mail for mails like new user notification etc.
	'assocemailname'=> '{$notifyname}',			// The name for the above address, when sender
	'autoemail'		=> '{$autoaddress}',		// E-mail address of sender for system mails
	'autoemailname' => '{$autoname}',			// The name for the above address, when sender
	'automailcopy'  =>	{$usebcc},				// Whether any system mails should be BCC-ed to an address
	'automailcopyaddr'=> '{$bccaddress}',		// The address such BCCs should be sent to

	'feeagelimit'	=>	{$agelimit},            // Minimum age for which fee is required. 0 for all members.
    
    // Settings related to mail sending
    'mailSMTP'      => {$usesmtp},              // If set to true, the specified mailserver and port is used. Otherwise, the builtin PHP mail() function is used.
    'mailserver'    => '{$smtphost}',
    'mailport'      => {$smtpport},
    'mailauth'      => {$useauth},              // If set to true, the two next are used, otherwise not
    'mailuser'      => '{$authuser}',
    'mailpass'      => '{$authpass}',

	'baseaddr' 		=> '{$baseaddr}',
	'cleanbaseaddr' => '{$cleanbaseaddr}',
	'logo50'		=> '/img/logo.png',          // 50 px high logo, light background
	'logo30'		=> '/img/logo_banner.png',   // 30 px high logo, dark background
	'favicon' 		=> '/img/favicon.png',
);
EOCF;

        if (!file_put_contents('../core/config.php',$configfile)) {$settingsok = false;} else {$settingsok = true;}
        // And finally, add the admin user to the members table
 		$dbh = new PDO("mysql:host={$sqlhost};".
                              "port={$sqlport};".
							  "dbname=$sqldb;".
							  "charset=utf8",
							   "{$sqluser}",
							   "{$sqlpass}");
        $dbh->exec("INSERT INTO {$sqlprefix}members (username,password,passchangeok,validationok,gcnick,firstname,lastname,boardmember,birthdate,membersince,address,email)
                    VALUES ('admin','pw',1,1,'admin','Admin','User',1,'1980-01-01','1980-01-01','nil','{$autoaddress}')");       
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
                <li><i class="glyphicon glyphicon-ok" style="color:green"></i> <?=_("E-mail settings")?></li>
            </ul>
      
  	</div><!-- /col-3 -->
    <div class="col-md-10">
    <div style="text-align:center"><img src="geomemberz.png" alt="GeoMemberz-logo"/></div>
    <br/>
<?php
    if(!$settingsok) {
        echo _("<p><strong>Writing the settings failed.</strong><br/>Please contact us for help.</p>");
    } else {
        echo sprintf(_("<p><strong>Settings saved.</strong></p><p>The username <strong>admin</strong> has been created with the email <strong>%s</strong> and the member number 0. Please go to the <a href=\"../forgotpass.php\">password reset page</a> to set a password. You can then use the admin account to grant board access (administrative) to other users, including your own. Once you have a regular username, you should remove the admin account.</p><p>Remember to delete or remove access to the /install folder.</p><p>Good luck, and thank you for using GeoMemberz.</p>"),$autoaddress);
?>
      <!-- column 2 -->	
		<div class="row">
           
            

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