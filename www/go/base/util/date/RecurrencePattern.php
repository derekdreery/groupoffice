<?php

/**
 * An Icalendar Rrule object
 */
class GO_Base_Util_Date_RecurrencePattern{
	
	protected $_count;
	/**
	 *
	 * @var int Unix timstamp 
	 */
	protected $_until;
	protected $_freq;
	protected $_interval;
	/**
	 *
	 * @var array eg. array('MO','WE') OR array('1MO') in case of the first monday
	 */
	protected $_byday;
	protected $_bymonth;
	protected $_bymonthday;
	protected $_eventStartTime;
	
	protected $_days=array('SU','MO','TU','WE','TH','FR','SA');

	
	public function __construct($params = array()){
		$this->setParams($params);
	}
	
	public function setParams($params){
		foreach($params as $paramName=>$value)
		{
			$setter = '_set'.ucfirst($paramName);
			if(method_exists($this, $setter)){
				$this->$setter($value);
			}else
			{
				$var = '_'.$paramName;
				$this->$var=$value;
			}			
			
		}
	}
	
	public function getParams(){
		return array(
				'interval' => $this->_interval,
				'freq' => $this->_freq,
				'until' => $this->_until,
				'count' => $this->_count,
				'byday' => $this->_byday,
				'bymonth' => $this->_bymonth,
				'bymonthday' => $this->_bymonthday,
				'eventStartTime' => $this->_eventStartTime,
		);
	}
	
	
	protected function _setCount($count){
		$this->_count=intval($count);
	}
	
	protected function _setFreq($freq){
		if(empty($freq))
			throw new Exception("Frequency can't be empty!");
			
		$this->_freq=$freq;
	}
	
	protected $_recurPositionStartTime;

	public function getNextRecurrence($startTime=false)
	{
		if(!isset($this->_recurPositionStartTime))
			$this->_recurPositionStartTime=time();
		
		if(!$startTime)
			$startTime=$this->_recurPositionStartTime;
		
		$func = '_getNextRecurrence'.ucfirst($this->_freq);
		
		$next=call_user_func(array($this, $func),$startTime);
		if(empty($this->_until) || $next<GO_Base_Util_Date::date_add($this->_until,1)){
			
			//check next recurrence from one day later
			$this->_recurPositionStartTime=GO_Base_Util_Date::date_add($next,1);
						
			return $next;
		}else
		{
			unset($this->_recurPositionStartTime);
			return false;
		}
	}
	
	protected function _getNextRecurrenceDaily($startTime){
							
		$daysBetweenNextAndFirstEvent=$this->_findNumberOfPeriods($startTime, $this->_interval, 'days');
		$recurrenceTime =  GO_Base_Util_Date::date_add($this->_eventStartTime,$daysBetweenNextAndFirstEvent);
		return $recurrenceTime;
		
	}
	
	protected function _getNextRecurrenceWeekly($startTime){
				
		/*
		 * eg. Recurs every 2 weeks on Wednesday. Starting on 04-09-2011.
		 * $startTime = 17-09-2011
		 * This function should return 21-09-2011
		 * 
	*/
		
		
		
		$period = $this->_interval*7;
	
		$daysBetweenNextAndFirstEvent=$this->_findNumberOfPeriods($startTime, $period, 'days', false);
		
		$firstPossibleWeekStart = $recurrenceTime = GO_Base_Util_Date::date_add($this->_eventStartTime,$daysBetweenNextAndFirstEvent);
		
		//check each weekday for a match
		for($day=0;$day<7;$day++){
			
			
			if($recurrenceTime>=$startTime){
				$weekdayInt = date('w',$recurrenceTime); //0-6
				$weekday = $this->_days[$weekdayInt]; //WE
				//echo $weekdayInt.':'.$weekday."\n";	
				if(in_array($weekday, $this->_byday)){
					return $recurrenceTime;
				}			
			}
			
			$recurrenceTime = GO_Base_Util_Date::date_add($recurrenceTime,1);
		}
		
	  //It did not fall in this week. Check the next week in the recurrence
		return $this->_getNextRecurrenceWeekly(GO_Base_Util_Date::date_add($firstPossibleWeekStart,$period));
	}
	
	protected function _hasWeekday($weekday){
		
	}
	
	protected function _getNextRecurrenceMonthly($startTime){
							
		if(empty($this->_byday)){
			$daysBetweenNextAndFirstEvent=$this->_findNumberOfPeriods($startTime, $this->_interval, 'm');
			$recurrenceTime =  GO_Base_Util_Date::date_add($this->_eventStartTime, 0, $daysBetweenNextAndFirstEvent);
		}else
		{
			//bv.  de 3e woensdag vd maand
		}
		
		return $recurrenceTime;		
	}
	
	protected function _getNextRecurrenceYearly($startTime){
		$daysBetweenNextAndFirstEvent=$this->_findNumberOfPeriods($startTime, $this->_interval, 'y');
		$recurrenceTime =  GO_Base_Util_Date::date_add($this->_eventStartTime, 0, 0, $daysBetweenNextAndFirstEvent);
		
		return $recurrenceTime;
	}
	
	
	/**
	 * Returns the minimum number of periods between the start of the recurrence
	 * and a given time.
	 * 
	 * @param int $startTime Unixtime of start time
	 * @param int $period Number of days, months or years
	 * @param string $type days=days, m=months, y= years 
	 * @param string ceil or floor the difference.  For weekly we need to floor it because the time can fall in the week where a recurrence may take place in. 
	 * @return int Number of periods that fall between event start and start time
	 */
	protected function _findNumberOfPeriods($startTime, $period, $type, $ceil=true){
		
		$eventStartDateTime = new GO_Base_Util_Date_DateTime(date('c',$this->_eventStartTime));
		$startDateTime= new GO_Base_Util_Date_DateTime(date('c',$startTime));
		$diff = $eventStartDateTime->diff($startDateTime, true); 
		
//		echo "-\n";
//		echo date('c',$this->_eventStartTime)."\n";
//		echo date('c',$startTime)."\n";
		
		
		$elapsed = $diff->$type; //get the days, months or years elapsed since the event.
		
//		echo $elapsed."\n";
//		echo "-\n";
		
		$devided = $elapsed/$period; 
		
		
		$rounded = $ceil ? ceil($devided) : floor($devided);
		$periodsBetweenNextAndFirstEvent = $period*$rounded;
		
//		if($ceil){
//			if($periodsBetweenNextAndFirstEvent == $elapsed)
//				$periodsBetweenNextAndFirstEvent+=$period;
//		}
		
		return $periodsBetweenNextAndFirstEvent;
		
	}

	/**
	 * Calculate and if needed shifts a task item to another day of the week when GMT = +>1 or ->1
	 * 
	 * @param boolean $toGmt Will be converted to GMT time (true) or from GMT time (false).
	 */
	protected function _shiftDays($toGmt=true){
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
	
}