<?php 

class GO_Cron_Model_PDF extends GO_Base_Util_Pdf {
			
	private $_cellheight = 15;
	private $_columnLeftWidth = 13;
	private	$_columnTimeWidth = 60;
	private	$_columnNameWidth = 0; //180;
	private	$_columnDescriptionWidth = 0; //240;
	private	$_headerFontSize = 12;
	private $_timeFontSize = 8;
	private	$_textFontSize = 9;
	private $_border = 0;
		
	protected $font = 'dejavusans';
	protected $font_size=9;
	
	public function setTitle($title){
		$this->title = $title;
	}
	
	public function setSubTitle($subtitle){
		$this->subtitle = $subtitle;
	}
	
	public function render($user){
		$this->SetAutoPageBreak(True, 60); // Set the auto pagebreak to 60 pixels from the bottom.
		$this->AddPage();
		$this->SetFillColor(255,255,255); // RESET FILL TO WHITE

		$textColor = $this->TextColor;
		$textFont = $this->getFontFamily();
		
		$events = $this->_getEvents($user);
		$tasks = $this->_getTasks($user);
		
		// RENDER EVENTS
		$this->SetTextColor(125,162,180);
		$this->SetFont($this->font,'',$this->_headerFontSize);

		$this->MultiCell(440,$this->_cellheight,'Events',0,'L',1,1);
		
		$this->SetTextColor($textColor);
		$this->SetFont($textFont,'',$this->_textFontSize);
		$this->MultiCell(0,$this->_cellheight,'',0,'L',1,1);
		
		$this->setEqualColumns(2);
		
		foreach($events as $event){
			$this->_renderEventRow($event);
		}
		
		//$this->selectColumn();
		
		// 2 empty lines
		$this->MultiCell(0,$this->_cellheight,'',0,'L',1,1);
		$this->MultiCell(0,$this->_cellheight,'',0,'L',1,1);
		
		// RENDER TASKS
		$this->SetTextColor(125,162,180);
		$this->SetFont($this->font,'',$this->_headerFontSize);

		$this->MultiCell(440,$this->_cellheight,'Tasks',0,'L',1,1);
		
		$this->SetTextColor($textColor);
		$this->SetFont($textFont,'',$this->_textFontSize);
		$this->MultiCell(0,$this->_cellheight,'',0,'L',1,1);
		
		//$this->setEqualColumns(2);
		
		foreach($tasks as $task){
			$this->_renderTaskRow($task);
		}
		
		$this->resetColumns();
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
		
		$eventStart = GO_Base_Util_Date_DateTime::fromUnixtime($event->start_time);
		$eventEnd = GO_Base_Util_Date_DateTime::fromUnixtime($event->end_time);
		$eventStartTime = $eventStart->format('H:i');
		$eventEndTime = $eventEnd->format('H:i');
		
		$timeString = $eventStartTime.' - '.$eventEndTime;
		
		$this->MultiCell($this->_columnLeftWidth,$this->_cellheight,'--',$this->_border,'L',1,0);
		
		$this->SetFontSize($this->_timeFontSize);
		$this->MultiCell($this->_columnTimeWidth,$this->_cellheight,$timeString,$this->_border,'L',1,0);
		
		$this->SetFontSize($this->_textFontSize);
		$this->MultiCell($this->_columnNameWidth,$this->_cellheight,$event->getAttribute('name', 'html'),$this->_border,'L',1,1,'','',true,0,true);
		
		if($event->description){
			$this->MultiCell($this->_columnLeftWidth,$this->_cellheight,'',$this->_border,'L',1,0);
			$this->MultiCell($this->_columnDescriptionWidth,$this->_cellheight,$event->getAttribute('description', 'html'),$this->_border,'L',1,1,'','',true,0,true);
		}
		//$this->MultiCell(0,$this->_cellheight,'',0,'L',1,1);
	}
	
	private function _renderTaskRow($task){
		$this->MultiCell($this->_columnLeftWidth,$this->_cellheight,'--',$this->_border,'L',1,0);
		$this->MultiCell($this->_columnDescriptionWidth,$this->_cellheight,$task->getAttribute('name', 'html'),$this->_border,'L',1,1,'','',true,0,true);
		
		if($task->description){
			$this->MultiCell($this->_columnLeftWidth,$this->_cellheight,'',$this->_border,'L',1,0);
			
			$this->MultiCell($this->_columnDescriptionWidth,$this->_cellheight,$task->getAttribute('description', 'html'),$this->_border,'L',1,1,'','',true,0,true);
		}
		//$this->MultiCell(0,$this->_cellheight,'',0,'L',1,1);
	}
}