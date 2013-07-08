<?php
//require_once('../../../../GO.php');
//GO::session()->runAsRoot();

if(GO::modules()->isInstalled('projects')){
	$fp = GO_Base_Db_FindParams::newInstance()->ignoreAcl();

	$joinCriteria = GO_Base_Db_FindCriteria::newInstance()->addRawCondition('t.acl_id', 'p.acl_id');

	$fp->join('pm_types', $joinCriteria,'p');

	$stmt = GO_Calendar_Model_Calendar::model()->find($fp);

	foreach($stmt as $calendar){

		echo "Fixing ".$calendar->name."\n";
		$oldAcl = $calendar->acl;

		$newAcl = $calendar->setNewAcl();
		$calendar->save();

		$oldAcl->copyPermissions($newAcl);
	}
}