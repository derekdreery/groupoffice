<?php
if(GO_Base_Util_Http::isAjaxRequest()){
	echo json_encode($data);
}elseif(PHP_SAPI=='cli'){
	echo "ERROR: ".trim($data['feedback'])."\n\n";
	if(GO::config()->debug)
		echo $data['trace']."\n\n";
}else
{
	echo '<h1>Error</h1>';
	echo '<p style="color:red">'.$data['feedback'].'</p>';
	if(GO::config()->debug)
		echo '<pre>'.$data['trace'].'</pre>';
}