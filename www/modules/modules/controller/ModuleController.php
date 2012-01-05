<?php
class GO_Modules_Controller_Module extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Base_Model_Module';
	
	protected function prepareStore(GO_Base_Data_Store $store){		
			
		$store->getColumnModel()->setFormatRecordFunction(array('GO_Modules_Controller_Module', 'formatRecord'));
		
    return parent::prepareStore($store);
	}
	
	protected function getStoreParams($params) {
		return GO_Base_Db_FindParams::newInstance()
						->limit(0);
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
		
		return $record;
	}
	
	
	public function actionAvailableModulesStore($params){
		
		$response['results']=array();
		
		$modules = GO::modules()->getAvailableModules();
				
		foreach($modules as $moduleClass){
			
			$module = new $moduleClass;//call_user_func($moduleClase();
			
			$response['results'][] = array(
					'id'=>$module->id(),
					'name'=>$module->name(),
					'description'=>$module->description()
			);
		}
		
		$response['total']=count($response['results']);
		
		return $response;
	}
	
	
	public function actionInstall($params){
		$modules = json_decode($params['modules'], true);
		foreach($modules as $moduleId)
		{
			$module = new GO_Base_Model_Module();
			$module->id=$moduleId;
			if(!$module->save())
				throw new GO_Base_Exception_Save();			
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
		
		return array('success'=>true);
	}
}

