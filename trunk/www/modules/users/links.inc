<?php
global $GO_USERS, $GO_SECURITY;

require($GO_LANGUAGE->get_language_file('users'));

$table = new datatable('links_users_table');
$table->allow_configuration();
$table->set_attribute('width','100%');

if($table->task == 'delete')
{
	foreach($table->selected as $delete_user_id)
	{
		if (($delete_user_id != $GO_SECURITY->user_id) && ($delete_user_id != 1))
		{
			$GO_USERS->delete_user($delete_user_id);
		}else
		{
			$feedback = $delete_fail;
		}
	}
}


$table->add_column(new table_heading($GLOBALS['strName'], 'name'));
$table->set_attribute('style','width:100%;margin-bottom:20px;margin-top:0px;');

$count = $GO_USERS->get_linked_users($GO_SECURITY->user_id, $link_id);

$table->set_pagination($count);

if($count>0)
{

	
	$table->add_column(new table_heading($GLOBALS['strUsername'], 'username'));
	$table->add_column(new table_heading($GLOBALS['strCompany'], 'company'));
	$table->add_column(new table_heading($strLogins, 'logins'));
	$table->add_column(new table_heading($ac_lastlogin , 'lastlogin'));
	$table->add_column(new table_heading($strRegistrationDate, 'registration_time'));

	$table->add_column(new table_heading($GLOBALS['strAddress'], 'address'));
	$table->add_column(new table_heading($GLOBALS['strZip'], 'zip'));
	$table->add_column(new table_heading($GLOBALS['strCity'], 'city'));
	$table->add_column(new table_heading($GLOBALS['strState'], 'state'));
	$table->add_column(new table_heading($GLOBALS['strCountry'], 'country_id'));

	$table->add_column(new table_heading($GLOBALS['strEmail'], 'email'));
	$table->add_column(new table_heading($GLOBALS['strPhone'], 'home_phone'));
	$table->add_column(new table_heading($GLOBALS['strWorkphone'], 'work_phone'));


	$table->add_column(new table_heading($GLOBALS['strWorkAddress'], 'work_address'));
	$table->add_column(new table_heading($GLOBALS['strWorkZip'], 'work_zip'));
	$table->add_column(new table_heading($GLOBALS['strWorkCity'], 'work_city'));
	$table->add_column(new table_heading($GLOBALS['strWorkState'], 'work_state'));
	$table->add_column(new table_heading($GLOBALS['strWorkCountry'], 'work_country_id'));

	

	$GO_USERS2 = new $GLOBALS['go_users_class'];

	while ($GO_USERS->next_record())
	{
		$name = format_name($GO_USERS->f('last_name'),$GO_USERS->f('first_name'),$GO_USERS->f('middle_name'));

		$row = new table_row($GO_USERS->f('id'));
		$row->set_attribute('ondblclick', "document.location='user.php?user_id=".$GO_USERS->f("id")."&return_to=".urlencode($return_to)."'");
		if($GO_USERS->f('enabled') == '0')
		{
			$row->set_attribute('class', 'Error');
		}

		$row->add_cell(new table_cell(htmlspecialchars($name)));
		$row->add_cell(new table_cell($GO_USERS->f("username")));
		$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($GO_USERS->f("company")))));
		$row->add_cell(new table_cell(
		number_format($GO_USERS->f("logins"), 0,
		$_SESSION['GO_SESSION']['decimal_seperator'],
		$_SESSION['GO_SESSION']['thousands_seperator'])));
		$row->add_cell(new table_cell(get_timestamp($GO_USERS->f("lastlogin"))));
		$row->add_cell(new table_cell(get_timestamp($GO_USERS->f("registration_time"))));


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

		$row->add_cell(new table_cell(htmlspecialchars($GO_USERS->f('email'))));
		$row->add_cell(new table_cell(htmlspecialchars($GO_USERS->f('home_phone'))));
		$row->add_cell(new table_cell(htmlspecialchars($GO_USERS->f('work_phone'))));


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


		$table->add_row($row);
	}

	$link = new hyperlink($GO_MODULES->modules['users']['url'], $lang_modules['users']);
	$this->add_html_element(new html_element('h2', $link->get_html()));

	$this->add_html_element($table);
	$this->delete_tasks .= $table->get_task_var().'.value="delete";';
	$this->unlink_tasks .= $table->get_task_var().'.value="unlink";';
}