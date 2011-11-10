<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 */
class GO_Addressbook_Controller_EmailTemplate extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Addressbook_Model_EmailTemplate';	
	
	protected function remoteComboFields() {
		return array(
				'user_name'=>'$model->user->name'
				);
	}

	protected function beforeSubmit(&$response, &$model, &$params) {
		
		$message = new GO_Base_Mail_Message();
		$message->handleEmailFormInput($params);
		
		$model->content = $message->toString();
		
		return parent::beforeSubmit($response, $model, $params);
	}
	
	
	protected function afterLoad(&$response, &$model, &$params) {
		
		// create message model from client's content field, turned into HTML format
		$message = GO_Email_Model_SavedMessage::model()->createFromMimeData($model->content);
		
		$response['data'] = array_merge($response['data'], $message->toOutputArray());
		
		return parent::afterLoad($response, $model, $params);
	}
	
}

