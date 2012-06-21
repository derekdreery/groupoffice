<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

class GO_Summary_Controller_Announcement extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Summary_Model_Announcement';

	protected function beforeSubmit(&$response, &$model, &$params) {
		$model->due_time = $params['due_time'] = GO_Base_Util_Date::to_unixtime($params['due_time']);
	}
	
	protected function beforeLoad(&$response, &$model, &$params) {
		$model->due_time = $response['due_time'] = GO_Base_Util_Date::format($model->due_time);
		return $response;
	}

	public function formatStoreRecord($record, $model, $store) {
		$record['due_time'] = date($_SESSION['GO_SESSION']['date_format'],$record['due_time']);
		$record['content'] = htmlspecialchars_decode($record['content']);
		return parent::formatStoreRecord($record, $model, $store);
	}
	
	protected function getStoreParams($params) {
		if (!empty($params['active']))
			return GO_Base_Db_FindParams::newInstance()
				->select('t.*')
				->criteria(
					GO_Base_Db_FindCriteria::newInstance()
						->addCondition('due_time', 0, '=', 't', false)
						->addCondition('due_time', mktime(0,0,0), '>=', 't', false)
				);
		else
			return GO_Base_Db_FindParams::newInstance()->select('t.*');
	}
}

