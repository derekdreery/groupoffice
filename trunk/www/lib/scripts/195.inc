<?php
/*
This script will update the databse fopr version 1.96
*/
echo 'Updating ACL Table...';

$acl_tables[] = 'ab_addressbooks';
$acl_tables[] = 'cal_calendars';
$acl_tables[] = 'cal_events';
$acl_tables[] = 'cms_sites';
$acl_tables[] = 'cms_templates';
$acl_tables[] = 'fsShares';
$acl_tables[] = 'pmProjects';
//$acl_tables[] = 'pr_presentations';
//$acl_tables[] = 'pr_templates';
//$acl_tables[] = 'tp_templates';

require_once($GO_CONFIG->class_path.'email.class.inc');
$db = new db();
$db2 = new db();

if (!ini_set('max_execution_time', '3600'))
{
	die('Aborted: Could not set max_execution_time');
}

$db->query("SELECT id FROM acl_items");

while($db->next_record())
{
	for ($i=0;$i<count($acl_tables);$i++)
	{
		$db2->query("SELECT user_id FROM ".$acl_tables[$i]." WHERE acl_read='".$db->f('id')."' OR  acl_write='".$db->f('id')."'");
		if ($db2->next_record())
		{
			$user_id=$db2->f('user_id');
			if ($user_id > 0)
			{
				$sql = "UPDATE acl_items SET user_id='$user_id' WHERE id='".$db->f('id')."'";
				echo 'Executing:' .$sql.'<br />';
				$db2->query($sql);
				break;
			}
		}
	}
	if ($user_id == 0)
	{
		$db2->query("SELECT id FROM users WHERE acl_id='".$db->f('id')."'");
		if ($db2->next_record())
		{
			$user_id=$db2->f('id');
			if ($user_id > 0)
			{
				$sql = "UPDATE acl_items SET user_id='$user_id' WHERE id='".$db->f('id')."'";
				echo 'Executing:' .$sql.'<br />';
				$db2->query($sql);
			}
		}
	}
}
echo 'Done<br />Encrypting passwords...';

$email = new email();
$email2 = new email();

$email->query("SELECT password, id FROM emAccounts");

while($email->next_record())
{
	$email2->query("SELECT password FROM users WHERE id='".$email->f('user_id')."'");
	if ($email2->next_record())
	{
		$_SESSION['GO_SESSION']['password'] = $email2->f('password');
		$email2->query("UPDATE emAccounts SET password='".$GO_CRYPTO->encrypt($email->f('password'))."' WHERE id='".$email->f('id')."'");
	}
}

$db->query("UPDATE users SET password=md5(password);");

/*
echo 'Done<br />Converting addressbook....';

$db->query("SELECT id, addressbook_id FROM ab_contacts");

while($db->next_record())
{
	$addressbook_id = $db->f('addressbook_id');
	$contact_id = $db->f('id');

	$acl_read = $GO_SECURITY->get_new_acl('contact read');
	$acl_write = $GO_SECURITY->get_new_acl('contact write');

	if($db2->query("UPDATE ab_contacts SET acl_read='$acl_read', acl_write='$acl_write' WHERE id='$contact_id'"))
	{
		$db2->query("SELECT acl_read, acl_write FROM ab_addressbooks WHERE id='$addressbook_id'");
		if ($db2->next_record())
		{
			$addressbook = $db2->Record;
			$GO_SECURITY->copy_acl($addressbook['acl_read'], $acl_read);
			$GO_SECURITY->copy_acl($addressbook['acl_write'], $acl_write);
		}
	}
}*/
echo 'All Done<br />';
?>
