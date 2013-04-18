<?php
/**
 * @property int $block_id
 * @property string $model_type_name
 * @property int $model_id
 */
class GO_Customfields_Model_EnabledBlock extends GO_Base_Db_ActiveRecord{
		
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Customfields_Model_Field 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function tableName() {
		return 'cf_enabled_blocks';
	}
	
	public function primaryKey() {
		return array('block_id','model_id','model_type_name');
	}
	
	public function relations() {
		return array(
				'block' => array('type' => self::BELONGS_TO, 'model' => 'GO_Customfields_Model_Block', 'field' => 'block_id')
			);
	}
		
//	protected function init() {
//		
//		$this->columns['model_type_name']['required']=true;
//		$this->columns['field_id']['required']=true;
//		
//		parent::init();
//	}

	public static function getEnabledBlocks($modelId,$modelTypeName) {
		
		return self::model()->findByAttributes(array(
			'model_id' => $modelId,
			'model_type_name' => $modelTypeName
		));
		
	}
	
}