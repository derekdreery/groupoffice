<?php
require($GO_LANGUAGE->get_language_file('addressbook'));
$GO_SCRIPTS_JS .= 'GO.addressbook.lang.defaultSalutationExpression="'.String::escape_javascript($lang['addressbook']['defaultSalutation']).'";';

if(isset($GO_MODULES->modules['customfields']))
{
	
	require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
	$cf = new customfields();
	$GO_SCRIPTS_JS .= $cf->get_javascript(3, $lang['addressbook']['companies']);
	$GO_SCRIPTS_JS .= $cf->get_javascript(2, $lang['addressbook']['contacts']);
}
