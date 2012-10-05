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
 * @copyright Copyright Intermesh BV
 * @version $Id InstallationUser.php 2012-09-03 09:34:14 mdhart $
 * @author Michael de Hart <mdehart@intermesh.nl> 
 * @package GO.servermanager,model
 */
/**
 * Activerecord for every user per installations database
 *
 * @package GO.servermanager.model
 * @copyright Copyright Intermesh
 * @version $Id InstallationUser.php 2012-09-03 09:34:14 mdhart $ 
 * @author Michael de Hart <mdehart@intermesh.nl> 
 *
 * @property int $user_id
 * @property int $installation_id
 * @property string $username
 * @property string $used_modules
 * @property int $ctime
 * @property int $lastlogin
 * @property boolean $enabled
 */

class GO_ServerManager_Model_InstallationUser extends GO_Base_Db_ActiveRecord {

	public $modules;
	
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	public function primaryKey()
	{
		return array('user_id', 'installation_id');
	}
	
	protected function init()
	{
		$this->columns['lastlogin']['gotype'] = 'unixtimestamp';
		$this->modules = !empty($this->used_modules) ? explode(',', $this->used_modules) : array();
	}
	
	protected function beforeSave()
	{
		$this->used_modules = implode(',', $this->modules);
		return parent::beforeSave();
	}
	public function addModule($module_id)
	{
		$this->modules[] = $module_id;
	}
	public function setAttributesFromUser($user)
	{
		$this->user_id = $user->id;
		$this->lastlogin = $user->lastlogin;
		$this->enabled = $user->enabled;
		$this->username = $user->username;
	}
	
	public function allowedToModule($module_id)
	{
		return in_array($module_id, $this->modules);
	}

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'sm_installation_users';
	}
	
	/**
	 * User is still in trail or does the client have to pay?
	 * @return boolean true when the user is still in trail period
	 */
	public function isTrial()
	{
		//trail day time 24hours times 60 minutes times 60 seconds
		$trail_time_in_seconds = time()-$this->installation->automaticInvoice->trailTimeInSeconds;
		return ($this->ctime > $trail_time_in_seconds );
	}

}
