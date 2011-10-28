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
	
	protected function getStoreParams($params) {
		
		$storeParams = GO_Base_Db_FindParams::newInstance();
		
		if(isset($params['addressbook_id'])){
			$storeParams->getCriteria()->addCondition('addressbook_id', $params['addressbook_id']);
		}
		
		return $storeParams;		
	}
	
	
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
	

}

