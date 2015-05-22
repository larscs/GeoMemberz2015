<?php

class Database {
	private static $_instance = null;
	private $_pdo,
			$_query,
			$_results,
			$_count = 0;

	private function __construct() {
		// Try connecting to the mysql server
		try {
			$this->_pdo = new PDO("mysql:host=".Config::get('sqlhost').";".
								  "dbname=".Config::get('sqldb'),
								   Config::get('sqluser'),
								   Config::get('sqlpw'));
		} catch(PDOException $e) {
			// Error, terminate and show error message
			die($e->getMessage());
		}
	}

	public static function getInstance() {
		if(!isset(self::$_instance)) {
			// Instance not created yet, create one
			self::$_instance = new Database();
		}
		return self::$_instance;
	}
	
	public function isBoard() {
		// Checks whether the logged in user is a board member.
		// Always run after Session::isLoggedIn, so it's safe to
		// assume that the session var userID is set.
		$table = Config::get('sqlprefix')."members";
		$this->_query = $this->_pdo->prepare("SELECT boardmember FROM {$table} WHERE membernum = ?");
		$this->_query->bindValue(1, $_SESSION[Config::get('sessionprefix')."userID"]);
		if($this->_query->execute()) {
			$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
			if($this->_results[0]["boardmember"]) {
				return true;
			}
		}
		return false;
	}
	public function getUserName($userID) {
		// Return the username given the userID.
		$table = Config::get('sqlprefix')."members";
		$this->_query = $this->_pdo->prepare("SELECT username FROM {$table} WHERE membernum = ?");
		$this->_query->bindValue(1, $userID);
		if($this->_query->execute()) {
			$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
			return $this->_results[0]["username"];
		}
		return "";		// If not found (should never happen), just return an empty string
	}
	public function checkUserPass($username,$password) {
		// Checks that the password matches the username. Returns array with userID and hash if successful, 
		// FALSE if not.
		$t_hasher = new PasswordHash(6, true);
		$table = Config::get('sqlprefix')."members";
		$this->_query = $this->_pdo->prepare("SELECT * FROM {$table} WHERE username = ?");
		$this->_query->bindValue(1, $username);
		if($this->_query->execute()) {
			// Query succeeded, fetch the results
			$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
			$this->_count = $this->_query->rowCount();
			switch($this->_count) {
				case 0:
					// No records; unknown username
					return false;
					break;
				case 1:
					// One record, check that hash matches password
					$check = $t_hasher->CheckPassword($password,$this->_results[0]["password"]);
					if($check) {
						// Hash matches, return userID and hash
						return Array("userID"=>$this->_results[0]["membernum"],"userhash"=>$this->_results[0]["password"]);
					}
					return false;
					break;
				default:
					// Multiple records, database integrity compromised
					die(_("Fatal error: Duplicate member number found"));
					break;
			}
			// We should never get here, but return false just in case
			return false;
		}
		// Query failed, return false
		echo "Query failed with the following error message: ";
		echo "<pre>";
		echo $this->_query->errorInfo()[2];
		echo "</pre>";
		exit();
		return false;
	}
	public function verifyHash($userID,$userhash) {
		$table = Config::get('sqlprefix')."members";
		$this->_query = $this->_pdo->prepare("SELECT * FROM {$table} WHERE membernum = ?");
		$this->_query->bindValue(1, $userID);
		if($this->_query->execute()) {
			// Query succeeded, fetch the results
			$this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
			$this->_count = $this->_query->rowCount();

			switch($this->_count) {
				case 0:
					// No records; unknown userID
					return false;
					break;
				case 1:
					// One record, check that hash matches
					if($this->_results[0]["password"]==$userhash) {
						// Hash matches, return success

						return true;
					}
					return false;
					break;
				default:
					// Multiple records, database integrity compromised
					die(_("Fatal error: Duplicate member number found"));
					break;
			}
			// We should never get here, but return false just in case
			return false;
		}
		// Query failed, return false
		echo "Query failed with the following error message: ";
		echo "<pre>";
		echo $this->_query->errorInfo()[2];
		echo "</pre>";
		exit();
		return false;
	}
}