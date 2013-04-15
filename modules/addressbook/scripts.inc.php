<?php

$GO_SCRIPTS_JS .= 'GO.addressbook.lang.defaultSalutationExpression="'.GO_Base_Util_String::escape_javascript(GO::t('defaultSalutation','addressbook')).'";';


$export_acl_id = GO::config()->get_setting('go_addressbook_export', 0);
if(!$export_acl_id)
{
	$acl = new GO_Base_Model_Acl();
	$acl->description='addressbook_export';
	$acl->save();
	
	$export_acl_id = $acl->id;
	GO::config()->save_setting('go_addressbook_export', $acl->id, 0);
}
$GO_SCRIPTS_JS .= 'GO.addressbook.export_acl_id="'.$export_acl_id.'";';

$acl_level = GO_Base_Model_Acl::getUserPermissionLevel($export_acl_id, GO::user()->id);
$GO_SCRIPTS_JS .= 'GO.addressbook.exportPermission="'.(($acl_level) ? 1 : 0).'";';