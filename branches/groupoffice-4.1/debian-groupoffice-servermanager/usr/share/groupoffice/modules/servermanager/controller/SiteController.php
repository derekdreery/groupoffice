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
 * This controller was made for frontend actions
 * Usage requires the Sites module to be installed
 *
 * @package GO.modules.Addressbook
 * @version $Id: SiteContoller.php 7607 2011-09-20 10:07:50Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits wsmits@intermesh.nl
 */

class GO_Servermanager_Controller_Site extends GO_Sites_Controller_Site{
	

	/**
	 * Render a page for creating new trail installation
	 * @throws Exception calling when trails are disabled in config when no wildcard domain is specified
	 */
	protected function actionNewTrial(){
		
		if(empty(GO::config()->servermanager_trials_enabled))
			throw new Exception("Trials are not enabled. Set \$config['servermanager_trials_enabled']=true;");
		
		if(!isset(GO::config()->servermanager_wildcard_domain))
			throw new Exception("\$config['servermanager_wildcard_domain']='example.com'; is not defined in /etc/groupoffice/config.php");
		
		
		$newTrial =  new GO_ServerManager_Model_NewTrial();
		
		if (GO_Base_Util_Http::isPostRequest()) {
			
			$newTrial->setAttributes($_POST['NewTrial']);
			if($newTrial->validate())
			{	
				$newTrial->save();

				$tplStr = file_get_contents($this->getTemplatePath().'views/servermanager/emails/trial.txt');
				$newTrial->sendMail($tplStr);

				$this->redirect(array('servermanager/site/trialcreated','key'=>$newTrial->key));
			}
		}		
		
		$this->render('newtrial', array('model' => $newTrial));
	}
	
	public function actionTrialCreated(){
		$newTrial = GO_ServerManager_Model_NewTrial::model()->findSingleByAttribute('key', $_REQUEST['key']);
		$this->render('trialcreated', array('model' => $newTrial));
	}
//	
//	private function _writeAddressbookEntries($params) {
//		if(!empty(GO::config()->servermanager_trials_addressbook_server_url)){
//			$companyModel = new GO_Addressbook_Model_Company();
//			$companyEmptyAttributes = $companyModel->getAttributes('raw');
//			$contactModel = new GO_Addressbook_Model_Contact();
//			$contactEmptyAttributes = $contactModel->getAttributes('raw');
//
//			foreach ($params as $k => $v) {
//				$paramKeyStringArr = explode('_',$k);
//				if ($paramKeyStringArr[0] == 'addressbook') {
//					array_shift($paramKeyStringArr);
//					switch ($paramKeyStringArr[0]) {
//						case 'company' :
//							array_shift($paramKeyStringArr);
//							$attributeName = implode('_',$paramKeyStringArr);
//							if (array_key_exists($attributeName,$companyEmptyAttributes))
//								$companyModel->$attributeName = $v;
//							break;
//						case 'contact' :
//							array_shift($paramKeyStringArr);
//						default:
//							$attributeName = implode('_',$paramKeyStringArr);
//							if (array_key_exists($attributeName,$contactEmptyAttributes))
//								$contactModel->$attributeName = $v;
//							break;
//					}
//				} else {
//					if (array_key_exists($k,$contactEmptyAttributes))
//						$contactModel->$k = $v;
//				}
//			}
//
//			$serverClient = new GO_Serverclient_HttpClient();
//			$serverClient->groupofficeLogin(GO::config()->servermanager_trials_addressbook_server_url, GO::config()->servermanager_trials_addressbook_login_username, GO::config()->servermanager_trials_addressbook_login_password);
//
//			if ($companyModel->isModified()) {
//				$companyModel->addressbook_id = GO::config()->servermanager_trials_addressbook_id;			
//				$serverClient->request(GO::config()->servermanager_trials_addressbook_server_url.'?r=addressbook/company/submit', $companyModel->getAttributes());
//			}
//
//			if ($contactModel->isModified()) {
//				$contactModel->addressbook_id = GO::config()->servermanager_trials_addressbook_id;
//				if ($companyModel->id > 0)
//					$contactModel->company_id = $companyModel->id;
//				$serverClient->request(GO::config()->servermanager_trials_addressbook_server_url.'?r=addressbook/contact/submit', $contactModel->getAttributes());
//			}
//		}
//	}
}
