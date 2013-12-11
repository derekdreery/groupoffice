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
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */

/**
 * The GO_Files_Model_Folder model
 * 

 * @property int $user_id
 * @property int $folder_id
 * @property boolean $thumbs
 */
class GO_Files_Model_FolderPreference extends \GO\Base\Db\ActiveRecord {


	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Files_Model_FolderNotification
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}


	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'fs_folder_pref';
	}
	
	public function primaryKey() {
		return array('user_id', 'folder_id');
	}
}