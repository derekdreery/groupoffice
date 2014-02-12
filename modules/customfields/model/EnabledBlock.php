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

	public static function getEnabledBlocks($modelId,$listedModelTypeName,$listingModelName) {
		
		if ($listingModelName=='GO_Addressbook_Model_Contact')
			$dataType = 'GO_Addressbook_Customfieldtype_Contact';
		if ($listingModelName=='GO_Addressbook_Model_Company')
			$dataType = 'GO_Addressbook_Customfieldtype_Company';
		
		return self::model()->find(
				GO_Base_Db_FindParams::newInstance()
					->joinModel(array(
						'model'=>'GO_Customfields_Model_Block',
						'localTableAlias'=>'t',
						'localField'=>'block_id',
						'foreignField'=>'id',
						'tableAlias'=>'b',
						'type'=>'INNER'
					))
					->joinModel(array(
						'model'=>'GO_Customfields_Model_Field',
						'localTableAlias'=>'b',
						'localField'=>'field_id',
						'foreignField'=>'id',
						'tableAlias'=>'cf',
						'type'=>'INNER'
					))
					->criteria(
						GO_Base_Db_FindCriteria::newInstance()
							->addCondition('model_id', $modelId, '=', 't')
							->addCondition('model_type_name', $listedModelTypeName, '=', 't')
							->addCondition('datatype', $dataType, '=', 'cf')
					)->debugSql()
			);
//		->findByAttributes(array(
//			'model_id' => $modelId,
//			'model_type_name' => $listedModelTypeName
//		));
		
	}
	
}