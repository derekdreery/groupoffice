<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

class GO_Addressbook_Controller_Site extends GO_Site_Components_Controller{
	
	/**
	 * Sets the access permissions for guests
	 * Defaults to '*' which means that all functions can be accessed by guests.
	 * 
	 * @return array List of all functions that can be accessed by guests 
	 */
	protected function allowGuests() {
		return array('*');
	}
	
	protected function ignoreAclPermissions(){
		return array('*');
	}
	
	
	protected function actionContact(){	
		//GOS::site()->config->contact_addressbook_id;	
		
		if (\GO_Base_Util_Http::isPostRequest()) {
			$addressbookModel = \GO_Addressbook_Model_Addressbook::model()->findSingleByAttribute('name', $_POST['Addressbook']['name']);
			if (!$addressbookModel)
				throw new \Exception(sprintf(\GO::t('addressbookNotFound','defaultsite'),$_POST['Addressbook']['name']));
			
			$contactModel = \GO_Addressbook_Model_Contact::model()->findSingleByAttributes(array('email'=>$_POST['Contact']['email'],'addressbook_id'=>$addressbookModel->id));
			if (!$contactModel) {
				$contactModel = new \GO_Addressbook_Model_Contact();
				$contactModel->addressbook_id = $addressbookModel->id;
			}
			$contactModel->setValidationRule('first_name', 'required', true);
			$contactModel->setValidationRule('last_name', 'required', true);
			$contactModel->setValidationRule('email', 'required', true);
			
			$companyModel = \GO_Addressbook_Model_Company::model()->findSingleByAttributes(array('name'=>$_POST['Company']['name'],'addressbook_id'=>$addressbookModel->id));
			if (!$companyModel) {
				$companyModel = new \GO_Addressbook_Model_Company();
				$companyModel->addressbook_id = $addressbookModel->id;
			}
			$companyModel->setValidationRule('name','required',true);
			
			$companyModel->setAttributes($_POST['Company']);
			if ($companyModel->validate())
				$companyModel->save();
			
			$contactModel->setAttributes($_POST['Contact']);

			if($contactModel->validate()){
				$saveSuccess = $contactModel->save();

				if ($saveSuccess) {
					// Add to mailings.
					$addresslists = !empty($_POST['Addresslist']) ? $_POST['Addresslist'] : array();
					foreach ($addresslists as $addresslistName=>$checked) {
						if (!empty($checked)) {
							$addresslistModel = \GO_Addressbook_Model_Addresslist::model()->findSingleByAttribute('name',$addresslistName);
							if ($addresslistModel) {
								$addresslistContactModel = \GO_Addressbook_Model_AddresslistContact::model()->findSingleByAttributes(array('contact_id'=>$contactModel->id,'addresslist_id'=>$addresslistModel->id));
								if (!$addresslistContactModel) {
									$addresslistContactModel = new \GO_Addressbook_Model_AddresslistContact();
									$addresslistContactModel->contact_id = $contactModel->id;
									$addresslistContactModel->addresslist_id = $addresslistModel->id;
									$addresslistContactModel->save();
								}
							}
						}
					}
					$this->render('contactform_done');
				} else {
					$this->render('contactform', array('contact'=>$contactModel,'company'=>$companyModel,'addressbook'=>$addressbookModel));
				}
			
				
			}else
			{
				$validationErrors = $contactModel->getValidationErrors();
				foreach ($validationErrors as $valError)
					echo $valError;
				$this->render('contactform', array('contact'=>$contactModel,'company'=>$companyModel,'addressbook'=>$addressbookModel));
			}
						
		}	else {
			$addressbookModel = new \GO_Addressbook_Model_Addressbook();
			$contactModel = new \GO_Addressbook_Model_Contact();
			$companyModel = new \GO_Addressbook_Model_Company();
			$this->render('contactform', array('contact'=>$contactModel,'company'=>$companyModel,'addressbook'=>$addressbookModel));
		}
	}

}
