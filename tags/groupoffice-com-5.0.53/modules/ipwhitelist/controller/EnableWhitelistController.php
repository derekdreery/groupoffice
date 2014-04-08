<?php
//class GO_Ipwhitelist_Controller_EnableWhitelist extends GO_Base_Controller_AbstractJsonController {
//	
//	protected function actionSetWhitelist($params) {
//		
//		$enable = $params['enable_whitelist'];
//		$groupId = $params['group_id'];
//		
//		if ($enable) {
//			
//			$enableWhitelistModel = GO_Ipwhitelist_Model_EnableWhitelist::model()->findByPk($groupId);
//			if (!$enableWhitelistModel) {
//				$enableWhitelistModel = new GO_Ipwhitelist_Model_EnableWhitelist();
//				$enableWhitelistModel->group_id = $groupId;
//				$enableWhitelistModel->save();
//			}
//			
//		} else {
//			$enableWhitelistModel = GO_Ipwhitelist_Model_EnableWhitelist::model()->findByPk($groupId);
//			if ($enableWhitelistModel)
//				$enableWhitelistModel->delete();
//		}
//		
//		echo json_encode(array('success'=>true));
//		
//	}
//	
//}
?>
