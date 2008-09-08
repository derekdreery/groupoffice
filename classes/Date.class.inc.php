<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: Date.class.inc.php 2306 2008-07-07 14:00:44Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * This class has functions that handle dates and takes the user's date
 * preferences into account.
 *
 * @copyright Copyright Intermesh
 * @version $Id: Date.class.inc.php 2306 2008-07-07 14:00:44Z mschering $
 * @package go.utils
 * @since Group-Office 3.0
 */


if(!defined('REPEAT_NONE'))
{
	define('REPEAT_NONE', '0');
	define('REPEAT_DAILY', '1');
	define('REPEAT_WEEKLY', '2');
	define('REPEAT_MONTH_DATE', '3');
	define('REPEAT_MONTH_DAY', '4');
	define('REPEAT_YEARLY', '5');
}


class Date
{
	/**
	 * Converts a Group-Office date to unix time.
	 *
	 * A Group-Office date is formated by user preference.
	 *
	 * @param	string $date_string The date string formated in the user's preference
	 * @access public
	 * @return int unix timestamp
	 */


	public function byday_to_days($byday)
	{
		$days_arr = explode(',', $byday);

		$days['sun'] = in_array('SU', $days_arr) ? '1' : '0';
		$days['mon'] = in_array('MO', $days_arr) ? '1' : '0';
		$days['tue'] = in_array('TU', $days_arr) ? '1' : '0';
		$days['wed'] = in_array('WE', $days_arr) ? '1' : '0';
		$days['thu'] = in_array('TH', $days_arr) ? '1' : '0';
		$days['fri'] = in_array('FR', $days_arr) ? '1' : '0';
		$days['sat'] = in_array('SA', $days_arr) ? '1' : '0';

		return $days;
	}


