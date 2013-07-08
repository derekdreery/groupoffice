<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * 
 * The Note controller
 * 
 */
class GO_Notes_Controller_Note extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Notes_Model_Note';
	
	protected function beforeStoreStatement(array &$response, array &$params, GO_Base_Data_AbstractStore &$store, GO_Base_Db_FindParams $storeParams) {
		
		$multiSel = new GO_Base_Component_MultiSelectGrid(
						'no-multiselect', 
						"GO_Notes_Model_Category",$store, $params, true);
		
		$multiSel->addSelectedToFindCriteria($storeParams, 'category_id');
		$multiSel->setButtonParams($response);
		$multiSel->setStoreTitle();
		
		return parent::beforeStoreStatement($response, $params, $store, $storeParams);
	}

	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('user_name','$model->user->name',array(),'user_id');
		return parent::formatColumns($columnModel);
	}

	protected function remoteComboFields(){
		return array('category_id'=>'$model->category->name');
	}
	
	
	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {
		 if(GO::modules()->files){
			 $f = new GO_Files_Controller_Folder();
			 $f->processAttachments($response, $model, $params);
		 }		
		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}
	
	protected function beforeSubmit(&$response, &$model, &$params) {
		if (!empty($params['encrypted'])) {
			if (!empty($params['userInputPassword1']) || !empty($params['userInputPassword2'])) {
				
				// User just entered a new password.
				
				if (empty($params['userInputPassword1']) || empty($params['userInputPassword2']))
					throw new GO_Base_Exception_BadPassword();
				if ($params['userInputPassword1']!=$params['userInputPassword2'])
					throw new Exception(GO::t('passwordMatchError'));
				$params['password'] = crypt($params['userInputPassword1']);
				$params['content'] = GO_Base_Util_Crypt::encrypt($params['content'],$params['userInputPassword1']);
				
			} else if (!empty($params['currentPassword'])) {
				
				// User just entered the previously set password.
				
				$params['password'] = crypt($params['currentPassword']);
				$params['content'] = GO_Base_Util_Crypt::encrypt($params['content'],$params['currentPassword']);
				
			} else {
				throw new Exception(GO::t('passwordSubmissionError'));
			}
		} else {
			$params['password'] = '';
		}
		return parent::beforeSubmit($response, $model, $params);
	}
	
	protected function beforeLoad(&$response, &$model, &$params) {
		if (isset($params['userInputPassword'])) {
			if (!$model->decrypt($params['userInputPassword'])) {
				throw new GO_Base_Exception_BadPassword();
			}
		}
	
		return parent::beforeLoad($response, $model, $params);
	}
	
	protected function afterLoad(&$response, &$model, &$params) {
		
		if ($model->encrypted)
			$response['data']['content'] = GO::t('contentEncrypted');
		
		$response['data']['encrypted']=$model->encrypted;
		
		return parent::afterLoad($response, $model, $params);
	}
	
	protected function beforeDisplay(&$response, &$model, &$params) {
		if (isset($params['userInputPassword'])) {
			if (!$model->decrypt($params['userInputPassword'])) {
				throw new GO_Base_Exception_BadPassword();
			}
		}
		
		return $response;
	}
	
	protected function afterDisplay(&$response, &$model, &$params) {
		if ($model->encrypted)
			$response['data']['content'] = GO::t('clickHereToDecrypt');
		
		$response['data']['encrypted']=$model->encrypted;
		
		return parent::afterDisplay($response, $model, $params);
	}
}

