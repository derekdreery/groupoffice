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
 * The Note model
 * 
 * @property int $id
 * @property int $category_id
 * @property int $files_folder_id
 * @property string $content
 * @property string $name
 * @property int $mtime
 * @property int $ctime
 * @property int $user_id
 */
class GO_Notes_Model_Note extends GO_Base_Db_ActiveRecord {
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Notes_Model_Note 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function getLocalizedName(){
		return GO::t('note','notes');
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
	public function hasLinks() {
		return true;
	}
	public function customfieldsModel(){
		return "GO_Notes_Customfields_Model_Note";
	}

	public function relations(){
		return array(	
				'category' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Notes_Model_Category', 'field'=>'category_id'),		);
	}


	protected function getCacheAttributes() {
		return array(
				'name' => $this->name,
				'description'=>''
		);
	}
	
	/**
	 * The files module will use this function.
	 */
	public function buildFilesPath() {

		return 'notes/' . GO_Base_Fs_Base::stripInvalidChars($this->category->name) . '/' . date('Y', $this->ctime) . '/' . GO_Base_Fs_Base::stripInvalidChars($this->name).' ('.$this->id.')';
	}
	
	public function defaultAttributes() {
		$attr = parent::defaultAttributes();
		
		$category = GO_Notes_NotesModule::getDefaultNoteCategory(GO::user()->id);
		$attr['category_id']=$category->id;
		
		return $attr;
	}
		
}