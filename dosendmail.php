<?php
	require_once 'core/init.php';                         // Must be present on all pages using settings and classes
	if(!Session::isLoggedIn()) {Session::redirect(404);}  // Use this on all pages requiring the user to be logged in
	$memb = Members::getInstance(); 			            // Use this on all pages using the database
	if(!$memb->isBoard()) {Session::redirect(404);}			// Non-members don't need to know this page exists. 

    if(Input::exists('post')) {
        if(!Input::getPost('subj')) {
            Session::errorPage(_("Error"),_("No subject provided"));            
        }
        if(!Input::getPost('sender')) {
            Session::errorPage(_("Error"),_("No sender provided"));            
        }
        if(!Input::getPost('target')) {
            Session::errorPage(_("Error"),_("No target group provided"));            
        }
        if(!Input::getPost('body')) {
            Session::errorPage(_("Error"),_("No body text provided"));            
        }
        // If we got here, we have all we need. Set vars with content.
        $jsondata = json_encode($_POST);
        $subj = Input::getPost('subj');
        $sender = Input::getPost('sender');
        $target = Input::getPost('target');
        $body = Input::getPost('body');
    } else {
        // No post data, show a 404
        Session::redirect(404);
    }

?><!DOCTYPE html>
<html lang="<?=_("en")?>">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<title><?=_("GeoBergen Member Pages")?></title>
		<meta name="generator" content="Bootply" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link href="css/bootstrap.min.css" rel="stylesheet">
                <link href="css/styles.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
        </head>
        <script src="js/jquery.js"></script>
        <script type="text/javascript">
        $(document).ready(function() {
            //kick off the process
            $.ajax({
                // Send as POST data the following: (Forwarding the received data)
                method: "POST",
                data: <?=$jsondata?>,
                dataType: "json",
                url: 'dodosendmail.php',         
                success: function(data) {}     
            }); 
            //start polling
            (function poll(){
               setTimeout(function(){
                  $.ajax({ 
                     url: "sendmailprogress.php",
                     success: function(data){
                        var dataparts = data.split("~~~~");
                        pct = dataparts[0];
                        txt = dataparts[1];
                        progelem = document.getElementById('progbar');
                        progelem.setAttribute('aria-valuenow',pct);
                        progelem.style.width=pct+"%";
                        progelem.innerHTML = pct+" %";
                        document.getElementById('progtext').innerHTML = txt;
                         if(pct<100) {
                            poll();
                         } else {
                            progelem.className = "progress-bar progress-bar-success progress-bar-striped active";
                         }
                     }, 
                     dataType: "text"
                 });
              }, 500);
            })();
        });
        </script>
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
        // Get info on the viewing user, for the signature
        $viewer = $memb->getUserData(Session::getSessionVar('userID'));
        $total=0;
        $failed=0;
        $errmsg="";
        
?>  
      
  	</div><!-- /col-3 -->
        <div class="col-md-10">
      	<h3><?=_("Sending e-mail")?></h3>
        <!-- column 2 -->	
            <div class="row">
                <div class="progress">
                  <div id="progbar" class="progress-bar progress-bar-striped active" role="progressbar"
                  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                    0 %
                  </div>
                </div>
                <div class="well well-lg" id="progtext"></div>
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