<?php

	/*
		This page will define the class that defines the database connections we will handle.
		At the end of the class, it instantiates a database connection with the constants defined
		in the constants file.
		
		NOTE: if you need a connection to multiple databases (i.e - we need multiple database objects),
		alter the open_connection() method so that it allows passing the strings in manually.
	*/

	class MySQLDatabase {
		
		private $connection;
		public $last_query;
		private $magic_quotes_active;
		private $real_escape_string_exists;
		
		//constructor
		function __construct() {
			$this->open_connection();
			$this->magic_quotes_active = get_magic_quotes_gpc();
			$this->real_escape_string_exists = function_exists( "mysqli_real_escape_string" );
		 }
		
		//open connection
		public function open_connection() {
			$this->connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DB);
			if (!$this->connection) {
				die('Could not successfully connect to database.');
			} 
		}

		//close connection
		public function close_connection() {
			if(isset($this->connection)) {
				mysqli_close($this->connection);
				unset($this->connection);
			}
		}

		//run this query
		public function query($sql) {
			$this->last_query = $sql;
			$result = mysqli_query($this->connection, $sql);
			$this->confirm_query($result);
			return $result;
		}
		
		//return the escape value
		public function escape_value( $value ) {
			if( $this->real_escape_string_exists ) { // PHP v4.3.0 or higher
				// undo any magic quote effects so mysqli_real_escape_string can do the work
				if( $this->magic_quotes_active ) 
					$value = stripslashes( $value ); 
				$value = mysqli_real_escape_string($this->connection, $value );
			} else { // before PHP v4.3.0
				// if magic quotes aren't already on then add slashes manually
				if (!$this->magic_quotes_active) 
					$value = addslashes( $value ); 
				// if magic quotes are active, then the slashes already exist
			}
			return $value;
		}
		
		// "database-neutral" methods
		public function fetch_array($result_set) {
			return mysqli_fetch_array($result_set);
		}
		  
		public function num_rows($result_set) {
			return mysqli_num_rows($result_set);
		}
		  
		public function insert_id() {
			return mysqli_insert_id($this->connection);
		}
		  
		public function affected_rows() {
			return mysqli_affected_rows($this->connection);
		}

		private function confirm_query($result) {
			if (!$result) {
				$output = "Database query failed: " . mysqli_error($this->connection);
				//$output .= "Last SQL query: " . $this->last_query;
				die( $output );
			}
		}
	
}

	//get new database connection
	$database = new MySQLDatabase();

?>