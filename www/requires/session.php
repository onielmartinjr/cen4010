<?php

	/*
		This page defines the Session script. This script is useful for user login handling,
		error handling, and any other kind of handling we might want to manage via a session.
	*/

	class Session {
		
		//needed by the architect of the system
		public $is_logged_in = false;
		public $user_wk;
		public $message;
		
		//additional variables to store
		public $pet_where;
		public $pet_order_by;
		public $user_where;
		public $user_order_by;
		
		function __construct() {
			session_start();
			$this->check_message();
			$this->check_login();
			//this will get all other $_SESSION items automatically
			$this->check_variables();
		}
	
		public function login($user) {
			// database should find user based on username/password
			if($user){
				$this->user_wk = $_SESSION['user_wk'] = $user->user_wk;
				$this->is_logged_in = true;
			}
		}
	  
	  	//set message or return message
		public function message($msg="") {
		  if(!empty($msg)) {
			// then this is "set message"
			$_SESSION['message'] = $msg;
			$this->message = $msg;
		  } else {
			// then this is "get message"
			return $this->message;
		  }
		}
	
		//checks user's login peripherals for session
		private function check_login() {
			if(isset($_SESSION['user_wk'])) {
				$this->user_wk = $_SESSION['user_wk'];
				$this->is_logged_in = true;
			} else {
				unset($this->user_wk);
				$this->is_logged_in = false;
			}
		}
	  
		private function check_message() {
			// Is there a message stored in the session?
			if(isset($_SESSION['message'])) {
				// Add it as an attribute and erase the stored version
				$this->message = $_SESSION['message'];
				unset($_SESSION['message']);
			} else {
				$this->message = "";
			}
		}
		
		private function check_variables() {
			//do this for all session variables dynamically
			
			$all_items = get_object_vars($this);
			//remove the default items
			unset($all_items['is_logged_in']);
			unset($all_items['user_wk']);
			unset($all_items['message']);
			
			//loop through all the variables, set them
			foreach($all_items AS $key => $value) {
				if(isset($_SESSION[$key])) {
					// add the value to the attribute
					$this->{$key} = $_SESSION[$key];
				} 
			}
		}
		
		//logout function
		public function logout($bypass_redirect=false) {
			unset($_SESSION['user_wk']);
			$this->is_logged_in = false;
			unset($this->user_wk);
			if ($bypass_redirect == false) 
			{
				$this->message("You were successfully logged out.");
				redirect_head(ROOT_URL."index.php");
			}
		}
		
		//set session variables
		public function set_variable($variable_name, $variable_value) {
			//only do this if the item name exists as a property
			if(property_exists($this, $variable_name)) {
				if(!empty($variable_value)) {
					// then this is "set variable"
					$_SESSION[$variable_name] = $variable_value;
					$this->{$variable_name} = $variable_value;
					return true;
				}
			}
			
			//if we're here, return false
			return false;
		}
		
		//remove session variables
		public function unset_variable($variable_name) {
			//only do this if the item name exists as a property
			if(property_exists($this, $variable_name)) {
				unset($_SESSION[$variable_name]);
				unset($this->{$variable_name});
				return true;
			}
				
			//if we're here, value could not be unset for whatever reason
			return false;
		}
		
	}

	//create a new message
	$session = new Session();

	// create user if logged in
	if($session->is_logged_in) {
	  // actions to take right away if user is logged in
	  $user = User::find_by_id($session->user_wk);
	}

?>