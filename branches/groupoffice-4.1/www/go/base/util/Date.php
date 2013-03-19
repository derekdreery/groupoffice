<?php
/*
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * This class has functions that handle dates and takes the user's date
 * preferences into account.
 *
 * @copyright Copyright Intermesh
 * @version $Id$
 * @package GO.base.util
 * @since Group-Office 3.0
 */

class GO_Base_Util_Date {
	

	public static function roundQuarters($time) {
		$date = getdate($time);

		$mins = ceil($date['minutes']/15)*15;
		$time = mktime($date['hours'], $mins, 0, $date['mon'], $date['mday'], $date['year']);

		return $time;
	}

	/**
	 * Returns true if the time is a holiday or in the weekend
	 *
	 * @param <type> $time
	 * @param <type> $region
	 * @return <type> boolean
	 */
	public static function is_on_free_day($time, $region=false) {
		
		$weekday = date('w', $time);
		if($weekday==6 || $weekday==0) {
			return true;
		}else {

//			global $GO_CONFIG, $GO_LANGUAGE;
//			
//			$date = getdate($time);
//
//			$day_start = mktime(0,0,0,$date['mon'], $date['mday'], $date['year']);
//			$day_end =  mktime(0,0,0,$date['mon'], $date['mday']+1, $date['year']);
//
//			require_once(GO::config()->class_path.'holidays.class.inc.php');
//			$holidays = new holidays();
//
//			$region=$region ? $region : GO::config()->language;
//
//			$hd = new holidays();
//			$count = $hd->get_holidays_for_period($region, $day_start, $day_end);
//			if($count) {
//				return true;
//			}
		}
		return false;
	}

	/**
	 * Calculate how many times the weekday has occured in the month
	 *
	 * @param <type> $time
	 * @return <type> the number of times the weekday occurred
	 */
	public static function get_occurring_number_of_day_in_month($time){
		$mday=date('j', $time);
		return ceil($mday/7);
	}
	/**
	 * Finds the difference in days between two calendar dates.
	 *
	 * @param Date $startDate
	 * @param Date $endDate
	 * @return Int
	 */
	public static function date_diff_days($start_time, $end_time) {
		// Parse dates for conversion
		$start = getdate($start_time);
		$end = getdate($end_time);

		// Convert dates to Julian Days
		$start_date = gregoriantojd($start["mon"], $start["mday"], $start["year"]);
		$end_date = gregoriantojd($end["mon"], $end["mday"], $end["year"]);
		
		return $end_date-$start_date;
		// Return difference
		//return round(($end_date - $start_date), 0);
	}


	public static function format_long_date($time,$add_time=true){
		
		$days = GO::t('full_days');
		$months = GO::t('months');
		$str  = $days[date('w', $time)].' '.date('d', $time).' '.$months[date('n', $time)].' ';
		if ($add_time)
			return $str.date('Y - '.GO::user()->time_format, $time);
		else
			return $str.date('Y', $time);
	}
	

	/**
	 * Reformat a date string formatted by Group-Office user preference to a string
	 * that can be read by strtotime related PHP functions
	 *
	 * @param string $date_string
	 * @param string $date_separator
	 * @param string $date_format
	 * @return string
	 */

	public static function to_input_format($date_string, $date_separator=null, $date_format=null)
	{
		if(strpos($date_string,'T')){
			return $date_string;
		}
		$date_string = trim($date_string);
		
//		if(!isset($date_format)){
//			$date_format=GO::user() ? GO::user()->completeDateFormat : GO::config()->default_date_format;
//		}
//
//		if(!isset($date_separator)){
//			$date_separator=GO::user() ? GO::user()->date_separator : GO::config()->default_date_separator;
//		}
		
		if(GO::user()->date_format=='mdY')
			$date_string = str_replace(array('-','.'),array('/','/'),$date_string);
		else
			$date_string = str_replace(array('/','.'),array('-','-'),$date_string);
		
		return $date_string;
	}

	/**
	 * Takes a date string formatted by Group-Office user preference and turns it 
	 * into a unix timestamp.
	 *
	 * @param String $date_string
	 * @return int Unix timestamp
	 */


	public static function to_unixtime($date_string) {
		if(empty($date_string) || $date_string=='0000-00-00')
		{
			return 0;
		}
		
		//$time = strtotime(GO_Base_Util_Date::to_input_format($date_string));			
		//return $time;		
		$date = new DateTime(GO_Base_Util_Date::to_input_format($date_string));
		return intval($date->format("U"));
	}

	/**
	 * Convert a Group-Office date to MySQL date format
	 *
	 * A Group-Office date is formated by user preference.
	 *
	 * @param	string $date_string The Group-Office date string
	 * @param	bool $with_time The output sting should contain time too
	 * @access public
	 * @return int unix timestamp
	 */

	public static function to_db_date($date_string, $with_time = false) {
		if(empty($date_string))
		{
			return null;
		}
		$time = GO_Base_Util_Date::to_unixtime($date_string);
		if(!$time)
		{
			return null;
		}
		$date_format = $with_time ? 'Y-m-d H:i' : 'Y-m-d';
		return date($date_format, $time);
	}

