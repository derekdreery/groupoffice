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

require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
$ab = new addressbook();

require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();

echo 'Creating addressbook links';
if(isset($GO_MODULES->modules['filesystem']))
{
	$db1->query("SELECT * FROM ab_contacts;");
	while($db1->next_record())
	{
		$path = $GO_CONFIG->file_storage_path.'contacts/'.$db1->f('id').'/';
		
		if($share=$fs->find_share($path))
		{
			$update_share['path'] = $share['path'];
			$update_share['link_id'] = $GO_LINKS->get_link_id();
			$db1->update_row('fs_shares','path', $update_share);
			
			if($db1->f('link_id') < 1)
			{
				$update_contact['id'] = $db1->f('id');
				$update_contact['link_id'] = $remote_link_id = $GO_LINKS->get_link_id();
				$db2->update_row('ab_contacts', 'id', $update_contact);
			}else
			{
				$remote_link_id = $db1->f('link_id');
			}
			
			$GO_LINKS->add_link($update_share['link_id'], 6, $remote_link_id, 2);		
		}	
		echo '.';
	}

	$db1->query("SELECT * FROM ab_companies;");
	while($db1->next_record())
	{
		$path = $GO_CONFIG->file_storage_path.'companies/'.$db1->f('id').'/';
		
		if($share=$fs->find_share($path))
		{
			$update_share['path'] = $share['path'];
			$update_share['link_id'] = $GO_LINKS->get_link_id();
			$db1->update_row('fs_shares','path', $update_share);
			
			if($db1->f('link_id') < 1)
			{
				$update_company['id'] = $db1->f('id');
				$update_company['link_id'] = $remote_link_id = $GO_LINKS->get_link_id();
				$db2->update_row('ab_companies', 'id', $update_company);
			}else
			{
				$remote_link_id = $db1->f('link_id');
			}		
			$GO_LINKS->add_link($update_share['link_id'], 6, $remote_link_id, 3);		
		}	
		echo '.';
	}
}
echo 'done<br />';
