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
		
		$protocol = empty(GO::config()->servermanager_ssl) ? 'http' : 'https';
		$url = $protocol.'://'.$newTrial->name.'.'.GO::config()->servermanager_wildcard_domain;
		
		$body = str_replace('{product_name}', GO::config()->product_name, $body);
		$body = str_replace('{url}', $url, $body);
		$body = str_replace('{name}', $newTrial->first_name.' '.$newTrial->last_name, $body);
		$body = str_replace('{link}',GO::url("servermanager/trial/create", array('key'=>$newTrial->key), false, false), $body);
		$body = str_replace('{password}', $newTrial->password, $body);
			
		$pos = strpos($body,"\n");
		
		$subject = trim(substr($body, 0, $pos));
		$body = trim(substr($body, $pos));
		
					
		$message = GO_Base_Mail_Message::newInstance($subject,$body)
						->setFrom(GO::config()->webmaster_email, GO::config()->title)
						->addTo($newTrial->email, $newTrial->first_name.' '.$newTrial->last_name)
						->addBcc(GO::config()->webmaster_email);
		
		GO_Base_Mail_Mailer::newGoInstance()->send($message);
	}
	
	protected function actionNewTrial($params){
		
		if(empty(GO::config()->servermanager_trials_enabled))
			throw new Exception("Trials are not enabled. Set \$config['servermanager_trials_enabled']=true;");
		
		if(!isset(GO::config()->servermanager_wildcard_domain))
			throw new Exception("\$config['servermanager_wildcard_domain']='example.com'; is not defined in /etc/groupoffice/config.php");
		
		
		if (GO_Base_Util_Http::isPostRequest()) {
			
			GO_Base_Html_Error::checkRequired();			
				
			GO_Base_Html_Error::checkEmailInput($params);
			
			$newTrial =  new GO_ServerManager_Model_NewTrial();
			$newTrial->setAttributes($params);
			
			GO_Base_Html_Error::validateModel($newTrial);
						
			//var_dump(GO::session()->values['formErrors']);
			
					
			if(!GO_Base_Html_Error::hasErrors()){
			
				$this->_writeAddressbookEntries($params);
				
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
	
	private function _writeAddressbookEntries($params) {
		$companyModel = new GO_Addressbook_Model_Company();
		$companyEmptyAttributes = $companyModel->getAttributes('raw');
		$contactModel = new GO_Addressbook_Model_Contact();
		$contactEmptyAttributes = $contactModel->getAttributes('raw');
			
		foreach ($params as $k => $v) {
			$paramKeyStringArr = explode('_',$k);
			if ($paramKeyStringArr[0] == 'addressbook') {
				array_shift($paramKeyStringArr);
				switch ($paramKeyStringArr[0]) {
					case 'company' :
						array_shift($paramKeyStringArr);
						$attributeName = implode('_',$paramKeyStringArr);
						if (array_key_exists($attributeName,$companyEmptyAttributes))
							$companyModel->$attributeName = $v;
						break;
					case 'contact' :
						array_shift($paramKeyStringArr);
					default:
						$attributeName = implode('_',$paramKeyStringArr);
						if (array_key_exists($attributeName,$contactEmptyAttributes))
							$contactModel->$attributeName = $v;
						break;
				}
			} else {
				if (array_key_exists($k,$contactEmptyAttributes))
					$contactModel->$k = $v;
			}
		}

		$serverClient = new GO_Serverclient_HttpClient();
		$serverClient->groupofficeLogin(GO::config()->servermanager_trials_addressbook_server_url, GO::config()->servermanager_trials_addressbook_login_username, GO::config()->servermanager_trials_addressbook_login_password);
		
		if ($companyModel->isModified()) {
			$companyModel->addressbook_id = GO::config()->servermanager_trials_addressbook_id;			
			$serverClient->request(GO::config()->servermanager_trials_addressbook_server_url.'?r=addressbook/company/submit', $companyModel->getAttributes());
		}
		
		if ($contactModel->isModified()) {
			$contactModel->addressbook_id = GO::config()->servermanager_trials_addressbook_id;
			if ($companyModel->id > 0)
				$contactModel->company_id = $companyModel->id;
			$serverClient->request(GO::config()->servermanager_trials_addressbook_server_url.'?r=addressbook/contact/submit', $contactModel->getAttributes());
		}
	}
}
