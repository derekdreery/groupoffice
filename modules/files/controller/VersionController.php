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
 * @version $Id: GO_files_Controller_GO_Files_Model_Template.php 7607 2011-09-29 08:42:37Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */
class GO_files_Controller_Version extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Files_Model_Version';

	protected function actionDownload($params){
		$version = GO_Files_Model_Version::model()->findByPk($params['id']);
		$file = $version->getFilesystemFile();
	  GO_Base_Util_Http::outputDownloadHeaders($file);		
		$file->output();
	}
	
	protected function getStoreParams($params) {		
		$findParams = GO_Base_Db_FindParams::newInstance()->ignoreAcl();
		$findParams->getCriteria()->addCondition('file_id', $params['file_id']);		
		
		return $findParams;
	}
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		
		$columnModel->formatColumn('user_name', '$model->user->name');
		
		return parent::formatColumns($columnModel);
	}
}