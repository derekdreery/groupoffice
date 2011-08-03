<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * 
 * @todo delete files folder, delete custom fields
 * 
 * 
 * The Note model
 * 
 * @property int $id
 * @property int $category_id
 */
class GO_Notes_Model_Note extends GO_Base_Db_ActiveRecord {
	
	public function linkType(){
		return 4;	
	}
	
	public function aclField(){
		return 'category.acl_id';	
	}
	
	public function tableName(){
		return 'no_notes';
	}
	
	public function hasFiles(){
		return true;
	}
	
	public function hasCustomfields(){
		return true;
	}

	public function relations(){
		return array(	
				'category' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Notes_Model_Category', 'field'=>'category_id'),
				'user' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Base_Model_User', 'field'=>'user_id'),
				'customfieldRecord' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Notes_Model_CustomFieldsRecord', 'field'=>'id')
		);
	}


	protected function getCacheAttributes() {
		return array(
				'name' => $this->name,
				'type' => GO::t('note','notes')
		);
	}

	protected function afterSave() {		

		if (isset(GO::modules()->customfields))
			GO_Customfields_Controller_Item::saveCustomFields($this, "GO_Notes_Model_CustomFieldsRecord");

		
		//Does this belong in the controller?
		if (!empty($_POST['tmp_files']) && GO::modules()->has_module('files')) {
			require_once(GO::modules()->modules['files']['class_path'] . 'files.class.inc.php');
			$files = new files();
			$fs = new filesystem();

			$path = $files->build_path($this->files_folder_id);

			$tmp_files = json_decode($_POST['tmp_files'], true);
			while ($tmp_file = array_shift($tmp_files)) {
				if (!empty($tmp_file['tmp_file'])) {
					$new_path = GO::config()->file_storage_path . $path . '/' . $tmp_file['name'];
					$fs->move($tmp_file['tmp_file'], $new_path);
					$files->import_file($new_path, $this->files_folder_id);
				}
			}
		}

		return parent::afterSave();
	}

	/**
	 * The files module will use this function.
	 */
	protected function buildFilesPath() {

		return 'notes/' . File::strip_invalid_chars($this->category->name) . '/' . date('Y', $this->ctime) . '/' . GO_Base_Util_File::strip_invalid_chars($this->name);
	}

}