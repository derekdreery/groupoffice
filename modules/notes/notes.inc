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

require_once($GO_LANGUAGE->get_language_file('notes'));

$no_settings = $notes->get_settings($GO_SECURITY->user_id);

if (isset($_REQUEST['delete_note_id']) && $_REQUEST['delete_note_id'] > 0)
{
	$notes->delete_note(smart_addslashes($_REQUEST['delete_note_id']));
}

//define the items to show_notes
$notes_max_rows = isset($_REQUEST['notes_max_rows']) ? $_REQUEST['notes_max_rows'] : $_SESSION['GO_SESSION']['max_rows_list'];
$notes_first = isset($_REQUEST['notes_first']) ? $_REQUEST['notes_first'] : 0;

//define load parameters
$project_id = isset($project_id) ? $project_id : 0;
$contact_id = isset($contact_id) ? $contact_id : 0;
$company_id = isset($company_id) ? $company_id : 0;
$user_id = isset($user_id) ? $user_id : 0;
$file_path = isset($file_path) ? $file_path : '';


$projects_module = isset($GO_MODULES->modules['projects']) ? $GO_MODULES->modules['projects'] : false;
if($projects_module && $projects_module['read_permission'])
{
	require_once($projects_module['class_path'].'projects.class.inc');
	$projects = new projects();
}

$fs_module = isset($GO_MODULES->modules['filesystem']) ? $GO_MODULES->modules['filesystem'] : false;
if($fs_module && $fs_module['read_permission'])
{
	require_once($GO_CONFIG->class_path.'filesystem.class.inc');
	$fs = new filesystem();
}

$ab_module = isset($GO_MODULES->modules['addressbook']) ? $GO_MODULES->modules['addressbook'] : false;
if ($ab_module && $ab_module['read_permission'])
{
	require_once($ab_module['class_path'].'addressbook.class.inc');
	$ab = new addressbook();
}

if(isset($_REQUEST['new_notes_sort_order']) && $_REQUEST['new_notes_sort_order'] != $no_settings['sort_order'])
{
	$notes->set_sorting($GO_SECURITY->user_id,
	$_REQUEST['new_notes_sort_field'], $_REQUEST['new_notes_sort_order']);
	$no_settings['sort_order'] = $_REQUEST['new_notes_sort_order'];
	$no_settings['sort_field'] = $_REQUEST['new_notes_sort_field'];
}

if ($no_settings['sort_order'] == "DESC")
{
	$sort_arrow = '&nbsp;<img src="'.$GO_THEME->images['arrow_down'].'" border="0" />';
	$new_notes_sort_order = "ASC";
}else
{
	$sort_arrow = '&nbsp;<img src="'.$GO_THEME->images['arrow_up'].'" border="0" />';
	$new_notes_sort_order = "DESC";
}


$count = $notes->get_notes($user_id, $project_id, $contact_id, $company_id, addslashes($file_path), $no_settings['sort_field'], $no_settings['sort_order'], $notes_first, $notes_max_rows);

echo '<input type="hidden" name="notes_first" value="'.$notes_first.'" />';
echo '<input type="hidden" name="notes_max_rows" value="'.$notes_max_rows.'" />';
echo '<input type="hidden" name="delete_note_id" />';
echo '<input type="hidden" name="new_notes_sort_field" value="'.$no_settings['sort_field'].'" />';
echo '<input type="hidden" name="new_notes_sort_order" value="'.$no_settings['sort_order'].'" />';


$str_count = $count == 1 ? $no_notes_count_single : $no_notes_count;
echo '<div class="small" style="text-align:right;">'.$count.' '.$str_count.'</div>';
echo '<table class="go_table" width="100%">';
echo '<tr height="20">';
echo '<th><a href="javascript:notes_sort(\'name\',\''.$new_notes_sort_order.'\');">'.$strName.'</a>';
if ($no_settings['sort_field'] == 'name')
{
	echo $sort_arrow;
}
echo '</th>';

echo '<th><a href="javascript:notes_sort(\'mtime\',\''.$new_notes_sort_order.'\');">'.$strModifiedAt.'</a>';
if ($no_settings['sort_field'] == 'mtime')
{
	echo $sort_arrow;
}
echo '</th>';
echo '<th>'.$strRelatedTo.'</th>';
echo '<th>&nbsp;</th></tr>';

