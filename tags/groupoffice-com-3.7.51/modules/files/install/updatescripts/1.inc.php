<?php
if(is_dir($GO_CONFIG->module_path.'gota') && !isset($GO_MODULES->modules['gota']))
{
	$module=array();
	$module['id']='gota';
	$module['sort_order'] = count($GO_MODULES->modules)+1;
	if(isset($GO_MODULES->modules['users']['acl_id'])){
		$module['acl_id']=$GO_SECURITY->get_new_acl();
		$GO_SECURITY->copy_acl($GO_MODULES->modules['files']['acl_id'], $module['acl_id']);
	}else
	{
		$module['acl_read']=$GO_SECURITY->get_new_acl();
		$module['acl_write']=$GO_SECURITY->get_new_acl();

		$GO_SECURITY->copy_acl($GO_MODULES->modules['files']['acl_read'], $module['acl_read']);
		$GO_SECURITY->copy_acl($GO_MODULES->modules['files']['acl_write'], $module['acl_write']);
	}
	
	$db->insert_row('go_modules', $module);
	
	$mod = new GO_MODULES();	
	$mod->load_modules();
}
?>