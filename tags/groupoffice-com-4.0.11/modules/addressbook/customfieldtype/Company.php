<?php
class GO_Addressbook_Customfieldtype_Company extends GO_Customfields_Customfieldtype_Abstract{
	
	public function name(){
		return 'Company';
	}
	
	public function formatDisplay($key, &$attributes) {
		$html="";
		if(!empty($attributes[$key])) {

			if(GO::modules()->users && !defined('EXPORTING')){
				$html='<a href="#" onclick=\'GO.linkHandlers["GO_Addressbook_Model_Company"].call(this,'.
					$this->getId($attributes[$key]).');\' title="'.$this->getName($attributes[$key]).'">'.
						$this->getName($attributes[$key]).'</a>';
			}else
			{
				$html=$this->getName($attributes[$key]);
			}
		}
		return $html;
	}

	private function getId($cf) {
		$pos = strpos($cf,':');
		return substr($cf,0,$pos);
	}

	private function getName($cf) {
		$pos = strpos($cf,':');
		return htmlspecialchars(substr($cf,$pos+1), ENT_COMPAT,'UTF-8');
	}
}