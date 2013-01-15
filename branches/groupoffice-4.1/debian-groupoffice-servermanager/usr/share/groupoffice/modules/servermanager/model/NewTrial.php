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
 * The GO_ServerManager_Model_Installation model
 *

 * @property string $title
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $key
 * @property int $ctime
 */

class GO_ServerManager_Model_NewTrial extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_ServerManager_Model_NewTrial
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'sm_new_trials';
	}
	
	public function primaryKey() {
		return array("name");
	}
	protected function init() {
		
		$this->columns['name']['required']=true;
		$this->columns['name']['unique']=true;
		$this->columns['name']['regex']='/^[a-z0-9-_]*$/';
		
		$this->columns['first_name']['required']=true;

		$this->columns['last_name']['required']=true;
		$this->columns['email']['required']=true;
		$this->columns['title']['required']=true;
		
		
		return parent::init();
	}
	
	public function attributeLabels() {
		return array_merge(parent::attributeLabels(), array(
				'name'=>GO::t("domainName", "servermanager"),
				'first_name'=>GO::t("strFirstName"),
				'middle_name'=>GO::t("strMiddleName"),
				'last_name'=>GO::t("strLastName"),
				'email'=>GO::t("strEmail"),
		));
	}
	
	protected function beforeSave() {
		
		$this->password = GO_Base_Util_String::randomPassword(6);
		$this->key = md5($this->password.$this->name);
		
		return parent::beforeSave();
	}
	
	public function validate() {
		$installation = new GO_ServerManager_Model_Installation();
		$installation->name = $this->name.'.'.GO::config()->servermanager_wildcard_domain;
		
		if(!$installation->validate()){
			$this->setValidationError('name', implode("\n", $installation->getValidationErrors()));
		}
							
		return parent::validate();
	}
	
	public function sendMail($tplStr){
	
		
		$protocol = empty(GO::config()->servermanager_ssl) ? 'http' : 'https';
		$url = $protocol.'://'.$this->name.'.'.GO::config()->servermanager_wildcard_domain;
		
		$tplStr = str_replace('{product_name}', GO::config()->product_name, $tplStr);
		$tplStr = str_replace('{url}', $url, $tplStr);
		$tplStr = str_replace('{name}', $this->first_name.' '.$this->last_name, $tplStr);
		$tplStr = str_replace('{link}',GO::url("servermanager/trial/create", array('key'=>$this->key), false, false), $tplStr);
		$tplStr = str_replace('{password}', $this->password, $tplStr);
			
		$pos = strpos($tplStr,"\n");
		
		$subject = trim(substr($tplStr, 0, $pos));
		$body = trim(substr($tplStr, $pos));
		
					
		$message = GO_Base_Mail_Message::newInstance($subject,$body)
						->setFrom(GO::config()->webmaster_email, GO::config()->title)
						->addTo($this->email, $this->first_name.' '.$this->last_name)
						->addBcc(GO::config()->webmaster_email);
		
		return GO_Base_Mail_Mailer::newGoInstance()->send($message);
	}


	
}
