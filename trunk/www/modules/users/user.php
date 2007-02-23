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
require_once($GO_LANGUAGE->get_base_language_file('preferences'));
//require_once($GO_LANGUAGE->get_base_language_file('common'));

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('users');

load_basic_controls();
load_control('date_picker');

$user_id = isset($_REQUEST['user_id']) ? smart_addslashes($_REQUEST['user_id']) : 0;

$return_to = (isset($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];

$task = isset($_POST['task']) ? $_POST['task'] : '';

$GO_HEADER['head']='';

switch($task)
{
	case 'save_profile':

		//translate the given birthdayto gmt unix time
		$user['birthday'] = date_to_db_date(smart_addslashes($_POST['birthday']));
		$user['first_name'] = smart_addslashes(trim($_POST['first_name']));
		$user['middle_name'] = smart_addslashes(trim($_POST['middle_name']));
		$user['last_name'] = smart_addslashes(trim($_POST['last_name']));
		$user['initials'] = smart_addslashes($_POST["initials"]);
		$user['title'] = smart_addslashes($_POST["title"]);
		$user['email'] = smart_addslashes($_POST["email"]);
		$user['work_phone'] = smart_addslashes($_POST["work_phone"]);
		$user['home_phone'] = smart_addslashes($_POST["home_phone"]);
		$user['fax'] = smart_addslashes($_POST["fax"]);
		$user['cellular'] = smart_addslashes($_POST["cellular"]);
		$user['country_id'] = smart_addslashes($_POST["country_id"]);
		$user['state'] = smart_addslashes($_POST["state"]);
		$user['city'] = smart_addslashes($_POST["city"]);
		$user['zip'] = smart_addslashes($_POST["zip"]);
		$user['address'] = smart_addslashes($_POST["address"]);
		$user['address_no'] = smart_addslashes($_POST["address_no"]);
		$user['department'] = smart_addslashes($_POST["department"]);
		$user['function'] = smart_addslashes($_POST["function"]);
		$user['company'] = smart_addslashes($_POST["company"]);
		$user['work_country_id'] = smart_addslashes($_POST["work_country_id"]);
		$user['work_state'] = smart_addslashes($_POST["work_state"]);
		$user['work_city'] = smart_addslashes($_POST["work_city"]);
		$user['work_zip'] = smart_addslashes($_POST["work_zip"]);
		$user['work_address'] = smart_addslashes($_POST["work_address"]);
		$user['work_address_no'] = smart_addslashes($_POST["work_address_no"]);
		$user['work_fax'] = smart_addslashes($_POST["work_fax"]);
		$user['homepage'] = smart_addslashes($_POST["homepage"]);
		$user['enabled'] = isset($_POST["enabled"]) ? '1' : '0';
		//$user['language'] = isset($_POST['language']) ? smart_stripslashes($_POST['language']) : $GO_CONFIG->language;
		//$user['theme'] = isset($_POST['theme']) ? smart_stripslashes($_POST['theme']) : $GO_CONFIG->theme;
		$user['sex'] = $_POST['sex'];

		$user['id'] = $user_id;


		$existing_email_user = $GO_USERS->get_user_by_email($user['email']);
		if($user['id'] == 0)
		{
			$user['username'] = smart_addslashes(trim($_POST['username']));
			$pass1 = smart_stripslashes($_POST["pass1"]);
			$pass2 = smart_stripslashes($_POST["pass2"]);
		}

		if (
		((empty($user['username']) || empty($pass1) || empty ($pass2)) && $user['enabled']=='1' && $user['id']=='0') ||
		empty($user['first_name']) ||
		empty($user['last_name'])
		)
		{
			$feedback = $error_missing_field;
		}elseif($user_id==0 && !preg_match('/^[a-z0-9_-]*$/', $user['username']))
		{
			$feedback = $error_username;
		}elseif(!validate_email($user['email']))
		{
			$feedback = $error_email;
		}elseif($existing_email_user && ($user_id==0 || $existing_email_user['id'] != $user_id))
		{
			$feedback =  $error_email_exists;
		}elseif($user['id'] == 0 && $pass1 != $pass2)
		{
			$feedback = $error_match_pass;
		}else
		{
			if($user_id>0)
			{
				$old_user = $GO_USERS->get_user($user_id);
				if($old_user['password']=='' && $user['enabled']=='1')
				{
					$password = $GO_USERS->random_password();
					$user['password']=md5($password);
				}
				if (!$GO_USERS->update_user($user))
				{
					$feedback = $strSaveError;
				}else
				{
					if($old_user['password']=='' && $user['enabled']=='1')
					{
						$registration_mail_body = $GO_CONFIG->get_setting('registration_confirmation');
						$registration_mail_subject = $GO_CONFIG->get_setting('registration_confirmation_subject');

						if(!empty($registration_mail_body) && !empty($registration_mail_subject))
						{
							//send email to the user with password
							$registration_mail_body = str_replace("%beginning%", $sir_madam[$_POST['sex']], $registration_mail_body);
							// If $title is not set, then use $sex (sir_madam) instead for $title.
							$registration_mail_body = str_replace("%title%", ( ($user['title'] != '') ? $user['title'] : $sir_madam[$_POST['sex']] ), $registration_mail_body);
							$registration_mail_body = str_replace("%last_name%", smart_stripslashes($_POST['last_name']), $registration_mail_body);
							$registration_mail_body = str_replace("%middle_name%", smart_stripslashes($_POST['middle_name']), $registration_mail_body);
							$registration_mail_body = str_replace("%first_name%", smart_stripslashes($_POST['first_name']), $registration_mail_body);
							$registration_mail_body = str_replace("%username%",$old_user['username'], $registration_mail_body);
							$registration_mail_body = str_replace("%password%",smart_stripslashes($password), $registration_mail_body);
							$registration_mail_body = str_replace("%full_url%",'<a href="'.$GO_CONFIG->full_url.'">'.$GO_CONFIG->full_url.'</a>', $registration_mail_body);


							sendmail($user['email'], $GO_CONFIG->webmaster_email, $GO_CONFIG->title, $registration_mail_subject, $registration_mail_body,'3','text/HTML');
						}
					}
				}
			}else {
				if($user['enabled']=='1')
				{
					$password = $user['password'] = smart_stripslashes($_POST["pass1"]);
				}else{
					$password='';
				}

				$modules_read = array_map('trim', explode(',',$GO_CONFIG->register_modules_read));
				$modules_write = array_map('trim', explode(',',$GO_CONFIG->register_modules_write));
				$user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GO_CONFIG->register_user_groups)));
				$visible_user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GO_CONFIG->register_visible_user_groups)));

				$user['link_id']= $GO_LINKS->get_link_id();
				
				if ($user_id = $GO_USERS->add_user($user, $user_groups, $visible_user_groups, $modules_read, $modules_write	))
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
						$registration_mail_body = str_replace("%beginning%", $sir_madam[$_POST['sex']], $registration_mail_body);
						// If $title is not set, then use $sex (sir_madam) instead for $title.
						$registration_mail_body = str_replace("%title%", ( ($user['title'] != '') ? $user['title'] : $sir_madam[$_POST['sex']] ), $registration_mail_body);
						$registration_mail_body = str_replace("%last_name%", smart_stripslashes($_POST['last_name']), $registration_mail_body);
						$registration_mail_body = str_replace("%middle_name%", smart_stripslashes($_POST['middle_name']), $registration_mail_body);
						$registration_mail_body = str_replace("%first_name%", smart_stripslashes($_POST['first_name']), $registration_mail_body);
						$registration_mail_body = str_replace("%username%",smart_stripslashes($_POST['username']), $registration_mail_body);
						$registration_mail_body = str_replace("%password%",smart_stripslashes($password), $registration_mail_body);
						$registration_mail_body = str_replace("%full_url%",'<a href="'.$GO_CONFIG->full_url.'">'.$GO_CONFIG->full_url.'</a>', $registration_mail_body);

						sendmail($user['email'], $GO_CONFIG->webmaster_email, $GO_CONFIG->title, $registration_mail_subject, $registration_mail_body,'3','text/HTML');
					}

					//create Group-Office home directory
					$old_umask = umask(000);
					mkdir($GO_CONFIG->file_storage_path.'users/'.stripslashes($user['username']), $GO_CONFIG->create_mode);
					umask($old_umask);

					//confirm registration to the user and exit the script so the form won't load

					$feedback = $registration_success;

				}else {
					$feedback = $strSaveError;
				}

			}

			if ($_POST['close'] == 'true' && !isset($feedback))
			{
				header('Location: '.$return_to);
				exit();
			}
		}
		break;

	case 'save_custom_fields':
		require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
		$cf = new custom_fields();

		$cf->save_fields($_POST['user_tabstrip'], $_POST['link_id']);

		if ($_POST['close'] == 'true' && !isset($feedback))
		{
			header('Location: '.$return_to);
			exit();
		}
		break;
}

