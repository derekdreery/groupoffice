<?php
require_once($GO_CONFIG->root_path.'modules/calendar/classes/calendar.class.inc');
$cal = new calendar();
$db1 = new db();

$db1->query("SELECT * FROM cal_reminders");
while($db1->next_record())
{
	$sql = 'UPDATE cal_reminders SET occurence_time='.$cal->get_next_recurrence_time($db1->f('event_id')).' WHERE user_id='.$db1->f('user_id').' AND event_id='.$db1->f('event_id');
	$cal->query($sql);
}
