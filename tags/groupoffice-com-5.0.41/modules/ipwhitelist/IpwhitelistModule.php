<?php

class GO_Ipwhitelist_IpwhitelistModule extends GO_Base_Module{
	
	public function depends() {
		return array("groups");
	}

	public static function initListeners() {
		$invoiceController = new GO_Core_Controller_Auth();
		$invoiceController->addListener('beforelogin', "GO_Ipwhitelist_IpwhitelistModule", "checkIpAddress");
		$groupController = new GO_Groups_Controller_Group();
		$groupController->addListener('load', "GO_Ipwhitelist_IpwhitelistModule", "getWhitelistEnabled");
		$groupController->addListener('submit', 'GO_Ipwhitelist_IpwhitelistModule', 'setWhitelist');
	}
	
	public function autoInstall() {
		return false;
	}
	
	public function adminModule() {
		return true;
	}
	
	public static function checkIpAddress( array &$params, array &$response ) {
		
		$oldIgnoreAcl = GO::setIgnoreAclPermissions();
		$userModel = GO_Base_Model_User::model()->findSingleByAttribute('username',$params['username']);
		if (!$userModel)
			return true;
				
		$allowedIpAddresses = array();//"127.0.0.1");
		$whitelistIpAddressesStmt = GO_Ipwhitelist_Model_IpAddress::model()->find(
			GO_Base_Db_FindParams::newInstance()
				->select('t.ip_address')
				->joinModel(array(
					'model'=>'GO_Ipwhitelist_Model_EnableWhitelist',
					'localTableAlias'=>'t',
					'localField'=>'group_id',
					'foreignField'=>'group_id',
					'tableAlias'=>'ew',
					'type'=>'INNER'
				))
				->joinModel(array(
					'model'=>'GO_Base_Model_UserGroup',
					'localTableAlias'=>'ew',
					'localField'=>'group_id',
					'foreignField'=>'group_id',
					'tableAlias'=>'usergroup',
					'type'=>'INNER'
				))
				->criteria(GO_Base_Db_FindCriteria::newInstance()
					->addCondition('user_id',$userModel->id,'=','usergroup')
				)
		);
		if (!empty($whitelistIpAddressesStmt) && $whitelistIpAddressesStmt->rowCount() > 0) {
			
			foreach ($whitelistIpAddressesStmt as $ipAddressModel) {
//				$enabledWhitelistModel = GO_Ipwhitelist_Model_EnableWhitelist::model()->findByPk($groupModel->id);
//				if (!empty($enabledWhitelistModel)) {
//					$ipAddressesStmt = GO_Ipwhitelist_Model_IpAddress::model()->findByAttribute('group_id',$groupModel->id);
//					foreach ($ipAddressesStmt as $ipAddressModel) {
						if (!in_array($ipAddressModel->ip_address,$allowedIpAddresses))
							$allowedIpAddresses[] = $ipAddressModel->ip_address;
//					}
//				}
			}
			
		}
		
		GO::setIgnoreAclPermissions($oldIgnoreAcl);
		
		if (count($allowedIpAddresses)>0 && !in_array($_SERVER['REMOTE_ADDR'],$allowedIpAddresses)) {
			$response['feedback'] = sprintf(GO::t('wrongLocation','ipwhitelist'),$_SERVER['REMOTE_ADDR']);
			$response['success'] = false;
			return false;
		}
		
		return true;
	}
	
	public static function setWhitelist(GO_Groups_Controller_Group $groupController, array &$response, GO_Base_Model_Group $groupModel, array &$params, array $modifiedAttributes) {
				
		$enable = $params['enable_whitelist'];
		$groupId = $groupModel->id;
		
		if ($enable) {
			
			$enableWhitelistModel = GO_Ipwhitelist_Model_EnableWhitelist::model()->findByPk($groupId);
			if (!$enableWhitelistModel) {
				$enableWhitelistModel = new GO_Ipwhitelist_Model_EnableWhitelist();
				$enableWhitelistModel->group_id = $groupId;
				$enableWhitelistModel->save();
			}
			
		} else {
			$enableWhitelistModel = GO_Ipwhitelist_Model_EnableWhitelist::model()->findByPk($groupId);
			if ($enableWhitelistModel)
				$enableWhitelistModel->delete();
		}
	}

	public static function getWhitelistEnabled(GO_Groups_Controller_Group $groupController, array &$response, GO_Base_Model_Group $groupModel, array &$params) {
		$enabledWhitelistModel = GO_Ipwhitelist_Model_EnableWhitelist::model()->findByPk($groupModel->id);
		$response['data']['enable_whitelist'] = !empty($enabledWhitelistModel);
	}
	
}