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
 * Writes a CSV file
 * 
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 * @copyright Copyright Intermesh BV.
 * @package GO.base.util 
 */
class GO_Base_Csv_Writer extends GO_Base_Csv_Reader{
		
	/**
	 * Writes an array of strings as the next line of the CSV file, after making
	 * sure a file handle is set to write mode 'w'.
	 * @param Array $fields The elements of this array will be written into a line
	 * of the current CSV file.
	 * @return int The length of the written string, or false on failure.
	 */
	public function putRecord($fields){
		$this->setFP('w');
//		foreach ($fields as $k => $field)
//			$fields[$k] = str_replace(array($this->delimiter,$this->enclosure),array(' ',''),$field);
		return fputcsv($this->fp, $fields, $this->delimiter, $this->enclosure);
	}
}