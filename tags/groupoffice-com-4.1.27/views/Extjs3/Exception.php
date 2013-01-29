<?php
if(GO_Base_Util_Http::isAjaxRequest()){
	echo json_encode($data);
}elseif(PHP_SAPI=='cli'){
	echo "ERROR: ".trim($data['feedback'])."\n\n";
	if(GO::config()->debug)
		echo $data['exception']."\n\n";
}else
{
	require("externalHeader.php");
	echo '<h1>'.GO::t('strError').'</h1>';
	echo '<p style="color:red">'.$data['feedback'].'</p>';
	if(GO::config()->debug){
		unset($data['feedback']);
		echo '<h2>Debug info:</h2>';
		echo '<pre>';
		var_dump($data);
		echo '</pre>';
	}
	
	require("externalFooter.php");
}