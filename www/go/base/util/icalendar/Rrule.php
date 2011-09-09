<?php

/**
 * An Icalendar Rrule object
 */
class GO_Base_Util_Icalendar_Rrule{
	
	private $_count;
	/**
	 *
	 * @var int Unix timstamp 
	 */
	private $_until;
	private $_freq;
	private $_interval;
	/**
	 *
	 * @var array eg. array('MO','WE') OR array('1MO') in case of the first monday
	 */
	private $_byday;
	private $_bymonth;
	private $_bymonthday;
	private $_eventStartTime;
	private $_bysetpos;
	
	private $_days=array('SU','MO','TU','WE','TH','FR','SA');

	/**
	 * Create a Rrule object from a Rrule string. This function automatically finds 
	 * out which Rrule version is used. 
	 * 
	 * @param String $rrule 'FREQ=DAILY;UNTIL=22-02-2222;INTERVAL=2;
	 */
	public function readIcalendarRruleString($eventStartTime, $rrule) {

		$this->_eventStartTime=$eventStartTime;
		
		$rrule = str_replace('RRULE:', '', $rrule);
		
		if (strpos($rrule, 'FREQ') === false) {
			$this->_parseRruleIcalendarV1($rrule);
		} else {
			$this->_parseRruleIcalendar($rrule);
		}
	}
	
	public function setParams($params){
		foreach($params as $paramName=>$value)
		{
			$var = '_'.$paramName;
			$this->$var=$value;
		}
	}
	
	private function _reset()
	{

		unset($this->_interval);
		unset($this->_byday);
		unset($this->_freq);
	}

	/**
	 * Output a rrule
	 * 
	 * @return String $rrule eg.: 'FREQ=DAILY;UNTIL=22-02-2222;INTERVAL=2;
	 */
	public function createRrule($params=array()) {
		
		$this->setParams ($params);
		
		$rrule = 'RRULE:INTERVAL='.$this->_interval.';FREQ='.$this->_freq;

		switch($this->_freq)
		{
			case 'WEEKLY':
				$rrule .= ";BYDAY=".implode(',', $this->_byday);
			break;

			case 'MONTHLY':				
				if($this->_bymonthday){
					$rrule .= ';BYMONTHDAY='.date('j', $this->_eventStartTime);
				}else
				{
					$rrule .= ';BYDAY='.implode(',', $this->_byday);
				}
			break;
		}
			
		if ($this->_until>0)
		{
			$rrule .= ";UNTIL=".date('Ymd', $this->_until);
		}
		return $rrule;
	}

	public function getNextRecurrence($startTime=false)
	{
		if(!$startTime)
			$startTime=time();
		
		$func = '_getNextRecurrence'.ucfirst($this->_freq);
		
		$next=call_user_func(array($this, $func),$startTime);
		if(empty($this->_until) || $next<GO_Base_Util_Date::date_add($this->_until,1))
			return $next;
		else
			return false;		
	}
	
	private function _getNextRecurrenceDaily($startTime){
							
		$daysBetweenNextAndFirstEvent=$this->_findNumberOfPeriods($startTime, $this->_interval, 'd');		
		$recurrenceTime =  GO_Base_Util_Date::date_add($this->_eventStartTime,$daysBetweenNextAndFirstEvent); 
		
		return $recurrenceTime;		
	}
	
	private function _getNextRecurrenceWeekly($startTime){
							
		$daysBetweenNextAndFirstEvent=$this->_findNumberOfPeriods($startTime, $this->_interval, 'd');		
		$recurrenceTime =  GO_Base_Util_Date::date_add($this->_eventStartTime,$daysBetweenNextAndFirstEvent); 
		
		return $recurrenceTime;
	}
	
	
	private function _getNextRecurrenceMonthly($startTime){
							
		if(empty($this->_bydays)){
			$daysBetweenNextAndFirstEvent=$this->_findNumberOfPeriods($startTime, $this->_interval, 'm');		
			$recurrenceTime =  GO_Base_Util_Date::date_add($this->_eventStartTime, 0, $daysBetweenNextAndFirstEvent); 
		}else
		{
			// IEDERE DAG VAN DE MAAND
			
			// IEDERE 2E DAG VAN DE MAAND
		}
		
		return $recurrenceTime;		
	}
	
