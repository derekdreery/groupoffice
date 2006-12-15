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

if (isset($_REQUEST['delete_contact_id']) && $_REQUEST['delete_contact_id'] > 0)
{
	$contact = $ab->get_contact($_REQUEST['delete_contact_id']);
	if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $contact['acl_write']))
	{
		$ab->add_contact_to_company($_REQUEST['delete_contact_id'], 0);
	}else
	{
		$feedback = $strAccessDenied;
	}
}

$datatable->add_column(new table_heading($strName, 'name'));
$datatable->add_column(new table_heading($strEmail, 'email'));
$datatable->add_column(new table_heading($strPhone, 'home_phone'));
$datatable->add_column(new table_heading($strWorkphone, 'work_phone'));

$count = $ab->get_company_contacts($company_id, $datatable->sort_index, $datatable->sql_sort_order, $datatable->start, $datatable->offset);
$str_count = $count == 1 ? $count.' '.$ab_employee_count_single : $count.' '.$ab_employee_count_multiple;

$datatable->set_pagination($count);

$div = new html_element('div', $str_count);
$div->set_attribute('style','width:100%;text-align:right;');

if ($count > 0)
{
	while($ab->next_record())
	{
	 	$mail_name = format_name($ab->f('last_name'), $ab->f('first_name'), $ab->f('middle_name'), 'first_name');
		$full_email = ($ab->f("email") != "") ? '"'.$mail_name.'" <'.$ab->f('email').'>' : '';
		$name = format_name($ab->f('last_name'), $ab->f('first_name'), $ab->f('middle_name'));
		
		$row = new table_row($ab->f('id'));
		$row->set_attribute('ondblclick', "document.location='contact.php?contact_id=".$ab->f("id")."&return_to=".urlencode($link_back)."'");
		$cell = new table_cell(htmlspecialchars($name));
		if ($ab->f('color') != '')
		{
			$cell->set_attribute('style','color: '.$ab->f('color'));
		}
		$row->add_cell($cell);
		$row->add_cell(new table_cell(mail_to(empty_to_stripe(addslashes($full_email)), empty_to_stripe($ab->f('email')),'normal',true, $ab->f('id'))));
		$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('home_phone')))));
		$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('work_phone')))));
		$datatable->add_row($row);
	}
}else
{
	$row = new table_row();
	$cell = new table_cell($ab_no_employees);
	$cell->set_attribute('colspan','99');
	$row->add_cell($cell);
	$datatable->add_row($row);
}
$tabstrip->add_html_element($datatable);
$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."'"));
?>

<script type="text/javascript">

function _sort(field, direction)
{
	document.company_form.new_sort_field.value=field;
	document.company_form.new_sort_order.value=direction;
	document.company_form.task.value='sort';	
	document.company_form.submit();
}

function delete_contact(id)
{
	document.company_form.delete_contact_id.value=id;
	document.company_form.submit();
}

function change_list(first, max_rows)
{
	document.company_form.first.value=first;
	document.company_form.max_rows.value=max_rows;
	document.company_form.submit();
}
</script>
