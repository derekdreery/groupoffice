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
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * 
 * The Category controller
 * 
 */
namespace GO\Notes\Controller;

use GO\Base\Controller;
use GO\Notes\Model\Category;

class CategoryController extends Controller\AbstractJsonController {

	protected function actionStore($params) {

		$columnModel = new \GO_Base_Data_ColumnModel("\GO\Notes\Model\Note");
		$columnModel->formatColumn('user_name', '$model->user ? $model->user->name : 0');
		
		$store = new \GO_Base_Data_DbStore('\GO\Notes\Model\Category', $columnModel, $params);
		$store->defaultSort = 'name';
		$store->multiSelectable('no-multiselect');

		echo $this->renderStore($store);
	}

	protected function actionLoad($params) {
		//Load or create model
		$model = Category::model()->createOrFindByParams($params);

		// return render response
		$remoteComboFields = array('user_id' => '$model->user->name');
		echo $this->renderForm($model, $remoteComboFields);
	}

	protected function actionSubmit($params) {
		$model = Category::model()->createOrFindByParams($params);

		$model->setAttributes($params);
		$model->save();

		echo $this->renderSubmit($model);
	}

}
