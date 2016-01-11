<?php
    $boardviewstring = "";

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
  </div><!-- /container -->
</div>