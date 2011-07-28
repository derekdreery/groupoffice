<?php
header('Content-Type: text/html; charset=UTF-8');

require('Group-Office.php');

if(!isset($_REQUEST['m']) && isset($_REQUEST['module']))
	$_REQUEST['m']=$_REQUEST['module'];

if(!isset($_REQUEST['e']) && isset($_REQUEST['loadevent']))
	$_REQUEST['e']=$_REQUEST['loadevent'];

if(!isset($_REQUEST['f']) && isset($_REQUEST['function']))
	$_REQUEST['f']=$_REQUEST['function'];

if(!isset($_REQUEST['p']) && isset($_REQUEST['params']))
	$_REQUEST['p']=$_REQUEST['params'];

$module = isset($_REQUEST['m']) && preg_match('/[a-z]+/', $_REQUEST['m']) ? $_REQUEST['m'] : 'email';
$function = isset($_REQUEST['f']) ? $_REQUEST['f'] : 'showComposer';

$params = isset($_REQUEST['p']) ? base64_decode($_REQUEST['p']) : '';

if(strpos($_SERVER['QUERY_STRING'], '<script') || strpos(urldecode($_SERVER['QUERY_STRING']), '<script'))
				die('Invalid request');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?php echo $GO_CONFIG->product_name; ?></title>
<script type="text/javascript">
function launchGO(){
	var win = window.open('', "groupoffice");

	if(win.GO && win.GO.<?php echo $module; ?>)
	{
		win.GO.<?php echo $module; ?>.<?php echo $function; ?>.apply(this, <?php echo $params; ?>);

	}else
	{
		//the parameters will be handled in default_scripts.inc.php
		win.location.href="<?php echo $GO_CONFIG->host; ?>?<?php echo $_SERVER['QUERY_STRING']; ?>";
	}

	self.close();
	//win.focus();

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