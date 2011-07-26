<?php
global $GO_MODULES, $GO_SECURITY, $GO_CONFIG;
if(isset($GLOBALS['GO_MODULES']->modules['addressbook'])){
	if(!isset($GLOBALS['GO_MODULES']->modules['mailings'])){
		$GLOBALS['GO_MODULES']->add_module('mailings');

		$GLOBALS['GO_SECURITY']->copy_acl($GLOBALS['GO_MODULES']->modules['addressbook']['acl_id'], $GLOBALS['GO_MODULES']->modules['mailings']['acl_id']);
	}

	if(is_dir($GLOBALS['GO_CONFIG']->module_path.'documenttemplates')){
		$GLOBALS['GO_MODULES']->add_module('documenttemplates');
		$GLOBALS['GO_MODULES']->add_module('savemailas');
		
		$GLOBALS['GO_SECURITY']->copy_acl($GLOBALS['GO_MODULES']->modules['mailings']['acl_id'], $GLOBALS['GO_MODULES']->modules['documenttemplates']['acl_id']);
		$GLOBALS['GO_SECURITY']->copy_acl($GLOBALS['GO_MODULES']->modules['mailings']['acl_id'], $GLOBALS['GO_MODULES']->modules['savemailas']['acl_id']);
	}
}