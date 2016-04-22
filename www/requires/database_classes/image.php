<?php

	/*
		Defines the Image class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Image extends Database_Object {
	
	protected static $table_name = 'image';
	protected static $db_fields = array('image_wk', 'filename', 'type', 'size', 'create_dt');
	
	//database fields
	public $image_wk;
	public $filename;
	public $type;
	public $size;
	public $create_dt;
	public $is_deleted;
	
	//image attribute fields during upload
	public $temp_path;
	public static $upload_directory = "../uploads";
	public $error;
	public $is_image = 0;
  
  	//dictionary to define all errors
	public static $error_dictionary = array(
		UPLOAD_ERR_OK 			=> "No errors.",
		UPLOAD_ERR_INI_SIZE  	=> "Larger than upload_max_filesize.",
		UPLOAD_ERR_FORM_SIZE 	=> "Larger than form MAX_FILE_SIZE.",
		UPLOAD_ERR_PARTIAL 		=> "Partial upload.",
		UPLOAD_ERR_NO_FILE 		=> "No file.",
		UPLOAD_ERR_NO_TMP_DIR 	=> "No temporary directory.",
		UPLOAD_ERR_CANT_WRITE 	=> "Can't write to disk.",
		UPLOAD_ERR_EXTENSION 	=> "File upload stopped by extension."
	);
	
	//get the form data from the server and apply it
	public function get_form_data($file_return_array) {
		$this->filename = strtolower(str_replace(' ', '_', $file_return_array['name']));
		$this->type = $file_return_array['type'];
		$this->size = $file_return_array['size'];
		$this->temp_path = $file_return_array['tmp_name'];
		$this->error = $file_return_array['error'];
		
		//check to see if the file is an actual image
		if ( strpos( $this->type, 'image' ) !== false ) 
			// this is an image 
			$this->is_image = 1;
			
		//if we're here, success
		return true;
	}
	
	//check for any potential errors
	public function check_errors() {
		global $session;
		
		//first, we check to make sure the file was uploaded successfully
		if(Image::$error_dictionary[$this->error] != 'No errors.') {
			$session->message("There was an issue uploading your file: <strong>".Image::$error_dictionary[$this->error]."</strong>");
			redirect_head(current_url());
		}
		
		//second, we check to make sure the file is image
		if($this->is_image != 1) {
			$session->message("You did not upload an image file; you can only upload images.");
			redirect_head(current_url());
		}
		
		//if we're here, success
		return true;
	}
	
	//move the file to the actual location
	public function move_file() {
		global $session;
		
		//pre-append the key to the beggining, followed by an underscore
		//this ensures image uniqueness and no overrides
		$this->filename = $this->image_wk."_".basename($this->filename);
		
		//die($this->temp_path."<br />".BASE."uploads/".$this->filename); //debug
		if(!move_uploaded_file($this->temp_path, BASE."uploads/".$this->filename)) {
			//remove the record from the database
			$this->delete();
			
			$session->message("There was an issue uploading the image, please try again.");
			redirect_head(current_url());
		}
		
		$this->save();
		
		//if we're here, success
		return true;
	}
	
}

?>