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
		
		if(isset($params['freq']) && !empty($params['freq']))
		{
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
	
	protected function prepareGrid($grid) {
		$grid->formatColumn('completed','$model->status=="COMPLETED" ? 1 : 0');
		return parent::prepareGrid($grid);
	}
	
	protected function getGridParams($params){
		
		
		$gridParams =  array(
				'ignoreAcl'=>true,
				'joinCustomFields'=>true,
				'by'=>array(array('tasklist_id', $this->multiselectIds, 'IN'))
		);
		
		if(isset($params['show'])) {
			$gridParams['taskFilter']=$params['show'];
		}
//		$params['show'] = isset($params['show']) ? $params['show'] : 0;
		
//
//		
//	
//		
//		if(!empty($params['show'])) {
//			$start_time = 0;
//			$end_time = 0;
//			
//			switch($params['show']) {
//				case 'today':
//					$start_time = mktime(0,0,0);
//					$end_time = GO_Base_Util_Date::date_add($start_time, 1);
//					break;
//
//				case 'sevendays':
//					$start_time = mktime(0,0,0);
//					$end_time = GO_Base_Util_Date::date_add($start_time, 7);
//					break;
//
//				case 'overdue':
//					$start_time = 0;
//					$end_time = mktime(0,0,0);
//					$show_completed=false;
//					$show_future=false;
//					break;
//
//				case 'completed':
//					$start_time = 0;
//					$end_time = 0;
//					$show_completed=true;
//					$show_future=false;
//					break;
//
//				case 'future':
//					$start_time = 0;
//					$end_time = 0;
//					$show_completed=false;				
//					$show_future=true;
//					break;
//
//				case 'active':
//				case 'portlet':
//					$start_time = 0;
//					$end_time = 0;
//					$show_completed=false;
//					$show_future=false;
//				break;
//
//				default:
//					//$start_time=0;
//					//$end_time=0;
//					//unset($show_completed);
//					//unset($show_future);
//					break;
//			}
//			
//			$gridParams['bindParams'] = array();
//			
//			$gridParams['where'] = ' 1';
//			
//			if(isset($show_completed)) {
//			if($show_completed)
//				$gridParams['where'] .=' AND completion_time>0';
//			else
//				$gridParams['where'] .=' AND completion_time=0';
//			}
//			
//			if(!empty($start_time) && !empty($end_time)) {
//				$gridParams['where'] .=' AND due_time>=:start_time AND due_time<:end_time';
//				$gridParams['bindParams'][':start_time'] = $start_time;
//				$gridParams['bindParams'][':end_time'] = $end_time;
//			}	else if(!empty($start_time)) {
//				$gridParams['where']=' AND due_time>=:start_time';
//				$gridParams['bindParams'][':start_time'] = $start_time;
//			}	else if(!empty($end_time)){
//				$gridParams['where']=' AND due_time<:end_time';
//				$gridParams['bindParams'][':end_time'] = $end_time;
//			}
//
//			if(isset($show_future)) {
//				$now = GO_Base_Util_Date::date_add(mktime(0,0,0),1);
//				$gridParams['bindParams'][':now_time'] = $now;
//				if($show_future)
//					$gridParams['where'] .=' AND start_time<:now_time';
//				else
//					$gridParams['where'] .=' AND start_time >=:now_time';
//			}
//			
//		}
//
//		GO::debug("*************************************");
//		GO::debug($gridParams);
//		GO::debug("*************************************");
//	
		return $gridParams;
	}
}
	