<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * CSV Output stream.
 * 
 * @version $Id: Group.php 7607 2011-08-04 13:41:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.export
 */
class GO_Base_Export_ExportHTML implements GO_Base_Export_ExportInterface{	
	private $_addKeysAsHeaders = true;
	private $_headRendered = false;
	private $_title = '';
	
	public function __construct($filename, $addKeysAsHeaders=true) {
		$this->sendHeaders();
		$this->_title = $filename;
		$this->_addKeysAsHeaders=$addKeysAsHeaders;
	}
	
	public function showInView() {
		return true;
	}
	
	public function sendHeaders() {
		header('Content-Type: text/html; charset=UTF-8');
	}

	private function _renderHead() {
		echo "<html>\n";
		echo "<head>\n<title>$this->_title</title>\n</head>\n";
		echo "<body>\n";
		echo "<table border='1' cellspacing='0' cellpadding='0' style='border-collapse:collapse;'>\n";
		$this->_headRendered = true;
	}
	
	public function write($data) {
		if(!$this->_headRendered)
			$this->_renderHead();
		
		if($this->_addKeysAsHeaders) {
			echo "<tr>\n";
			foreach($data as $column)
				echo "<th style='padding:2px; font-weight:bold;'>$column</th>";
			echo "</tr>\n";
			$this->_addKeysAsHeaders = false;
		} else {
			echo "<tr>\n";
			foreach($data as $column)
				echo "<td style='padding:2px;'>$column</td>";
			echo "</tr>\n";
		}
	}	
	
	public function flush() {
		
	}
	
	public function endFlush() {
		echo "</table>\n";
		echo "</body>\n";
		echo "</html>";
	}
	
	public function getName() {
		return 'HTML';
	}
		
	public function useOrientation(){
		return false;
	}
}