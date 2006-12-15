<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.1 $ $Date: 2006/11/28 12:30:49 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once ("../../Group-Office.php");

$GO_HEADER['head'] ='';

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('timeregistration');
require_once ($GO_LANGUAGE->get_language_file('timeregistration'));

load_basic_controls();
load_control('date_picker');
load_control('tooltip');


//check for the addressbook module
$ab_module = isset ($GO_MODULES->modules['addressbook']) ? $GO_MODULES->modules['addressbook'] : false;
if ($ab_module && $ab_module['read_permission']) {
	require_once ($ab_module['class_path'].'addressbook.class.inc');
	$ab = new addressbook();
} else {
	$ab_module = false;
}

require_once ($GO_MODULES->class_path."timeregistration.class.inc");
$projects = new timeregistration();

$task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';
$project_id = isset ($_REQUEST['project_id']) ? $_REQUEST['project_id'] : 0;

$return_to = isset ($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$link_back = (isset ($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];

switch ($task) {
	case 'activate_linking':
		$link_project = $projects->get_project($project_id);
		if(empty($link_project['link_id']))
		{
			$update_project['id'] = $project_id;
			$update_project['link_id'] = $link_project['link_id'] = $GO_LINKS->get_link_id();
			$projects->update_project($update_project);
		}

		$GO_LINKS->activate_linking($link_project['link_id'], 5, $link_project['name'], $link_back);
		header('Location: '.$GO_CONFIG->host.'link.php');
		exit();
		break;

	case 'create_link':
		if($link = $GO_LINKS->get_active_link())
		{
			$link_project = $projects->get_project($project_id);
			$link_id = $link_project['link_id'];
			if(empty($link_project['link_id']))
			{
				$update_project['id'] = $project_id;
				$update_project['link_id'] = $link_id = $GO_LINKS->get_link_id();
				$projects->update_project($update_project);
			}
			$GO_LINKS->add_link($link['id'], $link['type'], $link_id, 8);
			$GO_LINKS->deactivate_linking();
			header('Location: '.$link['return_to']);
			exit();
		}
		break;
	case 'save_project' :
		//translate the given date stamp to unix time

		$project['name'] = smart_addslashes(trim($_POST['name']));
		$project['comments'] =smart_addslashes($_POST['comments']);


		if ($project_id > 0) {
			if ($project['name'] == '') {
				$feedback = $error_missing_field;
			}else {
				$existing_project = $projects->get_project_by_name($project['name']);
				$project['id'] = $project_id;
				if ($existing_project && $existing_project['id'] != $project_id) {
					$feedback = $pm_project_exists;
				}else
				{
					$old_project = $projects->get_project($project_id);
					
					if(isset($_POST['link_calendar']) && $old_project['calendar_id']==0)
					{
						require($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
						$cal = new calendar();
						$project['calendar_id'] = $cal->add_calendar($GO_SECURITY->user_id, $project['name'], 8,20);
						
						
						$projects->get_hours(0,0,0,$project_id);
						
						while($projects->next_record())
						{						
							$projects->add_booking_to_calendar($projects->Record, $project['calendar_id']);
						}
				
						
						
					}elseif(!isset($_POST['link_calendar']) && $old_project['calendar_id']>0)
					{
						require($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
						$cal = new calendar();
						$cal->delete_calendar($old_project['calendar_id']);
						$project['calendar_id']=0;
					}
					
					if (!$projects->update_project($project)) {

						$feedback = $strSaveError;
					} else {
						if ($_POST['close'] == 'true') {
							header('Location: '.$return_to);
							exit ();
						}
					}
				}
			}
		} else {
			if ($project['name'] == '') {
				$feedback = $error_missing_field;
			}
			elseif ($projects->get_project_by_name($project['name'])) {
				$feedback = $pm_project_exists;
			} else {
				if(isset($_POST['link_calendar']))
				{
					require($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
					$cal = new calendar();
					$project['calendar_id'] = $cal->add_calendar($GO_SECURITY->user_id, $project['name'], 8,20);
				}
				$project['acl_read'] = $GO_SECURITY->get_new_acl('Project read: '.$project['name']);
				$project['acl_write'] = $GO_SECURITY->get_new_acl('Project write: '.$project['name']);
				$project['acl_book'] = $GO_SECURITY->get_new_acl('Project book: '.$project['name']);
				if ($project['acl_read'] > 0 && $project['acl_write'] > 0) {

					$project['user_id'] = $GO_SECURITY->user_id;

					if($link = $GO_LINKS->get_active_link())
					{
						$project['link_id'] = $GO_LINKS->get_link_id();
					}

					if ($GO_SECURITY->add_user_to_acl($GO_SECURITY->user_id, $project['acl_write'])) {
						if (!$project_id = $projects->add_project($project)) {

							$GO_SECURITY->delete_acl($project['acl_read']);
							$GO_SECURITY->delete_acl($project['acl_write']);
							$feedback = $strSaveError;
						} else {

							if(isset($_POST['template_id']) && $_POST['template_id'] > 0)
							{
								$projects->apply_template($_POST['template_id'], $project_id, $_POST['calendar_id']);
							}

							if(isset($link) && $link)
							{
								$GO_LINKS->add_link($link['id'], $link['type'], $project['link_id'], 5);
								$GO_LINKS->deactivate_linking();
							}
							if ($_POST['close'] == 'true') {
								header('Location: '.$return_to);
								exit ();
							}
						}
					} else {
						$GO_SECURITY->delete_acl($project['acl_read']);
						$GO_SECURITY->delete_acl($project['acl_write']);
						$feedback = $strSaveError;
					}
				} else {
					$feedback = $strAclError;
				}
			}
		}
		break;

	case 'copy_project':
		$project_id = $projects->copy_project($project_id);
		break;

	case 'save_custom_fields':
		require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
		$cf = new custom_fields();


		$cf->save_fields($_POST['project_tabstrip_'.$project_id], $_POST['link_id']);

		if ($_POST['close'] == 'true') {
			header('Location: '.$return_to);
			exit ();
		}
		break;

}


$link_back = $_SERVER['PHP_SELF'].'?project_id='.$project_id.'&return_to='.urlencode($return_to);

$pm_settings = $projects->get_settings($GO_SECURITY->user_id);

if ($project_id > 0) {
	$project = $projects->get_project($project_id);

	if(empty($project['link_id']))
	{
		$update_project['id'] = $project_id;
		$update_project['link_id'] = $project['link_id'] = $GO_LINKS->get_link_id();
		$projects->update_project($update_project);
	}

	
	$tabstrip = new tabstrip('project_tabstrip_'.$project_id, $project['name']);
	$tabstrip->set_attribute('style','width:100%');

	$tabstrip->add_tab('properties', $strProperties);

	$write_permissions = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $project['acl_write']);
	$read_permissions = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $project['acl_read']);

	if (!$write_permissions && !$read_permissions) {

		if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $project['acl_book']))
		{
			header('Location: book.php?project_id='.$project_id.'&return_to='.urlencode($return_to));
			exit ();
		}else {
			header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
			exit ();
		}
	}

	$tabstrip->add_tab('load', $pm_load);

	if(isset($GO_MODULES->modules['custom_fields']))
	{
		require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
		$cf = new custom_fields();

		if($cf->get_authorized_categories(5, $GO_SECURITY->user_id))
		{
			while($cf->next_record())
			{
				$tabstrip->add_tab($cf->f('id'), $cf->f('name'));
			}
		}
	}


	$tabstrip->add_tab('links', $strLinks);

	$tabstrip->add_tab('book_permissions', $pm_book_rights);
	$tabstrip->add_tab('read_permissions', $strReadRights);
	$tabstrip->add_tab('write_permissions', $strWriteRights);

} else {
	$tabstrip = new tabstrip('project_tab', $pm_new_project);
}
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to(htmlspecialchars($return_to));

if ($project_id == 0 || $task == 'save_project') {
	$write_permissions = true;
	$read_permissions = true;

	$project['name'] = isset ($_POST['name']) ? smart_stripslashes($_POST['name']) : '';
	$project['comments'] = isset ($_POST['comments']) ? smart_stripslashes($_POST['comments']) : '';
	$project['description'] = isset ($_POST['description']) ? smart_stripslashes($_POST['description']) : '';
	$project['ctime'] = $project['mtime'] = get_gmt_time();
	$project['user_id'] = $GO_SECURITY->user_id;
	$project['calendar_id'] = isset($_POST['calendar_id']) ? 1 : 0;
	//$project['book_units'] = isset ($_POST['book_units']) ? '1' : '0';

}



$form = new form('projects_form');
$form->add_html_element(new input('hidden', 'close', 'false'));
$form->add_html_element(new input('hidden', 'project_id', $project_id, false));
$form->add_html_element(new input('hidden', 'task', '', false));
$form->add_html_element(new input('hidden', 'return_to',$return_to));
$form->add_html_element(new input('hidden', 'link_back',$link_back));
$form->add_html_element(new input('hidden', 'goto_url','', false));


if($tabstrip->get_active_tab_id() == 'links')
{
	load_control('links_list');
	$links_list = new links_list($project['link_id'], '0', $link_back);
	$GO_HEADER['head'] = $links_list->get_header();

}else {
	$GO_HEADER['head'] = '<link href="'.$GO_THEME->theme_url.'css/projects.css" type="text/css" rel="stylesheet" />';
}
if ($project_id > 0) {

	$menu = new button_menu();


	if($write_permissions || $GO_SECURITY->has_permission($GO_SECURITY->user_id, $project['acl_book']))
	{
		$menu->add_button('enter_data_big',
		$pm_enter_data,
		$GO_MODULES->url.'book.php?project_id='.$project_id.'&return_to='.urlencode($link_back));
	}


	if($GO_LINKS->linking_is_active())
	{
		if($GO_LINKS->get_active_link())
		{
			$menu->add_button('link', $strCreateLink, "javascript:document.projects_form.task.value='create_link';document.projects_form.submit();");
		}
	}else
	{
		$menu->add_button('link', $strCreateLink, "javascript:document.projects_form.task.value='activate_linking';document.projects_form.submit();");
	}

	if($write_permissions)
	{

		if($tabstrip->get_active_tab_id() == 'links')
		{
			$menu->add_button(
			'unlink',
			$cmdUnlink,
			$links_list->get_unlink_handler());

			$menu->add_button(
			'delete_big',
			$cmdDelete,
			$links_list->get_delete_handler());

			/*	$menu->add_button(
			'print',
			$cmdPrint,
			'javascript:popup(\'print_projects.php?type=tl&project_id='.$project_id.'\');');*/
		}
	}


	$form->add_html_element($menu);
}

if($tabstrip->get_active_tab_id() == '' || $tabstrip->get_active_tab_id() == 'properties')
{
	$GO_HEADER['body_arguments'] = 'onload="document.forms[0].name.focus();"';
}

$GO_HEADER['head'] .= date_picker::get_header();

$GO_HEADER['head'] .= tooltip::get_header();


$page_title = $lang_modules['projects'];
require_once ($GO_THEME->theme_path."header.inc");

switch ($tabstrip->get_active_tab_id()) {

	case 'book_permissions' :
		$tabstrip->innerHTML .= get_acl($project['acl_book']);
		$tabstrip->add_html_element(new html_element('br'));
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));
		break;

	case 'read_permissions' :
		$tabstrip->innerHTML .= get_acl($project['acl_read']);
		$tabstrip->add_html_element(new html_element('br'));
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));
		break;

	case 'write_permissions' :
		$tabstrip->innerHTML .= get_acl($project['acl_write']);
		$tabstrip->add_html_element(new html_element('br'));
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));
		break;

	case 'load' :
		$container = &$tabstrip;
		require_once ('load.inc');
		break;

	case 'links' :

		$tabstrip->add_html_element($links_list);
		break;

	default :

		if($tabstrip->get_active_tab_id() > 0)
		{
			$form->add_html_element(new input('hidden', 'link_id', $project['link_id']));
			if($cf_table = $cf->get_fields_table($tabstrip->get_active_tab_id(), $project['link_id']))
			{
				$tabstrip->add_html_element($cf_table);

				if ($write_permissions) {
					$tabstrip->add_html_element(new button($cmdOk, "javascript:_save('save_custom_fields', 'true');"));
					$tabstrip->add_html_element(new button($cmdApply, "javascript:_save('save_custom_fields', 'false')"));
				}
			}
			$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));
		}else
		{
			if (isset($feedback))
			{
				$p = new html_element('p', $feedback);
				$p->set_attribute('class','Error');
				$tabstrip->add_html_element($p);
			}

			$table = new table();

			if(
			isset($GO_MODULES->modules['calendar']) &&
			$GO_MODULES->modules['calendar']['read_permission'] &&
			$project_id == 0 &&
			$projects->get_authorized_templates($GO_SECURITY->user_id))
			{
				$select = new select('template_id');
				$select->add_value('0',$pm_no_template);

				while($projects->next_record())
				{
					$select->add_value($projects->f('id'), $projects->f('name'));
				}

				$row = new table_row();

				$row->add_cell(new table_cell($pm_template.':'));
				$cell = new table_cell($select->get_html());

				require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
				$cal = new calendar();


				if(!$cal->get_writable_calendars($GO_SECURITY->user_id))
				{
					$cal->get_calendar();
					$cal->get_writable_calendars($GO_SECURITY->user_id);
				}

				$calendar_id = isset($_POST['calendar_id']) ? $_POST['calendar_id'] : '0';
				$select = new select('calendar_id', $calendar_id);
				while($cal->next_record())
				{
					$select->add_value($cal->f('id'), $cal->f('name'));
				}

				$cell->innerHTML .= ' '.$pm_put_events_in.' '.$select->get_html();
				$row->add_cell($cell);

				$table->add_row($row);
			}

			$row = new table_row();

			$row->add_cell(new table_cell($strName.':*'));

			if ($write_permissions) {
				$input = new input('text', 'name', $project['name']);
				$input->set_attribute('maxlength','50');
				$input->set_attribute('style','width:250px;');
				$row->add_cell(new table_cell($input->get_html()));
			} else {
				$row->add_cell(new table_cell(htmlspecialchars($project['name'])));
			}

			$table->add_row($row);				

			$row = new table_row();
			$cell = new table_cell('&nbsp;');
			$cell->set_attribute('colspan','2');
			$row->add_cell($cell);
			$table->add_row($row);

			if ($project_id > 0) {

				$row = new table_row();
				$row->add_cell(new table_cell($strOwner.':'));
				$row->add_cell(new table_cell(show_profile($project['user_id'])));
				$table->add_row($row);

				$row = new table_row();
				$row->add_cell(new table_cell($strCreatedAt.':'));
				$row->add_cell(new table_cell(
				date($_SESSION['GO_SESSION']['date_format'].' '.
				$_SESSION['GO_SESSION']['time_format'],
				$project['ctime'] +
				(get_timezone_offset($project['ctime']) * 3600))));
				$table->add_row($row);

				$row = new table_row();
				$row->add_cell(new table_cell($strModifiedAt.':'));
				$row->add_cell(new table_cell(
				date($_SESSION['GO_SESSION']['date_format'].' '.
				$_SESSION['GO_SESSION']['time_format'],
				$project['mtime'] +
				(get_timezone_offset($project['mtime']) * 3600))));
				$table->add_row($row);

				$row = new table_row();
				$cell = new table_cell('&nbsp;');
				$cell->set_attribute('colspan','2');
				$row->add_cell($cell);
				$table->add_row($row);
			}

			$row = new table_row();

			$cell = new table_cell($strComments.':');
			$cell->set_attribute('style','vertical-align:top');
			$row->add_cell($cell);

			if ($write_permissions) {
				$textarea = new textarea('comments', $project['comments']);
				$textarea->set_attribute('style','width:500px; height:80px;');
				$row->add_cell(new table_cell($textarea->get_html()));
			} else {
				$row->add_cell(new table_cell(text_to_html($project['comments'])));
			}

			$table->add_row($row);

			if(isset($GO_MODULES->modules['calendar']) && $GO_MODULES->modules['calendar']['read_permission'])
			{
				$row = new table_row();
				$checkbox = new checkbox('link_calendar', 'link_calendar', '1', $pm_link_calendar, $project['calendar_id']>0);
				$cell = new table_cell($checkbox->get_html());
				$cell->set_attribute('colspan','2');
				$row->add_cell($cell);
				$table->add_row($row);
			}



			$tabstrip->add_html_element($table);

			if ($write_permissions) {
				$tabstrip->add_html_element(new button($cmdOk, "javascript:_save('save_project', 'true');"));
				$tabstrip->add_html_element(new button($cmdApply, "javascript:_save('save_project', 'false')"));
				$tabstrip->add_html_element(new button($cmdCopy,
				"javascript:document.location='project.php?project_id=".
				$project_id."&task=copy_project&return_to=".urlencode($return_to)."';"));
			}

			$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));
		}
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

function activate_linking(goto_url)
{
	document.projects_form.goto_url.value=goto_url;
	document.projects_form.task.value='activate_linking';
	document.projects_form.submit();
}
</script>
<?php
require_once ($GO_THEME->theme_path."footer.inc");
?>
