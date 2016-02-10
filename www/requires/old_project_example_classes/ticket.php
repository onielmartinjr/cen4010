<?php


class Ticket {
	
	public static $table_name = 'tickets';
	protected static $db_fields = array('ticket_wk', 'submitted_by_user_wk', 'tech_assigned_user_wk', 'name', 'reason_wk', 'reason', 'status', 'body', 'status_wk', 'close_dt', 'create_dt', 'flag', 'submitted_by', 'tech_assigned', 'last_viewed_dt');
	
	public $ticket_wk;
	public $submitted_by_user_wk = 0;
	public $tech_assigned_user_wk;
	public $name;
	public $reason_wk = 1;
	public $reason = "Test - Reason";
	public $status_wk = 1;
	public $status = "New";
	public $body;
	public $close_dt;
	public $create_dt;
	public $flag = 0;
	

	//checks if user can work on ticket
	public function can_work_on_ticket($user) {
		if ($user->role == 'Business')
			return false;
		if ($user->role == 'Admin')
			return true;
		if ($user->role == 'Technician') {
			if ($user->user_wk == $this->tech_assigned_user_wk)
				return true;
			else
				return false;	
		}
	}

	//checks if user can view ticket
	public function can_view_ticket($user) {
		if ($user->role != 'Business')
			return true;
		else {
			if ($user->user_wk == $this->submitted_by_user_wk)
				return true;
			else
				return false;	
		}
	}
	
	//checks if ticket is closed
	public function is_ticket_closed() {
		if ($this->status == "Closed")
			return true;
		else
			return false;
	}
	
	//returns last viewed date of a ticket by a user
	public function last_viewed_date($user) {
		global $database;
		//depending on role permission level, you may have restrictions
		$sql = "SELECT create_dt FROM `log` WHERE `user_wk` = ".$user->user_wk." AND `ticket_wk` = ".$this->ticket_wk." ORDER BY `log`.`create_dt` DESC LIMIT 1";
   		$result_set = $database->query($sql);
		//if 0 records came back, user has never seen this ticket
		if ($database->num_rows($result_set) == 0)
			return false;
		else {
			$row = $database->fetch_array($result_set);
    		return array_shift($row);	
		}
	}
	
