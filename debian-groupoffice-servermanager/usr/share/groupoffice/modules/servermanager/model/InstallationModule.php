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
 * @package GO.servermanager.model
 * @copyright Copyright Intermesh BV.
 * @author Michael de Hart mdhart@intermesh.nl
 */
 
/**
 * The GO_ServerManager_Model_InstallationModule
 * 
 * Keeps track of when a module was installed for the first time for trail period
 * The enabled boolean is there to see if the module is still enabled for the installation
 * The record can't be deleted from the database because installed_since value will be lost
 * 
 * @package GO.servermanager.model
 * 
 * @property string $name the modules directory name to be added to the config (PK)
 * @property int $installation_id the foreignkey to the installation record (PK)
 * @property int $ctime a unix timestamp that shown when the module was activated for the first time
 * @property int $mtime a unix timestamp that shows when the module was changed for the last time
 * @property boolean $enabled true if the module is used by the installation
 */
class GO_ServerManager_Model_InstallationModule extends GO_Base_Db_ActiveRecord 
{

	public $_usercount; //count of user that are using this module
	/*
	public function __construct($module, $installation)
	{
		$this->module_name = $module->id();
		$this->_module = $module;
		$this->installation_id = $installation->id;
		//$this->enabled = in_array($module->id(), $installation->getAllowedModules());
		
		parent::__construct();
	}
	*/
	public function tableName()
	{
		return 'sm_installation_modules';
	}
	
	public function primaryKey()
	{
		return array('name', 'installation_id');
	}
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function getModuleName()
	{
		$module = GO_Base_Module::findByModuleId($this->name);
		return $module->name();
	}
	/*
	public function getInstalledSinceText()
	{
		if(empty($this->ctime))
			return "Never";
		return $this->ctime;
	}*/
	
	public function getChecked()
	{
		//if($this->installation == null)
		//	return false;
		//else
			return in_array($this->name, $this->installation->getAllowedModules());
	}
	/**
	 * Some installation modules should not be shown in the list of available modules
	 * @return boolean returns true if the module with this name should be marked hidden
	 */
	public function isHidden()
	{
		$hiddenModules = array('servermanager','serverclient','users','groups','modules','postfixadmin');
		return in_array($this->name, $hiddenModules);
	}
	
	public function relations()
	{
		return array(
				'installation'=>array('type'=>self::BELONGS_TO, 'model'=>'GO_ServerManager_Model_Installation', 'field'=>'installation_id'),
		);
	}
	
	/**
	 * Loop through installation users and count all users that have access to this module
	 * @return int amount of user with access to this module
	 */
	public function getUsercount()
	{
		if($this->isNew)
			return 0;
		if($this->_usercount !== null)
			return $this->_usercount;
		$this->_usercount = 0;
		foreach($this->installation->users as $user)
		{
			if($user->allowedToModule($this->name))
				$this->_usercount++;
		}
		return $this->_usercount;
	}
	
	/**
	 * @return boolean true is this module is still in trail use
	 */
	public function isTrial()
	{
		//trail day time 24hours times 60 minutes times 60 seconds
		$trial_time_in_seconds = time()-($this->installation->automaticInvoice->trial_days * 24 * 60 * 60);
		return ($this->ctime > $trial_time_in_seconds );
	}
	
	/**
	 * returns this object as an array for json store
	 * @return type 
	 */
	public function toArray(){
		return array(
				'id'=>$this->name,
				'name'=>$this->getModuleName(),
				'usercount'=>$this->getUsercount(),
				'checked'=>$this->getChecked(),
				'ctime'=>$this->getAttribute('ctime', 'formatted')
		);
	}
}
