<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Michael de Hart <mdhart@intermesh.nl>
 */

/**
 * 
 * The Category controller
 * 
 */
class GO_Notes_Controller_Category extends GO_Base_Controller_AbstractJsonController {

	protected function actionStore($params) {

		$columnModel = new \GO_Base_Data_ColumnModel(GO_Notes_Model_Note::model());
		$columnModel->formatColumn('user_name', '$model->user ? $model->user->name : 0');
		
		$store = new \GO_Base_Data_DbStore('GO_Notes_Model_Category', $columnModel, $params);
		$store->defaultSort = 'name';
		$store->multiSelectable('no-multiselect');

		echo $this->renderStore($store);
	}

	protected function actionLoad($params) {
		//Load or create model
		$model = GO_Notes_Model_Category::model()->createOrFindByParams($params);

		// return render response
		$remoteComboFields = array('user_id' => '$model->user->name');
		echo $this->renderForm($model, $remoteComboFields);
	}

	protected function actionSubmit($params) {
		$model = GO_Notes_Model_Category::model()->createOrFindByParams($params);

		$model->setAttributes($params);
		$model->save();

		echo $this->renderSubmit($model);
	}

}
