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
$GO_MODULES->authenticate('groups');
require_once ($GO_LANGUAGE->get_language_file('groups'));

load_basic_controls();

$task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';
$group_users = isset ($_REQUEST['group_users']) ? $_REQUEST['group_users'] : array ();

$group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] : 0;


if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if (isset ($_REQUEST['search_field'])) {
		SetCookie("user_search_field", $_REQUEST['search_field'], time() + 3600 * 24 * 365, "/", "", 0);
		$_COOKIE['user_search_field'] = $_REQUEST['search_field'];
	}

	switch ($task) {
		case 'delete_users' :
			for ($i = 0; $i < count($group_users); $i ++) {
				if ($group_users[$i] != $GO_SECURITY->user_id && $group_users[$i] != 1) {
					$GO_GROUPS->delete_user_from_group($group_users[$i], $group_id);
				}
			}
			break;

		case 'save_add_users' :
			for ($i = 0; $i < count($group_users); $i ++) {
				if (!$GO_GROUPS->is_in_group($group_users[$i], $group_id)) {
					$GO_GROUPS->add_user_to_group($group_users[$i], $group_id);
				}
			}
			break;

		case 'save_group_name' :
			$group_name = smart_addslashes(trim($_POST['group_name']));

			if ($group_name != "") {
				if (validate_input($group_name)) {
					if ($group_id == '0') {
						if (!$GO_GROUPS->get_group_by_name($group_name)) {
						$group_id= $GO_GROUPS->add_group($GO_SECURITY->user_id, $group_name);
							if (!$group_id) {
								$feedback = $add_group_fail;
							} else {
								if ($_POST['close'] == 'true') {
									header('Location: '.$GO_MODULES->url);
									exit ();
								}
							}
						} else {
							$feedback = $add_group_exists;
						}
					} else {
						$existing_group = $GO_GROUPS->get_group_by_name($group_name);

						if ($existing_group && $existing_group['id'] != $group_id) {
							$feedback =$add_group_exists;
						} else {
							$GO_GROUPS->update_group($group_id, $group_name);

							if ($_POST['close'] == 'true') {
								header('Location: '.$GO_MODULES->url);
								exit ();
							}
						}
					}
				} else {
					$feedback = $invalid_chars.": \\ / ? & \\";
				}

			} else {
				$feedback = $add_group_no_name;
			}
			break;
	}

}

if ($group_id > 0) {
	$group = $GO_GROUPS->get_group($group_id);
	$group_name = $group['name'];
} else {	
	$group_name = $groups_new_group;
	$group_id = 0;
}

if ($group_id == $GO_CONFIG->group_everyone) {
	$feedback = $groups_everyone;
	$enabled = false;
} else {
	$enabled = true;
}

if($task == 'add_users')
{
	$GO_HEADER['body_arguments'] = 'onload="document.group_form.query.focus();"';
}else
{
	$GO_HEADER['body_arguments'] = 'onload="document.group_form.group_name.focus();" onkeypress="javascript:executeOnEnter(event, \'save_group_name()\')"';
}
require_once ($GO_THEME->theme_path."header.inc");

$tabstrip = new tabstrip('group_tabstrip', $group_name);
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to('index.php');

$form = new form('group_form');
$form->add_html_element(new input('hidden','group_id', $group_id, false));
$form->add_html_element(new input('hidden','task'));
$form->add_html_element(new input('hidden','close'));

if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $tabstrip->add_html_element($p);
}