	/**
	 * Calculates the next occurence of a recurring event
	 *
	 * @param int $first_occurence_time The time this events occurs for the first time.
	 * @param int $start_time The next occurence returns will happen after this time.
	 * @param string $rrule The iCalendar rrule
	 * @param boolean $local_time Set to true if the days in the url are not in GMT but local time
	 * @return int
	 */
	public static function get_next_recurrence_time($first_occurence_time, $start_time, $rrule, $local_time=false)
	{
		global $GO_CONFIG;

		//go_log(LOG_DEBUG, date('r',$start_time));

		require_once($GO_CONFIG->class_path.'ical2array.class.inc');
		$ical2array = new ical2array();

		if(!$rrule = $ical2array->parse_rrule($rrule))
		{
			//Recurrence rule is not understood by GO, abort
			return false;
		}



		if(!isset($rrule['FREQ']))
		{
			return false;
		}

		//if the requested start
		if($start_time < $first_occurence_time)
		$start_time = $first_occurence_time-1;
			
		//we cannot simply return the first_occurrence_time because a recurring event
		//can start on a day that it doesn't occur on.

		if (isset($rrule['UNTIL']))
		{
			if($event['repeat_end_time'] = $ical2array->parse_date($rrule['UNTIL']))
			{
				$event['repeat_forever']='0';
				$event['repeat_end_time'] = mktime(0,0,0, date('n', $event['repeat_end_time']), date('j', $event['repeat_end_time'])+1, date('Y', $event['repeat_end_time']));
			}else
			{
				$event['repeat_forever'] = 1;
			}
		}elseif(isset($rrule['COUNT']))
		{
			//figure out end time later when event data is complete
			$event['repeat_forever'] = 1;
			$event_count = intval($rrule['COUNT']);
			if($event_count==0)
			{
				unset($event_count);
			}
		}else
		{
			$event['repeat_forever'] = 1;
		}

		$event['repeat_every']=$rrule['INTERVAL'];

		if($event['repeat_every']==0)
		return false;

		$occurence_time=0;

		$event['start_time']=$first_occurence_time;



		$day_db_field[0] = 'sun';
		$day_db_field[1] = 'mon';
		$day_db_field[2] = 'tue';
		$day_db_field[3] = 'wed';
		$day_db_field[4] = 'thu';
		$day_db_field[5] = 'fri';
		$day_db_field[6] = 'sat';

		switch($rrule['FREQ'])
		{

			case 'WEEKLY':

				if(empty($rrule['BYDAY']))
				return false;
					
				$days = Date::byday_to_days($rrule['BYDAY']);
				$days = Date::shift_days_to_local($days, date('G', $event['start_time']), Date::get_timezone_offset($event['start_time']));

				$interval = $start_time - $first_occurence_time;

				$interval_weeks = floor($interval/604800);
				$devided = $interval_weeks/$event['repeat_every'];
				$rounded = ceil($devided);

				for ($i=0;$i<7;$i++)
				{
					if($i==0)
					{
						$last_occurence_time = $first_occurence_time+($event['repeat_every']*$rounded*604800);
						$last_occurence_time=mktime(date('H', $first_occurence_time), date('i', $first_occurence_time),0, date('m', $last_occurence_time), date('j', $last_occurence_time)-date('w', $last_occurence_time), date('Y', $last_occurence_time));
					}

					$test_time = Date::date_add($last_occurence_time, $i);
					$weekday = date("w", $test_time);
					//echo '*'.date('r', $start_time).' -> '.date('r', $test_time).' -> '.$event[$day_db_field[$weekday]]."\n";
					if ($days[$day_db_field[$weekday]] == '1' && $test_time>$start_time)
					{
						$occurence_time = $test_time;
						break;
					}

					if($i==6 && $occurence_time<$start_time)
					{
						$rounded++;
						$i=-1;
					}
				}
				break;

			case 'DAILY':
				$interval = $start_time - $first_occurence_time;

				$interval_days = floor($interval/86400);
				$devided = $interval_days/$event['repeat_every'];
				$rounded = ceil($devided);

				while($occurence_time<=$start_time)
				{
					$occurence_time = $first_occurence_time+($event['repeat_every']*$rounded*86400);
					$occurence_time=mktime(date('H', $first_occurence_time), date('i', $first_occurence_time),0, date('m', $occurence_time), date('j', $occurence_time), date('Y', $occurence_time));
					$rounded++;
				}

				break;

			case 'MONTHLY':
				$interval_years = date('Y', $start_time)-date('Y', $first_occurence_time);
				$interval_months = date('n', $start_time)-date('n', $first_occurence_time);
				$interval_months = 12*$interval_years+$interval_months;
					
				$devided = $interval_months/$event['repeat_every'];
				$rounded = ceil($devided);
				//echo '*'.$rounded."\n";

				if (!isset($rrule['BYDAY']))
				{
					while($occurence_time<=$start_time)
					{
						$occurence_time=mktime(date('H', $first_occurence_time), date('i', $first_occurence_time),0, date('n', $first_occurence_time)+($event['repeat_every']*$rounded), date('j', $first_occurence_time), date('Y', $first_occurence_time));
						$rounded++;
					}
				}else
				{

					$event['month_time'] = $rrule['BYDAY'][0];
					$day = substr($rrule['BYDAY'], 1);
						
					$days = Date::byday_to_days($day);

					if(!count($days))
					return false;


					$days = Date::shift_days_to_local($days, date('G', $event['start_time']), Date::get_timezone_offset($event['start_time']));


					$last_occurence_time=0;
					while($occurence_time==0)
					{
						//go_log(LOG_DEBUG, '*'.date('Ymd G:i', $last_occurence_time));

						$last_occurence_time=mktime(date('H', $first_occurence_time), date('i', $first_occurence_time),0, date('n', $first_occurence_time)+($event['repeat_every']*$rounded), 1, date('Y', $first_occurence_time));
						$rounded++;
							
							
							
						for($d=0;$d<31;$d++)
						{
							$test_time = Date::date_add($last_occurence_time, $d);

							//echo '*'.date('r', $test_time)."\n";

							//go_log(LOG_DEBUG, '**'.date('Ymd G:i', $test_time));

							$weekday = date("w", $test_time);

							if (isset($days[$day_db_field[$weekday]]) && $test_time>$start_time && $test_time>$first_occurence_time)
							{
								//echo '**'.ceil(date('j',$test_time)/7).' = '.$event['month_time'].'<br>';
								if (ceil(date('j',$test_time)/7) == $event['month_time'])
								{
									$occurence_time=$test_time;
									break;
								}
							}

							if($d==31 && $occurence_time<$start_time)
							{
								$rounded++;
								$d=-1;
							}
						}
					}
				}
				break;

			case 'YEARLY';
			$interval_years = date('Y', $start_time)-date('Y', $first_occurence_time);
			$devided = $interval_years/$event['repeat_every'];
			$rounded = ceil($devided);

			//go_log(LOG_DEBUG, $rounded);
			while($occurence_time<=$start_time)
			{
				$new_year = date('Y', $first_occurence_time)+($event['repeat_every']*$rounded);



				$occurence_time=mktime(
				date('H', $first_occurence_time),
				date('i', $first_occurence_time),
				0,
				date('n', $first_occurence_time),
				date('j', $first_occurence_time),
				$new_year);


				//	go_log(LOG_DEBUG, date('r', $occurence_time).' -> '.date('r', $start_time));

				if(!$occurence_time)
				break;

				$rounded++;
			}
			break;
		}


		if ($event['repeat_forever'] == '0' && $occurence_time > $event['repeat_end_time'])
		{
			return 0;
		}else
		{
			return $occurence_time;
		}

	}





