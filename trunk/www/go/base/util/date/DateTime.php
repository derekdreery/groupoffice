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
 * Extended DateTime class to add GO specific functions 
 * 
 * @copyright Copyright Intermesh BV
 * @version $Id: Number.php 7962 2011-08-24 14:48:45Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.util.date
 */
class GO_Base_Util_Date_DateTime extends DateTime {

	/**
	 * Create a date time object with timezone information with a unixtime stamp
	 * @param int $unixtime
	 * @return GO_Base_Util_Date_DateTime 
	 */
	public static function fromUnixtime($unixtime) {
		return new self(date('Y-m-d H:i:s', $unixtime), new DateTimeZone(date_default_timezone_get()));
	}

	/**
	 * Get the number of days elapsed. We could not use DateTime::diff() because it's only
	 * compatible with PHP 5.3
	 * 
	 * @param GO_Base_Util_Date_DateTime $dateTime
	 * @return int 
	 */
	public function getDaysElapsed($dateTime) {
		$jdThis = gregoriantojd($this->format('n'), $this->format('j'), $this->format('Y'));
		$jdDT = gregoriantojd($dateTime->format('n'), $dateTime->format('j'), $dateTime->format('Y'));

		return $jdDT - $jdThis;
	}

//	/**
//	 * Get an array with elapsed days, hours and minutes that can be used for
//	 * addDiffCompat. These functions are for php 5.2 compatibility.
//	 * 
//	 * @param GO_Base_Util_Date_DateTime $dateTime
//	 * @return array 
//	 */
//	public function getDiffCompat($dateTime){
//		
//		$hours = $dateTime->format('G')-$this->format('G');
//		$mins = $dateTime->format('i')-$this->format('i');
//		
//		return array('days'=>$this->getDaysElapsed($dateTime),'hours'=>$hours, 'mins'=>$mins);
//	}

	public function getDiffCompat($dateTime) {
		
		return $this->_date_diff($this->format('U'), $dateTime->format('U'));
	}

	/**
	 * Calculate differences between two dates with precise semantics. Based on PHPs DateTime::diff()
	 * implementation by Derick Rethans. Ported to PHP by Emil H, 2011-05-02. No rights reserved.
	 * 
	 * See here for original code:
	 * http://svn.php.net/viewvc/php/php-src/trunk/ext/date/lib/tm2unixtime.c?revision=302890&view=markup
	 * http://svn.php.net/viewvc/php/php-src/trunk/ext/date/lib/interval.c?revision=298973&view=markup
	 */
	private function _date_range_limit($start, $end, $adj, $a, $b, &$result) {
		if ($result[$a] < $start) {
			$result[$b] -= intval(($start - $result[$a] - 1) / $adj) + 1;
			$result[$a] += $adj * intval(($start - $result[$a] - 1) / $adj + 1);
		}

		if ($result[$a] >= $end) {
			$result[$b] += intval($result[$a] / $adj);
			$result[$a] -= $adj * intval($result[$a] / $adj);
		}

		return $result;
	}

	private function _date_range_limit_days(&$base, &$result) {
		$days_in_month_leap = array(31, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$days_in_month = array(31, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

		$this->_date_range_limit(1, 13, 12, "m", "y", $base);

		$year = $base["y"];
		$month = $base["m"];

		if (!$result["invert"]) {
			while ($result["d"] < 0) {
				$month--;
				if ($month < 1) {
					$month += 12;
					$year--;
				}

				$leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
				$days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

				$result["d"] += $days;
				$result["m"]--;
			}
		} else {
			while ($result["d"] < 0) {
				$leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
				$days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

				$result["d"] += $days;
				$result["m"]--;

				$month++;
				if ($month > 12) {
					$month -= 12;
					$year++;
				}
			}
		}

		return $result;
	}

	private function _date_normalize(&$base, &$result) {
		$result = $this->_date_range_limit(0, 60, 60, "s", "i", $result);
		$result = $this->_date_range_limit(0, 60, 60, "i", "h", $result);
		$result = $this->_date_range_limit(0, 24, 24, "h", "d", $result);
		$result = $this->_date_range_limit(0, 12, 12, "m", "y", $result);

		$result = $this->_date_range_limit_days($base, $result);

		$result = $this->_date_range_limit(0, 12, 12, "m", "y", $result);

		return $result;
	}

	/**
	 * Accepts two unix timestamps.
	 */
	private function _date_diff($one, $two) {
		$invert = false;
		if ($one > $two) {
			list($one, $two) = array($two, $one);
			$invert = true;
		}

		$key = array("y", "m", "d", "h", "i", "s");
		$a = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $one))));
		$b = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $two))));

		$result = array();
		$result["y"] = $b["y"] - $a["y"];
		$result["m"] = $b["m"] - $a["m"];
		$result["d"] = $b["d"] - $a["d"];
		$result["h"] = $b["h"] - $a["h"];
		$result["i"] = $b["i"] - $a["i"];
		$result["s"] = $b["s"] - $a["s"];
		$result["invert"] = $invert ? 1 : 0;
		$result["days"] = intval(abs(($one - $two) / 86400));

		if ($invert) {
			$this->_date_normalize($a, $result);
		} else {
			$this->_date_normalize($b, $result);
		}

		return $result;
	}

	/**
	 * Add a diff array returned by getDiffCompat
	 * 
	 * @param array $diff
	 * @return GO_Base_Util_Date_DateTime 
	 */
	public function addDiffCompat($diff) {
//		$unixtime = GO_Base_Util_Date::date_add($this->format('U'), $diff['days']);
//		$unixtime += (($diff['hours']*60)+$diff['mins'])*60;

		if ($diff['d'] > 0)
			$this->setDate($this->format('Y'), $this->format('n'), $this->format('j') + $diff['days']);

		if ($diff['h'] > 0 || $diff['i'] > 0)
			$this->setTime($this->format('G') + $diff['h'], $this->format('i')+$diff['i'], $this->format('s'));

		return $this;
	}

	/**
	 * Convert a diff array to a readable string
	 * 
	 * @param array $diff
	 * @return string
	 */
	public static function diffToString($diff) {
		$string = '';

		if (!empty($diff['y']))
			$string .= $diff['y'] . ' ' . GO::t('strYears') . ', ';

		if (!empty($diff['m']))
			$string .= $diff['m'] . ' ' . GO::t('strMonths') . ', ';

		if (!empty($diff['d']))
			$string .= $diff['d'] . ' ' . GO::t('strDays') . ', ';

		if (!empty($diff['h']))
			$string .= $diff['h'] . ' ' . GO::t('strHours') . ', ';

		if (!empty($diff['i']))
			$string .= $diff['i'] . ' ' . GO::t('strMinutes');

//		if(!empty($diff['s']))
//			$string .= $diff['s'].' '.GO::t('strSeconds');

		return rtrim($string,', ');
	}

}