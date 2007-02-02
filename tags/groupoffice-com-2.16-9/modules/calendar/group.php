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
require_once ("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('calendar');
require_once ($GO_LANGUAGE->get_language_file('calendar'));


load_basic_controls();
load_control('datatable');
load_control('select_users');

require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
$cal = new calendar();

$task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';
$group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] : 0;

$return_to = (isset($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];

if ($task == 'save_group')
{
	$group['name'] = smart_addslashes(trim($_POST['name']));
	if ($group['name'] != "") {
		if (validate_input($group['name'])) {
			if ($group_id == '0') {
				if (!$cal->get_group_by_name($group['name'])) {
				
				$group['acl_write'] = $GO_SECURITY->get_new_acl('cal_groups.acl_write');				
				$GO_SECURITY->add_user_to_acl($GO_SECURITY->user_id, $group['acl_write']);
				
				$group_id= $cal->add_group($group);
					if (!$group_id) {
						$feedback = $strSaveError;
					} else {
						$cal->add_group_admin($group_id, $GO_SECURITY->user_id);
						
						if ($_POST['close'] == 'true') {
							header('Location: '.$GO_MODULES->url);
							exit ();
						}
					}
				} else {
					$feedback = $cal_group_exists;
				}
			} else {
				$existing_group = $cal->get_group_by_name($group['name']);

				if ($existing_group && $existing_group['id'] != $group_id) {
					$feedback =$cal_group_exists;
				} else {
					$group['id'] = $group_id;
					$cal->update_group($group);

					if ($_POST['close'] == 'true') {
						header('Location: '.$return_to);
						exit ();
					}
				}
			}
		} else {
			$feedback = $invalid_chars.": \\ / ? & \\";
		}

	} else {
		$feedback = $error_missing_field;
	}		
}

$link_back = $_SERVER['PHP_SELF'].'?group_id='.$group_id.'&return_to='.urlencode($return_to);


if ($group_id > 0) {
	$group = $cal->get_group($group_id);
	$title = $group['name'];
} else {	
	$title = $cal_new_group;
	$group['name'] = '';
	$group['book'] = '0';
	$group_id = 0;
}

$datatable = new datatable('custom_fields');


$GO_HEADER['head'] = $datatable->get_header();
$GO_HEADER['head'] .= select_users::get_header();
$GO_HEADER['body_arguments'] = 'onload="document.group_form.name.focus();" onkeypress="javascript:executeOnEnter(event, \'save_group(true)\')"';
require_once ($GO_THEME->theme_path."header.inc");



$tabstrip = new tabstrip('group_tabstrip', $title);
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to('index.php');

if($group_id > 0)
{
	$tabstrip->add_tab('properties',$strProperties);
	$tabstrip->add_tab('acl_write',$strWriteRights);
	$tabstrip->add_tab('admins',$cal_group_admins);
}


$form = new form('group_form');
$form->add_html_element(new input('hidden','group_id', $group_id, false));
$form->add_html_element(new input('hidden','task','',false));
$form->add_html_element(new input('hidden','close'));
$form->add_html_element(new input('hidden', 'return_to', $return_to));
$form->add_html_element(new input('hidden', 'link_back', $link_back));



if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $tabstrip->add_html_element($p);
}

