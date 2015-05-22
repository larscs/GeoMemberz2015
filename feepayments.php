<?php
	require_once 'core/init.php';                         // Must be present on all pages using settings and classes
	if(!Session::isLoggedIn()) {Session::redirect(404);}  // Use this on all pages requiring the user to be logged in
	$memb = Members::getInstance(); 			            // Use this on all pages using the database
	if(!$memb->isBoard()) {Session::redirect(404);}			// Non-members don't need to know this page exists.
	if(Session::getSessionVar('scrollpos')) {
		$scrollposition = "window.scrollTo(0,".trim(Session::getSessionVar('scrollpos')).")";
		Session::clearSessionVar('scrollpos');			// Only use once per click setting this var.
	} else {
		$scrollposition = "";
	}
	if(Session::getSessionVar('sortkey')) {
		$sortkey = Session::getSessionVar('sortkey');
		Session::clearSessionVar('sortkey');			// Only use once per click setting this var.
	} else {
		$sortkey = "[[0,0]]";
	}
    if(Input::getGet('perid')) {
        $getpostid = Input::getGet('perid');
    } elseif(Input::getPost('perid')) {
        $getpostid = Input::getPost('perid');
    }
    if(Session::getSessionVar('sortkey')) {
        $sortkey = Session::getSessionVar('sortkey');
        Session::clearSessionVar('sortkey'); // Only use once per click setting this var.
    } else {
        $sortkey = "[[0,0]]";
    }     	
	$showinactive = (Input::getPost('showinactive')) ? true : false ;
    // Perform payment actions
    if(Input::getPost('payment')) {
        if(is_numeric(Input::getPost('user')) && is_numeric(Input::getPost('payment')) ) {
            if(Input::getPost('payment')==1) {
                // Set user as paid for given period with today's date
                $q = "INSERT INTO ".Config::get('sqlprefix')."feepayments (paymentMember,paymentPeriod,paymentDate, paymentAmount) VALUES ".
                     "(".Input::getPost('user').",".Input::getPost('perid').",'".date("Y-m-d",strtotime(Input::getPost('date')))."',".Input::getPost('amount').")";
                
                if(!$memb->customQueryExec($q)) {Session::errorPage(_("Database error"),_("An error ocurred while creating a database record."));}
                
            }

            if(Input::getPost('payment')==2) {
                // Set user as unpaid for given period
                $q = "DELETE FROM ".Config::get('sqlprefix')."feepayments WHERE paymentMember=".Input::getPost('user')." AND paymentPeriod=".Input::getPost('perid');
                if(!$memb->customQueryExec($q)) {Session::errorPage(_("Database error"),_("An error ocurred while updating a database record."));}
                }

            }
    }

	if(Input::getPost('scrollpos')) {
        $scrollposition = "window.scrollTo(0,".trim(Input::getPost('scrollpos')).")";
    } else {
        $scrollposition = "";
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
	    <link href="css/styles.css" rel="stylesheet"/>
	    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	    <!--[if lt IE 9]>
	      <script src="js/html5shiv.js"></script>
	      <script src="js/respond.min.js"></script>
	    <![endif]-->
	    <script src="js/jquery.js"></script>
	    <script src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/js/jquery.js"></script>
        <script type="text/javascript" src="/js/jquery.tablesorter.js"></script>
        <script type="text/javascript" src="/js/sorting.js"></script>
        <script type="text/javascript">
			$(document).ready(function() { 
			        $("#memberlist").tablesorter({
			            sortList: <?= $sortkey ?>,
			            headers: {
			                6: {sorter: false}
			            }
			        });
			        <?= $scrollposition ?>  
			}); 

                </script>
                
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
        <h3><?=_("Fee payments")?></h3>

		<div class="row table-responsive">
        <form class="form-inline" id="perform" role="form" method="post">
            <?=_("Fee period:")?> 
            <select name="perid" class="form-control" style="min-width:100px;margin-bottom:10px">
<?php
    $periods = $memb->getFeePeriods();
    if($periods) {
        foreach($periods as $row) {
            $fromdate = date_create($row["perFrom"]);
            $todate = date_create($row["perTo"]);
            $today = date_create(date("Y-m-d"));
            $current = $row['current'];
            // If we have a session var, select it
            if(Session::getSessionVar('perid')) {
                $selectedper = Session::getSessionVar('perid');
            }
            // If we have a POST var, override the session var with it

            if(Input::getPost('perid')) {
                    $selectedper = Input::getPost('perid');
            }
            // If $selectedper is not set now, we use defaults (else clause) 
            
            if(isset($selectedper)) {
                // Set selected period as session var
                Session::setSessionVar('perid',$selectedper);
                // Check if current row should be preselected
                if($row["perID"]==$selectedper) {
                    // If we have a post saying which is selected
                    $selected = true;
                } else {
                    // If not, it should select the current period
                    $selected = $current;
                }
                $myperid = $selectedper;
            } else {
                // We don't have POST or SESSION data, so only set $myperid for the $current period
                if($current) {
                    $myperid = $row["perID"];
                }
                $selected = $current;
            }
?>
                <option value="<?=$row["perID"] ?>" onclick="document.getElementById('perform').submit();"<?php if($selected){echo " selected";}?>><?= $row["perName"] ?><?php if($current){echo _(" (Current period)");}?></option>
<?php
        }
        
    } else {
        // no result
?>
           <option>
                <?=_("No periods are defined yet.")?>
           </option>
<?php
    }

    if(isset($myperid)) { 			// Only show the page if we have a current period.
    	$feeperiod = $memb->getFeePeriod($myperid);
    	$perenddate = $feeperiod['perTo'];
    	$perfeeamount = $feeperiod['perAmount'];
         
?>
            </select>
            <span style="margin-left:10px"><? echo _("Fee amount for this period: ")." <strong>".number_format($perfeeamount,0,Config::get('dp'),Config::get('ts'))."</strong>" ?></span>
        </form>
        <form class="form-inline" id="activeform" role="form" method="post" style="clear:right;float:right">
        <input type="checkbox" name="showinactive" <?php if($showinactive) echo "checked=\"checked\""; ?> onclick="document.getElementById('activeform').submit();"/> <?php echo _("Also show inactive members") ?>
        </form>
                    <table class="table table-condensed table-hover" id="memberlist">
<?php
		if(isset($myperid)) {
?>
                        <thead>
                            <tr>
                                <th id="col0"><?=_("#")?></th>
                                <th id="col1"><?=_("Username")?></th>
                                <th id="col2"><?=_("GC name")?></th>
                                <th id="col3"><?=_("First name")?></th>
                                <th id="col4"><?=_("Last name")?></th>
                                <th id="col5"><?=_("Payment status")?></th>
                                <th id="col6"><?=_("Actions")?></th>
                            </tr>
                        </thead>
<?php
		}
?>                        
                        <tbody>
<?php
		$members = $memb->getPaymentsList($myperid,$showinactive);
	    if($members) {

	        foreach($members as $row) {
	            if(Session::getSessionVar('membernum')==$row['membernum'] || $memb->isBoard()) {
	                // If the record belongs to the logged in user OR the logged in user is a board member, always show names.
	                // Board members should see the names in red to know that they are undisclosed.
	                $firstname = $row["firstname"];
	                $lastname = $row["lastname"];
	                if(Session::getSessionVar('boardmember') && !$row["pubfirstname"]) {$firstname = "<span style=\"color:red\">".$firstname."</span>";}
	                if(Session::getSessionVar('boardmember') && !$row["publastname"]) {$lastname = "<span style=\"color:red\">".$lastname."</span>";}        
	            } else {
	                // For other users, only show details that are set as public.
	                if($row["pubfirstname"]) {$firstname=$row["firstname"];} else {$firstname=_("<span style=\"color:silver\">(Undisclosed)</span>");}
	                if($row["publastname"]) {$lastname=$row["lastname"];} else {$lastname=_("<span style=\"color:silver\">(Undisclosed)</span>");} 
	            }
	            $bdate = date_create($row["birthdate"]);
	            $today = date_create();
	            $diff = date_diff($bdate,$today);
	            $age = (int) $diff->format('%y');
	            //echo "num: ".$row["membernum"]." ";
	            //dump($row);
	            //echo "age:$age, active: ".$row["active"];
	            if($age>=Config::get('feeagelimit') && $row["active"]==1) {
	                if(is_null($row["paymentDate"])) {
	                    // Not paid
	                    $paystatus = "<span class=\"glyphicon glyphicon-remove\" style=\"color:Red\"></span> ";
	                    
	                    $paycolumns =       "<td>".
	                                            "<span class=\"glyphicon glyphicon-remove\" style=\"color:Red;float:left\"></span>&nbsp;".
	                                            "<input class=\"form-control\" type=\"text\" name=\"formdate\" id=\"formdate".$row["membernum"]."\" style=\"display:none;width:82px;height:24px;padding:0px;padding-left:4px;margin-left:4px;margin-right:10px;float:left\" value=\"".Misc::formatDate(date("Y-m-d"))."\"/>".
	                                            "<input class=\"form-control\" type=\"text\" name=\"formamount\" id=\"formamount".$row["membernum"]."\" style=\"display:none;width:50px;height:24px;padding:0px;padding-right:4px;float:left;text-align:right\" value=\"".number_format($perfeeamount,0,Config::get('dp'),Config::get('ts'))."\"/>".
	                                        "</td>".
	                                        "<td>".
	                                            "<form class=\"form-inline\" id=\"member".$row["membernum"]."\" role=\"form\" method=\"post\" action=\"feepayments.php\">".
	                                                "<input type=\"hidden\" name=\"payment\" value=\"1\"/>".
	                                                "<input type=\"hidden\" id=\"scrollpos".$row["membernum"]."\" name=\"scrollpos\" value=\"\"/>".
	                                                "<input type=\"hidden\" name=\"perid\" value=\"".$myperid."\"/>".
	                                                "<input type=\"hidden\" name=\"date\" id=\"date".$row["membernum"]."\" value=\"".Misc::formatDate(date("Y-m-d"))."\"/>".
	                                                "<input type=\"hidden\" name=\"amount\" id=\"amount".$row["membernum"]."\" value=\"".number_format($perfeeamount,0,Config::get('dp'),Config::get('ts'))."\"/>".
	                                                "<input type=\"hidden\" name=\"user\" value=\"".$row["membernum"]."\"/>".
	                                                "<input type=\"hidden\" name=\"sortkey\" id=\"sortkey".$row["membernum"]."\" value=\"[[0,0]]\"/>".
	                                                "<input type=\"submit\" onclick=\"payStep1('".$row["membernum"]."');return false;\" id=\"submit".$row["membernum"]."\" class=\"btn btn-success btn-xs\" value=\""._("Pay")."\"/>".
	                                            "</form>".
	                                        "</td>";
	                                    
	                    
	                } else {
	                    // Paid
	                    $paystatus = "<span class=\"glyphicon glyphicon-ok\" style=\"color:Green\"></span> ";
	                    $hilightstart = $row["paymentAmount"]<$perfeeamount ? "<span style=\"color:Red\">" : "" ;
	                    $hilightend = $row["paymentAmount"]<$perfeeamount ? "</span>" : "" ;
	                    $paymentamount = isset($row["paymentAmount"]) ? " - ".$hilightstart.Config::get('currency').number_format($row["paymentAmount"],0,Config::get('dp'),Config::get('ts')).$hilightend : "";
	                    $paycolumns =    "<td>".
	                                    $paystatus.Misc::formatDate($row["paymentDate"]).
	                                    $paymentamount.
	                                    "</td>".
	                                    "<td>".
	                                    "<form class=\"form-inline\" id=\"member".$row["membernum"]."\" role=\"form\" method=\"post\" action=\"feepayments.php\">".
	                                    "<input type=\"hidden\" name=\"payment\" value=\"2\"/>".
	                                    "<input type=\"hidden\" id= \"scrollpos".$row["membernum"]."\" name=\"scrollpos\" value=\"\"/>".
	                                    "<input type=\"hidden\" name=\"perid\" value=\"".$myperid."\"/>".
	                                    "<input type=\"hidden\" name=\"user\" value=\"".$row["membernum"]."\"/>".
	                                    "<input type=\"hidden\" name=\"sortkey\" id=\"sortkey".$row["membernum"]."\" value=\"[[0,0]]\"/>".
	                                    "<button type=\"submit\" id=\"submit".$row["membernum"]."\" onclick=\"document.getElementById('sortkey".$row["membernum"]."').value=getSortKeyPayments();document.getElementById('scrollpos".$row["membernum"]."').value=self.pageYOffset;if(!confirm('"._("Are you sure you want to cancel this payment?")."')){return false;}\" class=\"btn btn-danger btn-xs\">"._("Unpay")."</button>".
	                                    "</form>".
	                                    "</td>";                            
	                }
	            } else {
	                $paystatus = _("<span style=\"color:silver\">(Not due)</span> ");
	                $paycolumns = "<td>$paystatus</td><td></td>";
	            }
                ?>
           <tr<?php if($row["active"]!=1) {echo " class=\"danger\"";} else if($row["membernum"]==Session::getSessionVar('membernum')) {echo " class=\"success\"";} ?>>
                <td><?php echo $row["membernum"] ?></td>
                <td><?php echo $row["username"] ?></td>
                <td><?php echo $row["gcnick"] ?></td>
                <td><?php echo $firstname ?></td>
                <td><?php echo $lastname ?></td>
                <?php echo $paycolumns ?>
           </tr>
<?php
        	}        
    	}
	} else {
?>
    Current date is not within any of the fee periods. Please select one or <a href="./feeperiods.php">create a new one</a>.

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
    <!-- script references -->
		<script src="js/bootstrap.min.js"></script>
	</body>
</html>