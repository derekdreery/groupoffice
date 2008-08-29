<?php
/**
 * @copyright Intermesh 2004
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.33 $ $Date: 2006/11/23 11:34:44 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once("Group-Office.php");
require_once($GO_LANGUAGE->get_language_file('users'));

load_basic_controls();
load_control('date_picker');

function check_fields($required_fields)
{

	foreach($required_fields as $field)
	{
		if(!empty($field) && empty($_POST[$field]))
		{
			return false;
		}
	}

	return true;
}

require($GO_LANGUAGE->get_language_file('users'));

$login_task = isset($_REQUEST['login_task']) ? $_REQUEST['login_task'] : '';
//$goto_url = isset($_REQUEST['goto_url']) ? smart_stripslashes($_REQUEST['goto_url']) : $_SERVER['PHP_SELF'];
//$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];

$fields = explode(',', $GO_CONFIG->registration_fields);

$form = new form('login_form');
$form->add_html_element(new input('hidden','login_task', 'register', false));
$form->add_html_element(new input('hidden','task', 'login'));
if(isset($_REQUEST['return_to']))
{
	$form->add_html_element(new input('hidden','return_to', smart_stripslashes($_REQUEST['return_to'])));
}

$modules_read = array_map('trim', explode(',',$GO_CONFIG->register_modules_read));
$modules_write = array_map('trim', explode(',',$GO_CONFIG->register_modules_write));

//user groups the user will be added to.
$user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GO_CONFIG->register_user_groups)));

//user groups that this user will be visible to
$visible_user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GO_CONFIG->register_visible_user_groups)));



if(!$GO_CONFIG->allow_registration)
{
	header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
	exit();
}


$user['first_name'] = isset($_POST['first_name']) ?  smart_stripslashes(trim($_POST['first_name'])) : '';
$user['middle_name'] = isset($_POST['middle_name']) ?  smart_stripslashes(trim($_POST['middle_name'])) : '';
$user['last_name'] = isset($_POST['last_name']) ?  smart_stripslashes(trim($_POST['last_name'])) : '';

if(in_array('title_initials', $fields))
{
	$user['initials'] = isset($_POST['initials']) ? smart_stripslashes($_POST["initials"]) : '';
	$user['title'] = isset($_POST['title']) ? smart_stripslashes($_POST["title"]) : '';
}
if(in_array('birthday', $fields))
{
	$user['birthday'] = isset($_POST['birthday']) ? smart_stripslashes($_POST["birthday"]) : '';
}
$user['email'] = isset($_POST['email']) ? smart_stripslashes($_POST["email"]) : '';
if(in_array('home_phone', $fields))
{
	$user['home_phone'] = isset($_POST['home_phone']) ? smart_stripslashes($_POST["home_phone"]) : '';
}
if(in_array('work_phone', $fields))
{
	$user['work_phone'] = isset($_POST['work_phone']) ? smart_stripslashes($_POST["work_phone"]) : '';
}
if(in_array('fax', $fields))
{
	$user['fax'] = isset($_POST['fax']) ? smart_stripslashes($_POST["fax"]) : '';
}
if(in_array('work_fax', $fields))
{
	$user['work_fax'] = isset($_POST['work_fax']) ? smart_stripslashes($_POST["work_fax"]) : '';
}
if(in_array('cellular', $fields))
{
	$user['cellular'] = isset($_POST['cellular']) ? smart_stripslashes($_POST["cellular"]) : '';
}
if(in_array('address', $fields))
{
	$user['country_id'] = isset($_POST['country_id']) ? smart_addslashes($_POST["country_id"]) : $GO_CONFIG->default_country_id;
	$user['state'] = isset($_POST['state']) ? smart_stripslashes($_POST["state"]) : '';
	$user['city'] = isset($_POST['city']) ? smart_stripslashes($_POST["city"]) : '';
	$user['zip'] = isset($_POST['zip']) ? smart_stripslashes($_POST["zip"]) : '';
	$user['address'] = isset($_POST['address']) ? smart_stripslashes($_POST["address"]) : '';
	$user['address_no'] = isset($_POST['address_no']) ? smart_stripslashes($_POST["address_no"]) : '';
}

if(in_array('work_address', $fields))
{
	$user['work_country_id'] = isset($_POST['work_country_id']) ? smart_addslashes($_POST["work_country_id"]) : $GO_CONFIG->default_country_id;
	$user['work_state'] = isset($_POST['work_state']) ? smart_stripslashes($_POST["work_state"]) : '';
	$user['work_city'] = isset($_POST['work_city']) ? smart_stripslashes($_POST["work_city"]) : '';
	$user['work_zip'] = isset($_POST['work_zip']) ? smart_stripslashes($_POST["work_zip"]) : '';
	$user['work_address'] = isset($_POST['work_address']) ? smart_stripslashes($_POST["work_address"]) : '';
	$user['work_address_no'] = isset($_POST['work_address_no']) ? smart_stripslashes($_POST["work_address_no"]) : '';
}

if(in_array('company', $fields))
{
	$user['company'] = isset($_POST['company']) ? smart_stripslashes($_POST["company"]) : '';
}
if(in_array('department', $fields))
{
	$user['department'] =  isset($_POST['department']) ? smart_stripslashes($_POST["department"]) : '';
}
if(in_array('function', $fields))
{
	$user['function'] =  isset($_POST['function']) ? smart_stripslashes($_POST["function"]) : '';
}
if(in_array('sex', $fields))
{
	$user['sex'] = isset($_POST['sex']) ? smart_stripslashes($_POST["sex"]) : 'M';
}

if(in_array('homepage', $fields))
{
	$user['homepage'] = isset($_POST['homepage']) ? smart_stripslashes($_POST["homepage"]) : '';
}

$user['language'] = isset($_POST['SET_LANGUAGE']) ? $_POST['SET_LANGUAGE'] : $GO_LANGUAGE->language['code'];

$user['theme'] = $GO_CONFIG->theme;
$user['username'] = isset($_POST['username']) ? smart_stripslashes($_POST['username']) : '';
$user['enabled'] = $GO_CONFIG->auto_activate_accounts ? '1' : '0';


$login_task = isset($_REQUEST['login_task']) ? $_REQUEST['login_task'] : '';
$goto_url = isset($_REQUEST['goto_url']) ? smart_stripslashes($_REQUEST['goto_url']) : $_SERVER['PHP_SELF'];

$birthday = isset($_REQUEST['birthday']) ? $_REQUEST['birthday'] : '';
$birthday_picker = new date_picker('birthday', $_SESSION['GO_SESSION']['date_format'], $birthday);
$GO_HEADER['head'] = $birthday_picker->get_header();

$required_registration_fields = str_replace('address', 'address,address_no,zip,city,state,country_id', $GO_CONFIG->required_registration_fields);
$required_registration_fields = str_replace('work_address', 'work_address,work_address_no,work_zip,work_city,work_state,work_country_id', $required_registration_fields);
$required_registration_fields = str_replace('title_initials', 'title,initials', $required_registration_fields);
$required_fields = explode(',',$required_registration_fields);
$required_fields[]='username';
$required_fields[]='email';
$required_fields[]='first_name';
$required_fields[]='last_name';

if ($login_task == "register")
{

	if($GO_CONFIG->auto_activate_accounts)
	{
		$pass1 = smart_stripslashes($_POST["pass1"]);
		$pass2 = smart_stripslashes($_POST["pass2"]);
		$user['password'] = smart_stripslashes($_POST["pass1"]);
	}else {
		$user['password']='';
	}

	$user = array_map('addslashes',$user);

	if (!check_fields($required_fields) || ($GO_CONFIG->auto_activate_accounts && (empty($pass1) || empty ($pass2))))
	{
		$feedback = $GLOBALS['error_missing_field'];
	}elseif(!preg_match('/^[a-z0-9_-]*$/', $user['username']))
	{
		$feedback = $GLOBALS['error_username'];
	}elseif(!validate_email($user['email']))
	{
		$feedback = $GLOBALS['error_email'];
	}elseif($GO_USERS->get_user_by_username($user['username']))
	{
		$feedback = $GLOBALS['error_username_exists'];
	}elseif(!$GO_CONFIG->allow_duplicate_email && $GO_USERS->email_exists($user['email']))
	{
		$feedback = $GLOBALS['error_email_exists'];
	}elseif($GO_CONFIG->auto_activate_accounts && $pass1 != $pass2)
	{
		$feedback = $GLOBALS['error_match_pass'];
	}else
	{
		if(isset($_POST['birthday']))
		{
			$user['birthday'] = date_to_db_date($_POST['birthday']);
		}

		if ($new_user_id = $GO_USERS->add_user($user, $user_groups, $visible_user_groups, $modules_read, $modules_write	))
		{

			if($user['enabled']=='1')
			{
				$registration_mail_body = $GO_CONFIG->get_setting('registration_confirmation');
				$registration_mail_subject = $GO_CONFIG->get_setting('registration_confirmation_subject');
			}else {
				$registration_mail_body = $GO_CONFIG->get_setting('registration_unconfirmed');
				$registration_mail_subject = $GO_CONFIG->get_setting('registration_unconfirmed_subject');
			}

			if(!empty($registration_mail_body) && !empty($registration_mail_subject))
			{
				//send email to the user with password
				if(isset($_POST['sex']))
				{
					$registration_mail_body = str_replace("%beginning%", $GLOBALS['sir_madam'][$_POST['sex']], $registration_mail_body);
				}else {
					$registration_mail_body = str_replace("%beginning%", $GLOBALS['sir_madam']['M'].'/'.$GLOBALS['sir_madam']['F'], $registration_mail_body);
				}
				// If $title is not set, then use $sex (sir_madam) instead for $title.
				if(isset($_POST['title']))
				{
					if($user['title']=='')
					{
						if(isset($_POST['sex']))
						{
							$title = $sir_madam[$_POST['sex']];
						}else {
							$title = $sir_madam['M'].'/'.$sir_madam['F'];
						}
					}else {
						$title = $user['title'];
					}
					$registration_mail_body = str_replace("%title%", $title, $registration_mail_body);
				}
				$registration_mail_body = str_replace("%last_name%", smart_stripslashes($_POST['last_name']), $registration_mail_body);
				$registration_mail_body = str_replace("%middle_name%", smart_stripslashes($_POST['middle_name']), $registration_mail_body);
				$registration_mail_body = str_replace("%first_name%", smart_stripslashes($_POST['first_name']), $registration_mail_body);
				$registration_mail_body = str_replace("%username%",smart_stripslashes($_POST['username']), $registration_mail_body);
				$registration_mail_body = str_replace("%password%",smart_stripslashes($user['password']), $registration_mail_body);
				$registration_mail_body = str_replace("%full_url%",'<a href="'.$GO_CONFIG->full_url.'">'.$GO_CONFIG->full_url.'</a>', $registration_mail_body);

				sendmail($user['email'], $GO_CONFIG->webmaster_email, $GO_CONFIG->title, $registration_mail_subject, $registration_mail_body,'3','text/HTML');
			}

			if($GO_CONFIG->notify_admin_of_registration)
			{
				$body = $admin_new_user_body."\r\n\r\n".$GO_CONFIG->full_url.'?return_to='.urlencode($GO_MODULES->modules['users']['url'].'user.php?user_id='.$new_user_id.'&return_to=index.php');
				$subject = $user['enabled'] == '1' ? $admin_new_user_subject : $admin_new_user_subject_activate;

				sendmail($GO_CONFIG->webmaster_email, $GO_CONFIG->webmaster_email, $GO_CONFIG->title, $subject, $body);
			}

			//create Group-Office home directory
			$old_umask = umask(000);
			mkdir($GO_CONFIG->file_storage_path.stripslashes($user['username']), $GO_CONFIG->create_mode);
			umask($old_umask);

			//confirm registration to the user and exit the script so the form won't load

			if($user['enabled']=='1')
			{
				$h1 = new html_element('h1',  sprintf($registration_self_success,$GO_CONFIG->title));
				$form->add_html_element($h1);
				$p = new html_element('p',  $registration_self_success_text);
				$form->add_html_element($p);
			}else {
				$p = new html_element('p',  $registration_success_activate);
				$form->add_html_element($p);
			}


			$link = 'index.php?username='.urlencode(smart_stripslashes($_POST['username']));

			if(isset($_REQUEST['return_to']))
			{
				$link = add_params_to_url($link,'return_to='.urlencode($_REQUEST['return_to']));
			}


			$button = new button($GLOBALS['cmdContinue'],"javascript:document.location='".$link."';");

			$form->add_html_element($button);

			require_once($GO_CONFIG->root_path.'login_screens/'.$GO_CONFIG->login_screen.'/login_header.inc');
			echo $form->get_html();
			require_once($GO_CONFIG->root_path.'login_screens/'.$GO_CONFIG->login_screen.'/login_footer.inc');
			exit();

			exit();
		}else
		{
			$error = $registration_failure;
		}
	}
}





$form->add_html_element(new html_element('h1', str_replace("%groupoffice_title%",$GO_CONFIG->title, $registration_title)));
$form->add_html_element(new html_element('p', $registration_text));

if (isset($feedback))
{
	$p = new html_element('p', $feedback);
	$p->set_attribute('class','error');
	$form->add_html_element($p);
}

$table = new table();
$row = new table_row();


$row->add_cell(new table_cell($GLOBALS['strFirstName'].'*:'));
$input = new input('text','first_name',$user['first_name'],true,true);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($GLOBALS['strMiddleName'].':'));
$input = new input('text','middle_name',$user['middle_name']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($GLOBALS['strLastName'].'*:'));
$input = new input('text','last_name', $user['last_name'],true,true);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','100');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);



if(in_array('title_initials',$fields))
{
	$row = new table_row();


	if(in_array('title',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row->add_cell(new table_cell($GLOBALS['strTitle'].' / '.$GLOBALS['strInitials'].$end));

	$input1 = new input('text','title', $user['title'],true,$required);
	$input1->set_attribute('style','width:135px');
	$input1->set_attribute('maxlength','12');

	$span = new html_element('span', ' / ');
	$span->set_attribute('style', 'width: 20px;text-align:center;');

	$input2 = new input('text','initials', $user['initials'],true,$required);
	$input2->set_attribute('style','width:135px');
	$input2->set_attribute('maxlength','50');

	$row->add_cell(new table_cell($input1->get_html().$span->get_html().$input2->get_html()));
	$table->add_row($row);
}



if(in_array('sex',$fields))
{
	$row = new table_row();
	if(in_array('sex',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row->add_cell(new table_cell($GLOBALS['strSex'].$end));
	$radiogroup = new radiogroup('sex', $user['sex']);
	$male_button = new radiobutton('sex_m', 'M',$required);
	$female_button = new radiobutton('sex_f', 'F',$required);

	$row->add_cell(new table_cell($radiogroup->get_option($male_button, $GLOBALS['strSexes']['M']).$radiogroup->get_option($female_button, $GLOBALS['strSexes']['F'])));
	$table->add_row($row);
}

if(in_array('birthday',$fields))
{
	if(in_array('birthday',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strBirthday'].$end));
	$row->add_cell(new table_cell($birthday_picker->get_html()));
	$table->add_row($row);
}

$row = new table_row();
$row->add_cell(new table_cell($GLOBALS['strEmail'].'*:'));
$input = new input('text','email', $user['email'],true,true);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);

if(in_array('address',$fields))
{
	if(in_array('address',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}

	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strAddressAndNo'].$end));
	$input = new input('text','address', $user['address'],true,$required);
	$input->set_attribute('style','width:230px');
	$input->set_attribute('maxlength','50');

	$input1 = new input('text','address_no', $user['address_no'],true,$required);
	$input1->set_attribute('style','width:47px');
	$input1->set_attribute('maxlength','10');

	$row->add_cell(new table_cell($input->get_html().$input1->get_html()));
	$table->add_row($row);

	if(in_array('zip',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strZip'].$end));
	$input = new input('text','zip', $user['zip'],true,$required);
	$input->set_attribute('style','width:280px');
	$input->set_attribute('maxlength','20');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	if(in_array('city',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strCity'].$end));
	$input = new input('text','city', $user['city'],true,$required);
	$input->set_attribute('style','width:280px');
	$input->set_attribute('maxlength','50');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	if(in_array('state',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strState'].$end));
	$input = new input('text','state', $user['state'],true,$required);
	$input->set_attribute('style','width:280px');
	$input->set_attribute('maxlength','30');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	if(in_array('country_id',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strCountry'].$end));
	$select = new select('country_id', $user['country_id'],false,$required);
	$select->add_value('0', $GLOBALS['cmdPleaseSelect']);
	$GO_USERS->get_countries();
	while($GO_USERS->next_record())
	{
		$select->add_value($GO_USERS->f('id'), $GO_USERS->f('name'));
	}
	$row->add_cell(new table_cell($select->get_html()));
	$table->add_row($row);

	$row = new table_row();
	$cell = new table_cell('&nbsp;');
	$cell->set_attribute('colspan','2');
	$row->add_cell($cell);
	$table->add_row($row);
}

$break=false;
if(in_array('home_phone',$fields))
{
	if(in_array('home_phone',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strPhone'].$end));
	$input = new input('text','home_phone', $user['home_phone'],true,$required);
	$input->set_attribute('style','width:280px');
	$input->set_attribute('maxlength','20');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
	$break=true;
}

if(in_array('fax',$fields))
{
	if(in_array('fax',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strFax'].$end));
	$input = new input('text','fax', $user['fax'],true,$required);
	$input->set_attribute('style','width:280px');
	$input->set_attribute('maxlength','20');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
	$break=true;
}
if(in_array('cellular',$fields))
{
	if(in_array('cellular',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strCellular'].$end));
	$input = new input('text','cellular', $user['cellular'],true,$required);
	$input->set_attribute('style','width:280px');
	$input->set_attribute('maxlength','20');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
	$break=true;
}

if($break)
{
	$row = new table_row();
	$cell = new table_cell('&nbsp;');
	$cell->set_attribute('colspan','2');
	$row->add_cell($cell);
	$table->add_row($row);
	$break=false;
}



$break = false;
if(in_array('company',$fields))
{
	if(in_array('company',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strCompany'].$end));
	$input = new input('text','company', $user['company'],true,$required);
	$input->set_attribute('style','width:280px');
	$input->set_attribute('maxlength','50');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
	$break=true;
}

if(in_array('department',$fields))
{
	if(in_array('department',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strDepartment'].$end));
	$input = new input('text','department', $user['department'],true,$required);
	$input->set_attribute('style','width:280px');
	$input->set_attribute('maxlength','50');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
	$break=true;
}

if(in_array('function',$fields))
{
	if(in_array('function',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strFunction'].$end));
	$input = new input('text','function', $user['function'],true,$required);
	$input->set_attribute('style','width:280px');
	$input->set_attribute('maxlength','50');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
	$break=true;
}

if($break)
{
	$row = new table_row();
	$cell = new table_cell('&nbsp;');
	$cell->set_attribute('colspan','2');
	$row->add_cell($cell);
	$table->add_row($row);
	$break=false;
}
if(in_array('work_address',$fields))
{
	$break=true;
	if(in_array('work_address',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strWorkAddressAndNo'].$end));
	$input = new input('text','work_address', $user['work_address'],true,$required);
	$input->set_attribute('style','width:230px');
	$input->set_attribute('maxlength','100');

	$input1 = new input('text','work_address_no', $user['work_address_no'],true,$required);
	$input1->set_attribute('style','width:47px');
	$input1->set_attribute('maxlength','10');

	$row->add_cell(new table_cell($input->get_html().$input1->get_html()));
	$table->add_row($row);

	if(in_array('work_zip',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strWorkZip'].$end));
	$input = new input('text','work_zip', $user['work_zip'],true,$required);
	$input->set_attribute('style','width:280px');
	$input->set_attribute('maxlength','20');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	if(in_array('work_city',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strWorkCity'].$end));
	$input = new input('text','work_city', $user['work_city'],true,$required);
	$input->set_attribute('style','width:280px');
	$input->set_attribute('maxlength','50');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	if(in_array('work_state',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strWorkState'].$end));
	$input = new input('text','work_state', $user['work_state'],true,$required);
	$input->set_attribute('style','width:280px');
	$input->set_attribute('maxlength','50');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	if(in_array('work_country_id',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strCountry'].$end));


	$select = new select('work_country_id', $user['work_country_id'],false,$required);
	$select->add_value('0', $GLOBALS['cmdPleaseSelect']);
	$GO_USERS->get_countries();
	while($GO_USERS->next_record())
	{
		$select->add_value($GO_USERS->f('id'), $GO_USERS->f('name'));
	}
	$row->add_cell(new table_cell($select->get_html()));
	$table->add_row($row);

	$row = new table_row();
	$cell = new table_cell('&nbsp;');
	$cell->set_attribute('colspan','2');
	$row->add_cell($cell);
	$table->add_row($row);
}

if(in_array('work_phone',$fields))
{
	$break=true;
	if(in_array('work_phone',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strWorkphone'].$end));
	$input = new input('text','work_phone', $user['work_phone'],true,$required);
	$input->set_attribute('style','width:280px');
	$input->set_attribute('maxlength','20');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
}

if(in_array('work_fax',$fields))
{
	$break=true;
	if(in_array('work_fax',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strWorkFax'].$end));
	$input = new input('text','work_fax', $user['work_fax'],true,$required);
	$input->set_attribute('style','width:280px');
	$input->set_attribute('maxlength','20');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
}

if(in_array('homepage',$fields))
{
	$break=true;
	if(in_array('homepage',$required_fields))
	{
		$required=true;
		$end='*:';
	}else {
		$end=':';
		$required=false;
	}
	$row = new table_row();
	$row->add_cell(new table_cell($GLOBALS['strHomepage'].$end));
	$input = new input('text','homepage', $user['homepage'],true,$required);
	$input->set_attribute('style','width:280px');
	$input->set_attribute('maxlength','100');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
}


if($break)
{
	$row = new table_row();
	$cell = new table_cell('&nbsp;');
	$cell->set_attribute('colspan','2');
	$row->add_cell($cell);
	$table->add_row($row);
	$break=false;
}
$row = new table_row();
$row->add_cell(new table_cell($GLOBALS['strUsername'].'*:'));
$input = new input('text', 'username',$user['username'],true,true);
$input->set_attribute('style','width:200px');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

if($GO_CONFIG->auto_activate_accounts)
{
	$row = new table_row();
	$row->add_cell(new table_cell($admin_password.'*:'));
	$input = new input('password', 'pass1','',true,true);
	$input->set_attribute('style','width:200px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	$row = new table_row();
	$row->add_cell(new table_cell($admin_confirm_password.'*:'));
	$input = new input('password', 'pass2','',true,true);
	$input->set_attribute('style','width:200px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
}

$form->add_html_element($table);


$button = new button($cmdOk, 'javascript:register_user();');
$form->add_html_element($button);
$button = new button($cmdReset, 'javascript:document.forms[0].reset();');
$form->add_html_element($button);
$link = 'index.php';

if(isset($_REQUEST['return_to']))
{
	$link = add_params_to_url($link, 'return_to='.urlencode($_REQUEST['return_to']));
}
$button = new button($cmdCancel, "javascript:document.location='".$link."';");
$form->add_html_element($button);

require_once($GO_CONFIG->root_path.'login_screens/'.$GO_CONFIG->login_screen.'/login_header.inc');
echo $form->get_html();
?>
<script type="text/javascript">
document.forms[0].first_name.focus();
function register_user()
{
	document.forms[0].task.value='register';
	document.forms[0].submit();
}
</script>
<?php

require_once($GO_CONFIG->root_path.'login_screens/'.$GO_CONFIG->login_screen.'/login_footer.inc');
