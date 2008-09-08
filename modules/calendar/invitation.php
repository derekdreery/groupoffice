<?php
require_once("../../Group-Office.php");

require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');

$cal = new calendar();
require_once($GO_LANGUAGE->get_language_file('calendar'));

$calendar_id = isset($_REQUEST['calendar_id']) ? $_REQUEST['calendar_id'] : 0;
$email = isset($_REQUEST['email']) ? smart_addslashes($_REQUEST['email']) : "";
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : "";
$event_id = isset($_REQUEST['event_id']) ? smart_addslashes($_REQUEST['event_id']) : 0;


$user = $GO_USERS->get_user_by_email($email);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
require($GO_CONFIG->root_path.'default_head.inc');
require($GO_CONFIG->root_path.'default_scripts.inc');
$load_modules=isset($_REQUEST['load_modules']) ? explode(',', $_REQUEST['load_modules']) : array();

$load_modules[]='calendar';

foreach($load_modules as $module_id)
{
	$module = $GO_MODULES->modules[$module_id];
	
	echo '<script type="text/javascript" src="'.$module['url'].'language/en.js"></script>';
	echo "\n";
	
	if(file_exists($module['path'].'language/'.$GO_LANGUAGE->language.'.js'))
	{
		echo '<script type="text/javascript" src="'.$module['url'].'language/'.$GO_LANGUAGE->language.'.js"></script>';
		echo "\n";
	}
	
	if(file_exists($module['path'].'scripts.txt') && $GO_CONFIG->debug)
	{					
		$data = file_get_contents($module['path'].'scripts.txt');
		$lines = explode("\n", $data);
		foreach($lines as $line)
		{
			if(!empty($line))
			{
				echo '<script type="text/javascript" src="'.$GO_CONFIG->host.$line.'"></script>';
				echo "\n";
			}
		}
	}else if(file_exists($module['path'].'all-module-scripts-min.js'))
	{
		echo '<script type="text/javascript" src="'.$module['url'].'all-module-scripts-min.js"></script>';
		echo "\n";
	}
		
	if(file_exists($module['path'].'scripts.inc.php'))
	{
		require($module['path'].'scripts.inc.php');
	}	
}
?>
<script src="SelectCalendarWindow.js" type="text/javascript"></script>
</head>
<body>

<?php
$event = $cal->get_event($event_id);
$owner = $GO_USERS->get_user($event['user_id']);
if(!$event = $cal->get_event($event_id))
{
	echo '<p>'.$lang['calendar']['bad_event'].'</p>';
}elseif(!$user || $task == 'decline')
{
	if($task=='accept')
	{
		$cal->set_event_status($event_id, '1', $email);
		echo '<h1>'.$lang['calendar']['accept_title'].'</h1>';
		echo '<p>'.$lang['calendar']['accept_confirm'].'</p>';
		
		
			
		require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
		$swift = new GoSwift($owner['email'],  sprintf($lang['calendar']['accept_mail_subject'],$event['name']));
		
		$body = sprintf($lang['calendar']['accept_mail_body'],$email);		
		$body .= '<br /><br />'.$cal->event_to_html($event);
		
		$swift->set_body($body);
		$swift->sendmail($GO_CONFIG->webmaster_email, $GO_CONFIG->title);
		
	}else
	{
		$cal->set_event_status($event_id, '2', $email);
		echo '<h1>'.$lang['calendar']['decline_title'].'</h1>';
		echo '<p>'.$lang['calendar']['decline_confirm'].'</p>';
		
		require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
		$swift = new GoSwift($owner['email'], sprintf($lang['calendar']['decline_mail_subject'],$event['name']));
		
		$body = sprintf($lang['calendar']['decline_mail_body'],$email);		
		$body .= '<br /><br />'.$cal->event_to_html($event);
		
		$swift->set_body($body);
		$swift->sendmail($GO_CONFIG->webmaster_email, $GO_CONFIG->title);
		
	}
		
	$user = $GO_USERS->get_user($event['user_id']);

}else
{
	echo '<script type="text/javascript">
	Ext.onReady(function(){

		GO.mainLayout.fireReady();
		selectCalendarWin = new SelectCalendarWindow();
		selectCalendarWin.show('.$event_id.');
	});
	</script>';

}
?>

</body>
</html>