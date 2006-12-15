<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.5 $ $Date: 2006/11/22 09:35:41 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once ("../../Group-Office.php");

load_basic_controls();
load_control('datatable');
$GO_HEADER['head'] ='';

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('projects');
require_once ($GO_LANGUAGE->get_language_file('projects'));
require_once ($GO_LANGUAGE->get_language_file('calendar'));


$page_title = $lang_modules['projects'];
require_once ($GO_MODULES->class_path."projects.class.inc");
$projects = new projects();

$task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';
$template_id = isset ($_REQUEST['template_id']) ? $_REQUEST['template_id'] : 0;

$return_to = isset ($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];



switch ($task) {
	case 'save_template' :
		//translate the given date stamp to unix time
		$template['name'] = smart_addslashes(trim($_POST['name']));
	
		if ($template_id > 0) {
			if ($template['name'] == '') {
				$feedback = $error_missing_field;
			} else {
				$existing_template = $projects->get_template_by_name($template['name']);
				$template['id'] = $template_id;
				if ($existing_template && $existing_template['id'] != $template_id) {
					$feedback = $pm_template_exists;
				}elseif (!$projects->update_template($template)) {						
					$feedback = $strSaveError;
				} else {
					if ($_POST['close'] == 'true') {
						header('Location: '.$return_to);
						exit ();
					}
				}
			}
		} else {
			if ($template['name'] == '') {
				$feedback = $error_missing_field;
			}
			elseif ($projects->get_template_by_name($template['name'])) {
				$feedback = $pm_template_exists;
			} else {
				$template['acl_read'] = $GO_SECURITY->get_new_acl('template read: '.$template['name']);
				$template['acl_write'] = $GO_SECURITY->get_new_acl('template write: '.$template['name']);
				if ($template['acl_read'] > 0 && $template['acl_write'] > 0) {
				
					$template['user_id'] = $GO_SECURITY->user_id;
					
					if ($GO_SECURITY->add_user_to_acl($GO_SECURITY->user_id, $template['acl_write'])) {
						if (!$template_id = $projects->add_template($template)) {
									
							$GO_SECURITY->delete_acl($template['acl_read']);
							$GO_SECURITY->delete_acl($template['acl_write']);
							$feedback = $strSaveError;
						} else {
							if ($_POST['close'] == 'true') {
								header('Location: '.$return_to);
								exit ();
							}
						}
					} else {
						$GO_SECURITY->delete_acl($template['acl_read']);
						$GO_SECURITY->delete_acl($template['acl_write']);
						$feedback = $strSaveError;
					}
				} else {
					$feedback = $strAclError;
				}
			}
		}
		break;
}
$link_back = $_SERVER['PHP_SELF'].'?template_id='.$template_id.'&return_to='.urlencode($return_to);

if ($template_id > 0) {
	$template = $projects->get_template($template_id);
	
	$tabstrip = new tabstrip('template_tabstrip_'.$template_id, $template['name']);
	$tabstrip->set_attribute('style','width:100%');
	
	$tabstrip->add_tab('properties', $strProperties);

	$write_permissions = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $template['acl_write']);
	$read_permissions = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $template['acl_read']);

	if (!$write_permissions && !$read_permissions) {
		header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
		exit ();
	}
	
	$tabstrip->add_tab('read_permissions', $strReadRights);
	$tabstrip->add_tab('write_permissions', $strWriteRights);
	
} else {
	$tabstrip = new tabstrip('template_tab', $pm_new_template);
}
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to(htmlspecialchars($return_to));

if ($template_id == 0 || $task == 'save_template') {
	$write_permissions = true;
	$read_permissions = true;

	$template['name'] = isset ($_POST['name']) ? smart_stripslashes($_POST['name']) : '';
	$template['user_id'] = $GO_SECURITY->user_id;

}



$form = new form('projects_form');
$form->add_html_element(new input('hidden', 'close', 'false'));
$form->add_html_element(new input('hidden', 'template_id', $template_id, false));
$form->add_html_element(new input('hidden', 'task', '', false));
$form->add_html_element(new input('hidden', 'return_to',$return_to));



