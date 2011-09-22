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
 * @package go.base.util 
 */
class GO_Base_Util_CsvReader{
	
	private $_fp;
	
	public function __construct($file){
		$this->_fp = fopen($file, "r");
		if (!$this->_fp) {
			throw new Exception('Could not open CSV file');
		}
	}
	
	public function nextRecord(){
		return fgetcsv($this->_fp, 4096, GO::user()->list_separator, GO::user()->text_separator);
	}
}