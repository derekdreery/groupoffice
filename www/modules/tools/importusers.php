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

//$file = '/home/mschering/Desktop/users.csv';
$delimiter=';';
$quote='"';

$enabled='0';


require_once("../../Group-Office.php");
require_once($GO_LANGUAGE->get_language_file('users'));


$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('tools');

load_basic_controls();



$fields = array(
'first_name'=>$strFirstName,
'middle_name'=>$strMiddleName,
'last_name'=>$strLastName,
'initials'=>$strInitials,
'title'=>$strTitle,
'birthday'=>$strBirthday,
'email'=>$strEmail,
'home_phone'=>$strPhone,
'work_phone'=>$strWorkphone,
'fax'=>$strFax,
'cellular'=>$strCellular,
'country_id'=>$strCountry,
'state'=>$strState,
'city'=>$strCity,
'zip'=>$strZip,
'address'=>$strAddress,
'address_no'=>$strAddressNo,
'company'=>$strCompany,
'department'=>$strDepartment,
'function'=>$strFunction,
'work_country_id'=>$strWorkCountry,
'work_state'=>$strWorkState,
'work_city'=>$strWorkCity,
'work_zip'=>$strWorkZip,
'work_address'=>$strWorkAddress,
'work_address_no'=>$strWorkAddressNo,
'work_fax'=>$strWorkFax,
'homepage'=>$strHomepage,
'sex'=>$strSex
);

$datatypes=array();
$cf_fields = array();


if(isset($GO_MODULES->modules['custom_fields']))
{
	require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
	$cf = new custom_fields();
	$cf2 = new custom_fields();

	$cf->get_categories(8);

	while($cf->next_record())
	{
		$cf2->get_fields($cf->f('id'));
		while($cf2->next_record())
		{
			$cf_fields['col_'.$cf2->f('id')]=$cf2->f('name');

			$datatypes['col_'.$cf2->f('id')]=$cf2->f('datatype');
		}
	}
}

//$record = fgetcsv($fp, 4096, $delimiter,$quote);

$task = isset($_POST['task']) ? $_POST['task'] : '';


if(isset($_FILES['csvfile']))
{
	$tmpfile = $GO_CONFIG->tmpdir.uniqid(time()).'.csv';

	move_uploaded_file($_FILES['csvfile']['tmp_name'], $tmpfile);
	$_SESSION['csvfile']=$tmpfile;

	$task='match';

}



$form = new form('users_settings_form');
$form->add_html_element(new input('hidden', 'task', '', false));
$form->add_html_element(new input('hidden', 'close', '', false));



