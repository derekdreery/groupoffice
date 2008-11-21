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

$GO_CONFIG->set_help_url($us_help_url);

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('users');

load_basic_controls();
load_control('htmleditor');

$return_to = 'index.php';

$task = isset($_POST['task']) ? $_POST['task'] : '';



$form = new form('users_settings_form');
$form->add_html_element(new input('hidden', 'task', '', false));
$form->add_html_element(new input('hidden', 'close', '', false));



	
$tabstrip = new tabstrip('users_settings', $cmdSettings,'200');
$tabstrip->set_attribute('style', 'width:100%;height:100%');
$tabstrip->set_return_to($return_to);

$tabstrip->add_tab('disabled_user_fields', $admin_disabled_user_fields);
$tabstrip->add_tab('registration_unconfirmed', $admin_registration_unconfirmed);
$tabstrip->add_tab('registration_confirmation', $admin_registration_confirmation);


switch($tabstrip->get_active_tab_id())
{

	
	case 'registration_confirmation':
		
		if($task == 'save')
		{
			$registration_confirmation = isset($_POST['registration_confirmation']) ? smart_addslashes($_POST['registration_confirmation']) : '';
			$registration_confirmation_subject = isset($_POST['registration_confirmation_subject']) ? smart_addslashes($_POST['registration_confirmation_subject']) : '';

			
			$GO_CONFIG->save_setting('registration_confirmation', $registration_confirmation);
			$GO_CONFIG->save_setting('registration_confirmation_subject', $registration_confirmation_subject);
			if($_POST['close']=='true')
			{
				header('Location: '.$return_to);
				exit();
			}
		}
		
		$registration_confirmation = $GO_CONFIG->get_setting('registration_confirmation');
		
		if(!$registration_confirmation)
		{
			$registration_confirmation='';
		}
		
		$registration_confirmation_subject = $GO_CONFIG->get_setting('registration_confirmation_subject');
		
		if(!$registration_confirmation_subject)
		{
			$registration_confirmation_subject='';
		}
		
		
		$table = new table();
		$table->set_attribute('style','width:100%;height:100%');
		
		$cell = new table_cell($admin_registration_confirmation_text);
		$row = new table_row();
		$row->add_cell($cell);
		$table->add_row($row);
		
		if(isset($feedback))
		{
			$cell = new table_cell($feedback);
			$cell->set_attribute('class','Error');
			$row = new table_row();
			$row->add_cell($cell);
			$table->add_row($row);
		}				
		
		$subtable = new table();
		$row = new table_row();
		$row->add_cell(new table_cell($admin_subject.': '));
		$input = new input('text', 'registration_confirmation_subject',$registration_confirmation_subject);
		$input->set_attribute('maxlength','100');
		$row->add_cell(new table_cell($input->get_html()));
		$subtable->add_row($row);
			
		$row = new table_row();
		$row->add_cell(new table_cell($subtable->get_html()));
		$table->add_row($row);
		
		
		$htmleditor = new htmleditor('registration_confirmation');
		$htmleditor->setAutoDataDefinitionsPath($GO_MODULES->modules['users']['path'].'auto_data_fields.inc');
		
		$htmleditor->Value=$registration_confirmation;
		
		$htmleditor->SetConfig('CustomConfigurationsPath', $GO_MODULES->modules['users']['url'].'fckconfig.js');
		$htmleditor->ToolbarSet='Users';
		$cell = new table_cell($htmleditor->CreateHtml());
		
		$cell->set_attribute('style', 'height:100%');		
		$row = new table_row();
		$row->add_cell($cell);
		$table->add_row($row);
		
		$row = new table_row();
		$cell = new table_cell();
		$cell->add_html_element(new button($cmdOk, 'javascript:save(\'save\', \'true\');'));
		$cell->add_html_element(new button($cmdApply, 'javascript:save(\'save\', \'false\');'));
		$cell->add_html_element(new button($cmdCancel, 'javascript:document.location=\''.$return_to.'\';'));
		$row->add_cell($cell);
		$table->add_row($row);
		
		$tabstrip->add_html_element($table);
		break;
		
	case 'registration_unconfirmed':
		if($task == 'save')
		{
			$registration_unconfirmed = isset($_POST['registration_unconfirmed']) ? smart_addslashes($_POST['registration_unconfirmed']) : '';
			$registration_unconfirmed_subject = isset($_POST['registration_unconfirmed_subject']) ? smart_addslashes($_POST['registration_unconfirmed_subject']) : '';

			
			$GO_CONFIG->save_setting('registration_unconfirmed', $registration_unconfirmed);
			$GO_CONFIG->save_setting('registration_unconfirmed_subject', $registration_unconfirmed_subject);
			if($_POST['close']=='true')
			{
				header('Location: '.$return_to);
				exit();
			}
		}
		
		$registration_unconfirmed = $GO_CONFIG->get_setting('registration_unconfirmed');
		
		if(!$registration_unconfirmed)
		{
			$registration_unconfirmed='';
		}
		
		$registration_unconfirmed_subject = $GO_CONFIG->get_setting('registration_unconfirmed_subject');
		
		if(!$registration_unconfirmed_subject)
		{
			$registration_unconfirmed_subject='';
		}
		
		
		$table = new table();
		$table->set_attribute('style','width:100%;height:100%');
		
		$cell = new table_cell($admin_registration_unconfirmed_text);
		$row = new table_row();
		$row->add_cell($cell);
		$table->add_row($row);
		
		if(isset($feedback))
		{
			$cell = new table_cell($feedback);
			$cell->set_attribute('class','Error');
			$row = new table_row();
			$row->add_cell($cell);
			$table->add_row($row);
		}				
		
		$subtable = new table();
		$row = new table_row();
		$row->add_cell(new table_cell($admin_subject.': '));
		$input = new input('text', 'registration_unconfirmed_subject',$registration_unconfirmed_subject);
		$input->set_attribute('maxlength','100');
		$row->add_cell(new table_cell($input->get_html()));
		$subtable->add_row($row);
			
		$row = new table_row();
		$row->add_cell(new table_cell($subtable->get_html()));
		$table->add_row($row);
		
		
		$htmleditor = new htmleditor('registration_unconfirmed');
		$htmleditor->setAutoDataDefinitionsPath($GO_MODULES->modules['users']['path'].'auto_data_fields.inc');
		
		$htmleditor->Value=$registration_unconfirmed;
		
		$htmleditor->SetConfig('CustomConfigurationsPath', $GO_MODULES->modules['users']['url'].'fckconfig.js');
		$htmleditor->ToolbarSet='Users';
		$cell = new table_cell($htmleditor->CreateHtml());
		
		$cell->set_attribute('style', 'height:100%');		
		$row = new table_row();
		$row->add_cell($cell);
		$table->add_row($row);
		
		$row = new table_row();
		$cell = new table_cell();
		$cell->add_html_element(new button($cmdOk, 'javascript:save(\'save\', \'true\');'));
		$cell->add_html_element(new button($cmdApply, 'javascript:save(\'save\', \'false\');'));
		$cell->add_html_element(new button($cmdCancel, 'javascript:document.location=\''.$return_to.'\';'));
		$row->add_cell($cell);
		$table->add_row($row);
		
		$tabstrip->add_html_element($table);
		break;
		
	
	default:

	
		
		if($task == 'save')
		{
			$fields = isset($_POST['fields']) ? $_POST['fields'] : array();
			$disabled_user_fields = implode(',', $fields);
			
			$GO_CONFIG->save_setting('disabled_user_fields', $disabled_user_fields);
			if($_POST['close']=='true')
			{
				header('Location: '.$return_to);
				exit();
			}
		}
		
		$disabled_user_fields = $GO_CONFIG->get_setting('disabled_user_fields');
		
		if(!$disabled_user_fields)
		{
			$disabled_user_fields=array();
		}else {
			$disabled_user_fields = explode(',', $disabled_user_fields);
		}
		
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
					$fields['col_'.$cf2->f('id')]=$cf2->f('name');				
				}
			}			
		}
			
		$tabstrip->add_html_element(new html_element('p', $disabled_user_fields_text));
		
		$table = new table();
		
		$row = new table_row();
		
		foreach($fields as $field=>$name)
		{
			$checkbox = new checkbox($field, 'fields[]',$field, $name, in_array($field, $disabled_user_fields));
			$row->add_cell(new table_cell($checkbox->get_html()));
		
			if(count($row->cells)==2)
			{
				$table->add_row($row);
				$row = new table_row();	
			}	
		}
		if(count($row->cells)==1)
		{
			$table->add_row($row);
		}
		
		$tabstrip->add_html_element($table);
		
		$tabstrip->add_html_element(new button($cmdOk, 'javascript:save(\'save\', \'true\');'));
		$tabstrip->add_html_element(new button($cmdApply, 'javascript:save(\'save\', \'false\');'));
		$tabstrip->add_html_element(new button($cmdCancel, 'javascript:document.location=\''.$return_to.'\';'));
	break;
}





$form->add_html_element($tabstrip);
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
