<?php

class Session {
	public $errormsg;

	public static function isLoggedIn() {
		if(!$memb = Members::getInstance()) {
            // If an instance is not found or created, probably the mysql connection failed.
            // Then redirect the user to the installer.
            self::redirect('install/');
        }
        // Returns whether the active session has a logged in user
		// The user is logged in if the userID exists in the session variable "userID".
		// First check if there is a cookie. If there is, it should be copied to the session.
        $userID = NULL;
		$userhash = NULL;
		if(isset($_COOKIE[Config::get('cookieprefix')."userID"])) {
			// Cookie exists, copy userID and userhash to session
			$_SESSION[Config::get('sessionprefix')."userID"] = $_COOKIE[Config::get('cookieprefix')."userID"];
			$_SESSION[Config::get('sessionprefix')."userhash"] = $_COOKIE[Config::get('cookieprefix')."userhash"];
		}
		if(isset($_SESSION[Config::get('sessionprefix')."userID"])) {
			//The user ID is there, check that the userhash also matches the DB.
			$userID = $_SESSION[Config::get('sessionprefix')."userID"];
			$userhash = $_SESSION[Config::get('sessionprefix')."userhash"];
			if($memb->verifyHash($userID,$userhash)){
				// User credentials verified, return success
				$memb = NULL;
				return true;
			}
			$memb = NULL;
			// UserID set, but hash is wrong, so user is not logged in.
			return false;
		}
		// Neither cookie nor session var is set, so user is not logged in.
		return false;
	}

	public function login($username, $password, $rememberme) {
		// Checks username and password, returns true if successful
		$memb = Members::getInstance();
		if($userinfo = $memb->checkUserPass($username,$password)) {
			// Pop error if user has not validated e-mail
			if(!$userinfo['validationok']) {
				$this->errormsg = sprintf(_("<strong>Error:</strong> Account not yet validated. If you didn't receive the validation mail, please check your spam filter. Otherwise, contact us on <a href=\"mailto:%s?subject=%s\">%s</a>"),Config::get('autoemail'),_("Validation mail not received"),Config::get('autoemail'));
				return false;
			}
			// Pop error if user exists, but is not a member
			if(!$userinfo['active']) {
				$this->errormsg = sprintf(_("<strong>Error:</strong> Your account exists, but is frozen. If you have paid your membership fee, please contact us on <a href=\"mailto:%s?subject=%s\">%s</a>"),Config::get('autoemail'),_("Account frozen"),Config::get('autoemail'));
				return false;
			}            
			// Set session var

			$_SESSION[Config::get('sessionprefix')."userID"] = $userinfo['userID'];
			$_SESSION[Config::get('sessionprefix')."userhash"] = $userinfo['userhash'];

		    if($rememberme) {
		        Session::createCookie();
		    }

			Session::redirect('dashboard.php');
			return true;
		}
		$this->errormsg = _("<strong>Error:</strong> Incorrect username or password.");
		return false;
	}
	
	public static function logout() {
		// Logs out any currently logged in user and removes cookies
		unset($_SESSION[Config::get('sessionprefix')."userID"]);
		unset($_SESSION[Config::get('sessionprefix')."userhash"]);

		self::deleteCookie();
		Session::redirect('index.php');
	}

	public static function createCookie() {
		// Creates a cookie to use with the "Rembember me" function
		setcookie(Config::get('cookieprefix')."userID",$_SESSION[Config::get('sessionprefix')."userID"],time()+Config::get('cookieexpiry'),"/",Config::get('cleanbaseaddr'),false,true);
		setcookie(Config::get('cookieprefix')."userhash",$_SESSION[Config::get('sessionprefix')."userhash"],time()+Config::get('cookieexpiry'),"/",Config::get('cleanbaseaddr'),false,true);
		return true;
	}
	
	public static function deleteCookie() {
		// Removes the cookie
		setcookie(Config::get('cookieprefix')."userID","",1,"/",Config::get('cleanbaseaddr'),false,true);
		setcookie(Config::get('cookieprefix')."userhash","",1,"/",Config::get('cleanbaseaddr'),false,true);
		return true;
	}
	
