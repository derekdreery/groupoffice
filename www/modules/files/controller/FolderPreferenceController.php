<?php

/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * The GO_files_Controller_GO_Files_Model_Template controller
 *
 * @package GO.modules.files
 * @FolderPreference $Id: GO_files_Controller_GO_Files_Model_Template.php 7607 2011-09-29 08:42:37Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */
class GO_files_Controller_FolderPreference extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Files_Model_FolderPreference';
	
	/**
	 * Can be overriden if you have a primary key that's not 'id' or is an array.
	 * 
	 * @param array $params
	 * @return mixed 
	 */
	protected function getPrimaryKeyFromParams($params){	
		return empty($params['folder_id']) ? false : array('folder_id'=>$params['folder_id'],'user_id'=>\GO::user()->id);
	}
	
	protected function getModelFromParams($params){
		$modelName = $this->model;
		$model=false;
		$pk = $this->getPrimaryKeyFromParams($params);
		if(!empty($pk))
			$model = \GO::getModel($modelName)->findByPk($pk);
			
		if(!$model){				
			$model = new $modelName;
			$model->setAttributes($params);
		}	
		
		return $model;
	}
}