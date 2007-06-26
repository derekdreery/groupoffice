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

//add blank task var
echo '<input type="hidden" name="task" />';
echo '<input type="hidden" name="return" value="'.$folder_id.'" />';
echo '<input type="hidden" name="folder_id" value="'.$folder_id.'" />';
echo '<input type="hidden" name="file_id" />';
//builds the full path to this directory with links
function get_path($folder_id)
{
  global $cms;
  $path = '';

  while($folder = $cms->get_folder($folder_id))
  {
    $path = '/<a href="'.$_SERVER['PHP_SELF'].'?folder_id='.$folder['id'].'">'.$folder['name'].'</a>'.$path;
    $folder_id = $folder['parent_id'];
  }
  return $path;
}

$site_file_path = $GO_CONFIG->local_path.'cms/sites/'.$site['id'].'/';		
if(!is_dir($site_file_path))
{
	require_once($GO_CONFIG->class_path.'filesystem.class.inc');
	$fs = new filesystem();
	mkdir_recursive($site_file_path);	
	$acl_read = $GO_SECURITY->get_new_acl();
	$fs->add_share($site['user_id'], $site_file_path, 'site', $acl_read, $site['acl_write']);
}

//get current folder information and print path and buttons
$folder = $cms->get_folder($folder_id);
echo '<h2>'.get_path($folder['parent_id']).'/<a href="'.$_SERVER['PHP_SELF'].'?folder_id='.$folder['id'].'">'.$folder['name'].'</a></h2>';
echo '<table border="0" cellpadding="0" cellspacing="0">';
if($folder['parent_id']!=0)
{
  echo '<td class="ModuleIcons">';
  echo '<a class="small" href="'.$_SERVER['PHP_SELF'].'?folder_id='.$folder['parent_id'].'"><img src="'.$GO_THEME->images['uplvl_big'].'" border="0" height="32" width="32" /><br />'.$fbUpLevel .'</a></td>';
}

echo '<td class="ModuleIcons">';
echo '<a class="small" href="'.$_SERVER['PHP_SELF'].'?task=add_folder&folder_id='.$folder_id.'"><img src="'.$GO_THEME->images['new_folder'].'" border="0" height="32" width="32" /><br />'.$fbNewFolder.'</a></td>';
/*echo '<td class="ModuleIcons">';
echo '<a class="small" href="'.$GO_MODULES->modules['filesystem']['url'].'?path='.urlencode($site_file_path).'"><img src="'.$GO_THEME->images['upload'].'" border="0" height="32" width="32" /><br />'.$fbUpload.'</a></td>';*/
echo '<td class="ModuleIcons">';
echo '<a class="small" href="javascript:'.$datatable->get_delete_handler().'"><img src="'.$GO_THEME->images['delete_big'].'" border="0" height="32" width="32" /><br />'.$fbDelete.'</a></td>';
echo '<td class="ModuleIcons">';
echo '<a class="small" href="edit.php?folder_id='.$folder_id.'"><img src="'.$GO_THEME->images['new_doc'].'" border="0" height="32" width="32" /><br />'.$cms_new_page.'</a></td>';
echo '<td class="ModuleIcons">';
echo '<a class="small" href="javascript:cut_items()"><img src="'.$GO_THEME->images['cut'].'" border="0" height="32" width="32" /><br />'.$fbCut.'</a></td>';
echo '<td class="ModuleIcons">';
echo '<a class="small" href="javascript:copy_items()"><img src="'.$GO_THEME->images['copy'].'" border="0" height="32" width="32" /><br />'.$fbCopy.'</a></td>';

$_SESSION['cut_files'] = isset($_SESSION['cut_files']) ? $_SESSION['cut_files'] : array();
$_SESSION['cut_folders'] = isset($_SESSION['cut_folders']) ? $_SESSION['cut_folders'] : array();
$_SESSION['copy_folders'] = isset($_SESSION['copy_folders']) ? $_SESSION['copy_folders'] : array();
$_SESSION['copy_files'] = isset($_SESSION['copy_files']) ? $_SESSION['copy_files'] : array();

