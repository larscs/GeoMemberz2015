<?php
require_once 'core/init.php';                         // Must be present on all pages using settings and classes
if(!Session::isLoggedIn()) {Session::redirect(404);}  // Use this on all pages requiring the user to be logged in
$memb = Members::getInstance(); 			            // Use this on all pages using the database
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
if(!Input::getPost('showinactive')) {
	// No POST var, so probably redirected with session vars present
	if(Session::getSessionVar('inact')) {
		$showinactive = Session::getSessionVar('inact');
		Session::clearSessionVar('inact');			// Only use once per click setting this var.
	} else {
		$showinactive = false;
	}

} else {
	// POST var present (= clicking the check box), so use that
	$showinactive = (Input::getPost('showinactive')) ? true : false ;
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
			                7: {sorter: false}
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
        <h3><?=_("Members")?></h3>
		<div class="row table-responsive">
      	<div class="alert alert-warning">
            <?=_("Your record is marked in <span class=\"bg-success\">green</span>.")?> <?=_("Text marked in <span style=\"color:red\">red</span> on your own record, means they are undisclosed.")?><br/>
            <?=_("Your submembers, if any, are marked in <span class=\"bg-info\">blue</span>.")?><br/>
            
<?php
    if($memb->isBoard()) {
?>
            <?=_("Frozen members are marked in <span class=\"bg-danger\">red</span>.")?><br/>
            <br/>
            <?=_("Text in <span style=\"color:red\">red</span> is visible to board members only, and should not be disclosed.")?>

        <form class="form-inline" id="activeform" role="form" method="post" style="clear:both;float:right">
        <input type="checkbox" name="showinactive" <?php if($showinactive) echo "checked=\"checked\""; ?> onclick="document.getElementById('activeform').submit();"/> <?php echo _("Also show inactive members") ?>
        </form>
<?php
	}
?>
        </div>
           <table class="table table-condensed table-hover" id="memberlist">
               <thead>
                    <tr>
                         <th id="col0"><?=_("#")?></th>
                         <th id="col1"><?=_("Username")?></th>
                         <th id="col2"><?=_("GC name")?></th>
                         <th id="col3"><?=_("First name")?></th>
                         <th id="col4"><?=_("Last name")?></th>
                         <th id="col5"><?=_("Position")?></th>
                         <th id="col6" style="white-space:nowrap"><?=_("Member since")?></th>
                         <th id="col7" style="text-align:right"><?=_("Actions")?></th>
                    </tr>
               </thead>
               <tbody>
<?php
	$memberlist = $memb->getMemberList($showinactive);
	foreach($memberlist as $member) {
		if(Session::getSessionVar('userID')==$member["membernum"] || $memb->isBoard()) {
			
            // If the record belongs to the logged in user OR the logged in user is a board member, always show names.
			// Board members should see the names in red to know that they are undisclosed.
			$firstname = $member["firstname"];
			$lastname = $member["lastname"];
			if((Session::getSessionVar('userID')==$member["membernum"] || $memb->isBoard()) && !$member["pubfirstname"]) {$firstname = "<span style=\"color:red\">".$firstname."</span>";}
			if((Session::getSessionVar('userID')==$member["membernum"] || $memb->isBoard()) && !$member["publastname"]) {$lastname = "<span style=\"color:red\">".$lastname."</span>";}        
		} else {
			// For other users, only show details that are set as public.
			if($member["pubfirstname"]) {$firstname=$member["firstname"];} else {$firstname=_("<span style=\"color:silver\">(Undisclosed)</span>");}
			if($member["publastname"]) {$lastname=$member["lastname"];} else {$lastname=_("<span style=\"color:silver\">(Undisclosed)</span>");} 
		}
?>
                    <tr<?php if($member["active"]!=1) {echo " class=\"danger\"";} else if($memb->isSubmember($member["membernum"],Session::getSessionVar('userID'))) {echo " class=\"info\"";} else if($member["membernum"]==Session::getSessionVar('userID')) {echo " class=\"success\"";} ?>>
                         <td><?php echo $member["membernum"] ?></td>
                         <td><?php echo htmlspecialchars($member["username"]) ?></td>
                         <td><?php echo $member["gcnick"] ?></td>
                         <td><?php echo $firstname ?></td>
                         <td><?php echo $lastname ?></td>
                         <td><?php echo $member["position"] ?></td>
                         <td style="text-align:center"><?php echo date(Config::get('dateformat'),strtotime($member["membersince"])) ?></td>
                         <td style="text-align:right;white-space: nowrap;">
<?php
		if((!isset($member["email"])||is_null($member["email"])||$member["email"]=="")&&$memb->isBoard()) {
?>
                    <a href="member.php?membernum=<?php echo $member["membernum"] ?>" title="<?=_("E-mail address missing")?>"><span class="glyphicon glyphicon-warning-sign" style="color:red"></span></a>  
<?php
		}
?>
                    <a href="viewmember.php?id=<?php echo $member["membernum"] ?>" title="<?=_("View")?>"><span class="glyphicon glyphicon-search"></span></a>
<?php
    	if($memb->isBoard()) {
?>
                    <a href="member.php?membernum=<?php echo $member["membernum"] ?>" title="<?=_("Edit")?>"><span class="glyphicon glyphicon-edit"></span></a>
<?php
        	if($member["active"]) {
?>
                    <a onclick="window.location = 'freeze.php?'+getSortKey()+'scrollpos='+self.pageYOffset+'&amp;id=<?= $member["membernum"] ?>&amp;inact=<?= $showinactive ?>';return false;" href="" title="<?=_("Freeze")?>"><span class="glyphicon glyphicon-remove" style="color:red"></span></a>
<?php
        	} else {
?>
                    <a onclick="window.location = 'unfreeze.php?'+getSortKey()+'scrollpos='+self.pageYOffset+'&amp;id=<?= $member["membernum"] ?>&amp;inact=<?= $showinactive ?>';return false;" href="" title="<?=_("Unfreeze")?>"><span class="glyphicon glyphicon-plus" style="color:green"></span></a>
<?php            
        	}
	        if($member["access"]) {
	            $jsaccess = "window.location";
	            $jssuff = ";return false;";
?>
                    
                    <a onclick="<?php echo $jsaccess ?> = 'access.php?'+getSortKey()+'scrollpos='+self.pageYOffset+'&amp;id=<?php echo $member["membernum"] ?>&amp;inact=<?= $showinactive ?>&amp;action=remove'<?php echo $jssuff; ?>" href="" title="<?=_("Forum access")?>"><span class="glyphicon glyphicon-comment" style="color:green"></span></a>
<?php
        	} else {
            $jsaccess = "var r = confirm('".sprintf(_("Do you want to e-mail %s about this access?"),$member['username'])."');if(r){var locadd='&amp;mail=1&amp;mailaddr=".$member['email']."&amp;mailuser=".$member['username']."';} else {var locadd='&amp;mail=0';}; newloc";
            $jssuff = ";newloc = newloc + locadd;window.location = newloc;return false;";
?>
                    <a onclick="<?php echo $jsaccess ?> = 'access.php?'+getSortKey()+'scrollpos='+self.pageYOffset+'&amp;id=<?php echo $member["membernum"] ?>&amp;inact=<?= $showinactive ?>&amp;action=add'<?php echo $jssuff; ?>" href="" title="<?=_("Forum access")?>"><span class="glyphicon glyphicon-comment" style="color:red"></span></a>
<?php
        	}
        }
?>
                </td>
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