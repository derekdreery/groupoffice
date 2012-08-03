<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * The GO_Tasks_Controller_Category controller
 *
 * @package GO.modules.Tasks
 * @version $Id: GO_Tasks_Controller_Category.php 7607 2011-09-20 10:07:50Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits wsmits@intermesh.nl
 */

class GO_Tasks_Controller_Category extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Tasks_Model_Category';

	
	protected function beforeSubmit(&$response, &$model, &$params) {
		// Checkbox "Use Global" is checked
		if(isset($params['global']))
			$model->user_id = 0;
		else
			$model->user_id = GO::user ()->id;
		
		return parent::beforeSubmit($response, $model, $params);
	}
	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('user_name','$model->user ? $model->user->name : GO::t("globalCategory","tasks")');
		return parent::formatColumns($columnModel);
	}
	
	protected function beforeStoreStatement(array &$response, array &$params, GO_Base_Data_AbstractStore &$store,  GO_Base_Db_FindParams $storeParams) {
	
		$storeParams->criteria(
			GO_Base_Db_FindCriteria::newInstance()
						->addCondition('user_id', GO::user()->id)
						->addCondition('user_id', 0, '=', 't', false)
		);
		
		return parent::beforeStoreStatement($response, $params, $store, $storeParams);
	}
	
	
}

