<?php
require('Group-Office.php');

$icon = $GO_CONFIG->theme_url.$GO_THEME->theme.'/images/groupoffice.ico';
if(!file_exists($icon))
	$icon = $GO_CONFIG->theme_url.'Default/images/groupoffice.ico';

$style = $GO_CONFIG->theme_url.$GO_THEME->theme.'/reminder.css';
if(!file_exists($style))
	$style = $GO_CONFIG->theme_url.'Default/reminder.css';

$title = $_REQUEST['count']==1 ? $lang['common']['oneReminder'] : sprintf($lang['common']['nReminders'], $_REQUEST['count']);

?>
<html>
	<head>
		<link href="<?php echo $style; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $icon; ?>?" rel="shortcut icon" type="image/x-icon" />
		<title><?php echo $title; ?></title>		
	</head>
	<body>

		<div id="reminderText"><?php echo sprintf($lang['common']['youHaveReminders'], $title, $GO_CONFIG->product_name); ?></div>

	</body>
</html>