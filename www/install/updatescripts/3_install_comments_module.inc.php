<?php
/*
 * Script caused problems with upgrade to 3.3. Manual install required.
 *
 * if(is_dir(GO::config()->module_path.'comments') && !GO::modules()->get_module('comments'))
{		
	$mod = new GO_MODULES();	
	$mod->add_module('comments');
	
	GO::modules()->load_modules();
	
	if(isset(GO::modules()->modules['addressbook']))
	{
		GO::security()->copy_acl(GO::modules()->modules['addressbook']['acl_read'], GO::modules()->modules['comments']['acl_read']);
		GO::security()->copy_acl(GO::modules()->modules['addressbook']['acl_write'], GO::modules()->modules['comments']['acl_write']);
	}
}*/
?>