if(isset($_SESSION['csvfile']) && $task == 'match')
{
	$fp = fopen($_SESSION['csvfile'],'r');

	if(!$record = fgetcsv($fp, 2024, $delimiter,$quote))
	{
		die('Failed to read CSV!');
	}


	$table = new table();

	$indexes=explode(',',$GO_CONFIG->get_setting('users_import_indexes'));

	$index=0;
	foreach($fields as $key=>$name)
	{
		$row = new table_row();

		$row->add_cell(new table_cell($name.':'));

		$default = isset($indexes[$index]) ? $indexes[$index] : -1;
		$value = isset($_POST['gofields'][$key]) ? $_POST['gofields'][$key] : $default;
		$select = new select("gofields[$key]", $value);
		$select->add_value('-1','Don\'t import');

		for($i=0;$i<count($record);$i++)
		{
			$select->add_value($i, $record[$i]);
		}
		$row->add_cell(new table_cell($select->get_html()));
		$table->add_row($row);
		$index++;
	}

	foreach($cf_fields as $key=>$name)
	{
		$row = new table_row();

		$row->add_cell(new table_cell($name.':'));
		$default = isset($indexes[$index]) ? $indexes[$index] : -1;
		$value = isset($_POST['cffields'][$key]) ? $_POST['cffields'][$key] : $default;
		$select = new select("cffields[$key]", $value);
		$select->add_value('-1','Don\'t import');

		for($i=0;$i<count($record);$i++)
		{
			$select->add_value($i, $record[$i]);
		}
		$row->add_cell(new table_cell($select->get_html()));
		$table->add_row($row);
		$index++;
	}

	$form->add_html_element($table);


	$form->add_html_element(new button($cmdOk, 'javascript:save(\'save\', \'true\');'));
}elseif($task=='save')
{
	$fp = fopen($_SESSION['csvfile'],'r');

	if(!$record = fgetcsv($fp, 2024, $delimiter,$quote))
	{
		die('Failed to read CSV!');
	}
	
	$postfields = isset($_POST['cffields']) ? array_merge($_POST['gofields'], $_POST['cffields']) : $_POST['gofields'];
	$indexes=implode(',',$postfields);
	$GO_CONFIG->save_setting('users_import_indexes', $indexes);
	
	$import_count=0;
	$fail_count=0;
	$count=0;
	$error ='';
	while($record = fgetcsv($fp, 4096, $delimiter,$quote))
	{
		$user=array();

		foreach($_POST['gofields'] as $key=>$index)
		{
			if($index!=-1)
			{
				switch($key)
				{
					case 'sex':
						if($record[$index]=='Mevrouw')
						{
							$user['sex']='F';
						}else {
							$user['sex']='M';
						}
						break;
					case 'birthday':
						$user['birthday']= date_to_db_date($record[$index]);

						break;
					default;
					$user[$key]=addslashes($record[$index]);
					break;
				}

			}
		}

		//$user['email']='test@intermesh.nl';

		$modules_read = array_map('trim', explode(',',$GO_CONFIG->register_modules_read));
		$modules_write = array_map('trim', explode(',',$GO_CONFIG->register_modules_write));
		$user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GO_CONFIG->register_user_groups)));
		$visible_user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GO_CONFIG->register_visible_user_groups)));

		$password = $GO_USERS->random_password();
		$user['password']=md5($password);
		$user['enabled']=$enabled;


		$user['username'] = strtolower(str_replace(' ','',$user['first_name'][0].$user['last_name']));
		
		if(empty($user['email']))
		{
			$user['email']='secretariaat@nvvh.com';
		}


		if (empty($user['first_name']))
		{
			$feedback = 'Fist name missing';
		}elseif(empty($user['last_name']))
		{
			$feedback = 'Last name missing';
		}elseif(!validate_email($user['email']))
		{
			$feedback = $error_email;
		}elseif($GO_USERS->get_user_by_username($user['username']))
		{
			$feedback = $error_username_exists;
		/*}elseif($GO_USERS->email_exists($user['email']))
		{
			$feedback = $error_email_exists;
		*/	
		}else
		{
			$user['link_id']=$GO_LINKS->get_link_id();

			if($user_id = $GO_USERS->add_user($user, $user_groups, $visible_user_groups, $modules_read, $modules_write	))
			{
				if(isset($_POST['cffields']))
				{
					$cf_record=array();
					$cf_record['link_id']=$user['link_id'];
					foreach($_POST['cffields'] as $key=>$index)
					{
						if($index!=-1)
						{
							switch($datatypes[$key])
							{
								case 'checkbox':
									//$cf_record[$key]= !empty($record[$index]) ? '1' : '0';
									$cf_record[$key]= $record[$index]=='WAAR' ? '1' : '0';
									break;

								case 'date':
									$cf_record[$key]= date_to_db_date($record[$index]);
									break;

								case 'number':
									$cf_record[$key]= number_to_phpnumber($record[$index]);
									break;

								default:
									$cf_record[$key]=addslashes($record[$index]);
									break;
							}

						}
					}
					//var_dump($cf_record);
					$cf->insert_row('cf_8', $cf_record);
				}


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
					$user['sex']=isset($user['sex']) ? $user['sex'] : 'M';
					$registration_mail_body = str_replace("%beginning%", $sir_madam[$user['sex']], $registration_mail_body);
					// If $title is not set, then use $sex (sir_madam) instead for $title.
					$registration_mail_body = str_replace("%title%", (!empty($user['title']) ? $user['title'] : $sir_madam[$user['sex']] ), $registration_mail_body);
					$registration_mail_body = str_replace("%last_name%", smart_stripslashes($user['last_name']), $registration_mail_body);
					if(!isset($user['middle_name']))
					{
						$user['middle_name']='';
					}
					$registration_mail_body = str_replace("%middle_name%", smart_stripslashes($user['middle_name']), $registration_mail_body);
					$registration_mail_body = str_replace("%first_name%", smart_stripslashes($user['first_name']), $registration_mail_body);
					$registration_mail_body = str_replace("%username%",smart_stripslashes($user['username']), $registration_mail_body);
					$registration_mail_body = str_replace("%password%",smart_stripslashes($password), $registration_mail_body);
					$registration_mail_body = str_replace("%full_url%",'<a href="'.$GO_CONFIG->full_url.'">'.$GO_CONFIG->full_url.'</a>', $registration_mail_body);

					//sendmail($user['email'], $GO_CONFIG->webmaster_email, $GO_CONFIG->title, $registration_mail_subject, $registration_mail_body,'3','text/HTML');
					//sendmail('mschering@intermesh.nl', $GO_CONFIG->webmaster_email, $GO_CONFIG->title, $registration_mail_subject, $registration_mail_body,'3','text/HTML');
				}
				$import_count++;
			}else {
				$feedback = $strSaveError;
			}
		}

		if(isset($feedback))
		{
			$error .= '<b>Warning: </b>Record number '.$count.' failed to import.<br />Reason:'.$feedback.'<br />';
			$error .= implode($delimiter, $record);
			$error .= nl2br(var_export($user,true));
			$error .= '<hr>';

			$fail_count++;
		}

		$count++;
	}

	$error = 'Number of records:'.$count.'<br />'.
	'Succesfull:'.$import_count.'<br />'.
	'Failed:'.$fail_count.'<hr />'.
	$error;

	require_once($GO_THEME->theme_path."header.inc");
	echo $error;
	$button = new button($cmdClose, 'javascript:document.location=\'index.php\';');
	echo $button->get_html();
	require_once($GO_THEME->theme_path."footer.inc");
	exit();

}else {

	$form->add_html_element(new html_element('h2','Select CSV File:'));

	$form->set_attribute('enctype','multipart/form-data');

	$form->add_html_element(new input('file','csvfile',''));
	$form->add_html_element(new button($cmdOk, 'javascript:document.forms[0].submit();'));
	$form->add_html_element(new button($cmdClose, 'javascript:document.location=\'index.php\';'));
}










require_once($GO_THEME->theme_path."header.inc");
echo $form->get_html();
?>
<script type="text/javascript">
function save(task, close)
{
	document.users_settings_form.task.value=task;
	document.users_settings_form.close.value=close;
	document.users_settings_form.submit();
}
</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
?>
