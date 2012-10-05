<?php

/**
 * Group-Office
 * 
 * Copyright Intermesh BV. 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @copyright Copyright Intermesh BV
 * @version $Id PricesController.php 2012-08-29 15:17:36 mdhart $
 * @author Michael de Hart <mdehart@intermesh.nl> 
 * @package GO.servermanager.controllers
 */
/**
 * This controller handles saving the table for user/prices rate and space usage 
 *
 * @package GO.servermanager.controller
 * @copyright Copyright Intermesh
 * @version $Id PricesController.php 2012-08-29 15:17:36 mdhart $
 * @author Michael de Hart <mdehart@intermesh.nl> 
 */
class GO_Servermanager_Controller_Price extends GO_Base_Controller_AbstractModelController
{
	protected $model = "GO_ServerManager_Model_UserPrice";

	/*
	protected function getStoreParams($params) {
		if (isset($params['company_id'])) {
			$company_id = $params['company_id'];
			if (!empty($params['include_global_rates']))
				return array('ignoreAcl' => true, 'by' => array(array('company_id', $company_id, '='), array('company_id', '0', '=')), 'byOperator' => 'OR');
			else
				return array('ignoreAcl' => true, 'by' => array(array('company_id', $company_id, '=')));
		} else {
			return array('ignoreAcl' => true, 'by' => array(array('company_id', '0', '=')));
		}
	}*/
	
	protected function actionSubmitPrices()
	{
		$prices = json_decode($params['prices']);

		// insert rates from view
		foreach ($prices as $price) {
			if($price->max_users)
				$model = GO_ServerManger_Model_Price::model()->findByPk($price->max_users);
			else
				$model = new GO_ServerManager_Model_Price();

			//$rate->amount = GO_Base_Util_Number::to_phpnumber($rate->amount);
			//$model->company_id = !empty($params['company_id']) ? $params['company_id'] : 0;
//	    $model->setIsNew(true);
			$model->setAttribute('max_users',$price->max_users, true);
			$model->setAttribute('price_per_month',$price->price_per_month);

			$response['success'] = $model->save();
			if (empty($response['success'])) {
				throw new Exception('Failed to save price for '.$model->max_users.' users');
			}
		}
		//$this->output($response);
		return $response;
	}
	
	protected function actionSpace(){
		
	}
}
?>
