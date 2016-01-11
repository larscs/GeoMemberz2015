<?php
    $boardviewstring = "";
    if($memb->isBoard()) {
        $boardviewstring = " <span class=\"badge\" style=\"color:white;background-color:red;margin-left:10px\">"._("Board view")."</span>";
    }
?>
<div id="top-nav" class="navbar navbar-inverse navbar-static-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
      </button>
      <a href="http://www.geobergen.no/"><img src="<?= Config::get('logo30') ?>" alt="<?= Config::get('assocname')._(" logo")?>" style="float:left;margin-top:11px;margin-right:10px"/></a>
      <p class="navbar-brand" style="margin-top:5px;cursor:default"><?=_("Member pages")?> <?php echo $boardviewstring ?></p>
    </div>
    <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav navbar-right">
          <li>
              <p class="navbar-text">
                  <?= Session::getFlags($_GET) ?>
              </p>
          </li>
        <li>
          <p class="navbar-text" style="margin-left:15px;color:silver"><i class="glyphicon glyphicon-user"></i> <?php echo $memb->getUserDetail($_SESSION[Config::get('sessionprefix')."userID"],'username') ?> </p>
        </li>
        <li><a href="logout.php"><i class="glyphicon glyphicon-lock"></i> <?=_("Log out")?></a></li>
      </ul>
    </div>
  </div><!-- /container -->
</div>