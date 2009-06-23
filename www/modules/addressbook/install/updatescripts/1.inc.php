<?php
if(is_dir($GO_CONFIG->module_path.'mailings'))
{
	$module['version']='0';
	$module['id']='mailings';
	$module['sort_order'] = count($GO_MODULES->modules)+1;
	$module['acl_read']=$GO_SECURITY->get_new_acl();
	$module['acl_write']=$GO_SECURITY->get_new_acl();
	
	$GO_SECURITY->copy_acl($GO_MODULES->modules['addressbook']['acl_read'], $module['acl_read']);
	$GO_SECURITY->copy_acl($GO_MODULES->modules['addressbook']['acl_write'], $module['acl_write']);
	
	$db->insert_row('go_modules', $module);

	$GO_MODULES->load_modules();

	$RERUN_UPDATE=true;

}
?>