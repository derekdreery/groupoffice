<?php
global $GO_MODULES, $GO_SECURITY, $GO_CONFIG;
if(isset(GO::modules()->modules['addressbook'])){
	if(!isset(GO::modules()->modules['mailings'])){
		GO::modules()->add_module('mailings');

		GO::security()->copy_acl(GO::modules()->modules['addressbook']['acl_id'], GO::modules()->modules['mailings']['acl_id']);
	}

	if(is_dir(GO::config()->module_path.'documenttemplates')){
		GO::modules()->add_module('documenttemplates');
		GO::modules()->add_module('savemailas');
		
		GO::security()->copy_acl(GO::modules()->modules['mailings']['acl_id'], GO::modules()->modules['documenttemplates']['acl_id']);
		GO::security()->copy_acl(GO::modules()->modules['mailings']['acl_id'], GO::modules()->modules['savemailas']['acl_id']);
	}
}