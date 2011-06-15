<?php
require('../../Group-Office.php');

$response['data']=array();
$response['success']=true;

foreach(GO::modules()->modules as $module) {
	if($lang_file = GO::language()->get_language_file($module['id'])) {
		GO::language()->require_language_file($module['id']);
	}
}

$response['data']['wp_url']=GO::config()->get_setting('wp_url');
//$response['data']['wp_category_2']=GO::config()->get_setting('wp_category_2');
//$response['data']['wp_category_5']=GO::config()->get_setting('wp_category_5');

//otherwise false is displayed
foreach($response['data'] as $key=>$value){
	if(empty($value))
		unset($response['data'][$key]);
}
/*foreach($lang['link_type'] as $id=>$name) {
	$v = GO::config()->get_setting('wp_category_'.$id);
	if($v)
		$response['data']['wp_category_'.$id]=$v;
}*/

echo json_encode($response);