$link_back = $_SERVER['PHP_SELF'].'?user_id='.$user_id.'&return_to='.urlencode($return_to);


$form = new form('user_form');
$form->add_html_element(new input('hidden', 'user_id', $user_id, false));
$form->add_html_element(new input('hidden', 'return_to', $return_to));
$form->add_html_element(new input('hidden', 'close', false));
$form->add_html_element(new input('hidden', 'task', '', false));

if($user_id>0)
{
	$user = $GO_USERS->get_user($user_id);



	if (!$user)
	{
		$feedback = $strDataError;
	}
	$user['birthday'] = db_date_to_date($user['birthday']);

	$tabstrip = new tabstrip('user_tabstrip',$user_profile.' '.$user['username'],'160','user_form','vertical');
	$tabstrip->set_attribute('width','100%');
	$tabstrip->set_return_to($return_to);

	$tabstrip->add_tab('profile', $user_profile);
	
	
	
	$tabstrip->add_tab('links', $strLinks);


	
	
	$tabstrip->add_tab('permissions', $strPermissions);
	$tabstrip->add_tab('password', $admin_password);
	$tabstrip->add_tab('info', $ac_login_info);
	$tabstrip->add_tab('look', $pref_look);
	$tabstrip->add_tab('notations', $pref_notations);

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

	foreach($GO_MODULES->modules as $module)
	{
		$settings_include = $module['path'].$module['id'].'.admin.inc';
		if(file_exists($settings_include))
		{
			if(file_exists($GO_LANGUAGE->get_language_file($module['id'])))
			{
				require($GO_LANGUAGE->get_language_file($module['id']));
			}
			$module_name = isset($lang_modules[$module['id']]) ? $lang_modules[$module['id']] : $module['id'];
			$tabstrip->add_tab($settings_include, $module_name);
		}
	}
	
	
	
	$ll_link_back =$link_back;
	if(!strstr($ll_link_back, 'user_tabstrip'))
	{
		$ll_link_back=add_params_to_url($link_back,'user_tabstrip=links');
	}
	
	$menu = new button_menu();
	$menu->add_button('link', $strCreateLink, $GO_LINKS->search_link($user['link_id'], 8, 'opener.document.location=\''.$ll_link_back.'\';'));

	if($tabstrip->get_active_tab_id() == 'links')
	{
		load_control('links_list');
		$links_list = new links_list($user['link_id'], 'user_form', $ll_link_back);
		$GO_HEADER['head'] .= $links_list->get_header();

		$menu->add_button(
		'unlink',
		$cmdUnlink,
		$links_list->get_unlink_handler());

		$menu->add_button(
		'delete_big',
		$cmdDelete,
		$links_list->get_delete_handler());
	}
	
	if(isset($GO_MODULES->modules['filesystem']) && $GO_MODULES->modules['filesystem']['read_permission'])
	{
		$menu->add_button(
		'upload',
		$cmdAttachFile,
		$GO_MODULES->modules['filesystem']['url'].'link_upload.php?path=users/'.$user_id.'&link_id='.$user['link_id'].'&link_type=8&return_to='.urlencode($ll_link_back));



		//create contact directory with same permissions as project
		if(!file_exists($GO_CONFIG->file_storage_path.'users/'.$user_id))
		{
			mkdir_recursive($GO_CONFIG->file_storage_path.'users/'.$user_id);
		}
		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem();
		if(!$fs->find_share($GO_CONFIG->file_storage_path.'users/'.$user_id))
		{
			$fs->add_share(1, $GO_CONFIG->file_storage_path.'users/'.$user_id,'user',$GO_MODULES->modules['users']['acl_read'], $GO_MODULES->modules['users']['acl_write']);
		}
	}

	
	$form->add_html_element($menu);


}else {

	$tabstrip = new tabstrip('user_tabstrip',$user_profile);
	$tabstrip->set_attribute('width','100%');
	$tabstrip->set_return_to($return_to);

	$user['first_name'] = isset($_POST['first_name']) ?  smart_stripslashes(trim($_POST['first_name'])) : '';
	$user['middle_name'] = isset($_POST['middle_name']) ?  smart_stripslashes(trim($_POST['middle_name'])) : '';
	$user['last_name'] = isset($_POST['last_name']) ?  smart_stripslashes(trim($_POST['last_name'])) : '';
	$user['initials'] = isset($_POST['initials']) ? smart_stripslashes($_POST["initials"]) : '';
	$user['title'] = isset($_POST['title']) ? smart_stripslashes($_POST["title"]) : '';
	$user['birthday'] = isset($_POST['birthday']) ? smart_stripslashes($_POST["birthday"]) : '';
	$user['email'] = isset($_POST['email']) ? smart_stripslashes($_POST["email"]) : '';
	$user['home_phone'] = isset($_POST['home_phone']) ? smart_stripslashes($_POST["home_phone"]) : '';
	$user['work_phone'] = isset($_POST['work_phone']) ? smart_stripslashes($_POST["work_phone"]) : '';
	$user['fax'] = isset($_POST['fax']) ? smart_stripslashes($_POST["fax"]) : '';
	$user['cellular'] = isset($_POST['cellular']) ? smart_stripslashes($_POST["cellular"]) : '';
	$user['country_id'] = isset($_POST['country_id']) ? smart_addslashes($_POST["country_id"]) : $GO_CONFIG->default_country_id;
	$user['state'] = isset($_POST['state']) ? smart_stripslashes($_POST["state"]) : '';
	$user['city'] = isset($_POST['city']) ? smart_stripslashes($_POST["city"]) : '';
	$user['zip'] = isset($_POST['zip']) ? smart_stripslashes($_POST["zip"]) : '';
	$user['address'] = isset($_POST['address']) ? smart_stripslashes($_POST["address"]) : '';
	$user['address_no'] = isset($_POST['address_no']) ? smart_stripslashes($_POST["address_no"]) : '';
	$user['company'] = isset($_POST['company']) ? smart_stripslashes($_POST["company"]) : '';
	$user['department'] =  isset($_POST['department']) ? smart_stripslashes($_POST["department"]) : '';
	$user['function'] =  isset($_POST['function']) ? smart_stripslashes($_POST["function"]) : '';
	$user['work_country_id'] = isset($_POST['work_country_id']) ?  smart_addslashes($_POST["work_country_id"]) : $GO_CONFIG->default_country_id;
	$user['work_state'] = isset($_POST['work_state']) ? smart_stripslashes($_POST["work_state"]) : '';
	$user['work_city'] = isset($_POST['work_city']) ? smart_stripslashes($_POST["work_city"]) : '';
	$user['work_zip'] = isset($_POST['work_zip']) ? smart_stripslashes($_POST["work_zip"]) : '';
	$user['work_address'] = isset($_POST['work_address']) ? smart_stripslashes($_POST["work_address"]) : '';
	$user['work_address_no'] = isset($_POST['work_address_no']) ? smart_stripslashes($_POST["work_address_no"]) : '';
	$user['work_fax'] = isset($_POST['work_fax']) ? smart_stripslashes($_POST["work_fax"]) : '';
	$user['homepage'] = isset($_POST['homepage']) ? smart_stripslashes($_POST["homepage"]) : '';
	$user['sex'] = isset($_POST['sex']) ? smart_stripslashes($_POST["sex"]) : 'M';
	$user['language'] = isset($_POST['language']) ? smart_stripslashes($_POST['language']) : $GO_CONFIG->language;
	$user['theme'] = isset($_POST['theme']) ? smart_stripslashes($_POST['theme']) : $GO_CONFIG->theme;
	$user['username'] = isset($_POST['username']) ? smart_stripslashes($_POST['username']) : '';
	if($_SERVER['REQUEST_METHOD'] != 'POST')
	{
		$user['enabled'] = '1';
	}else
	{
		$user['enabled'] = isset($_POST["enabled"]) ? '1' : '0';
	}
}


