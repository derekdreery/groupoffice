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
	
	public static function renderBegin($targetRoute=false,$showErrors=false){
		echo '<form method="post" >';
		echo '<input type="hidden" name="formRoute" value="'.$targetRoute.'" />';
		if($showErrors){
			$error = GO_Base_Html_Error::getError();
			echo $error;
		}
	}
	
	public static function renderEnd(){
		echo '<div style="clear:both;"></div>';
		echo '</form>';
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