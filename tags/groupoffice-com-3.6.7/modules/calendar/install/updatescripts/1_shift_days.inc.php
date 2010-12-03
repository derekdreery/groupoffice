<?php

require_once($GO_CONFIG->class_path.'ical2array.class.inc');
require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc.php');
$cal = new calendar();
$cal1 = new db();
$cal2 = new db();

$cal1->query("SELECT * FROM cal_events WHERE rrule LIKE 'RRULE:FREQ=WEEKLY%' OR rrule LIKE 'RRULE:FREQ=MONTHLY%'");

while($event = $cal1->next_record())
{
	$rrule = ical2array::parse_rrule($event['rrule']);
	
	if($rrule)
	{	
		if($rrule['FREQ']=='MONTHLY')
		{
			$rrule['BYDAY'] = substr($rrule['BYDAY'], 1);
		}		
		$days = Date::byday_to_days($rrule['BYDAY']);
		$days = Date::shift_days_to_gmt($days, date('G', $event['start_time']), Date::get_timezone_offset($event['start_time']));
		
		$jevent = $cal->event_to_json_response($event);
		
		$update_event['id']=$event['id'];
		$update_event['rrule'] = Date::build_rrule($jevent['repeat_type'], $jevent['repeat_every'], $event['repeat_end_time'], $days, $jevent['month_time']);
		
		$cal2->update_row('cal_events', 'id', $update_event);
	}
}

