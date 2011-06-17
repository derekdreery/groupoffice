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
 * The Note model
 */

class GO_Notes_Model_Note extends GO_Base_Db_ActiveRecord{
	
	protected $_columns=array(
		'id'=>array('type'=>PDO::PARAM_INT),
		'category_id'=>array('type'=>PDO::PARAM_INT),
		'user_id'=>array('type'=>PDO::PARAM_INT),
		'name'=>array('type'=>PDO::PARAM_STR,'required'=>true,'length'=>100),
		'content'=>array('type'=>PDO::PARAM_STR,'gotype'=>'textarea'),
		'ctime'=>array('type'=>PDO::PARAM_INT,'gotype'=>'unixtimestamp'),
		'mtime'=>array('type'=>PDO::PARAM_INT,'gotype'=>'unixtimestamp'),
		'files_folder_id'=>array('type'=>PDO::PARAM_INT)
	);
	
	public $linkType=4;
	
	/**
	 * 
	 * @var string The database table name
	 */
	
	public $tableName='no_notes';
	
	/*
	 * Points to a relation here
	 */
	public $aclField='category.acl_id';	
	
	protected $relations=array(
				'category' => array(self::BELONGS_TO, 'GO_Notes_Model_Category', 'category_id')
		);

	protected function getCacheAttributes(){
		return array(
				'name'=>$this->name,
				'type'=>'Note'
		);
	}
	
	protected function beforeSave() {
		parent::beforeSave();
		
		
		if(empty($this->files_folder_id) && isset(GO::modules()->files))
			$this->files_folder_id = GO_Files_Controller_Item::itemFilesFolder($this, $this->_buildFilesPath());
	}
	
	/**
	 * The files module will use this function.
	 */
	public function getFilesFolderId()
	{
		$this->category->getFilesFolderId();
		
		return 'notes/'.File::strip_invalid_chars($this->category->name).'/'.date('Y', $this->ctime).'/'.File::strip_invalid_chars($this->name);
	}
}