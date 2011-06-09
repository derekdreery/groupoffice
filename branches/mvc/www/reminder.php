<?php
require('Group-Office.php');

GO::security()->html_authenticate();

header('Content-Type: text/html; charset=UTF-8');

require_once(GO::config()->class_path.'base/theme.class.inc.php');
$GO_THEME = new GO_THEME();

////////////////////////////////
// checking shortcut icon
////////////////////////////////
$icon = GO::theme()->theme_path.'images/groupoffice.ico';
if(!file_exists($icon))
	$icon = GO::config()->theme_url.'Default/images/groupoffice.ico';
else
	$icon = GO::theme()->theme_url.'images/groupoffice.ico';
////////////////////////////////

////////////////////////////////
// checking reminder CSS
////////////////////////////////
$style = GO::theme()->theme_path.'/reminder.css';
if(!file_exists($style))
	$style = GO::config()->theme_url.'Default/reminder.css';
else
	$style = GO::theme()->theme_url.'reminder.css';
////////////////////////////////

$title=$lang['common']['alert'].' - '.GO::config()->product_name;

$reminders= $_REQUEST['count']==1 ? $lang['common']['oneReminder'] : sprintf($lang['common']['nReminders'], $_REQUEST['count']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
		<link href="<?php echo $style; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $icon; ?>" rel="shortcut icon" type="image/x-icon" />
		<title><?php echo $title; ?></title>
	</head>
	<body>

		<div id="reminderText">
		<?php
		if($_REQUEST['count']>0)
			echo '<p>'.sprintf($lang['common']['youHaveReminders'], $reminders, GO::config()->product_name).'</p>';

		echo $_REQUEST['reminder_text'];
		?>
		</div>

	</body>
</html>
