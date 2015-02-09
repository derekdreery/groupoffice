<?php

class GO_Groups_Controller_Group extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Base_Model_Group';

	protected function allowWithoutModuleAccess() {
		return array('getusers', 'getrecipientsasstring');
	}
	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('user_name', '$model->user->name');
		return parent::formatColumns($columnModel);
	}

	/**
	 * Retreive all users that belong to the given group.
	 * 
	 * @param int $id
	 * @return array Users
	 */
	protected function actionGetUsers($params) {
		//don't check ACL here because this method may be called by anyone.
		$group = GO_Base_Model_Group::model()->findByPk($params['id'], false, true);

		if (empty($group))
			$group = new GO_Base_Model_Group();

		if (isset($params['add_users']) && !empty($group->id)) {
			$users = json_decode($params['add_users']);
			foreach ($users as $usr_id) {
				if ($group->addUser($usr_id))
					GO_Base_Model_User::model()->findByPk($usr_id)->checkDefaultModels();
			}
		}

		$store = GO_Base_Data_Store::newInstance(GO_Base_Model_User::model());
		$store->getColumnModel()->formatColumn('name', '$model->name', array(), array('first_name', 'last_name'));

		$storeParams = $store->getDefaultParams($params)->joinCustomFields(false);


		$delresponse = array();
		//manually check permission here because this method may be accessed by any logged in user. allowWithoutModuleAccess is used above.
		if ($group->checkPermissionLevel(GO_Base_Model_Acl::DELETE_PERMISSION)) {

			// The users in the group "everyone" cannot be deleted
			if ($group->id != GO::config()->group_everyone) {
				$store->processDeleteActions($params, 'GO_Base_Model_UserGroup', array('group_id' => $group->id));
			} else {
				$delresponse['deleteSuccess'] = false;
				$delresponse['deleteFeedback'] = 'Members of the group everyone cannot be deleted.';
			}			
			
		}

		$stmt = $group->users($storeParams);
		$store->setStatement($stmt);

		$response = $store->getData();

		$response = array_merge($response, $delresponse);

		return $response;
	}


	protected function beforeSubmit(&$response, &$model, &$params) {
		if (!empty($params['permissions'])) {
			$permArr = json_decode($params['permissions']);
			foreach ($permArr as $modPermissions) {
				$modModel = GO_Base_Model_Module::model()->findByPk($modPermissions->id);	
				$modModel->acl->addGroup(
						$params['id'],
						$modPermissions->permissionLevel
					);
				
			}
		}
		return parent::beforeSubmit($response, $model, $params);
	}
	
	protected function actionGetRecipientsAsString($params){
				
		if(empty($params['groups']))
			throw new Exception();
			
		$recipients = new GO_Base_Mail_EmailRecipients();
		
		$groupIds = json_decode($params['groups']);
				
		foreach($groupIds as $groupId){
			
			//ignore acl because members may use groups even without permissions
			$group = GO_Base_Model_Group::model()->findByPk($groupId, false, true);


			if($group){
				$users = $group->users(GO_Base_Db_FindParams::newInstance()->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('email', '','!=')));
				while($user = $users->fetch())				
					$recipients->addRecipient($user->email, $user->name);
			}	
		}
		
		return array(
				'success'=>true,
				'recipients'=>(string) $recipients
		);
	}
	
}
