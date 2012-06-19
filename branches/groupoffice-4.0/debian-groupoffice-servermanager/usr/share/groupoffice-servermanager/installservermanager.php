#!/usr/bin/php
<?php
require('/etc/groupoffice/config.php');
require($config['root_path'].'GO.php');

GO::setIgnoreAclPermissions();

try{	
	if(!GO::modules()->isInstalled('servermanager')){
		$module = new GO_Base_Model_Module();
		$module->id = 'servermanager';
		$module->save();
	}	
}
catch(Exception $e){
	echo 'ERROR: '.$e->getMessage();
}