if ($count > 0)
{
	while($notes->next_record())
	{
		echo '<tr><td><a class="normal" href="'.$GO_MODULES->modules['notes']['url'].'note.php?note_id='.$notes->f('id').'&return_to='.rawurlencode($link_back).'">'.htmlspecialchars($notes->f('name')).'</a>';
		if($notes->f('content') != '')
		{
			echo '<div class="description">'.text_to_html($notes->f('content')).'</div>';
		}
		echo '</td>';
		echo '<td valign="top" nowrap>'.date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], gmt_to_local_time($notes->f('mtime'))).'</td><td valign="top">';

		if($notes->f('contact_id') > 0)
		{
			echo show_contact($notes->f('contact_id'));
		}elseif($notes->f('company_id') > 0)
		{
			echo show_company($notes->f('company_id'));
		}elseif($notes->f('project_id') > 0)
		{
			if($project = $projects->get_project($notes->f('project_id')))
			{
				$project_name = $project['description'] == '' ? $project['name'] : $project['name'].' ('.$project['description'].')';

				if ($projects_module['read_permission'])
				{
					echo '<a href="'.$projects_module['url'].'project.php?project_id='.$notes->f('project_id').'&return_to='.urlencode($_SERVER['REQUEST_URI']).'" class="normal">'.htmlspecialchars($project_name).'</a>';
				}else
				{
					echo htmlspecialchars($project_name);
				}
			}
		}elseif($notes->f('file_path') != '' && isset($GO_MODULES->modules['filesystem']))
		{
			if (file_exists($notes->f('file_path')))
			{
				echo 	'<a class="normal" href="'.
				$GO_MODULES->modules['filesystem']['url'].'index.php?path='.urlencode(dirname($notes->f('file_path'))).
				'">'.stripslashes($notes->f('file_path')).'</a>';
			}
		}

		echo "</td><td align=\"right\" valign=\"top\"><a href='javascript:confirm_action(\"javascript:delete_note(".$notes->f('id').")\",\"".htmlspecialchars($strDeletePrefix."'".$notes->f('name')."'".$strDeleteSuffix, ENT_QUOTES)."\")' title=\"".$strDeleteItem." '".htmlspecialchars($notes->f('name'), ENT_QUOTES)."'\"><img src=\"".$GO_THEME->images['delete']."\" border=\"0\"></a></td>\n";

	}

	$links = '';
	$max_links=10;
	if ($notes_max_rows != 0)
	{
		if ($count > $notes_max_rows)
		{
			$links = '<table class="NavLinks"><tr><td>';
			$next_start = $notes_first+$notes_max_rows;
			$previous_start = $notes_first-$notes_max_rows;
			if ($notes_first != 0)
			{
				$links .= '<a href="javascript:notes_change_list(0, '.$notes_max_rows.');">&lt&lt</a>&nbsp;';
				$links .= '<a href="javascript:notes_change_list('.$previous_start.', '.$notes_max_rows.');">'.$cmdPrevious.'</a>&nbsp;';
			}else
			{
				$links .= '<font color="#cccccc">&lt&lt '.$cmdPrevious.'</font>&nbsp;';
			}

			$start = ($notes_first-(($max_links/2)*$notes_max_rows));

			$end = ($notes_first+(($max_links/2)*$notes_max_rows));

			if ($start < 0)
			{
				$end = $end - $start;
				$start=0;
			}
			if ($end > $count)
			{
				$end = $count;
			}
			if ($start > 0)
			{
				$links .= '...&nbsp;';
			}

			for ($i=$start;$i<$end;$i+=$notes_max_rows)
			{
				$page = ($i/$notes_max_rows)+1;
				if ($i==$notes_first)
				{
					$links .= '<b><i>'.$page.'</i></b>&nbsp;';
				}else
				{
					$links .= '<a href="javascript:notes_change_list('.$i.', '.$notes_max_rows.');">'.$page.'</a>&nbsp;';
				}
			}

			if ($end < $count)
			{
				$links .= '...&nbsp;';
			}

			$last_page = floor($count/$notes_max_rows)*$notes_max_rows;

			if ($count > $next_start)
			{
				$links .= '<a href="javascript:notes_change_list('.$next_start.', '.$notes_max_rows.');">'.$cmdNext.'</a>&nbsp;';
				$links .= '<a href="javascript:notes_change_list('.$last_page.', '.$notes_max_rows.');">&gt&gt</a>';
			}else
			{
				$links .= '<font color="#cccccc">'.$cmdNext.' &gt&gt</font>';
			}
			$links .= '</td><td align="right"><a class="normal" href="javascript:notes_change_list(0, 0);">'.$cmdShowAll.'</a></td></tr></table>';

			echo '<tr height="20"><td colspan="99">'.$links.'</td></tr>';
		}
	}

}else
{
	echo '<tr><td colspan="99">'.$no_no_notes.'</td></tr>';
}
echo '</table>';
?>

<script type="text/javascript">

function notes_sort(field, direction)
{
	document.forms[0].new_notes_sort_field.value=field;
	document.forms[0].new_notes_sort_order.value=direction;
	document.forms[0].submit();
}

function delete_note(id)
{
	document.forms[0].delete_note_id.value=id;
	document.forms[0].submit();
}

function notes_change_list(notes_first, notes_max_rows)
{
	document.forms[0].notes_first.value=notes_first;
	document.forms[0].notes_max_rows.value=notes_max_rows;
	document.forms[0].submit();
}
</script>
