<?php

class GO_Base_Html_Form {
//	
//	private $_targetRoute = '';
//	
//	public function __construct($targetRoute=false) {
//		if($targetRoute){
//			$this->_targetRoute = GO::url($targetRoute);
//		}
//	}
	
	public static function getHtmlBegin($targetRoute=false,$formName='form',$showErrors=false){
		$html = '<form method="post" name="'.$formName.'" >';
	//	$html .= '<input type="hidden" name="formRoute" value="'.$targetRoute.'" />';
		if($showErrors){
			$error = GO_Base_Html_Error::getError();
			$html .= $error;
		}
		return $html;
	}
	
	public static function renderBegin($targetRoute=false,$formName='form',$showErrors=false){
		echo self::getHtmlBegin($targetRoute,$formName,$showErrors);
	}
	
	public static function renderEnd($printErrors=true){
		
		if($printErrors && GO_Base_Html_Error::hasErrors()){
			GO_Base_Html_Error::printErrors();
		}
		
		echo self::getHtmlEnd();
	}
	
	public static function getHtmlEnd(){
		$html = '<div style="clear:both;"></div>';
		$html .= '</form>';
		return $html;
	}
	
//	
//	public function renderBegin($showErrors=false) {
//		echo '<form method="post" >';
//		echo '<input type="hidden" name="formRoute" value="'.$this->_targetRoute.'" />';
//		if($showErrors)
//			echo GO_Base_Html_Error::getError();
//	}
//	
//	public function renderEnd() {
//		echo '</form>';
//	}
//	
}