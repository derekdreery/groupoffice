<?php
global $GO_MODULES, $GO_SECURITY, $GO_CONFIG;
if(isset($GO_MODULES->modules['addressbook'])){
	if(!isset($GO_MODULES->modules['mailings'])){
		$GO_MODULES->add_module('mailings');

		$GO_SECURITY->copy_acl($GO_MODULES->modules['addressbook']['acl_id'], $GO_MODULES->modules['mailings']['acl_id']);
	}

	if(is_dir($GO_CONFIG->module_path.'documenttemplates')){
		$GO_MODULES->add_module('documenttemplates');
		$GO_MODULES->add_module('savemailas');
		
		$GO_SECURITY->copy_acl($GO_MODULES->modules['mailings']['acl_id'], $GO_MODULES->modules['documenttemplates']['acl_id']);
		$GO_SECURITY->copy_acl($GO_MODULES->modules['mailings']['acl_id'], $GO_MODULES->modules['savemailas']['acl_id']);
	}
}