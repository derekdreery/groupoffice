<?php
/**
 * Meant to be a log file created on the file system during the first log()
 * command. It is created in the current user's personal root folder the first
 * time the log() method is used to write data into it.
 */
class GO_Files_Fs_UserLogFile extends GO_Base_Fs_File{
	
	
	
	public function __construct($prefixString='') {
		
		if (!GO::modules()->isInstalled('files'))
			throw new Exception('The current action requires the files module to be activated for the current user.');
		
		// Make sure the current user's folder exists.

		$userFolderModel = GO_Files_Model_Folder::model()->findHomeFolder(GO::user());

		if (empty($userFolderModel)) {
			$userFolder = new GO_Base_Fs_Folder(GO::config()->file_storage_path.'users/'.GO::user()->username);
			$userFolder->create();
			$userFolderModel = new GO_Files_Model_Folder();
			$userFolderModel->findByPath('users/'.GO::user()->username,true);
		}
		
		parent::__construct(
				GO::config()->file_storage_path.$userFolderModel->path.
				'/'.$prefixString.GO_Base_Util_Date::get_timestamp(time(), true).'.log'
			);
	
	}
		
	/**
	 * Logs data in the file. If the file does not exist on the file system, it
	 * is created here.
	 * @param type $data Data to be logged in the file. Will be casted into a string
	 * if it is not a string.
	 */
	public function log($data){
	
		if(!$this->exists())
			$this->touch(true);
		
		if(!is_string($data))
			$data = var_export($data, true);
			
		$this->putContents($data."\n", FILE_APPEND);
	}
	
}