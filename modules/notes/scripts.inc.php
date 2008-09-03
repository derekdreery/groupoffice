<?php
require($GO_LANGUAGE->get_language_file('notes'));

if(isset($GO_MODULES->modules['customfields']))
{
	require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
	$cf = new customfields();
	echo $cf->get_javascript(4, $lang['notes']['notes']);
}