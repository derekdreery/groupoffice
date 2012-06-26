<?php
class GO_Addressbook_Customfieldtype_Company extends GO_Customfields_Customfieldtype_Abstract{
	
	public function name(){
		return 'Company';
	}
	
	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		$html="";
		if(!empty($attributes[$key])) {

			if(!defined('EXPORTING')){
				$name = htmlspecialchars($this->getName($attributes[$key]), ENT_COMPAT, 'UTF-8');
				$html='<a href="#" onclick=\'GO.linkHandlers["GO_Addressbook_Model_Company"].call(this,'.
					$this->getId($attributes[$key]).');\' title="'.$name.'">'.
						$name.'</a>';
			}else
			{
				$html=$this->getName($attributes[$key]);
			}
		}
		return $html;
	}
}