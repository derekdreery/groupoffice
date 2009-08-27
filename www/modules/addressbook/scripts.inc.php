<?php
if(isset($GO_MODULES->modules['customfields']))
{
	require($GO_LANGUAGE->get_language_file('addressbook'));
	require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
	$cf = new customfields();
	$GO_SCRIPTS_JS .= $cf->get_javascript(3, $lang['addressbook']['companies']);
	$GO_SCRIPTS_JS .= $cf->get_javascript(2, $lang['addressbook']['contacts']);
}
