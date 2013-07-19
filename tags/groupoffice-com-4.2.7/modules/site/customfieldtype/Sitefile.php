<?php
class GO_Site_Customfieldtype_Sitefile extends GO_Customfields_Customfieldtype_Abstract{
	
	public function name(){
		return 'Sitefile';
	}
	
	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		$html="";
		if(!empty($attributes[$key])) {

			if(!GO_Customfields_Model_AbstractCustomFieldsRecord::$formatForExport){
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
	
	public function formatFormOutput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		
		if(!GO_Customfields_Model_AbstractCustomFieldsRecord::$formatForExport){
			return parent::formatFormOutput($key, $attributes, $model);
		}else
		{
			return $this->getName($attributes[$key]);
		}		
	}	

	/**
	 * Function to enable this customfield type for some models only.
	 * When no modeltype is given then this customfield will work on all models.
	 * Otherwise it will only be available for the given modeltypes.
	 * 
	 * Example:
	 *	return array('GO_Site_Model_Content','GO_Site_Model_Site');
	 *  
	 * @return array
	 */
	public function supportedModels(){
		return array('GO_Site_Model_Content','GO_Site_Model_Site');
	}
	
}