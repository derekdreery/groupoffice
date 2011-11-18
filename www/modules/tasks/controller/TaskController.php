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
 * @version $Id: TaskController.php 7607 2011-09-20 10:09:05Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits <wsmits@intermesh.nl>
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
		
		$settings = GO_Tasks_Model_Settings::model()->findByPk(GO::user()->id);
		$response['data']['remind_before'] = $settings->reminder_days;
		
		if(!empty($response['data']['reminder'])) {			
			$response['data']['remind']=1;
			$response['data']['remind_date']=date(GO::user()->completeDateFormat, $model->reminder);
			$response['data']['remind_time']=date(GO::user()->time_format, $model->reminder);
		}	else {
			$response['data']['remind_date']=date(GO::user()->completeDateFormat, $model->getDefaultReminder($model->start_time));
			$response['data']['remind_time']=date(GO::user()->time_format, $model->getDefaultReminder($model->start_time));
		}
			
		//$response['data']['remind_time']=date(GO::user()->time_format, strtotime($response['data']['reminder']));
		
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
	
	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {		
		
		 if(GO::modules()->files){
			 $f = new GO_Files_Controller_Folder();
			 $f->processAttachments($response, $model, $params);
		 }
		
		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}

	protected function remoteComboFields(){
		return array(
				'category_id'=>'$model->category->name',
				'tasklist_id'=>'$model->tasklist->name'
				);
	}

	protected function getStoreMultiSelectProperties(){
		return array(
				'requestParam'=>'ta-taskslists',
				'permissionsModel'=>'GO_Tasks_Model_Tasklist'
				//'titleAttribute'=>'name'
				);
	}	
	
	protected function beforeStore(&$response, &$params, &$store) {
		
		if(isset($params['completed_task_id'])) {
			$updateTask = GO_Tasks_Model_Task::model()->findByPk($params['completed_task_id']);
			if(isset($params['checked']) && $params['checked'] == 1)
				$updateTask->setCompleted(true);
			else
				$updateTask->setCompleted(false);
		}
		
		return parent::beforeStore($response, $params, $store);
	}
	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		
		$columnModel->formatColumn('completed','$model->status=="COMPLETED" ? 1 : 0');
		$columnModel->formatColumn('category_name','$model->category->name',array(),'category_id');
		$columnModel->formatColumn('tasklist_name','$model->tasklist_name');
		$columnModel->formatColumn('late','$model->due_time<time() ? 1 : 0;');
		//$colModel->formatColumn('project_name','$model->project->name'); TODO: Implement the project from the ID and not from the name
		return parent::formatColumns($columnModel);
	}
	
	protected function getStoreMultiSelectDefault() {
		$settings = GO_Tasks_Model_Settings::model()->getDefault(GO::user());
		
		
		return $settings->default_tasklist_id;	
	}
		
	protected function getStoreParams($params) {
		
		//TODO store in settings
		if(!isset($params['show']))
			$params['show']='active';

//		$storeParams =  array(
//				'ignoreAcl'=>true,
//				'export'=>'tasks',
//				'joinCustomFields'=>true,
//				'by'=>array(array('tasklist_id', $this->multiselectIds, 'IN')),
//				'fields'=>'t.*t, tl.name AS tasklist_name',
//				'joinModel'=>array(
//					'model'=>'GO_Tasks_Model_Tasklist',					
//					'localField'=>'tasklist_id',
//					'tableAlias'=>'tl', //Optional table alias
//					)
//		);
		
		$storeParams = GO_Base_Db_FindParams::newInstance()
						
						->export("tasks")
						->joinCustomFields()
						->criteria(GO_Base_Db_FindCriteria::newInstance()
										->addModel(GO_Tasks_Model_Task::model(),'t')
										->addInCondition('tasklist_id', $this->multiselectIds))										
										
						->select('t.*, tl.name AS tasklist_name')
						->joinModel(array(
							'model'=>'GO_Tasks_Model_Tasklist',					
							'localField'=>'tasklist_id',
							'tableAlias'=>'tl', //Optional table alias
							));
		
		if(!empty($this->multiselectIds)){
			$storeParams->ignoreAcl();
		}
		
		
		if(isset($params['categories'])) {
			$categories = json_decode($params['categories'], true);
			
			$storeParams->getCriteria()->addInCondition('category_id', $categories,'t');
		}
		
		$storeParams->debugSql();
		
		$storeParams = $this->checkFilterParams($params['show'],$storeParams);
		
		return $storeParams;
	}
	
	private function checkFilterParams($show, GO_Base_Db_FindParams $params) {

		// Check for a given filter on the statusses
		if(!empty($show)) {
			$statusCriteria = $params->getCriteria();

			switch($show) {
				case 'today':
					$start_time = mktime(0,0,0);
					$end_time = GO_Base_Util_Date::date_add($start_time, 1);
					break;

				case 'sevendays':
					$start_time = mktime(0,0,0);
					$end_time = GO_Base_Util_Date::date_add($start_time, 7);
					$show_completed=false;	
					break;

				case 'overdue':
					$start_time = 0;
					$end_time = mktime(0,0,0);
					$show_completed=false;
					$show_future=false;
					break;

				case 'completed':
					$start_time = 0;
					$end_time = 0;
					$show_completed=true;
					//$show_future=false;
					break;

				case 'future':
					$start_time = 0;
					$end_time = 0;
					$show_completed=false;				
					$show_future=true;
					break;

				case 'active':
				case 'portlet':
					$start_time = 0;
					$end_time = 0;
					$show_completed=false;
					$show_future=false;
				break;

				default:
					// Nothing
				break;
			}
			
			if(isset($show_completed)) {
				if($show_completed)
					$statusCriteria->addCondition('completion_time', 0, '>');
				else
					$statusCriteria->addCondition('completion_time', 0, '=');
			}
			
			if(!empty($start_time)) 
				$statusCriteria->addCondition('due_time', $start_time, '>=');
				
			if(!empty($end_time)) 
				$statusCriteria->addCondition('due_time', $end_time, '<');

			if(isset($show_future)) {
				$now = GO_Base_Util_Date::date_add(mktime(0,0,0),1);
				if($show_future) 
					$statusCriteria->addCondition('start_time', $now, '>=');
				else
					$statusCriteria->addCondition('start_time', $now, '<');
			}
			
			//$params->getCriteria()->mergeWith($statusCriteria);
			//			$params['criteriaObject']=$statusCriteria;
		}
		
//		// Check for a given filter on the categories
//		if(isset($params['categoryFilter'])) {
//			$categoryCriteria = GO_Base_Db_FindCriteria::newInstance()
//				->addModel(GO_Tasks_Model_Task::model(),'t');
//			
//			$categories = json_decode($params['categoryFilter'], true);
//			
////			foreach($categories as $category) 
////				$categoryCriteria->addCondition('category_id', $category, '=','t',false);
//			//if(count($categories))
//			$categoryCriteria->addInCondition('category_id', $categories,'t',false,false);
//
//			if(isset($params['criteriaObject']))
//				$params['criteriaObject']->mergeWith($categoryCriteria);
//			else
//				$params['criteriaObject'] = $categoryCriteria;
//		}
		
		return $params;
	}
	
	
	
	public function actionImportIcs($params){
		
		$file = new GO_Base_Fs_File($params['file']);
		
		$data = $file->getContents();


		$vcalendar = GO_Base_VObject_Reader::read($data);
		
		foreach($vcalendar->vtodo as $vtodo)
			$task = new GO_Tasks_Model_Task();
			$task->importVObject($vtodo);
	}
	
	public function actionIcs($params) {
		$task = GO_Tasks_Model_Task::model()->findByPk($params['id']);
		header('Content-Type: text/plain');
		echo $task->toICS();
	}

}
	