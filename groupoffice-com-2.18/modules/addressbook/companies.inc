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


if($datatable->task == 'delete')
{
	if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_write']))
	{
		foreach($datatable->selected as $company_id)
		{
			$ab->delete_company($company_id);
		}
	}else
	{
		$feedback = $strAccessDenied;
	}	
}

if ($task == 'move_to_addressbook')
{
	$move_ab = $ab->get_addressbook($_POST['move_addressbook_id']);

	if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $move_ab['acl_write']) &&
	  $GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_write']))
	{
		foreach($datatable->selected as $company_id)
		{
		  $ab->move_company_to_addressbook($company_id, $_POST['move_addressbook_id']);
		}    
	} else
	{
		$feedback = $strAccessDenied;
	}
}


if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $form->add_html_element($p);
}

$table = new table();
$row = new table_row();
$cell = new table_cell();
$cell->set_attribute('style','white-space:nowrap');

if ($ab->get_user_addressbooks($GO_SECURITY->user_id) > 1)
{
	$row->add_cell(new table_cell($ab_addressbook.':'));
	
	$move_addressbooks = new select('move_addressbook_id', '0');
	$move_addressbooks->set_attribute('onchange','javascript:move_to_addressbook();');
	$move_addressbooks->add_value('0', $ab_move_to_ab);
	
	$view_addressbooks = new select('addressbook_id', $addressbook_id);
	$view_addressbooks->set_attribute('onchange','javascript:document.forms[0].submit();');
	
	while ($ab->next_record())
	{
		$view_addressbooks->add_value($ab->f('id'), $ab->f('name'));
		$move_addressbooks->add_value($ab->f('id'), $ab->f('name'));
	}
	$row->add_cell(new table_cell($view_addressbooks->get_html()));
	$cell->add_html_element($move_addressbooks);
}else
{
	$form->add_html_element(new input('hidden', 'addressbook_id',$addressbook_id));
	$row->add_cell(new table_cell('&nbsp;'));
}

$row->add_cell($cell);


$datatable->add_column(new table_heading($strName, 'name'));
$datatable->add_column(new table_heading($strCity, 'city'));
$datatable->add_column(new table_heading($strEmail, 'email'));
$datatable->add_column(new table_heading($strPhone, 'phone'));
$datatable->add_column(new table_heading($strHomepage, 'homepage'));

$count = $ab->get_companies($addressbook_id, $datatable->sort_index, $datatable->sql_sort_order, $datatable->start, $datatable->offset);

$datatable->set_pagination($count);

$str_count = $count == 1 ? $ab_companies_count_single : $ab_companies_count;
$cell = new table_cell($count.' '.$str_count);
$cell->set_attribute('style','width:100%;text-align:right');
$cell->set_attribute('class','small');
$row->add_cell($cell);
$table->add_row($row);

$form->add_html_element($table);



if ($count > 0)
{
	while($ab->next_record())
	{	
		$full_email = $ab->f('email') == '' ? '' : '"'.$ab->f('name').'" <'.$ab->f('email').'>';
					
		$row = new table_row($ab->f('id'));
		$row->set_attribute('ondblclick', "document.location='company.php?company_id=".$ab->f("id")."&return_to=".urlencode($link_back)."'");
		$cell = new table_cell(htmlspecialchars($ab->f('name')));
		if ($ab->f('color') != '')
		{
			$cell->set_attribute('style','color: '.$ab->f('color'));
		}
		$row->add_cell($cell);
		$row->add_cell(new table_cell($ab->f('city')));
		$row->add_cell(new table_cell(mail_to(empty_to_stripe(addslashes($full_email)), empty_to_stripe($ab->f('email')),'normal',true, $ab->f('id'))));
		$row->add_cell(new table_cell(empty_to_stripe(htmlspecialchars($ab->f('phone')))));
		
		$hyperlink = new hyperlink($ab->f('homepage'), $ab->f('homepage'));
		$hyperlink->set_attribute('class', 'normal');
		$hyperlink->set_attribute('target', '_blank');
		
		$row->add_cell(new table_cell($hyperlink->get_html()));
		$datatable->add_row($row);		
	}	
}else
{
	$row = new table_row();
	$cell = new table_cell($ab_no_companies);
	$cell->set_attribute('colspan','99');
	$row->add_cell($cell);
	$datatable->add_row($row);
}
$form->add_html_element($datatable);

?>
<script type="text/javascript">

function move_to_addressbook()
{
  document.forms[0].task.value="move_to_addressbook";
  document.forms[0].submit();
}

</script>