if ($template_id > 0 && $write_permissions) {

	$menu = new button_menu();
	$menu->add_button('enter_data_big', 
		$pm_new_template_event, 
		'template_event.php?template_id='.$template_id.'&return_to='.urlencode($link_back));
			
	if($tabstrip->get_active_tab_id() == 'properties')
	{
		$datatable = new datatable('pm_templates');
		$GO_HEADER['head'] = $datatable->get_header();

		$menu->add_button('delete_big', $cmdDelete, $datatable->get_delete_handler());

		if($datatable->task == 'delete')
		{
			foreach($datatable->selected as $template_event_id)
			{
				$projects->delete_template_event($template_event_id);
			}
		}		
	}
	$form->add_html_element($menu);
}


$GO_HEADER['body_arguments'] = 'onload="document.forms[0].name.focus();"';

require_once ($GO_THEME->theme_path."header.inc");

switch ($tabstrip->get_active_tab_id()) {
	case 'read_permissions' :
		$tabstrip->innerHTML .= get_acl($template['acl_read']);
		$tabstrip->add_html_element(new html_element('br'));
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));
		break;

	case 'write_permissions' :
		$tabstrip->innerHTML .= get_acl($template['acl_write']);
		$tabstrip->add_html_element(new html_element('br'));
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));
		break;

	default :
		
		if (isset($feedback))
		{
		  $p = new html_element('p', $feedback);
		  $p->set_attribute('class','Error');
		  $tabstrip->add_html_element($p);
		}
		
		$table = new table();
		$row = new table_row();
		
		$row->add_cell(new table_cell($strName.':*'));		

		if ($write_permissions) {
			$input = new input('text', 'name', $template['name']);
			$input->set_attribute('maxlength','50');
			$input->set_attribute('style','width:250px;');
			$row->add_cell(new table_cell($input->get_html()));			
		} else {
			$row->add_cell(new table_cell(htmlspecialchars($template['name'])));
		}
		
		$table->add_row($row);
		
		if ($template_id > 0) {
			$row = new table_row();
			$row->add_cell(new table_cell($strOwner.':'));
			$row->add_cell(new table_cell(show_profile($template['user_id'])));
			$table->add_row($row);
		}
		
		$tabstrip->add_html_element($table);
		
		if($template_id > 0)
		{
			$datatable->add_column(new table_heading($strName));
			$datatable->add_column(new table_heading($strType));
			
			$count = $projects->get_template_events($template_id);
			
			if($count)
			{
				while($projects->next_record())
				{
					$row = new table_row($projects->f('id'));
					$row->set_attribute('ondblclick', "javascript:document.location='template_event.php?template_event_id=".$projects->f('id')."&return_to=".urlencode($link_back)."'");
					$row->add_cell(new table_cell($projects->f('name')));					
					$type = $projects->f('todo')  == '1' ? $cal_todo : $cal_event;
					$row->add_cell(new table_cell($type));				
					
					
					
					$datatable->add_row($row);		
				}
			}else
			{
				$row = new table_row();
				$cell = new table_cell($pm_no_template_events);
				$row->add_cell($cell);
				$datatable->add_row($row);		
			}
			
			$tabstrip->add_html_element($datatable);
		}			
		if ($write_permissions) {
			$tabstrip->add_html_element(new button($cmdOk, "javascript:_save('save_template', 'true');"));
			$tabstrip->add_html_element(new button($cmdApply, "javascript:_save('save_template', 'false')"));
		}
		
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));				
		break;
}


$form->add_html_element($tabstrip);
echo $form->get_html();
?>
<script type="text/javascript">
function _save(task, close)
{
	document.projects_form.task.value = task;
	document.projects_form.close.value = close;
	document.projects_form.submit();
}
</script>
<?php
require_once ($GO_THEME->theme_path."footer.inc");
?>
