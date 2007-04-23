<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

require_once("../../Group-Office.php");
require_once($GO_LANGUAGE->get_language_file('users'));

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('users');

load_basic_controls();
load_control('datatable');

require_once($GO_CONFIG->class_path.'admin.class.inc');
$admin = new admin();


if(isset($_POST['query']))
{
	$_SESSION['GO_SESSION']['query'] = smart_stripslashes($_POST['query']);
	$_SESSION['GO_SESSION']['search_field'] = smart_stripslashes($_POST['search_field']);
}else {
	$_SESSION['GO_SESSION']['query'] = isset($_SESSION['GO_SESSION']['query']) ? $_SESSION['GO_SESSION']['query'] : '';
	$_SESSION['GO_SESSION']['search_field'] = isset($_SESSION['GO_SESSION']['search_field']) ? $_SESSION['GO_SESSION']['search_field'] : '';
}

$search_fields = new select('search_field', $_SESSION['GO_SESSION']['search_field']);
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

$GO_HEADER['head'] = datatable::get_header();
$GO_HEADER['body_arguments'] = 'onload="document.forms[0].query.focus();" onkeypress="executeOnEnter(event, \'search();\');"';
require_once($GO_THEME->theme_path."header.inc");

$form = new form('users_form');

$table = new datatable('users');
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

$table->add_column(new table_heading($strName, 'name'));
$table->add_column(new table_heading($strUsername, 'username'));
$table->add_column(new table_heading($strCompany, 'company'));
$table->add_column(new table_heading($strLogins, 'logins'));
$table->add_column(new table_heading($ac_lastlogin , 'lastlogin'));
$table->add_column(new table_heading($strRegistrationDate, 'registration_time'));

$table->add_column(new table_heading($strAddress, 'address'));
$table->add_column(new table_heading($strZip, 'zip'));
$table->add_column(new table_heading($strCity, 'city'));
$table->add_column(new table_heading($strState, 'state'));
$table->add_column(new table_heading($strCountry, 'country_id'));

$table->add_column(new table_heading($strEmail, 'email'));
$table->add_column(new table_heading($strPhone, 'home_phone'));
$table->add_column(new table_heading($strWorkphone, 'work_phone'));


$table->add_column(new table_heading($strWorkAddress, 'work_address'));
$table->add_column(new table_heading($strWorkZip, 'work_zip'));
$table->add_column(new table_heading($strWorkCity, 'work_city'));
$table->add_column(new table_heading($strWorkState, 'work_state'));
$table->add_column(new table_heading($strWorkCountry, 'work_country_id'));

if(isset($GO_MODULES->modules['custom_fields']))
{
	require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
	$cf = new custom_fields();
	$cf2 = new custom_fields();

	$cf->get_categories(8);
	
	$cf_fields=array();

	while($cf->next_record())
	{
		$cf2->get_fields($cf->f('id'));
		while($cf2->next_record())
		{
			$cf_fields[]=$cf2->Record;
			$table->add_column(new table_heading($cf2->f('name'), 'col_'.$cf2->f('id')));
		}
	}

}



$count = $GO_USERS->search('%'.$_SESSION['GO_SESSION']['query'].'%', $search_fields->value, 0, $table->start, $table->offset, $table->sort_index, $table->sql_sort_order);

$table->set_pagination($count);

$GO_USERS2 = new $go_users_class;

while ($GO_USERS->next_record())
{
	$name = format_name($GO_USERS->f('last_name'),$GO_USERS->f('first_name'),$GO_USERS->f('middle_name'));

	$row = new table_row($GO_USERS->f('id'));
	$row->set_attribute('ondblclick', "document.location='user.php?user_id=".$GO_USERS->f("id")."&return_to=".urlencode($_SERVER['PHP_SELF'])."'");
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
	
	$last_login = $GO_USERS->f("lastlogin")>0 ? get_timestamp($GO_USERS->f("lastlogin")) : $us_never;
	$row->add_cell(new table_cell($last_login));
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

	if(isset($GO_MODULES->modules['custom_fields']))
	{
		foreach($cf_fields as $field)
		{
			$row->add_cell(new table_cell($cf->format_field($field, $GO_USERS->f('col_'.$field['id']))));
		}
	}


	$table->add_row($row);
}

if (isset($feedback)){
	$p = new html_element('p', $feedback);
	$p->set_attribute('class','Error');
	$form->innerHTML .= $p->get_html();
}

$menu = new button_menu();
if ($GO_CONFIG->max_users == 0 || ($count < $GO_CONFIG->max_users))
{
	$menu->add_button('user_add',$cmdAdd, 'user.php');
}
$menu->add_button('delete_big',$cmdDelete, $table->get_delete_handler());
$menu->add_button('cms_settings',$cmdSettings, 'settings.php');
$form->add_html_element($menu);


$form->add_html_element($search_fields);


$input = new input('text', 'query', $_SESSION['GO_SESSION']['query'], false);
$input->set_attribute('style', 'width:300px;');

$form->add_html_element($input);
$form->add_html_element(new button($cmdSearch, 'javascript:search()'));
$form->add_html_element(new button($us_reset, 'javascript:_reset();'));


$div = new html_element('div', $count.' '.$strUsers);

$div->set_attribute('style','text-align:right;');
if ($GO_CONFIG->max_users != 0)
{
	$div->innerHTML .= ' '.$strMaxOf.' '.$GO_CONFIG->max_users;
	$div->set_attribute('class','Error');
}else
{
	$div->set_attribute('class','small');
}
$form->innerHTML .= $div->get_html().
$table->get_html();

echo $form->get_html();
?>
<script type="text/javascript">
function search()
{
	<?php echo $table->set_page_one(); ?>
	document.forms[0].submit();
}
function _reset()
{
	<?php echo $table->set_page_one(); ?>
	document.forms[0].search_field.value='';
	document.forms[0].query.value='';	
	document.forms[0].submit();
}

</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
