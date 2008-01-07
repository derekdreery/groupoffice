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

if (!isset($_SESSION['contacts_expanded']))
$_SESSION['contacts_expanded'][]=-1;


if($datatable->task == 'delete')
{
	if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_write']))
	{
		foreach($datatable->selected as $contact_id)
		{
			$ab->delete_contact($contact_id);
		}
	}else
	{
		$feedback = $strAccessDenied;
	}	
}

switch($task)
{
	case "move":
	if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_write']))
	{
		if (!isset($_POST['group_id']))
		{
			$_POST['group_id']="0";
		}

		foreach($datatable->selected as $contact_id)
		{
			$ab->move_contact_to_group($contact_id, $_POST['group_id']);
		}
	}else
	{
		$feedback = $strAccessDenied;
	}

	break;

	case "move_to_addressbook":
	$move_ab = $ab->get_addressbook($_POST['move_addressbook_id']);

	if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $move_ab['acl_write']) && 
	$GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_write']))
	
	{
		foreach($datatable->selected as $contact_id)
		{
			$ab->move_contact_to_addressbook($contact_id, $_POST['move_addressbook_id']);
		}
	}else
	{
		$feedback = $strAccessDenied;
	}

	break;

	case "expand":

	if (isset($_POST['expand_id']))
	{
		$key = array_search($_POST['expand_id'], $_SESSION['contacts_expanded']);
		if (!$key)
		{
			$_SESSION['contacts_expanded'][]=$_POST['expand_id'];
		}else
		{
			unset($_SESSION['contacts_expanded'][$key]);
		}
	}
	break;
}

$form->add_html_element(new input('hidden', 'expand_id'));


if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $form->add_html_element($p);
}

$table = new table();
$row = new table_row();

$cell = new table_cell();
$cell->set_attribute('style','width:100%;text-align:right');

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

$cell = new table_cell();
$cell->set_attribute('style', 'text-align:right');
	

$group_count = $ab->get_groups($addressbook_id);
if ($group_count > 0)
{
	$groups = array();

	$select = new select('group_id');
	$select->set_attribute('onchange','javascript:move_to_group();');
	
	$select->add_value('',$contacts_move_to_group);
	$select->add_value('0',$contacts_other);

	while ($ab->next_record())
	{
		$select->add_value($ab->f('id'), $ab->f('name'));
		$group['name'] = $ab->f('name');
		$group['id'] = $ab->f('id');
		$groups[] = $group;
	}
	if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_write']))
	{
		$cell->add_html_element($select);
	}
}
$groups[] = array('id'=>0, 'name'=>$contacts_other);
$group_count++;

$row->add_cell($cell);
$table->add_row($row);

$form->add_html_element($table);

$datatable->add_column(new table_heading($strName, 'name'));
$datatable->add_column(new table_heading($strEmail, 'email'));
$datatable->add_column(new table_heading($strPhone, 'home_phone'));
$datatable->add_column(new table_heading($strWorkphone, 'work_phone'));

