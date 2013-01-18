<?php
require('Group-Office.php');

$GLOBALS['GO_SECURITY']->html_authenticate();

header('Content-Type: text/html; charset=UTF-8');

require_once($GLOBALS['GO_CONFIG']->class_path.'base/theme.class.inc.php');
$GO_THEME = new GO_THEME();

////////////////////////////////
// checking shortcut icon
////////////////////////////////
$icon = $GLOBALS['GO_THEME']->theme_path.'images/groupoffice.ico';
if(!file_exists($icon))
	$icon = $GLOBALS['GO_CONFIG']->theme_url.'Default/images/groupoffice.ico';
else
	$icon = $GLOBALS['GO_THEME']->theme_url.'images/groupoffice.ico';
////////////////////////////////

////////////////////////////////
// checking reminder CSS
////////////////////////////////
$style = $GLOBALS['GO_THEME']->theme_path.'/reminder.css';
if(!file_exists($style))
	$style = $GLOBALS['GO_CONFIG']->theme_url.'Default/reminder.css';
else
	$style = $GLOBALS['GO_THEME']->theme_url.'reminder.css';
////////////////////////////////

$title=$lang['common']['alert'].' - '.$GLOBALS['GO_CONFIG']->product_name;

$count = empty($_REQUEST['count']) ? 0 : intval($_REQUEST['count']);

$reminders= $count==1 ? $lang['common']['oneReminder'] : sprintf($lang['common']['nReminders'], $count);

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
		if($count>0)
			echo '<p>'.sprintf($lang['common']['youHaveReminders'], $reminders, $GLOBALS['GO_CONFIG']->product_name).'</p>';

		echo strip_tags($_REQUEST['reminder_text'],'<p><div>');
		?>
		</div>

	</body>
</html>
