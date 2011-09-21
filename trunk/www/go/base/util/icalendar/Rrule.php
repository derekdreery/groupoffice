<?php

/**
 * An Icalendar Rrule object
 */
class GO_Base_Util_Icalendar_Rrule extends GO_Base_Util_Date_RecurrencePattern
{
	/**
	 * Create a Rrule object from a Rrule string. This function automatically finds 
	 * out which Rrule version is used. 
	 * 
	 * @param String $eventStartTime The time the recurrence pattern starts. This is important to calculate the correct interval.
	 * @param String $rrule 'FREQ=DAILY;UNTIL=22-02-2222;INTERVAL=2;
	 */
	public function readIcalendarRruleString($eventStartTime, $rrule) {
		$this->_eventStartTime = $eventStartTime;
		$rrule = str_replace('RRULE:', '', $rrule);
		if (strpos($rrule, 'FREQ') === false) 
			$this->_parseRruleIcalendarV1($rrule);
		else
			$this->_parseRruleIcalendar($rrule);
	}
	
		
	public function readJsonArray($json)
	{
		$parameters = array();
		
		$parameters['interval'] = intval($json['interval']);
		$parameters['freq'] = strtoupper($json['freq']);
		$parameters['until'] = !isset($json['repeat_forever']) && isset($json['until']) ? GO_Base_Util_Date::to_unixtime($json['until']) : '';
		$parameters['bymonth'] = isset($json['bymonth'])?$json['bymonth']:'';
		$parameters['bymonthday'] = isset($json['bymonthday'])?$json['bymonthday']:'';
		$parameters['eventStartTime'] = isset($json['eventStartTime'])?strtotime($json['eventStartTime']):strtotime($json['start_time']);
		
		$parameters['byday']=array();
		
		foreach($this->_days as $day){
			if(isset($json[$day])){
				$day = $day;
				if(!empty($json['bysetpos']))
					$day = $json['bysetpos'].$day;
				
				$parameters['byday'][]=$day;
			}
		}		
		
		$this->setParams($parameters);
		
		$this->_shiftDays();	
		
		
	}
	
		
	/**
	 * Output a rrule
	 * 
	 * @return String $rrule eg.: 'FREQ=DAILY;UNTIL=22-02-2222;INTERVAL=2;
	 */
	public function createRrule() {
		
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

	/**
	 * Set the values of this object from a version 1.0 Icalendar Rrule
	 * @TODO: This function is not yet changed for the new go version
	 * This must be a vcalendar 1.0 rrule
	 */
	private function _parseRruleIcalendarV1($rrule) {
		
		$rrule_arr = array(); // An new array of params that need to be set
		
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
		
		$return = array();
		$this->_byday = !empty($rrule_arr['BYDAY']) ? explode(',', $rrule_arr['BYDAY']) : array();
		$this->_bymonth = !empty($rrule_arr['BYMONTH']) ? intval($rrule_arr['BYMONTH']) : 0;
		$this->_bymonthday = !empty($rrule_arr['BYMONTHDAY']) ? intval($rrule_arr['BYMONTHDAY']) : 0;
		$this->_freq = !empty($rrule_arr['FREQ']) ? $rrule_arr['FREQ'] : '';
		$this->_until = isset($rrule_arr['UNTIL']) ? GO_Base_Util_Date::parseIcalDate($rrule_arr['UNTIL']) : 0;
		$this->_count = !empty($rrule_arr['COUNT']) ? intval($rrule_arr['COUNT']) : 0;
		$this->_interval = !empty($rrule_arr['INTERVAL']) ? intval($rrule_arr['INTERVAL']) : 1;
		$this->_bysetpos = !empty($rrule_arr['BYSETPOS']) ? intval($rrule_arr['BYSETPOS']) : 0;
	}
	
	/**
	 * Creates a Rrule response which can be merged with a normal JSON response.
	 * 
	 * @return array Rrule 
	 */
	public function createJSONOutput() {
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
						$response['bysetpos'] = substr($this->_byday[0], 0, 1);
						foreach($this->_byday as $day)
							$response[substr($day,1)]=1;						
					} else {
						$response['freq'];
					}
					break;
			}
		}
		return $response;
	}	
}