if(isset($groups))
{
	for($i=0;$i<$group_count;$i++)
	{
		if (in_array($groups[$i]['id'], $_SESSION['contacts_expanded']) || $group_count == 1)
		{
			if($group_count > 1)
			{
				$row = new table_row();
				$row->set_attribute('class','groupRow');
				
				$img = new image('min_node');
				$img->set_attribute('style', 'border:0px;margin-right:5px;');				
		
				$hyperlink = new hyperlink("javascript:expand_group(".$groups[$i]['id'].");", $img->get_html().$groups[$i]['name']);
				
				$input = new input('checkbox', 'dummy', 'dummy');
				$input->set_attribute('onclick', "javascript:select_group('".$groups[$i]['id']."',this.checked);");
				
				$cell = new table_cell($input->get_html().$hyperlink->get_html());
				$cell->set_attribute('colspan','4');			

				$group_start_input = new input('hidden', 'group_start_'.$groups[$i]['id']);
				$cell->add_html_element($group_start_input);			


				if(isset($group_end_input))
				{
					$cell->add_html_element($group_end_input);			
				}
				
				$row->add_cell($cell);
				
				$datatable->add_row($row);			
			}
			if ($ab->get_contacts_group($addressbook_id, $groups[$i]['id'], $datatable->sort_index, $datatable->sql_sort_order) > 0)
			{
				while ($ab->next_record())
				{
					$mail_name = format_name($ab->f('last_name'), $ab->f('first_name'), $ab->f('middle_name'), 'first_name');
					$full_email = ($ab->f("email") != "") ? '"'.$mail_name.'" <'.$ab->f('email').'>' : '';
					$name = format_name($ab->f('last_name'), $ab->f('first_name'), $ab->f('middle_name'));
					
					$row = new table_row($ab->f("id"));
					$row->set_attribute('ondblclick', "document.location='contact.php?contact_id=".$ab->f("id")."&return_to=".urlencode($link_back)."';");
					
					$cell = new table_cell($name);
					if ($ab->f('color') != '')
					{
						$cell->set_attribute('style','color: '.$ab->f('color').';');
					}
					$row->add_cell($cell);
					$row->add_cell(new table_cell(mail_to(empty_to_stripe(addslashes($full_email)), empty_to_stripe($ab->f("email")),'normal',true, $ab->f("id"))));
					$row->add_cell(new table_cell(empty_to_stripe($ab->f("home_phone"))));
					$row->add_cell(new table_cell(empty_to_stripe($ab->f("work_phone"))));
					$datatable->add_row($row);					
				}
				
				$group_end_input = new input('hidden', 'group_end_'.$groups[$i]['id']);
			}else
			{
				$row = new table_row();
				$cell = new table_cell($contacts_empty_group);
				$cell->set_attribute('colspan','99');
				$row->add_cell($cell);
				$datatable->add_row($row);
			}
		}else
		{
			$row = new table_row();
			$row->set_attribute('class','groupRow');
			
			$img = new image('plus_node');
			$img->set_attribute('style', 'border:0px;margin-right:5px;');				
	
			$hyperlink = new hyperlink("javascript:expand_group(".$groups[$i]['id'].");", $img->get_html().$groups[$i]['name']);
			
			$input = new input('checkbox', 'dummy', 'dummy');
			$input->set_attribute('onclick', "javascript:select_group('".$groups[$i]['id']."',this.checked);");
			
			$cell = new table_cell($input->get_html().$hyperlink->get_html());
			$cell->set_attribute('colspan','4');			

			$group_start_input = new input('hidden', 'group_start_'.$groups[$i]['id']);
			$cell->add_html_element($group_start_input);			


			if(isset($group_end_input))
			{
				$cell->add_html_element($group_end_input);			
			}
			
			$row->add_cell($cell);
			
			$datatable->add_row($row);			

		}
	}
}

$form->add_html_element($datatable);
?>
<script type="text/javascript" language="javascript">
<!--

function select_group(group_id, check)
{
	var add = false;

	for (var i = 0; i < document.forms[0].elements.length; i++)
	{
		if (document.forms[0].elements[i].name == 'group_start_'+group_id)
		{
			add = true;
		}

		if (document.forms[0].elements[i].name == 'group_end_'+group_id)
		{
			add = false;
		}

		if(document.forms[0].elements[i].type == 'checkbox' && document.forms[0].elements[i].name != 'dummy' && add==true)
		{
			document.forms[0].elements[i].checked = check;
			document.forms[0].elements[i].onclick();
		}
	}
}

function move_to_group()
{
	document.forms[0].task.value="move";
	document.forms[0].submit();
}

function move_to_addressbook()
{
	document.forms[0].task.value="move_to_addressbook";
	document.forms[0].submit();
}

function expand_group(group_id)
{
	document.forms[0].expand_id.value = group_id;
	document.forms[0].task.value = "expand";
	document.forms[0].submit();
}

//-->
</script>
