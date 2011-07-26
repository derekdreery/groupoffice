<?php
if(is_dir($GLOBALS['GO_CONFIG']->module_path.'gota') && !isset($GLOBALS['GO_MODULES']->modules['gota']))
{
	$module=array();
	$module['id']='gota';
	$module['sort_order'] = count($GLOBALS['GO_MODULES']->modules)+1;
	if(isset($GLOBALS['GO_MODULES']->modules['users']['acl_id'])){
		$module['acl_id']=$GLOBALS['GO_SECURITY']->get_new_acl();
		$GLOBALS['GO_SECURITY']->copy_acl($GLOBALS['GO_MODULES']->modules['files']['acl_id'], $module['acl_id']);
	}else
	{
		$module['acl_read']=$GLOBALS['GO_SECURITY']->get_new_acl();
		$module['acl_write']=$GLOBALS['GO_SECURITY']->get_new_acl();

		$GLOBALS['GO_SECURITY']->copy_acl($GLOBALS['GO_MODULES']->modules['files']['acl_read'], $module['acl_read']);
		$GLOBALS['GO_SECURITY']->copy_acl($GLOBALS['GO_MODULES']->modules['files']['acl_write'], $module['acl_write']);
	}
	
	$db->insert_row('go_modules', $module);
	
	$mod = new GO_MODULES();	
	$mod->load_modules();
}
?>