if (intval($tabstrip->get_active_tab_id()) > 0) {
	$catagory_id = $tabstrip->get_active_tab_id();
	$active_tab_id = 'custom_fields';
} else {
	$active_tab_id = $tabstrip->get_active_tab_id();
}


if(file_exists($active_tab_id))
{
	require_once($active_tab_id);
}else {


	switch($active_tab_id)
	{
		case 'links' :
			$tabstrip->add_html_element($links_list);
		break;
		
		case 'custom_fields' :
			$GO_HEADER['head'] = date_picker::get_header();

			if(empty($user['link_id']))
			{
				$update_user['id'] = $user_id;
				$update_user['link_id'] = $user['link_id']= $GO_LINKS->get_link_id();
				$GO_USERS->update_profile($update_user);
			}

			$form->add_html_element(new input('hidden', 'link_id', $user['link_id']));
			if($cf_table = $cf->get_fields_table($tabstrip->get_active_tab_id(), $user['link_id']))
			{
				$tabstrip->add_html_element($cf_table);

				$tabstrip->add_html_element(new button($cmdOk, "javascript:_save('save_custom_fields', 'true');"));
				$tabstrip->add_html_element(new button($cmdApply, "javascript:_save('save_custom_fields', 'false')"));
			}
			$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."';"));
			break;

		case 'notations':
			if($task=='save')
			{
				$user['id'] = $user_id;
				$user['language'] = smart_addslashes($_POST['language']);
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
			}

			$table = new table();

			$row = new table_row();
			$row->add_cell(new table_cell($pref_language.':'));

			$select = new select('language', $user['language']);
			$languages = $GO_LANGUAGE->get_languages();
			foreach($languages as $language)
			{
				$select->add_value($language['code'], $language['description']);
			}
			$row->add_cell(new table_cell($select->get_html()));
			$table->add_row($row);

			$row = new table_row();
			$row->add_cell(new table_cell($pref_timezone.':'));

			$select = new select('timezone', $user['timezone']);
			$select->add_value('12','+12 GMT');
			$select->add_value('11.5','+11.5 GMT');
			$select->add_value('11','+11 GMT');
			$select->add_value('10.5','+10.5 GMT');
			$select->add_value('10','+10 GMT');
			$select->add_value('9.5','+9.5 GMT');
			$select->add_value('9','+9 GMT');
			$select->add_value('8.5','+8.5 GMT');
			$select->add_value('8','+8 GMT');
			$select->add_value('7.5','+7.5 GMT');
			$select->add_value('7','+7 GMT');
			$select->add_value('6.5','+6.5 GMT');
			$select->add_value('6','+6 GMT');
			$select->add_value('5.5','+5.5 GMT');
			$select->add_value('5','+5 GMT');
			$select->add_value('4.5','+4.5 GMT');
			$select->add_value('4','+4 GMT');
			$select->add_value('3.5','+3.5 GMT');
			$select->add_value('3','+3 GMT');
			$select->add_value('2.5','+2.5 GMT');
			$select->add_value('2','+2 GMT');
			$select->add_value('1.5','+1.5 GMT');
			$select->add_value('1','+1 GMT');
			$select->add_value('0','GMT');
			$select->add_value('-1','-1 GMT');
			$select->add_value('-1.5','-1.5 GMT');
			$select->add_value('-2','-2 GMT');
			$select->add_value('-2.5','-2.5 GMT');
			$select->add_value('-3','-3 GMT');
			$select->add_value('-3.5','-3.5 GMT');
			$select->add_value('-4','-4 GMT');
			$select->add_value('-4.5','-4.5 GMT');
			$select->add_value('-5','-5 GMT');
			$select->add_value('-5.5','-5.5 GMT');
			$select->add_value('-6','-6 GMT');
			$select->add_value('-6.5','-6.5 GMT');
			$select->add_value('-7','-7 GMT');
			$select->add_value('-7.5','-7.5 GMT');
			$select->add_value('-8','-8 GMT');
			$select->add_value('-8.5','-8.5 GMT');
			$select->add_value('-9','-9 GMT');
			$select->add_value('-9.5','-9.5 GMT');
			$select->add_value('-10','-10 GMT');
			$select->add_value('-10.5','-10.5 GMT');
			$select->add_value('-11','-11 GMT');
			$select->add_value('-11.5','-11.5 GMT');
			$select->add_value('-12','-12 GMT');

			$checkbox = new checkbox('DST', 'DST', '1', $adjust_to_dst, $_SESSION['GO_SESSION']['DST']);

			$row->add_cell(new table_cell($select->get_html().$checkbox->get_html()));
			$table->add_row($row);

			$row = new table_row();
			$row->add_cell(new table_cell($pref_date_format.':'));

			$select = new select('date_format', $user['date_format']);
			for ($i=0;$i<count($GO_CONFIG->date_formats);$i++)
			{
				$friendly[strpos($GO_CONFIG->date_formats[$i], 'Y')]=$strYear;
				$friendly[strpos($GO_CONFIG->date_formats[$i], 'm')]=$strMonth;
				$friendly[strpos($GO_CONFIG->date_formats[$i], 'd')]=$strDay;

				$strFriendly = $friendly[0].$user['date_seperator'].
				$friendly[1].$user['date_seperator'].
				$friendly[2];

				$select->add_value($GO_CONFIG->date_formats[$i], $strFriendly);
			}
			$row->add_cell(new table_cell($select->get_html()));
			$table->add_row($row);

			$row = new table_row();
			$row->add_cell(new table_cell($pref_date_seperator.':'));

			$select = new select('date_seperator', $user['date_seperator']);
			for ($i=0;$i<count($GO_CONFIG->date_seperators);$i++)
			{
				$select->add_value($GO_CONFIG->date_seperators[$i], $GO_CONFIG->date_seperators[$i]);
			}
			$row->add_cell(new table_cell($select->get_html()));
			$table->add_row($row);

			$row = new table_row();
			$row->add_cell(new table_cell($pref_time_format.':'));

			$select = new select('time_format', $user['time_format']);
			$select->add_value($GO_CONFIG->time_formats[0], $strTwentyfourHourFormat);
			$select->add_value($GO_CONFIG->time_formats[1], $strTwelveHourFormat);

			$row->add_cell(new table_cell($select->get_html()));
			$table->add_row($row);


			$row = new table_row();
			$row->add_cell(new table_cell($pref_first_weekday.':'));

			$select = new select('first_weekday', $user['first_weekday']);
			$select->add_value('0', $full_days[0]);
			$select->add_value('1', $full_days[1]);

			$row->add_cell(new table_cell($select->get_html()));
			$table->add_row($row);


			$row = new table_row();
			$row->add_cell(new table_cell($pref_thousands_seperator.':'));

			$input = new input('text', 'thousands_seperator', $user['thousands_seperator']);
			$input->set_attribute('style', 'width:50px;');
			$input->set_attribute('maxlength', '1');

			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);


			$row = new table_row();
			$row->add_cell(new table_cell($pref_decimal_seperator.':'));

			$input = new input('text', 'decimal_seperator', $user['decimal_seperator']);
			$input->set_attribute('style', 'width:50px;');
			$input->set_attribute('maxlength', '1');

			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);


			$row = new table_row();
			$row->add_cell(new table_cell($pref_currency.':'));

			$input = new input('text', 'currency', $user['currency']);
			$input->set_attribute('style', 'width:50px;');
			$input->set_attribute('maxlength', '3');

			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);

			$tabstrip->add_html_element($table);

			$tabstrip->add_html_element(new button($cmdOk, "javascript:_save('save', 'true')"));
			$tabstrip->add_html_element(new button($cmdApply, "javascript:_save('save', 'false')"));
			$tabstrip->add_html_element(new button($cmdClose, 'javascript:document.location=\''.$return_to.'\';'));
			break;

		case 'look':

			if($task=='save')
			{
				$user['id'] = $user_id;
				$user['max_rows_list'] = smart_addslashes($_POST['max_rows_list']);
				$user['start_module'] = smart_addslashes($_POST['start_module']);
				$user['sort_name'] =	smart_addslashes($_POST['sort_name']);
				$user['theme'] = smart_addslashes($_POST['theme']);
				$user['use_checkbox_select'] = isset($_POST['use_checkbox_select']) ? '1' : '0';
			}

			$GO_USERS->update_profile($user);

			$table = new table();

			if ($GO_CONFIG->allow_themes == true)
			{
				$row = new table_row();
				$row->add_cell(new table_cell($pref_theme.':'));

				$select = new select('theme', $user['theme']);
				$themes = $GO_THEME->get_themes();
				foreach($themes as $theme)
				{
					$select->add_value($theme, $theme);
				}
				$row->add_cell(new table_cell($select->get_html()));
				$table->add_row($row);
			}

			$row = new table_row();
			$row->add_cell(new table_cell($pref_startmodule.':'));

			$select = new select('start_module', $user['start_module']);
			$GO_MODULES->get_modules();
			while ($GO_MODULES->next_record())
			{
				if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $GO_MODULES->f('acl_read')) ||
				$GO_SECURITY->has_permission($GO_SECURITY->user_id, $GO_MODULES->f('acl_write'))
				)
				{
					$language_file = $GO_LANGUAGE->get_language_file($GO_MODULES->f('id'));
					if(file_exists($language_file))
					{
						require_once($language_file);
					}
					$lang_var = isset($lang_modules[$GO_MODULES->f('id')]) ?
					$lang_modules[$GO_MODULES->f('id')] : $GO_MODULES->f('id');
					$select->add_value($GO_MODULES->f('id'), $lang_var);
				}
			}
			$row->add_cell(new table_cell($select->get_html()));
			$table->add_row($row);


			$row = new table_row();
			$row->add_cell(new table_cell($pref_max_rows_list.':'));

			$select = new select('max_rows_list', $user['max_rows_list']);

			$select->add_value('10','10');
			$select->add_value('15','15');
			$select->add_value('20','20');
			$select->add_value('25','25');
			$select->add_value('30','30');
			$select->add_value('50','50');
			$select->add_value('75','75');
			$select->add_value('100','100');
			$row->add_cell(new table_cell($select->get_html()));
			$table->add_row($row);

			$row = new table_row();
			$row->add_cell(new table_cell($pref_name_order.':'));

			$select = new select('sort_name', $user['sort_name']);
			$select->add_value('first_name', $strFirstName);
			$select->add_value('last_name', $strLastName);

			$row->add_cell(new table_cell($select->get_html()));
			$table->add_row($row);

			$tabstrip->add_html_element($table);

			$div = new html_element('div');
			$div->add_html_element(new checkbox('use_checkbox_select','use_checkbox_select','1', $pref_use_checkbox_select, $user['use_checkbox_select']));
			$tabstrip->add_html_element($div);


			$tabstrip->add_html_element(new button($cmdOk, "javascript:_save('save', 'true')"));
			$tabstrip->add_html_element(new button($cmdApply, "javascript:_save('save', 'false')"));
			$tabstrip->add_html_element(new button($cmdClose, 'javascript:document.location=\''.$return_to.'\';'));
			break;

		default:



			$birthday_picker = new date_picker('birthday', $_SESSION['GO_SESSION']['date_format'], $user['birthday']);
			$GO_HEADER['head'] = $birthday_picker ->get_header();

			if (isset($feedback))
			{
				$p = new html_element('p', $feedback);
				$p->set_attribute('class','Error');
				$tabstrip->add_html_element($p);
			}


			$maintable = new table();
			$mainrow = new table_row();

			$table = new table();

			$row = new table_row();
			$cell = new table_cell();
			$checkbox = new checkbox('enabled', 'enabled','1',$users_enabled, ($user['enabled'] == '1'));
			if($user_id==0)
			{
				$checkbox->set_attribute('onclick', 'javascript:show_pass(this.checked);');
			}
			$cell->add_html_element($checkbox);
			$cell->set_attribute('colspan','2');
			$row->add_cell($cell);
			$table->add_row($row);

			if($user_id==0)
			{
				$row = new table_row();
				$row->add_cell(new table_cell($strUsername.'*:'));
				$input = new input('text','username',$user['username']);
				$input->set_attribute('style','width:280px');
				$input->set_attribute('maxlength','50');
				$row->add_cell(new table_cell($input->get_html()));
				$table->add_row($row);


				$row = new table_row('passrow1');
				$row->add_cell(new table_cell($admin_password.':'));
				$input = new input('password', 'pass1', '',false);
				$input->set_attribute('style','width:280px');
				$row->add_cell(new table_cell($input->get_html()));
				$table->add_row($row);

				$row = new table_row('passrow2');
				$row->add_cell(new table_cell($admin_confirm_password.':'));
				$input = new input('password', 'pass2', '',false);
				$input->set_attribute('style','width:280px');
				$row->add_cell(new table_cell($input->get_html()));
				$table->add_row($row);
			}




			$row = new table_row();
			$cell = new table_cell('&nbsp;');
			$cell->set_attribute('colspan','2');
			$row->add_cell($cell);
			$table->add_row($row);


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
			$input->set_attribute('maxlength','50');
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
			$cell = new table_cell('&nbsp;');
			$cell->set_attribute('colspan','2');
			$row->add_cell($cell);
			$table->add_row($row);

			$row = new table_row();
			$row->add_cell(new table_cell($strPhone.':'));
			$input = new input('text','work_phone', $user['work_phone']);
			$input->set_attribute('style','width:280px');
			$input->set_attribute('maxlength','20');
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);

			$row = new table_row();
			$row->add_cell(new table_cell($strFax.':'));
			$input = new input('text','work_fax', $user['work_fax']);
			$input->set_attribute('style','width:280px');
			$input->set_attribute('maxlength','20');
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);

			$row = new table_row();
			$row->add_cell(new table_cell($strHomepage.':'));
			$input = new input('text','homepage', $user['homepage']);
			$input->set_attribute('style','width:280px');
			$input->set_attribute('maxlength','20');
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);

			$cell = new table_cell($table->get_html());
			$cell->set_attribute('valign','top');

			$mainrow->add_cell($cell);
			$maintable->add_row($mainrow);

			$mainrow = new table_row();

			$tabstrip->add_html_element($maintable);

			$tabstrip->add_html_element(new button($cmdOk, "javascript:_save('save_profile', 'true')"));
			$tabstrip->add_html_element(new button($cmdApply, "javascript:_save('save_profile', 'false')"));
			$tabstrip->add_html_element(new button($cmdClose, 'javascript:document.location=\''.$return_to.'\';'));

			break;

		case 'permissions':

			if($task=='save')
			{
				$user_groups = isset($_POST['user_groups']) ? $_POST['user_groups'] : array();
				$modules_read =  isset($_POST['modules_read']) ? $_POST['modules_read'] : array();
				$modules_write =  isset($_POST['modules_write']) ? $_POST['modules_write'] : array();
				$visible_user_groups = isset($_POST['visible_user_groups']) ? $_POST['visible_user_groups'] : array();

				$GO_MODULES->get_modules();
				while ($GO_MODULES->next_record())
				{
					$could_read = $GO_SECURITY->has_permission($user['id'], $GO_MODULES->f('acl_read'));
					$can_read =  in_array($GO_MODULES->f('id'), $modules_read);

					if ($could_read && !$can_read)
					{
						$GO_SECURITY->delete_user_from_acl($user['id'], $GO_MODULES->f('acl_read'));
					}

					if ($can_read && !$could_read)
					{
						$GO_SECURITY->add_user_to_acl($user['id'], $GO_MODULES->f('acl_read'));
					}

					$could_write = $GO_SECURITY->has_permission($user['id'], $GO_MODULES->f('acl_write'));
					$can_write =  in_array($GO_MODULES->f('id'), $modules_write);

					if ($could_write && !$can_write)
					{
						$GO_SECURITY->delete_user_from_acl($user['id'], $GO_MODULES->f('acl_write'));
					}

					if ($can_write && !$could_write)
					{
						$GO_SECURITY->add_user_to_acl($user['id'], $GO_MODULES->f('acl_write'));
					}
				}

				$user = $GO_USERS->get_user($user['id']);


				$GO_GROUPS->get_groups();
				$groups2 = new $GLOBALS['go_groups_class']();
				while($GO_GROUPS->next_record())
				{
					$is_in_group = $groups2->is_in_group($user['id'], $GO_GROUPS->f('id'));
					$should_be_in_group = in_array($GO_GROUPS->f('id'), $user_groups);

					if ($is_in_group && !$should_be_in_group)
					{
						$groups2->delete_user_from_group($user['id'], $GO_GROUPS->f('id'));
					}

					if (!$is_in_group && $should_be_in_group)
					{
						$groups2->add_user_to_group($user['id'], $GO_GROUPS->f('id'));
					}



					$group_is_visible = $GO_SECURITY->group_in_acl($GO_GROUPS->f('id'), $user['acl_id']);
					$group_should_be_visible = in_array($GO_GROUPS->f('id'), $visible_user_groups);

					if ($group_is_visible && !$group_should_be_visible)
					{
						$GO_SECURITY->delete_group_from_acl($GO_GROUPS->f('id'), $user['acl_id']);
					}

					if (!$group_is_visible  && $group_should_be_visible)
					{
						$GO_SECURITY->add_group_to_acl($GO_GROUPS->f('id'), $user['acl_id']);
					}
				}
			}
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

			$tabstrip->add_html_element(new button($cmdOk, "javascript:_save('save', 'true')"));
			$tabstrip->add_html_element(new button($cmdApply, "javascript:_save('save', 'false')"));
			$tabstrip->add_html_element(new button($cmdClose, 'javascript:document.location=\''.$return_to.'\';'));


			break;

		case 'password':

			if($task =='save')
			{
				if(empty($_POST['pass1']))
				{
					$feedback = $error_missing_field;
				}elseif ($_POST['pass1'] != $_POST['pass2'])
				{
					$feedback = $error_match_pass;
				}else{
					$GO_USERS->update_password($user_id, smart_stripslashes($_POST['pass1']));
					$feedback = $admin_password_changed;
				}
			}

			if (isset($feedback))
			{
				$p = new html_element('p', $feedback);
				$p->set_attribute('class','Error');
				$tabstrip->add_html_element($p);
			}


			$table = new table();

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


			$tabstrip->add_html_element(new button($cmdOk, "javascript:_save('save', 'true')"));
			$tabstrip->add_html_element(new button($cmdApply, "javascript:_save('save', 'false')"));
			$tabstrip->add_html_element(new button($cmdClose, 'javascript:document.location=\''.$return_to.'\';'));


			break;

		case 'info':

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


			$tabstrip->add_html_element(new button($cmdClose, 'javascript:document.location=\''.$return_to.'\';'));

			break;
	}

}
$form->add_html_element($tabstrip);

require_once($GO_THEME->theme_path."header.inc");

echo $form->get_html();
?>
<script type="text/javascript">
function _save(task, close)
{
	document.forms[0].task.value = task;
	document.forms[0].close.value = close;
	document.forms[0].submit();
}

function show_pass(show)
{
	if(show)
	{
		document.getElementById('passrow1').style.display='table-row';
		document.getElementById('passrow2').style.display='table-row';
	}else
	{
		document.getElementById('passrow1').style.display='none';
		document.getElementById('passrow2').style.display='none';
	}
}
</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
