<?php
require_once("../../Group-Office.php");

require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc.php');

$cal = new calendar();
require($GO_LANGUAGE->get_language_file('calendar'));

$calendar_id = isset($_REQUEST['calendar_id']) ? $_REQUEST['calendar_id'] : 0;
$email = isset($_REQUEST['email']) ? ($_REQUEST['email']) : "";
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : "";
$event_id = isset($_REQUEST['event_id']) ? ($_REQUEST['event_id']) : 0;
$event_exists = isset($_REQUEST['import']) ? $_REQUEST['import'] : 0;

$user = $GO_USERS->get_user_by_email($email);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
require($GO_THEME->theme_path.'default_head.inc.php');
?>
</head>
<body>

<div style="padding:20px">

<?php
$event = $cal->get_event($event_id);
$owner = $GO_USERS->get_user($event['user_id']);
if(!$event = $cal->get_event($event_id))
{
	echo '<h1 class="cal-go-title">'.$GO_CONFIG->title.'</h1>';
	echo '<p>'.$lang['calendar']['bad_event'].'</p>';
}elseif(!$user || $task == 'decline')
{
	if($task=='accept')
	{
		$cal->set_event_status($event_id, '1', $email);
		echo '<h1 class="cal-go-title">'.$GO_CONFIG->title.'</h1>';
		echo '<h1>'.$lang['calendar']['accept_title'].'</h1>';
		echo '<p>'.$lang['calendar']['accept_confirm'].'</p>';

		require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
		$swift = new GoSwift($owner['email'],  sprintf($lang['calendar']['accept_mail_subject'],$event['name']));
		$swift->set_from($GO_CONFIG->webmaster_email, $GO_CONFIG->title);
		$body = sprintf($lang['calendar']['accept_mail_body'],$email);
		$body .= '<br /><br />'.$cal->event_to_html($event);

		$swift->set_body($body);

	}else
	{
		$cal->set_event_status($event_id, '2', $email);

		echo '<h1 class="cal-go-title">'.$GO_CONFIG->title.'</h1>';
		echo '<h1>'.$lang['calendar']['decline_title'].'</h1>';
		echo '<p>'.$lang['calendar']['decline_confirm'].'</p>';

		require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
		$swift = new GoSwift($owner['email'], sprintf($lang['calendar']['decline_mail_subject'],$event['name']));
		$swift->set_from($GO_CONFIG->webmaster_email, $GO_CONFIG->title);

		$body = sprintf($lang['calendar']['decline_mail_body'],$email);
		$body .= '<br /><br />'.$cal->event_to_html($event);


	}

	$swift->set_body($body);
	$swift->sendmail();
	//$user = $GO_USERS->get_user($event['user_id']);

}else
{
	$status = $cal->get_event_status($event['id'], $email);
	if($status['status']=='1')
	{
		echo '<h1 class="cal-go-title">'.$GO_CONFIG->title.'</h1>';
		echo '<h1>'.$lang['calendar']['accept_title'].'</h1>';
		echo '<p>'.$lang['calendar']['already_accepted'].'</p>';
	}else
	{
		require($GO_CONFIG->root_path.'default_scripts.inc.php');

		echo '<script src="language/en.js" type="text/javascript"></script>';

		if($GO_LANGUAGE->language!='en' && file_exists($GO_MODULES->modules['calendar']['path'].'language/'.$GO_LANGUAGE->language.'.js'))
		{
			echo '<script src="language/'.$GO_LANGUAGE->language.'.js" type="text/javascript"></script>';
		}

		echo '<script src="SelectCalendarWindow.js" type="text/javascript"></script><script type="text/javascript">
		Ext.onReady(function(){

			GO.mainLayout.fireReady();
			selectCalendarWin = new SelectCalendarWindow();
			selectCalendarWin.show('.$event_id.','.$event_exists.');
		});
		</script>';
	}
}
?>
</div>
</body>
</html>