<?php
$form = new form('user_form');
$form->add_html_element(new input('hidden', 'user_id', $user_id));
$form->add_html_element(new input('hidden', 'return_to', $return_to));
$form->add_html_element(new input('hidden', 'close', 'false'));
$form->add_html_element(new input('hidden', 'task'));

if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $form->add_html_element($p);
}

$tabstrip = new tabstrip('user_tabstrip',$user_profile.' '.$user['username']);
$tabstrip->set_attribute('width','100%');
$tabstrip->set_return_to($return_to);
$tabstrip->add_html_element(new checkbox('enabled', 'enabled','1',$users_enabled, ($user['enabled'] == '1')));


$maintable = new table();
$mainrow = new table_row();

$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($strFirstName.'*:'));
$input = new input('text','first_name',$user['first_name']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strMiddleName.':'));
$input = new input('text','middle_name',$user['middle_name']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strLastName.'*:'));
$input = new input('text','last_name', $user['last_name']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','100');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strTitle.' / '.$strInitials.':'));
$input1 = new input('text','title', $user['title']);
$input1->set_attribute('style','width:135px');
$input1->set_attribute('maxlength','12');

$span = new html_element('span', ' / ');
$span->set_attribute('style', 'width: 20px;text-align:center;');

$input2 = new input('text','initials', $user['initials']);
$input2->set_attribute('style','width:135px');
$input2->set_attribute('maxlength','50');

$row->add_cell(new table_cell($input1->get_html().$span->get_html().$input2->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strSex.':'));
$radiogroup = new radiogroup('sex', $user['sex']);
$male_button = new radiobutton('sex_m', 'M');
$female_button = new radiobutton('sex_f', 'F');

$row->add_cell(new table_cell($radiogroup->get_option($male_button, $strSexes['M']).$radiogroup->get_option($female_button, $strSexes['F'])));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strBirthday.':'));
$row->add_cell(new table_cell($birthday_picker->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strAddress.':'));
$input = new input('text','address', $user['address']);
$input->set_attribute('style','width:230px');
$input->set_attribute('maxlength','50');

$input1 = new input('text','address_no', $user['address_no']);
$input1->set_attribute('style','width:47px');
$input1->set_attribute('maxlength','10');

$row->add_cell(new table_cell($input->get_html().$input1->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strZip.':'));
$input = new input('text','zip', $user['zip']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCity.':'));
$input = new input('text','city', $user['city']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strState.':'));
$input = new input('text','state', $user['state']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','30');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCountry.':'));
$select = new select('country_id', $user['country_id']);
$select->add_value('0', $cmdPleaseSelect);
$GO_USERS->get_countries();
while($GO_USERS->next_record())
{
	$select->add_value($GO_USERS->f('id'), $GO_USERS->f('name'));
}
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strEmail.'*'));
$input = new input('text','email', $user['email']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strPhone.':'));
$input = new input('text','home_phone', $user['home_phone']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strFax.':'));
$input = new input('text','fax', $user['fax']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCellular.':'));
$input = new input('text','cellular', $user['cellular']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$cell = new table_cell($table->get_html());
$cell->set_attribute('valign','top');
$mainrow->add_cell($cell);


$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($strCompany.':'));
$input = new input('text','company', $user['company']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strDepartment.':'));
$input = new input('text','department', $user['department']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strFunction.':'));
$input = new input('text','function', $user['function']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strAddress.':'));
$input = new input('text','work_address', $user['work_address']);
$input->set_attribute('style','width:230px');
$input->set_attribute('maxlength','50');

$input1 = new input('text','work_address_no', $user['work_address_no']);
$input1->set_attribute('style','width:47px');
$input1->set_attribute('maxlength','10');

