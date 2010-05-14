<?php
require('Group-Office.php');

$icon = $GO_CONFIG->theme_url.$GO_THEME->theme.'/images/groupoffice.ico';
if(!file_exists($icon))
	$icon = $GO_CONFIG->theme_url.'Default/images/groupoffice.ico';

$style = $GO_CONFIG->theme_url.$GO_THEME->theme.'/reminder.css';
if(!file_exists($style))
	$style = $GO_CONFIG->theme_url.'Default/reminder.css';

$title=$lang['common']['alert'].' - '.$GO_CONFIG->product_name;

$reminders= $_REQUEST['count']==1 ? $lang['common']['oneReminder'] : sprintf($lang['common']['nReminders'], $_REQUEST['count']);

?>
<html>
	<head>
		<link href="<?php echo $style; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $icon; ?>?" rel="shortcut icon" type="image/x-icon" />
		<title><?php echo $title; ?></title>		
	</head>
	<body>

		<div id="reminderText">
		<?php
		if($_REQUEST['count']>1)
			echo '<p>'.sprintf($lang['common']['youHaveReminders'], $reminders, $GO_CONFIG->product_name).'</p>';
		
		echo $_REQUEST['reminder_text'];
		?>
		</div>

	</body>
</html>