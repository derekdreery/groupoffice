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

require_once("../Group-Office.php");

$GO_CONFIG->set_help_url($config_help_url);

load_basic_controls();
load_control('tooltip');
load_control('date_picker');

function check_fields($required_fields, $disabled_user_fields)
{
	foreach($required_fields as $field)
	{
		if(!empty($field) && empty($_POST[$field]) && !in_array($field, $disabled_user_fields))
		{
			return false;
		}
	}

	return true;
}

$required_registration_fields = str_replace('address', 'address,address_no,zip,city,state,country_id', $GO_CONFIG->required_registration_fields);
$required_registration_fields = str_replace('work_address', 'work_address,work_address_no,work_zip,work_city,work_state,work_country_id', $required_registration_fields);
$required_registration_fields = str_replace('title_initials', 'title,initials', $required_registration_fields);

$required_fields = explode(',',$required_registration_fields);
$required_fields[]='email';
$required_fields[]='first_name';
$required_fields[]='last_name';


$GO_SECURITY->authenticate();
//if the user is authorising but it's logged in under another user log him out first.
if(isset($_REQUEST['requested_user_id']) && $_REQUEST['requested_user_id'] != $GO_SECURITY->user_id)
{
	SetCookie("GO_UN","",time()-3600,"/","",0);
	SetCookie("GO_PW","",time()-3600,"/","",0);
	unset($_SESSION);
	unset($_COOKIES);
	$GO_SECURITY->logout();
	$GO_SECURITY->authenticate();
}


$return_to = $GO_CONFIG->host.'configuration/';

require_once($GO_LANGUAGE->get_base_language_file('account'));
require_once($GO_LANGUAGE->get_base_language_file('preferences'));

$page_title = $acTitle;

$tabstrip = new tabstrip('account_tab', $menu_configuration, '150',  'account_form', 'vertical');
$tabstrip->add_tab('profile.inc', $acProfile);

if(isset($GO_MODULES->modules['custom_fields']))
{
	require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
	$cf = new custom_fields();

	if($cf->get_authorized_categories(8, $GO_SECURITY->user_id))
	{
		while($cf->next_record())
		{
			$tabstrip->add_tab($cf->f('id'), $cf->f('name'));
		}
	}
}


$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

if(isset($_REQUEST['feedback']))
{
	$feedback = $_REQUEST['feedback'];
}

$disabled_user_fields = $GO_CONFIG->get_setting('disabled_user_fields');

if(!$disabled_user_fields)
{
	$disabled_user_fields=array();
}else {
	$disabled_user_fields = explode(',', $disabled_user_fields);
}


