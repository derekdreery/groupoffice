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

if ($task == 'start_timer') {
	$projects->start_timer($GO_SECURITY->user_id, $_REQUEST['project_id']);
}

if (isset ($_REQUEST['delete_project_id']) && $_REQUEST['delete_project_id'] > 0) {
	$delete_project_id = smart_addslashes($_REQUEST['delete_project_id']);

	$project = $projects->get_project($delete_project_id);
	if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $project['acl_write'])) {
		if ($projects->delete_project($delete_project_id)) {
			$GO_SECURITY->delete_acl($project['acl_write']);
			$GO_SECURITY->delete_acl($project['acl_read']);
		}
	} else {
		$feedback = '<p class="Error">'.$strAccessDenied.'</p>';
	}
}

$pm_settings = $projects->get_settings($GO_SECURITY->user_id);

$projects_max_rows = isset ($_REQUEST['projects_max_rows']) ? $_REQUEST['projects_max_rows'] : $_SESSION['GO_SESSION']['max_rows_list'];
$projects_first = isset ($_REQUEST['projects_first']) ? $_REQUEST['projects_first'] : 0;

//check if we are included from the addressbook and if we have a contact related to us
$contact_id = isset ($contact_id) ? $contact_id : 0;
$company_id = isset ($company_id) ? $company_id : 0;
$project_id = isset ($project_id) ? $project_id : 0;

if ($timer = $projects->get_timer($GO_SECURITY->user_id)) {
	echo '<table border="0" class="ErrorBox"><tr><td><img src="'.$GO_THEME->images['alert'].'" border="0" /></td>'.'<td><a class="normal" href="'.$GO_MODULES->modules['projects']['url'].'book.php?task=stop_timer&project_id='.$timer['project_id'].'&return_to='.rawurlencode($link_back).'&contact_id='.$contact_id.'">'.$pm_clocked_in.'</a></td></tr></table>';
}
if (isset ($_REQUEST['show_projects']) && $_REQUEST['show_projects'] != $pm_settings['show_projects']) {
	$projects->set_show_projects($GO_SECURITY->user_id, $_REQUEST['show_projects']);
	$pm_settings['show_projects'] = $_REQUEST['show_projects'];
}

if (isset ($_REQUEST['new_projects_sort_order']) && $_REQUEST['new_projects_sort_order'] != $pm_settings['sort_order']) {
	$projects->set_sorting($GO_SECURITY->user_id, $_REQUEST['new_projects_sort_field'], $_REQUEST['new_projects_sort_order']);
	$pm_settings['sort_order'] = $_REQUEST['new_projects_sort_order'];
	$pm_settings['sort_field'] = $_REQUEST['new_projects_sort_field'];
}
if ($pm_settings['sort_order'] == "DESC") {
	$sort_arrow = '&nbsp;<img src="'.$GO_THEME->images['arrow_down'].'" border="0" />';
	$new_projects_sort_order = "ASC";
} else {
	$sort_arrow = '&nbsp;<img src="'.$GO_THEME->images['arrow_up'].'" border="0" />';
	$new_projects_sort_order = "DESC";
}

echo '<table border="0" cellspacing="0" cellpadding="1" width="100%">';
echo '<tr><td colspan="2"><table border="0"><tr><td>'.$pm_filter.':</td><td colspan="2">';

echo '<input type="hidden" name="projects_first" value="'.$projects_first.'" />';
echo '<input type="hidden" name="projects_max_rows" value="'.$projects_max_rows.'" />';
echo '<input type="hidden" name="post_action" value="projects" />';
echo '<input type="hidden" name="new_projects_sort_field" value="'.$pm_settings['sort_field'].'" />';
echo '<input type="hidden" name="new_projects_sort_order" value="'.$pm_settings['sort_order'].'" />';
echo '<input type="hidden" name="delete_project_id" value="0" />';

$dropbox = new dropbox();

$dropbox->add_value('', $cmdShowAll);
$dropbox->add_value(STATUS_OFFER, $pm_status_values[STATUS_OFFER]);
$dropbox->add_value(STATUS_ONGOING, $pm_status_values[STATUS_ONGOING]);
$dropbox->add_value(STATUS_WAITING, $pm_status_values[STATUS_WAITING]);
$dropbox->add_value(STATUS_DONE, $pm_status_values[STATUS_DONE]);
$dropbox->add_value(STATUS_BILLED, $pm_status_values[STATUS_BILLED]);

$dropbox->print_dropbox('show_projects', $pm_settings['show_projects'], 'onchange="javascript:document.forms[0].submit()"');

