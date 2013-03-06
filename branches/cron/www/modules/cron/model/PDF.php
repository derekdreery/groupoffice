<?php 

class GO_Cron_Model_PDF extends GO_Base_Util_Pdf {
			
	private	$_headerFontSize = '14';
	private	$_headerFontColor = '#3194D0';
	private $_nameFontSize = '12';
	private $_timeFontSize = '12';
	private $_descriptionFontSize = '12';
	protected $font = 'dejavusans';
	protected $font_size=10;
	
	/**
	 * Set the title that will be printed in the header of the PDF document
	 * 
	 * @param String $title
	 */
	public function setTitle($title){
		$this->title = $title;
	}
	
	/**
	 * Set the subtitle that will be printed in the header of the PDF document
	 * 
	 * @param String $subtitle
	 */
	public function setSubTitle($subtitle){
		$this->subtitle = $subtitle;
	}
	
	/**
	 * Render the pdf content.
	 * 
	 * This will render the events and the tasks of the user that is given with 
	 * the $user param.
	 * 
	 * @param GO_Base_Model_User $user
	 */
	public function render($user){
		$this->AddPage();
		$this->setEqualColumns(2, ($this->pageWidth/2)-10);
		$eventsString = GO::t('appointments','calendar');
		$tasksString = GO::t('tasks','tasks');
		
		$textColor = $this->TextColor;
		$textFont = $this->getFontFamily();
		
		$events = $this->_getEvents($user);
		$tasks = $this->_getTasks($user);
		
		// RENDER EVENTS
		$this->writeHTML('<h2 style="color:'.$this->_headerFontColor.';font-size:'.$this->_headerFontSize.'px;">'.$eventsString.'</h2>', true, false, false, false, 'L');
		$this->Ln();
		
		foreach($events as $event)
			$this->_renderEventRow($event);

		$this->Ln();
		
		// RENDER TASKS
		$this->writeHTML('<h2 style="color:'.$this->_headerFontColor.';font-size:'.$this->_headerFontSize.'px;">'.$tasksString.'</h2>', true, false, false, false, 'L');
		$this->Ln();
		
		foreach($tasks as $task)
			$this->_renderTaskRow($task);
	}
	
	/**
	 * Get all today's events from the database.
	 * 
	 * @param GO_base_Model_User $user
	 * @return GO_Calendar_Model_Event[]
	 */
	private function _getEvents($user){
		$defaultCalendar = GO_Calendar_Model_Calendar::model()->getDefault($user);		
		
		$todayStart = strtotime('today')+1;
		$todayEnd = strtotime('tomorrow');
		
		if($defaultCalendar){
			$findParams = GO_Base_Db_FindParams::newInstance()
			->select()
			->order('start_time','ASC')
			->criteria(GO_Base_Db_FindCriteria::newInstance()
					->addCondition('calendar_id', $defaultCalendar->id)
					->addCondition('start_time', $todayStart,'>=')
					->addCondition('start_time', $todayEnd,'<')
			);
			$events = GO_Calendar_Model_Event::model()->find($findParams);
			
			return $events->fetchAll();
		} else {
			return array();
		}
	}
	
	/**
	 * Get all today's tasks from the database.
	 * 
	 * @param GO_base_Model_User $user
	 * @return GO_Tasks_Model_Task[]
	 */
	private function _getTasks($user){	
		$defaultTasklist = GO_Tasks_Model_Tasklist::model()->getDefault($user);
		
		$todayStart = strtotime('today');
		$todayEnd = strtotime('tomorrow');
		
		if($defaultTasklist){
			$findParams = GO_Base_Db_FindParams::newInstance()
			->select()
			->order('start_time','ASC')
			->criteria(GO_Base_Db_FindCriteria::newInstance()
					->addCondition('tasklist_id', $defaultTasklist->id)
					->addCondition('start_time', $todayStart,'>=')
					->addCondition('start_time', $todayEnd,'<')
			);
			$tasks = GO_Tasks_Model_Task::model()->find($findParams);
			
			return $tasks->fetchAll();
		} else {
			return array();
		}
	}
	
	/**
	 * Render the event row in the PDF
	 * 
	 * @param GO_Calendar_Model_Event $event
	 */
	private function _renderEventRow(GO_Calendar_Model_Event $event){	

		$html = '';
		$html .= '<tcpdf method="renderLine" />';
		$html .= '<b><font style="font-size:'.$this->_timeFontSize.'px">'.GO_Base_Util_Date_DateTime::fromUnixtime($event->start_time)->format('H:i').' - '.GO_Base_Util_Date_DateTime::fromUnixtime($event->end_time)->format('H:i').'</font> <font style="font-size:'.$this->_nameFontSize.'px">'.GO_Base_Util_String::text_to_html($event->getAttribute('name', 'html'), true).'</font></b>';
		if(!empty($event->description))
			$html .= 	'<br /><font style="font-size:'.$this->_descriptionFontSize.'px">'.$event->getAttribute('description', 'html').'</font>';
		
		$this->writeHTML($html, true, false, false, false, 'L');
	}
		
	/**
	 * Render the task row in the PDF
	 * 
	 * @param GO_Tasks_Model_Task $task
	 */
	private function _renderTaskRow($task){
		
		$html = '';
		$html .= '<tcpdf method="renderLine" />';
		$html .= '<b><font style="font-size:'.$this->_nameFontSize.'px">'.GO_Base_Util_String::text_to_html($task->getAttribute('name', 'html'),true).'</font></b>';
		if(!empty($task->description))
			$html .= 	'<br /><font style="font-size:'.$this->_descriptionFontSize.'px">'.$task->getAttribute('description', 'html').'</font>';

		$this->writeHTML($html, true, false, false, false, 'L');
	}
	
	/**
	 * Function to render the 2 dashes before the title
	 */
	protected function renderLine(){
		$oldX = $this->getX();
		$this->setX($oldX-14);
		$this->write(10, '--');
		$this->setX($oldX);
	}
}