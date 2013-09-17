<?php
class GO_Files_Exception_FileLocked extends Exception{
	public function __construct() {
		$message = "File is locked";
		return parent::__construct($message);
	}
}