$count = $projects->get_authorized_projects($GO_SECURITY->user_id, $contact_id, $company_id, $project_id, $pm_settings['sort_field'], $pm_settings['sort_order'], $projects_first, $projects_max_rows, $pm_settings['show_projects']);
echo '</td></tr></table></td><td colspan="99" class="small" align="right">'.$count.' '.$pm_projects_count.'</td></tr>';

$contacts_module = $GO_MODULES->get_module('addressbook');
$has_contacts_module = ($contacts_module && ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $contacts_module['acl_read']) || $GO_SECURITY->has_permission($GO_SECURITY->user_id, $contacts_module['acl_write'])));

echo '</table>';

echo '<table width="100%" class="go_table">';
echo '<tr height="20">';

echo '<th width="16">&nbsp;</th>';

echo '<th width="100"><a href="javascript:_sort_projects(\'name\',\''.$new_projects_sort_order.'\');">'.$strName.'</a>';
if ($pm_settings['sort_field'] == 'name') {
	echo $sort_arrow;
}
echo '</th>';

echo '<th width="100"><a href="javascript:_sort_projects(\'start_date\',\''.$new_projects_sort_order.'\');">'.$pm_start_date.'</a>';
if ($pm_settings['sort_field'] == 'start_date') {
	echo $sort_arrow;
}
echo '</th>';

echo '<th width="100"><a href="javascript:_sort_projects(\'end_date\',\''.$new_projects_sort_order.'\');">'.$pm_end_date.'</a>';
if ($pm_settings['sort_field'] == 'end_date') {
	echo $sort_arrow;
}
echo '</th>';

echo '<th width="100"><a href="javascript:_sort_projects(\'status\',\''.$new_projects_sort_order.'\');">'.$pm_status.'</a>';
if ($pm_settings['sort_field'] == 'status') {
	echo $sort_arrow;
}
echo '</th>';

echo '<th><a href="javascript:_sort_projects(\'probability\',\''.$new_projects_sort_order.'\');">'.$pm_probability.'</a>';
if ($pm_settings['sort_field'] == 'probability') {
	echo $sort_arrow;
}
echo '</th>';

echo '<th>'.$strRelatedTo.'</th>';

echo '<th colspan="3">&nbsp;</th></tr>';