switch($task)
{
	case 'add_user':
		$GO_HEADER['body_arguments'] = 'onkeypress="executeOnEnter(event, \'_save(\\\'authorize)\\\')\');" onload="document.forms[0].auth_email_address.focus();"';
		break;

	case 'save_look':

		$old_user = $GO_USERS->get_user($GO_SECURITY->user_id);
		$old_theme = $old_user['theme'];

		$user['id'] = $GO_SECURITY->user_id;
		$user['max_rows_list'] = smart_addslashes($_POST['max_rows_list']);
		$user['start_module'] = smart_addslashes($_POST['start_module']);
		$user['sort_name'] =	smart_addslashes($_POST['sort_name']);
		$user['theme'] = smart_addslashes($_POST['theme']);
		$user['use_checkbox_select'] = isset($_POST['use_checkbox_select']) ? '1' : '0';


		$GO_USERS->update_profile($user);

		if($user['theme'] != $old_theme)
		{
			echo '<script type="text/javascript">';
			echo 'parent.location="'.$GO_CONFIG->host.'index.php?return_to='.urlencode($_SERVER['PHP_SELF']).'";';
			echo '</script>';
			exit();
		}
		break;

	case 'save_notations':

		$old_user = $GO_USERS->get_user($GO_SECURITY->user_id);
		$old_language = $old_user['language'];

		$user['language'] = smart_addslashes($_POST['language']);

		$user['id'] = $GO_SECURITY->user_id;
		$user['DST'] =isset($_POST['DST']) ? '1' : '0';
		$user['date_format'] =	smart_addslashes($_POST['date_format']);
		$user['date_seperator'] =	smart_addslashes($_POST['date_seperator']);
		$user['time_format'] =	smart_addslashes($_POST['time_format']);
		$user['thousands_seperator'] =	smart_addslashes($_POST['thousands_seperator']);
		$user['decimal_seperator'] =	smart_addslashes($_POST['decimal_seperator']);
		$user['currency'] =	smart_addslashes($_POST['currency']);
		$user['timezone'] =	smart_addslashes($_POST['timezone']);
		$user['first_weekday'] =	smart_addslashes($_POST['first_weekday']);


		$GO_USERS->update_profile($user);

		if($user['language'] != $old_language)
		{
			echo '<script type="text/javascript">';
			echo 'parent.location="'.$GO_CONFIG->host.'index.php?return_to='.urlencode($_SERVER['PHP_SELF']).'";';
			echo '</script>';
			exit();
		}
		break;

	case 'accept':
		if (isset($_REQUEST['requested_user_id']) && isset($_REQUEST['authcode']))
		{
			if ($user = $GO_USERS->get_user($_REQUEST['requesting_user_id']))
			{
				$middle_name = $user['middle_name'] == '' ? '' : $user['middle_name'].' ';
				$user_name = $middle_name.$user['last_name'];

				if($GO_USERS->authorize($_REQUEST['requesting_user_id'], $_REQUEST['authcode'], $GO_SECURITY->user_id))
				{
					$feedback = $ac_auth_success.'<br /><br />';

					$mail_body = $ac_salutation." ".$sir_madam[$user['sex']]." ".$user_name.",\r\n\r\n";
					$mail_body .= $_SESSION['GO_SESSION']['name']." ".$ac_auth_accept_mail_body;

					sendmail($user['email'], $GO_CONFIG->webmaster_email,
					$GO_CONFIG->title, $ac_auth_accept_mail_title,
					$mail_body,'3 (Normal)', 'text/plain');
				}

			}else
			{
				$feedback = '<p class="Error">'.$ac_auth_error.'</p>';
			}
			$task = 'privacy';
			$tabstrip->set_active_tab('privacy.inc');
		}
		break;

	case 'decline':
		if (isset($_REQUEST['requested_user_id']) && isset($_REQUEST['authcode']))
		{
			if ($user = $GO_USERS->get_user($_REQUEST['requesting_user_id']))
			{
				$middle_name = $user['middle_name'] == '' ? '' : $user['middle_name'].' ';
				$user_name = $middle_name.$user['last_name'];

				$feedback = $ac_auth_decline.'<br /><br />';
				$mail_body = $ac_salutation." ".$sir_madam[$user['sex']]." ".$user_name.",\r\n\r\n";
				$mail_body .= $_SESSION['GO_SESSION']['name']." ".$ac_auth_decline_mail_body;
				sendmail($user['email'], $GO_CONFIG->webmaster_email, $GO_CONFIG->title,
				$ac_auth_decline_mail_title, $mail_body,'3 (Normal)', 'text/plain');

			}else
			{
				$feedback = '<p class="Error">'.$ac_auth_error.'</p>';
			}
			$task = 'privacy';
			$tabstrip->set_active_tab('privacy.inc');
		}
		break;

	case 'save_profile':
		$user['id'] = $GO_SECURITY->user_id;
		if(isset($_POST['first_name']))
		{
			$user['first_name'] =  smart_addslashes($_POST["first_name"]);
		}
		if(isset($_POST['middle_name']))
		{
			$user['middle_name'] = smart_addslashes($_POST["middle_name"]);
		}
		if(isset($_POST['last_name']))
		{
			$user['last_name'] = smart_addslashes($_POST["last_name"]);
		}
		if(isset($_POST['initials']))
		{
			$user['initials'] = smart_addslashes($_POST["initials"]);
		}
		if(isset($_POST['title']))
		{
			$user['title'] = smart_addslashes($_POST["title"]);
		}
		if(isset($_POST['birthday']))
		{
			$user['birthday'] = date_to_db_date(smart_stripslashes($_POST["birthday"]));
		}
		if(isset($_POST['email']))
		{
			$user['email'] = smart_addslashes($_POST["email"]);
		}
		if(isset($_POST['home_phone']))
		{
			$user['home_phone'] = smart_addslashes($_POST["home_phone"]);
		}
		if(isset($_POST['work_phone']))
		{
			$user['work_phone'] = smart_addslashes($_POST["work_phone"]);
		}
		if(isset($_POST['fax']))
		{
			$user['fax'] = smart_addslashes($_POST["fax"]);
		}
		if(isset($_POST['cellular']))
		{
			$user['cellular'] = smart_addslashes($_POST["cellular"]);
		}
		if(isset($_POST['country_id']))
		{
			$user['country_id'] = smart_addslashes($_POST["country_id"]);
		}
		if(isset($_POST['state']))
		{
			$user['state'] = smart_addslashes($_POST["state"]);
		}
		if(isset($_POST['city']))
		{
			$user['city'] = smart_addslashes($_POST["city"]);
		}
		if(isset($_POST['zip']))
		{
			$user['zip'] = smart_addslashes($_POST["zip"]);
		}
		if(isset($_POST['address']))
		{
			$user['address'] = smart_addslashes($_POST["address"]);
		}
		if(isset($_POST['address_no']))
		{
			$user['address_no'] = smart_addslashes($_POST["address_no"]);
		}
		if(isset($_POST['company']))
		{
			$user['company'] = smart_addslashes($_POST["company"]);
		}
		if(isset($_POST['department']))
		{
			$user['department'] = smart_addslashes($_POST["department"]);
		}
		if(isset($_POST['function']))
		{
			$user['function'] =  smart_addslashes($_POST["function"]);
		}
		if(isset($_POST['work_country_id']))
		{
			$user['work_country_id'] = smart_addslashes($_POST["work_country_id"]);
		}
		if(isset($_POST['work_state']))
		{
			$user['work_state'] = smart_addslashes($_POST["work_state"]);
		}
		if(isset($_POST['work_city']))
		{
			$user['work_city'] = smart_addslashes($_POST["work_city"]);
		}
		if(isset($_POST['work_zip']))
		{
			$user['work_zip'] = smart_addslashes($_POST["work_zip"]);
		}
		if(isset($_POST['work_address']))
		{
			$user['work_address'] = smart_addslashes($_POST["work_address"]);
		}
		if(isset($_POST['work_address_no']))
		{
			$user['work_address_no'] = smart_addslashes($_POST["work_address_no"]);
		}
		if(isset($_POST['work_fax']))
		{
			$user['work_fax'] = smart_addslashes($_POST["work_fax"]);
		}
		if(isset($_POST['homepage']))
		{
			$user['homepage'] = smart_addslashes($_POST["homepage"]);
		}
		if(isset($_POST['sex']))
		{
			$user['sex'] = smart_addslashes($_POST["sex"]);
		}



		if(!check_fields($required_fields, $disabled_user_fields))
		{
			$feedback = $error_missing_field;
		}elseif(!in_array('last_name', $disabled_user_fields) && !validate_email($user['email']))
		{
			$feedback = $error_email;
		}else
		{
			$GO_USERS->update_profile($user);

			if (isset($_POST['load_frames']))
			{
				if(isset($GO_MODULES->modules['email']) && $GO_MODULES->modules['email']['read_permission'])
				{
					require_once($GO_MODULES->modules['email']['class_path'].'email.class.inc');
					$email = new email();

					$email->get_accounts($GO_SECURITY->user_id);
					if($email->next_record())
					{
						$account['id'] = $email->f('id');
						$account['email'] = $user['email'];
						$account['name'] = format_name($user['last_name'], $user['first_name'], $user['middle_name'], 'first_name');

						$email->_update_account($account);
					}
				}
				header('Location: '.$GO_CONFIG->host);
				exit();
			}
		}

		break;

	case 'change_password':
		require_once($GO_CONFIG->class_path."/validate.class.inc");
		$val = new validate;
		$val->error_required = $error_required;
		$val->error_min_length = $error_min_length;
		$val->error_max_length = $error_max_length;
		$val->error_expression = $error_email;
		$val->error_match = $error_match_auth;

		$newpass1 = smart_stripslashes($_POST['newpass1']);
		$newpass2 = smart_stripslashes($_POST['newpass2']);

		if($newpass1 == '' || $newpass2 == '')
		{
			$feedback = $error_missing_field;
		}elseif ($newpass1 != $newpass2)
		{
			$feedback = $error_match_pass;
		}else
		{
			if (!$GO_USERS->check_password(smart_stripslashes($_POST['currentpassword'])))
			{
				$feedback = $security_wrong_password;
			}else
			{
				if ($_POST['newpass1'] != "")
				{
					if ($GO_USERS->update_password($GO_SECURITY->user_id,
					smart_stripslashes($_POST['newpass1'])))
					{
						$feedback = $security_password_update;
					}else
					{
						$feedback = $strSaveError;
					}
				}
			}
		}
		break;

	case 'save_custom_fields':
		require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
		$cf = new custom_fields();

		$cf->save_fields($_POST['account_tab'], $_POST['link_id'], $disabled_user_fields);

		if ($_POST['close'] == 'true' && !isset($feedback))
		{
			header('Location: '.$return_to);
			exit();
		}
		break;
}

