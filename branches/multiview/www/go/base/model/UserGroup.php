<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *

 */

/**
 * 
 * The UserGroup model
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 * 
 * @package GO.base.model
 * 
 * @property int $user_id
 * @property int $group_id
 */
namespace GO\Base\Model;

class UserGroup extends \GO\Base\Db\ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return UserGroup 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function tableName() {
		return 'go_users_groups';
	}
  
	public function relations() {
		return array(
				'group' => array('type' => self::BELONGS_TO, 'model' => '\GO\Base\Model\Group', 'field' => 'group_id'),
		);
	}
  public function primaryKey() {
    return array('user_id','group_id');
  }
}