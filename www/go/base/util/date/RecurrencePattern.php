<?php
/*
 * Copyright Intermesh BV
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * A date recurrence pattern to calculate occurence times.
 * 
 * @copyright Copyright Intermesh BV
 * @version $Id: Number.php 7962 2011-08-24 14:48:45Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.util.date
 * 
 * @property int $until Recurrence end date
 * @property string $freq Recurrence type. DAILY, WEEKLY, MONTHLY and YEARLY
 * @property int $interval Repeats every n days/weeks/months/years
 * @property array $byday Day to occur with monthly recurrence
 * @property array $bymonth Month to occur with yearly recurrence
 * @property int $bymonthday The day of the mont
 * @property int $eventStartTime Start time of the event
 * @property int $bysetpos The nth day of the mont
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
	protected $_eventstarttime;
	protected $_bysetpos;
	
	protected $_days=array('SU','MO','TU','WE','TH','FR','SA');
	
	public function __construct($params = array()){
		$this->setParams($params);
	}
	
	public function setParams($params){
		foreach($params as $paramName=>$value)
		{
			$this->$paramName=$value;
		}
	}
	
	public function __set($name, $value) {
		$setter = '_set'.ucfirst($name);
		if(method_exists($this, $setter)){
			$this->$setter($value);
		}else
		{
			$var = '_'.strtolower($name);
			if(property_exists($this, $var))
				$this->$var=$value;
			else
				trigger_error ("Access to undefined recurrence property ".$name);
		}			
	}
	
	public function __get($name) {
		$getter = '_get'.ucfirst($name);
		if(method_exists($this, $getter)){
			$this->$getter($name);
		}else
		{
			$var = '_'.strtolower($name);		
			if(property_exists($this, $var))
				return $this->$var;
			else
				trigger_error ("Access to undefined recurrence property ".$name);
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
				'eventstarttime' => $this->_eventstarttime,
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
	
	public function setRecurpositionStartTime($startTime){
		$this->_recurPositionStartTime=$startTime;
	}

	/**
	 * Return the first valid occurrence time after the given startTime.
	 * 
	 * If $startTime is omitted it returns the next recurrence since last call or the first occurrence.
	 * 
	 * @param int $startTime Unix timstamp
	 * @return int Unix timestamp 
	 */
	public function getNextRecurrence($startTime=false)
	{
		if(!isset($this->_recurPositionStartTime) || $this->_recurPositionStartTime<$this->_eventstarttime)
			$this->_recurPositionStartTime=$this->_eventstarttime;
		
		if(!$startTime)
			$startTime=$this->_recurPositionStartTime;

		//if the start of the event matches the time to check then return 0.
		//the next recurrence matches exactly.
		if($this->_eventstarttime==$startTime){
			$next = $startTime;
		}else
		{
			$func = '_getNextRecurrence'.ucfirst($this->_freq);		
			$next=call_user_func(array($this, $func),$startTime);
		}
		if(empty($this->_until) || $next<GO_Base_Util_Date::date_add($this->_until,1)){
			
			//check next recurrence from one day later
			$this->_recurPositionStartTime=$next+1;//GO_Base_Util_Date::date_add($next,1);
			//echo "N:".date('c', $this->_recurPositionStartTime)."\n";
			return $next;
		}else
		{
			unset($this->_recurPositionStartTime);
			return false;
		}
	}
	
	protected function _getNextRecurrenceDaily($startTime){
							
		$daysBetweenNextAndFirstEvent=$this->_findNumberOfDays($startTime, $this->_interval);
		$recurrenceTime =  GO_Base_Util_Date::date_add($this->_eventstarttime,$daysBetweenNextAndFirstEvent);
		return $recurrenceTime;
		
	}
	
	protected function _getNextRecurrenceWeekly($startTime){
		
		
		if(empty($this->_byday))
		{
			//this rrule is invalid. no days set for weekly recurrence.
			return false;
		}
				
		/*
		 * eg. Recurs every 2 weeks on Wednesday. Starting on 04-09-2011.
		 * $startTime = 17-09-2011
		 * This function should return 21-09-2011
		 */	
		
		$period = $this->_interval*7;
	
		$daysBetweenNextAndFirstEvent=$this->_findNumberOfDays($startTime, $period, false);
		
		$firstPossibleWeekStart = $recurrenceTime = GO_Base_Util_Date::date_add($this->_eventstarttime,$daysBetweenNextAndFirstEvent);
		
		//check each weekday for a match
		for($day=0;$day<7;$day++){			
			
			if($recurrenceTime>=$startTime){			
				if($this->_hasWeekday($recurrenceTime)){
					return $recurrenceTime;
				}			
			}
			
			$recurrenceTime = GO_Base_Util_Date::date_add($recurrenceTime,1);
		}
		
	  //It did not fall in this week. Check the next week in the recurrence
		return $this->_getNextRecurrenceWeekly(GO_Base_Util_Date::date_add($firstPossibleWeekStart,$period));
	}
	
	protected function _getNextRecurrenceMonthly($startTime){
		
							
		if(empty($this->_byday)){
			//eg. every 12th of the month
			$monthBetweenNextAndFirstEvent=$this->_findNumberOfMonths($startTime, $this->_interval);
			//echo $monthBetweenNextAndFirstEvent."\n";
			
			$recurrenceTime =  GO_Base_Util_Date::date_add($this->_eventstarttime, 0, $monthBetweenNextAndFirstEvent);
		}else
		{

			//set event start to first day of month for calculation of the number of periods.
			$this->_eventstarttime=mktime(0,0,0,date('m',$this->_eventstarttime),1,date('Y',$this->_eventstarttime));
			
			//eg. 3rd monday of the month
			$monthBetweenNextAndFirstEvent=$this->_findNumberOfMonths($startTime, $this->_interval, false);
			
			$recurrenceTime = $firstPossibleTime=GO_Base_Util_Date::date_add($this->_eventstarttime, 0, $monthBetweenNextAndFirstEvent);
			
			$currentMonth = $startMonth = date('m', $recurrenceTime);
						
			while($currentMonth==$startMonth){
				
				$bySetPos = ceil(date('j', $recurrenceTime)/7);
			
				if($recurrenceTime>=$startTime){
					if($this->_hasWeekday($recurrenceTime, $bySetPos)){
						return $recurrenceTime;
					}
				}
				
				$recurrenceTime =  GO_Base_Util_Date::date_add($recurrenceTime, 1);
				$currentMonth = date('m', $recurrenceTime);
			}
			
			$nextDate = date('Y', $firstPossibleTime).'-'.($startMonth+$this->_interval).'-01';

			//It did not fall in this month. Check the next month in the recurrence
			return $this->_getNextRecurrenceMonthly(mktime(0,0,0,$startMonth+$this->_interval,1,date('Y',$firstPossibleTime)));
		}
		
		return $recurrenceTime;		
	}
	
	private function _splitDaysAndSetPos(){
		
		$response['days']=array();
		$response['bysetpos']=array();
		
		foreach($this->_byday as $day){
			if(strlen($day)>2){
				$_day = substr($day,1);
				$response['days'][]=$_day;
				$response['bysetpos'][$_day]=$day[0];
			}else
			{
				$response['days'][]=$day;
				$response['bysetpos'][$day]=$this->_bysetpos;
			}
		}
		
		return $response;
			
	}
	
	/**
	 * Check if a weekday of a given time matches the recurrence pattern
	 * 
	 * @param int $time unix timestamp
	 * @param int $bySetPos The nth occurrence in a month for a monthly recurrence.
	 * @return boolean 
	 */
	private function _hasWeekday($time, $bySetPos=0){
		$weekdayInt = date('w',$time); //0-6
		$weekday = $this->_days[$weekdayInt]; //WE
		//echo $weekdayInt.':'.$weekday."\n";	
		
		if($bySetPos==0){
			//for weekly
			if(in_array($weekday, $this->_byday))
				return true;			
		}else
		{
			//for every nth weekday in the month
			$daysAndSetPos = $this->_splitDaysAndSetPos();
			if(in_array($weekday, $daysAndSetPos['days']) && $bySetPos==$daysAndSetPos['bysetpos'][$weekday])
				return true;
		}
		
		return false;
		
	}
	
	protected function _getNextRecurrenceYearly($startTime){
		$monthsBetweenNextAndFirstEvent=$this->_findNumberOfMonths($startTime, $this->_interval*12);
		$recurrenceTime =  GO_Base_Util_Date::date_add($this->_eventstarttime, 0, $monthsBetweenNextAndFirstEvent);
		
		return $recurrenceTime;
	}
	
	
	protected function _findNumberOfMonths($startTime, $interval, $ceil=true){
		
		$intervalYears = date('Y', $startTime)-date('Y', $this->_eventstarttime);
		$intervalMonths = date('n', $startTime)-date('n', $this->_eventstarttime);
		$intervalMonths = 12*$intervalYears+$intervalMonths;

		$devided = $intervalMonths/$interval;
		$rounded = ceil($devided);
		
		$rounded = $ceil ? ceil($devided) : floor($devided);
		$periodsBetweenNextAndFirstEvent = $interval*$rounded;
		
		if($ceil){
			if($periodsBetweenNextAndFirstEvent == $intervalMonths)
				$periodsBetweenNextAndFirstEvent+=$interval;
		}
		
		return $periodsBetweenNextAndFirstEvent;	
	}
	
	
	/**
	 * Returns the minimum number of periods between the start of the recurrence
	 * and a given time.
	 * 
	 * @param int $startTime Unixtime of start time
	 * @param int $interval Number of days, months or years
	 * @param string $type days=days, m=months, y= years 
	 * @param string ceil or floor the difference.  For weekly we need to floor it because the time can fall in the week where a recurrence may take place in. 
	 * @return int Number of periods that fall between event start and start time
	 */
	protected function _findNumberOfDays($startTime, $interval=1, $ceil=true){
		$eventStartDateTime = new GO_Base_Util_Date_DateTime(date('c',$this->_eventstarttime));
		$startDateTime= new GO_Base_Util_Date_DateTime(date('c',$startTime));
		
		//diff is only compatible with 5.3 and we want 5.2 compatibility
		//$diff = $eventStartDateTime->diff($startDateTime, true); 
		//$elapsed = $diff->days; //get the days, months or years elapsed since the event.
		
		$elapsed = $days = $eventStartDateTime->getDaysElapsed($startDateTime);
		$devided = $elapsed/$interval; 
		
		
		$rounded = $ceil ? ceil($devided) : floor($devided);
		$periodsBetweenNextAndFirstEvent = $interval*$rounded;
		
		
		if($ceil){
			if($periodsBetweenNextAndFirstEvent == $elapsed)
				$periodsBetweenNextAndFirstEvent+=$interval;
		}	
		
		return $periodsBetweenNextAndFirstEvent;		
	}

	/**
	 * Calculate and if needed shifts a task item to another day of the week when GMT = +>1 or ->1
	 * 
	 * @param boolean $toGmt Will be converted to GMT time (true) or from GMT time (false).
	 */
	protected function shiftDays($days, $toGmt=true){
		$date = new DateTime(date('Y-m-d G:i', $this->_eventstarttime));
		$timezoneOffset = $date->getOffset();
				
		$localStartHour = $date->format('G');
		
		$gmtStartHour = $localStartHour-($timezoneOffset/3600);

		if ($gmtStartHour > 23) {
			$shiftDay = $toGmt ? 1 : -1;
		}elseif ($gmtStartHour < 0) {
			$shiftDay = $toGmt ? -1 : 1;
		} else {
			$shiftDay = 0;
		}	
	
		$newByDay=array();
		if($shiftDay!=0){
			foreach($days as $day){
				
				$number = "";
				$dayStr = $day;
				if(strlen($day)>2){
					$number = substr($day,0,1);
					$dayStr = substr($day, 1);
				}
					
				$shiftedDay = $this->_days[array_search($dayStr, $this->_days)+$shiftDay];
				$newByDay[]=$number.$shiftedDay;
			}						
			return $newByDay;
		}else
		{
			return $days;
		}
	}	
}