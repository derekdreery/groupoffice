<?php
class GO_Files_Controller_Bookmark extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Files_Model_Bookmark';
	
	protected function beforeSubmit(&$response, &$model, &$params) {
		// See if folder with this ID can be accessed.
		$folderModel = GO_Files_Model_Folder::model()->findByPk($params['folder_id']);
		
		if (empty($folderModel))
			return false;		
		
		$params['user_id'] = $model->user_id = GO::user()->id;
		
		$response['user_id'] = GO::user()->id;
		$response['folder_id'] = $folderModel->id;
		
		return parent::beforeSubmit($params, $folderModel, $params);
	}
	
	public function formatStoreRecord($record, $model, $store) {
		$record['folder_id'] = $model->folder_id;
		$record['name'] =
			'<span class="x-tree-node x-tree-node-leaf">'.
				'<img class="x-tree-node-icon folder-default" unselectable="on" src="'.GO::config()->host.'/views/Extjs3/ext/resources/images/default/s.gif" alt="" style="width:16px;height:16px;">'.
				'&nbsp;&nbsp;'.$model->folder->name.
			'</span>';
		return parent::formatStoreRecord($record, $model, $store);
	}
	
	protected function actionDelete($params) {
		
		$pk = array('user_id' => GO::user()->id, 'folder_id' => $params['folder_id']);
		
		
		$model = GO_Files_Model_Bookmark::model()->findByPk($pk);
		
//		$response = array();
//		$response = $this->beforeDelete($response, $model, $params);
		$response['success'] = $model->delete();
//		$response = $this->afterDelete($response, $model, $params);

		return $response;
	}
	
	
	protected function beforeStoreStatement(array &$response, array &$params, GO_Base_Data_AbstractStore &$store, GO_Base_Db_FindParams $storeParams) {
		$storeParams
            ->select('`t`.`folder_id`,`t`.`user_id`,`f`.`name`')
            ->joinModel(array(
              'model'=>'GO_Files_Model_Folder',
              'localTableAlias'=>'t',
              'localField'=>'folder_id',
              'foreignField'=>'id',
              'tableAlias'=>'f'
            ))
			->getCriteria()->addCondition('user_id',GO::user()->id);
		return parent::beforeStoreStatement($response, $params, $store, $storeParams);
	}
	
}
?>
