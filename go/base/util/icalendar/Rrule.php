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
 * Read and build RRULE strings into a recurrence pattern object
 * 
 * @copyright Copyright Intermesh BV
 * @version $Id: Number.php 7962 2011-08-24 14:48:45Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.util.icalendar
 */
class GO_Base_Util_Icalendar_Rrule extends GO_Base_Util_Date_RecurrencePattern
{
	/**
	 * Create a Rrule object from a Rrule string. This function automatically finds 
	 * out which Rrule version is used. 
	 * 
	 * @param String $eventstarttime The time the recurrence pattern starts. This is important to calculate the correct interval.
	 * @param String $rrule 'FREQ=DAILY;UNTIL=22-02-2222;INTERVAL=2;
	 */
	public function readIcalendarRruleString($eventstarttime, $rrule, $shiftDaysToLocal=false) {
		if(!empty($rrule)){			
			$this->_eventstarttime = $eventstarttime;
			$rrule = str_replace('RRULE:', '', $rrule);
			if (strpos($rrule, 'FREQ') === false){
				if(!$this->_parseRruleIcalendarV1($rrule))
					return false;
			}else{
				if(!$this->_parseRruleIcalendar($rrule))
					return false;
			}
			
			if($this->_until){
				$this->_until = GO_Base_Util_Date::date_add(
							GO_Base_Util_Date::clear_time($this->_until),
							0,
							0,
							0,
							23,//date('H', $this->_eventstarttime), 
							59//date('i', $this->_eventstarttime)
							);
			}
			
			if($shiftDaysToLocal)
				$this->shiftDays(false);
		}
		
	}
	
		
	public function readJsonArray($json)
	{
		$parameters = array();
		
		$parameters['interval'] = GO_Base_Util_Number::unlocalize($json['interval']);
		$parameters['freq'] = strtoupper($json['freq']);
		if($parameters['freq']=='MONTHLY_DATE')
			$parameters['freq']='MONTHLY';
		$parameters['eventstarttime'] = isset($json['eventstarttime'])?strtotime($json['eventstarttime']):strtotime($json['start_time']);
		$parameters['until'] = empty($json['repeat_forever']) && isset($json['until']) ? GO_Base_Util_Date::to_unixtime($json['until'].' 23:59') : 0; //date('G', $parameters['eventstarttime']).':'.date('i', $parameters['eventstarttime'])) : 0;
		$parameters['bymonth'] = isset($json['bymonth'])?$json['bymonth']:'';
		$parameters['bymonthday'] = isset($json['bymonthday'])?$json['bymonthday']:'';
		
		//bysetpos is not understood by old lib
		$parameters['bysetpos']=isset($json['bysetpos']) ? $json['bysetpos'] : 1;
		$parameters['byday']=array();
		
		foreach($this->_days as $day){
			if(isset($json[$day])){
				$day = $day;
//				if(!empty($json['bysetpos']))
//					$day = $json['bysetpos'].$day;
				
				$parameters['byday'][]=$day;
			}
		}		
		
		// Weekly recurrence _must_ have BYDAY set.
		if (strtolower($parameters['freq'])=='weekly' && count($parameters['byday'])<1 ) {
			$dayInt = date('w',$parameters['eventstarttime']);
			$parameters['byday'][] = $this->_days[$dayInt];
		}
		// Monthly by day recurrence _must_ have valid BYSETPOS and BYDAY.
		else if (strtolower($parameters['freq'])=='monthly' && isset($json['bysetpos'])) {
			if (count($parameters['byday'])<1)
				throw new Exception(GO::t('selectMonthlyDay'));
			else if (empty($json['bysetpos']))
				throw new Exception(GO::t('selectWeekOfMonth'));
		}
		
		$this->setParams($parameters);
		
		$this->shiftDays();			
	}
	
		
	/**
	 * Output a rrule
	 * 
	 * @return String $rrule eg.: 'FREQ=DAILY;UNTIL=22-02-2222;INTERVAL=2;
	 */
	public function createRrule() {
		
		if(empty($this->_freq))
			return "";
		
		$rrule = 'RRULE:FREQ='.$this->_freq;
		
		if($this->_interval>1)
			$rrule .= ';INTERVAL='.$this->_interval;

		switch($this->_freq)
		{
			case 'WEEKLY':
				$rrule .= ";BYDAY=".implode(',', $this->_byday);
			break;

			case 'MONTHLY':				
				if($this->_bymonthday){
					$rrule .= ';BYMONTHDAY='.date('j', $this->_eventstarttime);
				}elseif (!empty($this->_byday))
				{
					if(!empty($this->_bysetpos))
						$rrule .= ";BYSETPOS=".$this->_bysetpos;
						
					$rrule .= ';BYDAY='.implode(',', $this->_byday);
				}
			break;
		}
			
		if ($this->_until>0)
		{
			//$rrule .= ";UNTIL=".gmdate('Ymd\\THis\\Z', $this->_until);
			$rrule .= ";UNTIL=".date('Ymd\\THis', $this->_until);
		}
		return $rrule;
	}
	
	
	/**
	 * Output a vcalendar 1.0 rrule
	 * 
	 * @return String $rrule eg.: 'FREQ=DAILY;UNTIL=22-02-2222;INTERVAL=2;
	 */
	public function createVCalendarRrule() {
		
		$rrule = 'RRULE:';

		switch($this->_freq)
		{
			case 'DAILY':
				$rrule .= 'D'.$this->_interval;
				break;
			case 'WEEKLY':
				$rrule .= "W".$this->_interval." ".implode(' ', $this->_byday);
			break;

			case 'MONTHLY':				
				if(empty($this->_byday)){
					$rrule .= 'MD'.$this->_interval.' '.date('j', $this->_eventstarttime);
				}else
				{
					$rrule .= 'MP'.$this->_interval.' '.$this->_bysetpos.'+ '.implode(',', $this->_byday);
				}
			break;
			
			case 'YEARLY':
				$rrule .= 'YM'.$this->_interval;
				break;
		}
			
		if ($this->_until>0)
		{			
			$rrule .= " ".date('Ymd\THis', $this->_until);
		}else
		{
			$rrule .= " #0";
		}
		return $rrule;
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
		
		$this->_until=0;
		//the count or until is always in the last element
		if ($until = array_pop($expl_rrule)) {
			
			if(empty($until))
				return false;
			
			if ($until{0} == '#') {
				$count = substr($until, 1);
				if ($count > 0) {
					$this->_count = $count;
				}

				if (strlen($expl_rrule[count($expl_rrule) - 1]) > 2) {
					//this must be the end date
					$this->_until = intval(GO_Base_Util_Date::parseIcalDate(array_pop($expl_rrule)));
				}
			} else {
				$this->_until = intval(GO_Base_Util_Date::parseIcalDate($until));
			}
		}


		if ($this->_freq = array_shift($expl_rrule)) {

			$this->_interval = '';

			$lastchar = substr($this->_freq, -1, 1);
			while (is_numeric($lastchar)) {
				$this->_interval = $lastchar . $this->_interval;
				$this->_freq = substr($this->_freq, 0, strlen($this->_freq) - 1);
				$lastchar = substr($this->_freq, -1, 1);
			}

			switch ($this->_freq) {
				case 'D':
					$this->_freq = 'DAILY';
					break;

				case 'W':
					$this->_freq = 'WEEKLY';
					$this->_byday = $expl_rrule;
					break;

				case 'MP':
					$this->_freq = 'MONTHLY';

					//GO Supports only one position in the month
					/* if(count($expl_rrule) > 1)
					  {
					  //return false;
					  } */
					$month_time = array_shift($expl_rrule);
					//todo negative month times
					$this->_byday = array(substr($month_time, 0, strlen($month_time) - 1) . array_shift($expl_rrule));
					break;

				case 'MD':
					$this->_freq = 'MONTHLY';
					//GO Supports only one position in the month
					if (count($expl_rrule) > 1) {
						return false;
					}

					$month_time = array_shift($expl_rrule);
					//todo negative month times
					//$this->_bymonthday = substr($month_time, 0, strlen($month_time)-1);
					//for nexthaus
					$this->_bymonthday = trim($month_time); //substr($month_time, 0, strlen($month_time)-1);
					break;

				case 'YM':
					$this->_freq = 'YEARLY';
					//GO Supports only one position in the month
					if (count($expl_rrule) > 1) {
						return false;
					}
					$this->_bymonth = array_shift($expl_rrule);
					break;

				default:
				case 'YD':
					//Currently not supported by GO
					return false;
					break;
			}
			
			return true;
		}else
		{
			return false;
		}
	}
	
