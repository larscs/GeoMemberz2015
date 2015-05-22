<?php
	require_once 'core/init.php';                         // Must be present on all pages using settings and classes
	if(!Session::isLoggedIn()) {Session::redirect(404);}  // Use this on all pages requiring the user to be logged in
	$memb = Members::getInstance(); 			            // Use this on all pages using the database
	if(!$memb->isBoard()) {Session::redirect(404);}			// Non-members don't need to know this page exists.
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
    <link href="css/styles.css" rel="stylesheet"/>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
	</head>
	<body>
<!-- Header -->
<?php
 include_once "includes/headerbar.php";
?>
<!-- /Header -->

<!-- Main -->
<div class="container">
<div class="row">
	<div class="col-md-2">
      <!-- Left column -->
<?php
        include_once "includes/navmenu.php";
?>  
      
  	</div><!-- /col-3 -->
    <div class="col-md-10">
      	<h3><?=_("Change log")?></h3>
      <!-- column 2 -->	
  
		<div class="row">

                    <?=_("These are the up to 250 latest database changes performed in the system. The newest are on top.")?>
                    <table class="table table-striped table-condensed">
                        <tr>
                            <th><?=_("Time")?></th>
                            <th><?=_("User")?></th>
                            <th><?=_("Table")?></th>
                            <th><?=_("Row")?></th>
                            <th><?=_("Field")?></th>
                            <th><?=_("Action")?></th>
                            <th><?=_("Old value")?></th>
                            <th><?=_("New value")?></th>
                            
                        </tr>
<?php
    $rows = $memb->getChangeLog(250);
                foreach($rows as $row) {

?>
                        <tr class="<?= $row["rowcol"] ?>">
                            <td class="nowrap"><?=$row["chgTime"]?></td>
                            <td title="<?= _("IP address: ").$row["chgByIP"] ?>"><?=$row["username"]?></td>
                            <td><?=$row["chgTable"]?></td>
                            <td><?=$row["chgRow"]?></td>
                            <td><?=$row["chgField"]?></td>
                            <td><?=$row["chgAction"]?></td>
                            <td><?=$row["chgOldValue"]?></td>
                            <td><?=$row["chgNewValue"]?></td>
                        </tr>
<?php
                }
?>
                    </table>
     
        </div><!--/row-->
  	</div><!--/col-span-9-->
</div>
</div>
<!-- /Main -->
<hr>
<?php include "includes/footer.php"; ?>
    <!-- script references -->
		<script src="js/jquery.js"></script>
		<script src="js/bootstrap.min.js"></script>
	</body>
</html>