	function shift_days_to_gmt($days, $local_start_hour, $timezone_offset)
	{
		$gmt_start_hour = $local_start_hour-$timezone_offset;

		if ($gmt_start_hour > 23) {
			$shift_day = 1;
		}elseif ($gmt_start_hour < 0) {
			$shift_day = -1;
		} else {
			$shift_day = 0;
		}



		if($shift_day!=0)
		{
			switch ($shift_day) {
				case 1 :
					$mon = $days['sun'];
					$tue = $days['mon'];
					$wed = $days['tue'];
					$thu = $days['wed'];
					$fri = $days['thu'];
					$sat = $days['fri'];
					$sun = $days['sat'];
					break;

				case -1 :
					$mon = $days['tue'];
					$tue = $days['wed'];
					$wed = $days['thu'];
					$thu = $days['fri'];
					$fri = $days['sat'];
					$sat = $days['sun'];
					$sun = $days['mon'];
					break;
			}
			$days['sun']=$sun;
			$days['mon']=$mon;
			$days['tue']=$tue;
			$days['wed']=$wed;
			$days['thu']=$thu;
			$days['fri']=$fri;
			$days['sat']=$sat;
		}
		return $days;
	}


	public function shift_days_to_local($days, $local_start_hour, $timezone_offset)
	{
		//shift the selected weekdays to local time
		//var_dump($days);
		$gmt_start_hour = $local_start_hour-$timezone_offset;

		if ($gmt_start_hour > 23) {
			$shift_day = -1;
		}elseif ($gmt_start_hour < 0) {
			$shift_day = 1;
		} else {
			$shift_day = 0;
		}

		//debug($gmt_start_hour.' > '.$timezone_offset.' > '.$shift_day);

		if($shift_day!=0)
		{
			switch ($shift_day) {
				case 1 :
					$mon = $days['sun'];
					$tue = $days['mon'];
					$wed = $days['tue'];
					$thu = $days['wed'];
					$fri = $days['thu'];
					$sat = $days['fri'];
					$sun = $days['sat'];
					break;

				case -1 :
					$mon = $days['tue'];
					$tue = $days['wed'];
					$wed = $days['thu'];
					$thu = $days['fri'];
					$fri = $days['sat'];
					$sat = $days['sun'];
					$sun = $days['mon'];
					break;
			}

			$days['sun']=$sun;
			$days['mon']=$mon;
			$days['tue']=$tue;
			$days['wed']=$wed;
			$days['thu']=$thu;
			$days['fri']=$fri;
			$days['sat']=$sat;
		}
		return $days;
	}

