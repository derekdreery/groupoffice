<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: GO_Files_Model_File.php 7607 2011-09-01 15:40:20Z <<USERNAME>> $
 * @copyright Copyright Intermesh
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */

/**
 * The GO_Files_Model_File model
 * 
 * @property int $id
 * @property int $folder_id
 * @property String $name

 * @property int $locked_user_id
 * @property int $status_id
 * @property int $ctime
 * @property int $mtime
 * @property int $muser_id
 * @property int $size
 * @property int $user_id
 * @property String $comments
 * @property String $extension
 * @property int $expire_time
 * @property String $random_code
 * 
 * @property String $thumbURL
 * 
 * @property String $downloadUrl
 * 
 * @property String $path
 * @property GO_Base_Fs_File $fsFile
 * @property GO_Files_Model_Folder $folder
 * @property GO_Base_Model_User $lockedByUser
 */
class GO_Files_Model_File extends GO_Base_Db_ActiveRecord {
	
	
	public static $deleteInDatabaseOnly=false;
	
	private $_permissionLevel;

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Files_Model_File
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * Enable this function if you want this model to check the acl's automatically.
	 */
	public function aclField() {
		return 'folder.acl_id';
	}

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'fs_files';
	}

	protected function getLocalizedName() {
		return GO::t('file', 'files');
	}
	
	public function customfieldsModel() {
		return "GO_Files_Customfields_Model_File";
	}

	public function hasLinks() {
		return true;
	}
	
	protected function getCacheAttributes() {
		
		$path = $this->path;
		
		//Don't cache tickets files because there are permissions issues. Everyone has read access to the types but may not see other peoples files.
		if(strpos($path, 'tickets/')===0){
			return false;
		}
		
		return array('name'=>$this->name, 'description'=>$path);
	}
	
	public function getLogMessage($action){
		return $this->path;
	}
	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	public function relations() {
		return array(
				'lockedByUser' => array('type' => self::BELONGS_TO, 'model' => 'GO_Base_Model_User', 'field' => 'locked_user_id'),
				'folder' => array('type' => self::BELONGS_TO, 'model' => 'GO_Files_Model_Folder', 'field' => 'folder_id'),
				'versions' => array('type'=>self::HAS_MANY, 'model'=>'GO_Files_Model_Version', 'field'=>'file_id', 'delete'=>true),
		);
	}
	
	public function getPermissionLevel(){
		
		if(GO::$ignoreAclPermissions)
			return GO_Base_Model_Acl::MANAGE_PERMISSION;
		
		if(!$this->aclField())
			return -1;	
		
		if(!GO::user())
			return false;
		
		//if($this->isNew && !$this->joinAclField){
		if(empty($this->{$this->aclField()}) && !$this->joinAclField){
			//the new model has it's own ACL but it's not created yet.
			//In this case we will check the module permissions.
			$module = $this->getModule();
			if($module=='base'){
				return GO::user()->isAdmin() ? GO_Base_Model_Acl::MANAGE_PERMISSION : false;
			}else
				return GO::modules()->$module->permissionLevel;
			 
		}else
		{		
			if(!isset($this->_permissionLevel)){

				$acl_id = $this->findAclId();
				if(!$acl_id){
					throw new Exception("Could not find ACL for ".$this->className()." with pk: ".$this->pk);
				}

				$this->_permissionLevel=GO_Base_Model_Acl::getUserPermissionLevel($acl_id);// model()->findByPk($acl_id)->getUserPermissionLevel();
			}
			return $this->_permissionLevel;
		}
		
	}

	protected function init() {
		$this->columns['expire_time']['gotype'] = 'unixdate';
		$this->columns['name']['required']=true;
		parent::init();
	}
	
	/**
	 * Check if a file is locked by another user.
	 * 
	 * @return boolean 
	 */
	public function isLocked(){
		return !empty($this->locked_user_id) && (!GO::user() || $this->locked_user_id!=GO::user()->id);
	}
	
	public function unlockAllowed(){
		return ($this->locked_user_id==GO::user()->id || GO::user()->isAdmin()) && $this->checkPermissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION);
	}
	
        public function getJsonData() {
            return array(
                'id' => $this->model_id,
                'name' => $this->path,
                'ctime' => GO_Base_Util_Date::get_timestamp($this->ctime),
                'mtime' => GO_Base_Util_Date::get_timestamp($this->mtime),
                'extension' => $this->extension,
                'size' => $this->size,
                'user_id' => $this->user_id,
                'type' => $this->type,
                'folder_id' => $this->folder_id,
                'type_id' => 'f:'.$this->id,
                'path' => $this->path,
                'locked' => $this->isLocked(),
                'locked_user_id' => $this->locked_user_id,
                'unlock_allowed' => $this->unlockAllowed(),
                'expire_time' => $this->expire_time > 0 ? GO_Base_Util_Date::get_timestamp($this->expire_time,false) : '',
                'thumbs' => 0,
                'thumb_url' => $this->getThumbURL()
              );
        }        
        
	/**
	 * 
	 * @return \GO_Base_Fs_File
	 */
	private function _getOldFsFile(){
		
		if($this->isNew)
			return $this->fsFile;
		
		$filename = $this->isModified('name') ? $this->getOldAttributeValue('name') : $this->name;
		if($this->isModified('folder_id')){
			//file will be moved so we need the old folder path.
			$oldFolderId = $this->getOldAttributeValue('folder_id');
			$oldFolder = GO_Files_Model_Folder::model()->findByPk($oldFolderId);				
			$oldRelPath = $oldFolder->path;				
			$oldPath = GO::config()->file_storage_path . $oldRelPath . '/' . $filename;

		}else{
			$oldPath = GO::config()->file_storage_path . $this->folder->path.'/'.$filename;
		}
		return new GO_Base_Fs_File($oldPath);
	}
	
	protected function beforeDelete() {
		
		//blocked database check. We check this in the controller now.
		if($this->isLocked() && !GO::user()->isAdmin())
			throw new Exception(GO::t("fileIsLocked","files").': '.$this->path);
		
		return parent::beforeDelete();
	}
	
	public static function checkQuota($newBytes){
		if(GO::config()->quota>0){				
			$currentQuota = GO::config()->get_setting('file_storage_usage');			
			return $currentQuota+$newBytes<=GO::config()->quota;
		}else
		{
			return true;
		}
	}

	
	protected function beforeSave() {		
		
		//check permissions on the filesystem
		if($this->isNew){
			if(!$this->folder->fsFolder->isWritable()){
				throw new Exception("Folder ".$this->folder->path." is read only on the filesystem. Please check the file system permissions (hint: chown -R www-data:www-data /home/groupoffice)");
			}
		}else
		{
			if($this->isModified('name') || $this->isModified('folder_id')){
				if(!$this->_getOldFsFile()->isWritable())
					throw new Exception("File ".$this->path." is read only on the filesystem. Please check the file system permissions (hint: chown -R www-data:www-data /home/groupoffice)");
			}
		}
		
		if(!$this->isNew){
			
			if($this->isModified('name')){				
				//rename filesystem file.
				//throw new Exception($this->getOldAttributeValue('name'));
				$oldFsFile = $this->_getOldFsFile();		
				if($oldFsFile->exists())
					$oldFsFile->rename($this->name);
				
				$this->notifyUsers(
					$this->folder_id,
					GO_Files_Model_FolderNotificationMessage::RENAME_FILE,
					$this->folder->path . '/' . $this->getOldAttributeValue('name'),
					$this->folder->path . '/' . $this->name
				);
			}

			if($this->isModified('folder_id')){				
				if(!isset($oldFsFile))
					$oldFsFile = $this->_getOldFsFile();

				if (!$oldFsFile->move(new GO_Base_Fs_Folder(GO::config()->file_storage_path . dirname($this->path))))
					throw new Exception("Could not rename folder on the filesystem");
				
				//get old folder objekt
                                $oldFolderId = $this->getOldAttributeValue('folder_id');
				$oldFolder = GO_Files_Model_Folder::model()->findByPk($oldFolderId);

				$this->notifyUsers(
					array(
					    $this->getOldAttributeValue('folder_id'),
					    $this->folder_id
					),
					GO_Files_Model_FolderNotificationMessage::MOVE_FILE,
					$oldFolder->path . '/' . $this->name,
					$this->path
				);
			}
		}
			
		if($this->isModified('locked_user_id')){
			$old_locked_user_id = $this->getOldAttributeValue('locked_user_id');
			if(!empty($old_locked_user_id) && $old_locked_user_id != GO::user()->id && !GO::user()->isAdmin())
				throw new GO_Files_Exception_FileLocked();
		}
			

		$this->extension = $this->fsFile->extension();
		//make sure extension is not too long
		$this->cutAttributeLength("extension");
		
		$this->size = $this->fsFile->size();
		//$this->ctime = $this->fsFile->ctime();
		$this->mtime = $this->fsFile->mtime();
		
		$existingFile = $this->folder->hasFile($this->name);
		if($existingFile && $existingFile->id!=$this->id)
			throw new Exception(sprintf(GO::t('filenameExists','files'), $this->path));
		
		return parent::beforeSave();
	}

	protected function getPath() {
		return $this->folder ? $this->folder->path . '/' . $this->name : $this->name;
	}

	protected function getFsFile() {
		return new GO_Base_Fs_File(GO::config()->file_storage_path . $this->path);
	}
	
	private function _addQuota(){
		if(GO::config()->quota>0 && ($this->isModified('size') || $this->isNew)){
			$sizeDiff = $this->fsFile->size()-$this->getOldAttributeValue('size');
			
			GO::debug("Adding quota: $sizeDiff");
			
			GO::config()->save_setting("file_storage_usage", GO::config()->get_setting('file_storage_usage')+$sizeDiff);
		}
	}
	
	private function _removeQuota(){
		if(GO::config()->quota>0){
			GO::debug("Removing quota: $this->size");
			GO::config()->save_setting("file_storage_usage", GO::config()->get_setting('file_storage_usage')-$this->size);
		}
	}
	
	protected function afterSave($wasNew) {
		$this->_addQuota();

		if ($wasNew) {
			$this->notifyUsers(
				$this->folder_id,
				GO_Files_Model_FolderNotificationMessage::ADD_FILE,
                $this->name,
				$this->folder->path
			);
		} else {
			if (!$this->isModified('name') && !$this->isModified('folder_id')) {
				$this->notifyUsers(
					$this->folder_id,
					GO_Files_Model_FolderNotificationMessage::UPDATE_FILE,
					$this->path
				);
			}
		}
		

		//touch the timestamp so it won't sync with the filesystem
		$this->folder->touch();
		

		return parent::afterSave($wasNew);
	}

	protected function afterDelete() {
		
		$this->_removeQuota();
		
		if(!GO_Files_Model_File::$deleteInDatabaseOnly)			
			$this->fsFile->delete();
		
		$versioningFolder = new GO_Base_Fs_Folder(GO::config()->file_storage_path.'versioning/'.$this->id);
		$versioningFolder->delete();
		
		$this->notifyUsers(
            $this->folder_id,
			GO_Files_Model_FolderNotificationMessage::DELETE_FILE, 
			$this->path
		);

		return parent::afterDelete();
	}

	/**
	 * The link that can be send in an e-mail as download link.
	 * 
	 * @return string 
	 */
	public function getEmailDownloadURL($html=true, $newExpireTime=false) {
		
		if($newExpireTime){
			$this->random_code=GO_Base_Util_String::randomPassword(11,'a-z,A-Z,0-9');
			$this->expire_time = $newExpireTime;
			$this->save();
		}
		
		if (!empty($this->expire_time) && !empty($this->random_code)) {
			return GO::url('files/file/download', array('id'=>$this->id,'random_code'=>$this->random_code,'inline'=>'false'), false, $html);
		}
	}
	
	
	/**
	 * The link to download the file.
	 * This function does not check the file download expire time and the random code
	 * 
	 * @return string 
	 */
	public function getDownloadURL($downloadAttachment=true, $relative=false) {
		return GO::url('files/file/download', array('id'=>$this->id, 'inline'=>$downloadAttachment?'false':'true'), $relative);		
	}

	
	public function getThumbURL($urlParams=array("lw"=>100, "ph"=>100, "zc"=>1)) {
		
		$urlParams['filemtime']=$this->mtime;
		$urlParams['src']=$this->path;
		
		if($this->extension=='svg'){
			return $this->getDownloadURL(false, true);
		}else
		{		
			return GO::url('core/thumb', $urlParams);
		}
	}
	
	/**
	 * Move a file to another folder
	 * 
	 * @param GO_Files_Model_Folder $destinationFolder
	 * @return boolean 
	 */
	public function move($destinationFolder,$appendNumberToNameIfExists=false){
		
		$this->folder_id=$destinationFolder->id;		
		if($appendNumberToNameIfExists)
			$this->appendNumberToNameIfExists();
		return $this->save();
	}
	
	/**
	 * Copy a file to another folder.
	 * 
	 * @param GO_Files_Model_Folder $destinationFolder
	 * @param string $newFileName. Leave blank to use the same name.
	 * @return GO_Files_Model_File 
	 */
	public function copy($destinationFolder, $newFileName=false, $appendNumberToNameIfExists=false){
		
		$copy = $this->duplicate(array('folder_id'=>$destinationFolder->id), false);
		
		if($newFileName)
			$copy->name=$newFileName;
		
		if($appendNumberToNameIfExists)
			$copy->appendNumberToNameIfExists();
			
		$this->fsFile->copy($copy->fsFile->parent(), $copy->name);
		
		$copy->save();
		
		return $copy;
	}
	
	/**
	 * Import a filesystem file into the database.
	 * 
	 * @param GO_Base_Fs_File $fsFile
	 * @return GO_Files_Model_File 
	 */
	public static function importFromFilesystem(GO_Base_Fs_File $fsFile){
		
		$folderPath = str_replace(GO::config()->file_storage_path,"",$fsFile->parent()->path());
		
		$folder = GO_Files_Model_Folder::model()->findByPath($folderPath, true);
		return $folder->addFile($fsFile->name());	
	}
	
	/**
	 * Replace filesystem file with given file.
	 * 
	 * @param GO_Base_Fs_File $fsFile 
	 */
	public function replace(GO_Base_Fs_File $fsFile, $isUploadedFile=false){
		
		if($this->isLocked())
			throw new GO_Files_Exception_FileLocked();
//		for safety allow replace action
//		if(!GO_Files_Model_File::checkQuota($fsFile->size()-$this->size))
//			throw new GO_Base_Exception_InsufficientDiskSpace();
		
		$this->saveVersion();
				
		$fsFile->move($this->folder->fsFolder,$this->name, $isUploadedFile);
		$fsFile->setDefaultPermissions();
		
		$this->mtime=$fsFile->mtime();	
		$this->save();
	}
	
	public function putContents($data){
//		for safety allow replace actions
//		if(!GO_Files_Model_File::checkQuota(strlen($data)))
//			throw new GO_Base_Exception_InsufficientDiskSpace();
		
		$this->fsFile->putContents($data);		
		$this->mtime=$this->fsFile->mtime();	
		$this->save();
	}
	
	/**
	 * Copy current file to the versioning system. 
	 */
	public function saveVersion(){		
		if(GO::config()->max_file_versions>-1){
			$version = new GO_Files_Model_Version();
			$version->file_id=$this->id;
			$version->save();
		}
	}
	
	/**
	 * Find the file model by relative path.
	 * 
	 * @param string $relpath Relative path from GO::config()->file_storage_path
	 * @return GO_Files_Model_File 
	 */
	public function findByPath($relpath,$caseSensitive=true){
		$folder = GO_Files_Model_Folder::model()->findByPath(dirname($relpath),false,array(),$caseSensitive);
		if(!$folder)
			return false;
		else
		{
			return $folder->hasFile(GO_Base_Fs_File::utf8Basename($relpath),$caseSensitive);
		}
		
	}
	
	/**
	 * Check if the file is an image.
	 * 
	 * @return boolean 
	 */
	public function isImage(){
		switch(strtolower($this->extension)){
			case 'ico':
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'gif':
			case 'bmp':
			case 'xmind':
			case 'svg':

				return true;
			default:
				return false;
		}
	}
	
	
	
	/**
	 * Checks if a filename exists and renames it.
	 *
	 * @param	string $filepath The complete path to the file
	 * @access public
	 * @return string  New filename
	 */
	public function appendNumberToNameIfExists()
	{
		$dir = $this->folder->path;		
		$origName = $this->fsFile->nameWithoutExtension();
		$extension = $this->fsFile->extension();
		$x=1;
		$newName=$this->name;
		while($this->folder->hasFile($newName))
		{			
			$newName=$origName.' ('.$x.').'.$extension;
			$x++;
		}
		$this->name=$newName;
		return $this->name;
	}
	
	/**
	 *
	 * @param type $folder_id
	 * @param type $type
	 * @param type $arg1
	 * @param type $arg2 
	 */
	public function notifyUsers($folder_id, $type, $arg1, $arg2 = '') {
		GO_Files_Model_FolderNotification::model()->storeNotification($folder_id, $type, $arg1, $arg2);
	}
	
	
	
	public function findRecent($start=false,$limit=false){
		$storeParams = GO_Base_Db_FindParams::newInstance()->ignoreAcl();	

		
		$joinSearchCacheCriteria = GO_Base_Db_FindCriteria::newInstance()
					->addRawCondition('`t`.`id`', '`sc`.`model_id`')
					->addCondition('model_type_id', $this->modelTypeId(),'=','sc');
		
		$storeParams->join(GO_Base_Model_SearchCacheRecord::model()->tableName(), $joinSearchCacheCriteria, 'sc', 'INNER');
		
		
		$aclJoinCriteria = GO_Base_Db_FindCriteria::newInstance()
							->addRawCondition('a.acl_id', 'sc.acl_id','=', false);
			
		$aclWhereCriteria = GO_Base_Db_FindCriteria::newInstance()
						->addCondition('user_id', GO::user()->id,'=','a', false)
						->addInCondition("group_id", GO_Base_Model_User::getGroupIds(GO::user()->id),"a", false);

		$storeParams->join(GO_Base_Model_AclUsersGroups::model()->tableName(), $aclJoinCriteria, 'a', 'INNER');

		$storeParams->criteria(GO_Base_Db_FindCriteria::newInstance()
								->addModel(GO_Files_Model_Folder::model())									
								->mergeWith($aclWhereCriteria));
	
		$storeParams->group(array('t.id'))->order('mtime','DESC');
		
		$storeParams->getCriteria()->addCondition('mtime', GO_Base_Util_Date::date_add(GO_Base_Util_Date::clear_time(time()),-7),'>');
		
		if ($start!==false)
			$storeParams->start($start);
		if ($limit!==false)
			$storeParams->limit($limit);
				
		return $this->find($storeParams);
	}
	
	public function getHandlers(){
		$handlers=array();
		$classes = GO_Files_FilesModule::getAllFileHandlers();
		foreach($classes as $class){
			/* @var $class ReflectionClass */

			$fileHandler = new $class->name;
			if($fileHandler->fileIsSupported($this)){
				$handlers[]= $fileHandler;
			}
		}
		
		return $handlers;
	}
	
	
	public static $defaultHandlers;
	/**
	 * 
	 * @return GO_Files_Filehandler_Interface
	 */
	public function getDefaultHandler(){
		
		$ex = strtolower($this->extension);
		
		if(!isset(self::$defaultHandlers[$ex])){
			$fh = GO_Files_Model_FileHandler::model()->findByPk(
						array('extension'=>$ex, 'user_id'=>GO::user()->id));
			
			if($fh && class_exists($fh->cls)){
				self::$defaultHandlers[$ex]=new $fh->cls;
			}else{
				$classes = GO_Files_FilesModule::getAllFileHandlers();
				foreach($classes as $class){
					/* @var $class ReflectionClass */

					$fileHandler = new $class->name;
					if($fileHandler->isDefault($this)){
						self::$defaultHandlers[$ex]= $fileHandler;
						break;
					}
				}
				
				if(!isset(self::$defaultHandlers[$ex]))
					self::$defaultHandlers[$ex]=new GO_Files_Filehandler_Download();
			}
		}
		
		return self::$defaultHandlers[$ex];
		
		
	}
}
