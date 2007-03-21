<?php



$form->add_html_element(new input('hidden', 'clicked_letter'));

if(isset($_REQUEST['search_type']))
{
	$ab_settings['search_type'] = $_REQUEST['search_type'];
	if($_REQUEST['search_type'] != 'user')
	{
		$ab_settings['search_addressbook_id'] = $_REQUEST['search_addressbook_id'];
	}
}

$tabstrip = new tabstrip('search_type', $cmdSearch);
$tabstrip->set_attribute('width','100%');

$tabstrip->add_tab('contact',$contacts_contacts);
$tabstrip->add_tab('company',$ab_companies);
$tabstrip->add_tab('user',$contacts_members);
$tabstrip->set_active_tab($ab_settings['search_type']);

if($tabstrip->submitted)
{
	$datatable->start=0;
}


if(isset($_POST['move_addressbook_id']) && $_POST['move_addressbook_id']>0)
{
	$move_ab = $ab->get_addressbook($_POST['move_addressbook_id']);

	if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $move_ab['acl_write']) &&
	$GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_write']))

	{
		switch($tabstrip->value)
		{
			case 'contact':

				foreach($datatable->selected as $contact_id)
				{
					$ab->move_contact_to_addressbook($contact_id, $_POST['move_addressbook_id']);
				}
				break;

			case 'company':

				foreach($datatable->selected as $company_id)
				{
					$ab->move_company_to_addressbook($company_id, $_POST['move_addressbook_id']);
				}
				break;

		}
	}else
	{
		$feedback = $strAccessDenied;
	}
}




if (isset($feedback))
{
	$p = new html_element('p', $feedback);
	$p->set_attribute('class','Error');
	$tabstrip->add_html_element($p);
}

$h2 = new html_element('h1');
$alphabet = explode(',',$alphabet);
for($i=0;$i<count($alphabet);$i++)
{
	$hyperlink = new hyperlink("javascript:letter_click('".$alphabet[$i]."')",$alphabet[$i]);
	$hyperlink->set_attribute('style', 'margin-right:10px;');
	$h2->add_html_element($hyperlink);
}

$tabstrip->add_html_element($h2);

$table = new table();

$row = new table_row();

$search_fields = new select('search_field');

