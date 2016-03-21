<?php

	/*
		This page defines the Session script. This script is useful for user login handling,
		error handling, and any other kind of handling we might want to manage via a session.
	*/

	class Session {
		
		public $is_logged_in = false;
		public $user_wk;
		public $message;
		public $pet_where;
		public $pet_order_by;
		public $user_where;
		public $user_order_by;
		
		function __construct() {
			session_start();
			$this->check_message();
			$this->check_filters();
			$this->check_login(); 
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
		
		private function check_filters() {
			//do this for all 4 different filter types
			if(isset($_SESSION['pet_where'])) {
				// Add it as an attribute and erase the stored version
				$this->pet_where = $_SESSION['pet_where'];
				unset($_SESSION['pet_where']);
			} 
			
			if(isset($_SESSION['pet_order_by'])) {
				// Add it as an attribute and erase the stored version
				$this->pet_order_by = $_SESSION['pet_order_by'];
				unset($_SESSION['pet_order_by']);
			} 
			
			if(isset($_SESSION['user_where'])) {
				// Add it as an attribute and erase the stored version
				$this->user_where = $_SESSION['user_where'];
				unset($_SESSION['user_where']);
			} 
			
			if(isset($_SESSION['user_order_by'])) {
				// Add it as an attribute and erase the stored version
				$this->user_order_by = $_SESSION['user_order_by'];
				unset($_SESSION['user_order_by']);
			} 
		}
		
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
		
		public function remove_message() {
			unset($_SESSION['message']);
			unset($this->message);
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