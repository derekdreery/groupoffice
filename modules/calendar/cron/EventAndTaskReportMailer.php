<?php

class GO_Calendar_Cron_EventAndTaskReportMailer extends GO_Base_Cron_AbstractCron {
	
	/**
	 * Return true or false to enable the selection fo users and groups for 
	 * this cronjob.
	 * 
	 * CAUTION: This will give the run() function a different behaviour. 
	 *					Please see the documentation of the run() function 
	 *					to see what is different.
	 */
	public function enableUserAndGroupSupport(){
		return true;
	}
	
	/**
	 * Get the unique name of the Cronjob
	 * 
	 * @return String
	 */
	public function getLabel(){
		return GO::t('cronEventAndTaskReportMailer','calendar');
	}
	
	/**
	 * Get the unique name of the Cronjob
	 * 
	 * @return String
	 */
	public function getDescription(){
		return GO::t('cronEventAndTaskReportMailerDescription','calendar');
	}
	
	/**
	 * The code that needs to be called when the cron is running
	 * 
	 * If $this->enableUserAndGroupSupport() returns TRUE then the run function 
	 * will be called for each $user. (The $user parameter will be given)
	 * 
	 * If $this->enableUserAndGroupSupport() returns FALSE then the 
	 * $user parameter is null and the run function will be called only once.
	 * 
	 * @param GO_Base_Cron_CronJob $cronJob
	 * @param GO_Base_Model_User $user [OPTIONAL]
	 */
	public function run(GO_Base_Cron_CronJob $cronJob,GO_Base_Model_User $user = null){
		
		GO::session()->runAsRoot();
		$pdf = $this->_getUserPdf($user);
		if($this->_sendEmail($user,$pdf))
			GO::debug("CRON MAIL IS SEND!");
		else
			GO::debug("CRON MAIL HAS NOT BEEN SEND!");		
	}
	
	/**
	 * Get the pdf of the given user
	 * 
	 * @param GO_Base_Model_User $user
	 * @return String
	 */
	private function _getUserPdf(GO_Base_Model_User $user){		
		$pdf = new eventAndTaskPdf();
		$pdf->setTitle($user->name); // Set the title in the header of the PDF
		$pdf->setSubTitle(GO::t('cronEventAndTaskReportMailerPdfSubtitle','calendar')); // Set the subtitle in the header of the PDF
		$pdf->render($user); // Pass the data to the PDF object and let it draw the PDF
		
		return $pdf->Output('','s');// Output the pdf
	}
	
	/**
	 * Send the email to the users
	 * 
	 * @param GO_Base_Model_User $user
	 * @param eventAndTaskPdf $pdf
	 * @return Boolean
	 */
	private function _sendEmail(GO_Base_Model_User $user,$pdf){
		
		$filename = GO_Base_Fs_File::stripInvalidChars($user->name).'.pdf'; //Set the PDF filename
		$filename = str_replace(',', '', $filename);
		
		$mailSubject = GO::t('cronEventAndTaskReportMailerSubject','calendar');
		$body = GO::t('cronEventAndTaskReportMailerContent','calendar');
		
		$message = GO_Base_Mail_Message::newInstance(
										$mailSubject
										)->setFrom(GO::config()->webmaster_email, GO::config()->title)
										->addTo($user->email);

		$message->setHtmlAlternateBody(nl2br($body));
		$message->attach(Swift_Attachment::newInstance($pdf,$filename,'application/pdf'));
		GO::debug('CRON SEND MAIL TO: '.$user->email);
		return GO_Base_Mail_Mailer::newGoInstance()->send($message);
	}
}

/**
 * Class to render the PDF
 */
class eventAndTaskPdf extends GO_Base_Util_Pdf {
			
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
			//->order(array('start_time','name'),array('ASC','ASC'))
			->criteria(GO_Base_Db_FindCriteria::newInstance()
					->addCondition('calendar_id', $defaultCalendar->id)
			);
			$events = GO_Calendar_Model_Event::model()->findCalculatedForPeriod($findParams,$todayStart,$todayEnd);
			
			return $events; //->fetchAll();
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
			->order(array('start_time','name'),array('ASC','ASC'))
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
	private function _renderEventRow(GO_Calendar_Model_LocalEvent $event){	

		$html = '';
		$html .= '<tcpdf method="renderLine" />';
		$html .= '<b><font style="font-size:'.$this->_timeFontSize.'px">'.GO_Base_Util_Date_DateTime::fromUnixtime($event->getAlternateStartTime())->format('H:i').' - '.GO_Base_Util_Date_DateTime::fromUnixtime($event->getAlternateEndTime())->format('H:i').'</font> <font style="font-size:'.$this->_nameFontSize.'px">'.GO_Base_Util_String::text_to_html($event->getName(), true).'</font></b>';
		$realEvent = $event->getEvent();
		if(!empty($realEvent->description))
			$html .= 	'<br /><font style="font-size:'.$this->_descriptionFontSize.'px">'.$realEvent->getAttribute('description', 'html').'</font>';
		
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