	private function _getNextRecurrenceYearly($startTime){							
		$daysBetweenNextAndFirstEvent=$this->_findNumberOfPeriods($startTime, $this->_interval, 'y');		
		$recurrenceTime =  GO_Base_Util_Date::date_add($this->_eventStartTime, 0, 0, $daysBetweenNextAndFirstEvent); 
		
		return $recurrenceTime;		
	}
	
	
	/**
	 * 
	 * @param int $startTime Unixtime of start time
	 * @param int $period Number of days, months or years
	 * @param string $type d=days, m=months, y= years 
	 * @return int Number of periods that fall between event start and start time
	 */
	private function _findNumberOfPeriods($startTime, $period, $type){
		
		$eventStartDateTime = new GO_Base_Util_DateTime(date('c',$this->_eventStartTime));
		$startDateTime= new GO_Base_Util_DateTime(date('c',$startTime));
		$diff = $eventStartDateTime->diff($startDateTime);
		
		
		
		$elapsed = $diff->$type; //get the days, months or years elapsed since the event.
		//var_dump($intervalDays);
		$devided = $elapsed/$period; 
		$rounded = ceil($devided);
		$periodsBetweenNextAndFirstEvent = $period*$rounded;
		
		if($periodsBetweenNextAndFirstEvent == $elapsed)
			$periodsBetweenNextAndFirstEvent+=$period;
		
		return $periodsBetweenNextAndFirstEvent;
		
	}

	/**
	 * Read an array of params and convert the to a Rrule object.
	 * 
	 * @param int $eventStartTime
	 * @param array $params 
	 */
	public function readInputArray($eventStartTime, $params) {
		
		$this->_eventStartTime=$eventStartTime;
		$this->_freq=strtoupper($params['freq']);
		$this->_interval = intval($params['interval']);	
		$this->_bysetpos = intval($params['bysetpos']);
		$this->_until = !isset($params['repeat_forever']) && isset($params['until']) ? GO_Base_Util_Date::to_unixtime($params['until']) : '';
		$this->_byday=array();

		foreach($this->_days as $day){
			if(isset($_POST[$day])){
//				if(!empty($params['bysetpos']))
//				{
//					$day = $params['bysetpos'].$day;
//					$this->_bysetpos
			//	}
				$this->_byday[]=$day;
			}
		}		
		
		$this->_shiftDays();		
	}

	/**
	 * Creates a Rrule response which can be merged with a normal JSON response.
	 * 
	 * @return array Rrule 
	 */
	public function createOutputArray() {
		$response = array();
		if (isset($this->_freq)) {
			if (!empty($this->_until)){
				$response['until'] = GO_Base_Util_Date::get_timestamp($this->_until, false);
				$response['repeat_forever'] = 0;
			}else
			{
				$response['repeat_forever'] = 1;
			}
			
			$response['interval'] = $this->_interval;
			$response['freq'] = $this->_freq;
			switch ($this->_freq) {

				case 'WEEKLY':
					
					foreach($this->_byday as $day)
						$response[$day]=1;

				case 'MONTHLY':
					if (isset($this->_byday) && !empty($this->_byday)) {
						$response['bysetpos'] = $this->_byday[0];
						$day = substr($this->_byday[0], 1);
						
						$response[$day]=1;
						
					} else {
						$response['freq'];
					}
					break;
			}
		}
		return $response;
	}
	
	/**
	 * Calculate and if needed shifts a task item to another day of the week when GMT = +>1 or ->1
	 * 
	 * @param boolean $toGmt Will be converted to GMT time (true) or from GMT time (false).
	 */
	private function _shiftDays($toGmt=true){
		$date = new DateTime(date('Y-m-d G:i', $this->_eventStartTime));
		$timezoneOffset = $date->getOffset();
				
		$localStartHour = $date->format('G');
		
		$gmtStartHour = $localStartHour-($timezoneOffset/3600);

		if ($gmtStartHour > 23) {
			$shiftDay = $toGmt ? 1 : 0;
		}elseif ($gmtStartHour < 0) {
			$shiftDay = $toGmt ? 0 : 1;
		} else {
			$shiftDay = 0;
		}	
	
		$newByDay=array();
		if($shiftDay!=0){
			foreach($this->_byday as $day){
				
				$number = "";
				$dayStr = $day;
				if(strlen($day)>2){
					$number = substr($day,0,1);
					$dayStr = substr($day, 1);
				}
					
				
				$shiftedDay = $this->_days[array_search($dayStr, $this->_days)+$shiftDay];
				$newByDay[]=$number.$shiftedDay;
			}						
			$this->_byday=$newByDay;
		}
	}
	
