<?php

/**
 * Group-Office
 * 
 * Copyright Intermesh BV. 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @package GO.modules.servermanager.model
 * @version $Id: example.php 7607 20120101Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */
 
/**
 * The GO_ServerManager_Model_InstallationUser model
 *
 * @package GO.modules.servermanager.model
 * @property int $installation_id
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $username
 * @property string $email
 * @property int $ctime
 * @property int $lastlogin
 * @property boolean $enabled
 */

class GO_ServerManager_Model_InstallationUser extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_ServerManager_Model_Installation
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'sm_installation_users';
	}
	
	public function relations() {
		return array(
				'modules' => array('type'=>self::HAS_MANY, 'model'=>'GO_ServerManager_Model_InstallationUserModule', 'field'=>'user_id', 'delete'=>true)
				);
	}

}
