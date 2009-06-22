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

	var $line_break="\n";

	function go_ical($version='2.0')
	{
		parent::__construct();

		$this->version = $version;

		$this->calendar_properties =
			"VERSION:".$version.$this->line_break.
			"PRODID:-//Intermesh//NONSGML Group-Office//EN".$this->line_break.
			"CALSCALE:GREGORIAN".$this->line_break.
			"METHOD:PUBLISH".$this->line_break;
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


	function export_event($event_id)
	{
		$ics = $this->export_calendar_header();
		$ics .= $this->convert_event($this->get_event($event_id));
		$ics .= $this->export_calendar_footer();

		return $ics;
	}

	function export_calendar($calendar_id)
	{
		$ics = $this->export_calendar_header();

		$cal = new calendar();

		$cal->get_events(array($calendar_id));
		while($cal->next_record())
		{
			$ics .= $this->convert_event($cal->record);
		}

		$ics .= $this->export_calendar_footer();
		return $ics;
	}


	function format_line($name_part, $value_part)
	{
		$value_part = str_replace("\r\n","\n", $value_part);

		$qp_value_part = String::quoted_printable_encode($value_part);

		if($value_part != $qp_value_part)
		{
			$name_part .= ";ENCODING=QUOTED-PRINTABLE;CHARSET=UTF-8:";
			return explode("\n", $name_part.$qp_value_part);
		}else
		{
			$name_part .= ';CHARSET=UTF-8:';
		}
		return array($name_part.$value_part);
	}

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

	function convert_event($event)
	{
		global $GO_CONFIG, $GO_SECURITY, $charset;

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
		$lines[] = "UID:".$event['id'];

		$lines = array_merge($lines, $this->format_line('SUMMARY', $event['name']));
		if ($event['description'] != '')
		{
			$lines = array_merge($lines, $this->format_line('DESCRIPTION', $event['description']));
		}
		if ($event['location'] != '')
		{
			$lines = array_merge($lines, $this->format_line('LOCATION', $event['location']));
		}

		$lines[] =	"STATUS:".$event['status'];
			
		if($event['private'] == '1')
		{
			$lines[] ="CLASS:PRIVATE";
		}else
		{
			$lines[] ="CLASS:PUBLIC";
		}

		if($this->version == '1.0' && $event['all_day_event'] == '1')
		{
			$line = "DTSTART:".date($this->datetime_format, $event['start_time']);
			$lines[] = $line;

			//if($event['all_day_event']=='0')
			//{
			$line = "DTEND:";
			$line .= date($this->datetime_format, $event['end_time']);
			$lines[] = $line;
			//}

		}else
		{
			$DT_format = $event['all_day_event'] == '1' ? go_ical::date_format : $this->datetime_format;

			$line = "DTSTART";

			if ($event['all_day_event'] == '1')
			{
				$line .= ";VALUE=DATE";
			}
			if($this->timezone_id != '')
			{
				$line .= ";TZID=".$this->timezone_id.":".date($DT_format, $event['start_time']);
			}else
			{
				$line .= ":".date($DT_format, $event['start_time']);

			}
			$lines[]=$line;

			$line = "DTEND";

			if ($event['all_day_event'] == '1')
			{
				$line .= ";VALUE=DATE";
			}
			if($this->timezone_id != '')
			{
				$line .= ";TZID=".$this->timezone_id.":".date($DT_format, $event['end_time']);
			}else
			{
				$line .= ":".date($DT_format, $event['end_time']+$timezone_offset);


			}
			$lines[]=$line;
		}


		if(!empty($event['rrule']))
		{

			if($this->version != '1.0')
			{
					
				$lines[]= $event['rrule'];
			}else
			{
					
					
				require_once($GO_CONFIG->class_path.'ical2array.class.inc');
				$ical2array = new ical2array();


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
					$days =Date::byday_to_days($rrule['BYDAY']);

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
							//$line .= ' #0';
							//test for symbian
							$line .= ' '.date($this->datetime_format, mktime(0,0,0,1,1,2100)).'Z';
						}
						$lines[]=$line;
						break;

					case 'MONTHLY':
						if (!isset($rrule['BYDAY']))
						{
							$line = 'RRULE:MD'.$event['repeat_every'].' '.date('j', $event['start_time']).'+';
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
					$exception = date($this->datetime_format, $cal->f('time'));
					$exceptions[]=$exception;
				}
				$line = 'EXDATE:';

				//$count=1;
				$exception_count = count($exceptions);
				for($i=0;$i<$exception_count;$i++)
				{
					$line.=$exceptions[$i];
					if($i!=$exception_count-1)
					{
						$line .=';';
					}
					/*$count++;
					 if($count=='3')
					 {
						$lines[]=$line;
						$line='  ';
						$count=0;
						}*/
				}
				if(!empty($line))
				{
					$lines[]=$line;
				}
			}
		}


		//alarm
		if($event['reminder']>0)
		{
			$remind_time = $event['start_time']-$event['reminder'];

			$atime = date($this->datetime_format, $remind_time);
			$lines[] = 'AALARM:'.$atime.';;0;'.$event['name'];
			//Nokia crashes on DALARM at event replace
			//$lines[] = 'DALARM:'.date($this->datetime_format, Date::gmt_to_local_time($remind_time)).';;'.$event['name'];
		}

		$lines[]= "DTSTAMP:".date($this->datetime_format, $event['ctime']);
		$lines[] = "END:VEVENT";

		$vevent = '';
		foreach ($lines as $line) {
		 preg_match_all( '/.{1,73}([^=]{0,2})?/', $line, $matches);
		 $vevent .= implode( '=' . chr(13).chr(10), $matches[0] )."\r\n"; // add soft crlf's
		}
		return $vevent;
	}
}
