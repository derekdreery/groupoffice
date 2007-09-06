<?php
/**
* @copyright Intermesh 2007
* @author Merijn Schering <mschering@intermesh.nl>
* @version $Revision: 1.13 $ $Date: 2006/10/20 12:36:43 $3
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */


require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('calendar');

require_once ($GO_MODULES->class_path."calendar.class.inc");
require_once ($GO_LANGUAGE->get_language_file('calendar'));
$cal = new calendar();

//ini_set('display_errors','off');


//we are unsuccessfull by default
$result =array('success'=>false);

switch($_REQUEST['task'])
{
	case 'update_event':

		$email = new email();
		
		$event['id']=smart_addslashes($_POST['event_id']);
		
		if(isset($_POST['start_time']))
		{
			$old_event = $cal->get_event($event['id']);
			
			$event['start_time']=strtotime(smart_stripslashes($_POST['start_time']));
			$event['end_time']=$event['start_time']+$old_event['end_time']-$old_event['start_time'];			
		}
		
		if(isset($_POST['end_time']))
		{
			$event['end_time']=strtotime(smart_stripslashes($_POST['end_time']));
		}	
		$result['success']=$cal->update_event($event);
		break;
		
	case 'add_event':
		
		$calendar_id=smart_addslashes($_POST['calendar_id']);
		
		$gridEvent = json_decode($_POST['gridEvent']);
		
		$event['name']=addslashes($gridEvent['startDate']);
		$event['start_time']=strtotime($gridEvent['startDate']);
		$event['end_time']=strtotime($gridEvent['endDate']);
		
		$event_id= $cal->add_event($event);
		
		$cal->subscribe_event($event_id, $calendar_id);
		
		$result['success']=true;
		
		break;
}

echo json_encode($result);