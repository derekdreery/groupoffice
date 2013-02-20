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
 * @version $Id: CronController.php 7962 2011-08-24 14:48:45Z wsmits $
 * @author Wesley Smits <wsmits@intermesh.nl>
 * @package GO.core.controller
 */

class GO_Core_Controller_Cron extends GO_Base_Controller_AbstractJsonController{

	protected function allowGuests() {
		return array('run');
	}
	
	//don't check token in this controller
	protected function checkSecurityToken(){}
	
	/**
	 * Load the Cronjob model
	 * 
	 * @param array $params
	 */
	protected function actionLoad($params) {
		$model = GO_Base_Cron_CronJob::model()->createOrFindByParams($params);

		$remoteComboFields = array();
		$this->renderForm($model, $remoteComboFields);
  }
  
	/**
	 * Update a Cronjob model
	 * 
	 * @param array $params
	 */
  protected function actionUpdate($params) {
		$model = GO_Base_Cron_CronJob::model()->findByPk($params['id']);
		$model->setAttributes($params);
		$model->save();

		$this->renderSubmit($model);
  }
	
	/**
	 * Create a new Cronjob model
	 * 
	 * @param array $params
	 */
	protected function actionCreate($params) {
		$model = new GO_Base_Cron_CronJob();
		$model->setAttributes($params);
		$model->save();

		$this->renderSubmit($model);
  }

	/**
	 * Get a list of all created Cronjob models
	 * 
	 * @param array $params
	 */
	public function actionStore($params){
		
		$colModel = new GO_Base_Data_ColumnModel(GO_Base_Cron_CronJob::model());
		
		$store = new GO_Base_Data_DbStore('GO_Base_Cron_CronJob',$colModel , $params);
		$store->defaultSort = 'name';
		
		$this->renderStore($store);
		
	}
	
	
	/**
	 * Get a list of all created Cronjob models that have a 'nextrun' between the 
	 * $params['from'] and $params['till'] time.
	 * 
	 * If $params['from'] and $params['till'] are not given then
	 * From = the current time
	 * Till = the current time + 1 day
	 * 
	 * @param array $params
	 */
	public function actionRunBetween($params){
		
		$from = false;
		$till = false;
		
		if(isset($params['from']))
			$from = new GO_Base_Util_Date_DateTime($params['from']);
		
		if(isset($params['till']))
			$till = new GO_Base_Util_Date_DateTime($params['till']);
		
		if(!$from)
			$from = new GO_Base_Util_Date_DateTime();
		
		if(!$till){
			$till = new GO_Base_Util_Date_DateTime();
			$till->add(new DateInterval('P1D'));
		}
		
		$findParams = GO_Base_Db_FindParams::newInstance()
			->criteria(GO_Base_Db_FindCriteria::newInstance()
				->addCondition('nextrun', $till->getTimestamp(),'<')
				->addCondition('nextrun', $from->getTimestamp(),'>')
				->addCondition('active', 1,'=')
			);
		
		$colModel = new GO_Base_Data_ColumnModel(GO_Base_Cron_CronJob::model());
		
		$store = new GO_Base_Data_DbStore('GO_Base_Cron_CronJob',$colModel , $params, $findParams);
		$store->defaultSort = 'nextrun';
		
		$result = $this->renderStore($store,true);
		
		$result['from'] = $from->format('d-m-Y H:i');
		$result['till'] = $till->format('d-m-Y H:i');
		
		$this->renderJson($result);
	}
	
	
	/**
	 * This is the function that is called from the server's cron deamon.
	 * The cron deamon is supposed to call this function every minute.
	 * 
	 * TODO: Check if 1 minute doesn't set the server under heavy load.
	 */
	protected function actionRun($params){
		
		$this->requireCli();

		$currentTime = new GO_Base_Util_Date_DateTime();
//		$currentMinusTime = new GO_Base_Util_Date_DateTime();
//		$currentMinusTime->sub(new DateInterval('PT1H'));

		$findParams = GO_Base_Db_FindParams::newInstance()
			->calcFoundRows()
			->criteria(GO_Base_Db_FindCriteria::newInstance()
				->addCondition('nextrun', $currentTime->getTimestamp(),'<')
			//	->addCondition('nextrun', $currentMinusTime->getTimestamp(),'>')
			);
		
		$cronsToHandle = GO_Base_Cron_CronJob::model()->find($findParams);
		
		//		
		//		echo 'KLEINER DAN: '.$currentTime->getTimestamp() .' ('.$currentTime->format('d-m-Y H:i').')';
		//		echo '<br />';
		//		echo 'GROTER DAN:  '.$currentMinusTime->getTimestamp() .' ('.$currentMinusTime->format('d-m-Y H:i').')';
		//		echo '<br />';
		//		
		//		$crons = GO_Base_Cron_CronJob::model()->find();
		//		foreach($crons as $c){
		//			echo $c->name;
		//			echo ' | ';
		//			echo 'NEXT RUN : '.$c->nextrun.' ('.date('d-m-Y H:i',$c->nextrun).')';
		//			echo '<br />';
		//		}
		//		
		//		
		//		
		
		GO::debug('CRONJOB START');
		
		if($cronsToHandle->foundRows == 0)
			GO::debug('CRONJOB NONE FOUND');
		
		foreach($cronsToHandle as $cron){
			if(!$cron->active){
				GO::debug('CRONJOB ('.$cron->name.') IS NOT ACTIVATED');
			} else {
				$cron->run();
			}
		}
	}

	/**
	 * Get all availabe cron files that are selectable when creating a new cron.
	 * 
	 * @return array
	 */
	protected function actionAvailableCronCollection($params){
		$response = array();
		$response['results'] = array();
		
		$cronJobCollection = new GO_Base_Cron_CronCollection();
		
		$cronfiles = $cronJobCollection->getAllCronJobClasses();
		$response['total'] = count($cronfiles);
		foreach($cronfiles as $c=>$label){
			$response['results'][] = array('name'=>$label,'class'=>$c);
		}
		
		$response['success'] = true;
		
		return $response;
	}
	
	/**
	 * Load the settings panel
	 * 
	 * @param array $params
	 * @return array
	 */
	protected function actionLoadSettings($params) {
		
		$settings =  GO_Base_Cron_CronSettings::load();
		
		return array(
				'success'=>true,
				'data'=>$settings->getArray()
		);
	}
	
	/**
	 * Save the settings panel
	 * 
	 * @param array $params
	 * @return array
	 */
	protected function actionSubmitSettings($params) {
		
		$settings =  GO_Base_Cron_CronSettings::load();

		return array(
				'success'=>$settings->saveFromArray($params),
				'data'=>$settings->getArray()
		);
	}
	
	
	
}
