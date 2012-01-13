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
	
	public static function checkRequired(){		
		if(isset($_POST['required'])){
			foreach($_POST['required'] as $inputName){
				if ($pos = strpos($inputName, '[')) {
					$key1 = substr($inputName, 0, $pos);
					$key2 = substr($inputName, $pos + 1, -1);
					if(empty($_POST[$key1][$key2]))
						parent::setError($inputName, 'This field is required');
				}else
				{
					if(empty($_POST[$inputName]))
						parent::setError($inputName, 'This field is required');
				}
			}
		}
		
		return !parent::hasErrors();
	}
	
	public static function validateModel($model,$attrmapping=false){
		
//		if(GO_Base_Util_Http::isPostRequest()){
			
//			if(!empty($attrmapping)){
//				foreach($attrmapping as $attr=>$replaceattr){
//					$model->$replaceattr = $_POST[$attr];
//				}
//			}

			if (!$model->validate()) {
				$errors = $model->getValidationErrors();
				foreach ($errors as $attribute => $message) {
					
					$formAttribute = isset($attrmapping[$attribute]) ? $attrmapping[$attribute] : $attribute;
					
					GO_Base_Html_Input::setError($formAttribute, $message); // replace is needed because of a mix up with order model and company model
				}
				GO_Base_Html_Error::setError(GO::t('errorsInForm'));
			}
		}
//	}
}