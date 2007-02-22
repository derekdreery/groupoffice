<?php
$db2 = new db();

if($GO_MODULES->get_module('addressbook'))
{
	$tables[]='ab_contacts';	
	$tables[]='ab_companies';
}
if($GO_MODULES->get_module('calendar'))
{
	$tables[]='cal_events';	
}
if($GO_MODULES->get_module('projects'))
{
	$tables[]='pmProjects';	
}
if($GO_MODULES->get_module('notes'))
{
	$tables[]='no_notes';	
}
if($GO_MODULES->get_module('users'))
{
	$tables[]='users';	
}

if($GO_MODULES->get_module('billing'))
{
	$tables[]='bs_orders';	
}

if($GO_MODULES->get_module('shipping'))
{
	$tables[]='sh_jobs';	
	$tables[]='sh_packages';	
}

foreach($tables as $table)
{
	$sql = "SELECT id FROM $table WHERE link_id=0 OR ISNULL(link_id)";
	$db->query($sql);
	
	while($db->next_record())
	{
		$ud['id']=$db->f('id');
		$ud['link_id']=$GO_LINKS->get_link_id();
		
		$db2->update_row($table,'id', $ud);
	}
}