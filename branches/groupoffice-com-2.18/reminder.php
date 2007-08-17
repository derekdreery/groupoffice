<?php
/**
 * @copyright Intermesh 2006
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.00 $ $Date: 2006/12/05 11:37:30 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

//Initialize Group-Office framework
require_once('Group-Office.php');

//Load commonly used controls
load_basic_controls();

//Authenticate the user for the framework
$GO_SECURITY->authenticate();

require($GO_CONFIG->class_path.'base/reminder.class.inc');
$rm = new reminder();

//Declare variables
$reminder_id = isset($_REQUEST['reminder_id']) ? smart_addslashes($_REQUEST['reminder_id']) : 0;
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$return_to = isset ($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];


//Handle save of a reminder
if ($task=='save')
{
	$reminder['name']=smart_addslashes(trim($_POST['name']));
	$reminder['time']=smart_addslashes(local_to_gmt_time(date_to_unixtime(trim($_POST['time']).' '.$_POST['start_hour'].':'.$_POST['start_min'])));
	$reminder['user_id']=$GO_SECURITY->user_id;
	$reminder['url']=smart_addslashes($_POST['url']);
	$reminder['link_id']=smart_addslashes($_POST['link_id']);

	if (empty($reminder['name']))
	{
		$feedback = $error_missing_field;
	}else
	{
		if ($reminder_id>0)
		{
			$reminder['id'] = $reminder_id;
			if (!$rm->update_reminder($reminder))
			{
				$feedback = $strSaveError;
			}
		}else
		{

			$reminder_id=$rm->add_reminder($reminder);
			if(!$reminder_id)
			{
				$feedback = $strSaveError;
			}
		}
	}
	if(!isset($feedback) && $_POST['close'] == 'true')
	{
		header('Location: '.$return_to);
		exit();
	}

}


//This URL links back to this page
$link_back = $_SERVER['PHP_SELF'].'?reminder_id='.$reminder_id.'&return_to='.urlencode($return_to);


$form = new form('reminder_form');
$form->add_html_element(new input('hidden', 'task', '', false));
$form->add_html_element(new input('hidden', 'reminder_id', $reminder_id, false));
$form->add_html_element(new input('hidden','close', 'false', false));
$form->add_html_element(new input('hidden', 'return_to',$return_to));
$form->add_html_element(new input('hidden', 'link_back',$link_back));

$form->add_html_element(new input('hidden', 'url',$_REQUEST['url']));
$form->add_html_element(new input('hidden', 'link_id',$_REQUEST['link_id']));

if ($reminder_id > 0)
{
	$reminder = $helpdesk->get_reminder($reminder_id);
}else
{
	$reminder['name']=isset($_REQUEST['name']) ? smart_stripslashes(trim($_REQUEST['name']))  : '';
	
	$date = getdate();
	
	$default_time = local_to_gmt_time(mktime($date['hours']+1,0,0,$date['mon'], $date['mday'], $date['year']));
	$reminder['time']=isset($reminder['time']) ? $reminder['time']  : $default_time;

}

//Create tabstrip control
$tabstrip = new tabstrip('reminder_tabstrip', $strReminder);
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to($return_to);


//If there's feedback display it
if (isset($feedback))
{
	$p = new html_element('p', $feedback);
	$p->set_attribute('class','Error');
	$tabstrip->add_html_element($p);
}

//Display the active tab content
switch($tabstrip->get_active_tab_id())
{
	default:
		
		load_control('date_picker');
		$GO_HEADER['head'] = date_picker::get_header();
		

		$table = new table();
		$row = new table_row();
		$row->add_cell(new table_cell($strName.':'));
		$input = new input('text','name', $reminder['name']);
		$input->set_attribute('maxlength','50');
		$input->set_attribute('style', 'width:300px;');
		$row->add_cell(new table_cell($input->get_html()));
		$table->add_row($row);
		
		$row = new table_row();
		$row->add_cell(new table_cell($strStartTime.':'));
		
		$subtable= new table();
		$subtable->set_attribute('cellpadding','0');
		$subtable->set_attribute('cellspacing','0');
		$subrow= new table_row();
		
		
		
		$local_time = gmt_to_local_time($reminder['time']);
		$start_hour = date('G', $local_time);
		$start_min = date('i', $local_time);
		
		$datepicker = new date_picker('time', $_SESSION['GO_SESSION']['date_format'], date($_SESSION['GO_SESSION']['date_format'], $local_time));
		$subrow->add_cell(new table_cell($datepicker->get_html()));
			
		$select_hour = new select("start_hour", $start_hour);
		for ($i = 0; $i < 24; $i ++) {
			$select_hour->add_value($i, str_replace(':00', '', date($_SESSION['GO_SESSION']['time_format'], mktime($i, 0, 0))));
		}
	
		$mins = array ("00", "15", "30", "45");
		
		$select_min = new select('start_min', $start_min);		
		$select_min->add_arrays($mins, $mins);	
		$subrow->add_cell(new table_cell($select_hour->get_html().'&nbsp;:&nbsp;'.$select_min->get_html()));

		$subtable->add_row($subrow);
		
		$row->add_cell(new table_cell($subtable->get_html()));
		$table->add_row($row);


		$tabstrip->add_html_element($table);
		$tabstrip->add_html_element(new button($cmdOk, "javascript:dotask('save','true');"));
		$tabstrip->add_html_element(new button($cmdApply, "javascript:dotask('save','false');"));
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='$return_to';"));
		break;
}

//Output header form and footer


$GO_HEADER['body_arguments'] = 'onload="document.reminder_form.name.focus();"';
require_once($GO_THEME->theme_path.'header.inc');
$form->add_html_element($tabstrip);
echo $form->get_html();
?>
<script reminder="text/javascript">
function dotask(task, close)
{
	document.reminder_form.task.value=task;
	document.reminder_form.close.value=close;
	document.reminder_form.submit();	
}
</script>
<?php
require_once($GO_THEME->theme_path.'footer.inc');
