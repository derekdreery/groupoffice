<?php
/*
 * Script caused problems with upgrade to 3.3. Manual install required.
 *
 * if(is_dir($GO_CONFIG->module_path.'comments') && !$GO_MODULES->get_module('comments'))
{		
	$mod = new GO_MODULES();	
	$mod->add_module('comments');
	
	$GO_MODULES->load_modules();
	
	if(isset($GO_MODULES->modules['addressbook']))
	{
		$GO_SECURITY->copy_acl($GO_MODULES->modules['addressbook']['acl_read'], $GO_MODULES->modules['comments']['acl_read']);
		$GO_SECURITY->copy_acl($GO_MODULES->modules['addressbook']['acl_write'], $GO_MODULES->modules['comments']['acl_write']);
	}
}*/
?>