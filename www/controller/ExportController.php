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
 * class to export data in GO
 * 
 * 
 * @package GO.base.controller
 * @version $Id: AbstractExportController.php 7607 2011-06-15 09:17:42Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits <wsmits@intermesh.nl> 
 * @abstract
 */
class GO_Core_Controller_Export extends GO_Base_Controller_AbstractController { 

	/**
	 * Get the exporttypes that can be used
	 * 
	 * @param array $params
	 * @return array 
	 */
	public function actionTypes($params=false) {
		$types = array();
		
		$defaultExports = $this->_getDefaultExportTypes();
		$types = array_merge($types,$defaultExports);
		
		if(!empty($params['model'])) {
			$model = new $params['model']();
			$modelExports = $this->_getExportTypesFromModel($model);
			$types = array_merge($types,$modelExports);
		}
		
		$response = array();		
		$response['outputTypes'] = $types;
		$response['success'] =true;
		return $response;
	}
	
	/**
	 * TODO: Create the description for this function
	 * @param GO_Base_Db_ActiveRecord $model
	 * @return array 
	 */
	private function _getExportTypesFromModel($model) {
		return array();
	}
	
	/**
	 * Return the default found exportclasses that are available in the export 
	 * folder and where the showInView parameter is true
	 * 
	 * @return array 
	 */
	private function _getDefaultExportTypes() {
		
		$defaultTypes = array();
		
		$folder = new GO_Base_Fs_Folder(GO::config()->root_path.'go/base/export/');
		$contents = $folder->ls();
		
		foreach($contents as $exporter) {
			if(is_file($exporter->path())) {
				$classname = 'GO_Base_Export_'.$exporter->nameWithoutExtension();
				if($classname != 'GO_Base_Export_ExportInterface')
				{
					//$export = new $classname('temp');
					
					//this is only compatible with php 5.3:
					//$classname::$showInView
					//so we use ReflectionClass
					
					$class = new ReflectionClass($classname);
					$showInView=$class->getStaticPropertyValue('showInView');
					
					if($showInView)
						$defaultTypes[$classname] = array('name'=>$classname::$name,'useOrientation'=>$classname::$useOrientation);
				}
			}
		}

		return $defaultTypes;
	}
}