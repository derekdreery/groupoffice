<?php
/*
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * Read a CSV file using Group-Office preferences
 * 
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id: config.class.inc.php 7687 2011-06-23 12:00:34Z mschering $
 * @copyright Copyright Intermesh BV.
 * @package GO.base.util 
 */
class GO_Base_Csv_Reader{
	
	private $filename;
	public $delimiter=',';
	public $enclosure='"';
	protected $fp;
	
	public function __construct($filename) {
		if(GO::user()){
			$this->delimiter=GO::user()->list_separator;
			$this->enclosure=GO::user()->text_separator;
		}
		$this->filename=$filename;
	}
	
	public function __destruct() {
		fclose($this->fp);
	}
	
	/**
	 * Sets the current file handle's file pointer.
	 * @param string $mode Mode to set the file at. Default: mode read 'r'. See
	 * php's fopen documentation for possible modes.
	 */
	protected function setFP($mode='r'){
		if(!isset($this->fp))
			$this->fp = fopen($this->filename, $mode);	
		if(!is_resource($this->fp))
			throw new Exception("Could not read CSV file");
	}
	
	/**
	 * Retrieves the contents of the next row in the CSV file. If the file's
	 * handle (i.e., file pointer) has not been set using setFP(), this will be
	 * done now, giving the file a read mode 'r'.
	 * @return Array An array of elements read from the CSV line.
	 */
	public function getRecord(){
		$this->setFP();
		return fgetcsv($this->fp, 4096, $this->delimiter, $this->enclosure);
	}
	
}