<?php
class GO_Modules_Controller_Module extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Base_Model_Module';
	
	protected function prepareGrid($grid){
		
			
		$grid->setFormatRecordFunction(array('GO_Modules_Controller_Module', 'formatRecord'));
		
    return parent::prepareGrid($grid);
	}
	
	public static function formatRecord($record, $model, $grid){

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
	
	
	public function actionAvailableModulesGrid($params){
		
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
}

