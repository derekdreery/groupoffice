<?php
class GO_Customfields_Customfieldtype_User extends GO_Customfields_Customfieldtype_Abstract{
	
	public function name(){
		return 'User';
	}
	
	public function includeInSearches() {
		return true;
	}
	
	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		$html="";
		if(!empty($attributes[$key])) {

			if(!GO_Customfields_Model_AbstractCustomFieldsRecord::$formatForExport){
				$name = htmlspecialchars($this->getName($attributes[$key]), ENT_COMPAT, 'UTF-8');
				$html='<a href="#" onclick=\'GO.linkHandlers["GO_Base_Model_User"].call(this,'.
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