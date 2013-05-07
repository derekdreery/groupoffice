<?php
/*
 Copyright Intermesh 2003
 Author: Merijn Schering <mschering@intermesh.nl>
 Version: 1.0 Release date: 14 March 2004

 This program is free software; you can redistribute it and/or modify it
 under the terms of the GNU General Public License as published by the
 Free Software Foundation; either version 2 of the License, or (at your
 option) any later version.

 TODO: Attendee and VTODO support
 */

class go_ical extends calendar
{
	var $calendar_properties = '';
	var $events = array();
	var $datetime_format = 'Ymd\THis';

	const date_format = 'Ymd';

	var $timezone_id = '';

	var $version;

	var $line_break="\r\n";

	var $utc=false;

	var $method = '';

	var $add_leading_space_to_qp_encoded_line_wraps=false;

	var $dont_use_quoted_printable=false;


	var $reminder;

	function go_ical($version='2.0', $utc=false, $method='REQUEST')
	{
		parent::__construct();

		global $GO_CONFIG;

		require_once($GLOBALS['GO_CONFIG']->class_path.'base/reminder.class.inc.php');
		$this->reminder = new reminder();

		$this->version = $version;
		$this->utc=$utc;

		$this->method = $method;

		$this->calendar_properties =
			"VERSION:".$version.$this->line_break.
			"PRODID:-//Intermesh//NONSGML Group-Office//EN".$this->line_break.
			"CALSCALE:GREGORIAN".$this->line_break;
		
		if(!empty($method))
			$this->calendar_properties .= "METHOD:".$method.$this->line_break;
	}

