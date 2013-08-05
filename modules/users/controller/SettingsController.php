<?php
class GO_Users_Controller_Settings extends GO_Base_Controller_AbstractController{
	
	protected function actionLoad($params) {
		$response = array();
		$settings =  GO_Users_Model_Settings::load();
		
		if(empty($settings->register_email_subject))
			$settings->register_email_subject = GO::t('register_email_subject','users');
		
		if(empty($settings->register_email_body))
			$settings->register_email_body = GO::t('register_email_body','users');
		
		// Load the custom field categories of the contact model
		if(GO::modules()->customfields){
			$tabs = GO_Users_Model_CfSettingTab::model()->find();
			foreach($tabs as $t)
				$response['tab_cf_cat_'.$t->cf_category_id]=true;
		}
		
		$responseData = array_merge($response,$settings->getArray());
		
		return array(
				'success'=>true,
				'data'=>$responseData
		);
	}
	
	protected function actionSubmit($params) {
		
		if(GO::modules()->addressbook && GO::modules()->customfields){
			
			// Remove all existing records from the table
			$tabs = GO_Users_Model_CfSettingTab::model()->find();
			foreach($tabs as $t)
				$t->delete();
			
			$contactClassName = GO_Addressbook_Model_Contact::model()->className();
			$customfieldsCategories = GO_Customfields_Model_Category::model()->findByModel($contactClassName);

			// Add the posted records back to the table (go_cf_setting_tabs)
			foreach($customfieldsCategories as $cfc){
				if(isset($params['tab_cf_cat_'.$cfc->id])){
					$cft = new GO_Users_Model_CfSettingTab();
					$cft->cf_category_id = $cfc->id;
					$cft->save();
				}
			}
		}
	
		$settings =  GO_Users_Model_Settings::load();
		
		return array(
				'success'=>$settings->saveFromArray($params),
				'data'=>$settings->getArray()
		);
	}

	/**
	 * Get all customfield categories of the contact model
	 * 
	 * @param type $params
	 */
	public function actionLoadContactCustomfieldCategories($params){
		$response = array();
		$customfieldsCategoriesArray = array();
		
		$contactClassName = GO_Addressbook_Model_Contact::model()->className();
		$customfieldsCategories = GO_Customfields_Model_Category::model()->findByModel($contactClassName);

		foreach($customfieldsCategories as $cfc)
			$customfieldsCategoriesArray[] = $cfc->getAttributes();
		
		$response['results'] = $customfieldsCategoriesArray;
		$response['total'] = count($customfieldsCategoriesArray);
		$response['success'] = true;
		
		return $response;
	}	
}