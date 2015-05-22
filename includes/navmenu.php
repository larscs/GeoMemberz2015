<?php
    $thisfile = basename($_SERVER["SCRIPT_FILENAME"],'.php');
?>
            <h4><?=_("Navigation")?></h4>
            <ul class="list-unstyled collapse in" id="userMenu" style="font-size:0.96em">
                <li<?php if($thisfile == 'dashboard') echo " class=\"active\"" ?>> <a href="dashboard.php"><i class="glyphicon glyphicon-dashboard"></i> <?=_("Dashboard")?></a></li>
                <li<?php if($thisfile == 'members') echo " class=\"active\"" ?>><a href="members.php"><i class="glyphicon glyphicon-user"></i> <?=_("Members")?></a></li>
                <li<?php if($thisfile == 'member') echo " class=\"active\"" ?>><a href="member.php"><i class="glyphicon glyphicon-list-alt"></i> <?=_("My details")?></a></li>
<?php
    if($memb->isBoard()) {
?>
                <li<?php if($thisfile == 'feeperiods') echo " class=\"active\"" ?>><a href="feeperiods.php"><i class="glyphicon glyphicon-usd"></i> <?=_("Fee periods")?></a></li>
                <li<?php if($thisfile == 'feepayments') echo " class=\"active\"" ?>><a href="feepayments.php"><i class="glyphicon glyphicon-ok"></i> <?=_("Fee payments")?></a></li>
                <li<?php if($thisfile == 'sendmail') echo " class=\"active\"" ?>><a href="sendmail.php"><i class="glyphicon glyphicon-envelope"></i> <?=_("Send e-mail")?></a></li>
                <li<?php if($thisfile == 'changelog') echo " class=\"active\"" ?>><a href="changelog.php"><i class="glyphicon glyphicon-saved"></i> <?=_("Change log")?></a></li>
<?php
}
?>
            </ul>
