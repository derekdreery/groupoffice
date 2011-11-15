<?php
class GO_Addressbook_Controller_Company extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Addressbook_Model_Company';
	
	protected function afterDisplay(&$response, &$model, &$params) {
				
		$response['data']['google_maps_link']=GO_Base_Util_Common::googleMapsLink(
						$model->address, $model->address_no,$model->city, $model->country);
		
		$response['data']['formatted_address']=nl2br(GO_Base_Util_Common::formatAddress(
						$model->country, $model->address, $model->address_no,$model->zip, $model->city, $model->state
						));
		
		$response['data']['post_google_maps_link']=GO_Base_Util_Common::googleMapsLink(
						$model->post_address, $model->post_address_no,$model->post_city, $model->post_country);
		
		$response['data']['post_formatted_address']=nl2br(GO_Base_Util_Common::formatAddress(
						$model->post_country, $model->post_address, $model->post_address_no,$model->post_zip, $model->post_city, $model->post_state
						));
		
		$response['data']['employees']=array();
		$stmt = $model->contacts();
		while($contact = $stmt->fetch()){
			$response['data']['employees'][]=array(
					'id'=>$contact->id,
					'name'=>$contact->name,
					'function'=>$contact->function,
					'email'=>$contact->email
			);
		}
		
		return parent::afterDisplay($response, $model, $params);
	}
	
//	protected function getStoreParams($params) {
//		
//		$storeParams = GO_Base_Db_FindParams::newInstance();
//		
//		if(isset($params['addressbook_id'])){
//			$storeParams->getCriteria()->addCondition('addressbook_id', $params['addressbook_id']);
//		}
//		
//		return $storeParams;		
//	}
	
	
	public function formatStoreRecord($record, $model, $store) {
		
		$record['name_and_name2']=$model->name;
		
		if(!empty($model->name2))
			$record['name_and_name2'] .= ' - '.$model->name2;
		
		return parent::formatStoreRecord($record, $model, $store);
	}
	
	protected function remoteComboFields() {
		return array(
				'addressbook_id'=>'$model->addressbook->name'
				);
	}
	
	protected function afterLoad(&$response, &$model, &$params) {
		
		if (GO::modules()->customfields)
			$response['customfields'] = GO_Customfields_Controller_Category::getEnabledCategoryData("GO_Addressbook_Model_Company", $model->addressbook_id);
		
		$stmt = $model->addresslists();
		while($addresslist = $stmt->fetch()){
			$response['data']['addresslist_'.$addresslist->id]=1;
		}
				
		
		return parent::afterLoad($response, $model, $params);
	}
	
	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {
		$stmt = GO_Addressbook_Model_Addresslist::model()->find();
		while($addresslist = $stmt->fetch()){
			$linkModel = $addresslist->hasManyMany('companies', $model->id);
			$mustHaveLinkModel = isset($params['addresslist_' . $addresslist->id]);
			if ($linkModel && !$mustHaveLinkModel) {
				$linkModel->delete();
			}
			if (!$linkModel && $mustHaveLinkModel) {
				$addresslist->addManyMany('companies',$model->id);
			}
		}
		
		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}
	
	/*
	 * This function initiates the contact filter by checked addressbooks.
	 */
	protected function getStoreMultiSelectProperties(){
		return array(
			'requestParam'=>'books',
			'permissionsModel'=>'GO_Addressbook_Model_Addressbook'
			//'titleAttribute'=>'name'
		);
	}	
	
	/*
	 * This function initiates the contact filters by:
	 * - search query (happens automatically in GO base class)
	 * - by clicked letter
	 * - checked addresslists
	 */
	protected function getStoreParams($params) {
		
		$criteria = GO_Base_Db_FindCriteria::newInstance()
			->addModel(GO_Addressbook_Model_Company::model(),'t')
			->addInCondition('addressbook_id', $this->multiselectIds);
		
		// Filter by clicked letter
		if (!empty($params['clicked_letter'])) {
			if ($params['clicked_letter'] == '[0-9]') {
				$query = '^[0-9].*$';
				$query_type = 'REGEXP';
			} else {
				$query = $params['clicked_letter'] . '%';
				$query_type = 'LIKE';
			}
			$criteria->addCondition('name', $query, $query_type);
		}		
		
		$storeParams = GO_Base_Db_FindParams::newInstance()
			->criteria($criteria)
			->select('t.*t, ab.name AS addressbook_name')
			->joinModel(array(
				'model'=>'GO_Addressbook_Model_Addressbook',					
				'localField'=>'addressbook_id',
				'tableAlias'=>'ab', //Optional table alias
			));	

		//if(empty($params['enable_addresslist_filter'])){
		
		// Filter by addresslist
			if(isset($params['addresslist_filter']))
			{
				$addresslist_filter = json_decode(($params['addresslist_filter']), true);
				if (!empty($addresslist_filter)) {
					$storeParams->join(GO_Addressbook_Model_AddresslistCompany::model()->tableName(),
							GO_Base_Db_FindCriteria::newInstance()->addCondition('id', 'ac.company_id', '=', 't', true, true),
							'ac'
						)->getCriteria()->addInCondition('addresslist_id', $addresslist_filter,'ac');
				}
				GO::config()->save_setting('ms_addresslist_filter', implode(',',$addresslist_filter), GO::user()->id);
			}
//			elseif ($addresslist_filter = GO::config()->get_setting('ms_addresslist_filter', GO::user()->id))
//			{
//				$addresslist_filter = empty($addresslist_filter) ? array() : explode(',', $addresslist_filter);
//				$storeParams->join(GO_Addressbook_Model_AddresslistCompany::model()->tableName(),
//						GO_Base_Db_FindCriteria::newInstance()->addCondition('id', 'ac.company_id', '=', 't', true, true),
//						'ac'
//					)->getCriteria()->addInCondition('addresslist_id', $addresslist_filter,'ac');
//			}
		//}

		return $storeParams;
		
	}
	
	public function actionChangeAddressbook($params) {
		$ids = json_decode($params['items']);
		
		$response['success'] = true;
		$response['failedToMove'] = array();
		
		foreach ($ids as $id) {
			$model = GO_Addressbook_Model_Company::model()->findByPk($id);
			$failed_id = !($model->setAttribute('addressbook_id',$params['book_id']) && $model->save()) ? $id : null;
			
			if ($failed_id) {
				$response['failedToMove'][] = $failed_id;
				$response['success'] = false;
			}
			
			foreach ($model->contacts as $contact) {
				$failed_id = !($contact->setAttribute('addressbook_id',$params['book_id']) && $contact->save()) ? $id : null;
				if ($failed_id) {
					$response['failedToMove'][] = $failed_id;
					$response['success'] = false;
				}
			}
		}
		
		return $response;
	}
}

