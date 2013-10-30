<?php

class GO_Freebusypermissions_FreebusypermissionsModule extends GO_Base_Module{
	
	/**
	 * Initialize the listeners for the ActiveRecords
	 */
	public static function initListeners(){	
		GO_Calendar_Model_Event::model()->addListener('load', 'GO_Freebusypermissions_FreebusypermissionsModule', 'has_freebusy_access');
	}
	
	public function autoInstall() {
		return false;
	}
	
	public static function hasFreebusyAccess($request_user_id, $target_user_id){
		
		$fbAcl = GO_Freebusypermissions_FreebusypermissionsModule::getFreeBusyAcl($target_user_id);
		

		return GO_Base_Model_Acl::getUserPermissionLevel($fbAcl->acl_id, $request_user_id) > 0;
	}

	public static function loadSettings(&$settingsController, &$params, &$response, $user) {
		
		$acl = GO_Freebusypermissions_FreebusypermissionsModule::getFreeBusyAcl($user->id);
		
		if(!empty($acl))
			$response['data']['freebusypermissions_acl_id']=$acl->acl_id;
		
		return parent::loadSettings($settingsController, $params, $response, $user);
	}
	
	public static function getFreeBusyAcl($userId){
		
		$fbAcl = GO_Freebusypermissions_Model_FreeBusyAcl::model()->findSingleByAttribute('user_id', $userId);
		
		if(!$fbAcl){
			
			$acl = new GO_Base_Model_Acl();
			$acl->user_id = $userId;
			$acl->description = GO_Freebusypermissions_Model_FreeBusyAcl::model()->tableName();
			$acl->save();
			
			if($acl){
				$fbAcl = new GO_Freebusypermissions_Model_FreeBusyAcl();
				$fbAcl->user_id = $userId;
				$fbAcl->acl_id = $acl->id;
				$fbAcl->save();
			} else {
				$fbAcl = false;
			}		
		}
		return $fbAcl;
	}
}