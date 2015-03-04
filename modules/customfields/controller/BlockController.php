<?php
class GO_Customfields_Controller_Block extends GO_Base_Controller_AbstractJsonController{

	protected function actionManageStore($params) {
		
		$columnModel = new GO_Base_Data_ColumnModel(GO_Customfields_Model_Block::model());
		$columnModel->formatColumn('col_id', '"col_".$model->customField->id', array(), 'field_id');
		$columnModel->formatColumn('customfield_name', '$model->customField->name', array(), 'field_id');
		$columnModel->formatColumn('customfield_datatype', '$model->customField->datatype', array(), 'field_id');
		$columnModel->formatColumn('extends_model', '$model->customField->category->extends_model', array(), 'field_id');

		$findParams = GO_Base_Db_FindParams::newInstance()
			->joinModel(array(
				'model'=>'GO_Customfields_Model_Field',
				'localTableAlias'=>'t',
				'localField'=>'field_id',
				'foreignField'=>'id',
				'tableAlias'=>'cf',
				'type'=>'INNER'
			))
			->joinModel(array(
				'model'=>'GO_Customfields_Model_Category',
				'localTableAlias'=>'cf',
				'localField'=>'category_id',
				'foreignField'=>'id',
				'tableAlias'=>'cfcat',
				'type'=>'INNER'
			))
			->join(
				'go_acl',
				GO_Base_Db_FindCriteria::newInstance()->addRawCondition('a.acl_id', 'cfcat.acl_id'),
				'a'
				)
			->join(
				'go_users_groups',
				GO_Base_Db_FindCriteria::newInstance()->addRawCondition('a.group_id', 'ug.group_id'),
				'ug',
				'LEFT'
			)
			->criteria(
				GO_Base_Db_FindCriteria::newInstance()
					->addCondition('user_id', GO::user()->id, '=', 'a', false)
					->addCondition('user_id', GO::user()->id, '=', 'ug', false)
			);
		
		$store = new GO_Base_Data_DbStore('GO_Customfields_Model_Block', $columnModel, $params, $findParams);

		echo $this->renderStore($store);
		
	}
	
	protected function actionSubmit($params) {
		
		if (!empty($params['id']))
			$blockModel = GO_Customfields_Model_Block::model()->findByPk($params['id']);
		else
			$blockModel = new GO_Customfields_Model_Block();
		
		$blockModel->setAttributes($params);
		$blockModel->save();
		
		echo $this->renderSubmit($blockModel);
		
	}
	
	protected function actionLoad($params) {
		
		if (!empty($params['id']))
			$blockModel = GO_Customfields_Model_Block::model()->findByPk($params['id']);
		else
			$blockModel = new GO_Customfields_Model_Block();
		
		$remoteComboFields = array('field_id' => '"[".GO::t($model->customField->category->extends_model,"customfields")."] ".$model->customField->category->name." : ".$model->customField->name." (col_".$model->id.")"');
		
		echo $this->renderForm($blockModel,$remoteComboFields);
		
	}

	protected function actionEnableStore($params) {
				
		$columnModel = new GO_Base_Data_ColumnModel(GO_Customfields_Model_Block::model());
		$columnModel->formatColumn('col_id', '"col_".$model->customField->id', array(), 'field_id');
		$columnModel->formatColumn('customfield_name', '$model->customField->name', array(), 'field_id');
		$columnModel->formatColumn('customfield_datatype', '$model->customField->datatype', array(), 'field_id');
		$columnModel->formatColumn('extends_model', '$model->customField->category->extends_model', array(), 'field_id');
		$columnModel->formatColumn('enabled', '!empty($model->enabled_block_id)', array(), 'enabled_block_id');

		$findParams = GO_Base_Db_FindParams::newInstance()
			->select('t.*,eb.block_id AS enabled_block_id')
			->joinModel(array(
				'model'=>'GO_Customfields_Model_EnabledBlock',
				'localTableAlias'=>'t',
				'localField'=>'id',
				'foreignField'=>'block_id',
				'tableAlias'=>'eb',
				'type'=>'LEFT',
				'criteria'=>GO_Base_Db_FindCriteria::newInstance()
					->addCondition('model_type_name',$params['model_name'],'=','eb')
					->addCondition('model_id',$params['model_id'],'=','eb')
			))
			->joinModel(array(
				'model'=>'GO_Customfields_Model_Field',
				'localTableAlias'=>'t',
				'localField'=>'field_id',
				'foreignField'=>'id',
				'tableAlias'=>'cf',
				'type'=>'INNER'
			))
			->joinModel(array(
				'model'=>'GO_Customfields_Model_Category',
				'localTableAlias'=>'cf',
				'localField'=>'category_id',
				'foreignField'=>'id',
				'tableAlias'=>'cfcat',
				'type'=>'INNER'
			))
			->join(
				'go_acl',
				GO_Base_Db_FindCriteria::newInstance()->addRawCondition('a.acl_id', 'cfcat.acl_id'),
				'a'
				)
			->join(
				'go_users_groups',
				GO_Base_Db_FindCriteria::newInstance()->addRawCondition('a.group_id', 'ug.group_id'),
				'ug',
				'LEFT'
			)
			->criteria(
				GO_Base_Db_FindCriteria::newInstance()
					->addCondition('user_id', GO::user()->id, '=', 'a', false)
					->addCondition('user_id', GO::user()->id, '=', 'ug', false)
			);
		
		$store = new GO_Base_Data_DbStore('GO_Customfields_Model_Block', $columnModel, $params, $findParams);

		echo $this->renderStore($store);
		
	}
	
	protected function actionEnable($params) {
		
		$response['success'] = true;
		
		$enableBlockModel = GO_Customfields_Model_EnabledBlock::model()
			->findSingleByAttributes(array(
				'block_id' => $params['block_id'],
				'model_id' => $params['model_id'],
				'model_type_name' => $params['model_name']
			));
		
		if (!empty($params['enable']) && $params['enable']!=='false') {
			
			if (!$enableBlockModel) {
				$enableBlockModel = new GO_Customfields_Model_EnabledBlock();
				$enableBlockModel->block_id = $params['block_id'];
				$enableBlockModel->model_id = $params['model_id'];
				$enableBlockModel->model_type_name = $params['model_name'];
				$response['success'] = $enableBlockModel->save();
			}
			
		} else {
			
			if ($enableBlockModel)
				$response['success'] = $enableBlockModel->delete();
		}
		
		echo $this->renderJson($response);
		
	}
	
}