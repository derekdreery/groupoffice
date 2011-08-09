<?php

class GO_Users_Controller_User extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Base_Model_User';

	protected function prepareGrid($grid) {
		$grid->formatColumn('name', '$model->name');
		return $grid;
	}

	protected function afterSubmit(&$response, $user) {
		if (isset($_POST['modules'])) {
			$modules = json_decode($_POST['modules'], true);
			$groupsMember = json_decode($_POST['group_member'], true);
			$groupsVisible = json_decode($_POST['groups_visible'], true);

			foreach ($modules as $module) {
				
				$mod = GO_Base_Model_Module::model()->findByPk($module['id']);
				
	
				$level = 0;
				if ($module['write_permission']) {
					$level = GO_SECURITY::WRITE_PERMISSION;
				} elseif ($module['read_permission']) {
					$level = GO_SECURITY::READ_PERMISSION;
				}

				if ($level) {
					$mod->acl->addUser($user->id, $level);
				} else {
					$mod->acl->removeUser($user->id);					
				}
			}

			foreach ($groupsMember as $group) {
				if ($group['id'] != GO::config()->group_everyone) {
					if ($group['group_permission']) {						
						GO_Base_Model_Group::model()->findByPk($group['id'])->addUser($user->id);
					} else {
						GO_Base_Model_Group::model()->findByPk($group['id'])->removeUser($user->id);
					}
				}
			}

			foreach ($groupsVisible as $group) {				
				if ($group['visible_permission']){
					$user->acl->addUser($user->id);
				} else {
					$user->acl->removeUser($user->id);					
				}
			}
		}
	}

}