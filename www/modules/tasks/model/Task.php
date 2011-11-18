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
	
	const PRIORITY_LOW = 0;
	const PRIORITY_NORMAL = 1;
	const PRIORITY_HIGH = 2;
	
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
	
	protected function getLocalizedName() {
		return GO::t('task', 'tasks');
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
	
	public function hasLinks() {
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
		$settings = GO_Tasks_Model_Settings::model()->getDefault(GO::user());
		
		$defaults = array(
				//'description'=>'DIT IS DE BESCHRIJVING DIE STANDAARD WORDT INGEVULD',
				'status' => GO_Tasks_Model_Task::STATUS_NEEDS_ACTION,
				'remind' => $settings->remind,
				'start_time'=> time(),
				'due_time'=> time(),
				'tasklist_id'=>$settings->default_tasklist_id,
				'reminder' =>$this->getDefaultReminder(time())
		);
		
		return $defaults;
	}
	
	public function getDefaultReminder($startTime){
		$settings = GO_Tasks_Model_Settings::model()->getDefault(GO::user());
		
		$tmp = GO_Base_Util_Date::date_add($startTime, -$settings->reminder_days);
		$dateString = date('Y-m-d', $tmp).' '.$settings->reminder_time;
		$time = strtotime($dateString);
		return $time;
	}
	
	
	/**
	 * Get vcalendar data for an *.ics file.
	 * 
	 * @return string 
	 */
	public function toICS() {		
		
		$c = new GO_Base_VObject_VCalendar();		
		$c->add($this->toVObject());		
		return $c->serialize();		
	}
	
	
	/**
	 * Get this task as a VObject. This can be turned into a vcalendar file data.
	 * 
	 * @return Sabre_VObject_Component 
	 */
	public function toVObject(){
		$e=new Sabre_VObject_Component('vtodo');
		$e->uid=$this->uuid;	
		
		$dtstamp = new Sabre_VObject_Element_DateTime('dtstamp');
		$dtstamp->setDateTime(new DateTime(), Sabre_VObject_Element_DateTime::UTC);		
		$e->add($dtstamp);
		
		$mtimeDateTime = new DateTime();
		$mtimeDateTime->setTimestamp($this->mtime);
		$lm = new Sabre_VObject_Element_DateTime('LAST-MODIFIED');
		$lm->setDateTime($mtimeDateTime, Sabre_VObject_Element_DateTime::UTC);		
		$e->add($lm);
		
		$ctimeDateTime = new DateTime();
		$ctimeDateTime->setTimestamp($this->mtime);
		$ct = new Sabre_VObject_Element_DateTime('created');
		$ct->setDateTime($ctimeDateTime, Sabre_VObject_Element_DateTime::UTC);		
		$e->add($ct);
		
    $e->summary = $this->name;
		
		$e->status = $this->status;
		
		$dateType = Sabre_VObject_Element_DateTime::DATE;
		
		$dtstart = new Sabre_VObject_Element_DateTime('dtstart',$dateType);
		$dtstart->setDateTime(GO_Base_Util_Date_DateTime::fromUnixtime($this->start_time));		
		$e->add($dtstart);
		
		$due = new Sabre_VObject_Element_DateTime('due',$dateType);
		$due->setDateTime(GO_Base_Util_Date_DateTime::fromUnixtime($this->due_time));		
		$e->add($due);
		
		if($this->completion_time>0){
			$completed = new Sabre_VObject_Element_DateTime('completed',Sabre_VObject_Element_DateTime::LOCALTZ);
			$completed->setDateTime(GO_Base_Util_Date_DateTime::fromUnixtime($this->completion_time));		
			$e->add($completed);
		}
		
		if(!empty($this->description))
			$e->description=$this->description;
		
		//todo exceptions
		if(!empty($this->rrule)){
			$e->rrule=str_replace('RRULE:','',$this->rrule);					
		}
		
		switch($this->priority){
			case self::PRIORITY_HIGH:
				$e->priority=1;
				break;
			
			case self::PRIORITY_LOW:
				$e->priority=10;
				break;
			
			default:
				$e->priority=5;
				break;
		}
		
		return $e;
	}
	
	
	/**
	 * Import a task from a VObject 
	 * 
	 * @param Sabre_VObject_Component $vobject
	 * @param array $attributes Extra attributes to apply to the event
	 * @return GO_Tasks_Model_Task 
	 */
	public function importVObject(Sabre_VObject_Component $vobject, $attributes=array()){
		//$event = new GO_Calendar_Model_Event();
		
		$this->uuid = (string) $vobject->uid;
		$this->name = (string) $vobject->summary;
		$this->description = (string) $vobject->description;
		if(!empty($vobject->dtstart))
			$this->start_time = $vobject->dtstart->getDateTime()->format('U');
		
		if(!empty($vobject->dtend))
			$this->due_time = $vobject->dtend->getDateTime()->format('U');
		
		if(!empty($vobject->due))
			$this->due_time = $vobject->due->getDateTime()->format('U');
				
		if($vobject->dtstamp)
			$this->mtime=$vobject->dtstamp->getDateTime()->format('U');
		
		if(empty($this->due_time))
			$this->due_time=time();
		
		if(empty($this->start_time))
			$this->start_time=$this->due_time;
		
		if($vobject->rrule){			
			$rrule = new GO_Base_Util_Icalendar_Rrule();
			$rrule->readIcalendarRruleString($this->start_time, (string) $vobject->rrule);			
			$this->rrule = $rrule->createRrule();
			$this->repeat_end_time = $rrule->until;
		}		
		
		//var_dump($vobject->status);
		if($vobject->status)
			$this->status=(string) $vobject->status;
		
		if($vobject->duration){
			$duration = GO_Base_VObject_Reader::parseDuration($vobject->duration);
			$this->end_time = $this->start_time+$duration;
		}
		
		if(!empty($vobject->priority))
		{			
			if((string) $vobject->priority>5)
			{
				$this->priority=self::PRIORITY_LOW;
			}elseif((string) $vobject->priority==5)
			{
				$this->priority=self::PRIORITY_NORMAL;
			}else
			{
				$this->priority=self::PRIORITY_HIGH;
			}
		}
		
		if(!empty($vobject->completed)){
			$this->completion_time=$vobject->completed->getDateTime()->format('U');
			$this->status='COMPLETED';
		}else
		{
			$this->completion_time=0;
		}
		
		if($this->status=='COMPLETED' && empty($this->completion_time))
			$this->completion_time=time();
		
		if($vobject->valarm){
			
		}else
		{
			$this->reminder=0;
		}		
		
		$this->setAttributes($attributes);
		
		$this->save();
		
		return $this;
	}	
}