<?php

namespace GO\Files\Customfieldtype;


class File extends \GO\Customfields\Customfieldtype\AbstractCustomfieldtype{
	
	public function name(){
		return 'File';
	}
	
	public function formatDisplay($key, &$attributes, \GO\Customfields\Model\AbstractCustomFieldsRecord $model) {
		$html="";
		if(!empty($attributes[$key])) {

			if(!\GO\Customfields\Model\AbstractCustomFieldsRecord::$formatForExport){
				$html='<a href="#" onclick=\'GO.linkHandlers["\GO\Files\Model\File"].call(this,"'.
					$attributes[$key].'");\' title="'.$attributes[$key].'">'.
						$attributes[$key].'</a>';
			}else
			{
				$html=$attributes[$key];
			}
		}
		return $html;
	}
	
	public function formatFormOutput($key, &$attributes, \GO\Customfields\Model\AbstractCustomFieldsRecord $model) {
		
		if(!\GO\Customfields\Model\AbstractCustomFieldsRecord::$formatForExport){
			return parent::formatFormOutput($key, $attributes, $model);
		}else
		{
			return $this->getName($attributes[$key]);
		}		
	}	

}