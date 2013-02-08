<?php
require($GLOBALS['GO_LANGUAGE']->get_language_file('addressbook'));
$GO_SCRIPTS_JS .= 'GO.addressbook.lang.defaultSalutationExpression="'.String::escape_javascript($lang['addressbook']['defaultSalutation']).'";';

//if(isset($GLOBALS['GO_MODULES']->modules['customfields']))
//{
//	
//	require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
//	$cf = new customfields();
//	$GO_SCRIPTS_JS .= $cf->get_javascript(3, $lang['addressbook']['companies']);
//	$GO_SCRIPTS_JS .= $cf->get_javascript(2, $lang['addressbook']['contacts']);
//}


$export_acl_id = $GLOBALS['GO_CONFIG']->get_setting('go_addressbook_export', 0);
if(!$export_acl_id)
{
	$export_acl_id = $GLOBALS['GO_SECURITY']->get_new_acl('addressbook_export');
	$GLOBALS['GO_CONFIG']->save_setting('go_addressbook_export', $export_acl_id, 0);
}
$GO_SCRIPTS_JS .= 'GO.addressbook.export_acl_id="'.$export_acl_id.'";';

$acl_level = $GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $export_acl_id);
$GO_SCRIPTS_JS .= 'GO.addressbook.exportPermission="'.(($acl_level) ? 1 : 0).'";';