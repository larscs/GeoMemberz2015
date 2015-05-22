<?php

	require_once $_SERVER["DOCUMENT_ROOT"].'/core/init.php';                         // Must be present on all pages using settings and classes
    
    if(Input::exists('post')) {
        // Try to connect using the given credentials.
        $sqlhost = Input::getPost('sqlhost');
        $sqlport = Input::getPost('sqlport');
        $sqldb = Input::getPost('sqldb');
        $sqluser = Input::getPost('sqluser');
        $sqlpass = Input::getPost('sqlpass');
        try{
            $dbh = @new PDO( "mysql:host={$sqlhost};port:{$sqlport};dbname={$sqldb}",
                            "{$sqluser}",
                            "{$sqlpass}",
                            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            // successful connection
            $connection = true;
        }
        catch(PDOException $e){
            // unsuccessful connection
            $connection = false;
            $errmessage = $e->getMessage();
        }

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
                <li><i class="glyphicon glyphicon-play" style="color:red"></i> <strong><?=_("Database setup")?></strong></li>
                <li><i class="glyphicon glyphicon-play" style="color:transparent"></i> <?=_("General settings")?></li>
                <li><i class="glyphicon glyphicon-play" style="color:transparent"></i> <?=_("E-mail settings")?></li>
            </ul>
      
  	</div><!-- /col-3 -->
    <div class="col-md-10">
    <div style="text-align:center"><img src="geomemberz.png" alt="GeoMemberz-logo"/></div>
    <br/>
<?php
    if(!$connection) {
        echo _("<p><strong>The connection failed with the following error:</strong><br/><br/><span style=\"color:red\">{$errmessage}</span><br/><br/>Please use your browser's <em>Back</em> button and review your connection details.</p>");
    } else {
        echo _("<p><strong>Connection successful.</strong></p><p>The next step is to create the tables. You now have the option to change the prefix that will be used for tables and views. If you plan on running more than one GeoMemberz instance on the same server, you must change this to uniquely identify each instance.</p>");
?>
      <!-- column 2 -->	
		<div class="row">
           
            
                <form class="form-horizontal" role="form" action="install2.php" method="post">
                    <div class="form-group">
                        <label for="sqlprefix" class="col-sm-3 control-label"><?=_("MySQL prefix")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="sqlprefix" name="sqlprefix" placeholder="<?=_("MySQL prefix")?>" value="gm_"
                                data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" 
                                data-content="<div class='popoverhead'><?=_("The prefix for MySQL tables and views")?></div><?=_("This will be used as a prefix for all table and view names. It must be unique for each GeoMemberz instance.")?>"/>
                        </div>
                    </div>
                    <div class="form-group" style="float:right;margin-right:0px">
                        <input type="hidden" name="sqlhost" value="<?= $sqlhost ?>"/>
                        <input type="hidden" name="sqlport" value="<?= $sqlport ?>"/>
                        <input type="hidden" name="sqldb" value="<?= $sqldb ?>"/>
                        <input type="hidden" name="sqluser" value="<?= $sqluser ?>"/>
                        <input type="hidden" name="sqlpass" value="<?= $sqlpass ?>"/>
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