	/**
	 * Set the values of this object from a version 1.0 Icalendar Rrule
	 * @TODO: This function is not yet changed for the new go version
	 * This must be a vcalendar 1.0 rrule
	 */
	private function _parseRruleIcalendarV1($rrule) {
		//we are attempting to convert it to icalendar format
		//GO Supports only one rule everything behind the first rule is chopped
		$hek_pos = strpos($rrule, '#');
		if ($hek_pos) {
			$space_pos = strpos($rrule, ' ', $hek_pos);
			if ($space_pos) {
				return false;
				//$rrule = substr($rrule,0,$space_pos);
			}
		}

		$expl_rrule = explode(' ', $rrule);

		//the count or until is always in the last element
		if ($until = array_pop($expl_rrule)) {
			if ($until{0} == '#') {
				$count = substr($until, 1);
				if ($count > 0) {
					$rrule_arr['COUNT'] = $count;
				}

				if (strlen($expl_rrule[count($expl_rrule) - 1]) > 2) {
					//this must be the end date
					$rrule_arr['UNTIL'] = array_pop($expl_rrule);
				}
			} else {
				$rrule_arr['UNTIL'] = $until;
			}
		}


		if ($rrule_arr['FREQ'] = array_shift($expl_rrule)) {

			$rrule_arr['INTERVAL'] = '';

			$lastchar = substr($rrule_arr['FREQ'], -1, 1);
			while (is_numeric($lastchar)) {
				$rrule_arr['INTERVAL'] = $lastchar . $rrule_arr['INTERVAL'];
				$rrule_arr['FREQ'] = substr($rrule_arr['FREQ'], 0, strlen($rrule_arr['FREQ']) - 1);
				$lastchar = substr($rrule_arr['FREQ'], -1, 1);
			}

			switch ($rrule_arr['FREQ']) {
				case 'D':
					$rrule_arr['FREQ'] = 'DAILY';
					break;

				case 'W':
					$rrule_arr['FREQ'] = 'WEEKLY';
					$rrule_arr['BYDAY'] = implode(',', $expl_rrule);
					break;

				case 'MP':
					$rrule_arr['FREQ'] = 'MONTHLY';

					//GO Supports only one position in the month
					/* if(count($expl_rrule) > 1)
					  {
					  //return false;
					  } */
					$month_time = array_shift($expl_rrule);
					//todo negative month times
					$rrule_arr['BYDAY'] = substr($month_time, 0, strlen($month_time) - 1) . array_shift($expl_rrule);
					break;

				case 'MD':
					$rrule_arr['FREQ'] = 'MONTHLY';
					//GO Supports only one position in the month
					if (count($expl_rrule) > 1) {
						return false;
					}

					$month_time = array_shift($expl_rrule);
					//todo negative month times
					//$rrule_arr['BYMONTHDAY'] = substr($month_time, 0, strlen($month_time)-1);
					//for nexthaus
					$rrule_arr['BYMONTHDAY'] = trim($month_time); //substr($month_time, 0, strlen($month_time)-1);
					break;

				case 'YM':
					$rrule_arr['FREQ'] = 'YEARLY';
					//GO Supports only one position in the month
					if (count($expl_rrule) > 1) {
						return false;
					}
					$rrule_arr['BYMONTH'] = array_shift($expl_rrule);
					break;

				case 'YD':
					//Currently not supported by GO
					return false;
					break;
			}
		}
	}

	/**
	 * Convert a Rrule object from an Icalendar Rrule string.
	 * 
	 * Set the values of this object from the latest version of an Icalendar Rrule
	 */
	private function _parseRruleIcalendar($rrule) {
		$params = explode(';', $rrule);

		while ($param = array_shift($params)) {
			$param_arr = explode('=', $param);

			if (isset($param_arr[0]) && isset($param_arr[1])) {
				$rrule_arr[strtoupper(trim($param_arr[0]))] = strtoupper(trim($param_arr[1]));
			}
		}
				//var_dump($rrule_arr);
		$this->_byday = !empty($rrule_arr['BYDAY']) ? explode(',', $rrule_arr['BYDAY']) : array();
		$this->_bymonth = !empty($rrule_arr['BYMONTH']) ? intval($rrule_arr['BYMONTH']) : 0;
		$this->_bymonthday = !empty($rrule_arr['BYMONTHDAY']) ? intval($rrule_arr['BYMONTHDAY']) : 0;
		$this->_freq = !empty($rrule_arr['FREQ']) ? $rrule_arr['FREQ'] : '';
		$this->_until = isset($rrule_arr['UNTIL']) ? GO_Base_Util_Date::parseIcalDate($rrule_arr['UNTIL']) : 0;
		$this->_count = !empty($rrule_arr['COUNT']) ? intval($rrule_arr['COUNT']) : 0;
		$this->_interval = !empty($rrule_arr['INTERVAL']) ? intval($rrule_arr['INTERVAL']) : 1;
		$this->_bysetpos = !empty($rrule_arr['BYSETPOS']) ? intval($rrule_arr['BYSETPOS']) : 0;
	}
}