$user = $GO_USERS->get_user($GO_SECURITY->user_id);


$GO_HEADER['head'] = tooltip::get_header();

$birthday = db_date_to_date($user['birthday']);
$birthday_picker = new date_picker('birthday', $_SESSION['GO_SESSION']['date_format'], $birthday);
$GO_HEADER['head'] .= $birthday_picker->get_header();


if ($_SESSION['GO_SESSION']['first_name'] != '' && $_SESSION['GO_SESSION']['last_name'] != '' && $_SESSION['GO_SESSION']['email'] != '')
{
	require($GO_LANGUAGE->get_language_file('groups'));
	$tabstrip->add_tab('groups.inc', $lang_modules['groups']);
	
	if ($GO_CONFIG->allow_password_change)
	{
		$tabstrip->add_tab('security.inc', $acSecurity);
	}

	$tabstrip->add_tab('privacy.inc', $acPrivacy);
	$tabstrip->add_tab('look.inc', $pref_look);
	$tabstrip->add_tab('notations.inc', $pref_notations);


	$sync_tab=false;
	if(isset($GO_MODULES->modules['calendar']) && $GO_MODULES->modules['calendar']['read_permission'])
	{
		require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
		$cal = new calendar();
		$sync_tab=true;
	}
	if(isset($GO_MODULES->modules['addressbook']) && $GO_MODULES->modules['addressbook']['read_permission'])
	{
		require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
		$ab = new addressbook();
		$sync_tab=true;
	}

	$settings_include = $GO_CONFIG->root_path.'sync/sync.settings.inc';
	if($sync_tab && file_exists($settings_include))
	{
		$tabstrip->add_tab($settings_include, $strSynchronization);
	}


	foreach($GO_MODULES->modules as $module)
	{
		if($module['read_permission'])
		{
			$settings_include = $module['path'].$module['id'].'.settings.inc';
			if(file_exists($settings_include))
			{
				require($GO_LANGUAGE->get_language_file($module['id']));
				$module_name = isset($lang_modules[$module['id']]) ? $lang_modules[$module['id']] : $module['id'];
				$tabstrip->add_tab($settings_include, $module_name);
			}
		}
	}
}

