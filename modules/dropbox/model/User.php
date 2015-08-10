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
 * @package GO.modules.dropbox.model
 * @version $Id: User.php 18034 2014-01-27 10:18:31Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */
 
/**
 * The User model
 *
 * @package GO.modules.dropbox.model
 * @property string $access_token
 * @property string $dropbox_user_id
 */


namespace GO\Dropbox\Model;


class User extends \GO\Base\Db\ActiveRecord{
	public function tableName() {
		return 'dbx_users';
	}
	
	public function primaryKey() {
		return 'user_id';
	}
	
	public function relations() {
		return array('user'=>array('type'=>self::BELONGS_TO,'model'=>"GO\Base\Model\User","field"=>"user_id"));
	}
}