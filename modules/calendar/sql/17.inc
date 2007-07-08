<?php
require_once($GO_CONFIG->root_path.'modules/calendar/classes/calendar.class.inc');
$cal = new calendar();
$db1 = new db();
$event=array();
$db1->query("SELECT cal_events.*,users.timezone, users.DST FROM cal_events INNER JOIN users ON cal_events.user_id=users.id WHERE all_day_event='1'");
while($db1->next_record())
{
	if (date('I', $db1->f('start_time')) == 0) {
    $tz = $db1->f('timezone')*3600;
  } else {
    $tz = ($db1->f('timezone')+$db1->f('DST'))*3600;
  }
  
  $event['start_time'] = $db1->f('start_time')+$tz;
  
  
  if (date('I', $db1->f('end_time')) == 0) {
    $tz = $db1->f('timezone')*3600;
  } else {
    $tz = ($db1->f('timezone')+$db1->f('DST'))*3600;
  }
  
  $event['end_time'] = $db1->f('end_time')+$tz;
  
   if (date('I', $db1->f('repeat_end_time')) == 0) {
    $tz = $db1->f('timezone')*3600;
  } else {
    $tz = ($db1->f('timezone')+$db1->f('DST'))*3600;
  }
  
  $event['repeat_end_time'] = $db1->f('repeat_end_time')+$tz;
	
	$event['id'] = $db1->f('id');
	
	$cal->update_event($event);
}

$db1->query("SELECT * FROM cal_views");

while($db1->next_record())
{
	$acl_read = $GO_SECURITY->get_new_acl('view', $db1->f('user_id'));
	$acl_write = $GO_SECURITY->get_new_acl('view', $db1->f('user_id'));
	
	$GO_SECURITY->add_user_to_acl($db1->f('user_id'), $acl_write);
	
	$cal->query("UPDATE cal_views SET acl_read='$acl_read', acl_write='$acl_write' WHERE id=".$db1->f('id'));
}
