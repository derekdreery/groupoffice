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
	

}

