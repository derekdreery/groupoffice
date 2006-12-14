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
	var $date_format = 'Ymd';
	
	var $timezone_id = '';
	
	var $version;
	
	var $local_times;
	
	var $line_break="\n";

	function go_ical($version='2.0', $local_times=false)
	{
		$this->db();
		
		$this->version = $version;
		$this->local_times = $local_times;

		$this->calendar_properties =
			"VERSION:".$version.$this->line_break.
			"PRODID:-//Intermesh//NONSGML Group-Office//EN".$this->line_break.
			"CALSCALE:GREGORIAN".$this->line_break.
			"METHOD:PUBLISH\r\n";
	}
	
	function export_calendar_header($with_timezones=true)
	{
		$str= "BEGIN:VCALENDAR".$this->line_break.
				$this->calendar_properties;
				
		if($this->version=='2.0' && $with_timezones)
		{
				$str .= $this->export_timezone();
		}
		return $str;
	}
	
	function export_calendar_footer()
	{
		return "END:VCALENDAR\r\n";
	}

	function format_timezone($timezone)
	{
		if($timezone < 10 && $timezone > -10)
		{
			if($timezone > 0)
			{
				$timezone = '+0'.$timezone.'00';
			}else
			{
				$timezone = '-0'.($timezone*-1).'00';				
			}
		}else
		{
			if($timezone > 0)
			{
				$timezone = '+'.$timezone.'00';
			}else
			{
				$timezone = '-'.$timezone.'00';				
			}
		}
		
		return $timezone;
	}

	function export_timezone()
	{
		global $GO_LANGUAGE;
		
		$this->timezone_id = $GO_LANGUAGE->language['description'];
		
		$timezone =	"BEGIN:VTIMEZONE".$this->line_break.
								"TZID:".$this->timezone_id.$this->line_break;
								"LAST-MODIFIED:".date($this->date_format, get_time()).$this->line_break;
							
		$dst_tz_offset_from = $this->format_timezone($_SESSION['GO_SESSION']['timezone']);			
		$dst_tz_offset_to = $this->format_timezone($_SESSION['GO_SESSION']['DST']+$_SESSION['GO_SESSION']['timezone']);		
							
		if($_SESSION['GO_SESSION']['DST'] > 0)
		{		
			$timezone .= 	"BEGIN:DAYLIGHT".$this->line_break.
										"DTSTART:19700329T030000".$this->line_break.										
										"TZOFFSETTO:".$dst_tz_offset_to.$this->line_break.
										"TZOFFSETFROM:".$dst_tz_offset_from.$this->line_break.
										"TZNAME:GMT".$dst_tz_offset_to.$this->line_break.
										"RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=3".$this->line_break.
										"END:DAYLIGHT\r\n";
		}
		
		$timezone .=  "BEGIN:STANDARD".$this->line_break.
									"TZOFFSETTO:".$dst_tz_offset_from.$this->line_break.
									"TZOFFSETFROM:".$dst_tz_offset_to.$this->line_break.
									"TZNAME:GMT".$dst_tz_offset_from.$this->line_break;
			
		if($_SESSION['GO_SESSION']['DST'] > 0)
		{
				$timezone .= 	"DTSTART:19701025T040000".$this->line_break.
											"RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=10\r\n";
		}else
		{
				$timezone .= "DTSTART:19700101T000000\r\n";
		}
		
		$timezone .= 	"END:STANDARD".$this->line_break.
									"END:VTIMEZONE\r\n";
		return $timezone;
	}

	function export_event($event_id, $with_timezones=true)
	{
		$ics = $this->export_calendar_header($with_timezones);
		$ics .= $this->convert_event($this->get_event($event_id));
		$ics .= $this->export_calendar_footer();
		
		return $ics;
	}

	function export_calendar($calendar_id)
	{		
		$ics = $this->export_calendar_header();
		
		$cal = new calendar();
		
		$cal->get_events(false,true,false, $calendar_id);
		while($cal->next_record())
		{
			$ics .= $this->convert_event($cal->Record);
		}
		
		$ics .= $this->export_calendar_footer();
		return $ics;
	}
	
	function format_line($name_part, $value_part)
	{
		global $charset;
		$value_part = str_replace("\r\n","\n", $value_part);
		$qp_value_part = quoted_printable_encode($value_part);		
		
		if($value_part != $qp_value_part)
		{
			$name_part .= ';ENCODING=QUOTED-PRINTABLE;CHARSET='.$charset.":=\n";
//			$qp_value_part = ' '.str_replace("\n", "\n ", $qp_value_part);
			$qp_value_part = str_replace('=0A', "=0D=0A", $qp_value_part)."\n";
			
			return explode("\n", $name_part.$qp_value_part);
		}else
		{
			$name_part .= ':';			
		}
		return array($name_part.$value_part);		
	}
	
	function convert_event($event)
	{
		global $GO_CONFIG, $GO_SECURITY, $charset;
		
		if($this->timezone_id != '' || $this->local_times)
		{		  
			$timezone_offset = get_timezone_offset($event['start_time'])*3600;
		}else
		{
			$timezone_offset = 0;	
		}
		
		$lines = array();
	
		if($event['todo']=='1')
		{
			$lines[] = "BEGIN:VTODO";
		}else
		{
			$lines[] = "BEGIN:VEVENT";
		}
		$lines[] = "UID:".$event['id'];
			
		
		$lines = array_merge($lines, $this->format_line('SUMMARY', $event['name']));
		if ($event['description'] != '')
		{
		//$lines =$this->format_line('DESCRIPTION', $event['description']);
		//var_dump($lines);
			$lines = array_merge($lines, $this->format_line('DESCRIPTION', $event['description']));
		}
		if ($event['location'] != '')
		{
			$lines = array_merge($lines, $this->format_line('LOCATION', $event['location']));
		}		
		$status = $this->get_status($event['status_id']);
		$lines[] =	"STATUS:".$status['name'];
			
		if($event['permissions'] == PRIVATE_EVENT)
		{
			$lines[] ="CLASS:PRIVATE";
		}else
		{
			$lines[] ="CLASS:PUBLIC";
		}

		if($this->version == '1.0' && $event['all_day_event'] == '1')
		{
			$line = "DTSTART:".date($this->datetime_format, $event['start_time']+$timezone_offset);
			if(!$this->local_times) $line .= "Z";
			$lines[] = $line;
			
			if($event['todo'] == '1')
			{
				$line = "DUE:";
			}else
			{
				$line = "DTEND:";				
			}
			$line .= date($this->datetime_format, $event['end_time']+$timezone_offset+59);
			if(!$this->local_times) $line .= "Z";
			$lines[] = $line;
			
			
			if($event['completion_time']>0)
			{
				$line = "COMPLETED:".date($this->datetime_format, $event['start_time']+$timezone_offset);
				if(!$this->local_times) $line .= "Z";
				$lines[] = $line;
			}			
		}else
		{
			$DT_format = $event['all_day_event'] == '1' ? $this->date_format : $this->datetime_format;
			
			$line = "DTSTART";
			
			if ($event['all_day_event'] == '1')
			{
				$line .= ";VALUE=DATE";
			}			
			if($this->timezone_id != '') 
			{
				$line .= ";TZID=".$this->timezone_id.":".date($DT_format, $event['start_time']+$timezone_offset);
			}else
			{
				$line .= ":".date($DT_format, $event['start_time']+$timezone_offset);			
				if(!$this->local_times) $line .= "Z";
			}	
			$lines[]=$line;

			if($event['todo'] == '1')
			{
				$line = "DUE";
			}else
			{
				$line = "DTEND";
			}
			if ($event['all_day_event'] == '1')
			{
				$line .= ";VALUE=DATE";
			}			
			if($this->timezone_id != '') 
			{
				$line .= ";TZID=".$this->timezone_id.":".date($DT_format, $event['end_time']+$timezone_offset);
			}else
			{
				$line .= ":".date($DT_format, $event['end_time']+$timezone_offset);
				
				if(!$this->local_times) $line .= "Z";
			}			
			$lines[]=$line;
			
			if($event['completion_time']>0)
			{
				$line = "COMPLETED";			
				if ($event['all_day_event'] == '1')
				{
					$line .= ";VALUE=DATE";
				}			
				if($this->timezone_id != '') 
				{
					$line .= ";TZID=".$this->timezone_id.":".date($DT_format, $event['start_time']+$timezone_offset);
				}else
				{
					$line .= ":".date($DT_format, $event['start_time']+$timezone_offset);			
					if(!$this->local_times) $line .= "Z";
				}	
				$lines[]=$line;
			}
		}
		

		
		
		if($this->local_times)
		{
			$event = $this->shift_days_to_local($event);
		}
	
		if($event['todo'] == '0')
		{
			switch($event['repeat_type'])
			{
				case REPEAT_DAILY:
					if($this->version == '1.0')
					{
						$line = 'RRULE:D'.$event['repeat_every'];
						if ($event['repeat_forever'] == '0')
						{
							//$line .= ' '.date($this->datetime_format, $event['repeat_end_time']-86400).'Z';
							$line .= ' '.date($this->datetime_format, $event['repeat_end_time']-86400).'Z';
						}else
						{
							$line .= ' #0';
						}

					}else
					{
		 				$line = "RRULE:FREQ=DAILY;";

						if ($event['repeat_forever'] == '0')
						{
							//$line .= "UNTIL=".date($this->date_format, $event['repeat_end_time']-86400).";";
							$line .= "UNTIL=".date($this->date_format, $event['repeat_end_time']).";";
						}

						$line .= "INTERVAL=".$event['repeat_every'];

					}
					$lines[]=$line;
				break;

				case REPEAT_WEEKLY:
					
					$event_days = array();

					if ($event['sun'] == '1')
					{
						$event_days[] = "SU";
					}
					if ($event['mon'] == '1')
					{
						$event_days[] = "MO";
					}
					if ($event['tue'] == '1')
					{
						$event_days[] = "TU";
					}
					if ($event['wed'] == '1')
					{
						$event_days[] = "WE";
					}
					if ($event['thu'] == '1')
					{
						$event_days[] = "TH";
					}
					if ($event['fri'] == '1')
					{
						$event_days[] = "FR";
					}
					if ($event['sat'] == '1')
					{
						$event_days[] = "SA";
					}
				
					if($this->version == '1.0')
					{
						$line = 'RRULE:W'.$event['repeat_every'].' ';
						$line .= implode(' ', $event_days);
						if ($event['repeat_forever'] == '0')
						{
							$line .= ' '.date($this->datetime_format, $event['repeat_end_time']).'Z';
						}else
						{
							$line .= ' #0';
						}
					}else
					{
						$line = "RRULE:FREQ=WEEKLY;";

						if ($event['repeat_forever'] == '0')
						{
							$line .= "UNTIL=".date($this->date_format, $event['repeat_end_time']).";";
						}

						$line .= "INTERVAL=".$event['repeat_every'].";BYDAY=";
						$line .= implode(',', $event_days);
					}
					$lines[]=$line;
				break;

				case REPEAT_MONTH_DATE:
					if($this->version == '1.0')
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
						$line = "RRULE:FREQ=MONTHLY;";
						if ($event['repeat_forever'] == '0')
						{
							$line .= "UNTIL=".date($this->date_format, $event['repeat_end_time']).";";
						}
						$line .= "INTERVAL=".$event['repeat_every'];
					}
					$lines[]=$line;
				break;

				case REPEAT_MONTH_DAY:				
					if($this->version == '1.0')
					{
						$event_days = array();

						if ($event['sun'] == '1')
						{
							$event_days[] ="SU";
						}
						if ($event['mon'] == '1')
						{
							$event_days[] = "MO";
						}
						if ($event['tue'] == '1')
						{
							$event_days[] = "TU";
						}
						if ($event['wed'] == '1')
						{
							$event_days[] = "WE";
						}
						if ($event['thu'] == '1')
						{
							$event_days[] = "TH";
						}
						if ($event['fri'] == '1')
						{
							$event_days[] = "FR";
						}
						if ($event['sat'] == '1')
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
						
					}else
					{
						$event_days = array();

						if ($event['sun'] == '1')
						{
							$event_days[] = $event['month_time']."SU";
						}
						if ($event['mon'] == '1')
						{
							$event_days[] = $event['month_time']."MO";
						}
						if ($event['tue'] == '1')
						{
							$event_days[] = $event['month_time']."TU";
						}
						if ($event['wed'] == '1')
						{
							$event_days[] = $event['month_time']."WE";
						}
						if ($event['thu'] == '1')
						{
							$event_days[] = $event['month_time']."TH";
						}
						if ($event['fri'] == '1')
						{
							$event_days[] = $event['month_time']."FR";
						}
						if ($event['sat'] == '1')
						{
							$event_days[] = $event['month_time']."SA";
						}
						
						$line = "RRULE:FREQ=MONTHLY;";
						if ($event['repeat_forever'] == '0')
						{
							$line .= "UNTIL=".date($this->date_format, $event['repeat_end_time']).";";
						}
						$line .= "INTERVAL=".$event['repeat_every'].";BYDAY=";
						$line .= implode(',', $event_days);
					}
					$lines[]=$line;

				break;

				case REPEAT_YEARLY:
					if($this->version=='1.0')
					{
						//$line = 'RRULE:YM'.$event['repeat_every'].' '.date('n',$event['start_time']);
						$line = 'RRULE:YM'.$event['repeat_every'];
						if ($event['repeat_forever'] == '0')
						{
							$line .= ' '.date($this->datetime_format, $event['repeat_end_time']).'Z';
						}else
						{
							$line .= ' #0';
						}
					}else
					{
						$line = "RRULE:FREQ=YEARLY;";
						if ($event['repeat_forever'] == '0')
						{
							$line .= "UNTIL=".date($this->date_format, $event['repeat_end_time']).";";
						}
						$line .= "INTERVAL=".$event['repeat_every'].";BYMONTH=".date('n', $event['start_time']);
					}
					$lines[]=$line;
				break;
			}
		
			if($event['repeat_type']>0)
			{
				$cal = new calendar();
				if($cal->get_exceptions($event['id']))
				{				
					while($cal->next_record())
					{
							$exception = date($this->datetime_format, $cal->f('time')+$timezone_offset);
							if(!$this->local_times) $exception .= "Z";
							$exceptions[]=$exception;
					}
					$line = 'EXDATE:';
					
					$count=1;
					$exception_count = count($exceptions);
					for($i=0;$i<$exception_count;$i++)
					{
						$line.=$exceptions[$i];
						if($i!=$exception_count-1)
						{
							$line .=';';
						}
						$count++;
						if($count=='3')
						{
							$lines[]=$line;
							$line='  ';
							$count=0;
						}
					}
					if(!empty($line))
					{
						$lines[]=$line;
					}
				}
			}
		}
		
		//alarm
		if($event['reminder']>0)
		{			
			$remind_time = $event['start_time']-$event['reminder'];
			
//			$lines[] = 'AALARM:'.date($this->datetime_format, gmt_to_local_time($remind_time)).';;;'.$event['name'];

			$atime = date($this->datetime_format, $remind_time+$timezone_offset);
			if(!$this->local_times) $atime .= "Z";
			$lines[] = 'AALARM:'.$atime.';;0;'.$event['name'];
			//Nokia crashes on DALARM at event replace
			//$lines[] = 'DALARM:'.date($this->datetime_format, gmt_to_local_time($remind_time)).';;'.$event['name'];
		}

		$lines[]= "DTSTAMP:".date($this->datetime_format, $event['ctime']);
		$lines[]= "X-GO-BGCOLOR:".$event['background'];
		
		if($event['todo']=='1')
		{
			$lines[] = "END:VTODO";
		}else
		{
			$lines[] = "END:VEVENT";
		}
		
		/*
		//var_dump($lines);
		
		$ical_event='';
		
		foreach ($lines as $line) {
			$line_parts = array();			

			while(strlen($line)>80)
			{
				$test = substr($line,0,80);
				
				for($i=strlen($test)-1;$i>=0;$i--)
				{						
					$char=$test[$i];
					if($char==' ' || $char==';' || $char==':')
					{						
						$line_parts[] = substr($line, 0, $i+1);
						$line = substr($line, $i+1);
						break;
					}
				}
			}
			$line_parts[] = $line;

			for($i=0;$i<count($line_parts);$i++)
			{
				if($i>0)
				{
					$ical_event.= ' ';						
				}		
				 $ical_event .= $line_parts[$i]."\n";
			}
		}
		

		return $ical_event;*/
		
		return implode("\n", $lines)."\n";
	}
}
?>