	public static function redirect($location = null) {
		// Redirects the user to the location. If $location is a number, redirect to error page
		if(is_numeric($location)) {
			switch($location) {
				case 404:
					header('HTTP/1.0 404 Not Found');
					include '404.php';
					exit();
					break;
			}
		}
		// Arg is not numeric; just redirect
		header('Location: '.$location);
		exit();
	}
	
	public static function errorPage($title,$errmsg) {
		// Shows an error message page
		include 'errors/error.php';
		exit();
	}

	public static function getLang() {
		// Returns the currently selected language code
		// First check session vars, if not set, get default browser language
		if(isset($_SESSION[Config::get('sessionprefix')."lang"])) {
			return $_SESSION[Config::get('sessionprefix')."lang"];
		} else {
			// No session var, find default browser language
			$langs = array();
		    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		        // break up string into pieces (languages and q factors)
		        preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);
		        if (count($lang_parse[1])) {
		            // create a list like "en" => 0.8
		            $langs = array_combine($lang_parse[1], $lang_parse[4]);
		            // set default to 1 for any without q factor
		            foreach ($langs as $lang => $val) {
		                if ($val === '') $langs[$lang] = 1;
		            }
		            // sort list based on value	
		            arsort($langs, SORT_NUMERIC);
		        }
		    }
		    // look through sorted list and use first one that matches our languages
		    $locale="nb_NO.UTF-8"; // Default value
            foreach ($langs as $lang => $val) {
		    	if (strpos($lang, 'nb') === 0) {
		    		// show bokmål site
		            $locale="nb_NO.UTF-8";
		            break;
		    	} else if (strpos($lang, 'no') === 0) {
		    	    // show bokmål site
		            $locale="nb_NO.UTF-8";
		            break;
		        } else if (strpos($lang, 'en') === 0) {
		            // show english site
		            $locale="en_US";
		            break;
		        }
		    }
		    return $locale;		
		}
	}
	
	public static function setLang($lang) {
		// Sets the language session var to $lang and bind the locale.
		$_SESSION[Config::get('sessionprefix')."lang"] = $lang;
		self::bindLang();
		return true;
	}
	public static function bindLang() {
		// Binds the currently selected language
		$lang = self::getLang();
		putenv("LC_ALL=$lang");
		setlocale(LC_ALL, $lang);
		bindtextdomain("messages", $_SERVER["DOCUMENT_ROOT"]."/locale");
		textdomain("messages");
		bind_textdomain_codeset('default', 'UTF-8');
		return true;
	}
	
	public static function getFlags($qs) {
		// Return the flag HTML based on the currently selected language.
		// Use the passed querystring, if given, when building links
		$lang = self::getLang();
		$qs = (isset($_SERVER["QUERY_STRING"])) ? $_SERVER["QUERY_STRING"] : "";
		$sn = (isset($_SERVER["SCRIPT_NAME"])) ? $_SERVER["SCRIPT_NAME"] : "";
		$qm = (!empty($qs)) ? "?" : "";	// Set this to a question mark only if the querystring is not empty
		switch($lang) {
			case "nb_NO.UTF-8":
				$flagstr  = "<span class=\"selectednor\" title=\"Norsk er valgt\"></span>\n";
				$flagstr .= "<a href=\"changelang.php?lang=en_US&src=".$sn.$qm.$qs."\" class=\"unselecteduk\" title=\"Switch to English\"></a>\n";
				break;
			default:
				$flagstr  = "<a href=\"changelang.php?lang=nb_NO.UTF-8&src=".$sn.$qm.$qs."\" class=\"unselectednor\" title=\"Bytt til norsk\"></a>\n";
				$flagstr .= "<span class=\"selecteduk\" title=\"English is selected\"></span>\n";
				break;

		}
		return $flagstr;
	}

	public static function getSessionVar($varname) {
		if(isset($_SESSION[Config::get('sessionprefix').$varname])) {
			return $_SESSION[Config::get('sessionprefix').$varname];
		}
		return false;
	}
	public static function setSessionVar($varname,$value) {
		$_SESSION[Config::get('sessionprefix').$varname]=$value;
		return true;
	}
	public static function clearSessionVar($varname) {
		unset($_SESSION[Config::get('sessionprefix').$varname]);
		return true;
	}
}