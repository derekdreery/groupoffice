<?php

class GO_Calendar_Controller_CalendarTasklist extends GO_Base_Controller_AbstractMultiSelectModelController {
	
	/**
	 * The name of the model from where the MANY_MANY relation is called
	 * @return String 
	 */
	public function modelName() {
		return 'GO_Calendar_Model_Calendar';
	}
	
	/**
	 * Returns the name of the model that handles the MANY_MANY relation.
	 * @return String 
	 */
	public function linkModelName() {
		return 'GO_Calendar_Model_CalendarTasklist';
	}
	
	/**
	 * The name of the field in the linkModel where the key of the current model is defined.
	 * @return String
	 */
	public function linkModelField() {
		return 'tasklist_id';
	}
	
//	/**
//	 * Get the data for the grid that shows all the tasks from the selected tasklists.
//	 * 
//	 * @param Array $params
//	 * @return Array The array with the data for the grid. 
//	 */
//	protected function actionPortletGrid($params) {
//
//		// Find out the sort for the grid
//		$sort = !empty($params['sort']) ? $params['sort'] : 'due_time';
//		$dir = !empty($params['dir']) ? $params['dir'] : 'ASC';
//		
//		$store = GO_Base_Data_Store::newInstance(GO_Tasks_Model_Task::model());
//		
//		$findCriteria = GO_Base_Db_FindCriteria::newInstance()
//						->addCondition('status',  GO_Tasks_Model_Task::STATUS_COMPLETED , '<>', 't');
//		
//		$joinCriteria = GO_Base_Db_FindCriteria::newInstance()
//						->addCondition('user_id', GO::user()->id,'=','pt')
//						->addCondition('tasklist_id', 'pt.tasklist_id', '=', 't', true, true);
//		
//		$tasklistJoinCriteria = GO_Base_Db_FindCriteria::newInstance()
//						->addCondition('tasklist_id', 'tl.id', '=', 't', true, true);
//		
//		$findParams = $store->getDefaultParams($params)
//						->select('t.*, tl.name AS tasklist_name')
//						->criteria($findCriteria)
//						->order(array('tasklist_name', $sort), $dir)
//						->ignoreAcl()
//						->join(GO_Tasks_Model_PortletTasklist::model()->tableName(),$joinCriteria,'pt')
//						->join(GO_Tasks_Model_Tasklist::model()->tableName(), $tasklistJoinCriteria,'tl');
//		
//		$stmt = GO_Tasks_Model_Task::model()->find($findParams);
//		
//		$store->setStatement($stmt);
//		$store->getColumnModel()->formatColumn('tasklist_name', '$model->tasklist_name');
//		$store->getColumnModel()->formatColumn('late','$model->isLate();');
//		
//		return $store->getData();
//		
//	}
	
}