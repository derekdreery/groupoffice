<?php
class GO_Addressbook_Controller_Addressbook extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Addressbook_Model_Addressbook';
	
	public function formatStoreRecord($record, $model, $store) {
		
		$record['user_name']=$model->user->name;
		if(GO::modules()->customfields){
			$record['contactCustomfields']=GO_Customfields_Controller_Category::getEnabledCategoryData("GO_Addressbook_Model_Contact", $model->id);
			$record['companyCustomfields']=GO_Customfields_Controller_Category::getEnabledCategoryData("GO_Addressbook_Model_Company", $model->id);
		}
		
		return parent::formatStoreRecord($record, $model, $store);
	}
	
}

