<?php
    // This is a comment
	require_once 'core/init.php';                         // Must be present on all pages using settings and classes
	if(!Session::isLoggedIn()) {Session::redirect(404);}  // Use this on all pages requiring the user to be logged in
	$memb = Members::getInstance(); 			            // Use this on all pages using the database
	if(!$memb->isBoard()) {Session::redirect(404);}			// Non-members don't need to know this page exists.
if(Input::exists('post')) {
        // We have a form submission. Check all fields.
        $fieldsok = true;
        // Periodname must be something
        if(!Input::getPost('periodname')) {
            $fieldsok = false;
            $periodnameerror = _("You must specify a period name.");
        }
        // Periodfrom must be something, and cannot exist already
        if(!Input::getPost('periodfrom')) {
            $fieldsok = false;
            $periodfromerror = _("You must specify a from date.");
        }       
        // Periodto must be something, and cannot exist already
        if(!Input::getPost('periodto')) {
            $fieldsok = false;
            $periodtoerror = _("You must specify a to date.");
        }        
        // Periodamount must be something, and cannot exist already
        if(!Input::getPost('periodamount')) {
            $fieldsok = false;
            $periodamounterror = _("You must specify an amount.");
        } 
        if($fieldsok) {
            // If we get here, dates are specified. Check that they are not overlapping.
            $qadd = "";
            if(Input::getPost('periodid')) {
                if(is_numeric(Input::getPost('periodid'))) {
                    $qadd = " WHERE perID<>".Input::getPost('periodid');
                }
            }
            $result = $memb->customQuery("SELECT * FROM ".Config::get('sqlprefix')."feeperiods".$qadd);
            if($result) {
                $periodok = true;
                foreach($result as $row) {
                    $newfrom = date_create_from_format(Config::get('dateformat'),Input::getPost('periodfrom')); // For submission, get
                    $newto = date_create_from_format(Config::get('dateformat'),Input::getPost('periodto'));		// language format
                    $fromdate = date_create_from_format('Y-m-d',$row['perFrom']); 								// For db data,
                    $todate = date_create_from_format('Y-m-d',$row['perTo']);									// use db format
                    if($newfrom >= $newto) {
                        $periodok=false;
                        $fieldsok=false;
                        $periodfromerror = _("From date must be before to date");
                        $periodtoerror = _("To date must be after from date");
                    }
                    // Check if start date is within period
                    if($newfrom >= $fromdate && $newfrom <= $todate) {
                        $periodok=false;
                        $fieldsok = false;
                        $periodfromerror = sprintf(_("From date is inside an existing period (%s to %s)"),date_format($fromdate,Config::get('dateformat')),date_format($todate,Config::get('dateformat')));
                        
                    }
                    // Check if end date is within period
                    if($newto >= $fromdate && $newto <= $todate) {
                        $periodok=false;
                        $fieldsok = false;
                        $periodtoerror = sprintf(_("To date is inside an existing period (%s to %s)"),date_format($fromdate,Config::get('dateformat')),date_format($todate,Config::get('dateformat')));
                    }
                    // Check if both dates are within, is unnecessary, as both errors should be set.
                    // Check if dates are enclosing db date
                    if($newfrom <= $fromdate && $newto >= $todate) {
                        $periodok = false;
                        $fieldsok = false;
                        $periodfromerror = sprintf(_("An existing period exists between these dates (%s to %s)"),date_format($fromdate,Config::get('dateformat')),date_format($todate,Config::get('dateformat')));
                        $periodtoerror = sprintf(_("An existing period exists between these dates (%s to %s)"),date_format($fromdate,Config::get('dateformat')),date_format($todate,Config::get('dateformat')));                        
                    }

                    
                }
            }
        }
        if($fieldsok) {
            // Now we're ready to insert/update
            $fromdate = date_create_from_format(Config::get('dateformat'),Input::getPost('periodfrom')); 	// For submission, get
            $todate = date_create_from_format(Config::get('dateformat'),Input::getPost('periodto'));		// language format
            $fromdate = date_format($fromdate,'Y-m-d');			// Convert dates to db format
            $todate = date_format($todate,'Y-m-d');

            if(Input::getPost('periodid')=='new') {
                // Set MySQL var for use by trigger
                //prepSqlVars($dbh);  
                // insert data as new
            	if(!$memb->addFeePeriod(Input::getPost('periodname'),$fromdate,$todate,Input::getPost('periodamount'))) {Session::errorPage(_("Error"),_("Error adding fee period."));}
            } else {
                // update existing data with id in periodid
                // Set MySQL var for use by trigger
                //prepSqlVars($dbh);                  
            	if(!$memb->editFeePeriod(Input::getPost('periodid'),Input::getPost('periodname'),$fromdate,$todate,Input::getPost('periodamount'))) {Session::errorPage(_("Error"),_("Error modifying fee period."));}

            }
            // All is well, so send back to list
            Session::redirect('feeperiods.php');
        }                
    }

    if(Input::getGet('id') || Input::getPost('periodid')) {
        // We have id from either get or post
        if(Input::getGet('id')) {$myid = Input::getGet('id');}
        if(Input::getPost('periodid')) {$myid = Input::getPost('periodid');}
        if($myid == "new") {
            $isnew = true;
        } else {
            $result = $memb->customQuery("SELECT * FROM ".Config::get('sqlprefix')."feeperiods WHERE perID={$myid}");
            
            if($result) {
                $_POST["periodname"] = $result[0]["perName"];
                $_POST["periodfrom"] = $result[0]["perFrom"];
                $_POST["periodto"] = $result[0]["perTo"];
                $_POST["periodamount"] = $result[0]["perAmount"];
                // Dates are now strings in Y-m-d format from the DB.
                // Convert to DateTime
                $_POST["periodfrom"] = date_create_from_format('Y-m-d',$_POST["periodfrom"]);
                $_POST["periodto"] = date_create_from_format('Y-m-d',$_POST["periodto"]);
                // Convert to string based on localized string from the settings
                $_POST["periodfrom"] = date_format($_POST["periodfrom"],Config::get('dateformat'));
                $_POST["periodto"] = date_format($_POST["periodto"],Config::get('dateformat'));
            }
            $isnew=false;
        }
    } else {
        // No GET or POST data, must be new
        $isnew=true;
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
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
    <link href="css/styles.css" rel="stylesheet"/>
    <link href="css/bootstrap-datepicker.css" rel="stylesheet"/>
	<script src="js/misc.js"></script>
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
	</head>
	<body onload="initStuff()">
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
      	<h3><?php if($isnew) {echo _("New fee period");} else {echo _("Edit fee period");} ?></h3>
        <hr/>
      <!-- column 2 -->	
  
		<div class="row">
            <form class="form-horizontal" role="form" action="feeperiod.php" method="post">
<?php
    if(isset($periodnameerror)) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$periodnameerror."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
              <div class="form-group<?php echo $errorstyle ?>">
                <label for="periodname" class="col-sm-2 control-label"><?=_("Period name")?></label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" id="periodname" name="periodname" placeholder="<?=_("Period name")?>" value="<?php if(isset($_POST["periodname"])) {echo $_POST["periodname"];} ?>"/>
                  <?php echo $errormsg ?>
                </div>
                <div class="col-sm-5">&nbsp;</div>
              </div>
<?php
    if(isset($periodfromerror)) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$periodfromerror."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                <div class="form-group<?php echo $errorstyle ?>">
                    <label for="periodfrom" class="col-sm-2 control-label"><?=_("From date")?></label>
                    <div class="col-sm-4">
                        <div class="input-group date" id="dp1" data-date="<?php if(isset($_POST["periodfrom"])) {echo $_POST["periodfrom"];} ?>" data-date-format="<?= Config::get('altdateformat') ?>">
                            <input class="form-control" type="text" readonly="readonly" id="periodfrom" name="periodfrom" value="<?php if(isset($_POST["periodfrom"])) {echo $_POST["periodfrom"];} ?>"/>
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                        <?php echo $errormsg ?>
                    </div>
                    <div class="col-sm-6">&nbsp;</div>
                </div>
<?php
    if(isset($periodtoerror)) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$periodtoerror."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>    
                <div class="form-group<?php echo $errorstyle ?>">
                    <label for="periodto" class="col-sm-2 control-label"><?=_("To date")?></label>
                    <div class="col-sm-4">
                        <div class="input-group date" id="dp2" data-date="<?php if(isset($_POST["periodto"])) {echo $_POST["periodto"];} ?>" data-date-format="<?= Config::get('altdateformat') ?>">
                            <input class="form-control" type="text" readonly="readonly" id="periodto" name="periodto" value="<?php if(isset($_POST["periodto"])) {echo $_POST["periodto"];} ?>"/>
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                        <?php echo $errormsg ?>
                    </div>
                    <div class="col-sm-6">&nbsp;</div>
                </div>                    
<?php
    if(isset($periodamounterror)) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$periodamounterror."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
              <div class="form-group<?php echo $errorstyle ?>">
                <label for="periodname" class="col-sm-2 control-label"><?=_("Amount")?></label>
                <div class="col-sm-4">
                  <input type="text" class="form-control" id="periodamount" name="periodamount" placeholder="<?=_("Amount")?>" value="<?php if(isset($_POST["periodamount"])) {echo $_POST["periodamount"];} ?>"/>
                  <?php echo $errormsg ?>
                </div>
                <div class="col-sm-6">&nbsp;</div>
              </div>
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <input type="hidden" id="periodid" name="periodid" value="<?php
                if(isset($_GET["id"])) {echo $_GET["id"];} else if(Input::getPost('periodid')) {echo Input::getPost('periodid');} else {echo "new";}
                  	?>"/>
                  <button type="submit" class="btn btn-primary"><?php if($isnew) {echo _("Create");} else {echo _("Edit");} ?></button>
                </div>
              </div>
            </form>         

     
      </div><!--/row-->
  	</div><!--/col-span-9-->
</div>
</div>
<!-- /Main -->
<hr>
<?php include "includes/footer.php"; ?>
    <!-- script references -->

    <script>
		$.fn.datepicker.dates['nb'] = {days:["søndag","mandag","tirsdag","onsdag","torsdag","fredag","lørdag"],
		daysShort:["søn","man","tir","ons","tor","fre","lør"],daysMin:["sø","ma","ti","on","to","fr","lø"],
		months:["januar","februar","mars","april","mai","juni","juli","august","september","oktober","november","desember"],
		monthsShort:["jan","feb","mar","apr","mai","jun","jul","aug","sep","okt","nov","des"],
		today:"I dag",
		clear:"Nullstill",
		weekStart:1,
		format:"dd.mm.yyyy"};

		$('#dp1').datepicker({
		    weekStart: 1,
		    startView: 2,
		    language: "<?= _("en") ?>",
		    autoclose: true
		});
		$('#dp2').datepicker({
		    weekStart: 1,
		    startView: 2,
		    language: "<?= _("en") ?>",
		    autoclose: true
		});           
    </script>
	</body>
</html>