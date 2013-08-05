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
				->ignoreAcl()
				->order('mf.order')
				->joinModel(array(
					'model' => 'GO_Site_Model_MultifileFile',
					'localTableAlias' => 't',
					'localField' => 'id',
					'foreignField' => 'file_id',
					'tableAlias' => 'mf'))
		
			->criteria(GO_Base_Db_FindCriteria::newInstance()
				->addCondition('model_id', $model->model_id,'=','mf')
				->addCondition('field_id', $fieldId,'=','mf'));

		return GO_Files_Model_File::model()->find($findParams,'false',true);
	}	
	
	public function selectForGrid(){
		return false;
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