	function format_transition_hour($hour){		

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

	function export_timezone($timezone=null, $set_timezone_id=false){

		if(empty($timezone))
			$timezone = date_default_timezone_get ();

		$tz = new DateTimeZone($_SESSION['GO_SESSION']['timezone']);
    //$tz = new DateTimeZone("Europe/Amsterdam");
		$transitions = $tz->getTransitions();

		$start_of_year = mktime(0,0,0,1,1);

    $to = Date::get_timezone_offset(time());
    if($to<0){
      if(strlen($to)==2)
        $to='-0'.($to*-1);
    }else
    {
      if(strlen($to)==1)
        $to='0'.$to;

      $to='+'.$to;
    }

		$STANDARD_TZOFFSETFROM=$STANDARD_TZOFFSETTO=$DAYLIGHT_TZOFFSETFROM=$DAYLIGHT_TZOFFSETTO=$to;

		$STANDARD_RRULE='';
		$DAYLIGHT_RRULE='';

		for($i=0,$max=count($transitions);$i<$max;$i++) {
			if($transitions[$i]['ts']>$start_of_year) {
				$dst_end = $transitions[$i];
				$dst_start = $transitions[$i+1];				

				$STANDARD_TZOFFSETFROM=$this->format_transition_hour($dst_end['offset']/3600);
				$STANDARD_TZOFFSETTO=$this->format_transition_hour($dst_start['offset']/3600);

				$DAYLIGHT_TZOFFSETFROM=$this->format_transition_hour($dst_start['offset']/3600);
				$DAYLIGHT_TZOFFSETTO=$this->format_transition_hour($dst_end['offset']/3600);

				$DAYLIGHT_RRULE = "RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=".date('n', $dst_end['ts']). $this->line_break;
				$STANDARD_RRULE="RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=".date('n', $dst_start['ts']). $this->line_break ;


				break;
			}
		}

		$timezone_name = $tz->getName();
		
		//hack for outlook. It only treats recurring events right with this name. When a daily recurring event goes from daylight to standard time it shifts.
		if($timezone_name=='Europe/Amsterdam' || $timezone_name=='Europe/Brussels' || $timezone_name=='Europe/Berlin')
			$timezone_name = 'W. Europe Standard Time';

		if($set_timezone_id)
			$this->timezone_id = $timezone_name;
		
		$t="BEGIN:VTIMEZONE" . $this->line_break .
				"TZID:" . $timezone_name . $this->line_break .
				"LAST-MODIFIED:19870101T000000Z" . $this->line_break .
				"BEGIN:STANDARD" . $this->line_break .
				"DTSTART:16010101T000000" . $this->line_break .
				$STANDARD_RRULE.
				"TZOFFSETFROM:".$STANDARD_TZOFFSETFROM."00" . $this->line_break .
				"TZOFFSETTO:".$STANDARD_TZOFFSETTO."00" . $this->line_break .
				//"TZNAME:".$timezone_name ." Standard". $this->line_break .
				"END:STANDARD" . $this->line_break .
				"BEGIN:DAYLIGHT" . $this->line_break .
				"DTSTART:16010101T000000" . $this->line_break .
				$DAYLIGHT_RRULE.
				"TZOFFSETFROM:".$DAYLIGHT_TZOFFSETFROM."00" . $this->line_break .
				"TZOFFSETTO:".$DAYLIGHT_TZOFFSETTO."00" . $this->line_break .
				//"TZNAME:".$timezone_name ." DST" . $this->line_break .
				"END:DAYLIGHT" . $this->line_break .
				"END:VTIMEZONE" . $this->line_break;
		return $t;
	}	

	function set_method($method)
	{
		$this->calendar_properties .= "METHOD:".$method.$this->line_break;
	}

	function export_calendar_header()
	{
		$str= "BEGIN:VCALENDAR".$this->line_break.
		$this->calendar_properties;
		return $str;
	}

	function export_calendar_footer()
	{
		return "END:VCALENDAR".$this->line_break;
	}


	function export_event($event_id, $attendee_email='')
	{
		$ics = $this->export_calendar_header();
		$ics .= is_array($event_id) ? $this->generate_response($event_id, $attendee_email) : $this->convert_event($this->get_event($event_id), $attendee_email);
		$ics .= $this->export_calendar_footer();

		return $ics;
	}
	
	function export_events($events){
		$ics = $this->export_calendar_header();
		
		$recurrence_alternative_days=array();
		foreach($events as $event){
			if($event['rrule']=='')
				$recurrence_alternative_days[]=date('Ymd',$event['start_time']);
		}
		
		foreach($events as $event){
			$ics .=  $this->convert_event($event,'',$recurrence_alternative_days);
		}
		$ics .= $this->export_calendar_footer();

		return $ics;
	}

	function export_calendar($calendar_id, $months_in_past=0)
	{
		$ics = $this->export_calendar_header();

		$ics .= $this->export_timezone('', true);

		$cal = new calendar();

		$start_time = $months_in_past > 0 ? Date::date_add(time(),0,-$months_in_past) : 0;

		$cal->get_events(array($calendar_id),0, $start_time);
		while($cal->next_record())
		{
			$ics .= $this->convert_event($cal->record);
		}

		$ics .= $this->export_calendar_footer();
		return $ics;
	}


	/*function format_line($name_part, $value_part)
	{
		$value_part = str_replace("\r\n","\n", $value_part);

		$qp_value_part = String::quoted_printable_encode($value_part);

		if($value_part != $qp_value_part || strlen($name_part.$value_part)>=73)
		{
			$name_part .= ";ENCODING=QUOTED-PRINTABLE;CHARSET=UTF-8:";
			return explode("\n", $name_part.$qp_value_part);
		}else
		{
			$name_part .= ';CHARSET=UTF-8:';
		}
		return array($name_part.$value_part);
	}*/

	/*function format_line($name_part, $value_part)
	 {
		$value_part = str_replace("\r\n","\n", $value_part);
		$value_part = str_replace("\n",'\n', $value_part);

		$value_part = str_replace(',','\,', $value_part);
		$value_part = str_replace('"','\"', $value_part);
		$value_part = str_replace('\'',"\'", $value_part);

		$name_part .= ';CHARSET=UTF-8:';
		return array($name_part.$value_part);
		}*/

	/**
	 *
	 * @global type $GO_CONFIG
	 * @global type $GO_SECURITY
	 * @global type $GO_MODULES
	 * @global type $charset
	 * @param type $event
	 * @param type $attendee_email
	 * @param type $recurrence_alternative_times //exception dates shouldn't be exported if a related event with the same UUID and recurrence-id is set. This contains an array of dates with YYYYMMDD values of those events
	 * @return string 
	 */
	function convert_event($event, $attendee_email='', $recurrence_alternative_days=array())
	{
		global $GO_CONFIG, $GO_SECURITY, $GO_MODULES, $charset;

		if($this->timezone_id != '')
		{
			$timezone_offset = Date::get_timezone_offset($event['start_time'])*3600;
		}else
		{
			$timezone_offset = 0;
		}

		$lines = array();

		if($event['all_day_event']=='1')
		{
			$event['end_time']+=60;
		}

		$lines[] = "BEGIN:VEVENT";
		
		if (empty($event['uuid'])) {

			$db = new db();
			$event['uuid'] = $ue['uuid'] = UUID::create('event', $event['id']);
			$ue['id'] = $event['id'];
			$db->update_row('cal_events', 'id', $ue);
		}

		$lines[] = "UID:".$event['uuid'];
		
		if(!empty($event['exception_for_event_id'])){
//			$line = "RECURRENCE-ID;VALUE=DATE";			
//			if($this->timezone_id != '')
//			{
//				$line .= ";TZID=\"".$this->timezone_id."\"";
//			}
//			$line .= ":".date(go_ical::date_format,$event['start_time']);
//
//			$lines[]=$line;
			
			
			
			$cal = new calendar();
			$exception_for_event = $cal->get_event($event['exception_for_event_id']);
			
			$exception_date=getdate($event['start_time']);
			$old_date = getdate($exception_for_event['start_time']);					

			$exception_time= mktime($old_date['hours'],$old_date['minutes'], 0,$exception_date['mon'],$exception_date['mday'],$exception_date['year']);						
			
			
			//$line = "RECURRENCE-ID;VALUE=DATE";			
			$line = "RECURRENCE-ID";			
			if ($event['all_day_event'] == '1')
			{
				$line .= ";VALUE=DATE";
				$date = date(go_ical::date_format, $exception_time);
				
			}else
			{
				$date = $this->utc ? gmdate($this->datetime_format , $exception_time).'Z' : date($this->datetime_format, $exception_time);
			}
			if($this->timezone_id != '')
			{
				$line .= ";TZID=\"".$this->timezone_id."\"";
			}elseif(!$this->utc){
				$line .= ";TZID=\"".date_default_timezone_get()."\"";
			}
				
			$line .= ":".$date;
			$lines[]=$line;
		}

		$lines = array_merge($lines, String::format_vcard_line('SUMMARY', $event['name'], $this->add_leading_space_to_qp_encoded_line_wraps, $this->dont_use_quoted_printable));
		//if ($event['description'] != '')
		//{
			$lines = array_merge($lines, String::format_vcard_line('DESCRIPTION', $event['description'], $this->add_leading_space_to_qp_encoded_line_wraps, $this->dont_use_quoted_printable));
		//}
		//if ($event['location'] != '')
		//{
			$lines = array_merge($lines, String::format_vcard_line('LOCATION', $event['location'], $this->add_leading_space_to_qp_encoded_line_wraps, $this->dont_use_quoted_printable));
		//}
			
		//$user = $GO_USERS->get_user($event['user_id']);
	

		require_once($GLOBALS['GO_MODULES']->modules['calendar']['class_path'].'calendar.class.inc.php');

		$cal = new calendar();
		$cal2 = new calendar();
		$cal->get_participants($event['id']);
		while($cal->next_record())
		{
			if($cal->f('is_organizer'))
			{
				$lines = array_merge($lines, array('ORGANIZER;CN="'.$cal->f('name').'":mailto:'.$cal->f('email')));
			}
			
			if(!$attendee_email || ( $cal->f('email') == $attendee_email))
			{
				$status = $cal2->get_participant_status_name($cal->f('status'));
				$rsvp = ($cal->f('status')) ? 'FALSE' : 'TRUE';
				$lines = array_merge($lines, array('ATTENDEE;CUTYPE=INDIVIDUAL;ROLE='.$cal->f('role').';PARTSTAT='.$status.';RSVP='.$rsvp.';CN="'.$cal->f('name').'";LANGUAGE=en:MAILTO:'.$cal->f('email')));
			}
		}

		switch($this->method)
		{
			case 'REPLY':
				$status = 'CONFIRMED';
				break;
			case 'CANCEL':
				$status = 'CANCEL';
				break;
			default:
				$status = $event['status'];
				break;
		}
		
		$lines[] = "STATUS:".$status;
		$lines[] = "SEQUENCE:".$event['sequence'];
		$lines[] = "TRANSP:OPAQUE";
			
		if($event['private'] == '1')
		{
			$lines[] ="CLASS:PRIVATE";
		}else
		{
			$lines[] ="CLASS:PUBLIC";
		}		

		if($event['all_day_event']=='1'){

			//Used to be: $DT_format = go_ical::date_format;
			//Changed this for synthesis syncml client

			$DT_format = $this->version=='1.0' ? $this->datetime_format : go_ical::date_format;

			//changed this for funambol compatibility. Always send dates in local time for all day events

			$start_date = date($DT_format , $event['start_time']);
			$end_date = date($DT_format, $event['end_time']);
		}else
		{
			$DT_format = $this->datetime_format;

			$start_date = $this->utc ? gmdate($DT_format , $event['start_time']).'Z' : date($DT_format , $event['start_time']);
			$end_date = $this->utc ? gmdate($DT_format , $event['end_time']).'Z' : date($DT_format, $event['end_time']);
		}

		

		if($this->version == '1.0' && $event['all_day_event'] == '1')
		{
			//$start_date = $this->utc ? gmdate($this->datetime_format, $event['start_time']).'Z' : date($this->datetime_format, $event['start_time']);
			//$end_date = $this->utc ? gmdate($this->datetime_format, $event['end_time']).'Z' : date($this->datetime_format, $event['end_time']);

			$line = "DTSTART:".$start_date;
			$lines[] = $line;

			//if($event['all_day_event']=='0')
			//{
			$line = "DTEND:";
			$line .= $end_date;
			$lines[] = $line;
			//}

		}else
		{	
			$line = "DTSTART";

			if ($event['all_day_event'] == '1')
			{
				$line .= ";VALUE=DATE";
			}
			if($this->timezone_id != '')
			{
				$line .= ";TZID=\"".$this->timezone_id."\":".$start_date;
			}else
			{
				$line .= ":".$start_date;

			}
			$lines[]=$line;

			$line = "DTEND";

			if ($event['all_day_event'] == '1')
			{
				$line .= ";VALUE=DATE";
			}
			if($this->timezone_id != '')
			{
				$line .= ";TZID=\"".$this->timezone_id."\":".$end_date;
			}else
			{
				$line .= ":".$end_date;


			}
			$lines[]=$line;
		}

		

		if(!empty($event['rrule']))
		{
			require_once($GLOBALS['GO_CONFIG']->class_path.'ical2array.class.inc');
			$ical2array = new ical2array();

			if($this->version != '1.0')
			{
				$rrule = $ical2array->parse_rrule($event['rrule']);

				if((!$this->utc || $event['all_day_event']=='1') && isset($rrule['BYDAY'])){
					if($rrule['FREQ']=='MONTHLY'){
						$month_time = $rrule['BYDAY'][0];
						$day = substr($rrule['BYDAY'], 1);
						$days =Date::byday_to_days($day);
					}else
					{
						$month_time = 1;
						$days = Date::byday_to_days($rrule['BYDAY']);
					}
					$days = Date::shift_days_to_local($days, date('G', $event['start_time']), Date::get_timezone_offset($event['start_time']));
	

					$lines[]=Date::build_rrule(Date::ical_freq_to_repeat_type($rrule), $rrule['INTERVAL'], $event['repeat_end_time'], $days, $month_time);
				}else
				{
					$lines[]=$event['rrule'];
				}
			}else
			{
				$rrule = $ical2array->parse_rrule($event['rrule']);

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

				if(isset($rrule['BYDAY']))
				{

					if($rrule['FREQ']=='MONTHLY'){
						$event['month_time'] = $rrule['BYDAY'][0];
						$day = substr($rrule['BYDAY'], 1);
						$days =Date::byday_to_days($day);
					}else
					{
						$days =Date::byday_to_days($rrule['BYDAY']);
					}

					if(!$this->utc)
						$days = Date::shift_days_to_local($days, date('G', $event['start_time']), Date::get_timezone_offset($event['start_time']));

				}

				switch($rrule['FREQ'])
				{
					case 'DAILY':
						$line = 'RRULE:D'.$event['repeat_every'];
						if ($event['repeat_forever'] == '0')
						{
							//$line .= ' '.date($this->datetime_format, $event['repeat_end_time']-86400).'Z';
							$line .= ' '.date($this->datetime_format, $event['repeat_end_time']).'Z';
						}else
						{
							$line .= ' #0';
						}
						$lines[]=$line;
						break;

					case 'WEEKLY':

						if(!$this->utc || $event['all_day_event']=='1')
							$days = Date::shift_days_to_local($days, date('G', $event['start_time']), Date::get_timezone_offset($event['start_time']));
							
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



							
						$line = 'RRULE:W'.$event['repeat_every'].' ';
						$line .= implode(' ', $event_days);
						if ($event['repeat_forever'] == '0')
						{
							$line .= ' '.date($this->datetime_format, $event['repeat_end_time']).'Z';
						}else
						{
							$line .= ' #0';
						}
						$lines[]=$line;
						break;

					case 'MONTHLY':
						if (!isset($rrule['BYDAY']))
						{
							//$line = 'RRULE:MD'.$event['repeat_every'].' '.date('j', $event['start_time']).'+';
							$line = 'RRULE:MD'.$event['repeat_every'].' '.date('j', $event['start_time']);
							//$line = 'RRULE:MD'.$event['repeat_every'].' ';

							if ($event['repeat_forever'] == '0')
							{
								$line .= ' '.date($this->datetime_format, $event['repeat_end_time']).'Z';
							}else
							{
								$line .= ' #0';
							}
						}else
						{
							
							$event_days = array();

							if ($days['sun'] == '1')
							{
								$event_days[] ="SU";
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




							$line = 'RRULE:MP'.$event['repeat_every'].' '.$event['month_time'].'+ '.$event_days[0];
							if ($event['repeat_forever'] == '0')
							{
								$line .= ' '.date($this->datetime_format, $event['repeat_end_time']).'Z';
							}else
							{
								$line .= ' #0';
								
							}
						}

						$lines[]=$line;
						break;

					case 'YEARLY':

						//$line = 'RRULE:YM'.$event['repeat_every'].' '.date('n',$event['start_time']);
						$line = 'RRULE:YM'.$event['repeat_every'];
						if ($event['repeat_forever'] == '0')
						{
							$line .= ' '.date($this->datetime_format, $event['repeat_end_time']).'Z';
						}else
						{
							$line .= ' #0';
						}
							
						$lines[]=$line;
						break;
				}
			}

		
				$cal = new calendar();
				if($cal->get_exceptions($event['id']))
				{
					while($cal->next_record())
					{
						//exception dates shouldn't be exported if a related event with the same UUID
						//and recurrence-id is set.
						if(!in_array(date('Ymd', $cal->f('time')), $recurrence_alternative_days)){
							$exception_time=$cal->f('time');
							$line = 'EXDATE';
							if ($event['all_day_event'] == '1')
							{
								$line .= ";VALUE=DATE";
								$date = date(go_ical::date_format, $exception_time);

							}else
							{
								$date = $this->utc ? gmdate($this->datetime_format , $exception_time).'Z' : date($this->datetime_format, $exception_time);
							}
							
							//$exception = date($this->datetime_format, $cal->f('time'));
							$lines[]=$line.':'.$date;
						}
					}
					
//					while($cal->next_record())
//					{
//						$exception = $this->utc ? gmdate($this->datetime_format , $cal->f('time')).'Z' : date($this->datetime_format , $cal->f('time'));
//						//$exception = date($this->datetime_format, $cal->f('time'));
//						$exceptions[]=$exception;
//					}
//					$line = 'EXDATE:';
//
//					//$count=1;
//					$exception_count = count($exceptions);
//					for($i=0;$i<$exception_count;$i++)
//					{
//						$line.=$exceptions[$i];
//						if($i!=$exception_count-1)
//						{
//							$line .=';';
//						}
//						/*$count++;
//						 if($count=='3')
//						 {
//							$lines[]=$line;
//							$line='  ';
//							$count=0;
//							}*/
//					}
//					if(!empty($line))
//					{
//						$lines[]=$line;
//					}
				}
			}
		


		//alarm
		if(!empty($event['reminder']) && $event['start_time']>time())
		{
			$this->reminder->get_reminders_by_model_id($event['id'], 1);
			$reminder = $this->reminder->next_record();
			if($reminder){

				$remind_time = $event['start_time']-$event['reminder'];

				$atime = date($this->datetime_format, $remind_time);
				if($this->version != '1.0')
				{

					$lines[] = 'BEGIN:VALARM';
					$lines[] = 'DESCRIPTION:';
					$lines[] = 'ACTION:DISPLAY';
					$lines[] = 'TRIGGER;VALUE=DURATION;RELATED=START:-PT'.($event['reminder']/60).'M';
					$lines[] = 'END:VALARM';
				}else
				{
					$lines[] = 'AALARM:'.$atime.';;0;'.$event['name'];
					//Nokia crashes on DALARM at event replace
					//$lines[] = 'DALARM:'.date($this->datetime_format, Date::gmt_to_local_time($remind_time)).';;'.$event['name'];
				}
			}
		}

		if(!isset($event['mtime']))
			$event['mtime']=time();
		
		$timestamp = gmdate($this->datetime_format, $event['mtime']).'Z';
		$lines[]= "DTSTAMP:".$timestamp;
		$lines[]= "LAST-MODIFIED:".$timestamp;
		//$lines[]= "CREATED:".$timestamp;

		$lines[] = "END:VEVENT";

		/*$vevent = '';
		foreach ($lines as $line) {
		 preg_match_all( '/.{1,73}([^=]{0,2})?/', $line, $matches);
		 $vevent .= implode( '=' . chr(13).chr(10).' ', $matches[0] )."\r\n"; // add soft crlf's
		}*/
		$vevent = implode($this->line_break, $lines).$this->line_break;
		//go_debug($vevent);
		return $vevent;
	}

	function generate_response($object, $email)
	{
		$lines = array("BEGIN:VEVENT");
		
		foreach($object as $key => $value) 
		{
			if($key == 'ATTENDEES')
			{
				foreach($value as $attendee)
				{
					if(strtolower($attendee['value']) == 'mailto:'.$email)
					{
						$attendee['params']['PARTSTAT'] = 'DECLINED';
						$params_str = 'ATTENDEE';
						foreach($attendee['params'] as $k => $v)
						{
							$params_str .= ';'.$k.'='.$v;
						}
						$lines = array_merge($lines, String::format_vcard_line($params_str, $attendee['value'], $this->add_leading_space_to_qp_encoded_line_wraps, $this->dont_use_quoted_printable));
					}
				}
			}else
			if((count($object[$key]) == 1) && is_array($value) && isset($value['value']))
			{
				$lines = array_merge($lines, String::format_vcard_line($key, $value['value'], $this->add_leading_space_to_qp_encoded_line_wraps, $this->dont_use_quoted_printable));
			}else
			if(count($object[$key]) == 2 && is_array($value) && isset($value['params']))
			{
				$params_str = $key;
				foreach($value['params'] as $k => $v)
				{
					$params_str .= ';'.$k.'='.$v;
				}
				$lines = array_merge($lines, String::format_vcard_line($params_str, $value['value'], $this->add_leading_space_to_qp_encoded_line_wraps, $this->dont_use_quoted_printable));
			}			
		}

		$timestamp = date($this->datetime_format, time());
		
//		if($recurrence_id_time){
//			$line = "RECURRENCE-ID";			
//			if($this->timezone_id != '')
//			{
//				$line .= ";TZID=\"".$this->timezone_id."\"";
//			}
//			$line .= ":".date($this->datetime_format,$recurrence_id_time);
//
//			$lines[]=$line;
//		}
		
		$lines[]= "LAST-MODIFIED:".$timestamp;
		$lines[] = "END:VEVENT";
		
		//var_dump($event);
		
		return implode("\r\n", $lines)."\r\n";

	}
	
}
