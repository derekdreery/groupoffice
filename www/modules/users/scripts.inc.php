<?php
if(isset(GO::modules()->modules['customfields']))
{
	require(GO::language()->get_language_file('users'));
	require_once(GO::modules()->modules['customfields']['class_path'].'customfields.class.inc.php');
	$cf = new customfields();
	$GO_SCRIPTS_JS .= $cf->get_javascript(8, $lang['users']['name']);
}