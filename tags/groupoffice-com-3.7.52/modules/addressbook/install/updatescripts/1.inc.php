<?php
if(is_dir($GO_CONFIG->module_path.'mailings'))
{
	$module=array();
	$module['version']='0';
	$module['id']='mailings';
	$module['sort_order'] = count($GO_MODULES->modules)+1;

	$GO_MODULES->load_modules();

	if(isset($GO_MODULES->modules['users']['acl_id'])){
		$module['acl_id']=$GO_SECURITY->get_new_acl();
		$GO_SECURITY->copy_acl($GO_MODULES->modules['addressbook']['acl_id'], $module['acl_id']);
	}else
	{
		$module['acl_read']=$GO_SECURITY->get_new_acl();
		$module['acl_write']=$GO_SECURITY->get_new_acl();

		$GO_SECURITY->copy_acl($GO_MODULES->modules['addressbook']['acl_read'], $module['acl_read']);
		$GO_SECURITY->copy_acl($GO_MODULES->modules['addressbook']['acl_write'], $module['acl_write']);
	}
	
	
	
	$db->insert_row('go_modules', $module);

	$GO_MODULES->load_modules();

	$RERUN_UPDATE=true;

}
?>