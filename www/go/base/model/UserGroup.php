<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: Group.php 7607 2011-08-04 13:41:42Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

/**
 * 
 * The Group model
 * 
 * @property int $id
 * @property String $name
 * @property int $user_id
 * @property int $acl_id
 * @property bool $admin_only
 */
class GO_Base_Model_UserGroup extends GO_Base_Db_ActiveRecord {

	public function tableName() {
		return 'go_users_groups';
	}
  
  public function primaryKey() {
    return array('user_id','group_id');
  }
  
  
  public function relations() {
    
    return array(
				'groups' => array('type'=>self::HAS_MANY, 'model'=>'GO_Base_Model_Group', 'field'=>'group_id', 'delete'=>true),
        'users' => array('type'=>self::HAS_MANY, 'model'=>'GO_Base_Model_User', 'field'=>'group_id', 'delete'=>true),
    );
  }
}