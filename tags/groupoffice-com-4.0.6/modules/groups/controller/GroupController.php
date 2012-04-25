<?php

class GO_Groups_Controller_Group extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Base_Model_Group';

//  /**
//   * Add the username field to this default grid.
//   * 
//   * @param GO_Base_Data_Store $store
//   * @return GO_Base_Data_Store
//   */
//  protected function prepareStore(GO_Base_Data_Store $store){
//    $store->formatColumn('user_name','$model->user->name');
//    return $store;
//  }

	protected function allowWithoutModuleAccess() {
		return array('getusers');
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
		$store->getColumnModel()->formatColumn('name', '$model->name');

		$storeParams = $store->getDefaultParams($params)->joinCustomFields(false);


		$delresponse = array();
		//manually check permission here because this method may be accessed by any logged in user. allowWithoutModuleAccess is used above.
		if (GO::modules()->groups->checkPermissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION)) {

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
//
//	/**
//	 * Add the given user to the given group.
//	 * 
//	 * @param int $group_id
//	 * @param int $user_id
//	 * @return bool $success 
//	 */
//	protected function actionAddUserToGroup($group_id, $user_id) {
//		$group = new GO_Base_Model_Group();
//		$group->addUser($user_id);
//		return $userGroup->save();
//	}

	/**
	 * Update the params of a given Group
	 * 
	 * @param int $id
	 * @param String $name
	 * @param bool $admin_only
	 * @return bool $success 
	 */
	protected function actionUpdateGroup($id, $name, $admin_only=-1) {
		$group = $this->model->findByPk($id);

		$group->id = $id;
		$group->name = $name;
		$group->admin_only = $admin_only;

		return $group->save();
	}

	/**
	 *  Create a new group
	 * 
	 * @param int $user_id
	 * @param String $name
	 * @param bool $admin_only
	 * @return bool $success 
	 */
	protected function actionSaveGroup() {
		$group = new GO_Base_Model_Group();
		$group->setAttributes($_POST);
		$group->user_id = GO::user()->id;

		return $group->save();
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
	
}
