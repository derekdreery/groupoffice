<?php
/*
 *
 * disable because doesn't work well
 * 
if(isset($GO_MODULES->modules['calendar'])){
	require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc.php');
	$cal = new calendar();
	$cal1 = new db();
	$cal2 = new db();

	$db->query("ALTER TABLE `cal_settings` ADD `calendar_id` INT NOT NULL;");

	$cal1->query("SELECT calendar_id, user_id FROM cal_settings");

	while($settings = $cal1->next_record())
	{
		$calendar = $cal->get_calendar($settings['calendar_id'], $settings['user_id']);
		if($calendar){
			$cal2->query('REPLACE INTO su_visible_calendars(calendar_id, user_id) VALUES("'.$calendar['id'].'", "'.$cal1->f('user_id').'")');
		}
	}
}*/