$row->add_cell(new table_cell($input->get_html().$input1->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strZip.':'));
$input = new input('text','work_zip', $user['work_zip']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCity.':'));
$input = new input('text','work_city', $user['work_city']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strState.':'));
$input = new input('text','work_state', $user['work_state']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','30');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCountry.':'));
$select = new select('work_country_id', $user['work_country_id']);
$select->add_value('0', $cmdPleaseSelect);
$GO_USERS->get_countries();
while($GO_USERS->next_record())
{
	$select->add_value($GO_USERS->f('id'), $GO_USERS->f('name'));
}
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strPhone.':'));
$input = new input('text','work_phone', $user['work_phone']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strFax.':'));
$input = new input('text','work_fax', $user['work_fax']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strHomepage.':'));
$input = new input('text','homepage', $user['homepage']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','100');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$cell = new table_cell($table->get_html());
$cell->set_attribute('valign','top');

$mainrow->add_cell($cell);
$maintable->add_row($mainrow);

$mainrow = new table_row();

$tabstrip->add_html_element($maintable);
$form->add_html_element($tabstrip);

//Account info
if($user_id > 0)
{
	$tabstrip = new tabstrip('account_info_tabstrip', $ac_login_info);
	$tabstrip->set_attribute('width','100%');
	$table = new table();

	$row = new table_row();
	$row->add_cell(new table_cell($ac_registration_time.':'));
	$row->add_cell(new table_cell(get_timestamp($user["registration_time"])));
	$table->add_row($row);

	$row = new table_row();
	$row->add_cell(new table_cell($ac_lastlogin.':'));
	$row->add_cell(new table_cell(get_timestamp($user["lastlogin"])));
	$table->add_row($row);

	$row = new table_row();
	$row->add_cell(new table_cell($ac_logins.':'));
	$row->add_cell(new table_cell(number_format($user["logins"], 0, $_SESSION['GO_SESSION']['decimal_seperator'], $_SESSION['GO_SESSION']['thousands_seperator'])));
	$table->add_row($row);

	$tabstrip->add_html_element($table);
	$form->add_html_element($tabstrip);
}

//Modules

$tabstrip = new tabstrip('permissions_tabstrip', $strPermissions);
$tabstrip->set_attribute('width','100%');

$p = new html_element('p',$admin_module_access);

$table = new table();
$table->set_attribute('class','go_simple_table');
$table->add_column(new table_heading($admin_module));
$table->add_column(new table_heading($admin_use));
$table->add_column(new table_heading($admin_manage));

$module_count = $GO_MODULES->get_modules('0');
while($GO_MODULES->next_record())
{
  //require language file to obtain module name in the right language
	$language_file = $GO_LANGUAGE->get_language_file($GO_MODULES->f('id'));

  if(file_exists($language_file))
  {
  	require_once($language_file);
  }
  
  $lang_var = isset($lang_modules[$GO_MODULES->f('id')]) ? $lang_modules[$GO_MODULES->f('id')] : $GO_MODULES->f('id');
	
	$row = new table_row();
	$row->add_cell(new table_cell($lang_var));
	
	if($user_id > 0)
	{
		$read_check = $GO_SECURITY->has_permission($user_id, $GO_MODULES->f('acl_read'));
	}else
	{		
		$modules_read = isset($_POST['modules_read']) ? $_POST['modules_read'] : array();
		$read_check = in_array($GO_MODULES->f('id'), $modules_read);
	}
	
	$checkbox = new checkbox(
		$GO_MODULES->f('acl_read'), 
		'modules_read[]', 
		$GO_MODULES->f('id'), 
		'', 
		$read_check);
	
	$cell = new table_cell($checkbox->get_html());
	$cell->set_attribute('align','center');
	$row->add_cell($cell);		
	
	if($user_id > 0)
	{
		$write_check = $GO_SECURITY->has_permission($user_id, $GO_MODULES->f('acl_write'));
	}else
	{
		$modules_write = isset($_POST['modules_write']) ? $_POST['modules_write'] : array();
		$write_check = in_array($GO_MODULES->f('id'), $modules_write);
	}
	
	$checkbox = new checkbox(
		$GO_MODULES->f('acl_write'), 
		'modules_write[]', 
		$GO_MODULES->f('id'), 
		'', 
		$write_check);
	
	$cell = new table_cell($checkbox->get_html());
	$cell->set_attribute('align','center');
	$row->add_cell($cell);
	
	$table->add_row($row);
}

$maintable = new table();
$maintable->set_attribute('width','100%');
$mainrow = new table_row();

$cell = new table_cell();
$cell->set_attribute('valign','top');
$cell->add_html_element($p);
$cell->add_html_element($table);

$mainrow->add_cell($cell);


$cell = new table_cell();
$cell->set_attribute('valign','top');

$p = new html_element('p',$admin_groups_user.':');
$cell->add_html_element($p);

$input = new input('hidden', 'user_groups[]', $GO_CONFIG->group_everyone);
$cell->add_html_element($input);

$GO_GROUPS->get_groups();
$groups2 = new $go_groups_class();

while($GO_GROUPS->next_record())
{
  if ($GO_GROUPS->f('id') != $GO_CONFIG->group_everyone)
  {
  	if($user_id > 0)
		{
			$group_check= $groups2->is_in_group($user_id, $GO_GROUPS->f('id'));
		}else
		{
			$user_groups = isset($_POST['user_groups']) ? $_POST['user_groups'] : array();
			$group_check = in_array($GO_GROUPS->f('id'), $user_groups);
		}
  	
  	$checkbox = new checkbox(
  		'group_'.$GO_GROUPS->f('id'),
  		'user_groups[]',
  		$GO_GROUPS->f('id'),
  		$GO_GROUPS->f('name'),
  		$group_check);
  		
  	if($user_id == 1 && $GO_GROUPS->f('id') == $GO_CONFIG->group_root)
  	{
  		$checkbox->set_attribute('disabled','true');
  		$checkbox->set_attribute('checked','true');
  		$form->add_html_element(new input('hidden', 'user_groups[]',	$GO_GROUPS->f('id'), false));
		}
  	$cell->add_html_element($checkbox);
  	$cell->add_html_element(new html_element('br'));
  
  
  }
}

$mainrow->add_cell($cell);



$cell = new table_cell();
$cell->set_attribute('valign','top');

$p = new html_element('p',$admin_groups_visible.':');
$cell->add_html_element($p);

$GO_GROUPS->get_groups();
$groups2 = new $go_groups_class();

while($GO_GROUPS->next_record())
{
	if($user_id > 0)
	{
		$visible_group_check= $GO_SECURITY->group_in_acl($GO_GROUPS->f('id'), $user['acl_id']);
	}else
	{
		if($_SERVER['REQUEST_METHOD'] != 'POST')
		{
			$visible_user_groups = array($GO_CONFIG->group_root, $GO_CONFIG->group_everyone);
		}else
		{
			$visible_user_groups = isset($_POST['visible_user_groups']) ? $_POST['visible_user_groups'] : array();
		}
		$visible_group_check = in_array($GO_GROUPS->f('id'), $visible_user_groups);
	}
	
	$checkbox = new checkbox(
		'visible_group_'.$GO_GROUPS->f('id'),
		'visible_user_groups[]',
		$GO_GROUPS->f('id'),
		$GO_GROUPS->f('name'),
		$visible_group_check);
		
	if($GO_GROUPS->f('id') == $GO_CONFIG->group_root)
	{
		$checkbox->set_attribute('disabled','true');
		$checkbox->set_attribute('checked','true');
		if($user_id>0)
		{
			$form->add_html_element(new input('hidden', 'visible_user_groups[]',	$GO_GROUPS->f('id'), false));
		}
	}
		
	$cell->add_html_element($checkbox);
	$cell->add_html_element(new html_element('br'));
  
}

$mainrow->add_cell($cell);



$maintable->add_row($mainrow);

$tabstrip->add_html_element($maintable);
$form->add_html_element($tabstrip);

//look & feel
$tabstrip = new tabstrip('look_feel_tabstrip', $admin_look_feel);
$tabstrip->set_attribute('width','100%');

$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($reg_language.':'));

