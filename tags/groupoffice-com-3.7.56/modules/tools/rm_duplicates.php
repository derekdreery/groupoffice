<?php

require('../../Group-Office.php');

$delete=!empty($_REQUEST['delete']);
$verbose=true;

$GO_SECURITY->html_authenticate('tools');

session_write_close();

ini_set('max_exection_time','360');

$db = new db();
$db2 =  new db();

require_once('../../modules/addressbook/classes/addressbook.class.inc.php');
$ab = new addressbook();

$check_fields=array('first_name', 'middle_name', 'last_name', 'addressbook_id', 'company_id', 'email');

$sql = "SELECT id, count(*) AS n, ".implode(',', $check_fields)." ".
	"FROM `ab_contacts` GROUP BY ".implode(',', $check_fields)." HAVING n>1";
$db->query($sql);


$counter = 0;

echo '<table border="1">';
echo '<tr><td>ID</th><th>'.implode('</th><th>',$check_fields).'</th></tr>';

while($r1= $db->next_record())
{
	$sql = "SELECT id,".implode(',', $check_fields)." FROM ab_contacts WHERE ";

	$first=true;
	foreach($check_fields as $field){
		if($first){

			$first=false;
		}else
		{
			$sql .= ' AND ';
		}

		$v = $db2->escape($r1[$field]);
		if(!empty($v))
			$sql .= $field."='".$db2->escape($r1[$field])."'";
		else
			$sql .= '('.$field."='' OR ISNULL(".$field."))";
	}

	$sql .= " ORDER BY id ASC";


	$db2->query($sql);

	//skip first
	$r2 = $db2->next_record();

	while($r2 = $db2->next_record()){

		$counter++;

		if($delete)
			$ab->delete_contact($db2->f('id'));
		
		echo '<tr><td>'.implode('&nbsp;</td><td>',$r2).'&nbsp;</td></tr>';
	}
}
echo '</table>';

echo $delete ? 'Deleted' : 'Found';
echo ' '.$counter.' duplicate contacts<br /><hr /><br />';


require_once('../../modules/calendar/classes/calendar.class.inc.php');
$cal = new calendar();


$check_fields=array('name', 'start_time', 'end_time', 'calendar_id', 'rrule', 'user_id');

$sql = "SELECT id, count(*) AS n, ".implode(',', $check_fields)." ".
	"FROM `cal_events` GROUP BY ".implode(',', $check_fields)." HAVING n>1";
$db->query($sql);

$db2 =  new db();
$counter = 0;

echo '<table border="1">';
echo '<tr><td>ID</th><th>'.implode('</th><th>',$check_fields).'</th></tr>';

while($r1= $db->next_record())
{
	$sql = "SELECT id,".implode(',', $check_fields)." FROM cal_events WHERE ";

	$first=true;
	foreach($check_fields as $field){
		if($first){
			
			$first=false;
		}else
		{
			$sql .= ' AND ';
		}

		$sql .= $field.="='".$db2->escape($r1[$field])."'";
	}

	$sql .= " ORDER BY id ASC";


	$db2->query($sql);

	//skip first
	$r2 = $db2->next_record();
	
	while($r2 = $db2->next_record()){

		$counter++;
		
		if($delete)
			$cal->delete_event($db2->f('id'));
			
		
		$r2['start_time']=Date::get_timestamp($r2['start_time']);
		$r2['end_time']=Date::get_timestamp($r2['end_time']);
		echo '<tr><td>'.implode('&nbsp;</td><td>',$r2).'&nbsp;</td></tr>';
		
	}
}
echo '</table>';

echo $delete ? 'Deleted' : 'Found';
echo ' '.$counter.' duplicate events<br /><hr /><br />';

if(!$delete)
	echo '<a href="'.$_SERVER['PHP_SELF'].'?delete=true">Click here to delete the newest version of duplicates</a>';


