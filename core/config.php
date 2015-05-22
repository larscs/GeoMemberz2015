<?php 
$GLOBALS['config'] = array(
	'sqlhost' => 'geobergen.no', //'127.0.0.1',
    'sqlport' => '3306',
	'sqluser' => 'geobelxg_website',
	'sqlpw'   => 'Groundspeak!',
	'sqldb'	  => 'geobelxg_members',
	'sqlprefix'=> 'gb_',

	'passchangetimeout' => 10800,   // The timeout for password changes, in seconds
	'validationtimeout' => 21600,	// The timeout for validation, in seconds

	'sessionprefix' => 'gb_',
	'cookieprefix' => 'gb_',
	'cookieexpiry' => 31622400,               // The cookie expiry, in seconds

	'assocname' 	=> 'GeoBergen',
	'assocbank'		=> '1503.40.01909',
	'assocemail'	=> 'styret@geobergen.no',		// E-mail for mails like new user notification etc. (including reply-to for autoemail)
	'assocemailname'=> 'Styret i GeoBergen',			// The name for the above address, when sender
	'autoreplyassoc'=> false,                         // Whether reply-to should be assocemail when sender is autoemail 
    'autoemail'		=> 'medlem@geobergen.no',		// E-mail address of sender for system mails
	'autoemailname' => 'Medlemssider for GeoBergen',			// The name for the above address, when sender
	'automailcopy'  => true,				// Whether any system mails should be BCC-ed to an address
	'automailcopyaddr'=> 'medlem@geobergen.no',		// The address such BCCs should be sent to

	'feeagelimit'	=>	16,            // Minimum age for which fee is required. 0 for all members.
    
    // Settings related to mail sending
    'mailSMTP'      => true,              // If set to true, the specified mailserver and port is used. Otherwise, the builtin PHP mail() function is used.
    'mailserver'    => 'mail.geobergen.no',
    'mailport'      => 26,
    'mailauth'      => true,              // If set to true, the two next are used, otherwise not
    'mailuser'      => 'medlem@geobergen.no',
    'mailpass'      => 'Signal2014!',

	'baseaddr' 		=> 'http://medlem.geobergen.no/',
	'cleanbaseaddr' => 'medlem.geobergen.no',
	'logo50'		=> '/img/logo.png',          // 50 px high logo, light background
	'logo30'		=> '/img/logo_banner.png',   // 30 px high logo, dark background
	'logomail'      => '/img/logo_mail.png',     // 30 px high logo, light background    
	'favicon' 		=> '/img/favicon.png',
);