<?php
require('../../Group-Office.php');
$charset= isset($charset) ? $charset : 'UTF-8';
$htmldirection= isset($htmldirection) ? $htmldirection : 'ltr';
header('Content-Type: text/html; charset='.$charset);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset; ?>">
<link href="<?php echo $GO_THEME->theme_url.'css/common.css'; ?>" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="<?php echo $GO_CONFIG->host; ?>javascript/common.js"></script>
<?php require($GO_CONFIG->control_path.'fixpng.inc'); ?>
</head>
<body style="padding:0px;margin:0px;" dir="<?php echo $htmldirection; ?>">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="20">
<tr>
	<td colspan="0" class="FooterBar">

		<?php
		echo '&nbsp;Group-Office '.$GO_CONFIG->version;
		if ($GO_SECURITY->logged_in())
			echo ' - '.$strLoggedInAs.htmlspecialchars($_SESSION['GO_SESSION']['name']).'</td><td align="right" class="FooterBar">';
		echo date($_SESSION['GO_SESSION']['date_format'], get_time()).'&nbsp;';
		?>
		</td>
</tr>
</table>
</body>
</html>