switch($tabstrip->get_active_tab_id())
{
	case 'security.inc':
		$GO_HEADER['body_arguments'] = 'onkeypress="executeOnEnter(event, \'_save(\\\'change_password\\\')\');" '.
		'onload="document.forms[0].currentpassword.focus();"';
		break;

	case 'profile.inc':
		$GO_HEADER['body_arguments'] = 'onkeypress="executeOnEnter(event, \'_save(\\\'save_profile\\\')\');"'.
		' onload="document.forms[0].first_name.focus();"';
		break;

	case 'look.inc':
		$GO_HEADER['body_arguments'] = 'onkeypress="executeOnEnter(event, \'_save(\\\'save_look\\\')\');"';
		break;

	case 'notations.inc':
		$GO_HEADER['body_arguments'] = 'onkeypress="executeOnEnter(event, \'_save(\\\'save_notations\\\')\');"';
		break;
}

require_once($GO_THEME->theme_path."header.inc");

$form = new form('account_form');
$form->add_html_element(new input('hidden', 'task','',false));
$form->add_html_element(new input('hidden', 'close', 'false'));

if (isset($feedback))
{
	$p = new html_element('p', $feedback);
	$p->set_attribute('class','Error');
	$tabstrip->add_html_element($p);
}

if($tabstrip->get_active_tab_id()>0)
{
	if(empty($user['link_id']))
	{
		$update_user['id'] = $GO_SECURITY->user_id;
		$update_user['link_id'] = $user['link_id']= $GO_LINKS->get_link_id();
		$GO_USERS->update_profile($update_user);
	}

	$form->add_html_element(new input('hidden', 'link_id', $user['link_id']));
	if($cf_table = $cf->get_fields_table($tabstrip->get_active_tab_id(), $user['link_id'], $disabled_user_fields))
	{
		$tabstrip->add_html_element($cf_table);

		$tabstrip->add_html_element(new button($cmdSave, "javascript:_save('save_custom_fields')"));
	}

}else {
	require_once($tabstrip->get_active_tab_id());
}

$form->add_html_element($tabstrip);

echo $form->get_html();
?>
<script type="text/javascript">
function _save(task)
{
	document.forms[0].task.value = task;
	document.forms[0].submit();
}
</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
