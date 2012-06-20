<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

class GO_Summary_Controller_Note extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Summary_Model_Note';

	protected function beforeLoad(&$response, &$model, &$params) {
		$model = GO_Summary_Model_Note::model()->findByPk(GO::user()->id);
		return false;
	}
	
	protected function beforeSubmit(&$response, &$model, &$params) {
		$model = GO_Summary_Model_Note::model()->findByPk(GO::user()->id);
		if (empty($model)) {
			$model = new GO_Summary_Model_Note();
			$model->setIsNew(true);
		} else {
			$model->setIsNew(false);
		}
		$model->user_id = $params['user_id'] = GO::user()->id;
	}
	
}

