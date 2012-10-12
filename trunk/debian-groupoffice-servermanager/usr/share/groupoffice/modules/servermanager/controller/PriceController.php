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
 * @package GO.ServerManager.Controllers
 */
/**
 * This controller handles saving the table for user/prices rate and space usage 
 *
 * @package GO.servermanager.controller
 * @copyright Copyright Intermesh
 * @version $Id PricesController.php 2012-08-29 15:17:36 mdhart $
 * @author Michael de Hart <mdehart@intermesh.nl> 
 */
class GO_Servermanager_Controller_Price extends GO_Base_Controller_AbstractController
{
	
	protected function actionModuleStore($params)
	{
		$model = new GO_ServerManager_Model_ModulePrice();
		$store = GO_Base_Data_Store::newInstance($model);
		
		$store->processDeleteActions($params, 'GO_ServerManager_Model_ModulePrice');
		
		$storeParams = $store->getDefaultParams($params);
		$store->setStatement($model->find($storeParams));
		return $store->getData();
	}
	
	protected function actionModuleSubmit($params){
		$modulePrice = GO_ServerManager_Model_ModulePrice::model()->findByPk($params['module_name']);
		if($modulePrice == null)
			$modulePrice = new GO_ServerManager_Model_ModulePrice();
		
		$modulePrice->setAttributes($params);
		$success = $modulePrice->save();
		
		return array('success'=>$success);
	}
	
	/**
	 * returns all the userprices
	 * @param array $params $_REQUEST
	 * @return string JSON for ExtJs Grid 
	 */
	protected function actionUserStore($params)
	{
		$model = new GO_ServerManager_Model_UserPrice();
		$store = GO_Base_Data_Store::newInstance($model);
		
		$store->processDeleteActions($params, 'GO_ServerManager_Model_UserPrice');
		
		$storeParams = $store->getDefaultParams($params);
		$store->setStatement($model->find($storeParams));
		return $store->getData();
	}
	
	protected function actionUserSubmit($params){
		$userPrice = GO_ServerManager_Model_UserPrice::model()->findByPk($params['module_name']);
		if($userPrice == null)
			$userPrice = new GO_ServerManager_Model_UserPrice();
		
		$userPrice ->setAttributes($params);
		$success = $userPrice->save();
		
		return array('success'=>$success);
	}
	
	
	
	
	
	
	protected $model = "GO_ServerManager_Model_UserPrice";
	
	/**
	 * For saving the prices in the serprice grid
	 * @return array json response
	 * @throws Exception when failed to save a price
	 */
	protected function actionSubmitPrices()
	{
		$prices = json_decode($params['prices']);
		$response = array();
		// insert rates from view
		foreach ($prices as $price) {
			if($price->max_users)
				$model = GO_ServerManger_Model_Price::model()->findByPk($price->max_users);
			else
				$model = new GO_ServerManager_Model_Price();

			//$rate->amount = GO_Base_Util_Number::to_phpnumber($rate->amount);
			//$model->company_id = !empty($params['company_id']) ? $params['company_id'] : 0;
			//$model->setIsNew(true);
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
	
	/**
	 * UNTESTED: Loading the go_settings mbs_include and extra_mbs for prices config
	 * @return array json response 
	 */
	protected function actionLoadSettings($params)
	{
		$userPrices = GO_ServerManager_Model_UserPrice::model()->find();
		foreach($userPrices as $userPrice)
		{
			$response['items'][]=$userPrice;
			
		}
		$modulePrices = GO_ServerManager_Model_ModulePrice::model()->find();
		foreach($modulePrices as $modulePrice)
		{
			$response['moduleprices'][]=$modulePrice;
		}
		
		//GO::config()->save_setting('sm_price_extra_gb', 2.5);
		return array(
			'success'=>true,
			'data' => array(
				'mbs_included'=>GO::config()->get_setting('sm_mbs_included'), 
				'price_extra_gb'=>GO::config()->get_setting('sm_price_extra_gb'),
			),
		);
	}
	
	/**
	 * UNTESTED: submit the userpirces, moduleprices, and quota mbs included and extra costs
	 * @param array $params the $_REQUEST object
	 */
	protected function actionSubmitSettings($params)
	{
		if(isset($params['mbs_include']) && isset($params['price_extra_gb']))
		{
			GO::config()->save_setting('sm_mbs_include', $params['mbs_include']);
			GO::config()->save_setting('sm_price_extra_gb', $params['price_extra_gb']);
		}

		//Save userprices grid
		foreach($params['user_prices'] as $price)
		{
			$userprice = GO_ServerManager_Model_UserPrice::model()->findByPk($price->max_users);
			if($userprice == null)
				$userprice = new GO_ServerManager_Model_UserPrice();
			$userprice->max_users = $price->max_users;
			$userprice->price_per_month = $price->price_per_month;
			$userprice->save();
		}
		//Save moduleprices grid
		foreach($params['module_prices'] as $price)
		{
			$moduleprice = GO_ServerManager_Model_ModulePrice::model()->findByPk($price->module_name);
			if($moduleprice == null) //not found
				$moduleprice = new GO_ServerManager_Model_ModulePrice();
			$moduleprice->module_name = $price->module_name;
			$moduleprice->price_per_month = $price->price_per_month;
		}
		
		//TODO: give response if everything went splendid.
		
	}
}

