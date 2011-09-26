<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * The GO_Tasks_Controller_Task controller
 *
 * @package GO.modules.Tasks
 * @version $Id: GO_Tasks_Controller_Task.php 7607 2011-09-20 10:09:05Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */

class GO_Tasks_Controller_Task extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Tasks_Model_Task';
	
	protected function afterDisplay(&$response, &$model,&$params) {
		$response['data']['user_name']=$model->user->name;
		$response['data']['tasklist_name']=$model->tasklist->name;
		$response['data']['status_text']=GO::t($model->status,'tasks');
		
		return parent::afterDisplay($response, $model, $params);
	}
	
	protected function afterLoad(&$response, &$model, &$params) {
		
		if(!empty($model->rrule)) {
			$rRule = new GO_Base_Util_Icalendar_Rrule();
			$rRule->readIcalendarRruleString($model->start_time, $model->rrule);
			$createdRule = $rRule->createJSONOutput();

			$response['data'] = array_merge($response['data'],$createdRule);
		}
		
		if(isset($response['data']['reminder']) && !empty($response['data']['reminder'])) {			
			$response['data']['remind']=1;
			$response['data']['remind_date']=date(GO::user()->completeDateFormat, strtotime($response['data']['reminder']));
			$response['data']['remind_time']=date(GO::user()->time_format, strtotime($response['data']['reminder']));
		}

		return parent::afterLoad($response, $model, $params);
	}

	protected function beforeSubmit(&$response, &$model, &$params) {
		
		if(isset($params['freq']) && !empty($params['freq'])) {
			$rRule = new GO_Base_Util_Icalendar_Rrule();
			$rRule->readJsonArray($params);		
			$model->rrule = $rRule->createRrule();
		}
				
		if(isset($params['remind'])) // Check for a setted reminder
		{
			$model->reminder= GO_Base_Util_Date::to_unixtime($params['remind_date'].' '.$params['remind_time']);
			
			// Todo: Create the new reminder if it does not exist yet.
			//OUDE CODE UIT ACTION.PHP
			//			if(isset($_POST['remind'])) {
			//				$task['reminder']=Date::to_unixtime($_POST['remind_date'].' '.$_POST['remind_time']);
			//			}elseif(!isset($_POST['status'])) {
			//				//this task is added with the quick add option
			//				$settings=$tasks->get_settings($GLOBALS['GO_SECURITY']->user_id);
			//				if(!empty($settings['remind'])) {
			//					$reminder_day = $task['due_time'];
			//					if(!empty($settings['reminder_days']))
			//						$reminder_day = Date::date_add($reminder_day,-$settings['reminder_days']);
			//
			//					$task['reminder']=Date::to_unixtime(Date::get_timestamp($reminder_day, false).' '.$settings['reminder_time']);
			//				}
			//			}else {
			//				$task['reminder']=0;
			//			}		
		}
		else {
			$model->reminder = 0;
		}
		
		return parent::beforeSubmit($response, $model, $params);
	}

	protected function remoteComboFields(){
		return array(
				'category_id'=>'$model->category->name',
				'tasklist_id'=>'$model->tasklist->name'
				);
	}

	protected function getGridMultiSelectProperties(){
		return array(
				'requestParam'=>'tasks_tasklist_filter',
				'permissionsModel'=>'GO_Tasks_Model_Tasklist'
				//'titleAttribute'=>'name'
				);
	}	
	
	protected function beforeGrid(&$response, &$params, &$grid) {
		
		if(isset($params['completed_task_id'])) {
			$updateTask = GO_Tasks_Model_Task::model()->findByPk($params['completed_task_id']);
			if(isset($params['checked']) && $params['checked'] == 1)
				$updateTask->setCompleted(true);
			else
				$updateTask->setCompleted(false);
		}
		
		return parent::beforeGrid($response, $params, $grid);
	}
	
	protected function prepareGrid(GO_Base_Provider_Grid $grid) {
		$grid->formatColumn('completed','$model->status=="COMPLETED" ? 1 : 0');
		$grid->formatColumn('category_name','$model->category->name',array(),'category_id');
		$grid->formatColumn('tasklist_name','$model->tasklist_name');
		//$grid->formatColumn('project_name','$model->project->name'); TODO: Implement the project from the ID and not from the name
		return parent::prepareGrid($grid);
	}
	
	protected function getGridParams($params) {

		$gridParams =  array(
				'ignoreAcl'=>true,
				'joinCustomFields'=>true,
				'by'=>array(array('tasklist_id', $this->multiselectIds, 'IN')),
				'fields'=>'t.*, tl.name AS tasklist_name',
				'joinModel'=>array(
					'model'=>'GO_Tasks_Model_Tasklist',					
					'localField'=>'tasklist_id',
					'tableAlias'=>'tl', //Optional table alias
					)
		);
		
		if(isset($params['show'])) {
			$gridParams['statusFilter']=$params['show'];
		}
		
		if(isset($params['categories'])) {
			$gridParams['categoryFilter']=$params['categories'];
		}
		
		return $gridParams;
	}
	
	public function actionExport() {
		
	}
}
	