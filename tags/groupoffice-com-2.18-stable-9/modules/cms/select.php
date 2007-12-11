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

$site_id = isset ($_REQUEST['site_id']) ? $_REQUEST['site_id'] : 0;


function get_path($folder_id) {
	global $cms;
	$path = '';

	while ($folder = $cms->get_folder($folder_id)) {
		$path = '/<a href="'.$_SERVER['PHP_SELF'].'?folder_id='.$folder['id'].'">'.$folder['name'].'</a>'.$path;
		$folder_id = $folder['parent_id'];
	}
	return $path;
}

require_once ("../../Group-Office.php");

load_basic_controls();
load_control('datatable');

//authenticate the user
$GO_SECURITY->authenticate();

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('cms');

//get the language file
require_once ($GO_LANGUAGE->get_language_file('cms'));

require_once ($GO_MODULES->class_path.'cms.class.inc');
$cms = new cms();

$cms_settings = $cms->get_settings($GO_SECURITY->user_id);

$site = $cms->get_site($_SESSION['site_id']);

if (!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $site['acl_write'])) {
	require_once ($GO_THEME->theme_path."header.inc");
	require_once ($GO_CONFIG->root_path.'error_docs/403.inc');
	require_once ($GO_THEME->theme_path."footer.inc");
	exit ();

}

$_SESSION['GO_SESSION']['cms']['select_folder_id'] = isset($_SESSION['GO_SESSION']['cms']['select_folder_id']) ? $_SESSION['GO_SESSION']['cms']['select_folder_id'] : $site['root_folder_id'];
//set the folder id we are in
$folder_id = isset ($_REQUEST['folder_id']) ? $_REQUEST['folder_id'] : $_SESSION['GO_SESSION']['cms']['select_folder_id'];
$_SESSION['GO_SESSION']['cms']['select_folder_id'] = $folder_id;

$GO_HEADER['head'] = datatable::get_header();
require_once ($GO_THEME->theme_path."header.inc");

$folder = $cms->get_folder($folder_id);

$form = new form('cms_select');

$h2 = new html_element('h2', get_path($folder['parent_id']).'/'.$folder['name']);
$form->add_html_element($h2);

$menu = new button_menu();
if ($folder['parent_id'] != 0) {
	$menu->add_button('uplvl_big', $fbUpLevel,
		$_SERVER['PHP_SELF'].'?site_id='.$site_id.'&folder_id='.$folder['parent_id']);
}
$menu->add_button('close', $cmdClose,
		'javascript:window.close();');
		
$menu->add_button('filesystem', $lang_modules['filesystem'],
	'select_file.php?path='.urlencode($GO_CONFIG->local_path.'cms/sites/'.$_SESSION['site_id']));

$form->add_html_element($menu);


$datatable = new datatable('cms_table');
$datatable->add_column(new table_heading($strName));
$datatable->add_column(new table_heading($strType));

//list the folders first
$total_size = 0;
$count_folders = $cms->get_folders($folder_id);
while ($cms->next_record()) {
	$short_name = cut_string($cms->f('name'), 30);
	
	$row = new table_row('folder_'.$cms->f('id'));
	$row->set_attribute('ondblclick', "javascript:document.location='".$_SERVER['PHP_SELF']."?site_id=".$_SESSION['site_id']."&folder_id=".$cms->f('id')."';");
		
	if ($cms->f('disabled') == '1') {
		$img = new image('invisible_folder');		
	} else {
		$img = new image('folder');
	}
	$img->set_attribute('align','absmiddle');
	$img->set_attribute('style', 'width:16px;height:16px;margin-right:5px;');	
	$row->add_cell(new table_cell($img->get_html().$short_name));

	$row->add_cell(new table_cell($fbFolder));
	$datatable->add_row($row);
}

//list the files
$count_files = $cms->get_files($folder_id);
while ($cms->next_record()) {
	$short_name = strip_extension(cut_string($cms->f('name'), 30));
	
	$row = new table_row('file_'.$cms->f('id'));
	$row->set_attribute('ondblclick', "javascript:_insertHyperlink('".$GO_MODULES->full_url."view.php?folder_id=".$cms->f('folder_id')."&amp;file_id=".$cms->f('id')."', '".strip_extension($cms->f('name'))."');");

	$img = new image('', get_filetype_image($cms->f('extension')));		
	$img->set_attribute('align','absmiddle');
	$img->set_attribute('style', 'width:16px;height:16px;margin-right:5px;');	
	$row->add_cell(new table_cell($img->get_html().$short_name));
	
	$row->add_cell(new table_cell(get_filetype_description($cms->f('extension'))));
	$datatable->add_row($row);
}

$form->add_html_element($datatable);
echo $form->get_html();
?>
<script type="text/javascript" language="javascript">

function _insertHyperlink(url, name)
{
  window.opener.SetUrl(url, "", "", name);
  window.close();
}

document.onblur = function() {
  setTimeout('self.focus()',100);
}

</script>
<?php
require_once ($GO_THEME->theme_path."footer.inc");
