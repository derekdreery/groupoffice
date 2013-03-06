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
class GO_Notes_Controller_Category extends GO_Base_Controller_AbstractJsonController{
	
  protected function actionStore($params) {

	$columnModel = new GO_Base_Data_ColumnModel(GO_Notes_Model_Note::model());
	$columnModel->formatColumn('user_name','$model->user ? $model->user->name : 0');
	/*
	 * Example of joining the model directly
	$columnModel->formatColumn('user_name','GO_Base_Util_String::format_name($model->last_name, $model->first_name, $mode->middle_name)');
	$findParams = GO_Base_Db_FindParams::newInstance()->joinModel(array(
		'model'=>'GO_Base_Model_User',					
		'localField'=>'user_id',
		'tableAlias'=>'u',
	))->select('t.id, t.name, u.first_name, u.middle_name, u.last_name');
	*/		
	$store = new GO_Base_Data_DbStore('GO_Notes_Model_Category', $columnModel, $params);
	$store->defaultSort = 'name';
	$store->multiSelectable('no-multiselect');

	$this->renderStore($store);
  }

  protected function actionLoad($params) {
	//Load or create model
	$model = GO_Notes_Model_Category::model()->createOrFindByParams($params);

	// return render response
	$remoteComboFields = array('user_id'=>'$model->user->name');
	$this->renderForm($model, $remoteComboFields);

  }
  
  protected function actionSubmit($params) {
	$model = GO_Notes_Model_Category::model()->createOrFindByParams($params);

	$model->setAttributes($params);
	$model->save();

	$this->renderSubmit($model);
  }

}

