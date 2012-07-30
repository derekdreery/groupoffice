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
	public function readIcalendarRruleString($eventstarttime, $rrule) {
		if(!empty($rrule)){			
			$this->_eventstarttime = $eventstarttime;
			$rrule = str_replace('RRULE:', '', $rrule);
			if (strpos($rrule, 'FREQ') === false) 
				$this->_parseRruleIcalendarV1($rrule);
			else
				$this->_parseRruleIcalendar($rrule);
		}
	}
	
		
	public function readJsonArray($json)
	{
		$parameters = array();
		
		$parameters['interval'] = intval($json['interval']);
		$parameters['freq'] = strtoupper($json['freq']);
		if($parameters['freq']=='MONTHLY_DATE')
			$parameters['freq']='MONTHLY';
		$parameters['eventstarttime'] = isset($json['eventstarttime'])?strtotime($json['eventstarttime']):strtotime($json['start_time']);
		$parameters['until'] = empty($json['repeat_forever']) && isset($json['until']) ? GO_Base_Util_Date::to_unixtime($json['until'].' '.date('G', $parameters['eventstarttime']).':'.date('i', $parameters['eventstarttime'])) : '';
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
		
		$this->setParams($parameters);
		
		$this->_byday = $this->shiftDays($this->_byday);			
	}
	
		
	/**
	 * Output a rrule
	 * 
	 * @return String $rrule eg.: 'FREQ=DAILY;UNTIL=22-02-2222;INTERVAL=2;
	 */
	public function createRrule($shiftDays=true) {
		
		if(empty($this->_freq))
			return "";
		
		$byday = $shiftDays ? $this->shiftDays($this->_byday, false) : $this->_byday;
		
		$rrule = 'RRULE:INTERVAL='.$this->_interval.';FREQ='.$this->_freq;

		switch($this->_freq)
		{
			case 'WEEKLY':
				$rrule .= ";BYDAY=".implode(',', $byday);
			break;

			case 'MONTHLY':				
				if($this->_bymonthday){
					$rrule .= ';BYMONTHDAY='.date('j', $this->_eventstarttime);
				}elseif (!empty($this->_byday))
				{
					if(!empty($this->_bysetpos))
						$rrule .= ";BYSETPOS=".$this->_bysetpos;
						
					$rrule .= ';BYDAY='.implode(',', $byday);
				}
			break;
		}
			
		if ($this->_until>0)
		{
			$rrule .= ";UNTIL=".gmdate('Ymd\\THis\\Z', $this->_until);
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
				$rrule .= "W".$this->_interval." ".implode(',', $this->_byday);
			break;

			case 'MONTHLY':				
				if($this->_bymonthday){
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
			if ($until{0} == '#') {
				$count = substr($until, 1);
				if ($count > 0) {
					$this->_count = $count;
				}

				if (strlen($expl_rrule[count($expl_rrule) - 1]) > 2) {
					//this must be the end date
					$this->_until = GO_Base_Util_Date::parseIcalDate(array_pop($expl_rrule));
				}
			} else {
				$this->_until = GO_Base_Util_Date::parseIcalDate($until);
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
					$this->_byday = implode(',', $expl_rrule);
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
					$this->_byday = substr($month_time, 0, strlen($month_time) - 1) . array_shift($expl_rrule);
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

		$this->_byday = !empty($rrule_arr['BYDAY']) ? explode(',', $rrule_arr['BYDAY']) : array();
		$this->_bymonth = !empty($rrule_arr['BYMONTH']) ? intval($rrule_arr['BYMONTH']) : 0;
		$this->_bymonthday = !empty($rrule_arr['BYMONTHDAY']) ? intval($rrule_arr['BYMONTHDAY']) : 0;
		$this->_freq = !empty($rrule_arr['FREQ']) ? $rrule_arr['FREQ'] : '';
		$this->_until = isset($rrule_arr['UNTIL']) ? GO_Base_Util_Date::parseIcalDate($rrule_arr['UNTIL']) : 0;
		$this->_count = !empty($rrule_arr['COUNT']) ? intval($rrule_arr['COUNT']) : 0;
		$this->_interval = !empty($rrule_arr['INTERVAL']) ? intval($rrule_arr['INTERVAL']) : 1;
		$this->_bysetpos = !empty($rrule_arr['BYSETPOS']) ? intval($rrule_arr['BYSETPOS']) : 0;
		
		
		//figure out end time of event
		//UNTESTED
		if($this->_count>0 && empty($this->_until)){
			$this->_until=0;
			for($i=1;$i<$this->_count;$i++) {
				$this->_until=$this->getNextRecurrence();
			}			
		}
	}
	
	/**
	 * Creates a Rrule response which can be merged with a normal JSON response.
	 * 
	 * @return array Rrule 
	 */
	public function createJSONOutput() {
		
		$days = $this->shiftDays($this->_byday, false);
		//$days = $this->_byday;
		
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
