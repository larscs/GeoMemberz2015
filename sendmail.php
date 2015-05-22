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
<?php
    // Read template files into array
    // Enumerate files in "mail" folder
    $subjects = Array();
    $bodies = Array();
    $tempentries = Array();
    $mailfiles = scandir('mail');
    foreach ($mailfiles as $file) {
        if($file != "." && $file != "..") {
            $filecontents = file_get_contents('mail/'.$file);
            // Change \r\n into only \n (In case any file was saved in Windows format)
            $filecontents = str_replace("\r\n","\n",$filecontents);
            // Now separate into lines
            $filelines = explode("\n",$filecontents);
            // Now shift the first line off and push it into tempentries array
            array_push($tempentries,array_shift($filelines));
            // quote and js concatenate lines
            foreach($filelines as $key=>&$fileline) {
                // Leave the subject line alone
                if($key==max(array_keys($filelines))) {
                    $fileline = "\"".$fileline."\"";
                } elseif($key>0) {
                    $fileline = "\"".$fileline."\\r\\n\"+";
                }
            }

            // Now shift the second line off and push it into subjects array
            array_push($subjects,array_shift($filelines));
            // Combine into one string again
            $filecontents = implode("\r\n",$filelines);
            array_push($bodies,$filecontents);
        }
    }
    $subjvar = "[";
    foreach ($subjects as $subject) {
        $subjvar .= '"'.$subject.'",';
    }
    $subjvar .= "];\r\n";
    $bodyvar = "[";
    foreach ($bodies as $body) {
        $bodyvar .= $body.',';
    }
    $bodyvar .= "];\r\n";    
?>

        <script type="text/javascript">
            var selectedTemplate = 1;
            var subjects =  <?=$subjvar?>
            var templates = <?=$bodyvar?>;
            function loadTemplate(tempNo) {
                document.getElementById("body").value = templates[tempNo-1];
                document.getElementById("subj").value = subjects[tempNo-1];
            }
            function insVar(strPlaceholder) {
                el = document.getElementById("body");
                var val = el.value, endIndex, range, doc = el.ownerDocument;
                if(typeof el.selectionStart == "number"
                        && typeof el.selectionEnd == "number") {
                    endIndex = el.selectionEnd;
                    el.value = val.slice(0,endIndex)+strPlaceholder+val.slice(endIndex);
                    el.selectionStart = el.selectionEnd = endIndex + strPlaceholder.length;
                } else if (doc.selection != "undefined" && doc.selection.createRange) {
                    el.focus();
                    range = doc.selection.createRange();
                    range.collapse(false);
                    range.text = strPlaceholder;
                    range.select();
                }
            }
            function getTarget(targetId) {
                var xmlhttp;
                if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
                   xmlhttp=new XMLHttpRequest();
                } else {// code for IE6, IE5
                   xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                }           
                xmlhttp.onreadystatechange=function() {
                    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                        if(xmlhttp.responseText == "") {
                            document.getElementById("targetspan").innerHTML="<em><?=_("Your currently selected target group contains no members.")?></em>";
                            document.getElementById("memberpreview").innerHTML="";
                            document.getElementById("nummembs2").innerHTML=0;
                            //document.getElementById("sendbutton").disabled=true;
                        } else {
                            //document.getElementById("sendbutton").disabled=false;
                            document.getElementById("targetspan").innerHTML="<em><?=_("Your currently selected target group contains the following <span id=\\\"nummembs1\\\">0</span> <span id=\\\"noun1\\\">members</span>:")?></em>";
                            document.getElementById("memberpreview").innerHTML=xmlhttp.responseText;
                            var nicks = xmlhttp.responseText.split(',');
                            document.getElementById("nummembs1").innerHTML=nicks.length;
                            document.getElementById("nummembs2").innerHTML=nicks.length;
                            if(nicks.length == 1) {
                                document.getElementById("nummembs1").innerHTML="";
                                document.getElementById("noun1").innerHTML="<?=_("member")?>";
                                document.getElementById("noun2").innerHTML="<?=_("member")?>";
                            } else {
                                document.getElementById("noun1").innerHTML="<?=_("members")?>";
                                document.getElementById("noun2").innerHTML="<?=_("members")?>";                            
                            }
                        }
                    }
                }
                xmlhttp.open("GET","targetgroup.php?id="+targetId,true);
                xmlhttp.send();
            }
            function getPreview(targetId) {
                var xmlhttp;
                if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
                   xmlhttp=new XMLHttpRequest();
                } else {// code for IE6, IE5
                   xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                }           
                xmlhttp.onreadystatechange=function() {
                    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                        if(xmlhttp.responseText == "") {
                            document.getElementById("preview").innerHTML="";
                        } else {
                            document.getElementById("preview").innerHTML=xmlhttp.responseText;
                        }
                    }
                }
                xmlhttp.open("GET","mailpreview.php?id="+targetId+"&subj="+encodeURIComponent(document.getElementById("subj").value)+"&body="+encodeURIComponent(document.getElementById("body").value),true);
                xmlhttp.send();
            }
            function clearPreview() {
                document.getElementById("preview").innerHTML="<center><em><?=_("Click on a member name above to display a preview for that member's mail")?></em></center>";
            }
        </script>
        </head>
	<body onload="getTarget(1)">
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
        // Get info on the viewing user, for the signature
        $viewer = $memb->getUserData(Session::getSessionVar('userID'));
        $viewerfullname = $viewer['firstname']." ".$viewer['middlename']." ".$viewer['lastname'];
        $viewerfullname = str_replace("  ", " ", $viewerfullname);
        
