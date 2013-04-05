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

class GO_Addressbook_Controller_Site extends GO_Sites_Components_AbstractFrontController{
	
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
		$contact = new GO_Addressbook_Model_Contact();
		$contact->setValidationRule('first_name', 'required', true);
		$contact->setValidationRule('last_name', 'required', true);
		$contact->setValidationRule('email', 'required', true);
		$contact->setValidationRule('comment', 'required', true);
		
		//GOS::site()->config->contact_addressbook_id;	
		
		if (GO_Base_Util_Http::isPostRequest()) {
			$contact->setAttributes($_POST['Contact']);

			if($contact->validate()){
				$contact->save();
			}else
			{
//				var_dump($contact->getValidationErrors());
			}
		}			
		$this->render('contact', array('contact'=>$contact));
	}
}
