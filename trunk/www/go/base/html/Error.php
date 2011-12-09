<?php

class GO_Base_Html_Error extends GO_Base_Html_Input {
	public static function getError($inputName='form') {
		return parent::getError($inputName);
	}
	
	public static function setError($message,$inputName='form') {
		return parent::setError($inputName, $message);
	}
}