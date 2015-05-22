<?php

	require_once $_SERVER["DOCUMENT_ROOT"].'/core/init.php';                         // Must be present on all pages using settings and classes
    
    // Let's find out whether we have a working database connection.

    // Try connecting to the server - will fail if settings are not set, or the server cannot be reached.
    
	try {
		$conn = new PDO("mysql:host=".Config::get('sqlhost').";".
							  "dbname=".Config::get('sqldb').";".
							  "charset=utf8",
							   Config::get('sqluser'),
							   Config::get('sqlpw'));
        $connectionok = true;
	} catch(PDOException $e) {
        $connectionok = false;
	}
    $upgradeok = false;
    $upgradefile = false;
    $upgradedb = false;
    if($connectionok) {
        // Find the file and db version
        $memb = Members::getInstance(); 
        $fileversion = $memb->getMetaValue('fileversion');
        $dbversion = $memb->getMetaValue('dbversion');
        $fileverparts = explode(".",$fileversion);
        $dbverparts = explode(".",$dbversion);
        // Current filever is 1.1 and dbver is 1.1
        if($fileverparts[0]<1 || $fileverparts[1]<1) {$upgradeok = true; $upgradefile = true;}
        if($dbverparts[0]<1 || $dbverparts[1]<1) {$upgradeok = true; $upgradedb = true;}
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
<?php
    if($upgradefile || $upgradedb) {
        echo "<li><i class=\"glyphicon glyphicon-play\" style=\"color:red\"></i> <strong>"._("Confirmation")."</strong></li>";
        echo "<li><i class=\"glyphicon glyphicon-play\" style=\"color:transparent\"></i> "._("Upgrade")."</li>";
    }
    if(!$connectionok) {
        echo "<li><i class=\"glyphicon glyphicon-play\" style=\"color:red\"></i> <strong>"._("Connection")."</strong></li>";
        echo "<li><i class=\"glyphicon glyphicon-play\" style=\"color:transparent\"></i> "._("Database setup")."</li>";
        echo "<li><i class=\"glyphicon glyphicon-play\" style=\"color:transparent\"></i> "._("General settings")."</li>";
        echo "<li><i class=\"glyphicon glyphicon-play\" style=\"color:transparent\"></i> "._("E-mail settings")."</li>";
    } else {
        echo "<li><i class=\"glyphicon glyphicon-ok\" style=\"color:green\"></i> "._("No action")."</li>";
    }
?>
            </ul>
      
  	</div><!-- /col-3 -->
    <div class="col-md-10">
    <div style="text-align:center"><img src="geomemberz.png" alt="GeoMemberz-logo"/></div>
    <br/><p><strong><?=_("Welcome to the GeoMemberz installer.")?></strong></p>
<?php
if($connectionok) {
    echo _("<p>A previous installation was detected.</p>");

    if($upgradeok) {

    if($upgradefile) echo _("<p>Your current file version is <strong>{$fileversion}</strong>, and will be upgraded to <strong>1.2</strong>.</p>");
    if($upgradedb) echo _("<p>Your current database version is <strong>{$dbversion}</strong>, and will be upgraded to <strong>1.2</strong>.</p>");

    if($upgradefile || $upgradedb) {
        echo _("<p>Click <strong>Upgrade</strong> to perform the upgrade. Make sure you have backed up your files and databases before proceeding.</p>");
        echo "<a class=\"btn btn-primary\" href=\"upgrade1.php\" role=\"button\">"._("Upgrade")."</a>";
    }

    } else {
        echo _("<p>Your installation is already up-to-date, and no action is necessary.</p>");
        echo _("<p>Remember to delete or remove access to the /install folder.</p>");
    }

} else {

    echo _("<p>No previous installation detected.</p>");
    echo _("<p>Let's start by connecting to the database. If you haven't done so already, find out which existing MySQL database you want to use, or create a new one. GeoMemberz can coexist with other stuff in a database - it doesn't need a database alone. Also, you will need the username and password of a database user with full permissions for that database. We recommend creating a separate database user for this purpose, if possible, as the username and password will be stored in plain text in the core/config.php file.</p>");

}

if(!$connectionok) {
?>
      <!-- column 2 -->	
		<div class="row">
           
            
                <form class="form-horizontal" role="form" action="install1.php" method="post">
                    <div class="form-group">
                        <label for="sqlhost" class="col-sm-3 control-label"><?=_("MySQL Host name")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="sqlhost" name="sqlhost" placeholder="<?=_("MySQL Host name")?>" value="127.0.0.1"
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("Host name of the MySQL server")?></div><?=_("Usually, this is the same host as the web server. If that is the case, leave the default localhost IP address. The IP address is better than the <em>localhost</em> host name.")?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sqlport" class="col-sm-3 control-label"><?=_("MySQL Port")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="sqlport" name="sqlport" placeholder="<?=_("MySQL Port")?>" value="3306"
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("Port number of the MySQL server")?></div><?=_("This is the port number that the MySQL server is listening on. Usually, this is 3306.")?>"/>
                        </div>
                    </div>                    
                    <div class="form-group">
                        <label for="sqldb" class="col-sm-3 control-label"><?=_("Database name")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="sqldb" name="sqldb" placeholder="<?=_("Database name")?>" value=""
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("The database name")?></div><?=_("Enter the name of the database you want GeoMemberz to reside in. It can be a database used for other stuff, or one created just for GeoMemberz. All database items in this database will get a prefix, which will be set on the next page.")?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sqluser" class="col-sm-3 control-label"><?=_("MySQL username")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="sqluser" name="sqluser" placeholder="<?=_("MySQL username")?>" value=""
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("The username for the MySQL database")?></div><?=_("Enter the name of a user with full permission to the database provided above.")?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sqlpass" class="col-sm-3 control-label"><?=_("MySQL password")?></label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="sqlpass" name="sqlpass" placeholder="<?=_("MySQL Host name")?>" value=""
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("The password for the MySQL user")?></div><?=_("Enter the password for the above user. Note that this password will be stored in plain text in the core/config.php file.")?>"/>
                        </div>
                    </div>
                    <div class="form-group" style="float:right;margin-right:0px">
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