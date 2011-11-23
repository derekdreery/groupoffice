<?php
class GO_Addressbook_Controller_Addressbook extends GO_Base_Controller_AbstractModelController{
	
	/*
	 * This function initiates the contact filter by checked addressbooks.
	 */
	protected function getStoreMultiSelectProperties(){
		return array(
			'requestParam'=>'books',
			//'permissionsModel'=>'GO_Addressbook_Model_Addressbook'
			//'titleAttribute'=>'name'
		);
	}	
	
	protected $model = 'GO_Addressbook_Model_Addressbook';
	
		public function actionSearchSender($params) {
		$addressbooks = GO_Addressbook_Model_Addressbook::model()->find(
			GO_Base_Db_FindCriteria::newInstance()
				->addCondition('email',$params['email'])
		);
		
		$ab_ids = array();
		foreach ($addressbooks as $ab)
			$ab_ids[] = $ab->id;

		$criteria = GO_Base_Db_FindCriteria::newInstance()
			->addCondition('email',$params['email']);
		if (!empty($ab_ids))
			$criteria->addInCondition('addressbook_id', $ab_ids);
		
		$contacts = GO_Addressbook_Model_Contact::model()->find($criteria);		
		$response['total'] = count($contacts);
		$response['results']=array();

		foreach($contacts as $contact)
		{
			$addressbook = GO_Addressbook_Model_Addressbook::model()->findByPk($contact->addressbook_id);
			$res_contact['id']=$contact->id;
			$res_contact['name']=$contact->name.' ('.$addressbook->name.')';

			$response['results'][]=$res_contact;
		}
		return $response;
	}
	
	public function formatStoreRecord($record, $model, $store) {
		
		$record['user_name']=$model->user->name;
		if(GO::modules()->customfields){
			$record['contactCustomfields']=GO_Customfields_Controller_Category::getEnabledCategoryData("GO_Addressbook_Model_Contact", $model->id);
			$record['companyCustomfields']=GO_Customfields_Controller_Category::getEnabledCategoryData("GO_Addressbook_Model_Company", $model->id);
		}
		
		return parent::formatStoreRecord($record, $model, $store);
	}
	
}

