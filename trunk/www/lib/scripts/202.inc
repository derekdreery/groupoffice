<?php
/*
This script will update the databse fopr version 2.03
*/
#require_once($GO_CONFIG->root_path.'email.class.inc');
$db = new db();
$db2 = new db();

$db->Halt_On_Error = 'no';

$db->query("SELECT ab_contacts.id, ab_addressbooks.user_id FROM ab_contacts INNER JOIN ab_addressbooks ON ab_contacts.addressbook_id=ab_addressbooks.id");

while($db->next_record())
{
	$user_id = $db->f('user_id');
	$contact_id = $db->f('id');

	$db2->query("UPDATE ab_contacts SET user_id='$user_id' WHERE id='$contact_id'");
}

$db->query("SELECT ab_companies.id, ab_addressbooks.user_id FROM ab_companies INNER JOIN ab_addressbooks ON ab_companies.addressbook_id=ab_addressbooks.id");

while($db->next_record())
{
	$user_id = $db->f('user_id');
	$company_id = $db->f('id');

	$db2->query("UPDATE ab_companies SET user_id='$user_id' WHERE id='$company_id'");
}

echo 'All Done<br />';
