<?php
	require_once 'core/init.php';                         // Must be present on all pages using settings and classes
	if(!Session::isLoggedIn()) {Session::redirect(404);}  // Use this on all pages requiring the user to be logged in
	$memb = Members::getInstance(); 			            // Use this on all pages using the database
	if(!$memb->isBoard()) {Session::redirect(404);}			// Non-members don't need to know this page exists.
	if(Input::exists('get')) {
    if(Input::getGet('delete')) {
      if(is_numeric(Input::getGet('delete'))) {
        // Go ahead and delete the fee period.
        if(!$memb->deleteFeePeriod(Input::getGet('delete'))) {
          Session::errorPage(_("Database error"),_("Deleting the fee period failed. Probably payments exist, that refer to it. Remove all payments from the period and try again."));
        }
        // Deletion succeded. Reload the page to get rid of the GET args
        Session::redirect('feeperiods.php');
      }
      else {
        Session::errorPage(_("Error"),_("An argument that should be numeric, wasn't."));
      }
    }
  }
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
      	<h3><?=_("Fee periods")?></h3>
      <!-- column 2 -->	
  
		<div class="row">
                    <div class="alert alert-warning">
        <?=_("Current period is marked in <span class=\"bg-success\">green</span>.")?>
                    </div>
        <table class="table table-condensed table-hover">        
           <tr>
                <th><?=_("Period")?></th>
                <th><?=_("From")?></th>
                <th><?=_("To")?></th>
                <th class="rj"><?=_("Members")?></th>
                <th class="rj"><?=_("Fee payers")?></th>
                <th class="rj"><?=_("Amount")?></th>
                <th class="rj"><?=_("Paid")?></th>
                <th class="rj"><?=_("Potential")?></th>
                <th class="rj"><?=_("Achievement")?></th>
                <th><?=_("Action")?></th>
           </tr>
<?php

            $feeperiods = $memb->getFeePeriods();
            if($feeperiods) {
            	foreach($feeperiods as $feeperiod) {
            		if($feeperiod['current']) {$curper = " class=\"success\""; } else {$curper = "";}
            ?>
           <tr<?php echo $curper; ?>>
                <th><?php echo $feeperiod["perName"] ?></th>
                <td><?php echo Misc::formatDate($feeperiod["perFrom"]) ?></td>
                <td><?php echo Misc::formatDate($feeperiod["perTo"]) ?></td>
                <td class="rj"><?= $feeperiod["perpaymembers"] ?> </td><!-- # of members -->
                <td class="rj"><?= $feeperiod["perfeepayers"] ?></td><!-- # of members paid -->
                <td class="rj"><?php echo number_format($feeperiod["perAmount"],2,Config::get('dp'),Config::get('ts')) ?></td>
                <td class="rj"><?php echo number_format($feeperiod['perfeepaid'],2,Config::get('dp'),Config::get('ts')) ?></td><!-- # amount paid for period -->
                <td class="rj"><?= $feeperiod["perpotential"] ?></td><!-- # potential -->
                <td class="rj"><?= $feeperiod["perpercentpaid"].Config::get('pctsign') ?></td><!-- % paid -->
                <td>
                  <a href="feeperiod.php?id=<?php echo $feeperiod["perID"] ?>" title="<?=_("Edit")?>"><span class="glyphicon glyphicon-edit"></span></a>
                  <a href="?delete=<?php echo $feeperiod["perID"] ?>" title="<?=_("Delete")?>" onclick="if(!confirm('<?php echo _("Are you sure you want to delete this fee period?") ?>')){return false;}"><span class="glyphicon glyphicon-remove" style="color:red"></span></a>
                </td>
           </tr>
<?php
				}
    		} else {
        		// no result
?>
           <tr>
                <td colspan="10" style="text-align:center"><?=_("No periods are defined yet.")?></td>
           </tr>
<?php
    		}

?>

           <tr>
                <td colspan="10" style="text-align:right"><a href="feeperiod.php?id=new" class="btn btn-primary"><?=_("New fee period")?></a></td>
           </tr>
     
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