?>  
      
  	</div><!-- /col-3 -->
        <div class="col-md-10">
      	<h3><?=_("Send e-mail")?></h3>
        <!-- column 2 -->	
  
            <div class="row">
           
            
                <form class="form" action="dosendmail.php" method="post">
                    <!-- center left-->	
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="subj" class="col-sm-3 control-label" style="margin-top:5px"><?=_("Subject:")?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="subj" id="subj" placeholder="<?=_("E-mail subject")?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sender" class="col-sm-3 control-label" style="margin-top:5px"><?=_("Sender:")?></label>
                            <div class="col-sm-9">
                                <select class="form-control" name="sender" id="sender">
                                	<option value="<?=Config::get('autoemailname')?> &lt;<?=Config::get('autoemail')?>&gt;"><?=Config::get('autoemailname')?> &lt;<?=Config::get('autoemail')?>&gt;</option>
                                	<option value="<?=Config::get('assocemailname')?> &lt;<?=Config::get('assocemail')?>&gt;"><?=Config::get('assocemailname')?> &lt;<?=Config::get('assocemail')?>&gt;</option>
                                	<option value="<?= $viewerfullname ?> &lt;<?=$viewer['email']?>&gt;"><?= $viewerfullname ?> &lt;<?=$viewer['email']?>&gt;</option>
                                </select>
                            </div>
                        </div>                        
                        <div class="form-group">
                            <label for="target" class="col-sm-3 control-label" style="margin-top:5px"><?=_("Target group:")?></label>
                            <div class="col-sm-9">
                                <select class="form-control" name="target" id="target" onchange="getTarget(this.value);">
                                    <option value="1"><?=_("All members")?></option>
                                    <option value="2"><?=_("Inactive (frozen) members")?></option>
                                    <option value="3"><?=_("Primary members owing fee for current period")?></option>
                                    <option value="4"><?=_("All members excluding any submembers")?></option>
                                    <option value="5"><?=_("Primary members having submembers only")?></option>
                                    <option value="6"><?=_("Submembers only (excluding their parents)")?></option>
                                    <option value="7"><?=sprintf(_("Members over the age of %s"),Config::get('feeagelimit'))?></option>
                                    <option value="8"><?=sprintf(_("Members under age of %s"),Config::get('feeagelimit'))?></option>
                                    <option value="9"><?=_("Members with incomplete member data")?></option>
                                    <option value="10"><?=_("Yourself (for testing)")?></option>
                                </select>
                            </div>
                        </div>
                        <label for="body" class="col-sm-3 control-label" style="margin-top:5px"><?=_("Mail body:")?></label>
                        <textarea onkeyup="clearPreview();" class="form-control" rows="15" name="body" id="body"></textarea>
                        <br/>
                        <div class="well well-sm"><em id="targetspan"><?=_("Your currently selected target group contains the following <span id=\"nummembs1\">0</span> <span id=\"noun1\">members</span>:")?></em><br/><strong><span id="memberpreview"><span style="color:silver"><?=_("(None selected)")?></span></span></strong></div>
                        <strong><?=_("Preview")?></strong>
                        <div class="well well-sm" id="preview">
                            <center><em><?=_("Click on a member name above to display a preview for that member's mail")?></em></center>
                        </div>
                        <i class="glyphicon glyphicon-exclamation-sign" style="color:red"></i> <strong><?=_("Are you sure you want to send this message?")?></strong>
                        <div class="radio">
                            <label>
                                <input onclick="document.getElementById('sendbutton').disabled=false;" name="confirm" type="radio">
                                <?=_("Yes")?>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input onclick="document.getElementById('sendbutton').disabled=true;" name="confirm" type="radio" checked="checked">
                                <?=_("No")?> 
                            </label>
                        </div>                        
                        <button class="col-sm-12 btn btn-danger" id="sendbutton" disabled="disabled"><?=_("Send to")?> <span id="nummembs2">0</span> <span id="noun2"><?=_("members")?></span></button>
                    </div><!--/col-->
                </form>
                <div class="col-md-4">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?=_("Templates")?></h4>
                        </div>
                        <div class="panel-body">
