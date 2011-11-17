<?php
/*
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * Abstract class to export data in GO
 * 
 * 
 * @package GO.base.controller
 * @version $Id: BatchEditController.php 7607 2011-11-16 09:17:42Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits <wsmits@intermesh.nl> 
 * 
 */
class GO_Core_Controller_BatchEdit extends GO_Base_Controller_AbstractController {
	
	/**
	 * Update the given id's with the given data
	 * The params array must contain at least:
	 * 
	 * @param array $params 
	 * <code>
	 * $params['data'] = The new values that need to be set
	 * $params['keys'] = The keys of the records that need to get the new data
	 * $params['model']= The model classname of the records that need to be updated
	 * </code>
	 */
	public function actionSubmit($params) {
		if(empty($params['data']) || empty($params['keys']) || empty($params['model_name']))
			return false;
		
		$data = json_decode($params['data']);
		
		$keys = json_decode($params['keys']);
		
		if(is_array($keys)) {
			foreach($keys as $key) {
				$model = $params['model_name']::model()->findByPk($key);
				if(!empty($model))
					$this->_updateModel($model, $data);
			}
		}
		
		$response['success'] = true;
		return $response;
	}
	
	/**
	 * Update the model with the given attributes
	 *  
	 * @param GO_Base_Db_ActiveRecord $model
	 * @param array $data
	 * @return Boolean 
	 */
	private function _updateModel($model, $data) {
		foreach($data as $attr=>$value){
			if($value->edit){
				$attribute = $value->name;
				$model->$attribute = $value->value;
			}
		}
		return $model->save();
	}
	
	
	/**
	 * Return all attribute labels and names for the given object type
	 * With this data the batchedit form can be created
	 * 
	 * @param array $params 
	 * <code>
	 * $params['model']= The model classname of the records that need to be updated
	 * </code>
	 */
	public function actionAttributesStore($params) {
		if(empty($params['model_name']))
			return false;
		
		$tmpModel = new $params['model_name']();
		$columns = $tmpModel->getColumns();
		
		$rows = array();
		foreach($columns as $key=>$value) {
			if(!empty($value['gotype']) && $key != 'ctime' && $key != 'mtime' && $key != 'user_id') {
				$row = array();

				$row['name']= $key;
				$row['label']= $tmpModel->getAttributeLabel($key);
				$row['value']='';
				$row['edit']='';
				$row['gotype']=$value['gotype'];

				$rows[] = $row;
			}
		}
		
		$response['results'] = $rows;
						
		return $response;
	}
}