if ($task == 'add_users') {
	$search_field = isset ($_POST['search_field']) ? $_POST['search_field'] :'';

	$select = new select('search_field', $search_field);
	foreach ($GO_USERS->get_search_fields() as $fields) {
		$select->add_value($fields[0], $fields[1]);
	}
	
	$table = new table();
	
	$row = new table_row();
	$row->add_cell(new table_cell($select->get_html()));
	
	$query = isset($_REQUEST['query']) ? smart_stripslashes($_REQUEST['query']) : '';
	$input = new input('text','query', $query);
	$input->set_attribute('size','30');
	$input->set_attribute('maxlength','255');
	
	$row->add_cell(new table_cell($input->get_html()));
	
	$table->add_row($row);
	
	$tabstrip->add_html_element($table);
	
	$tabstrip->add_html_element(new button($cmdSearch, 'javascript:add_users()'));
	$tabstrip->add_html_element(new button($cmdShowAll, "javascript:document.group_form.query.value='';add_users()"));
	$tabstrip->add_html_element(new button($cmdCancel, 'javascript:return_to_group();'));

	if (isset($_POST['query'])) {
					
		if ($_POST['query'] != '') {
			$GO_USERS->search('%'.smart_addslashes($_REQUEST['query']).'%', smart_addslashes($search_field), $GO_SECURITY->user_id);
		} else {
			$GO_USERS->get_authorized_users($GO_SECURITY->user_id);
		}
		
		$select = new select('group_users[]','',true);
		$select->set_attribute('style', 'width: 250px;height: 200px;display:block;');
		
		while ($GO_USERS->next_record()) {
			$middle_name = $GO_USERS->f('middle_name') == '' ? '' : $GO_USERS->f('middle_name').' ';
			$name = $GO_USERS->f('first_name').' '.$middle_name.$GO_USERS->f('last_name');
			
			$select->add_value($GO_USERS->f('id'),$name);
		}
		
		$tabstrip->add_html_element($select);
		
		$tabstrip->add_html_element(new button($cmdAdd, 'javascript:save_add_users()'));
	}
} else {

	$table = new table();
	$row = new table_row();
	$row->add_cell(new table_cell($strName.':*'));
	
	$input = new input('text','group_name', $group_name);
	$input->set_attribute('maxlength','50');
	$input->set_attribute('size','30');
	if(!$enabled)
	{
		$input->set_attribute('disabled','disabled');
	}
	
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	if ($group_id > 0) {
		
		$row = new table_row();
		$cell = new table_cell($groups_members.':');
		$cell->set_attribute('valign','top');
		$row->add_cell($cell);
		
		$cell = new table_cell();		
		
		$select = new select('group_users[]','',true);
		$select->set_attribute('style', 'width: 250px;height: 200px;display:block;');
		
		if(!$enabled)
		{
			$select->set_attribute('disabled','disabled');
		}
		
		$GO_GROUPS->get_users_in_group($group_id, "name", "ASC");
		
		while ($GO_GROUPS->next_record()) {
			$middle_name = $GO_GROUPS->f('middle_name') == '' ? '' : $GO_GROUPS->f('middle_name').' ';
			$name = $GO_GROUPS->f('first_name').' '.$middle_name.$GO_GROUPS->f('last_name');
			
			$select->add_value($GO_GROUPS->f('id'),$name);
		}
		
		$cell->add_html_element($select);
		if ($enabled) {				
			$cell->add_html_element(new button($cmdAdd, 'javascript:add_users()'));
			$cell->add_html_element(new button($cmdDelete, 'javascript:delete_users()'));
		}	
		$row->add_cell($cell);
		$table->add_row($row);	
	}
	$tabstrip->add_html_element($table);
	if ($enabled) {
		$tabstrip->add_html_element(new button($cmdOk, 'javascript:save_close_group_name()'));
		$tabstrip->add_html_element(new button($cmdApply, 'javascript:save_group_name()'));
	}
	$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='index.php'"));
}

$form->add_html_element($tabstrip);
echo $form->get_html();
?>
<script type="text/javascript">

function delete_users()
{
  document.group_form.task.value='delete_users';
  document.group_form.submit();
}

function save_add_users()
{
  document.group_form.task.value='save_add_users';
  document.group_form.submit();
}
function add_users()
{
  document.group_form.task.value='add_users';
  document.group_form.submit();
}
function save_group_name()
{
  document.group_form.task.value='save_group_name';
  document.group_form.submit();
}
function save_close_group_name()
{
  document.group_form.close.value='true';
  document.group_form.task.value='save_group_name';
  document.group_form.submit();
}
function return_to_group()
{
  document.group_form.task.value='';
  document.group_form.submit();
}
</script>
<?php
require_once ($GO_THEME->theme_path."footer.inc");
?>
