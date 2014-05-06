<?php
class GO_Customfields_Customfieldtype_Php extends GO_Customfields_Customfieldtype_Abstract{
	
	public function name(){
		return 'Php';
	}
	
	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		if (empty($this->field->extra_options))
			return '';
			
		$f = $this->field->extra_options;
		$old = ini_set("display_errors", "on");
		$method = function ($record) use($f) {
			return @eval($f);
		};
		if($old!==false)
				ini_set("display_errors", $old);
		
		return (string)$method($model); //cast to strign because displaypanel checks for field.length
	}
	
	public function formatFormOutput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {		
		return $this->formatDisplay($key, $attributes, $model);
	}
	
	public function fieldSql() {
		return "TINYINT(1) NULL";
	}

}