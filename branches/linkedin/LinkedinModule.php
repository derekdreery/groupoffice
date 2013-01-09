<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * 
 * The Notes module maintenance class
 * 
 */
class GO_Linkedin_LinkedinModule extends GO_Base_Module{
	
	public function autoInstall() {
		return true;
	}
	
	public function author() {
		return 'WilmarVB';
	}
	
	public function authorEmail() {
		return 'wilmar@intermesh.nl';
	}
	
	public static function initListeners() {
		$c = new GO_Addressbook_Controller_Addressbook();
		$c->addListener('load', 'GO_Linkedin_LinkedinModule', 'loadLinkedinInfo');
		$c->addListener('submit', 'GO_Linkedin_LinkedinModule', 'submitLinkedinInfo');
		
		GO_Addressbook_Model_Contact::model()->addListener('display', 'GO_Linkedin_LinkedinModule', 'displayLinkedinInfo');
	}
	
	public static function loadLinkedinInfo(GO_Addressbook_Controller_Addressbook &$abController,&$response, GO_Addressbook_Model_Addressbook &$abModel, &$params) {
		$autoImportModel = GO_Linkedin_Model_AutoImport::model()->findByPk($abModel->id);
		
		$response['data']['auto_linkedin_import_set'] = !empty($autoImportModel);
		$response['data']['auto_linkedin_import_enabled'] = !empty($autoImportModel->auto_import_enabled);
		
	}

	public static function submitLinkedinInfo(GO_Addressbook_Controller_Addressbook &$abController, &$response, GO_Addressbook_Model_Addressbook &$abModel, &$params, $modifiedAttributes ) {
		$addressbookId = $abModel->id;
		$linkedinUserAddressbook = GO_Linkedin_Model_AutoImport::model()->findByPk($addressbookId);
		$linkedinUserAddressbook->auto_import_enabled = !empty($params['auto_linkedin_import_enabled']);
		$linkedinUserAddressbook->save();
	}
	
	public static function displayLinkedinInfo(GO_Addressbook_Controller_Contact &$contactController, &$response, GO_Addressbook_Model_Contact &$contactModel, &$params) {
		
		$linkedinInfo = $contactModel->linkedinProfile;
		
		if (!empty($linkedinInfo)) {
			// TODO :: voeg linkedin info toe aan response
		}
		
	}
	
}