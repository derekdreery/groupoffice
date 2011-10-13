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
 * The GO_Tasks_Model_Task model
 *
 * @package GO.modules.Tasks
 * @version $Id: GO_Tasks_Model_Task.php 7607 2011-09-20 10:05:23Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 *
 * @property int $id
 * @property String $uuid
 * @property int $tasklist_id
 * @property int $user_id
 * @property int $ctime
 * @property int $mtime
 * @property int $start_time
 * @property int $due_time
 * @property int $completion_time
 * @property String $name
 * @property String $description
 * @property String $status
 * @property int $repeat_end_time
 * @property int $reminder
 * @property String $rrule
 * @property int $files_folder_id
 * @property int $category_id
 * @property int $priority
 * @property String $project_name
 */

class GO_Tasks_Model_Task extends GO_Base_Db_ActiveRecord {
	
	const STATUS_NEEDS_ACTION = "NEEDS-ACTION";
	const STATUS_COMPLETED = "COMPLETED";
	const STATUS_ACCEPTED = "ACCEPTED";
	const STATUS_DECLINED = "DECLINED";
	const STATUS_TENTATIVE = "TENTATIVE";
	const STATUS_DELEGATED = "DELEGATED";
	const STATUS_IN_PROCESS = "IN-PROCESS";
	
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

	public function hasFiles(){
		return true;
	}
	
	public function customfieldsModel(){
		return "GO_Tasks_Model_CustomFieldsRecord";
	}
	
	public function relations() {
		return array(
				'tasklist' => array('type' => self::BELONGS_TO, 'model' => 'GO_Tasks_Model_Tasklist', 'field' => 'tasklist_id', 'delete' => false),
				'category' => array('type' => self::BELONGS_TO, 'model' => 'GO_Tasks_Model_Category', 'field' => 'category_id', 'delete' => false),
				'user' => array('type' => self::BELONGS_TO, 'model' => 'GO_Base_Model_User', 'field' => 'user_id', 'delete' => false)
				);
	}
	
	protected function getCacheAttributes() {
		return array('name'=>$this->name, 'description'=>$this->description);
	}
		
	public function beforeSave() {
		
		if($this->status==GO_Tasks_Model_Task::STATUS_COMPLETED && empty($this->completion_time))
			$this->setCompleted(true, false);

		return parent::beforeSave();
	}
	
	protected function afterDbInsert() {
		$this->uuid = GO_Base_Util_UUID::create('task', $this->id);
		return true;
	}
	
	
	/**
	 * Set the task to completed or not completed.
	 * 
	 * @param Boolean $complete 
	 * @param Boolean $save 
	 */
	public function setCompleted($complete=true, $save=true) {
		if($complete) {
			$this->completion_time = time();
			$this->status=GO_Tasks_Model_Task::STATUS_COMPLETED;
			$this->_recur();
			$this->rrule='';
		} else {
			$this->completion_time = 0;
			$this->status=GO_Tasks_Model_Task::STATUS_NEEDS_ACTION;
		}
		
		if($save)
			$this->save();
	}
	
	/**
	 * Creates the new Recurring task when the rrule is not empty
	 */
	private function _recur(){
		if(!empty($this->rrule)) {

			$rrule = new GO_Base_Util_Icalendar_Rrule();
			$rrule->readIcalendarRruleString($this->due_time, $this->rrule);
			
			$this->duplicate(array(
				'completion_time'=>0,
				'start_time'=>time(),
				'due_time'=>$rrule->getNextRecurrence($this->due_time+1),
				'status'=>GO_Tasks_Model_Task::STATUS_NEEDS_ACTION
			));
		}
	}
	
	/**
	 * The files module will use this function.
	 */
	public function buildFilesPath() {

		return 'tasks/' . GO_Base_Fs_Base::stripInvalidChars($this->tasklist->name) . '/' . date('Y', $this->due_time) . '/' . GO_Base_Fs_Base::stripInvalidChars($this->name);
	}
	
	public function defaultAttributes() {
		
		$defaults = array(
			//	'description'=>'DIT IS DE BESCHRIJVING DIE STANDAARD WORDT INGEVULD',
				'status'=>  GO_Tasks_Model_Task::STATUS_NEEDS_ACTION
		);
		
		return $defaults;
	}
}