<?php
class GO_Addressbook_Customfieldtype_Contact extends GO_Customfields_Customfieldtype_Abstract{
	
	public function name(){
		return 'Contact';
	}
	
	public static function getModelName() {
		return 'GO_Addressbook_Model_Contact';
	}
	
	public function includeInSearches() {
		return true;
	}

	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		$html="";
		if(!empty($attributes[$key])) {

			if(!GO_Customfields_Model_AbstractCustomFieldsRecord::$formatForExport){
				$name = htmlspecialchars($this->getName($attributes[$key]), ENT_COMPAT, 'UTF-8');
				$html='<a href="#" onclick=\'GO.linkHandlers["GO_Addressbook_Model_Contact"].call(this,'.
					$this->getId($attributes[$key]).');\' title="'.$name.'">'.
						$name.'</a>';
			}else
			{
				$html=$this->getName($attributes[$key]);
			}
		}
		return $html;
	}
	
	public function formatFormOutput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		
		if(!GO_Customfields_Model_AbstractCustomFieldsRecord::$formatForExport){
			return parent::formatFormOutput($key, $attributes, $model);
		}else
		{
			return $this->getName($attributes[$key]);
		}		
	}	
}