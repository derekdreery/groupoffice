<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

namespace GO\Notes\Model;

/**
 * The Note model
 * 
 * @property int $id
 * @property int $category_id
 * @property int $files_folder_id
 * @property string $content
 * @property string $name
 * @property int $mtime
 * @property int $muser_id
 * @property int $ctime
 * @property int $user_id
 * 
 * @property boolean $encrypted
 * @property string $password
 */
class Note extends \GO_Base_Db_ActiveRecord {
	
	private $_decrypted=false;
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return \GO\Notes\Model\Note 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	protected function init() {
		
		$this->columns['name']['required']=true;
		$this->columns['category_id']['required']=true;
		
		return parent::init();
	}
	
	public function getLocalizedName(){
		return \GO::t('note','notes');
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
				'category' => array('type'=>self::BELONGS_TO, 'model'=>'\GO\Notes\Model\Category', 'field'=>'category_id'),		);
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

		return 'notes/' . \GO_Base_Fs_Base::stripInvalidChars($this->category->name) . '/' . date('Y', $this->ctime) . '/' . \GO_Base_Fs_Base::stripInvalidChars($this->name).' ('.$this->id.')';
	}
	
	public function defaultAttributes() {
		$attr = parent::defaultAttributes();
		
		$category = \GO_Notes_NotesModule::getDefaultNoteCategory(\GO::user()->id);
		$attr['category_id']=$category->id;
		
		return $attr;
	}
	
	public $userInputPassword1;
	public $userInputPassword2;
	
	public function validate() {
		
		if (!empty($this->userInputPassword1) || !empty($this->userInputPassword2)) {
			if ($this->userInputPassword1 != $this->userInputPassword2){
				$this->setValidationError('password', \GO::t('passwordMatchError'));
			}				
		}
				
		return parent::validate();
	}
	
	protected function beforeSave() {
		
		if(!empty($this->userInputPassword1)){
			$this->password = crypt($this->userInputPassword1);
			$this->content = \GO_Base_Util_Crypt::encrypt($this->content, $this->userInputPassword1);
		}else
		{
			$this->password="";
		}		
		
		return parent::beforeSave();
	}
	
	
	
	protected function getEncrypted(){
		return !$this->_decrypted && !empty($this->password);
	}

	
	public function decrypt($password) {
		
		if($this->password!=crypt($password,$this->password)){
			return false;		
		}else{
			$this->_decrypted=true;
			$this->content = \GO_Base_Util_Crypt::decrypt($this->content, $password);
			return true;
		}
	}		
}