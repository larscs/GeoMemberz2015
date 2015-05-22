<?php
    require_once "core/init.php";
    // First, determine if user is logged in.
    if(Session::isLoggedIn()) {
        Session::redirect('dashboard.php');
    } else {
        Session::redirect('login.php');
    }

?>