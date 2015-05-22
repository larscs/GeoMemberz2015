<?php
	require_once 'core/init.php';
	if(!Session::isLoggedIn()) {Session::redirect(404);}  // Use this on all pages requiring the user to be logged in
	$memb = Members::getInstance(); 			            // Use this on all pages using the database
	if(!Input::exists('get')) {Session::errorPage(_("Error"),_("One or more arguments are required, none found."));}
	if(!Input::getGet('id')) {Session::errorPage(_("Error"),_("One or more arguments are invalid."));}
	if(!is_numeric(Input::getGet('id'))) {Session::errorPage(_("Error"),_("An argument that should be numeric, wasn't."));}
?><!DOCTYPE html>
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
	    <script src="js/bootstrap.min.js"></script>

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
      	<h3><?=_("View member")?></h3>
      <!-- column 2 -->	
<?php
$memberdata = $memb->getUserData(Input::getGet('id'));
// assign to vars
$membernum  = $memberdata["membernum"];
$username   = $memberdata["username"];
$gcnick     = $memberdata["gcnick"];
$firstname  = $memberdata["firstname"];
$middlename = $memberdata["middlename"];
$lastname   = $memberdata["lastname"];
$address    = str_replace("\n","<br/>",$memberdata["address"]);
$birthdate  = Misc::formatDate($memberdata["birthdate"]);
$email      = $memberdata["email"];
$phone      = $memberdata["phone"];
$position   = $memberdata["position"];
if(!$position) {$position = "<span style=\"color:silver\">"._("(None)")."</span>";}
$active     = ($memberdata["active"]) ? _("Active") : _("Inactive");
$membersince= Misc::formatDate($memberdata["membersince"]);

$feestatus = $memb->memberPaymentsList($membernum);
// Now change vars according to board status and privacy settings.
// If an element is public, it is displayed normally always.
// If an element is not public, it should be displayed as "(Undisclosed)" by default.
// However, if the user is oneself or a child, it should be displayed anyway.
// If the viewer is a board member, it should be marked red if it is not public. 
// Determine if member viewed is oneself or a child
$ownorchild = false;
if($memberdata["membernum"]==Session::getSessionVar('userID') || $memberdata["parent"]==Session::getSessionVar('userID')) {
    $ownorchild = true;
}
// Determine if viewing user is a board member
$boardmember = $memb->isBoard();

if(!$memberdata["pubemail"]) {if($boardmember) { $email = "<span style=\"color:red\">".$email."</span>"; } elseif(!$ownorchild) { $email = "<span style=\"color:silver\">("._("Undisclosed").")</span>"; } else { $email = "<span style=\"color:red\">".$email."</span>"; }} 
if(!$memberdata["pubfirstname"]) { if($boardmember) { $firstname = "<span style=\"color:red\">".$firstname."</span>"; } elseif(!$ownorchild) { $firstname = "<span style=\"color:silver\">("._("Undisclosed").")</span>"; } else { $firstname = "<span style=\"color:red\">".$firstname."</span>"; }}
if(!$memberdata["pubmiddlename"]) {if($boardmember) { $middlename = "<span style=\"color:red\">".$middlename."</span>"; } elseif(!$ownorchild) { $middlename = ""; } else { $middlename = "<span style=\"color:red\">".$middlename."</span>"; }}
if(!$memberdata["publastname"]) { if($boardmember) { $lastname = "<span style=\"color:red\">".$lastname."</span>"; } elseif(!$ownorchild) { $lastname = "<span style=\"color:silver\">("._("Undisclosed").")</span>"; } else { $lastname = "<span style=\"color:red\">".$lastname."</span>"; }}
if(!$memberdata["pubaddress"]) {if($boardmember) {$address = "<span style=\"color:red\">".$address."</span>";}elseif(!$ownorchild) {$address = "<span style=\"color:silver\">("._("Undisclosed").")</span>";} else { $address = "<span style=\"color:red\">".$address."</span>"; }}
if(!$memberdata["pubphone"]) { if($boardmember) { $phone = "<span style=\"color:red\">".$phone."</span>"; } elseif(!$ownorchild) { $phone = "<span style=\"color:silver\">("._("Undisclosed").")</span>"; } else { $phone = "<span style=\"color:red\">".$phone."</span>"; }}
if(!$memberdata["pubbirthdate"]) { if($boardmember) { $birthdate = "<span style=\"color:red\">".$birthdate."</span>"; } elseif(!$ownorchild) { $birthdate = "<span style=\"color:silver\">("._("Undisclosed").")</span>"; } else { $birthdate = "<span style=\"color:red\">".$birthdate."</span>"; }}

