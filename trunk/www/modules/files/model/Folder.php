<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: GO_Files_Model_Folder.php 7607 2011-09-01 15:44:36Z <<USERNAME>> $
 * @copyright Copyright Intermesh
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */  

/**
 * The GO_Files_Model_Folder model
 * 
 * @property int $user_id
 * @property int $id
 * @property int $parent_id
 * @property String $name
 * @property String $path
 * @property Boolean $visible
 * @property int $acl_id
 * @property String $comments
 * @property Boolean $thumbs
 * @property int $ctime
 * @property int $mtime
 * @property Boolean $readonly
 * @property String $cm_state
 * @property int $apply_state
 */

class GO_Files_Model_Folder extends GO_Base_Db_ActiveRecord{

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Files_Model_Folder
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}


	/**
	 * Enable this function if you want this model to check the acl's automatically.
	 */
	// public function aclField(){
	//	 return 'acl_id';	
	// }

	/**
	 * Returns the table name
	 */
	 public function tableName() {
		 return 'fs_folders';
	 }

	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	 public function relations() {
		 return array();
	 }
}