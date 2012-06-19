<?php
class GO_Modules_Controller_Module extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Base_Model_Module';
	
	
	protected function allowWithoutModuleAccess() {
		return array('permissionsstore');
	}
	
	protected function ignoreAclPermissions() {		
		return array('*');
	}
		
	protected function prepareStore(GO_Base_Data_Store $store){		
			
		$store->getColumnModel()->setFormatRecordFunction(array('GO_Modules_Controller_Module', 'formatRecord'));
		$store->setDefaultSortOrder('sort_order');
    return parent::prepareStore($store);
	}
	
	protected function getStoreParams($params) {
		$findParams = GO_Base_Db_FindParams::newInstance()
						->ignoreAcl()
						->limit(0);
		
		if(!empty(GO::config()->allowed_modules))
			$findParams->getCriteria ()->addInCondition ('id', explode(',',GO::config()->allowed_modules));
		
		return $findParams;
		
	}
	
	public static function formatRecord($record, $model, $store){

		if($model->moduleManager){
			$record['description'] = $model->moduleManager->description();
			$record['name'] = $model->moduleManager->name();
			$record['author'] = $model->moduleManager->author();
		}else
		{
			$record['name']=$model->id;
		}
		
		//$record['user_count']=$model->acl->countUsers();
		
		return $record;
	}
	
	
	protected function actionAvailableModulesStore($params){
		
		$response['results']=array();
		
		$modules = GO::modules()->getAvailableModules();
		
		$availableModules=array();
						
		foreach($modules as $moduleClass){		
			
			$module = new $moduleClass;//call_user_func($moduleClase();			
			$availableModules[$module->name()] = array(
					'id'=>$module->id(),
					'name'=>$module->name(),
					'description'=>$module->description()
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
			$module = new GO_Base_Model_Module();
			$module->id=$moduleId;
			$module->setAttribute('name',$module->moduleManager->name());			
			
			$module->moduleManager->checkDependenciesForInstallation($modules);	
			
			if(!$module->save())
				throw new GO_Base_Exception_Save();
			
			$response['results'][]=$module->getAttributes();
		}
		
//		$defaultModels = GO_Base_Model_AbstractUserDefaultModel::getAllUserDefaultModels();
//		
//		$stmt = GO_Base_Model_User::model()->find(GO_Base_Db_FindParams::newInstance()->ignoreAcl());		
//		while($user = $stmt->fetch()){
//			foreach($defaultModels as $model){
//				$model->getDefault($user);
//			}
//		}
		
		//todo make this irrelevant
		//backwards compat
		require_once(GO::config()->root_path.'Group-Office.php');
		$GLOBALS['GO_MODULES']->load_modules();
		
		return $response;
	}
	
	public function actionPermissionsStore($params) {
		
		
		//check access to users or groups module. Because we allow this action without
		//access to the modules module		
		if ($params['paramIdType']=='groupId'){
			if(!GO::modules()->groups)
				throw new GO_Base_Exception_AccessDenied();
		}else{
			if(!GO::modules()->users)
				throw new GO_Base_Exception_AccessDenied();
		}
			
		$paramId = intval($params['id']);
		$response = array(
			'success' => true,
			'results' => array(),
			'total' => 0
		);
		$modules = array();
		$mods = GO::modules()->getAllModules();
			
		while ($module=array_shift($mods)) {
			$permissionLevel = 0;
			$usersGroupPermissionLevel = false;
			if (empty($paramId)) {				
				$aclUsersGroup = $module->acl->hasGroup(GO::config()->group_everyone); // everybody group
				$permissionLevel=$aclUsersGroup ? $aclUsersGroup->level : 0;
			} else {
				if ($params['paramIdType']=='groupId') {
				
					$aclUsersGroup = $module->acl->hasGroup($paramId);
					$permissionLevel=$aclUsersGroup ? $aclUsersGroup->level : 0;
				} else {
					$permissionLevel = GO_Base_Model_Acl::getUserPermissionLevel($module->acl_id, $paramId);					
					$usersGroupPermissionLevel= GO_Base_Model_Acl::getUserPermissionLevel($module->acl_id, $paramId, true);
				}
			}
			
			$translated = $module->moduleManager ? $module->moduleManager->name() : $module->id;
			
			// ExtJs view was not built to handle Write / Write And Delete permissions,
			// but only no read permission, and read and manage permission:
			if (GO_Base_Model_Acl::hasPermission($permissionLevel,GO_Base_Model_Acl::CREATE_PERMISSION))
				$permissionLevel = GO_Base_Model_Acl::MANAGE_PERMISSION;			
			
			$modules[$translated]= array(
				'id' => $module->id,
				'name' => $translated,
				'permissionLevel' => $permissionLevel,
				'disable_none' => $usersGroupPermissionLevel!==false && GO_Base_Model_Acl::hasPermission($usersGroupPermissionLevel,GO_Base_Model_Acl::READ_PERMISSION),
				'disable_use' => $usersGroupPermissionLevel!==false && GO_Base_Model_Acl::hasPermission($usersGroupPermissionLevel, GO_Base_Model_Acl::CREATE_PERMISSION)
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
		$response = array('success' => true);
		$module = GO_Base_Model_Module::model()->findByPk($params['moduleId']);
		$users = $module->acl->getAuthorizedUsers($module->acl_id, GO_Base_Model_Acl::READ_PERMISSION);
		
		$models = array();
		$modMan = $module->moduleManager;
		if ($modMan) {
			$classes = $modMan->findClasses('model');
			foreach ($classes as $class) {
				if ($class->isSubclassOf('GO_Base_Model_AbstractUserDefaultModel')) {
					$models[] = GO::getModel($class->getName());
				}
			}
		}
		
		foreach ($users as $user) {
			foreach ($models as $model)
				$model->getDefault($user);
		}
		
		if(class_exists("GO_Professional_LicenseCheck")){
			$lc = new GO_Professional_LicenseCheck();
			$lc->checkProModules(true);
		}

		return $response;
	}
	
	public function actionSaveSortOrder($params){
		$modules = json_decode($params['modules']);
		
		$i=0;
		foreach($modules as $module){
			$moduleModel = GO_Base_Model_Module::model()->findByPk($module->id);
			$moduleModel->sort_order=$i++;
			$moduleModel->save();
		}
		
		//todo make this irrelevant
		//backwards compat
		require_once(GO::config()->root_path.'Group-Office.php');
		$GLOBALS['GO_MODULES']->load_modules();
		return array('success'=>true);
	}

}

