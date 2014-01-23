<?php

class GO_Core_Controller_LinkFolder extends GO_Base_Controller_AbstractModelController {

	protected $model = "GO_Base_Model_LinkFolder";

	protected function actionTree($params) {

		$response = array();

		$findParams = GO_Base_Db_FindParams::newInstance();

		$folder_id = isset($params['node']) && substr($params['node'], 0, 10) == 'lt-folder-' ? (substr($params['node'], 10)) : 0;

		if (!empty($folder_id))
			$findParams->getCriteria()->addCondition('parent_id', $folder_id);
		else
			$findParams->getCriteria()
							->addCondition('model_id', $params['model_id'])
							->addCondition('model_type_id', GO_Base_Model_ModelType::model()->findByModelName($params['model_name']));


		$stmt = GO_Base_Model_LinkFolder::model()->find($findParams);

		while ($model = $stmt->fetch()) {
			$node = array(
					'id' => 'lt-folder-' . $model->id,
					'text' => $model->name,
					'iconCls' => 'folder-default'
			);

			if (!$model->hasChildren()) {
				$node['expanded'] = true;
				$node['children'] = array();
			}

			$response[] = $node;
		}

		return $response;
	}

	protected function beforeSubmit(&$response, &$model, &$params) {
		if (empty($params['parent_id'])) {
			$model->model_type_id = GO_Base_Model_ModelType::model()->findByModelName($params['model_name']);
		} else {
			unset($params['model_id']);
		}
		unset($params['model_name']);

		return parent::beforeSubmit($response, $model, $params);
	}

	protected function actionMoveLinks($params) {
		$moveLinks = json_decode($params['selections'], true);
		$target = json_decode($params['target']);

		$response['moved_links'] = array();

		foreach ($moveLinks as $modelNameAndId) {
			$link = explode(':', $modelNameAndId);
			$modelName = $link[0];
			$modelId = $link[1];

			if ($modelName == 'GO_Base_Model_LinkFolder') {
				
				$moveFolder = GO_Base_Model_LinkFolder::model()->findByPk($modelId);
				$moveFolder->parent_id=intval($target->folder_id);
				$moveFolder->save();

			} else {
				
				$moveModel = GO::getModel($modelName)->findByPk($modelId);
				
				$targetModel = GO::getModel($target->model_name)->findByPk($target->model_id);
				$targetModel->updateLink($moveModel, array('folder_id'=>intval($target->folder_id)));
			}
		}
		$response['success'] = true;
		
		return $response;
	}

}