	/**
	 * Add a period to a unix timestamp
	 *
	 * @param int $time
	 * @param int $days
	 * @param int $months
	 * @param int $years
	 * @param int $hours
	 * @param int $minutes
	 * @param int $seconds
	 * @return int
	 */
	public static function date_add($time,$days=0,$months=0,$years=0, $hours=0, $minutes=0, $seconds=0)
	{
		$date=getdate($time);
		return mktime($date['hours']+$hours,$date['minutes']+$minutes, $date['seconds']+$seconds,$date['mon']+$months,$date['mday']+$days,$date['year']+$years);
	}
	
	
	/**
	 * Add a period to a unix timestamp
	 *
	 * @param int $time
	 * @param int $seconds
	 * @param int $minutes
	 * @param int $hours
	 * @param int $days
	 * @param int $months
	 * @param int $years
	 * @return int
	 */
	public static function dateTime_add($time,$seconds=0,$minutes=0,$hours=0,$days=0,$months=0,$years=0){
		$date=getdate($time);
		return mktime($date['hours']+$hours,$date['minutes']+$minutes, $date['seconds']+$seconds,$date['mon']+$months,$date['mday']+$days,$date['year']+$years);
	}
	
	
	/**
	 * Remove the time from a unix timestamp so it will return the start of a day.
	 * 
	 * @param int $time Unix timestamp
	 * @return int 
	 */
	public static function clear_time($time){
		$date=getdate($time);
		return mktime(0,0,0,$date['mon'],$date['mday'],$date['year']);
	}



	/**
	 * Takes two Group-Office settings like Ymd and - and converts this into Y-m-d
	 *
	 * @param	string $format Any format accepted by php's date function
	 * @param	string $separator A separate like - / or .
	 * @access public
	 * @return int unix timestamp
	 */

	public static function get_dateformat($format, $separator)
	{
		$newformat = '';
		$end = strlen($format)-1;
		for($i=0;$i<$end;$i++)
		{
			$newformat .= $format[$i].$separator;
		}
		$newformat .= $format[$i];
		return $newformat;
	}


	/**
	 * Get the current server time in microseconds
	 *
	 * @access public
	 * @return int
	 */
	public static function getmicrotime() {
		list ($usec, $sec) = explode(" ", microtime());
		return ((float) $usec + (float) $sec);
	}

	public static function get_timestamp($utime, $with_time=true)
	{
		$utime = intval($utime);
		if($utime<1)
			return '';
			
		return GO_Base_Util_Date::format('@'.$utime, $with_time);
	}

	public static function format($time, $with_time=true)//, $timezone='GMT')
	{
		if(empty($time) || $time=='0000-00-00' || $time=='0000-00-00 00:00:00')
		{
			return '';
		}
		/*$d = new DateTime($time, new DateTimeZone($timezone));


		if($timezone!=$_SESSION['GO_SESSION']['timezone'])
		{
			$tz = new DateTimeZone(date_default_timezone_get());
			if($tz)
			{
				$d->setTimezone($tz);
			}
		}*/
		
		$completeDateFormat = GO::user() ? GO::user()->completeDateFormat : GO::config()->getCompleteDateFormat();
		$timeFormat = GO::user() ? GO::user()->time_format : GO::config()->default_time_format;

		$date_format = $with_time ?  $completeDateFormat.' '.$timeFormat : $completeDateFormat;

		return date($date_format, strtotime($time));
	}

	public static function get_timezone_offset($utime)
	{
		$d = new DateTime('@'.$utime, new DateTimeZone('GMT'));
		$tz = new DateTimeZone(date_default_timezone_get());
		if($tz)
		{
				$d->setTimezone($tz);
		}
		return $d->getOffset()/3600;
	}

		
	public static function get_last_sunday($time)
	{
		$date = getdate($time);		
		return mktime(0,0,0,$date['mon'],$date['mday']-$date['wday'], $date['year']);
	}
	
	/**
	 * Convert a date formatted according to icalendar 2.0 specs to a unix timestamp.
	 * 
	 * @param String $date
	 * @param GO_Base_Util_Icalendar_Timezone $icalendarTimezone
	 * @return int Unix timestamp 
	 */
	public static function parseIcalDate($date, $icalendarTimezone=false) {
		$date=trim($date);
		$year = substr($date,0,4);
		$month = substr($date,4,2);
		$day = substr($date,6,2);
		if (strpos($date, 'T') !== false) {
			$hour = substr($date,9,2);
			$min = substr($date,11,2);
			$sec = substr($date,13,2);
		}else {
			$hour = 0;
			$min = 0;
			$sec = 0;
		}

		if(strpos($date, 'Z') !== false){
			return gmmktime($hour, $min, $sec, $month, $day , $year);
		}else
		{
			return mktime($hour, $min, $sec, $month, $day , $year);
		}
	}
	
	public static function getNextSaturday($unixTime) {
		$lastSunday = self::get_last_sunday($unixTime);
		return self::date_add($lastSunday,6);
	}		
}