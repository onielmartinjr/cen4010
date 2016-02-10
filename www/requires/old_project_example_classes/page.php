<?php

	class Page {
		
		public $title;
		public $is_business_only;
		public $is_tech_only;
		public $is_admin_only;
		
		//navigation
		public static function display_navigation() {
			include "navigation.php";
		}
		
		//set page as admin-only
		public function set_admin_security() {
			$this->is_business_only = true;
			$this->is_tech_only = false;
			$this->is_admin_only = true;	
		}
		
		//set page as technician-only
		public function set_tech_security() {
			$this->is_business_only = true;	
			$this->is_tech_only = true;
			$this->is_admin_only = false;
		}
		
		//set page as business-friendly
		public function set_business_security() {
			$this->is_business_only = true;
			$this->is_tech_only = false;
			$this->is_admin_only = false;
		}
		
		//set page as open, not protected, must not be logged in to view
		public function set_no_security() {
			$this->is_business_only = false;
			$this->is_tech_only = false;
			$this->is_admin_only = false;
		}
		
		//checks if user can see the following page
		public function user_security_check() {
			global $page_file_name_with_get;
			global $session;
			global $user;
			//all class variables must be set
			if (isset($this->title) && 
				isset($this->is_business_only) && 
				isset($this->is_tech_only) && 
				isset($this->is_admin_only)) {
				
				//if the page is not protected, you must be logged off to view it
				if (!$this->is_business_only) {
					if ($session->logged_in) {
						$session->message("You are already logged in.");
						redirect_head('dashboard.php');
					}
				}
				
				//check if viewing protected page & not logged in
				if ($this->is_business_only) {
					if (!$session->logged_in) {
						$session->message("You need to be logged in to access '".$this->title."'.");
						redirect_head(ROOT_URL.'?url='.$page_file_name_with_get);
					}						
				}
				
				//checks if viewing tech_only page & you're a user
				if ($this->is_tech_only) {
					if ($user->role == 'Business') {
						//if a business is trying to view a tech_only page, kick `em out
						$session->message("You need to be a technician to access '".$this->title."'.");
						redirect_head('dashboard.php');
					}
				}
				
				//checks if anyone but an admin is viewing an admin only page
				if ($this->is_admin_only) {
					if ($user->role == 'Business' || $user->role == 'Technician') {
						$session->message("You need to be an admin to access '".$this->title."'.");
						redirect_head('dashboard.php');
					}
				}
				
			}
		}
		
	}

?>