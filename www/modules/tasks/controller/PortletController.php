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
 * The GO_Tasks_Controller_Portlet controller
 *
 * @package GO.modules.Tasks
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

class GO_Tasks_Controller_Portlet extends \GO\Base\Controller\AbstractMultiSelectModelController {
	
	/**
	 * The name of the model from where the MANY_MANY relation is called
	 * @return String 
	 */
	public function modelName() {
		return 'GO_Tasks_Model_Tasklist';
	}
	
	/**
	 * Returns the name of the model that handles the MANY_MANY relation.
	 * @return String 
	 */
	public function linkModelName() {
		return 'GO_Tasks_Model_PortletTasklist';
	}
	
	/**
	 * The name of the field in the linkModel where the key of the current model is defined.
	 * @return String
	 */
	public function linkModelField() {
		return 'tasklist_id';
	}
	
	/**
	 * Get the data for the grid that shows all the tasks from the selected tasklists.
	 * 
	 * @param Array $params
	 * @return Array The array with the data for the grid. 
	 */
	protected function actionPortletGrid($params) {
		
		$now = \GO\Base\Util\Date::date_add(mktime(0,0,0),1);
		
		if(isset($params['completed_task_id'])) {
			$updateTask = GO_Tasks_Model_Task::model()->findByPk($params['completed_task_id']);
			
			if(isset($params['checked']))
				$updateTask->setCompleted($params['checked']=="true");
		}
		
		// Find out the sort for the grid
		$sort = !empty($params['sort']) ? $params['sort'] : 'due_time';
		$dir = !empty($params['dir']) ? $params['dir'] : 'ASC';
		
		$store = \GO\Base\Data\Store::newInstance(\GO_Tasks_Model_Task::model());
		
		$findCriteria = \GO\Base\Db\FindCriteria::newInstance()
						->addCondition('start_time', $now, '<')
						->addCondition('status',  GO_Tasks_Model_Task::STATUS_COMPLETED , '<>', 't');
		
		$joinCriteria = \GO\Base\Db\FindCriteria::newInstance()
						->addCondition('user_id', GO::user()->id,'=','pt')
						->addCondition('tasklist_id', 'pt.tasklist_id', '=', 't', true, true);
		
		$tasklistJoinCriteria = \GO\Base\Db\FindCriteria::newInstance()
						->addCondition('tasklist_id', 'tl.id', '=', 't', true, true);
		
		$findParams = $store->getDefaultParams($params)
						->select('t.*, tl.name AS tasklist_name')
						->criteria($findCriteria)
						->order(array('tasklist_name', $sort), $dir)
						->ignoreAcl()
						->join(\GO_Tasks_Model_PortletTasklist::model()->tableName(),$joinCriteria,'pt')
						->join(\GO_Tasks_Model_Tasklist::model()->tableName(), $tasklistJoinCriteria,'tl');
		
		$stmt = GO_Tasks_Model_Task::model()->find($findParams);
		
		$store->setStatement($stmt);
		$store->getColumnModel()->formatColumn('tasklist_name', '$model->tasklist_name');
		$store->getColumnModel()->formatColumn('late','$model->isLate();');
		
		return $store->getData();
		
	}
	
}