if((count($_SESSION['cut_files'] ) + count($_SESSION['cut_folders']) + count($_SESSION['copy_folders']) + count($_SESSION['copy_files'] )) > 0)
{
  echo '<td class="ModuleIcons">';
  echo '<a class="small" href="javascript:paste_items()"><img src="'.$GO_THEME->images['paste'].'" border="0" height="32" width="32" /><br />'.$fbPaste.'</a></td>';
}

echo '<td class="ModuleIcons">';
echo '<a class="small" target="_blank" href="view.php?site_id='.$_SESSION['site_id'].'&folder_id='.$folder_id.'" title="'.$cms_preview.' \''.$site['domain'].'\'"><img src="'.$GO_THEME->images['magnifier_big'].'" border="0" /><br />'.$cms_preview.'</a></td>';

echo '<td class="ModuleIcons">';
echo '<a class="small" href="site.php?site_id='.$_SESSION['site_id'].'&return_to='.rawurlencode($GO_MODULES->url.'browse.php?folder_id='.$folder_id).'"><img src="'.$GO_THEME->images['cms_settings'].'" border="0" height="32" width="32" /><br />'.$cms_settings.'</a></td>';


echo '<td class="ModuleIcons">';
echo '<a class="small" href="index.php"><img src="'.$GO_THEME->images['exit'].'" border="0" height="32" width="32" /><br />'.$cmdClose.'</a></td>';

echo '<td class="ModuleIcons">';
echo '<a class="small" href="upload.php"><img src="'.$GO_THEME->images['upload'].'" border="0" height="32" width="32" /><br />'.$fbUpload.'</a></td>';

echo '</table>';
$cms_settings = $cms->get_settings($GO_SECURITY->user_id);


//adjust sorting because folders because they lack some columns
switch ($cms_settings['sort_field'])
{
  case 'cms_files.priority':
    $folders_sort = "priority";
    break;

  case 'cms_files.time':
    $folders_sort = "time";
    break;

  default:
    $folders_sort = "name";
    break;
}
?>

<table border="0" cellpadding="10" width="100%">
<tr>
<td valign="top" nowrap width="20%">
<?php
require_once('treeview.inc');
?>
</td>
<td valign="top" width="50%" nowrap>
<?php

require_once('listview.inc');
?>
</td>
<td valign="top" width="30%" nowrap>
<?php
$tabstrip = new dynamic_tabstrip('properties_tab', $strProperties);
$tabstrip->set_attribute('style','width:100%');
//$tabstrip->css_table='cmsProperties';

if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $tabstrip->add_html_element($p);
}


$table = new table();

$row = new table_row();

$row->add_cell(new table_cell($strName.'*:'));
$input = new input('text', 'name',$folder['name'], false);
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($cms_template_item.':'));

$select = new select("template_item_id", $folder['template_item_id']);
$cms->get_template_items($site['template_id'], true);
while($cms->next_record())
{
	$select->add_value($cms->f('id'), $cms->f('name'));
}
$cell = new table_cell($select->get_html());
$row->add_cell($cell);
$table->add_row($row);


$row = new table_row();

$disabled_check = ($folder['disabled'] == '1') ? true : false;
$checkbox = new checkbox('disabled', 'disabled', '1', $cms_hide_folder, $disabled_check, ($folder['parent_id'] == 0));

$cell = new table_cell($checkbox->get_html());
$cell->set_attribute('colspan','2');
$row->add_cell($cell);

$table->add_row($row);
$row = new table_row();

$display_type = isset($folder['display_type']) ? $folder['display_type'] : NORMAL_DISPLAY;
$multipage_check = ($folder['multipage'] == '1') ? true : false;		  	
$checkbox = new checkbox('multipage', 'multipage', '1',$cms_multipage, $multipage_check);


$cell = new table_cell($checkbox->get_html());
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$checkbox = new checkbox('go_auth','go_auth', '1', $cms_go_auth, ($folder['acl']>0));
$cell = new table_cell($checkbox->get_html());
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$button = new button($cmdSave, 'javascript:save_properties();');
$cell = new table_cell($button->get_html());
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);


if($folder['acl']>0)
{
	$tabstrip->add_tab('properties', $fbProperties, $table);	
	$span = new html_element('span', get_acl($folder['acl']));
	$tabstrip->add_tab('acl', $strPermissions, $span);	
}else
{
	$tabstrip->add_html_element($table);
}



echo $tabstrip->get_html();
?>
</td>
</tr>
</table>
