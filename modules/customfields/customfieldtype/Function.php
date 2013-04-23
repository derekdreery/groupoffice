<?php

class GO_Customfields_Customfieldtype_Function extends GO_Customfields_Customfieldtype_Abstract {

	public function name() {
		return 'Function';
	}

//	public function formatFormOutput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
//		$result_string = '';
//
//		if (!empty($this->field->function)) {
//			$f = $this->field->function;
//			foreach ($attributes as $key=>$value) {
//				
//					$f = str_replace('{' . $key . '}', GO_Base_Util_Number::unlocalize($value), $f);
//				
//			}
//			$f = preg_replace('/\{[^}]*\}/', '0',$f);
//			//go_debug($fields[$i]['function']);
//			@eval("\$result_string=" . $f . ";");
//		}
//
//		$attributes[$key] = GO_Base_Util_Number::localize($result_string);
//		return $attributes[$key];
//	}
//	
//	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
//		return $this->formatFormOutput($key, $attributes, $model);
//	}
//	
	public function formatFormInput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model){
		$result_string = '';

		if (!empty($this->field->function)) {
			$f = $this->field->function;
			foreach ($attributes as $key=>$value) {
				
					$f = str_replace('{' . $key . '}', floatval(GO_Base_Util_Number::unlocalize($value)), $f);
				
			}
			$f = preg_replace('/\{[^}]*\}/', '0',$f);
			
			$old = ini_set("display_errors", "on"); //if we don't set display_errors to on the next eval will send a http 500 status. Wierd but this works.
			@eval("\$result_string=" . $f . ";");
			if($old!==false)
				ini_set("display_errors", $old);
			
			if($result_string=="")
				$result_string=null;
				
		}

		$attributes[$key] = $result_string;
		return $attributes[$key];
	}

}