<?php 
    $cnt = 0;
    $checkedstring = "checked=\"checked\"";
    foreach($tempentries as $tempentry) {
        $cnt++;
?>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="template" id="template<?=$cnt?>" value="<?=$cnt?>" <?=$checkedstring?> onclick="selectedTemplate=<?=$cnt?>;">
                                    <?=$tempentry ?>
                                </label>
                            </div>
<?php
        $checkedstring = "";
    }
?>
                            <button onclick="clearPreview();loadTemplate(selectedTemplate);return false;" class="btn btn-primary"><?=_("&lt; Load")?></button>
                        </div>
                    </div>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?=_("Variables")?></h4>
                        </div>
                        <div class="panel-body" style="font-size:0.9em">
                            <?=_("Click on the links below to insert variables in the body. If you need them in the subject, type them in manually.")?><br/><br/>
                            <a class="varlinks" onclick="insVar('%assocname%');return false;">&lt; <?=_("Association name")?> (%assocname%)</a><br/>
                            <a class="varlinks" onclick="insVar('%assocbank%');return false;">&lt; <?=_("Association account no.")?> (%assocbank%)</a><br/>
                            <a class="varlinks" onclick="insVar('%username%');return false;">&lt; <?=_("Username")?> (%username%)</a><br/>
                            <a class="varlinks" onclick="insVar('%gcname%');return false;">&lt; <?=_("Geocaching name")?> (%gcname%)</a><br/>
                            <a class="varlinks" onclick="insVar('%firstname%');return false;">&lt; <?=_("First name")?> (%firstname%)</a><br/>
                            <a class="varlinks" onclick="insVar('%middlename%');return false;">&lt; <?=_("Middle name")?> (%middlename%)</a><br/>
                            <a class="varlinks" onclick="insVar('%lastname%');return false;">&lt; <?=_("Last name")?> (%lastname%)</a><br/>
                            <a class="varlinks" onclick="insVar('%fullname%');return false;">&lt; <?=_("Full name")?> (%fullname%)</a><br/>
                            <a class="varlinks" onclick="insVar('%birthdate%');return false;">&lt; <?=_("Birth date")?> (%birthdate%)</a><br/>
                            <a class="varlinks" onclick="insVar('%membersince%');return false;">&lt; <?=_("Member since")?> (%membersince%)</a><br/>
                            <a class="varlinks" onclick="insVar('%address%');return false;">&lt; <?=_("Address")?> (%address%)</a><br/>
                            <a class="varlinks" onclick="insVar('%email%');return false;">&lt; <?=_("E-mail address")?> (%email%)</a><br/>
                            <a class="varlinks" onclick="insVar('%phone%');return false;">&lt; <?=_("Phone #")?> (%phone%)</a><br/>
                            <a class="varlinks" onclick="insVar('%position%');return false;">&lt; <?=_("Position")?> (%position%)</a><br/>
                            <a class="varlinks" onclick="insVar('%parent%');return false;">&lt; <?=_("Parent member")?> (%parent%)</a><br/>
                            <a class="varlinks" onclick="insVar('%membernum%');return false;">&lt; <?=_("Member #")?> (%membernum%)</a><br/>
                            <a class="varlinks" onclick="insVar('%submembers%');return false;">&lt; <?=_("Submembers")?> (%submembers%)</a><br/>
                            <a class="varlinks" onclick="insVar('%submembernos%');return false;">&lt; <?=_("Submember #s")?> (%submembernos%)</a><br/>
                            <a class="varlinks" onclick="insVar('%amountdue%');return false;">&lt; <?=_("Fee amount due")?> (%amountdue%)</a><br/><br/>
                            <div class="well well-sm" style="clear:both"><?= $viewerfullname ?><br/><?= $viewer["position"]?><br/><?= Config::get('assocname')?><br/><br/>
                            <a class="varlinks" onclick="insVar('%signature%');return false;">&lt; <?=_("Signature")?> (%signature%)</a><br/>
                            </div>

                        </div>
                    </div>
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