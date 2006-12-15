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

load_basic_controls();

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('projects', true);
require_once($GO_LANGUAGE->get_language_file('projects'));

require_once($GO_MODULES->class_path."projects.class.inc");
$projects = new projects();

$fee_id = isset($_REQUEST['fee_id']) ? $_REQUEST['fee_id'] : 0;
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

if ($task=='save')
{
	if($GO_MODULES->write_permission)
	{
		$fee['name'] = smart_addslashes(trim($_POST['name']));
		$fee['internal_value'] = number_to_phpnumber(trim(smart_addslashes($_POST['internal_value'])));
		$fee['external_value'] = number_to_phpnumber(trim(smart_addslashes($_POST['external_value'])));
		$fee['time'] = smart_addslashes($_POST['time']);
		
		if ($fee['name'] == '')
		{
			$feedback = $error_missing_field;
		}else
		{
			if ($fee_id>0)
			{
				$fee['id'] = $fee_id;
				if (!$projects->update_fee($fee))
				{
					$feedback = $strSaveError;
				}
			}else
			{
				$fee['acl_id'] = $GO_SECURITY->get_new_acl();
				
				if(!$projects->add_fee($fee))
				{
					$feedback = $strSaveError;
				}
			}
		}
		if(!isset($feedback) && $_POST['close'] == 'true')
		{
			header('Location: '.$GO_MODULES->url.'fees.php');
			exit();
		}
	}else
	{
		$title = $strAccessDenied;
		$require = $GO_CONFIG->root_path.'error_docs/403.inc';
	}
}
$GO_HEADER['body_arguments'] = 'onload="document.fee_form.name.focus();"';
require_once($GO_THEME->theme_path."header.inc");



$form = new form('fee_form');
$form->add_html_element(new input('hidden', 'task', $task, false));
$form->add_html_element(new input('hidden', 'fee_id', $fee_id, false));
$form->add_html_element(new input('hidden', 'close', 'false', false));
if ($fee_id > 0)
{
	$fee = $projects->get_fee($fee_id);
}else
{
	$fee["name"] = isset($_POST['name']) ? $_POST['name'] : '';
	$fee["time"] = isset($_POST['time']) ? $_POST['time'] : '60';
	$fee["internal_value"] = isset($_POST['internal_value']) ? smart_stripslashes($_POST['internal_value']) : '';
	$fee["external_value"] = isset($_POST['external_value']) ? smart_stripslashes($_POST['external_value']) : '';
}


$tabstrip = new tabstrip('fee_tabstrip', $pm_fees);
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to("fees.php");

if($fee_id>0)
{
	$tabstrip->add_tab('properties', $strProperties);
	$tabstrip->add_tab('acl_id', $strPermissions);
}

		
if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $tabstrip->add_html_element($p);
}

switch($tabstrip->get_active_tab_id())
{
	case 'acl_id':
		$tabstrip->innerHTML .= get_acl($fee['acl_id']);
	break;
	

	
	default:

		$table = new table();
		$row = new table_row();
		$row->add_cell(new table_cell($strName.':'));
		$input = new input('text','name', $fee['name']);
		$input->set_attribute('maxlenght','50');
		$row->add_cell(new table_cell($input->get_html()));
		$table->add_row($row);

		$row = new table_row();
		$row->add_cell(new table_cell($pm_internal_fee.': '.htmlspecialchars($_SESSION['GO_SESSION']['currency']).'&nbsp;'));
		$input = new input('text','internal_value', format_number($fee['internal_value']));
		$input->set_attribute('maxlength','50');
		$input->set_attribute('style','text-align:right');
		$input->set_attribute('onfocus','this.select();');
		$input->set_attribute('onblur', "javascript:this.value=number_format(this.value, 2, '".$_SESSION['GO_SESSION']['decimal_seperator']."', '".$_SESSION['GO_SESSION']['thousands_seperator']."');");
		$row->add_cell(new table_cell($input->get_html()));
		$table->add_row($row);
		
		$row = new table_row();
		$row->add_cell(new table_cell($pm_external_fee.': '.htmlspecialchars($_SESSION['GO_SESSION']['currency']).'&nbsp;'));
		$input = new input('text','external_value', format_number($fee['external_value']));
		$input->set_attribute('maxlength','50');
		$input->set_attribute('style','text-align:right');
		$input->set_attribute('onfocus','this.select();');
		$input->set_attribute('onblur', "javascript:this.value=number_format(this.value, 2, '".$_SESSION['GO_SESSION']['decimal_seperator']."', '".$_SESSION['GO_SESSION']['thousands_seperator']."');");
		$row->add_cell(new table_cell($input->get_html()));
		$table->add_row($row);
		
		$row = new table_row();
		$row->add_cell(new table_cell($pm_fee_time.': '));
		$select = new select('time',$fee['time']);
		for ($i=1;$i<=60;$i++)
		{
			$select->add_value($i,$i);
		}
		$row->add_cell(new table_cell($select->get_html().' '.$pm_mins));
		$table->add_row($row);
		
		
		$tabstrip->add_html_element($table);
		$tabstrip->add_html_element(new button($cmdOk, "javascript:document.fee_form.task.value='save';document.fee_form.close.value='true';document.fee_form.submit()"));
		$tabstrip->add_html_element(new button($cmdApply, "javascript:document.fee_form.task.value='save';document.fee_form.submit()"));
	break;
}
$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='fees.php';"));

$form->add_html_element($tabstrip);
echo $form->get_html();
require_once($GO_THEME->theme_path."footer.inc");
?>
