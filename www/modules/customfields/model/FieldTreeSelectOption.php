<?php

/**
 * Group-Office
 * 
 * Copyright Intermesh BV. 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @package GO.modules.customfields.model
 * @version $Id: example.php 7607 20120101Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */
 
/**
 * The GO_Customfields_Model_FieldTreeSelectOption model
 *
 * @package GO.modules.customfields.model
 * @property int $sort
 * @property string $name
 * @property int $field_id
 * @property int $parent_id
 * @property int $id
 */

class GO_Customfields_Model_FieldTreeSelectOption extends GO_Base_Db_ActiveRecord{
		
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Customfields_Model_FieldSelectOption 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function tableName() {
		return 'cf_tree_select_options';
	}	
	
	public function relations() {
		return array(
				'field' => array('type' => self::BELONGS_TO, 'model' => 'GO_Customfields_Model_Field', 'field' => 'field_id')		);
	}	
	
	protected function beforeSave() {
		
		if($this->isNew){
			$record = $this->findSingle(array(
					'fields'=>'MAX(`sort`) AS sort',
					'where'=>'field_id=:field_id',
					'bindParams'=>array('field_id'=>$this->field_id)
			));
			if($record)
				$this->sort=intval($record->sort);
		}
		
		return parent::beforeSave();
	}
	
	protected function afterSave($wasNew) {
		
		if($wasNew){
			
			//We need to create a GO_Customfields_Customfieldtype_TreeselectSlave field for all tree levels
			$nestingLevel = $this->field->getTreeSelectNestingLevel();
			
			for($i=1;$i<$nestingLevel;$i++){
				$field =GO_Customfields_Model_Field::model()->findSingle(array(
						'where'=>'treemaster_field_id=:treemaster_field_id AND nesting_level=:nesting_level',
						'bindParams'=>array('treemaster_field_id'=>$this->field_id,'nesting_level'=>$i)
				));
				
				if(!$field){
					$field = new GO_Customfields_Model_Field();
					$field->name=$this->field->name.' '.$i;
					$field->datatype='GO_Customfields_Customfieldtype_TreeselectSlave';
					$field->treemaster_field_id=$this->field_id;
					$field->nesting_level=$i;
					$field->category_id=$this->field->category_id;
					$field->save();
				}
				
			}
		}
		
		return parent::afterSave($wasNew);
	}
	

	
//	public function getChildren(){
//		$stmt = self::model()->find(array(
//			'where'=>'parent_id=:parent_id AND field_id=:field_id',
//			'bindParams'=>array('parent_id'=>$this->id,'field_id'=>$this->field_id),
//			'order'=>'sort'
//		));
//		
//		return $stmt->fetchAll();
//	}

}