<?php

require('../../Group-Office.php');

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('tools');

ini_set('max_exection_time','360');

function is_duplicate_contact($record)
{
	$db = new db();
	
	$record = array_map('addslashes', $record);
	
	$sql = "SELECT id FROM ab_contacts WHERE ".
		"addressbook_id='".$record['addressbook_id']."' AND ".
		"first_name='".$record['first_name']."' AND ".
		"middle_name='".$record['middle_name']."' AND ".
		"last_name='".$record['last_name']."' AND ".
		"email='".$record['email']."'";
		
	$db->query($sql);
	if($db->num_rows()>1)
	{
		return true;
	}
	return false;
}


require_once($GO_THEME->theme_path."header.inc");

$db = new db();

$sql = "SELECT *
	FROM `ab_contacts`
	ORDER BY mtime DESC";
	
$db->query($sql);

require('../../modules/addressbook/classes/addressbook.class.inc');
$ab = new addressbook();

$counter = 0;
while($db->next_record())
{
	if(is_duplicate_contact($db->Record))
	{
		$ab->delete_contact($db->f('id'));
		$counter++;
	}
}
echo 'Deleted '.$counter.' duplicate contacts';



require('../../modules/calendar/classes/calendar.class.inc');
$cal = new calendar();

function is_duplicate_event($record)
{
	$db = new db();
	
	$record = array_map('addslashes', $record);
	
	$sql = "SELECT id FROM cal_events INNER JOIN cal_events_calendars ON cal_events.id=cal_events_calendars.event_id WHERE ".
		"name='".$record['name']."' AND ".
		"start_time='".$record['start_time']."' AND ".
		"end_time='".$record['end_time']."' AND ".
		"calendar_id='".$record['calendar_id']."' AND ".
		"user_id='".$record['user_id']."'";
		
	$db->query($sql);
	if($db->num_rows()>1)
	{
		return true;
	}
	return false;
}



$sql = "SELECT id, name, start_time, end_time, user_id, calendar_id FROM `cal_events` INNER JOIN cal_events_calendars ON cal_events.id=cal_events_calendars.event_id ORDER BY mtime DESC";

$db->query($sql);

$counter = 0;
while($db->next_record())
{
	if(is_duplicate_event($db->Record))
	{
		$cal->delete_event($db->f('id'));
		$counter++;
	}
}
echo 'Deleted '.$counter.' duplicate events';

load_basic_controls();
$button = new button($cmdClose, 'javascript:document.location=\'index.php\';');
echo $button->get_html();
require_once($GO_THEME->theme_path."footer.inc");
