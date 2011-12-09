<?php

class GO_Base_Html_Error extends GO_Base_Html_Input {
	public static function getError($inputName='form') {
		$error = parent::getError($inputName);
		unset(GO::session()->values['formErrors'][$inputName]);
		return $error;
	}
	
	public static function setError($message,$inputName='form') {
		return parent::setError($inputName, $message);
	}
}