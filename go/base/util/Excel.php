<?php
require_once(GO::config()->root_path.'go/vendor/PHPExcel/PHPExcel.php');
class GO_Base_Util_Excel extends PHPExcel {
	
	private $_writer;
	
	private function _getWriter() {
		require_once(GO::config()->root_path.'go/vendor/PHPExcel/PHPExcel/Writer/Excel2007.php');
		if (empty($this->_writer))
			$this->_writer = new PHPExcel_Writer_Excel2007($this);
		
		return $this->_writer;
	}
	
	public function save($filePath) {
		
		$this->_getWriter()->save($filePath);
		
	}
	
	public function setDefaultStyle($fontFamily='Arial',$fontSize=10,$colorRGB='00000000',$bold=false,$italic=false,$underline=false) {
		require_once(GO::config()->root_path.'go/vendor/PHPExcel/PHPExcel/Style.php');
		require_once(GO::config()->root_path.'go/vendor/PHPExcel/PHPExcel/Style/Font.php');
		require_once(GO::config()->root_path.'go/vendor/PHPExcel/PHPExcel/Style/Color.php');
		$colorObj = new PHPExcel_Style_Color();
		$colorObj->setRGB($colorRGB);
		$fontObj = new PHPExcel_Style_Font();
		$fontObj->setName($fontFamily);
		$fontObj->setSize($fontSize);
		$fontObj->setColor($colorObj);
		$fontObj->setBold($bold);
		$fontObj->setItalic($italic);
		$fontObj->setUnderline($underline);
		
		$styleObj = new PHPExcel_Style();
		$styleObj->setFont($fontObj);
		$this->getActiveSheet()->setDefaultStyle($styleObj);
	}
	
	public function setStyle($cellRange,$fontFamily='Arial',$fontSize=10,$colorRGB='00000000',$bold=false,$italic=false,$underline=false) {
		require_once(GO::config()->root_path.'go/vendor/PHPExcel/PHPExcel/Style.php');
		require_once(GO::config()->root_path.'go/vendor/PHPExcel/PHPExcel/Style/Font.php');
		require_once(GO::config()->root_path.'go/vendor/PHPExcel/PHPExcel/Style/Color.php');
		$colorObj = new PHPExcel_Style_Color();
		$colorObj->setRGB($colorRGB);
		$fontObj = new PHPExcel_Style_Font();
		$fontObj->setName($fontFamily);
		$fontObj->setSize($fontSize);
		$fontObj->setColor($colorObj);
		$fontObj->setBold($bold);
		$fontObj->setItalic($italic);
		$fontObj->setUnderline($underline);
		
		$styleObj = new PHPExcel_Style();
		$styleObj->setFont($fontObj);
		$this->getActiveSheet()->setSharedStyle($styleObj, $cellRange);
		
	}
	
	public function setWidth($column='A',$width=-1) {
		
		$this->getActiveSheet()->getColumnDimension($column)->setWidth($width);
		
	}
	
	public function setCellValue($cellId,$value) {
		
		$this->getActiveSheet()->setCellValue($cellId,$value);
		
	}
	
	public function setCellValueByColumnAndRow($colId,$rowNr,$value) {
		
		$this->getActiveSheet()->setCellValueByColumnAndRow($colId,$rowNr,$value);
		
	}
	
}
?>
