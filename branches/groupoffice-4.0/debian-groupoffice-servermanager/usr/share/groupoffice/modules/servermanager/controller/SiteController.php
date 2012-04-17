<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * The GO_Addressbook_Controller_Site controller
 *
 * @package GO.modules.Addressbook
 * @version $Id: SiteContoller.php 7607 2011-09-20 10:07:50Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits wsmits@intermesh.nl
 */

class GO_Servermanager_Controller_Site extends GO_Sites_Controller_Site{
	
	/**
	 * Sets the access permissions for guests
	 * Defaults to '*' which means that all functions can be accessed by guests.
	 * 
	 * @return array List of all functions that can be accessed by guests 
	 */
	protected function allowGuests() {
		return array('*');
	}
	
	protected function ignoreAclPermissions() {
		return array('*');
	}
	
	
	private function _sendMail(GO_ServerManager_Model_NewTrial $newTrial){
		$body = file_get_contents($this->rootTemplatePath.'modules/servermanager/emails/trial.txt');
		
		$body = str_replace('{product_name}', GO::config()->product_name, $body);
		$body = str_replace('{url}', $url, $body);
		$body = str_replace('{name}', $newTrial->first_name.' '.$newTrial->last_name, $body);
		$body = str_replace('{link}',GO::url("servermanager/trial/create", array('key'=>$newTrial->key), false, false), $body);
		$body = str_replace('{password}', $newTrial->password, $body);
			
		$pos = strpos($body,"\n");
		
		$subject = trim(substr($body, 0, $pos));
		$body = trim(substr($body, $pos));
		
		$protocol = empty(GO::config()->servermanager_ssl) ? 'http' : 'https';
		
		$url = $protocol.'://'.$newTrial->name.'.'.GO::config()->servermanager_wildcard_domain;
			
		$message = GO_Base_Mail_Message::newInstance($subject,$body)
						->setFrom(GO::config()->webmaster_email, GO::config()->title)
						->addTo($newTrial->email, $newTrial->first_name.' '.$newTrial->last_name)
						->addBcc(GO::config()->webmaster_email);
		
		GO_Base_Mail_Mailer::newGoInstance()->send($message);
	}
	
	protected function actionNewTrial($params){
		
		if(empty(GO::config()->servermaner_trials_enabled))
			throw new Exception("Trials are not enabled. Set \$config['servermaner_trials_enabled']=true;");
		
		if(!isset(GO::config()->servermanager_wildcard_domain))
			throw new Exception("\$config['servermanager_wildcard_domain']='example.com'; is not defined in /etc/groupoffice/config.php");
		
		
		if (GO_Base_Util_Http::isPostRequest()) {
			
			GO_Base_Html_Error::checkRequired();			
				
			
			$newTrial =  new GO_ServerManager_Model_NewTrial();
			$newTrial->setAttributes($params);
			
			GO_Base_Html_Error::validateModel($newTrial);
			
			//var_dump(GO::session()->values['formErrors']);
			
					
			if(!GO_Base_Html_Error::hasErrors()){
				
				$newTrial->save();
				
				$this->_sendMail($newTrial);
				
				$this->pageRedirect('trialcreated', array('key'=>$newTrial->key));
			}
		}		
		
		$this->renderPage($params);
	}
	
	public function actionTrialCreated($params){
		$this->newTrial = GO_ServerManager_Model_NewTrial::model()->findSingleByAttribute('key', $params['key']);
		$this->renderPage($params);
	}
}
