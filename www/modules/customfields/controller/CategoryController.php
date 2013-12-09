<?php
class GO_Customfields_Controller_Category extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Customfields_Model_Category';	


	protected function actionSaveSort($params){		
		$fields = json_decode($params['categories'], true);
		$sort = 0;
		foreach ($fields as $field) {
			$model = \GO_Customfields_Model_Category::model()->findByPk($field['id']);
			$model->sort_index=$sort;
			$model->save();
			$sort++;
		}		
		
		return array('success'=>true);
	}	

	protected function getStoreParams($params) {
		
		$findParams = \GO_Base_Db_FindParams::newInstance()
						->order('sort_index');
		
		$findParams->getCriteria()->addCondition('extends_model', $params['extends_model']);						
		
		return $findParams;

	}
	
	
	protected function actionEnabled($params){
		
		
		$disableCategories = \GO_Customfields_Model_DisableCategories::model()->findByPk(array('model_id'=>$params['model_id'],'model_name'=>$params['model_name']));

		$response['enabled_customfield_categories']=$disableCategories!=false;
		
		$findParams = \GO_Base_Db_FindParams::newInstance()
						->order('sort_index');
		
		$findParams->getCriteria()->addCondition('extends_model', $params['model_name']);						
		
		$stmt = \GO_Customfields_Model_Category::model()->find($findParams);
		
		$response['results']=array();
		while($category = $stmt->fetch()){
			$record = $category->getAttributes('formatted');
			
			$record['checked']= \GO_Customfields_Model_EnabledCategory::model()->findByPk(array(
					'category_id'=>$category->id,
					'model_name'=>$params['model_name'],
					'model_id'=>$params['model_id']
			))!=false;
			
			$response['results'][]=$record;
		}
		return $response;
	}
	
	protected function actionEnableDisabledCategories($params){
		$disableCategories = \GO_Customfields_Model_DisableCategories::model()->findByPk(array('model_id'=>$params['model_id'],'model_name'=>$params['model_name']));
		
		$enable = !empty($params['enabled']) && $params['enabled']!='false';
		
		$response['enabled']=$enable;
		
		if(!$enable && $disableCategories)
			$disableCategories->delete();
		
		if($enable && !$disableCategories){
			$disableCategories = new \GO_Customfields_Model_DisableCategories();
			$disableCategories->model_name=$params['model_name'];
			$disableCategories->model_id=$params['model_id'];
			$disableCategories->save();
		}
		
		$response['success']=true;
		
		return $response;
	}
	
	/**
	 * Get the data for model edit forms. It should be added to $response['customfields'] in a model controller when loading the edit dialog.
	 * It's also usefull to put into calendar or addressbook select combo's so you can update the tabs on change of the combo.
	 * 
	 * @param string $modelName Model of the customfields
	 * @param int $modelId Model of the controlling model a calendar id for the event custom fields for example.
	 * @return array array("disable_categories"=>true,"enabled_categories"=>array(1,2)) 
	 */
	public static function getEnabledCategoryData($modelName, $modelId){
		$response['disable_categories']=\GO_Customfields_Model_DisableCategories::isEnabled($modelName, $modelId);
		
		if($response['disable_categories'])
			$response['enabled_categories']=\GO_Customfields_Model_EnabledCategory::model()->getEnabledIds($modelName, $modelId);
		
		return $response;
	}
	
	protected function actionSetEnabled($params){
		
		$categories = json_decode($params['categories'], true);
		
		foreach($categories as $category_id){
			$enabled = \GO_Customfields_Model_EnabledCategory::model()->findByPk(array(
					'category_id'=>$category_id,
					'model_name'=>$params['model_name'],
					'model_id'=>$params['model_id']
			));
			
			if(!$enabled){
				$enabled = new \GO_Customfields_Model_EnabledCategory();
				$enabled->category_id=$category_id;
				$enabled->model_name=$params['model_name'];
				$enabled->model_id=$params['model_id'];
				$enabled->save();
			}
		}
		
		$stmt = \GO_Customfields_Model_EnabledCategory::model()->find(
			\GO_Base_Db_FindParams::newInstance()
						->criteria(
								\GO_Base_Db_FindCriteria::newInstance()
									->addInCondition('category_id', $categories, 't', true, true)
									->addCondition('model_name', $params['model_name'])
										->addCondition('model_id', $params['model_id'])
										)

		);
		$stmt->callOnEach('delete');
		
		return array('success'=>true);
		
		
				
	}
}


