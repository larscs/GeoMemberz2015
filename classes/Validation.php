<?php

class Validation {
	public $errormsg;
	public $errors = Array();
	private $_error = false;
		
	public function checkFormData(array $userdetails, $isupdate) {
		// Takes registration details in an array and returns true or false,
		// accompanied by an array listing errors if false.
		$memb = Members::getInstance();
		foreach($userdetails as $formfield=>$fieldvalue) {
			switch($formfield){
				case 'username':
					// Rules: 1: Not empty, 2: Doesn't exist already
					if(empty($fieldvalue)) {$this->addFormError("username",_("Username cannot be empty"));}
					//if(!$this->userNameAvailable($fieldvalue)) {$this->addFormError("username",_("Username already exists. Please choose another username."));}

					if(!$isupdate) {
						// Only check if username doesn't exist if it is not an update
						if($memb->getFieldValue('membernum','username',$fieldvalue)) {$this->addFormError("username",_("Username already exists. Please choose another username."));}
					} else {
						// It is an update
						if($existingmemnum=$memb->getFieldValue('membernum','username',$fieldvalue)) {
							if($existingmemnum != $userdetails['membernum']) {$this->addFormError("username",_("Username already exists. Please choose another username."));}
						}
					}
					break;
				case 'password':
					// Rules: 1: Not empty 2: repeat must also be present
					if(empty($fieldvalue)) {$this->addFormError("password",_("Password cannot be empty"));}
					$pw1 = $fieldvalue;
					break;
				case 'reppassword':
					// Rules: 1: Not empty, 2: Matches password
					if(empty($fieldvalue)) {$this->addFormError("reppassword",_("Repeated password cannot be empty"));}
					if($fieldvalue != $pw1) {$this->addFormError("reppassword",_("The passwords don't match"));}
					
					break;
				case 'email':
					// Rules: 1: Not empty, 2: Is valid e-mail
					if(empty($fieldvalue)) {$this->addFormError("email",_("E-mail address cannot be empty"));}
					if(!$this->checkEmail($fieldvalue)) {$this->addFormError("email",_("This e-mail address is invalid"));}
					break;
				case 'gcnick':
					// Rules: 1: Not empty
					if(empty($fieldvalue)) {$this->addFormError("gcnick",_("Geocaching name cannot be empty"));}
					break;
				case 'firstname':
					// Rules: 1: Not empty
					if(empty($fieldvalue)) {$this->addFormError("firstname",_("First name cannot be empty"));}
					break;
				case 'middlename':
					// Rules: (none)
					break;
				case 'lastname':
					// Rules: 1: Not empty
					if(empty($fieldvalue)) {$this->addFormError("lastname",_("Last name cannot be empty"));}				
					break;
				case 'address':
					// Rules: 1: Not empty
					if(empty($fieldvalue)) {$this->addFormError("address",_("Address cannot be empty"));}
					break;
				case 'phone':
					// Rules: 1: Not empty, 2: Only digits
					if(empty($fieldvalue)) {$this->addFormError("phone",_("Phone cannot be empty"));}
					if($fieldvalue && !$this->checkPhone($fieldvalue)) {$this->addFormError("phone",_("Phone can only contain spaces, +, ( and )."));}
					break;
				case 'birthdate':
					// Rules: 1: Not empty, 2: Not in the future
					if(empty($fieldvalue)) {$this->addFormError("birthdate",_("Birth date cannot be empty"));}
					if(strtotime($fieldvalue)>=time()) {$this->addFormError("birthdate",_("Birth date cannot be in the future"));}
					
					break;

			}

		}
		// If we had no errors, return true.
		if(empty($this->errors)) return true;

		// There were errors, so return false.
		return false;
	}
	public function addFormError($field,$message) {
		// This function populates the $errors array, which is used when checkFormData returns false.
		$this->errors[$field]=$message;	// Set the error message
		return true;
	}
	public function checkUserPass($username = "", $password = "") {
		// Username and password only both needs to be not empty.
		if(!empty($username) && !empty($password)) {
			return true;
		}
		if(empty($username)) {
			$this->addError(_("<strong>Error:</strong> No username entered."));
		}
		if(empty($password)) {
			$this->addError(_("<strong>Error:</strong> No password entered."));
		}
		return false;
	}
	public function checkUserMail($username = "", $mailaddr = "") {
		$this->_error = false;
		if(!$this->checkTextField($username,1,128)) {
			$this->addError(_("<strong>Error:</strong> Username cannot be empty or longer than 128 characters."));
			$this->_error = true;
		}
		if(!$this->checkEmail($mailaddr)) {
			$this->addError(_("<strong>Error:</strong> The e-mail address is not valid."));	
			$this->_error = true;
		}
		if(!$this->_error) {
			return true;
		}
		return false;
	}
	private function checkTextField($data,$minlength=1,$maxlength=256) {
		// Returns true if $data is within the given sizes
		if(strlen($data)>$minlength && strlen($data)<$maxlength) {
			return true;
		}
		return false;
	}

	private function checkEmail($data) {
		// Returns true if $data is a valid e-mail address
		if(filter_var($data, FILTER_VALIDATE_EMAIL)) {
			return true;
		}
		return false;
	}
	private function checkPhone($data) {
		// Returns true if $data is a valid phone number; only digits, parentheses and a leading +
		$data = trim(str_replace(" ", "", $data)); // Remove spaces: "+1 (212) 555 1337" ->"+1(212)5551337"
		if(substr($data,0,1)=="+") {$data = substr($data,1);}  // Remove plus: "1(212)5551337"
		$data = str_replace("(","",$data);		// Remove start parenthesis: "1212)5551337"
		$data = str_replace(")","",$data);		// Remove end parenthesis: "12125551337"
		if(is_numeric($data)) {return true;}	// If it is numeric now, it is a valid phone number
		return false;
	}
	private function addError($message) {
		if(empty($this->errormsg)) {
			// First message, so just set it
			$this->errormsg = $message;
		} else {
			// Not first message, so add a line feed before it
			$this->errormsg .= "<br/>\n".$message;
		}
	}

}