	//find ticket by ticket_wk
	public static function find_by_id($id=0) {
		$result_array = self::find_by_sql("SELECT t.*, users.get_name as tech_assigned FROM ( SELECT tickets.*, ticket_status.name as status, ticket_reasons.name as reason, users.get_name as submitted_by FROM `tickets` INNER JOIN ticket_status ON `ticket_status`.`status_wk` = `tickets`.`status_wk` INNER JOIN ticket_reasons ON `ticket_reasons`.`reason_wk` = `tickets`.reason_wk LEFT JOIN users ON users.user_wk = tickets.submitted_by_user_wk ) t LEFT JOIN users ON users.user_wk = t.tech_assigned_user_wk WHERE ticket_wk={$id} LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	//find ticket by sql
	public static function find_by_sql($sql="") {
		global $database;
    	$result_set = $database->query($sql);
    	$object_array = array();
    	while ($row = $database->fetch_array($result_set)) {
    		$object_array[] = self::instantiate($row);
   		}
		return $object_array;	
	}
	
	//get all unclaimed tickets
	public static function get_all_unclaimed_tickets($user) {
		if ($user->role == "Business") 
			return false;
		if (self::count_all_unclaimed_tickets() == 0)
			return false;
		else
			return self::find_by_sql("SELECT * FROM(
    SELECT 
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned,
        MAX(t.last_viewed_dt) AS last_viewed_dt
    FROM (
        SELECT
            t.*,
            s.name as status,
            r.name as reason,
            u.get_name as submitted_by,
            u2.get_name as tech_assigned,
            l.create_dt as last_viewed_dt
        FROM
            tickets t
            INNER JOIN ticket_reasons r
                ON r.reason_wk = t.reason_wk
            INNER JOIN ticket_status s
                ON s.status_wk = t.status_wk
            INNER JOIN users u
                ON u.user_wk = t.submitted_by_user_wk
            LEFT JOIN users u2
                ON u2.user_wk = t.tech_assigned_user_wk
            LEFT JOIN log l
                ON l.user_wk = ".$user->user_wk."
                AND l.ticket_wk = t.ticket_wk
		WHERE
			t.tech_assigned_user_wk = 0)t
    GROUP BY
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned)t
ORDER BY ".$user->ticket_column_order_by." ".$user->ticket_column_order);
	}
	
	//get all my tickets
	public static function get_all_my_tickets($user) {
		if ($user->role == "Business") {
			if (self::count_all_my_tickets($user) == 0)
				return false;
			else {
				return self::find_by_sql("SELECT * FROM(
    SELECT 
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned,
        MAX(t.last_viewed_dt) AS last_viewed_dt
    FROM (
        SELECT
            t.*,
            s.name as status,
            r.name as reason,
            u.get_name as submitted_by,
            u2.get_name as tech_assigned,
            l.create_dt as last_viewed_dt
        FROM
            tickets t
            INNER JOIN ticket_reasons r
                ON r.reason_wk = t.reason_wk
            INNER JOIN ticket_status s
                ON s.status_wk = t.status_wk
            INNER JOIN users u
                ON u.user_wk = t.submitted_by_user_wk
            LEFT JOIN users u2
                ON u2.user_wk = t.tech_assigned_user_wk
            LEFT JOIN log l
                ON l.user_wk = ".$user->user_wk."
                AND l.ticket_wk = t.ticket_wk
			WHERE
				t.submitted_by_user_wk = ".$user->user_wk."
				OR t.tech_assigned_user_wk = ".$user->user_wk.")t
    GROUP BY
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned)t
ORDER BY ".$user->ticket_column_order_by." ".$user->ticket_column_order);
			}
		}
		if ($user->role == "Technician") {
			if (self::count_all_my_tickets($user) == 0)
				return false;
			else
				return self::find_by_sql("SELECT * FROM(
    SELECT 
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned,
        MAX(t.last_viewed_dt) AS last_viewed_dt
    FROM (
        SELECT
            t.*,
            s.name as status,
            r.name as reason,
            u.get_name as submitted_by,
            u2.get_name as tech_assigned,
            l.create_dt as last_viewed_dt
        FROM
            tickets t
            INNER JOIN ticket_reasons r
                ON r.reason_wk = t.reason_wk
            INNER JOIN ticket_status s
                ON s.status_wk = t.status_wk
            INNER JOIN users u
                ON u.user_wk = t.submitted_by_user_wk
            LEFT JOIN users u2
                ON u2.user_wk = t.tech_assigned_user_wk
            LEFT JOIN log l
                ON l.user_wk = ".$user->user_wk."
                AND l.ticket_wk = t.ticket_wk
			WHERE
				t.tech_assigned_user_wk = ".$user->user_wk."
				OR t.submitted_by_user_wk = ".$user->user_wk.")t
    GROUP BY
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned)t
ORDER BY ".$user->ticket_column_order_by." ".$user->ticket_column_order);
		}
		if ($user->role == 'Admin') {
			if (self::count_all_my_tickets($user) == 0)
				return false;
			else {
				$sql = "SELECT * FROM(
    SELECT 
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned,
        MAX(t.last_viewed_dt) AS last_viewed_dt
    FROM (
        SELECT
            t.*,
            s.name as status,
            r.name as reason,
            u.get_name as submitted_by,
            u2.get_name as tech_assigned,
            l.create_dt as last_viewed_dt
        FROM
            tickets t
            INNER JOIN ticket_reasons r
                ON r.reason_wk = t.reason_wk
            INNER JOIN ticket_status s
                ON s.status_wk = t.status_wk
            INNER JOIN users u
                ON u.user_wk = t.submitted_by_user_wk
            LEFT JOIN users u2
                ON u2.user_wk = t.tech_assigned_user_wk
            LEFT JOIN log l
                ON l.user_wk = ".$user->user_wk."
                AND l.ticket_wk = t.ticket_wk)t
    GROUP BY
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned)t
ORDER BY ".$user->ticket_column_order_by." ".$user->ticket_column_order;
				return self::find_by_sql($sql);
			}
		}
	}
	
	//get all my tickets
	public static function get_all_flagged_tickets($user) {
		if ($user->role == "Business") {
			return false;
		}
		if ($user->role == "Technician") {
			return false;
		}
		if ($user->role == 'Admin') {
			if (self::count_all_flagged_tickets($user) == 0)
				return false;
			else
				return self::find_by_sql("SELECT * FROM(
    SELECT 
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned,
        MAX(t.last_viewed_dt) AS last_viewed_dt
    FROM (
        SELECT
            t.*,
            s.name as status,
            r.name as reason,
            u.get_name as submitted_by,
            u2.get_name as tech_assigned,
            l.create_dt as last_viewed_dt
        FROM
            tickets t
            INNER JOIN ticket_reasons r
                ON r.reason_wk = t.reason_wk
            INNER JOIN ticket_status s
                ON s.status_wk = t.status_wk
            INNER JOIN users u
                ON u.user_wk = t.submitted_by_user_wk
            LEFT JOIN users u2
                ON u2.user_wk = t.tech_assigned_user_wk
            LEFT JOIN log l
                ON l.user_wk = ".$user->user_wk."
                AND l.ticket_wk = t.ticket_wk
		WHERE
			t.flag = 1)t
    GROUP BY
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned)t
ORDER BY ".$user->ticket_column_order_by." ".$user->ticket_column_order);
		}
	}
	
	//get all my open tickets
	public static function get_all_my_open_tickets($user) {
		if ($user->role == "Business") {
			if (self::count_all_my_open_tickets($user) == 0)
				return false;
			else {
				$sql = "SELECT * FROM(
    SELECT 
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned,
        MAX(t.last_viewed_dt) AS last_viewed_dt
    FROM (
        SELECT
            t.*,
            s.name as status,
            r.name as reason,
            u.get_name as submitted_by,
            u2.get_name as tech_assigned,
            l.create_dt as last_viewed_dt
        FROM
            tickets t
            INNER JOIN ticket_reasons r
                ON r.reason_wk = t.reason_wk
            INNER JOIN ticket_status s
                ON s.status_wk = t.status_wk
            INNER JOIN users u
                ON u.user_wk = t.submitted_by_user_wk
            LEFT JOIN users u2
                ON u2.user_wk = t.tech_assigned_user_wk
            LEFT JOIN log l
                ON l.user_wk = ".$user->user_wk."
                AND l.ticket_wk = t.ticket_wk
			WHERE 
				s.name != 'Closed' AND (t.submitted_by_user_wk = ".$user->user_wk." OR t.tech_assigned_user_wk = ".$user->user_wk."))t
    GROUP BY
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned)t
ORDER BY ".$user->ticket_column_order_by." ".$user->ticket_column_order;
				return self::find_by_sql($sql);
			}
		}
		if ($user->role == "Technician") {
			if (self::count_all_my_open_tickets($user) == 0)
				return false;
			else {
				$sql = "SELECT * FROM(
    SELECT 
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned,
        MAX(t.last_viewed_dt) AS last_viewed_dt
    FROM (
        SELECT
            t.*,
            s.name as status,
            r.name as reason,
            u.get_name as submitted_by,
            u2.get_name as tech_assigned,
            l.create_dt as last_viewed_dt
        FROM
            tickets t
            INNER JOIN ticket_reasons r
                ON r.reason_wk = t.reason_wk
            INNER JOIN ticket_status s
                ON s.status_wk = t.status_wk
            INNER JOIN users u
                ON u.user_wk = t.submitted_by_user_wk
            LEFT JOIN users u2
                ON u2.user_wk = t.tech_assigned_user_wk
            LEFT JOIN log l
                ON l.user_wk = ".$user->user_wk."
                AND l.ticket_wk = t.ticket_wk
			WHERE s.name != 'Closed' AND (t.tech_assigned_user_wk = ".$user->user_wk." OR t.submitted_by_user_wk = ".$user->user_wk."))t
    GROUP BY
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned)t
ORDER BY ".$user->ticket_column_order_by." ".$user->ticket_column_order;
				return self::find_by_sql($sql);
			}
		}
		if ($user->role == 'Admin') {
			if (self::count_all_my_open_tickets($user) == 0)
				return false;
			else
				return self::find_by_sql("SELECT * FROM(
    SELECT 
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned,
        MAX(t.last_viewed_dt) AS last_viewed_dt
    FROM (
        SELECT
            t.*,
            s.name as status,
            r.name as reason,
            u.get_name as submitted_by,
            u2.get_name as tech_assigned,
            l.create_dt as last_viewed_dt
        FROM
            tickets t
            INNER JOIN ticket_reasons r
                ON r.reason_wk = t.reason_wk
            INNER JOIN ticket_status s
                ON s.status_wk = t.status_wk
            INNER JOIN users u
                ON u.user_wk = t.submitted_by_user_wk
            LEFT JOIN users u2
                ON u2.user_wk = t.tech_assigned_user_wk
            LEFT JOIN log l
                ON l.user_wk = ".$user->user_wk."
                AND l.ticket_wk = t.ticket_wk
			WHERE s.name != 'Closed')t
    GROUP BY
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned)t
ORDER BY ".$user->ticket_column_order_by." ".$user->ticket_column_order);
		}
	}
	
	//get all my closed tickets
	public static function get_all_my_closed_tickets($user) {
		if ($user->role == "Business") {
			if (self::count_all_my_closed_tickets($user) == 0)
				return false;
			else
				return self::find_by_sql("SELECT * FROM(
    SELECT 
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned,
        MAX(t.last_viewed_dt) AS last_viewed_dt
    FROM (
        SELECT
            t.*,
            s.name as status,
            r.name as reason,
            u.get_name as submitted_by,
            u2.get_name as tech_assigned,
            l.create_dt as last_viewed_dt
        FROM
            tickets t
            INNER JOIN ticket_reasons r
                ON r.reason_wk = t.reason_wk
            INNER JOIN ticket_status s
                ON s.status_wk = t.status_wk
            INNER JOIN users u
                ON u.user_wk = t.submitted_by_user_wk
            LEFT JOIN users u2
                ON u2.user_wk = t.tech_assigned_user_wk
            LEFT JOIN log l
                ON l.user_wk = ".$user->user_wk."
                AND l.ticket_wk = t.ticket_wk
			WHERE s.name = 'Closed' AND (t.submitted_by_user_wk = ".$user->user_wk." OR t.tech_assigned_user_wk = ".$user->user_wk."))t
    GROUP BY
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned)t
ORDER BY ".$user->ticket_column_order_by." ".$user->ticket_column_order);
		}
		if ($user->role == "Technician") {
			if (self::count_all_my_closed_tickets($user) == 0)
				return false;
			else
				return self::find_by_sql("SELECT * FROM(
    SELECT 
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned,
        MAX(t.last_viewed_dt) AS last_viewed_dt
    FROM (
        SELECT
            t.*,
            s.name as status,
            r.name as reason,
            u.get_name as submitted_by,
            u2.get_name as tech_assigned,
            l.create_dt as last_viewed_dt
        FROM
            tickets t
            INNER JOIN ticket_reasons r
                ON r.reason_wk = t.reason_wk
            INNER JOIN ticket_status s
                ON s.status_wk = t.status_wk
            INNER JOIN users u
                ON u.user_wk = t.submitted_by_user_wk
            LEFT JOIN users u2
                ON u2.user_wk = t.tech_assigned_user_wk
            LEFT JOIN log l
                ON l.user_wk = ".$user->user_wk."
                AND l.ticket_wk = t.ticket_wk
			WHERE s.name = 'Closed' AND ( t.tech_assigned_user_wk = ".$user->user_wk." OR t.submitted_by_user_wk = ".$user->user_wk."))t
    GROUP BY
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned)t
ORDER BY ".$user->ticket_column_order_by." ".$user->ticket_column_order);
		}
		if ($user->role == 'Admin') {
			if (self::count_all_my_closed_tickets($user) == 0)
				return false;
			else
				return self::find_by_sql("SELECT * FROM(
    SELECT 
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned,
        MAX(t.last_viewed_dt) AS last_viewed_dt
    FROM (
        SELECT
            t.*,
            s.name as status,
            r.name as reason,
            u.get_name as submitted_by,
            u2.get_name as tech_assigned,
            l.create_dt as last_viewed_dt
        FROM
            tickets t
            INNER JOIN ticket_reasons r
                ON r.reason_wk = t.reason_wk
            INNER JOIN ticket_status s
                ON s.status_wk = t.status_wk
            INNER JOIN users u
                ON u.user_wk = t.submitted_by_user_wk
            LEFT JOIN users u2
                ON u2.user_wk = t.tech_assigned_user_wk
            LEFT JOIN log l
                ON l.user_wk = ".$user->user_wk."
                AND l.ticket_wk = t.ticket_wk
			WHERE s.name = 'Closed')t
    GROUP BY
        t.ticket_wk,
        t.submitted_by_user_wk,
        t.tech_assigned_user_wk,
        t.name,
        t.reason_wk,
        t.body,
        t.status_wk,
        t.close_dt,
        t.create_dt,
        t.flag,
        t.status,
        t.reason,
        t.submitted_by,
        t.tech_assigned)t
ORDER BY ".$user->ticket_column_order_by." ".$user->ticket_column_order);
		}
	}
	
	
	//returns count of all my tickets
	public static function count_all_my_tickets($user) {
		global $database;
		//depending on role permission level, you may have restrictions
		if ($user->role == 'Business')
			$sql = "SELECT COUNT(*) FROM ".self::$table_name." INNER JOIN ticket_status ON `ticket_status`.`status_wk` = `".self::$table_name."`.`status_wk` WHERE submitted_by_user_wk = ".$user->user_wk;
		if ($user->role == 'Admin')
			$sql = "SELECT COUNT(*) FROM ".self::$table_name." INNER JOIN ticket_status ON `ticket_status`.`status_wk` = `".self::$table_name."`.`status_wk`";
		if ($user->role == 'Technician') {
			$sql = "SELECT COUNT(*) FROM ".self::$table_name." INNER JOIN ticket_status ON `ticket_status`.`status_wk` = `".self::$table_name."`.`status_wk` WHERE tech_assigned_user_wk = ".$user->user_wk;	
		}
   		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
    	return array_shift($row);
	}
	
	public static function count_all_flagged_tickets($user) {
		global $database;
		//depending on role permission level, you may have restrictions
		if ($user->role == 'Business')
			return false;
		if ($user->role == 'Admin')
			$sql = "SELECT COUNT(*) FROM ".self::$table_name." INNER JOIN ticket_status ON `ticket_status`.`status_wk` = `".self::$table_name."`.`status_wk` WHERE flag = 1";
		if ($user->role == 'Technician') {
			return false;
		}
   		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
    	return array_shift($row);
	}
	
	//returns count of all open tickets
	public static function count_all_my_open_tickets($user) {
		global $database;
		//depending on role permission level, you may have restrictions
		if ($user->role == 'Business')
			$sql = "SELECT COUNT(*) FROM ".self::$table_name." INNER JOIN ticket_status ON `ticket_status`.`status_wk` = `".self::$table_name."`.`status_wk` WHERE submitted_by_user_wk = ".$user->user_wk." AND `ticket_status`.name != 'Closed'";
		if ($user->role == 'Admin')
			$sql = "SELECT COUNT(*) FROM ".self::$table_name." INNER JOIN ticket_status ON `ticket_status`.`status_wk` = `".self::$table_name."`.`status_wk` WHERE `ticket_status`.name != 'Closed'";
		if ($user->role == 'Technician') {
			$sql = "SELECT COUNT(*) FROM ".self::$table_name." INNER JOIN ticket_status ON `ticket_status`.`status_wk` = `".self::$table_name."`.`status_wk` WHERE tech_assigned_user_wk = ".$user->user_wk." AND `ticket_status`.name != 'Closed'";	
		}
   		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
    	return array_shift($row);
	}
	
	//returns count of all closed tickets
	public static function count_all_my_closed_tickets($user) {
		global $database;
		//depending on role permission level, you may have restrictions
		if ($user->role == 'Business')
			$sql = "SELECT COUNT(*) FROM ".self::$table_name." INNER JOIN ticket_status ON `ticket_status`.`status_wk` = `".self::$table_name."`.`status_wk` WHERE submitted_by_user_wk = ".$user->user_wk." AND `ticket_status`.name = 'Closed'";
		if ($user->role == 'Admin')
			$sql = "SELECT COUNT(*) FROM ".self::$table_name." INNER JOIN ticket_status ON `ticket_status`.`status_wk` = `".self::$table_name."`.`status_wk` WHERE `ticket_status`.name = 'Closed'";
		if ($user->role == 'Technician') {
			$sql = "SELECT COUNT(*) FROM ".self::$table_name." INNER JOIN ticket_status ON `ticket_status`.`status_wk` = `".self::$table_name."`.`status_wk` WHERE tech_assigned_user_wk = ".$user->user_wk." AND `ticket_status`.name = 'Closed'";	
		}
		
   		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
    	return array_shift($row);
	}
	
	//returns count of all unclaimed tickets
	public static function count_all_unclaimed_tickets() {
		//if you're a user, you can't see this message
		global $database;
		$sql = "SELECT COUNT(*) FROM ".self::$table_name." INNER JOIN ticket_status ON `ticket_status`.`status_wk` = `".self::$table_name."`.`status_wk` WHERE tech_assigned_user_wk = 0";
   		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
    	return array_shift($row);
	}
	
	private static function instantiate($record) {
    	$object = new self;		
		// More dynamic, short-form approach:
		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute)) {
		    $object->$attribute = $value;
		  }
		}
		return $object;
	}
	
	protected function attributes() { 
		// return an array of attribute names and their values
		$attributes = array();
		foreach(self::$db_fields as $field) {
	    	if(property_exists($this, $field)) {
	    		$attributes[$field] = $this->$field;
	    	}
		}
		return $attributes;
	}
	
	protected function sanitized_attributes() {
		global $database;
		$clean_attributes = array();
		// sanitize the values before submitting
		// Note: does not alter the actual value of each attribute
		foreach($this->attributes() as $key => $value){
			if ($key != 'reason' && $key != 'status' && $key != 'last_viewed_dt')
		    	$clean_attributes[$key] = $database->escape_value($value);
		}
		return $clean_attributes;
	}
	
	private function has_attribute($attribute) {
		// We don't care about the value, we just want to know if the key exists
	 	// Will return true or false
	 	return array_key_exists($attribute, $this->attributes());
	}
	
	public function save() {
		global $session;
		// if we have an object without an ID, create a new record - else, update current record
		
		//validate ticket name
		if (!validate($this->name, 50)) {
			if (!isset($this->ticket_wk))
				redirect_head("create_ticket.php");
			else
				redirect_head("view_ticket.php?ticket_wk=".$this->ticket_wk);
		}
		
		//validate ticket body
		if (!validate($this->body, 10000)) {
			if (!isset($this->ticket_wk))
				redirect_head("create_ticket.php");
			else
				redirect_head("view_ticket.php?ticket_wk=".$this->ticket_wk);
		}
		
		//trim the name
		$this->name = trim($this->name);
		//if it's good, run the update or create script
		return isset($this->ticket_wk) ? $this->update() : $this->create();
	}
	
	public function create() {
		//creates a ticket object to database
		global $database;
		$attributes = $this->sanitized_attributes();
		$sql = "INSERT INTO ".self::$table_name." (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
		if($database->query($sql)) {
			$this->ticket_wk = $database->insert_id();
			//update timestamp too
			$database->query("UPDATE ".self::$table_name." SET create_dt = CURRENT_TIMESTAMP WHERE ticket_wk = ".$this->ticket_wk);
			return true;
		} else {
			return false;
		}
	}

	public function update() {
		global $database;
		//updates ticket to database
		$attributes = $this->sanitized_attributes();
		$attribute_pairs = array();
		foreach($attributes as $key => $value) {
			if ($key != 'reason' && $key != 'status' && $key != 'last_viewed_dt' && $key != 'submitted_by' && $key != 'tech_assigned')
				$attribute_pairs[] = "{$key}='{$value}'";
		}
		$sql = "UPDATE ".self::$table_name." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE ticket_wk=". $database->escape_value($this->ticket_wk);
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}

	public function close() {
		$this->status_wk = 4;
		$this->status = "Closed";
		global $database;
		//closes ticket
		$sql = "UPDATE `tickets` SET `status_wk` = '4', `close_dt` = CURRENT_TIMESTAMP WHERE `tickets`.`ticket_wk` = ".$this->ticket_wk.";";
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}
	
	public function delete_ticket() {
		global $database;
		//closes ticket
		$sql = "DELETE FROM ".self::$table_name;
	 	$sql .= " WHERE ticket_wk=". $database->escape_value($this->ticket_wk);
		$sql .= " LIMIT 1";
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}
}

?>