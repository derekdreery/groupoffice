<?php
class GO_Cms_Controller_Site extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Cms_Model_Site';
	
	protected function remoteComboFields() {
		return array('user_id'=>'$model->user->name');
	}
	
	protected function getStoreParams($params) {
		
		if(empty($params['sort']))
			return array('order' => array('name'));
		else
			return parent::getStoreParams($params);
	}
		
}
