<?php
//start session
require('../../Group-Office.php');

foreach($_POST as $key=>$value){
	$GO_CONFIG->save_setting($key, $value);
}
$response['success']=true;

echo json_encode($response);