	/**
	 * Reformat a date string formatted by Group-Office user preference to a string
	 * that can be read by strtotime related PHP functions
	 *
	 * @param string $date_string
	 * @param string $date_seperator
	 * @param string $date_format
	 * @return string
	 */

	public static function to_input_format($date_string, $date_seperator=null, $date_format=null)
	{
		if(!isset($date_format))
			$date_format=$_SESSION['GO_SESSION']['date_format'];

		if(!isset($date_seperator))
			$date_seperator=$_SESSION['GO_SESSION']['date_seperator'];

		$date_string = trim($date_string);
		
		if ($date_string != '') {

			$datetime_array = explode(' ', $date_string);

			$date = isset ($datetime_array[0]) ?
			$datetime_array[0] :
    	'0000'.$date_seperator.
    	'00'.$date_seperator.'00';

			$date_array = explode($date_seperator, $datetime_array[0]);
			//$year = isset ($date_array[2]) ? $date_array[2] : date('Y');

			$format = str_replace($date_seperator,'',$date_format);

			$year_pos = strpos($format, 'Y');
			$month_pos = strpos($format, 'm');
			$day_pos = strpos($format, 'd');

			$year = isset ($date_array[$year_pos]) ? $date_array[$year_pos] : date('Y');
			$month = isset ($date_array[$month_pos]) ? $date_array[$month_pos] : date('m');
			$day = isset ($date_array[$day_pos]) ? $date_array[$day_pos] : 0;

			$time = isset ($datetime_array[1]) ? $datetime_array[1] : '00:00';
			$time_array = explode(':', $time);

			$hour = isset ($time_array[0]) ? $time_array[0] : '00';
			$min = isset ($time_array[1]) ? $time_array[1] : '00';
			//$sec = isset ($time_array[2]) ? $time_array[2] : '00';

			return $year.'-'.$month.'-'.$day.' '.$hour.':'.$min;
		}
		return false;

	}

	/**
	 * Takes a date string formatted by Group-Office user preference and turns it 
	 * into a unix timestamp.
	 *
	 * @param String $date_string
	 * @return int Unix timestamp
	 */


	public static function to_unixtime($date_string) {
		if(empty($date_string))
		{
			return 0;
		}
		$d = new DateTime(Date::to_input_format($date_string));
		return $d->format('U');
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
			return '';
		}
		$time = Date::to_unixtime($date_string);
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
	 * @return int
	 */


	public static function date_add($time,$days=0,$months=0,$years=0)
	{
		$date=getdate($time);
		return mktime($date['hours'],$date['minutes'], $date['seconds'],$date['mon']+$months,$date['mday']+$days,$date['year']+$years);
	}



	/**
	 * Takes two Group-Office settings like Ymd and - and converts this into Y-m-d
	 *
	 * @param	string $format Any format accepted by php's date function
	 * @param	string $seperator A seperate like - / or .
	 * @access public
	 * @return int unix timestamp
	 */

