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
class GO_files_Controller_Template extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Files_Model_Template';

	protected function beforeSubmit(&$response, &$model, &$params) {

		if (isset($_FILES['attachments']['tmp_name'][0]) && is_uploaded_file($_FILES['attachments']['tmp_name'][0])) {
			$file = new GO_Base_Fs_File($_FILES['attachments']['tmp_name'][0]);
			$fileWithName = new GO_Base_Fs_File($_FILES['attachments']['name'][0]);
			$model->content = $file->contents();
			$model->extension = $fileWithName->extension();
		}

		return parent::beforeSubmit($response, $model, $params);
	}
	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		
		$columnModel->formatColumn('type', 'GO_Base_Fs_File::getFileTypeDescription($model->extension)');
		
		return parent::formatColumns($columnModel);
	}
	
	protected function getStoreExcludeColumns() {
		return array('content');
	}
	
	
	public function actionDownload($params){
		$template = GO_Files_Model_Template::model()->findByPk($params['id']);
		
	  GO_Base_Util_Common::outputDownloadHeaders(new GO_Base_Fs_File($template->name.'.'.$template->extension));
		
		echo $template->content;
	}
	
	public function actionCreateFile($params){
		
		$filename = GO_Base_Fs_File::stripInvalidChars($params['filename']);
		if(empty($filename))
			throw new Exception("Filename can not be empty");
		
		$template = GO_Files_Model_Template::model()->findByPk($params['template_id']);
		
		$folder = GO_Files_Model_Folder::model()->findByPk($params['folder_id']);
		
		$fsFile = new GO_Base_Fs_File(GO::config()->file_storage_path.$folder->path.'/'.$filename.'.'.$template->extension);
		$fsFile->putContents($template->content);
		
		$fileModel = GO_Files_Model_File::importFromFilesystem($fsFile);
		
		return array('id'=>$fileModel->id, 'success'=>true);
	}

}