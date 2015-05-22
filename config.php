<?php
    // General configuration
    // The name of the association. Used in mail headers and mail content.
    $assocname = "GeoBergen";
    // Used for automated e-mails.
    $autoemail = "admin@geobergen.no";
    $autoemailname = "GeoBergen Medlemssider";   // This is the name to go along with the email address: This Name <email@address.here>
    // The association's e-mail address for things like new member notification
    $assocemail = "larscs@schreinerland.com";
    $assocemailname = "Styret i GeoBergen";      // This is the name to go along with the email address: This Name <email@address.here>
    // The domain name the membership site resides on
    $siteaddress = "http://medlem2.geobergen.no/";
    $siteaddressbare = "medlem2.geobergen.no";
    
    // Database configuration
    $dbhost = "localhost";
    $dbuser = "geobelxg_website";
    $dbpass = "Groundspeak!";
    $dbname = "geobelxg_members";
    $dbtableprefix = "gb_";
    
    $dp = _(".");
    $ts = _(",");
    $currency = _(" $");
    //$dateformat = "Y-m-d";
    $dateformat = _("Y-m-d");
    //$altdateformat = "yyyy-mm-dd";
    $altdateformat = _("yyyy-mm-dd");

    // If this is set to true, the following will happen:
    // - When a new user registers, the system checks if there is an open phpbb session.
    //   If so, the phpbb user ID is read and saved with the member system user record.
    //   Then, that user is added to the configured phpbb member group.
    // - If an existing user does not have a phpbb id and this setting is true, he will
    //   see a "Link to phpBB" navigation link. That link will show a page asking the
    //   user to log in to the phpbb forum (if not already) and then return to that page.
    // - The access links in the system will now directly control the user's access to
    //   the member group.
    $phpbblink = true;

    // This is the phpbb3 group_id of the group that should be linked to this system.
    // You will find this id in the phpbb_groups table.
    $phpbbmembergroup = 8;
    
    // This is the address of the phpbb3 forum, without "http://".
    $phpbbforum = "forum.geobergen.no";
    
    // This is the relative file system location of the forum in relation to the member system.
    // If the system is at "public_html/site1/members/" and the BB at "public_html/forum/",
    // the path would be "../../forum/". The two must be on the same server file system.
    $phpbbfslocation = "../forum/";

    // This is the cookie name. You'll find this in the ACP, under Server Configuration/Cookie settings
    $phpbbcookiename = "phpbb3_cm688";

    // phpBB3 database details. You'll find this in the file config.php in your phpBB3 root.
    // Just copy the values in here.    
    $phpbbdbhost = 'localhost';
    $phpbbdbname = 'geobelxg_phpb829';
    $phpbbdbuser = 'geobelxg_phpb829';
    $phpbbdbpasswd = '9qSe03pPhz';
    $phpbbtable_prefix = 'phpbb_';
?>