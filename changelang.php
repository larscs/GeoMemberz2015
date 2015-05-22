<?php
    require_once "core/init.php";
        
    if(isset($_GET["lang"])) {
        // Set session var and bind locale to the given language
        Session::setLang($_GET["lang"]);

        if(isset($_GET["src"])) {
            // If we have a source filename, redirect back to it.
            Session::redirect($_GET["src"]);
        }
    }