	private function _splitDaysAndSetPos(){
				
		for($i=0;$i<count($this->_byday);$i++){
			$day = $this->_byday[$i];
			if(strlen($day)>2){
				if(empty($this->_bysetpos))
				$this->_bysetpos = $day[0];
				$this->_byday[$i] = substr($day,1);
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

		$this->_byday = !empty($rrule_arr['BYDAY']) ? explode(',', $rrule_arr['BYDAY']) : array();
		$this->_bymonth = !empty($rrule_arr['BYMONTH']) ? intval($rrule_arr['BYMONTH']) : 0;
		$this->_bymonthday = !empty($rrule_arr['BYMONTHDAY']) ? intval($rrule_arr['BYMONTHDAY']) : 0;
		$this->_freq = !empty($rrule_arr['FREQ']) ? $rrule_arr['FREQ'] : '';
		$this->_until = isset($rrule_arr['UNTIL']) ? intval(GO_Base_Util_Date::parseIcalDate($rrule_arr['UNTIL'])) : 0;
		$this->_count = !empty($rrule_arr['COUNT']) ? intval($rrule_arr['COUNT']) : 0;
		$this->_interval = !empty($rrule_arr['INTERVAL']) ? intval($rrule_arr['INTERVAL']) : 1;
		$this->_bysetpos = !empty($rrule_arr['BYSETPOS']) ? intval($rrule_arr['BYSETPOS']) : 0;
		
		
		
		$this->_splitDaysAndSetPos();
		
		//if rrule is passed like this: RRULE:INTERVAL=1;FREQ=WEEKLY;BYDAY=
		//then assume days should be the event start time day.
		if(isset($rrule_arr['BYDAY']) && empty($this->_byday))
			$this->_byday=array($this->_days[date('w', $this->_eventstarttime)]);
		
		
		
		//figure out end time of event
		//UNTESTED
		if($this->_count>0 && empty($this->_until)){
			$this->_until=0;
			for($i=1;$i<$this->_count;$i++) {
				$this->_until=$this->getNextRecurrence();
			}			
		}
		
		return true;
	}
	
	/**
	 * Creates a Rrule response which can be merged with a normal JSON response.
	 * 
	 * @return array Rrule 
	 */
	public function createJSONOutput() {
		
		$this->shiftDays(false);
		$days = $this->_byday;
		
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
					
					foreach($days as $day)
						$response[$day]=1;
					break;

				case 'MONTHLY':
					$response['bysetpos'] = $this->bysetpos;
					if (!empty($days)) {						
						foreach($days as $day)
							$response[$day]=1;						
					} 
					
					if($this->bysetpos==0)
						$response['freq']='MONTHLY_DATE';
					break;
			}
		}
		return $response;
	}	
}
