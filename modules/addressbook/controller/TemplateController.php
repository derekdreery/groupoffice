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
class GO_Addressbook_Controller_Template extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Addressbook_Model_Template';	
	
	protected function remoteComboFields() {
		return array(
				'user_name'=>'$model->user->name'
				);
	}
	
	protected function getStoreParams($params) {
		if(isset($params['type'])){
			$findParams = GO_Base_Db_FindParams::newInstance();
			
			$findParams->getCriteria()->addCondition('type', $params['type']);
			return $findParams;
		}
		
		//return parent::getStoreParams($params);
	}
	
	protected function beforeStore(&$response, &$params, &$store) {
		$store->setDefaultSortOrder('name');
		return parent::beforeStore($response, $params, $store);
	}

	protected function beforeSubmit(&$response, &$model, &$params) {
		
		$message = new GO_Base_Mail_Message();
		$message->handleEmailFormInput($params);
		
		$model->content = $message->toString();
		
		return parent::beforeSubmit($response, $model, $params);
	}
	
	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {
		$message = GO_Email_Model_SavedMessage::model()->createFromMimeData($model->content);
		$response['htmlbody'] = $message->getHtmlBody();
		
		// reset the temp folder created by the core controller
//		$tmpFolder = new GO_Base_Fs_Folder(GO::config()->tmpdir . 'uploadqueue');
//		$tmpFolder->delete();
		
		parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}
	
	protected function afterLoad(&$response, &$model, &$params) {
		
		// create message model from client's content field, turned into HTML format
		$message = GO_Email_Model_SavedMessage::model()->createFromMimeData($model->content);
	
		$html = empty($params['content_type']) || $params['content_type']=='html';
		
		$response['data'] = array_merge($response['data'], $message->toOutputArray($html));
		unset($response['data']['content']);

		return parent::afterLoad($response, $model, $params);
	}
	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('user_name', '$model->user->name');
		return parent::formatColumns($columnModel);
	}
	
	private $_defaultTemplate;
	
	public function actionEmailSelection($params){	
		
		$this->_defaultTemplate = GO_Addressbook_Model_DefaultTemplate::model()->findByPk(GO::user()->id);
		if(!$this->_defaultTemplate){
			$this->_defaultTemplate= new GO_Addressbook_Model_DefaultTemplate();
			$this->_defaultTemplate->save();
		}
		
		if(isset($params['default_template_id']))
		{
			$this->_defaultTemplate->template_id=$params['default_template_id'];
			$this->_defaultTemplate->save();
		}
		
		$findParams = GO_Base_Db_FindParams::newInstance()->order('name');			
		$findParams->getCriteria()->addCondition('type', GO_Addressbook_Model_Template::TYPE_EMAIL);
				
		$stmt = GO_Addressbook_Model_Template::model()->find($findParams);
		
		$store = GO_Base_Data_Store::newInstance(GO_Addressbook_Model_Template::model());		
		$store->getColumnModel()->setFormatRecordFunction(array($this, 'formatEmailSelectionRecord'));
		
		$store->setStatement($stmt);
		$store->addRecord(array(
			'group' => 'templates',
			'checked'=>isset($this->_defaultTemplate->template_id) && $this->_defaultTemplate->template_id==0,
			'text' => GO::t('none'),
			'template_id'=>0
		));
		
		$response = $store->getData();
		
		if($response['total']>0){

			$response['results'][] = '-';

			$record = array(
				'text' => GO::t('setCurrentTemplateAsDefault','addressbook'),
				'template_id'=>'default'
			);

			$response['results'][] = $record;
		}
		
		return $response;
	}
	
	public function formatEmailSelectionRecord(array $formattedRecord, GO_Base_Db_ActiveRecord $model, GO_Base_Data_ColumnModel $cm){
		if(!isset($this->_defaultTemplate->template_id)){
			$this->_defaultTemplate->template_id=$model->id;
			$this->_defaultTemplate->save();
		}
		$formattedRecord['group'] = 'templates';
		$formattedRecord['checked']=$this->_defaultTemplate->template_id==$model->id;
		$formattedRecord['text']=$model->name;
		$formattedRecord['template_id']=$model->id;
		unset($formattedRecord['id']);
		return $formattedRecord;
	}
	
}

