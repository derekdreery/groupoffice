<?php

require('../../Group-Office.php');

$doreal=true;
$verbose=true;

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
		if($doreal)
		{
			$ab->delete_contact($db->f('id'));
		}
		if($verbose)
		{
			echo 'Deleted contact ID:'.$db->f('id').' '.$db->f('last_name').'<br />';
		}
		$counter++;
	}
}
echo 'Deleted '.$counter.' duplicate contacts<br /><hr /><br />';



require('../../modules/calendar/classes/calendar.class.inc');
$cal = new calendar();

function is_duplicate_event($record)
{
	$db = new db();
	
	$record = array_map('addslashes', $record);
	
	$sql = "SELECT DISTINCT id FROM cal_events INNER JOIN cal_events_calendars ON cal_events.id=cal_events_calendars.event_id WHERE ".
		"name='".$record['name']."' AND ".
		"start_time='".$record['start_time']."' AND ".
		"end_time='".$record['end_time']."' AND ".
		"calendar_id='".$record['calendar_id']."' AND ".
		"repeat_type='".$record['repeat_type']."' AND ".
		"repeat_end_time='".$record['repeat_end_time']."' AND ".	
		"user_id='".$record['user_id']."' ORDER BY mtime ASC";
		
	$db->query($sql);
	if($db->num_rows()>1)
	{
		$db->next_record();
		return $db->Record;
	}
	return false;
}



$sql = "SELECT id, name, start_time, end_time, user_id, calendar_id, repeat_type, repeat_end_time ".
	"FROM `cal_events` INNER JOIN cal_events_calendars ON cal_events.id=cal_events_calendars.event_id ".
	"ORDER BY mtime DESC";

$db->query($sql);

$counter = 0;
while($db->next_record())
{
	$duplicate = is_duplicate_event($db->Record);
	if($duplicate)
	{
		if($doreal)
		{
			$cal->delete_event($db->f('id'));
		}
		if($verbose)
		{
			echo 'Deleted event ID:'.$db->f('id').' calendar ID: '.$db->f('calendar_id').' '.date('Ymd G:i', $db->f('start_time')).' "'.$db->f('name').'" Duplicate was: '.$duplicate['id'].'<br />';
			
		}
		$counter++;
	}
}
echo 'Deleted '.$counter.' duplicate events<br /><hr /><br />';

load_basic_controls();
$button = new button($cmdClose, 'javascript:document.location=\'index.php\';');
echo $button->get_html();
require_once($GO_THEME->theme_path."footer.inc");