switch($tabstrip->get_active_tab_id())
{
	case 'admins':
		if($datatable->task == 'delete')
		{
			foreach($datatable->selected as $user_id)
			{
				$cal->delete_group_admin($group_id, $user_id);
			}
		}

		$menu = new button_menu();
		$menu->add_button('user_add', $cal_add_admin, 'field.php?group_id='.$group_id.'&return_to='.urlencode($link_back));
		$menu->add_button('delete_big', $cmdDelete, $datatable->get_delete_handler());
		$form->add_html_element($menu);
		
		/*
		$datatable->add_column(new table_heading($strName));
		
		
		if($cal->get_group_admins($group_id))
		{
			while($cal->next_record())
			{
				$row = new table_row($cal->f('user_id'));
				$row->add_cell(new table_cell(show_profile($cal->f('user_id'))));			
				$datatable->add_row($row);
			}
		}else
		{
			$row = new table_row();
			$cell = new table_cell($cal_no_admins);
			$row->add_cell($cell);
			$datatable->add_row($row);
		}
		$tabstrip->add_html_element($datatable);*/
		
		$select_users = new select_users('admins');
		if($select_users->task == 'add')
		{
			foreach($select_users->selected as $user_id)
			{
				$cal->add_group_admin($group_id, $user_id);
			}
		}elseif($select_users->task == 'delete')
		{
			foreach($select_users->selected as $user_id)
			{
				$cal->delete_group_admin($group_id, $user_id);
			}
		}
		
		if($cal->get_resource_group_admins($group_id))
		{
			while($cal->next_record())
			{
				$select_users->add_user($cal->f('user_id'));
			}
		}
			
		$tabstrip->add_html_element($select_users);
		
		$tabstrip->add_html_element(new html_element('br'));
		
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));
		
		
	break;
	
	case 'acl_write':
		$tabstrip->innerHTML .= get_acl($group['acl_write']);
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));
	break;
	
	default:
	
		if($datatable->task == 'delete')
		{
			foreach($datatable->selected as $field_id)
			{
				$cal->delete_custom_field($group_id, $field_id);
			}
		}
		
	if($group_id > 0 )
	{
		$menu = new button_menu();
		$menu->add_button('add', $cal_add_field, 'field.php?group_id='.$group_id.'&return_to='.urlencode($link_back));
		$menu->add_button('delete_big', $cmdDelete, $datatable->get_delete_handler());

		$form->add_html_element($menu);
	}
	
	$table = new table();
	$row = new table_row();
	$row->add_cell(new table_cell($strName.':*'));

	$input = new input('text','name', $group['name']);
	$input->set_attribute('maxlength','50');
	$input->set_attribute('size','30');

	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	$tabstrip->add_html_element($table);

	if($group_id > 0)
	{
		$tabstrip->add_html_element(new button($cmdOk, 'javascript:save_group(true)'));
	}
	$tabstrip->add_html_element(new button($cmdApply, 'javascript:save_group(false)'));
	$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));

	if($group_id > 0 )
	{
		$datatable->add_column(new table_heading($strName));
		$datatable->add_column(new table_heading($strType));

		if( $group['custom_fields'] != '' && trim($group['custom_fields']) != '<fields></fields>')
		{
			$fields = $cal->get_custom_fields($group_id);
			foreach(	$fields as $inputNode)
			{		
				$row = new table_row($inputNode->get_attribute('id'));		
				$row->set_attribute('ondblclick', "javascript:document.location='field.php?group_id=".
					$group_id."&field_id=".$inputNode->get_attribute('id')."&return_to=".urlencode($link_back)."';");
				$row->add_cell(new table_cell($inputNode->get_attribute('name')));
				$row->add_cell(new table_cell($cal_field_types[$inputNode->get_attribute('type')]));
				$datatable->add_row($row);
			}		
		}else
		{
			$row = new table_row();		
			$cell = new table_cell($cal_no_custom_fields);
			$cell->set_attribute('colspan','99');
			$row->add_cell($cell);
			$datatable->add_row($row);
		}
		$tabstrip->add_html_element($datatable);
	}
	break;
}
$form->add_html_element($tabstrip);
echo $form->get_html();
?>
<script type="text/javascript">

function save_group(close)
{
	document.group_form.close.value=close;
  document.group_form.task.value='save_group';
  document.group_form.submit();
}

function add_field()
{
	document.group_form.task.value='add_field';
  document.group_form.submit();
}

</script>
<?php
require_once ($GO_THEME->theme_path."footer.inc");