$usercomment = $memberdata["usercomment"] != "" && isset($memberdata["usercomment"]) ? $memberdata["usercomment"] : "<span style=\"color:silver\">("._("Nothing entered").")</span>";
$boardcomment = $memberdata["boardcomment"] != "" && isset($memberdata["boardcomment"]) ? $memberdata["boardcomment"] : "<span style=\"color:silver\">("._("Nothing entered").")</span>";
// Combine names
$fullname = $firstname;
if($middlename) { $fullname .= " ".$middlename;}
$fullname .= " ".$lastname; 
// Simplify consecutive "(Undisclosed)". It can be up to three, so do twice
$fullname = str_replace("<span style=\"color:silver\">("._("Undisclosed").")</span> <span style=\"color:silver\">("._("Undisclosed").")</span>","<span style=\"color:silver\">("._("Undisclosed").")</span>",$fullname);
$fullname = str_replace("<span style=\"color:silver\">("._("Undisclosed").")</span> <span style=\"color:silver\">("._("Undisclosed").")</span>","<span style=\"color:silver\">("._("Undisclosed").")</span>",$fullname); 

?>
  
		<div class="row">
<?php
    if($boardmember) {
?>
            <?=_("Text in <span style=\"color:red\">red</span> is visible to board members only, and should not be disclosed.")?>
<?php
}
?>
            <table class="table table-striped">
            <tr>
                <th class="col-md-3"><?=_("Member number")?></th>
                <td class="col-md-9"><?=$membernum?></td>    
            </tr>
            <tr>
                <th class="col-md-3"><?=_("Username")?></th>
                <td class="col-md-9"><?=$username?></td>    
            </tr>
            <tr>
                <th><?=_("Geocaching name")?></th>
                <td><?=$gcnick?></td>    
            </tr>
            <tr>
                <th><?=_("Full name")?></th>
                <td><?=$fullname?></td>    
            </tr>
            <tr>
                <th><?=_("Address")?></th>
                <td><?=$address?></td>    
            </tr>
            <tr>
                <th><?=_("Birth date")?></th>
                <td><?=$birthdate?></td>    
            </tr>
            <tr>
                <th><?=_("E-mail address")?></th>
                <td><?=$email?></td>    
            </tr>
            <tr>
                <th><?=_("Phone number")?></th>
                <td><?=$phone?></td>    
            </tr>
            <tr>
                <th><?=_("Position")?></th>
                <td><?=$position?></td>    
            </tr>  
            <tr>
                <th><?=_("Member since")?></th>
                <td><?=$membersince?></td>    
            </tr> 

                <?php
if($boardmember) {
?>          
            <tr>
                <th><?=_("Status")?></th>
                <td><?=$active?></td>    
            </tr> 
<?php
}
if($boardmember || $ownorchild) {
?>
            <tr>
                <th><?=_("User comment")?></th>
                <td><span style="color:red"><?=$usercomment?></span></td>    
            </tr>
<?php
}
if($boardmember) {
?>          
            <tr>
                <th style="background-color:#FFF0F0"><span style="color:red"><?=_("Board comment")?></span></th>
                <td style="background-color:#FFF0F0"><span style="color:red"><?=$boardcomment?></span></td>    
            </tr> 
<?php
}
if($boardmember || $ownorchild) {
?>
            <tr>
                <th><?=_("Fee status")?></th>
                <td><?=$feestatus?></td>    
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
	</body>
</html>