<?php
//require vendor lib SabreDav vobject
//require_once(GO::config()->root_path.'go/vendor/SabreDAV/lib/Sabre/VObject/includes.php');
		

class GO_Base_VObject_VTimezone extends Sabre\VObject\Component {

	/**
	 * Creates a new component.
	 *
	 * By default this object will iterate over its own children, but this can 
	 * be overridden with the iterator argument
	 * 
	 * @param string $name 
	 * @param Sabre\VObject\ElementList $iterator
	 */
	public function __construct($name='VTIMEZONE', Sabre\VObject\ElementList $iterator = null) {

		parent::__construct($name, $iterator);

		$tz = new DateTimeZone(GO::user() ? GO::user()->timezone : date_default_timezone_get());
		//$tz = new DateTimeZone("Europe/Amsterdam");
		$transitions = $tz->getTransitions();
		

		$start_of_year = mktime(0, 0, 0, 1, 1);

		$to = GO_Base_Util_Date::get_timezone_offset(time());
		if ($to < 0) {
			if (strlen($to) == 2)
				$to = '-0' . ($to * -1);
		}else {
			if (strlen($to) == 1)
				$to = '0' . $to;

			$to = '+' . $to;
		}

		$STANDARD_TZOFFSETFROM = $STANDARD_TZOFFSETTO = $DAYLIGHT_TZOFFSETFROM = $DAYLIGHT_TZOFFSETTO = $to;

		$STANDARD_RRULE = '';
		$DAYLIGHT_RRULE = '';
		
		for ($i = 0, $max = count($transitions); $i < $max; $i++) {
			if ($transitions[$i]['ts'] > $start_of_year) {
	
				$weekday1 = $this->_getDay($transitions[$i]['time']);
				$weekday2 = $this->_getDay($transitions[$i+1]['time']);
				
				if($transitions[$i]['isdst']){
					$dst_start = $transitions[$i];
					$dst_end = $transitions[$i + 1];
				}else
				{
					$dst_end = $transitions[$i];
					$dst_start = $transitions[$i + 1];
				}

				$STANDARD_TZOFFSETFROM = $this->_formatVtimezoneTransitionHour($dst_start['offset'] / 3600);
				$STANDARD_TZOFFSETTO = $this->_formatVtimezoneTransitionHour($dst_end['offset'] / 3600);

				$DAYLIGHT_TZOFFSETFROM = $this->_formatVtimezoneTransitionHour($dst_end['offset'] / 3600);
				$DAYLIGHT_TZOFFSETTO = $this->_formatVtimezoneTransitionHour($dst_start['offset'] / 3600);

				$DAYLIGHT_RRULE = "FREQ=YEARLY;BYDAY=$weekday1;BYMONTH=" . date('n', $dst_start['ts']);
				$STANDARD_RRULE = "FREQ=YEARLY;BYDAY=$weekday2;BYMONTH=" . date('n', $dst_end['ts']);


				break;
			}
		}

		$this->tzid = $tz->getName();
		$this->add("last-modified", "19870101T000000Z");

		$s = new Sabre\VObject\Component("standard");
		$s->dtstart = "19710101T030000";
		$s->rrule = $STANDARD_RRULE;
		$s->tzoffsetfrom = $STANDARD_TZOFFSETFROM . "00";
		$s->tzoffsetto = $STANDARD_TZOFFSETTO . "00";

		$this->add($s);

		$s = new Sabre\VObject\Component("daylight");
		$s->dtstart = "19710101T020000";
		$s->rrule = $DAYLIGHT_RRULE;
		$s->tzoffsetfrom = $DAYLIGHT_TZOFFSETFROM . "00";
		$s->tzoffsetto = $DAYLIGHT_TZOFFSETTO . "00";

		$this->add($s);
	}
	
	private function _getDay($date){
//		echo $date."\n";
		$time = new DateTime($date);				
		$dayOfMonth = $time->format('j');				
		$nth = ceil($dayOfMonth/7);				
		if($nth>2)
			$weekday = '-1SU';
		else
			$weekday = $nth.'SU';

		return $weekday;
	}
	
	private function _formatVtimezoneTransitionHour($hour){		

		if($hour<0){
			$prefix = '-';
			$hour = $hour*-1;
		}else
		{
			$prefix = '+';
		}

		if($hour<10)
			$hour = '0'.$hour;

		$hour = $prefix.$hour;

		return $hour;
	}

}

