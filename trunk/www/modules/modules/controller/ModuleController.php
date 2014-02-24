<?php

namespace GO\Modules\Controller;


class ModuleController extends \GO\Base\Controller\AbstractModelController{
	
	protected $model = 'GO\Base\Model\Module';
	
	
	protected function allowWithoutModuleAccess() {
		return array('permissionsstore');
	}
	
	protected function ignoreAclPermissions() {		
		return array('*');
	}
		
	protected function prepareStore(\GO\Base\Data\Store $store){		
			
		$store->getColumnModel()->setFormatRecordFunction(array($this, 'formatRecord'));
		$store->setDefaultSortOrder('sort_order');
    return parent::prepareStore($store);
	}
	
	protected function getStoreParams($params) {
		$findParams = \GO\Base\Db\FindParams::newInstance()
						->ignoreAcl()
						->limit(0);
		
		if(!empty(\GO::config()->allowed_modules))
			$findParams->getCriteria ()->addInCondition ('id', explode(',',\GO::config()->allowed_modules));
		
		return $findParams;
		
	}
	
	public function formatRecord($record, $model, $store){

		//if($model->moduleManager){
		$record['description'] = $model->moduleManager->description();
		$record['name'] = $model->moduleManager->name();
		$record['author'] = $model->moduleManager->author();
		$record['icon'] = $model->moduleManager->icon();

		
		//$record['user_count']=$model->acl->countUsers();
		
		return $record;
	}
	
	
	protected function actionAvailableModulesStore($params){
		
		$response['results']=array();
		
		$modules = \GO::modules()->getAvailableModules();
		
		$availableModules=array();
						
		foreach($modules as $moduleClass){		
			
			$module = new $moduleClass;//call_user_func($moduleClase();			
			$availableModules[$module->name()] = array(
					'id'=>$module->id(),
					'name'=>$module->name(),
					'description'=>$module->description(),
					'icon'=>$module->icon()
			);
		}
		
		ksort($availableModules);		
		
		$response['results']=array_values($availableModules);
		
		$response['total']=count($response['results']);
		
		return $response;
	}
	
	
	protected function actionInstall($params){
		
		$response = array('success'=>true,'results'=>array());
		$modules = json_decode($params['modules'], true);
		foreach($modules as $moduleId)
		{
			$module = new \GO\Base\Model\Module();
			$module->id=$moduleId;
			
			
			$module->moduleManager->checkDependenciesForInstallation($modules);	
			
			if(!$module->save())
				throw new \GO\Base\Exception\Save();
			
			$response['results'][]=$module->getAttributes();
		}
		
//		$defaultModels = \GO\Base\Model\AbstractUserDefaultModel::getAllUserDefaultModels();
//		
//		$stmt = \GO\Base\Model\User::model()->find(\GO\Base\Db\FindParams::newInstance()->ignoreAcl());		
//		while($user = $stmt->fetch()){
//			foreach($defaultModels as $model){
//				$model->getDefault($user);
//			}
//		}
				
		return $response;
	}
	
	public function actionPermissionsStore($params) {
		
		
		//check access to users or groups module. Because we allow this action without
		//access to the modules module		
		if ($params['paramIdType']=='groupId'){
			if(!\GO::modules()->groups)
				throw new \GO\Base\Exception\AccessDenied();
		}else{
			if(!\GO::modules()->users)
				throw new \GO\Base\Exception\AccessDenied();
		}
			
		$response = array(
			'success' => true,
			'results' => array(),
			'total' => 0
		);
		$modules = array();
		$mods = \GO::modules()->getAllModules();
			
		while ($module=array_shift($mods)) {
			$permissionLevel = 0;
			$usersGroupPermissionLevel = false;
			if (empty($params['id'])) {				
				$aclUsersGroup = $module->acl->hasGroup(\GO::config()->group_everyone); // everybody group
				$permissionLevel=$usersGroupPermissionLevel=$aclUsersGroup ? $aclUsersGroup->level : 0;
			} else {
				if ($params['paramIdType']=='groupId') {
					//when looking at permissions from the groups module.
					$aclUsersGroup = $module->acl->hasGroup($params['id']);
					$permissionLevel=$aclUsersGroup ? $aclUsersGroup->level : 0;
				} else {
					//when looking from the users module
					$permissionLevel = \GO\Base\Model\Acl::getUserPermissionLevel($module->acl_id, $params['id']);					
					$usersGroupPermissionLevel= \GO\Base\Model\Acl::getUserPermissionLevel($module->acl_id, $params['id'], true);
				}
			}
			
			$translated = $module->moduleManager ? $module->moduleManager->name() : $module->id;
			
			// Module permissions only support read permission and manage permission:
			if (\GO\Base\Model\Acl::hasPermission($permissionLevel,\GO\Base\Model\Acl::CREATE_PERMISSION))
				$permissionLevel = \GO\Base\Model\Acl::MANAGE_PERMISSION;			
			
			$modules[$translated]= array(
				'id' => $module->id,
				'name' => $translated,
				'permissionLevel' => $permissionLevel,
				'disable_none' => $usersGroupPermissionLevel!==false && \GO\Base\Model\Acl::hasPermission($usersGroupPermissionLevel,\GO\Base\Model\Acl::READ_PERMISSION),
				'disable_use' => $usersGroupPermissionLevel!==false && \GO\Base\Model\Acl::hasPermission($usersGroupPermissionLevel, \GO\Base\Model\Acl::CREATE_PERMISSION)
			);
			$response['total'] += 1;
		}
		ksort($modules);

		$response['results'] = array_values($modules);
		
		return $response;
	}
	
	
	/**
	 * Checks default models for this module for each user.
	 * 
	 * @param array $params 
	 */
	public function actionCheckDefaultModels($params) {
		
		\GO::session()->closeWriting();
		
//		\GO::$disableModelCache=true;
		$response = array('success' => true);
		$module = \GO\Base\Model\Module::model()->findByPk($params['moduleId']);
		
		
		$models = array();
		$modMan = $module->moduleManager;
		if ($modMan) {
			$classes = $modMan->findClasses('model');
			foreach ($classes as $class) {
				if ($class->isSubclassOf('GO\Base\Model\AbstractUserDefaultModel')) {
					$models[] = \GO::getModel($class->getName());
				}
			}
		}
//		\GO::debug(count($users));
		
		$module->acl->getAuthorizedUsers(
						$module->acl_id, 
						\GO\Base\Model\Acl::READ_PERMISSION, 
						array("GO\Modules\Controller\ModuleController","checkDefaultModelCallback"), array($models));
		
		
//		if(class_exists("GO\Professional\LicenseCheck")){
//			$lc = new \GO\Professional\LicenseCheck();
//			$lc->checkProModules(true);
//		}

		return $response;
	}
	
	public static function checkDefaultModelCallback($user, $models){		
		foreach ($models as $model)
			$model->getDefault($user);		
	}
	
	public function actionSaveSortOrder($params){
		$modules = json_decode($params['modules']);
		
		$i=0;
		foreach($modules as $module){
			$moduleModel = \GO\Base\Model\Module::model()->findByPk($module->id);
			$moduleModel->sort_order=$i++;
			$moduleModel->save();
		}
		
		//todo make this irrelevant
		//backwards compat
		require_once(\GO::config()->root_path.'Group-Office.php');
		$GLOBALS['GO_MODULES']->load_modules();
		return array('success'=>true);
	}

}

