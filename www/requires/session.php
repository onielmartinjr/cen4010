<?php

	/*
		This page defines the Session script. This script is useful for user login handling,
		error handling, and any other kind of handling we might want to manage via a session.
	*/

	class Session {
		
		public $is_logged_in = false;
		public $user_wk;
		public $message;
		
		function __construct() {
			session_start();
			$this->check_message();
			$this->check_login(); 
		}
	
		public function login($user) {
			// database should find user based on username/password
			if($user){
				$this->user_wk = $_SESSION['user_wk'] = $user->user_wk;
				$this->is_logged_in = true;
				//if the user was trying to view a page before, go there, if not, go to dashboard
				if (isset($_GET['url'])) 
					redirect_head(ROOT_URL.$_GET['url']); //go to previous location
				else 
					redirect_head("dashboard.php?type=".$user->last_ticket_view); //if not, go to dashboard
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

	// Create user if logged in
	if($session->is_logged_in) {
	  // actions to take right away if user is logged in
	  $user = User::find_by_id($session->user_wk);
	}

?>