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

class GO_Addressbook_Controller_Site extends GO_Sites_Controller_Site{
	
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
	
	protected function actionAddContact($params){
		$this->contact = new GO_Addressbook_Model_Contact();
		
		if (GO_Base_Util_Http::isPostRequest()) {
			$this->contact->setAttributes($params);

			// This checks the 2 email fields
			if(isset($params['confirm_email']) && isset($params['email'])){
				if($params['email'] != $params['confirm_email'])
					GO_Base_Html_Error::setError(GOS::t('compareEmailError'), 'confirm_email');
			}

			$ok = GO_Base_Html_Error::checkRequired();
			GO_Base_Html_Error::validateModel($this->contact);
			
			if(!GO_Base_Html_Error::hasErrors() && $ok){
				$this->contact->save();
				$this->notifications->addNotification('addcontact', GOS::t('formSubmitNotification'), GO_Sites_NotificationsObject::NOTIFICATION_OK);
				$this->pageRedirect('addcontact');
			}
		}			
		$this->renderPage($params);
	}
}