switch ($ab_settings['search_type'])
{
	case 'company':

		$datatable = new datatable('company_search');

		if($datatable->task == 'delete')
		{

			foreach($datatable->selected as $company_id)
			{
				$company = $ab->get_company($company_id);

				if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $company['acl_write']))
				{
					$ab->delete_company($company_id);
				}else
				{
					$feedback = $strAccessDenied;
					break;
				}
			}
		}
		$datatable->set_attribute('width','100%');
		$menu->add_button('delete_big',$cmdDelete, "javascript:document.addressbook_form.task.value='".$task."';".$datatable->get_delete_handler());
		$datatable->allow_configuration();




		$edit_file='edit_companies.php';
		$menu->add_button('enter_data_big',$ab_edit_selected, "javascript:edit_items();");


		$search_fields->add_value('', $strSearchAll);
		$search_fields->add_value('ab_companies.id', $ab_id);
		$search_fields->add_value('ab_companies.name', $strName);
		$search_fields->add_value('ab_companies.email', $strEmail);
		$search_fields->add_value('ab_companies.address',$strAddress);
		$search_fields->add_value('ab_companies.city', $strCity);
		$search_fields->add_value('ab_companies.zip',$strZip);
		$search_fields->add_value('ab_companies.state',$strState);
		$search_fields->add_value('ab_companies.country', $strCountry);
		if(isset($GO_MODULES->modules['custom_fields']) && $GO_MODULES->modules['custom_fields']['read_permission'])
		{
			require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
			$cf = new custom_fields();
			$cf2 = new custom_fields();

			$cf2->get_categories(3);
			while($cf2->next_record())
			{
				if($cf->get_fields($cf2->f('id')))
				{
					while($cf->next_record())
					{
						$cf_fields[]=$cf->Record;
						$search_fields->add_value('cf_3.col_'.$cf->f('id'), $cf->f('name'));
					}
				}
			}
		}



		$search_field = $ab_settings['search_companies_field'] ;
		if(isset($_REQUEST['search_field']))
		{
			if($search_fields->is_in_select($_REQUEST['search_field']))
			{
				$search_field = $_REQUEST['search_field'];
				if($task != 'show_letter')
				{
					$ab_settings['search_companies_field'] = $_REQUEST['search_field'];
				}
			}
		}
		$search_fields->value=$ab_settings['search_companies_field'];
		break;

	case 'user':

		//$edit_file=$GO_MODULES->modules['users']['url'].'edit_users.php';

		$search_fields->value = $ab_settings['search_users_field'];
		$search_fields->add_value('', $strSearchAll);
		$search_fields->add_value('users.first_name', $strFirstName);
		$search_fields->add_value('users.last_name', $strLastName);
		$search_fields->add_value('users.email', $strEmail);
		$search_fields->add_value('users.department',$strDepartment);
		$search_fields->add_value('users.function',$strFunction);
		$search_fields->add_value('users.address',$strAddress);
		$search_fields->add_value('users.city', $strCity);
		$search_fields->add_value('users.zip',$strZip);
		$search_fields->add_value('users.state',$strState);
		$search_fields->add_value('users.country', $strCountry);
		$search_fields->add_value('users.company', $strCompany);
		if(isset($GO_MODULES->modules['custom_fields']) && $GO_MODULES->modules['custom_fields']['read_permission'])
		{
			require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
			$cf = new custom_fields();
			$cf2 = new custom_fields();

			$cf2->get_categories(8);
			while($cf2->next_record())
			{
				if($cf->get_fields($cf2->f('id')))
				{
					$cf_fields[]=$cf->Record;
					while($cf->next_record())
					{
						$search_fields->add_value('cf_8.col_'.$cf->f('id'), $cf->f('name'));
					}
				}
			}
		}

		$search_field = $ab_settings['search_users_field'] ;
		if(isset($_REQUEST['search_field']))
		{
			if($search_fields->is_in_select($_REQUEST['search_field']))
			{
				$search_field = $_REQUEST['search_field'];
				$ab_settings['search_users_field'] = $_REQUEST['search_field'];
			}
		}
		$search_fields->value=$ab_settings['search_users_field'];
		break;

	default:

		$datatable = new datatable('contact_search');

		if($datatable->task == 'delete')
		{
			foreach($datatable->selected as $contact_id)
			{
				$contact = $ab->get_contact($contact_id);

				if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $contact['acl_write']))
				{
					$ab->delete_contact($contact_id);
				}else
				{
					$feedback = $strAccessDenied;
					break;
				}

			}
		}
		$datatable->set_attribute('width','100%');
		$menu->add_button('delete_big',$cmdDelete, "javascript:document.addressbook_form.task.value='".$task."';".$datatable->get_delete_handler());
		$datatable->allow_configuration();

		$edit_file='edit_contacts.php';
		$menu->add_button('enter_data_big',$ab_edit_selected, "javascript:edit_items();");


		$search_fields->add_value('', $strSearchAll);
		$search_fields->add_value('ab_contacts.id', $ab_id);
		$search_fields->add_value('ab_contacts.first_name', $strFirstName);
		$search_fields->add_value('ab_contacts.last_name', $strLastName);
		$search_fields->add_value('ab_contacts.email', $strEmail);
		$search_fields->add_value('ab_contacts.department',$strDepartment);
		$search_fields->add_value('ab_contacts.function',$strFunction);
		$search_fields->add_value('ab_contacts.address',$strAddress);
		$search_fields->add_value('ab_contacts.city', $strCity);
		$search_fields->add_value('ab_contacts.zip',$strZip);
		$search_fields->add_value('ab_contacts.state',$strState);
		$search_fields->add_value('ab_contacts.country', $strCountry);
		$search_fields->add_value('ab_contacts.comment', $ab_comment);

		if(isset($GO_MODULES->modules['custom_fields']) && $GO_MODULES->modules['custom_fields']['read_permission'])
		{
			require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
			$cf = new custom_fields();
			$cf2 = new custom_fields();

			$cf2->get_categories(2);
			while($cf2->next_record())
			{
				if($cf->get_fields($cf2->f('id')))
				{
					while($cf->next_record())
					{
						$cf_fields[]=$cf->Record;
						$search_fields->add_value('cf_2.col_'.$cf->f('id'), $cf->f('name'));
					}
				}
			}
		}

		$search_field = $ab_settings['search_contacts_field'] ;
		if(isset($_REQUEST['search_field']))
		{
			if($search_fields->is_in_select($_REQUEST['search_field']))
			{
				$search_field = $_REQUEST['search_field'];
				$ab_settings['search_contacts_field'] = $_REQUEST['search_field'];
			}
		}
		$search_fields->value=$ab_settings['search_contacts_field'];
		break;
}

