<?php
if(is_dir(GO::config()->module_path.'mailings'))
{
	$module=array();
	$module['version']='0';
	$module['id']='mailings';
	$module['sort_order'] = count(GO::modules()->modules)+1;

	GO::modules()->load_modules();

	if(isset(GO::modules()->modules['users']['acl_id'])){
		$module['acl_id']=GO::security()->get_new_acl();
		GO::security()->copy_acl(GO::modules()->modules['addressbook']['acl_id'], $module['acl_id']);
	}else
	{
		$module['acl_read']=GO::security()->get_new_acl();
		$module['acl_write']=GO::security()->get_new_acl();

		GO::security()->copy_acl(GO::modules()->modules['addressbook']['acl_read'], $module['acl_read']);
		GO::security()->copy_acl(GO::modules()->modules['addressbook']['acl_write'], $module['acl_write']);
	}
	
	
	
	$db->insert_row('go_modules', $module);

	GO::modules()->load_modules();

	$RERUN_UPDATE=true;

}
?>