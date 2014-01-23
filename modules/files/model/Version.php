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
 * The GO_files_Model_Template model
 *
 * @package GO.modules.files
 * @version $Id: GO_files_Model_Template.php 7607 2011-09-29 08:41:31Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 *
 * @property int $id
 * @property int $file_id
 * @property int $user_id 
 * @property int $mtime 
 * @property GO_Files_Model_File $file
 * @property string $path
 * @property int $version
 */
class GO_Files_Model_Version extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_files_Model_Template
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * Enable this function if you want this model to check the acl's automatically.
	 */
	public function aclField() {
		return 'file.folder.acl_id';
	}

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'fs_versions';
	}

	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	public function relations() {
		return array(
				'file' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Files_Model_File', 'field'=>'file_id')
		);
	}
	
	/**
	 * 
	 * @return \GO_Base_Fs_File
	 */
	public function getFilesystemFile(){
		return new GO_Base_Fs_File(GO::config()->file_storage_path.$this->path);
	}
	
	protected function beforeSave() {
		
		$this->mtime=$this->file->fsFile->mtime();
		$this->path = 'versioning/'.$this->file_id.'/'.date('Ymd_Gis', $this->file->fsFile->mtime()).'_'.$this->file->name;
		
		$lastVersion = $this->_findLastVersion();
		if($lastVersion)
			$this->version = $lastVersion->version+1;
		
		return parent::beforeSave();
	}
	
	private function _findLastVersion(){
		$params = GO_Base_Db_FindParams::newInstance()
						->ignoreAcl()
						->single()
						->order('mtime','DESC');
		
		$params->getCriteria()->addCondition('file_id', $this->file_id);
		
		return $this->find($params);
	}
	
	protected function afterSave($wasNew) {
		$file = $this->getFilesystemFile();
		$folder = $file->parent();
		$folder->create();
		
		$this->file->fsFile->move($folder, $file->name());
		
		GO::config()->save_setting("file_storage_usage", GO::config()->get_setting('file_storage_usage')+$file->size());
		
		$this->_deleteOld(); 
		
		return parent::afterSave($wasNew);
	}
	
	protected function beforeDelete() {
		
		$file = $this->getFilesystemFile();
		
		GO::config()->save_setting("file_storage_usage", GO::config()->get_setting('file_storage_usage')-$file->size());
		
		$file->delete();
		
		return parent::beforeDelete();
	}
	
	private function _deleteOld(){	

		if(!empty(GO::config()->max_file_versions)){
			$params = GO_Base_Db_FindParams::newInstance()
							->ignoreAcl()
							->start(GO::config()->max_file_versions)
							->limit(10)
							->order('mtime','DESC');

			$params->getCriteria()->addCondition('file_id', $this->file_id);

			$stmt = $this->find($params);

			foreach($stmt as $version){
				$version->delete(true);
			}
				
		//	$stmt->callOnEach('delete');
		}
	}
}

