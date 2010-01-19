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

class export_tasks
{
	var $calendar_properties = '';
	var $events = array();
	var $datetime_format = 'Ymd\THis';

	const date_format = 'Ymd';

	var $timezone_id = '';

	var $version;

	var $line_break="\n";

	var $utc=false;

	var $add_leading_space_to_qp_encoded_line_wraps=false;

	function __construct($version='2.0',$utc=false)
	{

		$this->version = $version;
		$this->utc=$utc;


		$this->tasklist_properties =
			"VERSION:".$version.$this->line_break.
			"PRODID:-//Intermesh//NONSGML Group-Office//EN".$this->line_break.
			"CALSCALE:GREGORIAN".$this->line_break.
			"METHOD:PUBLISH".$this->line_break;
	}

	function export_tasklist_header()
	{
		$str= "BEGIN:VCALENDAR".$this->line_break.
		$this->tasklist_properties;
		return $str;
	}

	function export_tasklist_footer()
	{
		return "END:VCALENDAR".$this->line_break;
	}


	function export_task($task, $no_rrule=false)
	{
		$ics = $this->export_tasklist_header();
		$ics .= $this->convert_task($task, $no_rrule);
		$ics .= $this->export_tasklist_footer();

		return $ics;
	}

	function export_tasklist($tasklist_id)
	{
		$ics = $this->export_tasklist_header();

		$tasks= new tasks();

		$tasks->get_tasks(array($tasklist_id),0,true,'id','ASC',0,0,true);
		while($record = $tasks->next_record())
		{
			$ics .= $this->convert_task($record);
		}

		$ics .= $this->export_tasklist_footer();
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

	function convert_task($task, $no_rrule=false)
	{
		global $GO_CONFIG, $GO_SECURITY, $charset;

		if($this->timezone_id != '')
		{
			$timezone_offset = Date::get_timezone_offset($task['due_time'])*3600;
		}else
		{
			$timezone_offset = 0;
		}

		$lines = array();

		$lines[] = "BEGIN:VTODO";
		$lines[] = "UID:".$task['id'];

		$lines = array_merge($lines, String::format_vcard_line('SUMMARY', $task['name'], $this->add_leading_space_to_qp_encoded_line_wraps));
		//if ($task['description'] != '')
		//{
			$lines = array_merge($lines, String::format_vcard_line('DESCRIPTION', $task['description'], $this->add_leading_space_to_qp_encoded_line_wraps));
		//}

		$lines[] =	"STATUS:".$task['status'];
			
		/*if($task['private'] == '1')
		 {
			$lines[] ="CLASS:PRIVATE";
			}else
			{
			$lines[] ="CLASS:PUBLIC";
			}*/

		if($this->version == '1.0')
		{
			//$line = "DTSTART:".date($this->datetime_format, $task['due_time']);
			//	$lines[] = $line;

			$due_date = $this->utc ? gmdate($this->datetime_format, $task['due_time']+60).'Z' : date($this->datetime_format, $task['due_time']+60);

			$line = "DUE:";
			//was 59 before nexthaus
			$line .= $due_date;
			$lines[] = $line;
				
			if($task['completion_time']>0)
			{
				$compl_date = $this->utc ? gmdate($this->datetime_format, $task['completion_time']).'Z' : date($this->datetime_format, $task['completion_time']);


				$line = "COMPLETED:".$compl_date;
				$lines[] = $line;
			}

		}else
		{
			$DT_format = export_tasks::date_format;



			$line = "DTSTART;VALUE=DATE";

			$start_date = $this->utc ? gmdate($DT_format , $task['start_time']).'Z' : date($DT_format , $task['start_time']);

			if($this->timezone_id != '')
			{
				$line .= ";TZID=".$start_date;
			}else
			{
				$line .= ":".$start_date;

			}
			$lines[]=$line;

			$line = "DUE;VALUE=DATE";

			$due_date = $this->utc ? gmdate($DT_format , $task['due_time']).'Z' : date($DT_format , $task['due_time']);


			if($this->timezone_id != '')
			{
				$line .= ";TZID=".$this->timezone_id.":".$due_date;
			}else
			{
				$line .= ":".$due_date;
			}
			$lines[]=$line;
				
			if($task['completion_time']>0)
			{
				$compl_date = $this->utc ? gmdate($DT_format , $task['completion_time']).'Z' : date($DT_format , $task['completion_time']);


				$line = "COMPLETED;VALUE=DATE";
				if($this->timezone_id != '')
				{
					$line .= ";TZID=".$this->timezone_id.":".$compl_date;
				}else
				{
					$line .= ":".$compl_date;
				}
				$lines[]=$line;
			}
		}


		if(!empty($task['rrule']) && !$no_rrule)
		{
			require_once($GO_CONFIG->class_path.'ical2array.class.inc');
			$ical2array = new ical2array();

			if($this->version != '1.0')
			{					
				$rrule = $ical2array->parse_rrule($task['rrule']);

				if(!$this->utc && isset($rrule['BYDAY'])){
					if($rrule['FREQ']=='MONTHLY'){
						$month_time = $rrule['BYDAY'][0];
						$day = substr($rrule['BYDAY'], 1);
						$days =Date::byday_to_days($day);
					}else
					{
						$month_time = 1;
						$days = Date::byday_to_days($rrule['BYDAY']);
					}
					$days = Date::shift_days_to_local($days, date('G', $task['start_time']), Date::get_timezone_offset($task['start_time']));


					$lines[]=Date::build_rrule(Date::ical_freq_to_repeat_type($rrule), $rrule['INTERVAL'], $task['repeat_end_time'], $days, $month_time);
				}else
				{
					$lines[]=$task['rrule'];
				}
			}else
			{
				$rrule = $ical2array->parse_rrule($task['rrule']);

				if (isset($rrule['UNTIL']))
				{
					if($task['repeat_end_time'] = $ical2array->parse_date($rrule['UNTIL']))
					{
						$task['repeat_forever']='0';
						$task['repeat_end_time'] = mktime(0,0,0, date('n', $task['repeat_end_time']), date('j', $task['repeat_end_time'])+1, date('Y', $task['repeat_end_time']));
					}else
					{
						$task['repeat_forever'] = 1;
					}
				}elseif(isset($rrule['COUNT']))
				{
					//figure out end time later when task data is complete
					$task['repeat_forever'] = 1;
					$task_count = intval($rrule['COUNT']);
					if($task_count==0)
					{
						unset($task_count);
					}
				}else
				{
					$task['repeat_forever'] = 1;
				}

				$task['repeat_every']=$rrule['INTERVAL'];

				if(isset($rrule['BYDAY']))
				{
					$days = Date::byday_to_days($rrule['BYDAY']);
					$task = Date::shift_days_to_local($days, date('G', $task['due_time']), Date::get_timezone_offset($task['due_time']));
				}

				switch($rrule['FREQ'])
				{
					case 'DAILY':
						$line = 'RRULE:D'.$task['repeat_every'];
						if ($task['repeat_forever'] == '0')
						{
							//$line .= ' '.date($this->datetime_format, $task['repeat_end_time']-86400).'Z';
							$line .= ' '.date($this->datetime_format, $task['repeat_end_time']).'Z';
						}else
						{
							$line .= ' #0';
						}
						$lines[]=$line;
						break;

					case 'WEEKLY':
							
						$task_days = array();

						if ($days['sun'] == '1')
						{
							$task_days[] = "SU";
						}
						if ($days['mon'] == '1')
						{
							$task_days[] = "MO";
						}
						if ($days['tue'] == '1')
						{
							$task_days[] = "TU";
						}
						if ($days['wed'] == '1')
						{
							$task_days[] = "WE";
						}
						if ($days['thu'] == '1')
						{
							$task_days[] = "TH";
						}
						if ($days['fri'] == '1')
						{
							$task_days[] = "FR";
						}
						if ($days['sat'] == '1')
						{
							$task_days[] = "SA";
						}

							
						$line = 'RRULE:W'.$task['repeat_every'].' ';
						$line .= implode(' ', $task_days);
						if ($task['repeat_forever'] == '0')
						{
							$line .= ' '.date($this->datetime_format, $task['repeat_end_time']).'Z';
						}else
						{
							$line .= ' #0';
						}
						$lines[]=$line;
						break;

					case 'MONTHLY':
						if (!isset($rrule['BYDAY']))
						{
							$line = 'RRULE:MD'.$task['repeat_every'].' '.date('j', $task['due_time']).'+';
							//$line = 'RRULE:MD'.$task['repeat_every'].' ';

							if ($task['repeat_forever'] == '0')
							{
								$line .= ' '.date($this->datetime_format, $task['repeat_end_time']).'Z';
							}else
							{
								$line .= ' #0';
							}
						}else
						{
							$task_days = array();

							if ($days['sun'] == '1')
							{
								$task_days[] ="SU";
							}
							if ($days['mon'] == '1')
							{
								$task_days[] = "MO";
							}
							if ($days['tue'] == '1')
							{
								$task_days[] = "TU";
							}
							if ($days['wed'] == '1')
							{
								$task_days[] = "WE";
							}
							if ($days['thu'] == '1')
							{
								$task_days[] = "TH";
							}
							if ($days['fri'] == '1')
							{
								$task_days[] = "FR";
							}
							if ($days['sat'] == '1')
							{
								$task_days[] = "SA";
							}


							$line = 'RRULE:MP'.$task['repeat_every'].' '.$task['month_time'].'+ '.$task_days[0];
							if ($task['repeat_forever'] == '0')
							{
								$line .= ' '.date($this->datetime_format, $task['repeat_end_time']).'Z';
							}else
							{
								$line .= ' #0';
							}
						}

						$lines[]=$line;
						break;

					case 'YEARLY':

						//$line = 'RRULE:YM'.$task['repeat_every'].' '.date('n',$task['due_time']);
						$line = 'RRULE:YM'.$task['repeat_every'];
						if ($task['repeat_forever'] == '0')
						{
							$line .= ' '.date($this->datetime_format, $task['repeat_end_time']).'Z';
						}else
						{
							$line .= ' #0';
						}
							
						$lines[]=$line;
						break;
				}
			}
		}


		//alarm
		if($task['reminder']>0)
		{
			$atime = date($this->datetime_format, $task['reminder']);
			$lines[] = 'AALARM:'.$atime.';;0;'.$task['name'];
			//Nokia crashes on DALARM at task replace
			//$lines[] = 'DALARM:'.date($this->datetime_format, Date::gmt_to_local_time($remind_time)).';;'.$task['name'];
		}

		$lines[]= "DTSTAMP:".date($this->datetime_format, $task['ctime']);
		$lines[] = "END:VTODO";

		//return implode("\n", $lines)."\n";
		
		/*$vtodo = '';
		foreach ($lines as $line) {
		 preg_match_all( '/.{1,73}([^=]{0,2})?/', $line, $matches);
		 $vtodo .= implode( '=' . chr(13).chr(10).' ', $matches[0] )."\r\n"; // add soft crlf's
		}*/
		return implode("\r\n", $lines)."\r\n";
	}


}
