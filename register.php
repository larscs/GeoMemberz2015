<?php
    require_once 'core/init.php';
    if(Input::exists('post')) {    
        // We have POST data    
        // Validate input
        $myVal = new Validation;
        $myVal->errors = Array();               // Clear the array
        if($myVal->checkFormData($_POST, false)) {
            // Validation successful.
            // Prepare password hash
            $t_hasher = new PasswordHash(6, true);
            // Generate validation hash
            $valhash = bin2hex(mcrypt_create_iv(8, MCRYPT_DEV_URANDOM));
            $userdetails = $_POST;      // Copy POST data to a new var
            unset($userdetails['reppassword']);         // remove, as this has been checked, and no DB field exists for this
            // Change and add necessary fields
            $userdetails['email'] = trim($userdetails['email']);
            $userdetails['password'] = $t_hasher->HashPassword(Input::getPost('password'));
            $userdetails['validationhash'] = $valhash;
            $userdetails['validationok'] = 0;                                               // To prevent logging in before validated
            $userdetails['membersince'] = date('Y-m-d');                                    // Current date
            $userdetails['birthdate'] = date('Y-m-d',strtotime($userdetails['birthdate'])); // Ensure date is in ISO format
            $userdetails['active'] = 1;
            

            // Add new member to database, including password hash and validation hash
            $memb = Members::getInstance();
            if(!$newID=$memb->addUser($userdetails)) {
                // Adding the user failed, show error page
                Session::errorPage(_("Database error"),_("Error when adding new user to the database.<br/>Please report this to ")."<a href=\"mailto:".Config::get('autoemail')."?subject="._("Error adding new user to database")."\">".Config::get('autoemail')."</a>.");   
            }
            // Get info about new user
            $memberdetails = $memb->getUserData($newID);
            $feeperdata = $memb->getCurrentFeePeriodData();
            // Send validation mail with validation hash to user
            $userfullname = $memberdetails['firstname']." ".$memberdetails['middlename']." ".$memberdetails['lastname'];
            $userfullname = str_replace("  "," ",$userfullname); // In case middle name is not used
            $altbody=sprintf(_("Welcome as a new member in %s, %s!"),Config::get('assocname'),$memberdetails['username'])."\r\n\r\n".
                          _("Before you can log in, you need to validate your e-mail address by clicking on the link below")."\r\n\r\n".
                          "{{LINK}}\r\n\r\n".
                          sprintf(_("Then, please pay the fee of kr. <strong>%s</strong> to account number <strong>%s</strong>, marking the payment with <strong>«Member %s»</strong>."),number_format($feeperdata['perAmount'],2,Config::get('dp'),Config::get('ts')),Config::get('assocbank'),$memberdetails['membernum'])."\r\n\r\n".
                          sprintf(_("If you didn't register at %s, please reply to this e-mail and say so,"),Config::get('assocname'))."\r\n".
                          _("and we will remove your e-mail address immediately.")."\r\n\r\n".
                          _("Best regards,")."\r\n".
                          Config::get('assocname');
            $body = str_replace("\r\n","<br/>",$altbody);
            $altbody=str_replace("{{LINK}}",Config::get('baseaddr')."validate.php?h=".$valhash,$altbody);
            $body = str_replace("{{LINK}}",'<a href="'.Config::get('baseaddr')."validate.php?h=".$valhash.'">'.Config::get('baseaddr')."validate.php?h=".$valhash.'</a>',$body);
            if(!$memb->mailUser(Config::get('autoemail'), Config::get('autoemailname'), $memberdetails['email'], $userfullname,sprintf(_("E-mail address validation for %s member account"),Config::get('assocname')), $body, $altbody)) {
                // Mailing failed, show error page
                Session::errorPage(_("Mail error"),_("Error when sending mail.<br/>Please report this to ")."<a href=\"mailto:".Config::get('autoemail')."?subject="._("Error sending mail")."\">".Config::get('autoemail')."</a>.");   
            }
            //Misc::dump($userdetails,true);
            // Send notification mail to board
            $altbody=sprintf(_("A new member has registered on %s:"),Config::get('assocname'))."\r\n\r\n".
                                sprintf(_("Username:    %s"),$memberdetails["username"])."\r\n".
                                sprintf(_("GC-nick:     %s"),$memberdetails["gcnick"])."\r\n".
                                sprintf(_("First name:  %s"),$memberdetails["firstname"])."\r\n".
                                sprintf(_("Last name:   %s"),$memberdetails["lastname"])."\r\n\r\n".
                                sprintf(_("Please go to %s for more details."),Config::get('baseaddr'))."\r\n\r\n".
                                _("Best regards,")."\r\n".
                                Config::get('assocname');
            $body=sprintf(_("<p>A new member has registered on %s:"),Config::get('assocname'))."</p>".
                                "<table style=\"font-family:Trebuchet MS, Helvetica, sans-serif\">".
                                sprintf(_("<tr><th style=\"text-align:left\">Username:</th><td>%s</td></tr>"),$memberdetails["username"]).
                                sprintf(_("<tr><th style=\"text-align:left\">GC-nick:</th><td>%s</td></tr>"),$memberdetails["gcnick"]).
                                sprintf(_("<tr><th style=\"text-align:left\">First name:</th><td>%s</td></tr>"),$memberdetails["firstname"]).
                                sprintf(_("<tr><th style=\"text-align:left\">Last name:</th><td>%s</td></tr>"),$memberdetails["lastname"]).
                                "</table>".
                                sprintf(_("<p>Please go to <a href=\"%s\">%s</a> for more details.</p>"),Config::get('baseaddr'),Config::get('baseaddr')).
                                sprintf(_("<p>Best regards,<br/>%s</p>"),Config::get('assocname'));
            
            if(!$memb->mailUser(Config::get('autoemail'), Config::get('autoemailname'), Config::get('assocemail'), Config::get('assocname'), _("New member alert"), $body, $altbody)) {
                // Mailing failed, show error page
                Session::errorPage(_("Mail error"),_("Error when sending notification mail to the board. However, your account has been validated, so you can <a href=\"login.php\">log in</a>.<br/>Please report this error to ")."<a href=\"mailto:".Config::get('autoemail')."?subject="._("Error sending notification mail")."\">".Config::get('autoemail')."</a>.");   
            }
            // Redirect to thankyou.php
            Session::redirect('thankyou.php');           
        }
        // Validation returned false. $myVal->errors now contains an array of error messages, keyed with the field name.
        $errors = $myVal->errors;
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
 include_once "includes/headerbarbare.php";
?>
<!-- /Header -->
    <div class="container">
        <div class="row">

                <form class="form-horizontal" role="form" action="" method="post">
                    <div class="form-group">
                        <h4><?=_("Account details")?></h4>
                    </div>
<?php
    $helptext =_("<div class='popoverhead'>Username in the member system.</div>Normally, it makes most sense if this is the same as your Geocaching name. You might however have reasons for choosing a different username.");
    if(isset($errors["username"])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors["username"]."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="username" class="col-sm-3 control-label"><?=_("Username")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="username" name="username" placeholder="<?=_("Username")?>" value="<?php if(isset($_POST["username"])) {echo $_POST["username"];} ?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"/>
                            <?php echo $errormsg ?>
                        </div>
                    </div>
<?php
    $helptext = _("<div class='popoverhead'>Your password.</div>We don't store your actual password on our server. Therefore we cannot recover it if you lose it, but in that case you can reset it.");
    if(isset($errors["password"])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors["password"]."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="password" class="col-sm-3 control-label"><?=_("Password")?></label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password" name="password" placeholder="<?=_("Password")?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"/>
                            <?php echo $errormsg ?>
                        </div>
                    </div>
<?php
    $helptext = _("Repeat your password to make sure you typed it correctly.");
    if(isset($errors["reppassword"])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors["reppassword"]."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="reppassword" class="col-sm-3 control-label"><?=_("Repeat password")?></label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="reppassword" name="reppassword" placeholder="<?=_("Repeat password")?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"/>
                            <?php echo $errormsg ?>
                        </div>
                    </div>
<?php
    $helptext = _("<div class='popoverhead'>Your e-mail address.</div>This is our primary communication channel, and required. Your e-mail address will need to be verified before you can log on to your account.");
    if(isset($errors["email"])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors["email"]."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="email" class="col-sm-3 control-label"><?=_("E-mail address")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="email" name="email" placeholder="<?=_("E-mail address")?>" value="<?php if(isset($_POST["email"])) {echo $_POST["email"];} ?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"/>
                            <?php echo $errormsg ?>
                        </div>
                    </div>
                    <div class="form-group" style="clear:left">
                        <h4><?=_("Member details")?></h4>
                    </div>
<?php
    $helptext = _("<div class='popoverhead'>Your geocaching name.</div>If you are a team, you can choose whether the other members should be separate accounts, or &quot;submembers&quot; to the first account created. You will receive one accumulated fee invoice including yourself and all your submembers.");
    if(isset($errors["gcnick"])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors["gcnick"]."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="gcnick" class="col-sm-3 control-label"><?=_("Geocaching name")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="gcnick" name="gcnick" placeholder="<?=_("Geocaching name")?>" value="<?php if(isset($_POST["gcnick"])) {echo $_POST["gcnick"];} ?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"/>
                            <?php echo $errormsg ?>
                        </div>
                    </div>
<?php
    $helptext = _("<div class='popoverhead'>Your first name.</div>If your have more than one first name, all should go here.");
    if(isset($errors["firstname"])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors["firstname"]."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="firstname" class="col-sm-3 control-label"><?=_("First name")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="firstname" name="firstname" placeholder="<?=_("First name")?>" value="<?php if(isset($_POST["firstname"])) {echo $_POST["firstname"];} ?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"/>
                            <?php echo $errormsg ?>
                        </div>
                    </div>
<?php
    $helptext = _("<div class='popoverhead'>Your middle name.</div>If you don&apos;t have one, leave this empty. This is displayed between your first and last names where full name is used.");
    if(isset($errors["middlename"])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors["middlename"]."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="middlename" class="col-sm-3 control-label"><?=_("Middle name")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="middlename" name="middlename" placeholder="<?=_("Middle name")?>" value="<?php if(isset($_POST["middlename"])) {echo $_POST["middlename"];} ?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"/>
                            <?php echo $errormsg ?>
                        </div>
                    </div>                    
<?php
    $helptext = _("<div class='popoverhead'>Your last name.</div>If you have more than one last name, the one that the name should be sorted by, should go here, and the others under &quot;Middle name&quot;.");
    if(isset($errors["lastname"])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors["lastname"]."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="lastname" class="col-sm-3 control-label"><?=_("Last name")?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="lastname" name="lastname" placeholder="<?=_("Last name")?>" value="<?php if(isset($_POST["lastname"])) {echo $_POST["lastname"];} ?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"/>
                            <?php echo $errormsg ?>
                        </div>
                    </div>                    
<?php
    $helptext = _("<div class='popoverhead'>Your address.</div>We need this in situations where we need to send ordered products or giveaways to the members. You can use the enter key to type multiple lines.");
    if(isset($errors["address"])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors["address"]."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="address" class="col-sm-3 control-label"><?=_("Address")?></label>
                        <div class="col-sm-9">
                            <textarea style="height:75px" class="form-control" id="address" name="address" placeholder="<?=_("Address")?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"><?php if(isset($_POST["address"])) {echo $_POST["address"];} ?></textarea>
                            <?php echo $errormsg ?>
                        </div>
                    </div> 
<?php
    $helptext = _("<div class='popoverhead'>Your phone number.</div>We need this as backup for the e-mail, if e-mail doesn&apos;t get through.");
    if(isset($errors["phone"])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors["phone"]."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="phone" class="col-sm-3 control-label"><?=_("Phone number")?></label>
                        <div class="col-sm-9">
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="<?=_("Phone number")?>" value="<?php if(isset($_POST["phone"])) {echo $_POST["phone"];} ?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"/>
                            <?php echo $errormsg ?>
                        </div>
                    </div> 
<?php
    $helptext = _("<div class='popoverhead'>Your birth date.</div>We need this for two reasons: Knowing how many members we have in each age segment, which is needed for some kinds of funding. Also, there is an age limit for membership fees.");
    if(isset($errors["birthdate"])) {$errorstyle = " has-error"; $errormsg = "<div class=\"errormsg\">".$errors["birthdate"]."</div>";} else {$errorstyle = ""; $errormsg = "";}
?>
                    <div class="form-group<?php echo $errorstyle ?>">
                        <label for="birthdate" class="col-sm-3 control-label"><?=_("Birth date")?></label>
                        <div class="col-sm-4">
                            <div class="input-group date" id="dp1" data-date="<?php if(isset($_POST["birthdate"])) {echo Misc::formatDate($_POST["birthdate"]);} ?>" data-date-format="<?= Config::get('altdateformat') ?>">
                                <input class="form-control" type="text" readonly="readonly" id="birthdate" name="birthdate" value="<?php if(isset($_POST["birthdate"])) {echo Misc::formatDate($_POST["birthdate"]);} ?>" data-trigger="focus" data-toggle="popover" data-placement="top" data-html="true" data-content="<?php echo $helptext; ?>"/>
                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            </div>
                            <?php echo $errormsg ?>
                        </div>
                    </div>
                    <div class="form-group">

                    </div>
                    <p class="bg-warning" style="padding:15px">
                    <?php echo _("Except for the Username and Geocaching name, none of these details will be visible to other members until you choose to make them visible from the <strong>My details</strong> page once your are logged in. Only members of the board can see everything.")?>
                    </p>                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary form-control"><?=_("Register my account")?></button>
                    </div>
                </form>
        </div>

    </div>
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


    </script>

  </body>
</html>