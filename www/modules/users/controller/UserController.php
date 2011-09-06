<?php

class GO_Users_Controller_User extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Base_Model_User';

	protected function remoteComboFields() {
		return array('addressbook_id' => '$model->contact->addressbook->name');
	}

	//GRID
	protected function prepareGrid($grid) {
		$grid->formatColumn('name', '$model->name', array(), 'first_name');
		return $grid;
	}

	//LOAD	

	protected function afterLoad(&$response, &$model, &$params) {


		//Join the contact that belongs to the user in the form response.
		if (GO::modules()->addressbook) {
			$contact = $model->contact();
			if ($contact) {
				$attr = $contact->getAttributes();

				$response['data'] = array_merge($attr, $response['data']);
			}
		}

		return parent::afterLoad($response, $model, $params);
	}

	//START OF SUBMIT

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

		if (!empty($params["password1"]) || !empty($params["password2"])) {
			if ($params["password1"] != $params["password2"]) {
				throw new Exception(GO::t('error_match_pass', 'users'));
			}
			if (!empty($params["password2"])) {
				$model->setAttribute('password', $_POST['password2']);
			}
		}

		return parent::beforeSubmit($response, $model, $params);
	}

	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {



		//Save the contact fields to the contact.
		if (GO::modules()->addressbook) {
			$contact = $model->contact();
			if (!$contact) {
				$contact = new GO_Addressbook_Model_Contact();
				$addressbook = GO_Addressbook_Model_Addressbook::model()->findSingleByAttribute('users', 1);
				$contact->go_user_id = $model->id;
				$contact->addressbook_id = $addressbook->id;
			}

			unset($params['addressbook_id'], $params['id']);
			$contact->setAttributes($params);
			$contact->save();
		}



		if (isset($_POST['modules'])) {
			$modules = json_decode($_POST['modules'], true);
			$groupsMember = json_decode($_POST['group_member'], true);
			$groupsVisible = json_decode($_POST['groups_visible'], true);

			/**
			 * Process selected module permissions
			 */
			foreach ($modules as $module) {

				$mod = GO_Base_Model_Module::model()->findByPk($module['id']);


				$level = 0;
				if ($module['write_permission']) {
					$level = GO_SECURITY::WRITE_PERMISSION;
				} elseif ($module['read_permission']) {
					$level = GO_SECURITY::READ_PERMISSION;
				}

				if ($level) {
					$mod->acl->addUser($model->id, $level);
				} else {
					$mod->acl->removeUser($model->id);
				}
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
					$model->acl->addGroup($group['id']);
				} else {
					$model->acl->removeGroup($group['id']);
				}
			}
		}



		if (!empty($_POST['send_invitation'])) {

			$email = $this->_getRegisterEmail();

			if (!empty($email['register_email_body']) && !empty($email['register_email_subject'])) {
				$swift = new GO_Base_Mail_Swift($model->email, $email['register_email_subject']);

				$email['register_email_body'] = str_replace('{password}', $params["password1"], $email['register_email_body']);

				foreach ($model->getAttributes() as $key => $value) {
					$email['register_email_body'] = str_replace('{' . $key . '}', $value, $email['register_email_body']);
				}

				$email['register_email_body'] = str_replace('{url}', GO::config()->full_url, $email['register_email_body']);
				$email['register_email_body'] = str_replace('{title}', GO::config()->title, $email['register_email_body']);


				$swift->set_body($email['register_email_body'], 'plain');
				$swift->set_from(GO::config()->webmaster_email, GO::config()->title);
				$swift->sendmail();
			}
		}
	}

	public function actionSyncContacts($params) {
		
		GO::setOutputStream(new GO_Base_OutputStream_OutputStreamLog());
		
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

		$pdo = GO::getDbConnection();

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

}