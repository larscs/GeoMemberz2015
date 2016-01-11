<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

require_once($_SERVER["DOCUMENT_ROOT"].'/core/config.php');

spl_autoload_register(function($class){
	// This function is called whenever a class is requested, i.e. by referencing it.
	// This way, classes are included whenever they are needed, and only the ones that are needed.
	// The files should only contain one class each, and the file name should be identical to
	// the class name, including capitalization.
	require_once $_SERVER["DOCUMENT_ROOT"].'/classes/'.$class.'.php';
});
require_once $_SERVER["DOCUMENT_ROOT"].'/ext/PHPMailerAutoload.php';
Session::bindLang();
// Localizable settings must be set here, after bindLang().
$GLOBALS['config']['dp'] 			= _('.');					// decimal point
$GLOBALS['config']['ts'] 			= _(',');					// thousands separator
$GLOBALS['config']['currency'] 		= _(' $');
$GLOBALS['config']['dateformat'] 	= _('Y-m-d');
$GLOBALS['config']['altdateformat']	= _('yyyy-mm-dd');
$GLOBALS['config']['pctsign']		= _('%');
