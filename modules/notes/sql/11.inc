<?php
/*
 *	1=cal_events
 * 2=ab_contacts
 * 3=ab_companies
 * 4=no_notes
 * 5=pmProjects
 * 6=folders
 * 7=bs_orders
*/
$db1 = new db();
$db2 = new db();

if(isset($GO_MODULES->modules['projects']))
{
	require_once($GO_MODULES->modules['projects']['class_path'].'projects.class.inc');
	$projects = new projects();
}

if(isset($GO_MODULES->modules['addressbook']))
{
	require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
	$ab = new addressbook();
}

$db1->query("SELECT * FROM no_notes WHERE project_id>0 OR company_id>0 OR contact_id>0");
while($db1->next_record())
{
	$note=array();	
	$note['link_id'] = $db1->f('link_id');
	if(empty($note['link_id']))
	{
		$note['id'] = $db1->f('id');
		$note['link_id'] = $GO_LINKS->get_link_id();
		$db2->update_row('no_notes', 'id', $note);
	}
	
	if($db1->f('project_id') > 0)
	{		
		$remote_link_type=5;		
		
		if($project = $projects->get_project($db1->f('project_id')))
		{	
			if($project['link_id'] < 1)
			{
				$update_project = array();
				$update_project['id'] = $db1->f('project_id');
				$update_project['link_id'] = $project['link_id'] = $GO_LINKS->get_link_id();
				$db2->update_row('pmProjects', 'id', $update_project);
			}
			$remote_link_id = $project['link_id'];			
		}
	}
	
	if($db1->f('contact_id') > 0)
	{		
		$remote_link_type=2;		
		
		if($contact = $ab->get_contact($db1->f('contact_id')))
		{	
			if($contact['link_id'] < 1)
			{
				$update_contact = array();
				$update_contact['id'] = $db1->f('contact_id');
				$update_contact['link_id'] = $contact['link_id'] = $GO_LINKS->get_link_id();
				$db2->update_row('ab_contacts', 'id', $update_contact);
			}
			$remote_link_id = $contact['link_id'];			
		}
	}
	
	if($db1->f('company_id') > 0)
	{		
		$remote_link_type=3;		
		
		if($company = $ab->get_company($db1->f('company_id')))
		{	
			if($company['link_id'] < 1)
			{
				$update_company = array();
				$update_company['id'] = $db1->f('company_id');
				$update_company['link_id'] = $company['link_id'] = $GO_LINKS->get_link_id();
				$db2->update_row('ab_companies', 'id', $update_company);
			}
			$remote_link_id = $company['link_id'];			
		}
	}
	if($remote_link_id >0 && !$GO_LINKS->link_exists($note['link_id'], $remote_link_id))
	{
		$GO_LINKS->add_link($note['link_id'], 4, $remote_link_id, $remote_link_type);	
		echo 'Creating note link to type '.$remote_link_type.'<br />';
	}
}
?>
