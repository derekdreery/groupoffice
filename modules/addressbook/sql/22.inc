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
//var_dump($GO_MODULES->modules);
require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
$ab = new addressbook();

if(file_exists($GO_MODULES->modules['addressbook']['path'].'templates/classes/templates.class.inc'))
{
	require($GO_MODULES->modules['addressbook']['path'].'templates/classes/templates.class.inc');
	$tp = new templates();

	$sql = "SELECT ab_groups.*, ab_addressbooks.user_id FROM ab_groups INNER JOIN ab_addressbooks ON ab_addressbooks.id=ab_groups.addressbook_id";
	$db1->query($sql);
	while($db1->next_record())
	{
		$name = addslashes($db1->f('name'));
		$i=0;
		while($tp->get_mailing_group_by_name($name))
		{
			$i++;
			$name = 	addslashes($db1->f('name')).' ('.$i.')';
		}
		echo 'Adding mailing group with name '.$name.'<br />';
		$acl_read=$GO_SECURITY->get_new_acl('', $db1->f('user_id'));
		$acl_write=$GO_SECURITY->get_new_acl('', $db1->f('user_id'));
		$GO_SECURITY->add_user_to_acl($db1->f('user_id'), $acl_write);
		
		if($mailing_group_id = $tp->add_mailing_group($db1->f('user_id'), $name, $acl_read, $acl_write))
		{
			$sql = "SELECT id FROM ab_contacts WHERE group_id='".$db1->f('id')."'";
			$db2->query($sql);
			while($db2->next_record())
			{
				 $tp->add_contact_to_mailing_group($db2->f('id'), $mailing_group_id);
			}
		}
	}
}
