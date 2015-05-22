<div id="top-nav" class="navbar navbar-inverse navbar-static-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
      </button>
      <a href="<?php echo Config::get('baseaddr') ?>"><img src="<?= Config::get('logo30') ?>" alt="<?= Config::get('assocname')._(" logo")?>" style="float:left;margin-top:11px;margin-right:10px"/></a>
      <span class="navbar-brand" style="margin-top:5px;cursor:default"><?=_("Member pages")?></span>
    </div>
    <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav navbar-right">
          <li>
              <p class="navbar-text">

                  <?= Session::getFlags($_GET) ?>
              </p>
          </li>
      </ul>
    </div>

  </div><!-- /container -->
</div>
