<?php
class GO_Customfields_Controller_FieldTreeSelectOption extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Customfields_Model_FieldTreeSelectOption';
	

	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		
		$columnModel->formatColumn('name_with_id', '$model->id.":".$model->name');

	}
	
	protected function getStoreParams($params) {
		
		if(isset($params['node']))
			$parent_id=$params['node'];
		else
			$parent_id=$params['parent_id'];
		
		$field_id = str_replace('col_','',$params['field_id']);
		
		$fieldModel = GO_Customfields_Model_Field::model()->findByPk($field_id);
		
		if ($params['parent_id']==0 && $fieldModel->datatype=='GO_Customfields_Customfieldtype_TreeselectSlave') {
			return GO_Base_Db_FindParams::newInstance()
						->order(array("parent_id","sort"))
						->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('field_id', $fieldModel->treemaster_field_id));
		} else {
			return GO_Base_Db_FindParams::newInstance()
						->order("sort")
						->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('field_id', $field_id)->addCondition('parent_id', $parent_id));
		}
		
//		return array(
//				'where'=>'field_id=:field_id AND parent_id=:parent_id',
//				'bindParams'=>array(':field_id'=>$params['field_id'], ':parent_id'=>$parent_id),
//				'order'=>'sort'
//		);
	}
	
	
	protected function actionTree($params){
		
		$s = call_user_func(array($this->model,'model'));
		
		$stmt = $s->find(array(
				'where'=>'field_id=:field_id AND parent_id=:parent_id',
				'bindParams'=>array(
						'field_id'=>$params['field_id'], 
						'parent_id'=>$params['node']),
				'order'=>'sort'
		));
		
		$response=array();
		$models = $stmt->fetchAll();
		while($model = array_shift($models)){
			$node = array(
				'id'=>$model->id,
				'text'=>$model->name,
				'iconCls'=>'folder-default'
				);
			
			$record = $s->findSingle(array(
				'fields'=>'count(*) AS count',
				'where'=>'parent_id=:parent_id',
				'bindParams'=>array(
						'parent_id'=>$model->id),
				'order'=>'sort'
			));

			if(!$record->count){
				$node['children']=array();
				$node['expanded']=true;
			}
			$response[]=$node;
		}
		
		return $response;
	}
}


