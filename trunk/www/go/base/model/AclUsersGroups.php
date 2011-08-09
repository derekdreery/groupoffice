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
 * @property int $acl_id
 * @property int $user_id
 * @property int $group_id
 * @property int $level {@see GO_Base_Model_Acl::READ_PERMISSION etc}
 */
class GO_Base_Model_AclUsersGroups extends GO_Base_Db_ActiveRecord {

	public function tableName() {
		return 'go_acl';
	}
  
  public function primaryKey() {
    return array('acl_id','user_id','group_id');
  }
}