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
	 * Get the exporttypes that can be used and get the data for the checkboxes
	 * 
	 * @param array $params
	 * @return array 
	 */
	protected function actionLoad($params){
		$response = array();		
		$response['data'] = array();
		
		$settings =  GO_Base_Export_Settings::load();
		$data = $settings->getArray();
		
		// retreive checkbox settings
		$response['data']['includeHeaders'] = $data['export_include_headers'];
		$response['data']['humanHeaders'] = $data['export_human_headers'];
		$response['data']['includeHidden'] = $data['export_include_hidden'];
		
		$response['outputTypes'] = $this->_getExportTypes(GO::config()->root_path.'go/base/export/');
		
		if(!empty($params['exportClassPath']))
			$response['outputTypes'] = array_merge($response['outputTypes'], $this->_getExportTypes(GO::config()->root_path.$params['exportClassPath']));
		
		$response['success'] =true;
		return $response;
	}
	
//	
//	/**
//	 * Get the exporttypes that can be used
//	 * 
//	 * @param array $params
//	 * @return array 
//	 */
//	protected function actionTypes($params) {
//		$response = array();		
//		$response['outputTypes'] = $this->_getExportTypes(GO::config()->root_path.'go/base/export/');
//		
//		if(!empty($params['exportClassPath']))
//			$response['outputTypes'] = array_merge($response['outputTypes'], $this->_getExportTypes(GO::config()->root_path.$params['exportClassPath']));
//		
//		$response['success'] =true;
//		return $response;
//	}
//	

	
	/**
	 * Return the default found exportclasses that are available in the export 
	 * folder and where the showInView parameter is true
	 * 
	 * @return array 
	 */
	private function _getExportTypes($path) {
		
		$defaultTypes = array();
		
		$folder = new GO_Base_Fs_Folder($path);
		$contents = $folder->ls();
		
		$classParts = explode('/',$folder->stripRootPath());
		
		$classPath='GO_';
		foreach($classParts as $part){
			if($part!='go' && $part != 'modules')
				$classPath.=ucfirst($part).'_';
		}
		
		foreach($contents as $exporter) {
			if(is_file($exporter->path())) {
				$classname = $classPath.$exporter->nameWithoutExtension();
				if($classname != 'GO_Base_Export_ExportInterface' && $classname != 'GO_Base_Export_Settings')
				{
					//$export = new $classname('temp');
					
					//this is only compatible with php 5.3:
					//$classname::$showInView
					//so we use ReflectionClass
					
					$class = new ReflectionClass($classname);
					$showInView=$class->getStaticPropertyValue('showInView');
					$name = $class->getStaticPropertyValue('name');
					$useOrientation = $class->getStaticPropertyValue('useOrientation');

					if($showInView)
						$defaultTypes[$classname] = array('name'=>$name,'useOrientation'=>$useOrientation);
				}
			}
		}

		return $defaultTypes;
	}
}