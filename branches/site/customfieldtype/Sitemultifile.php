<?php
class GO_Site_Customfieldtype_Sitemultifile extends GO_Customfields_Customfieldtype_Abstract{
	
	public function name(){
		return 'Sitemultifile';
	}
	
	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		
		return 'No display created (in GO_Site_Customfieldtype_Sitemultifile)';
	}
	
	public function formatFormOutput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {

		$column = $model->getColumn($key);
		if(!$column)
			return null;
				
		$fieldId = $column['customfield']->id;
		
		$findParams = GO_Base_Db_FindParams::newInstance()
				->select('COUNT(*) AS count')
				->single()
			->criteria(GO_Base_Db_FindCriteria::newInstance()
				->addCondition('model_id', $model->model_id)
				->addCondition('field_id', $fieldId));

		$model = GO_Site_Model_MultifileFile::model()->find($findParams);
		
		$string = '';
		$string = sprintf(GO::t('multifileSelectValue','site'), $model->count);
		
		return $string;
	}	
	
	public function formatRawOutput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {

		$column = $model->getColumn($key);
		if(!$column)
			return null;
		
		$fieldId = $column['customfield']->id;
		
		$findParams = GO_Base_Db_FindParams::newInstance()
			->criteria(GO_Base_Db_FindCriteria::newInstance()
				->addCondition('model_id', $model->id)
				->addCondition('field_id', $fieldId));

		return GO_Site_Model_MultifileFile::model()->find($findParams);
	}	
	
	public function selectForGrid(){
		return false;
	}
	
	
	
	

}