	public static function get_dateformat($format, $seperator)
	{
		$newformat = '';
		$end = strlen($format)-1;
		for($i=0;$i<$end;$i++)
		{
			$newformat .= $format[$i].$seperator;
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

	public static function get_timestamp($utime, $with_time=true, $timezone='GMT')
	{
		if(empty($utime))
		$utime=0;
			
		return Date::format('@'.$utime, $with_time, $timezone);
	}

	public static function format($time, $with_time=true, $timezone='GMT')
	{
		$d = new DateTime($time, new DateTimeZone($timezone));


		if($timezone!=$_SESSION['GO_SESSION']['timezone'])
		{
			$tz = new DateTimeZone(date_default_timezone_get());
			if($tz)
			{
				$d->setTimezone($tz);
			}
		}

		$date_format = $with_time ?  $_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'] : $_SESSION['GO_SESSION']['date_format'];

		return $d->format($date_format);
	}

	/*public static function local_to_gmt_time($utime)
	 {
		$d = new DateTime('@'.$utime, new DateTimeZone(date_default_timezone_get()));
		$d->setTimezone(new DateTimeZone('GMT'));
		return $d->format('U');
		}

		public static function gmt_to_local_time($utime)
		{
		$d = new DateTime('@'.$utime, new DateTimeZone('GMT'));
		//go_log(LOG_DEBUG, date_default_timezone_get());
		$d->setTimezone(new DateTimeZone(date_default_timezone_get()));
		return $d->format('U');
		}*/

	public static function get_timezone_offset($utime)
	{
		$d = new DateTime('@'.$utime, new DateTimeZone(date_default_timezone_get()));
		return $d->getOffset()/3600;
	}

	function ical_freq_to_repeat_type($freq)
	{
		switch($freq)
		{
			case 'WEEKLY':
				return REPEAT_WEEKLY;
				break;

			case 'DAILY':
				return REPEAT_DAILY;

				break;

			case 'MONTHLY':

				if (!isset($rrule['BYDAY']))
				{
					return REPEAT_MONTH_DATE;
				}else
				{
					return REPEAT_MONTH_DAY;
				}
				break;

			case 'YEARLY';
			return REPEAT_YEARLY;
			break;

			default:
				return REPEAT_NONE;
				break;
		}

	}


	public static function build_rrule($repeat_type, $interval, $repeat_end_time, $days, $month_time)
	{

		$rrule = 'RRULE:';

		switch($repeat_type)
		{
			case REPEAT_DAILY:
				$rrule .= 'FREQ=DAILY;';
				$rrule .= 'INTERVAL='.$interval.';';
				break;

			case REPEAT_WEEKLY:

				$event_days = array();

				if ($days['sun'] == '1')
				{
					$event_days[] = "SU";
				}
				if ($days['mon'] == '1')
				{
					$event_days[] = "MO";
				}
				if ($days['tue'] == '1')
				{
					$event_days[] = "TU";
				}
				if ($days['wed'] == '1')
				{
					$event_days[] = "WE";
				}
				if ($days['thu'] == '1')
				{
					$event_days[] = "TH";
				}
				if ($days['fri'] == '1')
				{
					$event_days[] = "FR";
				}
				if ($days['sat'] == '1')
				{
					$event_days[] = "SA";
				}

				$rrule .= "FREQ=WEEKLY;";
				$rrule .= 'INTERVAL='.$interval.';';
				$rrule .= "BYDAY=".implode(',', $event_days).';';
				break;

			case REPEAT_MONTH_DATE:
				$rrule = "RRULE:FREQ=MONTHLY;";
				$rrule .= 'INTERVAL='.$interval.';';

				break;

			case REPEAT_MONTH_DAY:

				$event_days = array();

				if ($days['sun'] == '1')
				{
					$event_days[] = $month_time."SU";
				}
				if ($days['mon'] == '1')
				{
					$event_days[] = $month_time."MO";
				}
				if ($days['tue'] == '1')
				{
					$event_days[] = $month_time."TU";
				}
				if ($days['wed'] == '1')
				{
					$event_days[] = $month_time."WE";
				}
				if ($days['thu'] == '1')
				{
					$event_days[] = $month_time."TH";
				}
				if ($days['fri'] == '1')
				{
					$event_days[] = $month_time."FR";
				}
				if ($days['sat'] == '1')
				{
					$event_days[] = $month_time."SA";
				}
					
				$rrule = "RRULE:FREQ=MONTHLY;";
				$rrule .= "BYDAY=".implode(',', $event_days).';';
				break;

			case REPEAT_YEARLY:
				$rrule = "RRULE:FREQ=YEARLY;";
				$rrule .= 'INTERVAL='.$interval.';';
				break;
		}

		if ($repeat_end_time>0)
		{
			$rrule .= "UNTIL=".date(go_ical::date_format, $repeat_end_time).";";
		}

		return $rrule;
	}



}