<?php
require_once($GO_CONFIG->root_path.'modules/calendar/classes/calendar.class.inc');
$cal = new calendar();
$db1 = new db();

$event = array();

$db1->query("SELECT cal_events.*,users.timezone, users.DST FROM cal_events INNER JOIN users ON cal_events.user_id=users.id WHERE all_day_event='1'");
while($db1->next_record())
{
	if (date('I', $db1->f('start_time')) == 0) {
    $tz = $db1->f('timezone')*3600;
  } else {
    $tz = ($db1->f('timezone')+$db1->f('DST'))*3600;
  }
  
  $event['start_time'] = $db1->f('start_time')-$tz;
  
  if($db1->f('timezone') > 0)
  {
		$event['mon'] = $db1->f('tue')== '1' ? '1' : '0';
		$event['tue'] = $db1->f('wed') == '1' ? '1' : '0';
		$event['wed'] = $db1->f('thu') == '1' ? '1' : '0';
		$event['thu'] = $db1->f('fri') == '1' ? '1' : '0';
		$event['fri'] = $db1->f('sat') == '1' ? '1' : '0';
		$event['sat'] = $db1->f('sun') == '1' ? '1' : '0';
		$event['sun'] = $db1->f('mon') == '1' ? '1' : '0';
	}elseif($db1->f('timezone')<0)
	{
		$event['mon'] = $db1->f('sun') == '1' ? '1' : '0';
		$event['tue'] = $db1->f('mon') == '1' ? '1' : '0';
		$event['wed'] = $db1->f('tue') == '1' ? '1' : '0';
		$event['thu'] = $db1->f('wed') == '1' ? '1' : '0';
		$event['fri'] = $db1->f('thu') == '1' ? '1' : '0';
		$event['sat'] = $db1->f('fri') == '1' ? '1' : '0';
		$event['sun'] = $db1->f('sat') == '1' ? '1' : '0';
	}
  
  if (date('I', $db1->f('end_time')) == 0) {
    $tz = $db1->f('timezone')*3600;
  } else {
    $tz = ($db1->f('timezone')+$db1->f('DST'))*3600;
  }
  
  $event['end_time'] = $db1->f('end_time')-$tz;
  
   if (date('I', $db1->f('repeat_end_time')) == 0) {
    $tz = $db1->f('timezone')*3600;
  } else {
    $tz = ($db1->f('timezone')+$db1->f('DST'))*3600;
  }
  
  $event['repeat_end_time'] = $db1->f('repeat_end_time')-$tz;
	
	$event['id'] = $db1->f('id');
	echo 'Updating event '.$db1->f('name').' with id: '.$event['id'].'<br />';
	$cal->update_event($event);
}
