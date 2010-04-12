<?php
header('Content-Type: text/html; charset=UTF-8');

require('Group-Office.php');

$module = isset($_REQUEST['module']) ? $_REQUEST['module'] : 'email';
$function = isset($_REQUEST['function']) ? $_REQUEST['function'] : 'showComposer';
$params = isset($_REQUEST['params']) ? ($_REQUEST['params']) : '';

//echo base64_decode($params);
//exit();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Group-Office</title>
<script type="text/javascript">
function launchGO(){
	var win = window.open('', "groupoffice");

	if(win.GO && win.GO.<?php echo $module; ?>)
	{
		win.GO.<?php echo $module; ?>.<?php echo $function; ?>.apply(this, <?php echo base64_decode($params); ?>);
	}else
	{
		win.location.href="<?php echo $GO_CONFIG->host; ?>?<?php echo $_SERVER['QUERY_STRING']; ?>";
	}
	//win.focus();
  self.close();
}
</script>
</head>

<body onload="launchGO();" style="font:12px arial">
<h1>Group-Office</h1>
<?php
echo str_replace('{FUNCTION}', $module.'.'.$function.'()', $lang['common']['goAlreadyStarted']);
?>
</body>

</html>