<?php

require('../../Group-Office.php');

$doreal=true;
$verbose=true;

$GO_SECURITY->html_authenticate('tools');

ini_set('max_exection_time','360');

function is_duplicate_contact($record)
{
	$db = new db();
		
	$sql = "SELECT id FROM ab_contacts WHERE ".
		"addressbook_id='".$db->escape($record['addressbook_id'])."' AND ".
		"first_name='".$db->escape($record['first_name'])."' AND ".
		"middle_name='".$db->escape($record['middle_name'])."' AND ".
		"last_name='".$db->escape($record['last_name'])."' AND ".
		"company_id='".$db->escape($record['company_id'])."' AND ".
		"email='".$db->escape($record['email'])."'";
		
	$db->query($sql);
	if($db->num_rows()>1)
	{
		return true;
	}
	return false;
}


$db = new db();

$sql = "SELECT *
	FROM `ab_contacts`
	ORDER BY mtime DESC";
	
$db->query($sql);

require_once('../../modules/addressbook/classes/addressbook.class.inc.php');
$ab = new addressbook();

$counter = 0;
while($db->next_record())
{
	if(is_duplicate_contact($db->record))
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



require_once('../../modules/calendar/classes/calendar.class.inc.php');
$cal = new calendar();

function is_duplicate_event($record)
{
	$db = new db();
	
	$sql = "SELECT DISTINCT id FROM cal_events WHERE ".
		"name='".$db->escape($record['name'])."' AND ".
		"start_time='".$db->escape($record['start_time'])."' AND ".
		"end_time='".$db->escape($record['end_time'])."' AND ".
		"calendar_id='".$db->escape($record['calendar_id'])."' AND ".
		"rrule='".$db->escape($record['rrule'])."' AND ".
		"user_id='".$db->escape($record['user_id'])."' ORDER BY mtime ASC";
		
	$db->query($sql);
	if($db->num_rows()>1)
	{
		$db->next_record();
		return $db->record;
	}
	return false;
}



$sql = "SELECT id, name, start_time, end_time, user_id, calendar_id, rrule ".
	"FROM `cal_events` ".
	" ORDER BY mtime DESC";

$db->query($sql);

$counter = 0;
while($db->next_record())
{
	$duplicate = is_duplicate_event($db->record);
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


