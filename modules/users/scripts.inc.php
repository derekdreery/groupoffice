<?php
if(isset($GO_MODULES->modules['customfields']))
{
	require($GO_LANGUAGE->get_language_file('users'));
	require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
	$cf = new customfields();
	echo $cf->get_javascript(8, $lang['users']['name']);
}