$select = new select('language', $user['language']);
$languages = $GO_LANGUAGE->get_languages();
foreach($languages as $language)
{
	$select->add_value($language['code'], $language['description']);
}
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($reg_theme.':'));

$select = new select('theme', $user['theme']);
$themes = $GO_THEME->get_themes();
foreach($themes as $theme)
{
	$select->add_value($theme, $theme);
}
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);

$tabstrip->add_html_element($table);
$form->add_html_element($tabstrip);

//change password



$table = new table();

if($user_id == 0)
{
	$tabstrip = new tabstrip('passchange_tabstrip', $registration_account_info);

	$row = new table_row();
	$row->add_cell(new table_cell($strUsername.'*:'));
	$input = new input('text', 'username');
	$input->set_attribute('style','width:200px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
}else
{
	$tabstrip = new tabstrip('passchange_tabstrip', $admin_change_password);
}


$tabstrip->set_attribute('width','100%');


$row = new table_row();
$row->add_cell(new table_cell($admin_password.':'));
$input = new input('password', 'pass1', '',false);
$input->set_attribute('style','width:200px');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($admin_confirm_password.':'));
$input = new input('password', 'pass2', '',false);
$input->set_attribute('style','width:200px');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$tabstrip->add_html_element($table);
$form->add_html_element($tabstrip);

//Buttons

$button = new button($cmdOk, "javascript:_save('save', 'true')");
$form->add_html_element($button);
$button = new button($cmdApply, "javascript:_save('save', 'false')");
$form->add_html_element($button);
$button = new button($cmdClose, 'javascript:document.location=\''.$return_to.'\';');
$form->add_html_element($button);

echo $form->get_html();
?>
<script type="text/javascript">
document.forms[0].first_name.focus();
function _save(task, close)
{
  document.forms[0].task.value = task;
  document.forms[0].close.value = close;
  document.forms[0].submit();
}
</script>
