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
 * @property String $path
 * @property int $locked_user_id
 * @property int $status_id
 * @property int $ctime
 * @property int $mtime
 * @property int $size
 * @property int $user_id
 * @property String $comments
 * @property String $extension
 * @property int $expire_time
 * @property String $random_code
 */

class GO_Files_Model_File extends GO_Base_Db_ActiveRecord{

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Files_Model_File
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
		 return 'fs_files';
	 }

	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	 public function relations() {
		 return array();
	 }
}
