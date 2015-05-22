<?php
require_once 'core/init.php';                         // Must be present on all pages using settings and classes
if(!Session::isLoggedIn()) {Session::redirect(404);}  // Use this on all pages requiring the user to be logged in
$memb = Members::getInstance();						            // Use this on all pages using the database
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
      	
      <!-- column 2 -->	
  
		<div class="row">
           
            
          
            <!-- center left-->	
         	<div class="col-md-6">
              <div class="panel panel-primary">
                  <div class="panel-heading"><h4><?=_("Newest members")?></h4></div>
                    <table class="table table-striped">
                          <thead>
                            <tr><th><?=_("User name")?></th><th><?=_("Geocaching name")?></th><th><?=_("Since")?></th></tr>
                          </thead>
                          <tbody>
<?php
    // Show five newest members
    $newmembers = $memb->getNewest(5);
    foreach($newmembers as $member) {   
?>
                            <tr><td><?= $member["username"] ?></td><td><?= $member["gcnick"] ?></td><td><?= Misc::formatDate($member["membersince"]) ?></td></tr>
<?php
    }
?>
                          </tbody>
                   	</table>

              </div><!--/panel-->
<?php
    // Get ages stats
    $ages = $memb->getAges();
?>
              <div class="panel panel-info">
                  <div class="panel-heading"><h4><?=_("Members")?></h4></div>
                  
                    <table class="table">
                          <tbody>
                            <tr><th><?=_("Members:")?></th><td><?= $ages["nummembers"] ?></td></tr>
                            <tr><td style="padding-left:60px"><?=_("Age 16+")?></td><td><?= $ages["num16plus"] ?></td></tr>
                            <tr><td style="padding-left:60px"><?=_("Age 8-15")?></td><td><?= $ages["num8to15"] ?></td></tr>
                            <tr><td style="padding-left:60px"><?=_("Age 0-7")?></td><td><?= $ages["num0to7"] ?></td></tr>
                          </tbody>
                   	</table>                    


                  
              </div><!--/panel-->   
          	</div><!--/col-->
        	<div class="col-md-6">
<?php
  if($memb->isBoard()){
      $fees = $memb->getFeeStats();
      if($fees) {   // If something is returned, we have data
        if(!$fees['noperiods']) {     // If this variable is not set, output results
?>
                <div class="panel panel-success">
                  <div class="panel-heading"><h4><?=_("Membership fees")?></h4></div>
                  <div class="panel-body">
                    
                    <small><?=_("Membership fees paid")?></small>
                    <div class="progress">
                      <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?=$fees['myperc']?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$fees['myperc']?>%">
                        <?= $fees['insideperc'] ?>
                        
                      </div>
                        <?= $fees['outsideperc'] ?>
                    </div>
                    <small><?=_("Fee amount")?></small>
                    <div><strong><?= number_format($fees['myamount'],2,Config::get('dp'),Config::get('ts')) ?></strong></div>
                    <small><?=sprintf(_("Fee payments for «%s»"),$fees['mypername'])?></small>
                    <div><strong><?= number_format($fees['actamount'],2,Config::get('dp'),Config::get('ts'))?></strong> <?=_("of")?> <strong><?= number_format($fees['potamount'],2,Config::get('dp'),Config::get('ts'))?></strong></div>
                  </div><!--/panel-body-->
              </div><!--/panel-->
<?php
        } else {  // The noperiods var is set, show message about this
?>

                <div class="panel panel-success">
                  <div class="panel-heading"><h4><?=_("Membership fees")?></h4></div>
                  <div class="panel-body">
                    <?= _("No fee periods defined. Go to «Fee periods» in the Navigation bar to define one.") ?>
                  </div><!--/panel-body-->
              </div><!--/panel-->

<?php
        }
      }
  }
  // Get fee status for member
  $table = Config::get('sqlprefix')."feepayments";
  $feemembernum = Session::getSessionVar('userID');
  $feeperioddata = $memb->getCurrentFeePeriodData();
  $feeperiodname = $feeperioddata["perName"];
  $feeperiodamount = number_format($feeperioddata["perAmount"],2,Config::get('dp'),Config::get('ts'));
  $feeperiodid = $feeperioddata["perID"];
  $mypayment = $memb->customQuery("SELECT * FROM {$table} WHERE paymentmember = {$feemembernum} AND paymentPeriod = {$feeperiodid}");
  if($mypayment) {
    $paymentdetails = "<span style=\"color:green\">".sprintf(_("Paid on %s"),Misc::formatDate($mypayment[0]["paymentDate"]))."</span>";
    $paymentextras = "";
  } else {
    $paymentdetails = "<span style=\"color:red\">"._("Unpaid")."</span>";
    $paymentextras = "<br/><br/>".sprintf(_("Please pay kr. %s into %s's account no. %s."),"<strong>".$feeperiodamount."</strong>","<strong>".Config::get('assocname')."</strong>","<strong>".Config::get('assocbank')."</strong>");
  }
  ?>
              <div class="panel panel-success">
                  <div class="panel-heading"><h4><?=_("My fee")?></h4></div>
                  <div class="panel-body">
                  <?=sprintf(_("My fee status for «%s»:"),$feeperiodname)?><br/><strong><?=$paymentdetails?></strong>
                  <?=$paymentextras?>
                  </div>
              </div><!--/panel-->  
			</div><!--/col-span-6-->
     
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