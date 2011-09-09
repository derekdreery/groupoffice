<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: Task.php 7607 2011-09-01 11:17:42Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

/**
 * 
 * The Task model
 *  
 * @param int $id
 * @param String $uuid
 * @param int $tasklist_id
 * @param int $user_id
 * @param int $ctime
 * @param int $mtime
 * @param int $start_time
 * @param int $due_time
 * @param int $completion_time
 * @param String $name
 * @param String $description
 * @param String $status
 * @param int $repeat_end_time
 * @param int $reminder
 * @param String $rrule
 * @param int $files_folder_id
 * @param int $category_id
 * @param int $priority
 * @param String $project_name 
 * 
 */
class GO_Tasks_Model_Task extends GO_Base_Db_ActiveRecord {

	
	private $_parsedRrule;
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Tasks_Model_Task
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	protected function init() {
		$this->columns['name']['required']=true;
		
		$this->columns['start_time']['gotype']='unixdate';
		$this->columns['due_time']['gotype']='unixdate';
		$this->columns['completion_time']['gotype']='unixdate';
		$this->columns['repeat_end_time']['gotype']='unixdate';
		$this->columns['reminder']['gotype']='unixtimestamp';
		parent::init();
	}

	public function tableName() {
		return 'ta_tasks';
	}
	
	public function aclField() {
		return 'tasklist.acl_id';
	}

	public function relations() {
		return array(
				'tasklist' => array('type' => self::BELONGS_TO, 'model' => 'GO_Tasks_Model_Tasklist', 'field' => 'tasklist_id', 'delete' => false),
				'category' => array('type' => self::BELONGS_TO, 'model' => 'GO_Tasks_Model_Category', 'field' => 'category_id', 'delete' => false),
				'user' => array('type' => self::BELONGS_TO, 'model' => 'GO_Base_Model_User', 'field' => 'user_id', 'delete' => false)
				);
	}
	
}