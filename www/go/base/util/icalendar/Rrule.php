<?php

/**
 * @property int $count Number of occurences
 * @property string $freq Frequency 
 */
class GO_Base_Util_Icalendar_Rrule {

//	private $_attributes;
	private $_count;
	private $_until;
	private $_freq;
	private $_interval;
	private $_byday;
	private $_bymonth;
	private $_bymonthday;

//	private function __set($name, $value){
//		$setter = 'set'.ucfirst($name);
//		if(method_exists($this, $setter))
//			$this->_attributes[$name]=$this->$setter($value);
//		else
//			$this->_attributes[$name]=$value;
//	}
//	
//	public function __get($name){
//		$getter = 'get'.ucfirst($name);
//		if(method_exists($this, $setter))
//			return $this->$getter($name);
//		else
//			return $this->_attributes[$name];
//	}

	private function setCount($value) {
		return intval($value);
	}

	/**
	 * Set the values of this object from a version 1.0 Icalendar Rrule
	 * 
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
				//echo $rrule_arr['FREQ'].'<br>';
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
		//	private $_byday;
		$this->_byday = !empty($rrule_arr['BYDAY']) ? explode(',', $rrule_arr['BYDAY']) : array();
		//	private $_bymonth;
		$this->_bymonth = !empty($rrule_arr['BYMONTH']) ? intval($rrule_arr['BYMONTH']) : 0;
		//	private $_bymonthday;
		$this->_bymonthday = !empty($rrule_arr['BYMONTHDAY']) ? intval($rrule_arr['BYMONTHDAY']) : 0;
		//	private $_freq;
		$this->_freq = !empty($rrule_arr['FREQ']) ? $rrule_arr['FREQ'] : '';
		//	private $_until;
		$this->_until = !empty($rrule_arr['UNTIL']) ? GO_Base_Util_Date::parseIcalDate($rrule_arr['UNTIL']) : 0;
		//	private $_count;
		$this->_count = !empty($rrule_arr['COUNT']) ? intval($rrule_arr['COUNT']) : 0;
		//	private $_interval;
		$this->_interval = !empty($rrule_arr['INTERVAL']) ? intval($rrule_arr['INTERVAL']) : 1;
	}

	/**
	 *
	 * @param String $rrule 'FREQ=DAILY;UNTIL=22-02-2222;INTERVAL=2;
	 */
	public function parseRrule($rrule) {

		$rrule = str_replace('RRULE:', '', $rrule);

		if (strpos($rrule, 'FREQ') === false) {
			$this->_parseRruleIcalendarV1($rrule);
		} else {
			$this->_parseRruleIcalendar($rrule);
		}
	}

	public function setParams($params) {
		foreach ($params as $key => $value) {
			$key = '_' . $key;
			$this->$key = $value;
		}
	}

	/**
	 * 
	 */
	public function createRrule() {
		return '';
	}

//	public function getNextRecurrence($startTime)
//	{
//		$func = '_getNextRecurrence'.ucfirst($this->freq);
//		return $func($starttime);
//	}
//	
//	private function _getNextRecurrenceDaily($startTime){
//		
//	}


	public function createResponseArray() {
		$response = array();
		if (isset($this->_freq)) {
			if (isset($this->_until))
				$response['repeat_end_time'] = $ical2array->parse_date($rrule['UNTIL']);
//			elseif(isset($this->_count)) 
			//go doesn't support this
			else
				$response['repeat_forever'] = 1;

			$response['repeat_every'] = $this->_interval;
			$response['freq'] = $this->_freq;
			switch ($this->_freq) {

				case 'WEEKLY':

					$response['repeat_days_0'] = in_array('SU', $this->_byday) ? '1' : '0';
					$response['repeat_days_1'] = in_array('MO', $this->_byday) ? '1' : '0';
					$response['repeat_days_2'] = in_array('TU', $this->_byday) ? '1' : '0';
					$response['repeat_days_3'] = in_array('WE', $this->_byday) ? '1' : '0';
					$response['repeat_days_4'] = in_array('TH', $this->_byday) ? '1' : '0';
					$response['repeat_days_5'] = in_array('FR', $this->_byday) ? '1' : '0';
					$response['repeat_days_6'] = in_array('SA', $this->_byday) ? '1' : '0';
					break;

				case 'MONTHLY':
					if (isset($this->_byday)) {

						$response['month_time'] = $this->_byday[0];
						$day = substr($this->_byday, 1);

						switch ($day) {
							case 'MO':
								$response['repeat_days_1'] = 1;
								break;

							case 'TU':
								$response['repeat_days_2'] = 1;
								break;

							case 'WE':
								$response['repeat_days_3'] = 1;
								break;

							case 'TH':
								$response['repeat_days_4'] = 1;
								break;

							case 'FR':
								$response['repeat_days_5'] = 1;
								break;

							case 'SA':
								$response['repeat_days_6'] = 1;
								break;

							case 'SU':
								$response['repeat_days_0'] = 1;
								break;
						}
					} else {
						$response['freq'] .= '_DATE';
					}
					break;
			}
		}
		return $response;
	}

}
