<?php
if(isset(GO::modules()->modules['customfields']))
{
	require(GO::language()->get_language_file('files'));
	require_once(GO::modules()->modules['customfields']['class_path'].'customfields.class.inc.php');
	$cf = new customfields();
	$GO_SCRIPTS_JS .= $cf->get_javascript(6, $lang['files']['files']);
}
?>
