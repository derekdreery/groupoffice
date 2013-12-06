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
 * The note controller provides action for basic crud functionality for the note model
 */
class GO_Notes_Controller_Note extends GO_Base_Controller_AbstractJsonController {

	/**
	 * Load data for the display panel on the right of the screen
	 * @param $_REQUEST $params
	 */
	protected function actionSubmit($params) {

		$model = GO_Notes_Model_Note::model()->createOrFindByParams($params);

		if(isset($params['currentPassword'])){
			//if the note was encrypted and no new password was supplied the current
			//pasword is sent.
			$params['userInputPassword1']=$params['userInputPassword2']=$params['currentPassword'];
		}
		
		$model->setAttributes($params);

		if ($model->save()) {
			if (GO::modules()->files) {
				$f = new GO_Files_Controller_Folder();
				$response = array(); //never used in processAttachements?
				$f->processAttachments($response, $model, $params);
			}
		}

		echo $this->renderSubmit($model);
	}

	/**
	 * Action for fetchin a JSON array to be loaded into a ExtJS form
	 * @param array $params the $_REQUEST data
	 * @throws GO_Base_Exception_AccessDenied When no create or write permissions for the loaded model
	 * @throws Exception when the notes decriptiopn password is incorrect
	 */
	protected function actionLoad($params) {

		//Load or create model
		$model = GO_Notes_Model_Note::model()->createOrFindByParams($params);

		// BEFORE LOAD: a password is entered to decrypt the content
		if (isset($params['userInputPassword'])) {
			if (!$model->decrypt($params['userInputPassword']))
				throw new Exception(GO::t('badPassword'));
		}

		// Build remote combo field array
		$remoteComboFields = array('category_id' => '$model->category->name');

		//add extra fields to 'data' array of jsonresponse
		$extraFields = array('encrypted' => $model->encrypted);
		if ($model->encrypted)
			$extraFields['content'] = GO::t('contentEncrypted');

		echo $this->renderForm($model, $remoteComboFields, $extraFields);
	}

	/**
	 * Load a note model from the database and call the renderDisplay function to render the JSON
	 * output for a ExtJS Display Panel
	 * @param array $params the $_REQUEST object
	 * @throws GO_Base_Exception_NotFound when the note model cant be found in database
	 * @throws Exception When the encryption password provided is incorrect
	 */
	protected function actionDisplay($params) {

		$model = GO_Notes_Model_Note::model()->findByPk($params['id']);
		if (!$model)
			throw new GO_Base_Exception_NotFound();

		// decrypt model if password provided
		if (isset($params['userInputPassword'])) {
			if (!$model->decrypt($params['userInputPassword']))
				throw new Exception(GO::t('badPassword'));
		}
		$extraFields = array();
		if ($model->encrypted)
			$extraFields['content'] = GO::t('clickHereToDecrypt');
		$extraFields['encrypted'] = $model->encrypted;

		echo $this->renderDisplay($model, $extraFields);
	}

	/**
	 * Render JSON output that can be used by ExtJS GridPanel
	 * @param array $params the $_REQUEST params
	 */
	protected function actionStore($params) {
		//Create ColumnModel from model
		$columnModel = new GO_Base_Data_ColumnModel(GO_Notes_Model_Note::model());
		$columnModel->formatColumn('user_name', '$model->user->name', array(), 'user_id');

		//Create store
		$store = new GO_Base_Data_DbStore('GO_Notes_Model_Note', $columnModel, $params);
		$store->multiSelect('no-multiselect', 'GO_Notes_Model_Category', 'category_id');

		echo $this->renderStore($store);
	}

}