$form->add_html_element($menu);

$ab->update_settings($ab_settings);

$form->add_html_element(new input('hidden', 'search_addressbook_id', $ab_settings['search_addressbook_id'], false));

if ($ab_settings['search_type'] != 'user')
{
	$row->add_cell(new table_cell($ab_search_in.':'));

	if($ab_settings['search_addressbook_id'] == 0)
	{
		$link_text = $ab_all_your_addressbooks;
	}else
	{
		$addressbook = $ab->get_addressbook($ab_settings['search_addressbook_id']);
		$link_text = $addressbook['name'];
	}
	$link = new hyperlink("javascript:popup('select_addressbook.php?callback=select_search_addressbook&add_null=true', '300','400');",$link_text);
	$link->set_attribute('style','margin:3px;');
	$link->set_attribute('class','normal');

	$row->add_cell(new table_cell($link->get_html()));
	$row->add_cell(new table_cell($ab_search_on.':'));
	$row->add_cell(new table_cell($search_fields->get_html()));
}else
{

	$row->add_cell(new table_cell($ab_search_on_users.':'));
	$row->add_cell(new table_cell($search_fields->get_html()));
}
$row->add_cell(new table_cell($ab_search_for_keyword.':'));

$query = $_SESSION['GO_SESSION']['addressbook']['query'] = isset($_SESSION['GO_SESSION']['addressbook']['query']) ? $_SESSION['GO_SESSION']['addressbook']['query'] : '';
if(isset($_POST['query']))
{
	$query = $_SESSION['GO_SESSION']['addressbook']['query'] = smart_addslashes($_POST['query']);
}


$input = new input('text', 'query', stripslashes($query), false);
$input->set_attribute('style', 'width:300px;');
$row->add_cell(new table_cell($input->get_html().$ab_wildcard));

$table->add_row($row);

$tabstrip->add_html_element($table);
$tabstrip->add_html_element(new button($cmdSearch, 'javascript:_search();'));



$form->add_html_element($tabstrip);

if ($task != 'show_letter')
{
	$query = '%'.smart_addslashes($query).'%';
}else
{
	switch ($ab_settings['search_type'])
	{
		case 'company':
			$search_field='ab_companies.name';
			break;

		case 'contact':
			$search_field='ab_contacts.'.$_SESSION['GO_SESSION']['sort_name'];
			break;

		case 'user':
			$search_field='users.'.$_SESSION['GO_SESSION']['sort_name'];
			break;
	}
	$query = smart_addslashes($_POST['clicked_letter']).'%';
}
$results = '';

