<?php
//require_once('../../../../GO.php');
//
//GO::session()->runAsRoot();

$fp = GO_Base_Db_FindParams::newInstance()->ignoreAcl();
$fp->getCriteria()->addCondition('project_id', 0, '>');

$stmt = GO_Calendar_Model_Calendar::model()->find($fp);

foreach($stmt as $calendar){
	
	echo "Fixing ".$calendar->name."\n";
	$oldAcl = $calendar->acl;
	
	$newAcl = $calendar->setNewAcl();
	$calendar->save();
	
	$oldAcl->copyPermissions($newAcl);
}