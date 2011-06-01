<?php
if(is_dir(GO::config()->module_path.'gota') && !isset(GO::modules()->modules['gota']))
{
	$module=array();
	$module['id']='gota';
	$module['sort_order'] = count(GO::modules()->modules)+1;
	if(isset(GO::modules()->modules['users']['acl_id'])){
		$module['acl_id']=GO::security()->get_new_acl();
		GO::security()->copy_acl(GO::modules()->modules['files']['acl_id'], $module['acl_id']);
	}else
	{
		$module['acl_read']=GO::security()->get_new_acl();
		$module['acl_write']=GO::security()->get_new_acl();

		GO::security()->copy_acl(GO::modules()->modules['files']['acl_read'], $module['acl_read']);
		GO::security()->copy_acl(GO::modules()->modules['files']['acl_write'], $module['acl_write']);
	}
	
	$db->insert_row('go_modules', $module);
	
	$mod = new GO_MODULES();	
	$mod->load_modules();
}
?>