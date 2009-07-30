<?php
header('Content-Type: text/html; charset=UTF-8');

require('../../Group-Office.php');


$qs=strtolower(str_replace('mailto:','', $_SERVER['QUERY_STRING']));
$qs=str_replace('?subject','&subject', $qs);

parse_str($qs, $vars);
//var_dump($vars);

$vars['to']=isset($vars['mail_to']) ? $vars['mail_to'] : '';
unset($vars['mail_to']);
	
if(!isset($vars['subject']))
	$vars['subject']='';
	
if(!isset($vars['body']))
	$vars['body']='';

$js = json_encode($vars);
?>
<html>
<head>
<title>Group-Office</title>
<script>
function launchGO(){
	var win = window.open('', "groupoffice");

	if(win.GO && win.GO.email)
	{
		win.GO.email.showComposer({
			values: <?php echo $js; ?>
		});
		
	}else
	{		
		win.location.href="<?php echo $GO_CONFIG->host; ?>?<?php echo $_SERVER['QUERY_STRING']; ?>";
	}	
	win.focus();	
}
</script>
</head>

<body onload="launchGO();" style="font:12px arial">
<h1>Group-Office</h1>
<?php 
require($GO_LANGUAGE->get_language_file('email'));
echo $lang['email']['goAlreadyStarted']; ?>
</body>
</html>
