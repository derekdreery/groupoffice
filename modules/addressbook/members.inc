<?php
/**
 * Copyright Intermesh 2005
 *  Author: Merijn Schering <mschering@intermesh.nl>
 *  Version: 1.1 Release date: 27 June 2005
 *
 *  This program is free software; you can redistribute it and/or modify it
 *  under the terms of the GNU General Public License as published by the
 *  Free Software Foundation; either version 2 of the License, or (at your
 *  option) any later version.
 */

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	if ($addressbook = $ab->get_addressbook($_POST['addressbook_id']))
	{
		if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_write']))
		{
			foreach($datatable->selected as $add_user_id)
			{
				if (!$ab->user_is_in_addressbook($add_user_id, $_POST['addressbook_id']))
				{
					$contact = $GO_USERS->get_user($add_user_id);				
					$contact = array_map('addslashes', $contact);			
					$contact['company_id'] = $ab->get_company_id_by_name($contact['company'], $_POST['addressbook_id']);
					
					unset($contact['company'], 
					$contact['username'], 
					$contact['password'],
					$contact['authcode'],
					$contact['homepage'],
					$contact['thousands_seperator'],
					$contact['decimal_seperator'],
					$contact['lastlogin'],
					$contact['registration_time'],
					$contact['max_rows_list'],
					$contact['timezone'],
					$contact['DST'],
					$contact['first_weekday'],
					$contact['sort_name'],
					$contact['start_module'],
					$contact['logins'],
					$contact['work_address'],
					$contact['work_zip'],
					$contact['work_state'],
					$contact['work_country'],
					$contact['work_city'],
					$contact['acl_id'],
					$contact['date_format'],
					$contact['time_format'],
					$contact['theme'],
					$contact['language'],
					$contact['currency'],
					$contact['mail_client'],				
					$contact['enabled'],
					$contact['use_checkbox_select'],
					$contact['country_id'],
					$contact['work_country_id'],
					$contact['date_seperator']
					);
					
								
					$contact['addressbook_id'] = smart_addslashes($_POST['addressbook_id']);

									
					$contact['source_id'] = $add_user_id;
					$ab->add_contact($contact);
				}
			}
		}else
		{
			$feedback = $strAccessDenied;
		}
	}
}

/*
$table = new table();
$table->set_attribute('style','width:100%');
$row = new table_row();
$cell = new table_cell();
$cell->set_attribute('style', 'text-align:right;width:100%;');

if ($ab->get_user_addressbooks($GO_SECURITY->user_id) > 1)
{

	$subscribed_addressbooks = new select('addressbook_id', $addressbook_id);
	$subscribed_addressbooks->set_attribute('onchange','javascript:document.forms[0].submit();');
	
	while ($ab->next_record())
	{
		$subscribed_addressbooks->add_value($ab->f('id'), $ab->f('name'));
	}
	$cell->add_html_element($subscribed_addressbooks);
}else
{
	$form->add_html_element(new input('hidden', 'addressbook_id',$addressbook_id));
}


	



$row->add_cell($cell);
$table->add_row($row);

$form->add_html_element($table);
*/

$datatable = new datatable('members');
$datatable->add_column(new table_heading($strName, 'name'));
$datatable->add_column(new table_heading($strEmail, 'email'));
$datatable->add_column(new table_heading($strCompany, 'company'));
$datatable->add_column(new table_heading('&nbsp;'));

$count = $GO_USERS->get_authorized_users($GO_SECURITY->user_id, $datatable->sort_index, $datatable->sql_sort_order);

while ($GO_USERS->next_record())
{
	$mail_name = format_name($GO_USERS->f('last_name'), $GO_USERS->f('first_name'), $GO_USERS->f('middle_name'), 'first_name');
	$full_email = ($GO_USERS->f("email") != "") ? '"'.$mail_name.'" <'.$GO_USERS->f('email').'>' : '';
	$name = format_name($GO_USERS->f('last_name'), $GO_USERS->f('first_name'), $GO_USERS->f('middle_name'));
	
	$row = new table_row($GO_USERS->f("id"));
	
	$row->set_attribute('ondblclick', 
		'document.location=\''.$GO_CONFIG->control_url.'user.php?id='.
		$GO_USERS->f('id').'&return_to='.
		urlencode($link_back).'\'');
	
	$row->add_cell(new table_cell(htmlspecialchars($name)));
	$row->add_cell(new table_cell(mail_to(empty_to_stripe(addslashes($full_email)), $GO_USERS->f("email"))));
	$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($GO_USERS->f("company")))));
	
	$img = new image('save');
	$img->set_attribute('style','border:0px;');
	
	$hyperlink = new hyperlink("contact.php?user_id=".$GO_USERS->f("id")."&return_to=".urlencode($link_back),$img->get_html(),$contacts_add_member);
	
	$row->add_cell(new table_cell($hyperlink->get_html()));
	$datatable->add_row($row);
}

if($count > 0)
{
	$cell = new table_cell($count.' '.$contacts_members);
}else
{
	$cell = new table_cell($count.' '.$contacts_no_contacts);
}
$cell->set_attribute('class','small');
$cell->set_attribute('colspan','99');
$datatable->add_footer($cell);

if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $form->add_html_element($p);
}

//$form->add_html_element($datatable);
