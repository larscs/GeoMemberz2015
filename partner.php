<?php
require_once 'core/init.php';                         // Must be present on all pages using settings and classes
//if(!Session::isLoggedIn()) {Session::redirect(404);}  // Use this on all pages requiring the user to be logged in
$memb = Members::getInstance(); 			            // Use this on all pages using the database

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
	    <link href="css/styles.css" rel="stylesheet"/>
	    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	    <!--[if lt IE 9]>
	      <script src="js/html5shiv.js"></script>
	      <script src="js/respond.min.js"></script>
	    <![endif]-->
	    <script src="js/jquery.js"></script>
	    <script src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/js/jquery.js"></script>

	</head>
	<body>
<!-- Header -->
<?php
	include_once "includes/headerbar_unlogged.php";
?>
<!-- /Header -->

<!-- Main -->
<div class="container">
<div class="row">
	<div class="col-md-2">
      <!-- Left column -->
<?php
	include_once "includes/navmenu_unlogged.php";
?>  
      
  	</div><!-- /col-3 -->
    <div class="col-md-10">
      <!-- column 2 -->	
        <h3><?=_("Betalende medlemmer")?></h3>
		<div class="row table-responsive">
           <table class="table table-condensed table-hover" id="memberlist">
               <thead>
                    <tr>
                         <th id="col2"><?=_("GC name")?></th>
                         <th id="col3"><?=_("First name")?></th>
                         <th id="col4"><?=_("Last name")?></th>
                    </tr>
               </thead>
               <tbody>
<?php

	$memberlist = $memb->getCurrentPayingMembers();
	foreach($memberlist as $member) {
			$firstname = $member["firstname"]." ".$member["middlename"];
			$lastname = $member["lastname"];
?>
                    <tr>
                         <td><?php echo $member["gcnick"] ?></td>
                         <td><?php echo $firstname ?></td>
                         <td><?php echo $lastname ?></td>
                    </tr>
<?php
    }
?>
                </tbody>
           </table>
      </div><!--/row-->
  	</div><!--/col-span-9-->
</div>
</div>
<!-- /Main -->
<hr>
<?php include "includes/footer.php"; ?>
	</body>
</html>