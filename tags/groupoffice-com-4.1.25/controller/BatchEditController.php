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
	 * $params['model_name']= The model classname of the records that need to be updated
	 * </code>
	 */
	protected function actionSubmit($params) {
		if(empty($params['data']) || empty($params['keys']) || empty($params['model_name']))
			return false;
		
		$data = json_decode($params['data'], true);
		
		$keys = json_decode($params['keys'], true);
		
		if(is_array($keys)) {
			foreach($keys as $key) {
				$model = GO::getModel($params['model_name'])->findByPk($key);
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
		
		$changeAttributes = array();
		
		foreach($data as $attr=>$value){
			if($value['edit']){
				$changeAttributes[$value['name']] = $value['value'];
			}
		}
		
		$model->setAttributes($changeAttributes);

		return $model->save();
	}
	
	
	/**
	 * Return all attribute labels and names for the given object type
	 * With this data the batchedit form can be created
	 * 
	 * @param array $params 
	 * <code>
	 * $params['model_name']= The model classname of the records that need to be updated
	 * </code>
	 */
	protected function actionAttributesStore($params) {
		if(empty($params['model_name']))
			return false;
		
		$tmpModel = new $params['model_name']();
		$columns = $tmpModel->getColumns();
		
		$params['excludeColumns']=array('ctime','mtime','model_id');
		
		if(isset($params['exclude']))
			$params['excludeColumns']=  array_merge($params['excludeColumns'],explode(',', $params['exclude']));
		
		$rows = array();
		foreach($columns as $key=>$value) {

			if(!in_array($key, $params['excludeColumns'])) {
				$row = array();

				$row['name']= $key;
				$row['label']= $tmpModel->getAttributeLabel($key);
				$row['value']='';
				$row['edit']='';
				$row['gotype']=!empty($value['gotype'])?$value['gotype']:'';
				if(!empty($value['regex'])){
					$regexDelimiter = substr($value['regex'], 0,1);
					$parts = explode($regexDelimiter, $value['regex']);
					$row['regex']=$parts[1];
					$row['regex_flags']=$parts[2];					
				}else
				{
					$row['regex_flags']='';
					$row['regex']='';
				}
				
				

				$rows[] = $row;
			}
		}
		
		// Get the customfields for this model
		$cf = $tmpModel->getCustomfieldsRecord();
		if($cf){
			$cfcolumns = $cf->getColumns();

			$cfrows = array();
			foreach($cfcolumns as $key=>$value) {
				if(!in_array($key, $params['excludeColumns']) && !empty($value['gotype'])) {
					$row = array();

					$row['name']= $key;
					$row['label']= $cf->getAttributeLabel($key);
					$row['value']='';
					$row['edit']='';
					$row['gotype']=$value['gotype'];

					$cfrows[] = $row;
				}
			}
		
			$rows = array_merge($rows,$cfrows);
		}
		$response['results'] = $rows;
						
		return $response;
	}
}