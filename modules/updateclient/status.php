<?php

$GO_INCLUDES[]='../updateclient/classes/updateclient.class.inc';
require('../../Group-Office.php');

load_basic_controls();
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('updateclient');
?>
<html>
<head>
<link href="<?php echo $GO_THEME->theme_url.'css/common.css'; ?>" rel="stylesheet" type="text/css" />
<?php
if(file_exists($GO_CONFIG->file_storage_path.'updateclient/log.txt'))
{
	$logfilechanged=filemtime($GO_CONFIG->file_storage_path.'updateclient/log.txt');
	$file=trim(file_get_contents($GO_CONFIG->file_storage_path.'updateclient/log.txt'));	
	$file = explode("\n", $file);
	if(!isset($_SESSION['GO_SESSION']['updateclient']['status']) || $file[count($file)-1]!='exited')
	{
		$_SESSION['GO_SESSION']['updateclient']['status']=$file[count($file)-1];
		echo '<meta http-equiv="refresh" content="2;url='.$_SERVER['PHP_SELF'].'">';
	}else {
		$_SESSION['GO_SESSION']['updateclient']['status']=$file[count($file)-2];
	}
}else {
	$_SESSION['GO_SESSION']['updateclient']['status']='Stopped';
}
?>
</head>
<body>
<?php



echo $_SESSION['GO_SESSION']['updateclient']['status'];
	
?>

</body>
</html>

