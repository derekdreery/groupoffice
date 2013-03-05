<?php 

class GO_Cron_Model_PDF extends GO_Base_Util_Pdf {
			
	private $_columnLeftWidth = '14';
	private	$_columnTextWidth = '300';
	private	$_headerFontSize = '16';
	private	$_headerFontColor = '#3194D0';
	private $_timeFontSize = '12';
	private	$_textFontSize = '14';
	private $_border = 0;
		
	protected $font = 'dejavusans';
	protected $font_size=9;
	
	public function setTitle($title){
		$this->title = $title;
	}
	
	public function setSubTitle($subtitle){
		$this->subtitle = $subtitle;
	}
	
	public function AddPage($orientation = '', $format = '', $keepmargins = false, $tocpage = false) {
		parent::AddPage($orientation, $format, $keepmargins, $tocpage);
		$this->SetAutoPageBreak(True, 30);
		$this->setEqualColumns(2);
	}
	
	public function render($user){
		//$this->SetAutoPageBreak(True, 60); // Set the auto pagebreak to 60 pixels from the bottom.
		
		$this->AddPage();
	//	$this->setEqualColumns(2);
		$eventsString = GO::t('appointments','calendar');
		$tasksString = GO::t('tasks','tasks');
		
		$textColor = $this->TextColor;
		$textFont = $this->getFontFamily();
		
		$events = $this->_getEvents($user);
		$tasks = $this->_getTasks($user);
		
		// RENDER EVENTS
		$html = '';
		$html .= '<h2 style="color:'.$this->_headerFontColor.';font-size:'.$this->_headerFontSize.'px;">'.$eventsString.'</h2>';
		$html .= '<br />';
		
		$html .= '<table border="'.$this->_border.'">';

		if(count($events) > 0){
			foreach($events as $event)
				$html .= $this->_renderEventRow($event);
		} else {
			$html .= '<tr><td width="'.$this->_columnLeftWidth.'">&nbsp;</td><td width="'.$this->_columnTextWidth.'">&nbsp;</td></tr>';
		}

		$html .= '</table>';
		$html .= '<br /><br /><br />';
		
		// RENDER TASKS
		$html .= '<h2 style="color:'.$this->_headerFontColor.';font-size:'.$this->_headerFontSize.'px;">'.$tasksString.'</h2>';
		$html .= '<br />';

		$html .= '<table border="'.$this->_border.'">';
		
		if(count($tasks) > 0){
			foreach($tasks as $task)
				$html .= $this->_renderTaskRow($task);
		} else {
			$html .= '<tr><td width="'.$this->_columnLeftWidth.'">&nbsp;</td><td width="'.$this->_columnTextWidth.'">&nbsp;</td></tr>';
		}
		
		$html .= '</table>';
			
		//var_export($html);
		$this->writeHTML($html, true, false, false, false, 'L');
	}
	
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
	
	private function _getTasks($user){	
		$defaultTasklist = GO_Tasks_Model_Tasklist::model()->getDefault($user);
		
		$todayStart = strtotime('today')+1;
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
	
	
	private function _renderEventRow(GO_Calendar_Model_Event $event){	
		
		$html = '';
		
		$eventStart = GO_Base_Util_Date_DateTime::fromUnixtime($event->start_time);
		$eventEnd = GO_Base_Util_Date_DateTime::fromUnixtime($event->end_time);
		$eventStartTime = $eventStart->format('H:i');
		$eventEndTime = $eventEnd->format('H:i');
		
		$nameString = $event->getAttribute('name', 'html');
		$nameString = GO_Base_Util_String::text_to_html($nameString, true);
		
		$timeString = $eventStartTime.' - '.$eventEndTime;
		
		$html .= '<tr><td width="'.$this->_columnLeftWidth.'" style="font-size:'.$this->_textFontSize.'px;">--</td>';
		$html .= '<td width="'.$this->_columnTextWidth.'"><font style="font-size:'.$this->_timeFontSize.'px;">'.$timeString.'</font> <font style="font-size:'.$this->_textFontSize.'px;">'.$nameString.'</font></td>';
		
		if($event->description){
			$html .= '</tr><tr><td>&nbsp;</td>';
			$html .= '<td>'.$event->getAttribute('description', 'html').'</td>';
		}	
		
		$html .='</tr>';
		
		return $html;
	}
	
	private function _renderTaskRow($task){
		
		$html = '';
		
		$nameString = $task->getAttribute('name', 'html');
		$nameString = GO_Base_Util_String::text_to_html($nameString, true);
		
		$html .= '<tr><td width="'.$this->_columnLeftWidth.'" style="font-size:'.$this->_textFontSize.'px;">--</td>';
		$html .= '<td style="font-size:'.$this->_textFontSize.'px;">'.$nameString.'</td>';
		
		if($task->description){
			$html .= '</tr><tr><td>&nbsp;</td>';
			$html .= '<td>'.$task->getAttribute('description', 'html').'</td>';
		}	
		
		$html .='</tr>';
		
		return $html;
	}
}