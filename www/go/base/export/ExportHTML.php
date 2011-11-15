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
 * HTML Output stream.
 * 
 * @version $Id: ExportHTML.php 7607 2011-08-04 13:41:42Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits <wsmits@intermesh.nl>
 * @package GO.base.export
 */
class GO_Base_Export_ExportHTML extends GO_Base_Export_AbstractExport {	
	
	private $_writeHeader;
	
	public function showInView() {
		return true;
	}
	
	private function _sendHeaders() {
		header('Content-Type: text/html; charset=UTF-8');
	}

	private function _renderHead() {
		echo "<html>\n";
		echo "<head>\n<title>".$this->title."</title>\n</head>\n";
		echo "<body>\n";
		echo "<table border='1' cellspacing='0' cellpadding='0' style='border-collapse:collapse;'>\n";
	}
	
	private function _write($data) {
		
		if($this->_writeHeader) {
			echo "<tr>\n";
			foreach($data as $column)
				echo "<th style='padding:2px; font-weight:bold;'>$column</th>";
			echo "</tr>\n";
			$this->_writeHeader = false;
		} else {
			echo "<tr>\n";
			foreach($data as $column)
				echo "<td style='padding:2px;'>$column</td>";
			echo "</tr>\n";
		}
	}	

	private function _renderFooter() {
		echo "</table>\n";
		echo "</body>\n";
		echo "</html>";
	}
	
	public function output() {
		$this->_sendHeaders();
		$this->_renderHead();
		
		if($this->header) {
			$this->_writeHeader = true;
			$this->_write($this->getLabels());
		}
		
		while($record = $this->store->nextRecord()){
			$this->_write($record);
		}
		
		$this->_renderFooter();
	}
	
	public function getName() {
		return 'HTML';
	}
		
	public function useOrientation(){
		return false;
	}
}