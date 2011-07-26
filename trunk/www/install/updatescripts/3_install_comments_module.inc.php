<?php
/*
 * Script caused problems with upgrade to 3.3. Manual install required.
 *
 * if(is_dir($GLOBALS['GO_CONFIG']->module_path.'comments') && !$GLOBALS['GO_MODULES']->get_module('comments'))
{		
	$mod = new GO_MODULES();	
	$mod->add_module('comments');
	
	$GLOBALS['GO_MODULES']->load_modules();
	
	if(isset($GLOBALS['GO_MODULES']->modules['addressbook']))
	{
		$GLOBALS['GO_SECURITY']->copy_acl($GLOBALS['GO_MODULES']->modules['addressbook']['acl_read'], $GLOBALS['GO_MODULES']->modules['comments']['acl_read']);
		$GLOBALS['GO_SECURITY']->copy_acl($GLOBALS['GO_MODULES']->modules['addressbook']['acl_write'], $GLOBALS['GO_MODULES']->modules['comments']['acl_write']);
	}
}*/
?>