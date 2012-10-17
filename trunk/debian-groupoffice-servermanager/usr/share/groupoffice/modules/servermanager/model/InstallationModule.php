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
 * 
 * @property GO_ServerManager_Model_Installation $installation the installation this module was installed for
 */
class GO_ServerManager_Model_InstallationModule extends GO_Base_Db_ActiveRecord 
{

	public $_usercount; //count of user that are using this module

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
		return GO::t('name', $this->name);
		//$module = GO_Base_Module::findByModuleId($this->name);
		//return $module->name();
	}
	
	public function getChecked()
	{
		//if($this->installation == null)
		//	return false;
		//else
			return in_array($this->name, $this->installation->getAllowedModules());
	}
	/**
	 * Some installation modules should not be shown in the list of available modules
	 * 
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
				'modulePrice'=>array('type'=>self::BELONGS_TO, 'model'=>'GO_ServerManager_Model_ModulePrice', 'field'=>'name'),
		);
	}
	
	/**
	 * Loop through installation users and count all users that have access to this module
	 * 
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
	 * Is this installation module in trial use?
	 * 
	 * @return boolean true is this module is payed and still in trail use
	 */
	public function isTrial()
	{
		if($this->modulePrice != null && $this->modulePrice->price_per_month > 0)
			return $this->trialDaysLeft > 0;
		else
			return false;
		
	}
	
	/**
	 * Get the amount of days this module can be used in trial mode
	 * If the installation time is lower than today minus the specified trial days
	 * the method will return 0
	 * 
	 * @return int the amount of days the trial period has left.
	 */
	public function getTrialDaysLeft()
	{
		if(empty($this->ctime)) 
			return $this->installation->trial_days;
		$trial_end_stamp = GO_Base_Util_Date::date_add($this->ctime, $this->installation->trial_days);
		
		$seconds_to_go = $trial_end_stamp - time();
		$days_to_go = $seconds_to_go / 60 / 60 / 24;
		
		$days_left = ($days_to_go > 0) ? ceil($days_to_go) : 0;
		
		return $days_left;
	}
	
	/**
	 * Returns this object as an array for json store
	 * 
	 * @return type 
	 */
	public function toArray(){
		return array(
				'id'=>$this->name,
				'name'=>$this->getModuleName(),
				'usercount'=>$this->getUsercount(),
				'checked'=>$this->getChecked(),
				'ctime'=>$this->getAttribute('ctime', 'formatted'),
				'isTrial'=>$this->isTrial(),
				'trialDaysLeft'=>$this->trialDaysLeft
		);
	}
	
	/**
	 * Send an email to the installations admin_email
	 * Will not send an email when nog in trial mode
	 * 
	 * @return boolean true if mail was send correctly
	 */
	public function sendTrialTimeLeftMail()
	{
		if(!$this->isTrial())
			return true;
		
		$message = GO_Base_Mail_Message::newInstance();
		$message->setSubject(vsprintf("Trial period for %s module",array($this->getModuleName()) )); //TODO: translate
		
		$fromName = GO::config()->title;
	
		$parts = explode('@', GO::config()->webmaster_email);
		$fromEmail = 'noreply@'.$parts[1];
		
		$toEmail = $this->installation->config['webmaster_email'];

		$emailBody = GO::t('module_trial_email_body','servermanager'); //TODO: add to translation
		$emailBody = vsprintf($emailBody,array($this->getModuleName(), $this->trialDaysLeft));
		
		$message->setBody($emailBody);
		$message->addFrom($fromEmail,$fromName);
		$message->addTo($toEmail);
		
		return GO_Base_Mail_Mailer::newGoInstance()->send($message);
	}
}
