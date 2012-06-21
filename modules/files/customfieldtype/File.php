<?php
class GO_Files_Customfieldtype_File extends GO_Customfields_Customfieldtype_Abstract{
	
	public function name(){
		return 'File';
	}
	
	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		$html="";
		if(!empty($attributes[$key])) {

			if(GO::modules()->users && !defined('EXPORTING')){
				$html='<a href="#" onclick=\'GO.linkHandlers["GO_Files_Model_File"].call(this,"'.
					$attributes[$key].'");\' title="'.$attributes[$key].'">'.
						$attributes[$key].'</a>';
			}else
			{
				$html=$attributes[$key];
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