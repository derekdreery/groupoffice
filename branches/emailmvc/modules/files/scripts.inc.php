<?php
if(isset($GLOBALS['GO_MODULES']->modules['customfields']))
{
	require($GLOBALS['GO_LANGUAGE']->get_language_file('files'));
	require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
	$cf = new customfields();
	$GO_SCRIPTS_JS .= $cf->get_javascript(6, $lang['files']['files']);
}
?>
