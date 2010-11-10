<?php
global $GO_MODULES;

$db2 = new db();
$db = new db();

if(isset($GO_MODULES->modules['calendar'])){

	$db->query("SELECT id,uuid FROM cal_events");

	while($r=$db->next_record()){
		$r['uuid']=File::strip_extension($r['uuid']);

		$db2->update_row('cal_events','id', $r);
	}
}

if(isset($GO_MODULES->modules['tasks'])){

	$db->query("SELECT id,uuid FROM ta_tasks");

	while($r=$db->next_record()){
		$r['uuid']=File::strip_extension($r['uuid']);

		$db2->update_row('ta_tasks','id', $r);
	}
}