<?php
require('Group-Office.php');

header('Content-Type: text/html; charset=UTF-8');

$GO_THEME->set_theme('ExtJS');

$icon = $GO_THEME->theme_path.'images/groupoffice.ico';
if(!file_exists($icon))
	$icon = $GO_CONFIG->theme_url.'Default/images/groupoffice.ico';
else
	$icon = $GO_THEME->theme_url.'images/groupoffice.ico';

$style = $GO_CONFIG->theme_url.$GO_THEME->theme.'/reminder.css';
if(!file_exists($style))
	$style = $GO_CONFIG->theme_url.'Default/reminder.css';

$title=$lang['common']['alert'].' - '.$GO_CONFIG->product_name;

$reminders= $_REQUEST['count']==1 ? $lang['common']['oneReminder'] : sprintf($lang['common']['nReminders'], $_REQUEST['count']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
		<link href="<?php echo $style; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $icon; ?>?" rel="shortcut icon" type="image/x-icon" />
		<title><?php echo $title; ?></title>		
	</head>
	<body>

		<div id="reminderText">
		<?php
		if($_REQUEST['count']>0)
			echo '<p>'.sprintf($lang['common']['youHaveReminders'], $reminders, $GO_CONFIG->product_name).'</p>';
		
		echo $_REQUEST['reminder_text'];
		?>
		</div>

	</body>
</html>