if ($count > 0) {

	$time = get_time();
	$projects2 = new projects();
	while ($projects->next_record()) {
		echo '<tr height="18"><td>';
		if ($projects->f('status') < 4 && $projects->f('end_date') < get_time()) {
			echo '<img border="0" src="'.$GO_THEME->images['alert'].'" alt="'.$pm_alert.'" />';
		} else {
			switch ($projects->f('status')) {
				case '5' :
					echo '<img border="0" src="'.$GO_THEME->images['ok'].'" alt="'.$pm_status_values[5].'" />';
					break;

				case '4' :
					echo '<img border="0" src="'.$GO_THEME->images['ok'].'" alt="'.$pm_status_values[4].'" />';
					break;

				case '3' :
					echo '<img border="0" src="'.$GO_THEME->images['clock'].'" alt="'.$pm_status_values[3].'" />';
					break;

				case '2' :
					echo '<img border="0" src="'.$GO_THEME->images['ongoing'].'" alt="'.$pm_status_values[2].'" />';
					break;

				case '1' :
					echo '<img border="0" src="'.$GO_THEME->images['offer'].'" alt="'.$pm_status_values[1].'" />';
					break;

			}
		}

		$project_name = ($projects->f('description') == '') ? htmlspecialchars($projects->f('name')) : htmlspecialchars($projects->f('name').' ('.$projects->f('description').')');
		echo '</td><td><a href="'.$GO_MODULES->modules['projects']['url'].'project.php?project_id='.$projects->f('id').'&return_to='.rawurlencode($link_back).'" class="normal">'.$project_name.'</a></td>';
		echo '<td>'.date($_SESSION['GO_SESSION']['date_format'], $projects->f('start_date')).'</td>';
		echo '<td>'.date($_SESSION['GO_SESSION']['date_format'], $projects->f('end_date')).'</td>';
		echo '<td>'.$pm_status_values[(int) $projects->f('status')].'</td>';
		echo '<td>'.$projects->f('probability').'%</td><td>';

		if ($projects->f('contact_id') > 0) {
			echo show_contact($projects->f('contact_id'));
		}
		elseif ($projects->f('company_id') > 0) {
			echo show_company($projects->f('company_id'));
		}
		elseif ($projects->f('project_id') > 0) {

			if ($project = $projects2->get_project($projects->f('project_id'))) {
				$project_name = $project['description'] == '' ? $project['name'] : $project['name'].' ('.$project['description'].')';

				if ($GO_MODULES->modules['projects']['read_permission']) {
					echo '<a href="'.$GO_MODULES->modules['projects']['url'].'project.php?project_id='.$projects->f('id').'&return_to='.urlencode($_SERVER['REQUEST_URI']).'" class="normal">'.htmlspecialchars($project_name).'</a>';
				} else {
					echo htmlspecialchars($project_name);
				}
			}
		}
		echo '</td><td align="right">';
		if (!$timer) {
			echo '<a href="'.$GO_MODULES->modules['projects']['url'].'book.php?project_id='.$projects->f("id").'&return_to='.rawurlencode($link_back).'&contact_id='.$contact_id.'" title="'.$pm_enter_data.'"><img src="'.$GO_THEME->images['enter_data'].'" border="0"></a>';
			echo '<a href="'.$GO_MODULES->modules['projects']['url'].'index.php?task=start_timer&project_id='.$projects->f("id").'&link_back='.urlencode($link_back).'" title="'.$pm_start_timer.'"><img src="'.$GO_THEME->images['clock'].'" border="0"></a>';
		}
		elseif ($timer['project_id'] == $projects->f('id')) {
			echo '<a href="'.$GO_MODULES->modules['projects']['url'].'book.php?task=stop_timer&project_id='.$projects->f("id").'&return_to='.rawurlencode($link_back).'&contact_id='.$contact_id.'" title="'.$pm_stop_timer.'"><img src="'.$GO_THEME->images['clockout'].'" border="0"></a>';
		}
		echo "<a href='javascript:confirm_action(\"javascript:delete_project(".$projects->f("id").")\",\"".htmlspecialchars($strDeletePrefix."'".$project_name."'".$strDeleteSuffix, ENT_QUOTES)."\")' title=\"".$strDeleteItem." '".$project_name."'\"><img src=\"".$GO_THEME->images['delete']."\" border=\"0\"></a></td>\n";
	}
	$links = '';
	$max_links = 10;
	if ($projects_max_rows != 0) {
		if ($count > $projects_max_rows) {
			$links = '<table class="NavLinks"><tr><td>';
			$next_start = $projects_first + $projects_max_rows;
			$previous_start = $projects_first - $projects_max_rows;
			if ($projects_first != 0) {
				$links .= '<a href="javascript:change_list(0, '.$projects_max_rows.');">&lt&lt</a>&nbsp;';
				$links .= '<a href="javascript:change_list('.$previous_start.', '.$projects_max_rows.');">'.$cmdPrevious.'</a>&nbsp;';
			} else {
				$links .= '<font color="#cccccc">&lt&lt '.$cmdPrevious.'</font>&nbsp;';
			}

			$start = ($projects_first - (($max_links / 2) * $projects_max_rows));

			$end = ($projects_first + (($max_links / 2) * $projects_max_rows));

			if ($start < 0) {
				$end = $end - $start;
				$start = 0;
			}
			if ($end > $count) {
				$end = $count;
			}
			if ($start > 0) {
				$links .= '...&nbsp;';
			}

			for ($i = $start; $i < $end; $i += $projects_max_rows) {
				$page = ($i / $projects_max_rows) + 1;
				if ($i == $projects_first) {
					$links .= '<b><i>'.$page.'</i></b>&nbsp;';
				} else {
					$links .= '<a href="'.$_SERVER['PHP_SELF'].'?post_action=projects&projects_first='.$i.'">'.$page.'</a>&nbsp;';
				}
			}

			if ($end < $count) {
				$links .= '...&nbsp;';
			}

			$last_page = floor($count / $projects_max_rows) * $projects_max_rows;

			if ($count > $next_start) {
				$links .= '<a href="javascript:change_list('.$next_start.', '.$projects_max_rows.');">'.$cmdNext.'</a>&nbsp;';
				$links .= '<a href="javascript:change_list('.$last_page.', '.$projects_max_rows.');">&gt&gt</a>';
			} else {
				$links .= '<font color="#cccccc">'.$cmdNext.' &gt&gt</font>';
			}
			$links .= '</td><td align="right"><a class="normal" href="javascript:change_list(0, 0);">'.$cmdShowAll.'</a></td></tr></table>';

			echo '<tr height="20"><td colspan="99">'.$links.'</td></tr>';
		}
	}

} else {
	echo '<tr><td colspan="99">'.$pm_no_projects.'</td></tr>';
}
echo '</table>';
?>

<script type="text/javascript">

function _sort_projects(field, direction)
{
  document.forms[0].new_projects_sort_field.value=field;
  document.forms[0].new_projects_sort_order.value=direction;
  document.forms[0].submit();
}

function delete_project(id)
{
  document.forms[0].delete_project_id.value=id;
  document.forms[0].submit();
}

function change_list(projects_first, projects_max_rows)
{
  document.forms[0].projects_first.value=projects_first;
  document.forms[0].projects_max_rows.value=projects_max_rows;
  document.forms[0].submit();
}
</script>
