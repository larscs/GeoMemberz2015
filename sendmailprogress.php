<?php 
    session_start(); 
    header('Content-Type:text/plain');
    echo $_SESSION["mailprogress"]."~~~~".$_SESSION["mailprogtext"]; 
