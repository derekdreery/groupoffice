<?php
class GO_Files_Customfieldtype_File extends GO_Customfields_Customfieldtype_Abstract{
	
	public function name(){
		return 'File';
	}
	
	public function fieldSql(){
		return "VARCHAR(255) NOT NULL default ''";
	}
	
	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		$html="";
		if(!empty($attributes[$key])) {
			
			$file = GO_Files_Model_File::model()->findByPath($attributes[$key]);

			if($file){
				if(!GO_Customfields_Model_AbstractCustomFieldsRecord::$formatForExport){
					$html='<a href="#" onclick="GO.linkHandlers[\'GO_Files_Model_File\'].call(this,\''.$attributes[$key].'\');">'.basename($attributes[$key]).'</a>'.
					'<a href="#" onclick=\''.$file->getDefaultHandler()->getHandler($file).'\' style="display:block;float: right;" class="go-icon btn-edit">&nbsp;</a>';
					//$html='<a href="#"  title="'.$attributes[$key].'">'.$attributes[$key].'</a>';
				}else
				{
					$html=$attributes[$key];
				}
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