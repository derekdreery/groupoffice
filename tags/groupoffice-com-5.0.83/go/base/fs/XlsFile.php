<?php
/**
 * Copyright Intermesh BV
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 */

/**
 * Reads OR writes CSV files.
 * 
 * @package GO.base.fs
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @copyright Copyright Intermesh BV.
 */

class GO_Base_Fs_XlsFile extends GO_Base_Fs_File{
		
	/**
	 * Full path to file.
	 * @var String
	 */
	protected $path;
	
	/**
	 * Type of XLS file.
	 * @var String
	 */
	protected $filetype;
	
	/**
	 * Excel worksheet object to read from.
	 * @var PHPExcel_Worksheet
	 */
	protected $phpExcelSheet;

	/**
	 * @var Boolean
	 */
	protected $readOnly;

	/**
	 * 2D-array of cell values. This is created by the constructor, and read from
	 * in getRecord()
	 * @var Array
	 */
	protected $rowsBuffer;
	
	/**
	 * The maximum row number to read from.
	 * @var Int
	 */
	protected $maxRows;
	
	/**
	 * The maximum column number to read from.
	 * @var Int
	 */
	protected $maxColNr;
	
	/**
	 * Row number for the iterator in getRecord().
	 * @var Int
	 */
	protected $nextRowNr;
	
	/**
	 * The largest column size encountered in createRowsBuffer(). Used to store
	 * the bottom of the XLS file.
	 * @var Int
	 */
	protected $bottomOfFileRowNr;
	
	/**
	 * The maximum number of empty cells permitted to read from, before continuing
	 * to the next row/colum.
	 * @var Int
	 */
	protected $nAllowedEmptyCells;
	
	/**
	 * Constructor.
	 * @param String $path The full path to the XLS file.
	 * @param Int $maxColNr The highest column number to read from.
	 * @param Int $maxRows The highest row number to read from.
	 * @param Int $readSheetNr The sheet number to read from
	 */
	public function __construct($path,$maxColNr=false,$maxRows=false,$readSheetNr=0) {
		
		$this->path=$path;
		$this->readOnly=true;		
	
		$this->init($readSheetNr);
				
		$this->maxRows = $maxRows!==false ? $maxRows : 1048576;
		$this->maxColNr = $maxColNr!==false ? $maxColNr : 26*26*2;
		$this->nAllowedEmptyCells = 1000;
		$this->createRowsBuffer();

		$this->nextRowNr = 1;
		
	}
	
	protected function init($readSheetNr=0) {
		require_once GO::config()->root_path.'go/vendor/PHPExcel/PHPExcel/IOFactory.php';
		$this->filetype = PHPExcel_IOFactory::identify($this->path);
		$xlsReader = PHPExcel_IOFactory::createReader($this->filetype);
		$xlsReader->setReadDataOnly($this->readOnly);
		$this->phpExcelSheet = $xlsReader->load($this->path)->getSheet($readSheetNr);
	}
	
	protected function createRowsBuffer() {
		
		$this->rowsBuffer = array();
		$HIGHEST_ROW_WITH_VALUE = -1;
		$HIGHEST_COLUMN_WITH_VALUE = -1;
										
		// First ascertain the dimensions of the worksheet.
		for ($row=1;$row<=$this->maxRows && $this->_maxEmptyCellsNotExceeded($row,$HIGHEST_ROW_WITH_VALUE);$row++) {		
			for ($col=0;$col<=$this->maxColNr && $this->_maxEmptyCellsNotExceeded($col,$HIGHEST_COLUMN_WITH_VALUE);$col++) {
								
				$value = $this->phpExcelSheet->getCellByColumnAndRow($col,$row)->getCalculatedValue();
				
				if ( isset($value) && $value!==NULL && $value!==false ) {
					
					// Keep track of the highest column nr that had a value in the cell.
					if ($col>$HIGHEST_COLUMN_WITH_VALUE)
						$HIGHEST_COLUMN_WITH_VALUE = $col;
					// Keep track of the highest row nr that had a value in the cell.
					if ($row>$HIGHEST_ROW_WITH_VALUE)
						$HIGHEST_ROW_WITH_VALUE = $row;
				}
								
			}
		}

		// Create rows buffer
		for ($row=1;$row<=$HIGHEST_ROW_WITH_VALUE;$row++) {
			$rowRecord = array();
			for ($col=0;$col<=$HIGHEST_COLUMN_WITH_VALUE;$col++) {
				$rowRecord[] = $this->phpExcelSheet->getCellByColumnAndRow($col,$row)->getCalculatedValue();
				GO::debug($this->phpExcelSheet->getCellByColumnAndRow($col,$row)->getCalculatedValue());
			}
			$this->rowsBuffer[$row] = $rowRecord;
		}
		
		$this->bottomOfFileRowNr = $HIGHEST_ROW_WITH_VALUE;
		
	}
	
	protected function _maxEmptyCellsNotExceeded($cellNr,$highestCellWithValue) {
		return $cellNr-$highestCellWithValue<=$this->nAllowedEmptyCells;
	}
	
	public function getFileType() {
		return $this->filetype;
	}
		
	/**
	 * Retrieves the contents of the next row in the XLS file.
	 * @return Array An array of elements read from the XLS line. Or false when there is no next record;
	 */
	public function getRecord(){

		if ($this->nextRowNr>$this->bottomOfFileRowNr)
			return false;
				
		$rowRecord = $this->rowsBuffer[$this->nextRowNr];
		$this->nextRowNr++;
		
		return $rowRecord;
	}

	/**
	 * TODO: Write record to XLS.
	 * @param Array $fields The elements of this array will be written into a line
	 * of the current XLS file.
	 * @return int The length of the written string, or false on failure.
	 */
	public function putRecord($fields){
		throw new Exception('TODO: Write record to XLS.');
		return false;
	}
	
}