switch($ab_settings['search_type'])
{
	case 'contact':



		$datatable->add_column(new table_heading($strName, 'name'));
		$datatable->add_column(new table_heading($strCompany, 'company_name'));
		$datatable->add_column(new table_heading($strEmail, 'ab_contacts.email'));
		$datatable->add_column(new table_heading($strPhone, 'ab_contacts.home_phone'));
		$datatable->add_column(new table_heading($strWorkphone, 'ab_contacts.work_phone'));

		$datatable->add_column(new table_heading($strAddress, 'ab_contacts.address'));
		$datatable->add_column(new table_heading($strZip, 'ab_contacts.zip'));
		$datatable->add_column(new table_heading($strCity, 'ab_contacts.city'));
		$datatable->add_column(new table_heading($strState, 'ab_contacts.state'));
		$datatable->add_column(new table_heading($strCountry, 'ab_contacts.country'));


		if(isset($cf_fields))
		{
			foreach($cf_fields as $field)
			{
				$datatable->add_column(new table_heading($field['name'], 'col_'.$field['id']));
			}
		}

		if(!$datatable->is_sort_index())
		{
			$datatable->sort_index='name';
		}
		$count = $ab->search_contacts(
		$GO_SECURITY->user_id,
		$query,
		$search_field,
		$ab_settings['search_addressbook_id'],
		0,
		$datatable->start,
		$datatable->offset,false, $datatable->sort_index, $datatable->sql_sort_order);

		$datatable->set_pagination($count);

		while ($ab->next_record())
		{

			$mail_name = format_name($ab->f('last_name'), $ab->f('first_name'), $ab->f('middle_name'), 'first_name');
			$full_email = ($ab->f("email") != "") ? '"'.$mail_name.'" <'.$ab->f('email').'>' : '';
			$name = format_name($ab->f('last_name'), $ab->f('first_name'), $ab->f('middle_name'));

			$row = new table_row($ab->f('id'));
			$row->set_attribute('ondblclick', "document.location='contact.php?contact_id=".$ab->f("id")."&return_to=".urlencode($GO_MODULES->url.'index.php')."'");
			$cell = new table_cell(htmlspecialchars($name));
			if ($ab->f('color') != '')
			{
				$cell->set_attribute('style','color: '.$ab->f('color'));
			}
			$row->add_cell($cell);
			$row->add_cell(new table_cell(show_company($ab->f('company_id'))));
			$row->add_cell(new table_cell(mail_to(empty_to_stripe(addslashes($full_email)), empty_to_stripe($ab->f('email')),'normal',true, $ab->f('id'))));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('home_phone')))));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('work_phone')))));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('address')))));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('zip')))));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('city')))));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('state')))));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('country')))));
			if(isset($cf_fields))
			{
				foreach($cf_fields as $field)
				{
					$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('col_'.$field['id'])))));
				}
			}
			$datatable->add_row($row);
		}

		$result_str =  ($count == 1) ? $count.' '.$contacts_result : $count.' '.$contacts_results;
		$form->add_html_element(new html_element('h2', $result_str));

		break;

	case 'company':



		$datatable->add_column(new table_heading($strName, 'ab_companies.name'));
		$datatable->add_column(new table_heading($strCity, 'ab_companies.city'));
		$datatable->add_column(new table_heading($strEmail, 'ab_companies.email'));
		$datatable->add_column(new table_heading($strPhone, 'ab_companies.phone'));
		$datatable->add_column(new table_heading($strHomepage,'ab_companies.homepage'));

		$datatable->add_column(new table_heading($strAddress, 'ab_companies.address'));
		$datatable->add_column(new table_heading($strZip, 'ab_companies.zip'));
		$datatable->add_column(new table_heading($strCity, 'ab_companies.city'));
		$datatable->add_column(new table_heading($strState, 'ab_companies.state'));
		$datatable->add_column(new table_heading($strCountry, 'ab_companies.country'));

		$datatable->add_column(new table_heading($strPostAddress, 'ab_companies.post_address'));
		$datatable->add_column(new table_heading($strPostZip, 'ab_companies.post_zip'));
		$datatable->add_column(new table_heading($strPostCity, 'ab_companies.post_city'));
		$datatable->add_column(new table_heading($strPostState, 'ab_companies.post_state'));
		$datatable->add_column(new table_heading($strPostCountry, 'ab_companies.post_country'));


		if(isset($cf_fields))
		{
			foreach($cf_fields as $field)
			{
				$datatable->add_column(new table_heading($field['name'], 'col_'.$field['id']));
			}
		}


		if(!$datatable->is_sort_index())
		{
			$datatable->sort_index='name';
		}

		$count = $ab->search_companies(
		$GO_SECURITY->user_id,
		$query,
		$search_field,
		$ab_settings['search_addressbook_id'],
		$datatable->start,
		$datatable->offset,false, $datatable->sort_index, $datatable->sql_sort_order);

		$datatable->set_pagination($count);

		while ($ab->next_record())
		{
			$full_email = $ab->f('email') == '' ? '' : '"'.$ab->f('name').'" <'.$ab->f('email').'>';

			$row = new table_row($ab->f('id'));
			$row->set_attribute('ondblclick', "document.location='company.php?company_id=".$ab->f("id")."&return_to=".urlencode($GO_MODULES->url.'index.php')."'");
			$cell = new table_cell(htmlspecialchars($ab->f('name')));
			if ($ab->f('color') != '')
			{
				$cell->set_attribute('style','color: '.$ab->f('color'));
			}
			$row->add_cell($cell);
			$row->add_cell(new table_cell($ab->f('city')));
			$row->add_cell(new table_cell(mail_to(empty_to_stripe(addslashes($full_email)), empty_to_stripe($ab->f('email')),'normal',true, $ab->f('id'))));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('phone')))));

			$hyperlink = new hyperlink($ab->f('homepage'), $ab->f('homepage'));
			$hyperlink->set_attribute('class', 'normal');
			$hyperlink->set_attribute('target', '_blank');

			$row->add_cell(new table_cell($hyperlink->get_html()));

			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('address')))));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('zip')))));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('city')))));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('state')))));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('country')))));

			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('post_address')))));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('post_zip')))));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('post_city')))));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('post_state')))));
			$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('post_country')))));
			if(isset($cf_fields))
			{
				foreach($cf_fields as $field)
				{
					$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('col_'.$field['id'])))));
				}
			}


			$datatable->add_row($row);
		}


		$result_str =  ($count == 1) ? $count.' '.$contacts_result : $count.' '.$contacts_results;
		$form->add_html_element(new html_element('h2', $result_str));







		break;

	case 'user':

		$datatable = new datatable('company_search');
		$datatable->set_attribute('width','100%');
		$menu->add_button('delete_big',$cmdDelete, "javascript:document.addressbook_form.task.value='".$task."';".$datatable->get_delete_handler());
		$datatable->allow_configuration();

		$datatable->add_column(new table_heading($strName, 'name'));
		$datatable->add_column(new table_heading($strCompany, 'company'));
		$datatable->add_column(new table_heading($strEmail, 'email'));
		$datatable->add_column(new table_heading($strPhone, 'home_phone'));
		$datatable->add_column(new table_heading($strWorkphone, 'work_phone'));
		$datatable->add_column(new table_heading($strWorkFax, 'work_fax'));

		$datatable->add_column(new table_heading($strAddress, 'address'));
		$datatable->add_column(new table_heading($strZip, 'zip'));
		$datatable->add_column(new table_heading($strCity, 'city'));
		$datatable->add_column(new table_heading($strState, 'state'));
		$datatable->add_column(new table_heading($strCountry, 'country_id'));


		$datatable->add_column(new table_heading($strWorkAddress, 'work_address'));
		$datatable->add_column(new table_heading($strWorkZip, 'work_zip'));
		$datatable->add_column(new table_heading($strWorkCity, 'work_city'));
		$datatable->add_column(new table_heading($strWorkState, 'work_state'));
		$datatable->add_column(new table_heading($strWorkCountry, 'work_country_id'));

		if(isset($cf_fields))
		{
			foreach($cf_fields as $field)
			{
				$datatable->add_column(new table_heading($field['name'], 'col_'.$field['id']));
			}
		}

		if(!$datatable->is_sort_index())
		{
			$datatable->sort_index='name';
		}

		$count = $GO_USERS->search(
		$query,
		$search_field,
		$GO_SECURITY->user_id,
		$datatable->start,
		$datatable->offset, $datatable->sort_index, $datatable->sql_sort_order);

		$datatable->set_pagination($count);
		
		$GO_USERS2 = new $go_users_class;

		while ($GO_USERS->next_record())
		{
			if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $GO_USERS->f('acl_id')))
			{
				$middle_name = $GO_USERS->f('middle_name') == '' ? '' : $GO_USERS->f('middle_name').' ';
				$name = $GO_USERS->f('first_name').' '.$middle_name.$GO_USERS->f('last_name');

				$full_email = $GO_USERS->f('email') == '' ? '' : '"'.$name.'" <'.$GO_USERS->f('email').'>';

				$name = format_name($GO_USERS->f('last_name'), $GO_USERS->f('first_name'), $GO_USERS->f('middle_name'));

				$row = new table_row($GO_USERS->f('id'));
				$row->set_attribute('ondblclick', "document.location='".$GO_CONFIG->control_url."user.php?id=".$GO_USERS->f('id')."&return_to=".urlencode($GO_MODULES->url)."'");
				$cell = new table_cell(htmlspecialchars($name));
				$row->add_cell($cell);
				$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('company')))));
				$row->add_cell(new table_cell(mail_to(empty_to_stripe(addslashes($full_email)), empty_to_stripe($GO_USERS->f('email')),'normal',true, $ab->f('id'))));
				$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($GO_USERS->f('home_phone')))));
				$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($GO_USERS->f('work_phone')))));
				$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($GO_USERS->f('work_fax')))));

				$row->add_cell(new table_cell(htmlspecialchars($GO_USERS->f('address').' '.$GO_USERS->f('address_no'))));
				$row->add_cell(new table_cell(htmlspecialchars($GO_USERS->f('zip'))));
				$row->add_cell(new table_cell(htmlspecialchars($GO_USERS->f('city'))));
				$row->add_cell(new table_cell(htmlspecialchars($GO_USERS->f('state'))));

				$country = $GO_USERS2->get_country($GO_USERS->f('country_id'));
				if($country)
				{
					$country = $country['name'];
				}else {
					$country = '';
				}

				$row->add_cell(new table_cell(htmlspecialchars($country)));



				$row->add_cell(new table_cell(htmlspecialchars($GO_USERS->f('work_address').' '.$GO_USERS->f('work_address_no'))));
				$row->add_cell(new table_cell(htmlspecialchars($GO_USERS->f('work_zip'))));
				$row->add_cell(new table_cell(htmlspecialchars($GO_USERS->f('work_city'))));
				$row->add_cell(new table_cell(htmlspecialchars($GO_USERS->f('work_state'))));

				$work_country = $GO_USERS2->get_country($GO_USERS->f('work_country_id'));
				if($work_country)
				{
					$work_country = $work_country['name'];
				}else {
					$work_country = '';
				}
				$row->add_cell(new table_cell(htmlspecialchars($work_country)));

				if(isset($GO_MODULES->modules['custom_fields']))
				{
					foreach($cf_fields as $field)
					{
						$row->add_cell(new table_cell($cf->format_field($field, $GO_USERS->f('col_'.$field['id']))));
					}
				}

				$datatable->add_row($row);
			}
		}

		$result_str =  ($count == 1) ? $count.' '.$contacts_result : $count.' '.$contacts_results;
		$h2 = new html_element('h2', $result_str);
		$form->add_html_element($h2);


		break;
}


?>
<script type="text/javascript">
function _search()
{
	<?php echo $datatable->set_page_one(); ?>
	document.forms[0].task.value='search';
	document.forms[0].submit();
}

function letter_click(letter)
{
	<?php 	echo $datatable->set_page_one(); 	?>

	document.forms[0].task.value='show_letter';
	document.forms[0].clicked_letter.value=letter;
	document.forms[0].submit();
}
</script>
