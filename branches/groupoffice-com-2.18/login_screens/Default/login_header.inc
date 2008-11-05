<?php
$charset = isset($charset) ? $charset : 'UTF-8';
header('Content-Type: text/html; charset='.$charset);

if(file_exists($GO_CONFIG->root_path.'LICENSE.PRO'))
{
	$img = $GO_CONFIG->host.'login_screens/'.$GO_CONFIG->login_screen.'/GOPRO.gif';
	$link = 'Intermesh Group-Office Professional';
}else {
	$img = $GO_CONFIG->host.'login_screens/'.$GO_CONFIG->login_screen.'/GOCOM.gif';
	$link = 'Intermesh Group-Office Community';
}
?>
<html>
<head>
<?php if (isset($GO_HEADER['head'])) echo $GO_HEADER['head']; ?>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset; ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GO_CONFIG->host; ?>login_screens/<?php echo $GO_CONFIG->login_screen; ?>/login.css" />
<script language="javascript" type="text/javascript" src="<?php echo $GO_CONFIG->host; ?>javascript/common.js"></script>
<link rel="shortcut icon" href="<?php echo $GO_CONFIG->host; ?>lib/favicon.ico" />
<title><?php echo $GO_CONFIG->title; ?></title>
<?php
if($GO_CONFIG->force_fullscreen && utf8_basename($_SERVER['PHP_SELF']) != 'fullscreen.php')
{
	echo '<script type="text/javascript">if(window.name != "groupoffice"){openPopup("groupoffice", document.location,"","");document.location="fullscreen.php";}</script>';

}

echo '<script type="text/javascript">
if (self != top)
{
	parent.location=document.location;
}
</script>';
?>
</head>
<body dir="<?php echo $htmldirection; ?>"  onkeypress="executeOnEnter(event, 'document.login_form.submit();')">
<table border="0" width="100%" height="95%" cellpadding="0" cellspacing="0">
<tr>
<td align="center" valign="center">
	<table id="header" border="0">
	<tr>
	<td id="header-logo">
	<a href="http://www.group-office.com" target="_blank" title="Group-Office homepage"><img src="<?php echo $img; ?>" border="0" align="absmiddle" /></a>
	</td>
	</tr>
	<tr>
	<td id="header-border"></td>
	</tr>
	<tr>
	<td style="padding:20px;" bgcolor="white">
