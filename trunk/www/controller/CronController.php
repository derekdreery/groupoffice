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
		
		// Add parameter for checking if the use
		if(!empty($model->job)){
			$cron = new $model->job();
			$select = $cron->enableUserAndGroupSupport();
			$remoteComboFields['job']='"'.$cron->getLabel().'"';
		} else {
			$select = false;
		}

		echo $this->renderForm($model, $remoteComboFields,array('select'=>$select));
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

		echo $this->renderSubmit($model);
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

		echo $this->renderSubmit($model);
  }

	/**
	 * Get a list of all created Cronjob models
	 * 
	 * @param array $params
	 */
	public function actionStore($params){
		
		$colModel = new GO_Base_Data_ColumnModel(GO_Base_Cron_CronJob::model());
					
		$colModel->formatColumn('active', '$model->isRunning()?GO::t("running","cron"):$model->active');
		
		$store = new GO_Base_Data_DbStore('GO_Base_Cron_CronJob',$colModel , $params);
		$store->defaultSort = 'name';
		
		echo $this->renderStore($store);	
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
		
		$result = $this->renderStore($store);
		
		$result['from'] = $from->format('d-m-Y H:i');
		$result['till'] = $till->format('d-m-Y H:i');
		
		echo $result;
	}
	
	private function _findNextCron(){
		$currentTime = new GO_Base_Util_Date_DateTime();

		$findParams = GO_Base_Db_FindParams::newInstance()
			->single()
			->criteria(GO_Base_Db_FindCriteria::newInstance()
				->addCondition('nextrun', $currentTime->getTimestamp(),'<')
				->addCondition('active',true)
			);
		
		return GO_Base_Cron_CronJob::model()->find($findParams);
	}
	/**
	 * This is the function that is called from the server's cron deamon.
	 * The cron deamon is supposed to call this function every minute.
	 * 
	 * TODO: Check if 1 minute doesn't set the server under heavy load.
	 */
	protected function actionRun($params){
		
		$this->requireCli();
		$jobAvailable = false;
		GO::debug('CRONJOB START (PID:'.getmypid().')');
		while($cronToHandle = $this->_findNextCron()){
			$jobAvailable = true;
			GO::debug('CRONJOB FOUND');
			$cronToHandle->run();
		}
		
		if(!$jobAvailable)
			GO::debug('NO CRONJOB FOUND');
		
		GO::debug('CRONJOB STOP (PID:'.getmypid().')');
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
			
			$cObject = new $c();
			$userAndGroupSelection = $cObject->enableUserAndGroupSupport();
						
			$response['results'][] = array('name'=>$label,'class'=>$c,'selection'=>$userAndGroupSelection);
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
