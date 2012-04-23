<?php

class GO_Users_Controller_User extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Base_Model_User';

	protected function ignoreAclPermissions() {
		//ignore acl on submit so normal users can use the users module. 
		//otherwise they are not allowed to save users.
		return array('*');
	}
	
	protected function afterDisplay(&$response, &$model, &$params) {
		
		$contact = $model->createContact();
		
		$response['data']['contact_id']=$contact->id;
		
		return parent::afterDisplay($response, $model, $params);
	}

	protected function remoteComboFields() {
		if(GO::modules()->isInstalled('addressbook')){
			return array(
					'addressbook_id' => '$model->contact->addressbook->name',
					'company_id' => '$model->contact->company->name'
					);
		}
	}

	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('name', '$model->name', array(), 'first_name');
		return parent::formatColumns($columnModel);
	}

	protected function afterLoad(&$response, &$model, &$params) {

		//Join the contact that belongs to the user in the form response.
		if(GO::modules()->isInstalled('addressbook')){
			$contact=false;
			if(!empty($model->id)){
				$contact = $model->contact;
			}elseif(!empty($params['contact_id'])){
				$contact = GO_Addressbook_Model_Contact::model()->findByPk($params['contact_id']);
				$response['data']['contact_id']=$contact->id;
			}
			if(!$contact)
			{
				$contact = new GO_Addressbook_Model_Contact();
			}
			if ($contact) {
				$attr = $contact->getAttributes();

				$response['data'] = array_merge($attr, $response['data']);
			}
			unset($response['data']['password']);
		}
		

		return parent::afterLoad($response, $model, $params);
	}

	private function _getRegisterEmail() {
		$r = array(
				'register_email_subject' => GO::config()->get_setting('register_email_subject'),
				'register_email_body' => GO::config()->get_setting('register_email_body')
		);

		if (!$r['register_email_subject']) {
			$r['register_email_subject'] = GO::t('register_email_subject', 'users');
		}
		if (!$r['register_email_body']) {
			$r['register_email_body'] = GO::t('register_email_body', 'users');
		}
		return $r;
	}

	protected function beforeSubmit(&$response, &$model, &$params) {

		if(empty($params['password'])){
			unset($params['password']);
		}
		
		return parent::beforeSubmit($response, $model, $params);
	}

	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {

		//Save the contact fields to the contact.
		
		$contact = $model->createContact();
		if($contact){
			unset($params['addressbook_id'], $params['id']);
			$contact->setAttributes($params);
			$contact->save();
		}


		if (isset($_POST['modules'])) {
			$modules = !empty($_POST['modules']) ? json_decode($_POST['modules']) : array();
			$groupsMember = json_decode($_POST['group_member'], true);
			$groupsVisible = json_decode($_POST['groups_visible'], true);

			/**
			 * Process selected module permissions
			 */
			foreach ($modules as $modPermissions) {
				$modModel = GO_Base_Model_Module::model()->findByPk(
					$modPermissions->id
				);	
				$modModel->acl->addUser(
					$model->id,
					$modPermissions->permissionLevel
				);
			}

			/**
			 * User will be member of the selected groups
			 */
			foreach ($groupsMember as $group) {
				if ($group['id'] != GO::config()->group_everyone) {
					if ($group['group_permission']) {
						GO_Base_Model_Group::model()->findByPk($group['id'])->addUser($model->id);
					} else {
						GO_Base_Model_Group::model()->findByPk($group['id'])->removeUser($model->id);
					}
				}
			}


			/**
			 * User will be visible to the selected groups
			 */
			foreach ($groupsVisible as $group) {
				if ($group['visible_permission']) {
					
					$model->acl->addGroup($group['id'], GO_Base_Model_Acl::MANAGE_PERMISSION);
				} else {
					$model->acl->removeGroup($group['id']);
				}
			}
		}

		$model->checkDefaultModels();

		if (!empty($params['send_invitation'])) {

			$email = $this->_getRegisterEmail();

			if (!empty($email['register_email_body']) && !empty($email['register_email_subject'])) {
				
				$email['register_email_body'] = str_replace('{password}', $params["password"], $email['register_email_body']);

				foreach ($model->getAttributes() as $key => $value) {
					if(is_string($value))
						$email['register_email_body'] = str_replace('{' . $key . '}', $value, $email['register_email_body']);
				}

				$email['register_email_body'] = str_replace('{url}', GO::config()->full_url, $email['register_email_body']);
				$email['register_email_body'] = str_replace('{title}', GO::config()->title, $email['register_email_body']);
				
				$message = new GO_Base_Mail_Message();
				$message->setSubject($email['register_email_subject'])
								->setTo(array($model->email=>$model->name))
								->setFrom(array(GO::config()->webmaster_email=>GO::config()->title))
								->setBody($email['register_email_body']);								

				GO_Base_Mail_Mailer::newGoInstance()->send($message);
				
			}
		}
	}

	protected function actionSyncContacts($params) {
		
		GO::$ignoreAclPermissions=true; //allow this script access to all
		GO::$disableModelCache=true; //for less memory usage
		ini_set('max_execution_time', '300');

		$ab = GO_Addressbook_Model_Addressbook::model()->findSingleByAttribute('users', '1'); //GO::t('users','base'));
		if (!$ab) {
			$ab = new GO_Addressbook_Model_Addressbook();
			$ab->name = GO::t('users');
			$ab->users = true;
			$ab->save();
		}

		//$pdo = GO::getDbConnection();

		$stmt = GO_Base_Model_User::model()->find();
		while ($user = $stmt->fetch()) {

			$contact = $user->contact();
			if (!$contact) {
				
				GO::output("Creating contact for ".$user->username);
				
				$contact = new GO_Addressbook_Model_Contact();
				$contact->go_user_id = $user->id;
				$contact->addressbook_id = $ab->id;
			}else
			{
				GO::output("Updating contact for ".$user->username);
			}
			$attr = $user->getAttributes();
			unset($attr['id']);

			$contact->setAttributes($attr);
			$contact->save();
		}
		
		GO::output("Done!");

		//return array('success' => true);
	}
	
	protected function getStoreParams($params) {
		
		$findParams =  GO_Base_Db_FindParams::newInstance();
		
		if(!empty($params['show_licensed'])){		
		
			if(class_exists("GO_Professional_LicenseCheck")){
				$lc = new GO_Professional_LicenseCheck();

				$proModuleAcls=array();
				$proModules = $lc->getProModules();
				foreach($proModules as $module)
					$proModuleAcls[]=$module->acl_id;
				
				$aclJoinCriteria= GO_Base_Db_FindCriteria::newInstance()
								->addRawCondition('a.user_id', 't.id')
								->addRawCondition('a.group_id', 'ug.group_id','=',false);

				$findParams
					->ignoreAcl()
					->joinModel(array(
						'model'=>'GO_Base_Model_UserGroup',
						'foreignField'=>'user_id',
						'tableAlias'=>'ug'				
					))
					->join(GO_Base_Model_AclUsersGroups::model()->tableName(), $aclJoinCriteria,'a')
					->group('t.id');

				$findParams->getCriteria()->addInCondition('acl_id', $proModuleAcls,'a');

			}		
		}
		
		return $findParams;
		
	}
	
	protected function afterStore(&$response, &$params, &$store, $storeParams) {
		if(class_exists("GO_Professional_LicenseCheck")){
			$lc = new GO_Professional_LicenseCheck();
			try{
				$lc->checkProModules(true);
			}catch(Exception $e){
				$response['feedback']=$e->getMessage();
			}
		}
		
		return parent::afterStore($response, $params, $store, $storeParams);
	}
	
}