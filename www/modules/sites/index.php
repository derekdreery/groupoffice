<?php
require('../../GO.php');

$site_id=isset($_REQUEST['site_id']) ? $_REQUEST['site_id'] : '1';

$site = GO_Sites_Model_Site::model()->findByPk($site_id);

$path = ltrim($_SERVER['REDIRECT_URL'],'/');

$page = GO_Sites_Model_Page::model()->findSingleByAttributes(array('site_id'=>$site_id, 'path'=>$path));

if(!$page){
	echo 'Not found';
	exit();
}

$controller = new $page->controller($site, $page);

$action = 'action'.$page->controller_action;

$controller->$action($_REQUEST);