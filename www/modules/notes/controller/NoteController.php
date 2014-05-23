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
 * The note controller provides action for basic crud functionality for the note model
 */

namespace GO\Notes\Controller;

use Exception;
use GO;
use GO\Base\View\JsonView;
use GO\Notes\Model\Note;
use GO\Professional\Controller\AbstractController;

class NoteController extends AbstractController{
	
	protected function init() {
		
		$this->view = new JsonView();
		parent::init();
	}

	/**
	 * Updates a note POST for save and GET for retrieve
	 * 
	 * @param $id Note ID
	 */
	protected function actionUpdate($id) {

		$model = Note::model()->findByPk($id);
		
		if (!$model)
			throw new \GO\Base\Exception\NotFound();
		
		if(GO::request()->isPost()){
			$note = GO::request()->post['note'];

			if(isset($note['currentPassword'])){
				//if the note was encrypted and no new password was supplied the current
				//pasword is sent.
				$note['userInputPassword1']=$note['userInputPassword2']=$note['currentPassword'];
			}

			$model->setAttributes($note);

			echo $this->render('submit', array('note'=>$model));
		}else
		{
			// BEFORE LOAD: a password is entered to decrypt the content
			if (isset($note['userInputPassword'])) {
				if (!$model->decrypt($note['userInputPassword']))
					throw new Exception(GO::t('badPassword'));
			}
		
			echo $this->render(
							'form',
							array(
									'note'=>$model, 
									'remoteComboFields' => array(
											'category_id' => '$model->category->name'
											)
									)
							);
		}		
	}
	
	
	/**
	 * Creates a note
	 */
	protected function actionCreate() {

		$model = new Note();
		
		if(GO::request()->isPost()){
			$note = GO::request()->post['note'];

			if(isset($note['currentPassword'])){
				//if the note was encrypted and no new password was supplied the current
				//pasword is sent.
				$note['userInputPassword1']=$note['userInputPassword2']=$note['currentPassword'];
			}

			$model->setAttributes($note);

			if ($model->save()) {
				if (GO::modules()->files) {
					$f = new \GO\Files\Controller\FolderController();
					$response = array(); //never used in processAttachements?
					$f->processAttachments($response, $model, $note);
				}
			}
			
			echo $this->render('submit',array('note'=>$model));
		}else
		{

			echo $this->render(
							'form',
							array(
									'note'=>$model,								
									'remoteComboFields' => array(
											'category_id' => '$model->category->name'
											)
									)
							);
		}		
	}

	

	/**
	 * Load a note model from the database and call the renderDisplay function to render the JSON
	 * output for a ExtJS Display Panel
	 * @param array $params the $_REQUEST object
	 * @throws \GO\Base\Exception\NotFound when the note model cant be found in database
	 * @throws Exception When the encryption password provided is incorrect
	 */
	protected function actionDisplay($id, $userInputPassword=null) {

		$model = Note::model()->findByPk($id);
		if (!$model)
			throw new \GO\Base\Exception\NotFound();
		
		
		// decrypt model if password provided
		if (isset($userInputPassword)) {
			if (!$model->decrypt($userInputPassword))
				throw new Exception(GO::t('badPassword'));
		}	

		$response =  $this->render('display',array('model'=>$model));
		
		if ($model->encrypted)
			$response->data['data']['content'] = GO::t('clickHereToDecrypt');
		
		$response->data['data']['encrypted'] = $model->encrypted;
		
		echo $response;
	}

	/**
	 * Render JSON output that can be used by ExtJS GridPanel
	 * @param array $params the $_REQUEST params
	 */
	protected function actionStore() {
		//Create ColumnModel from model
		$columnModel = new \GO\Base\Data\ColumnModel(Note::model());
		$columnModel->formatColumn('user_name', '$model->user->name', array(), 'user_id');

		//Create store
		$store = new \GO\Base\Data\DbStore('GO\Notes\Model\Note', $columnModel);
		$store->multiSelect('no-multiselect', 'GO\Notes\Model\Category', 'category_id');

		echo $this->render('store',array('store'=>$store));
	}

}
