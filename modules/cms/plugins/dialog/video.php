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


require_once ("../../../../Group-Office.php");

//authenticate the user
$GO_SECURITY->authenticate();

load_basic_controls();

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('cms');

//get the language file
require_once ($GO_LANGUAGE->get_language_file('cms'));

$task = isset($_REQUEST['task']) ? smart_stripslashes($_REQUEST['task']) : '';


if($task=='insert')
{	
	$src = smart_stripslashes($_POST['src']);
	//$height = number_to_phpnumber(smart_stripslashes($_POST['height']));
	
	require_once($GO_MODULES->path.'plugins/video.class.inc');
	$cms_video = new cms_video(array(),$src);
	
	if(empty($src))
	{
		$feedback = $GLOBALS['error_missing_field'];
	}else {
		$html = '<embed src="'.$src.'"></embed><br /><br />';		
	
		
		?>
		
		<script type="text/javascript" language="javascript">
		var oEditor = opener.FCKeditorAPI.GetInstance('content') ;
		oEditor.InsertHtml('<?php echo addslashes($html); ?>');
		window.close();
		</script>
		
		<?php
		exit();
	}
}


require_once ($GO_THEME->theme_path."header.inc");

$form = new form('video_form');
$form->add_html_element(new input('hidden','task', 'insert'));

$tabstrip = new tabstrip('select_plugin_tabstrip', $cms_insert_video);
$tabstrip->set_attribute('style','width:100%');

if (isset($feedback))
{
	$p = new html_element('p', $feedback);
	$p->set_attribute('class', 'Error');			
	$tabstrip->add_html_element($p);
}


$table = new table();

$row = new table_row();
$cell = new table_cell('src:');
$cell->set_attribute('style', 'whitespace:nowrap;');
$row->add_cell($cell);		
$cell = new table_cell();
$src = isset($_REQUEST['src']) ? smart_stripslashes($_REQUEST['src']) : '';
$input = new input('text', 'src', $src);
$input->set_attribute('style','width:200px');

$button = new button('Browse server', "javascript:popup('".$GO_MODULES->modules['cms']['url']."select_file.php?path=".urlencode($GO_CONFIG->local_path."cms/sites/".$_SESSION['site_id']."/")."','700','500');");


$cell->add_html_element($input);
$cell->add_html_element($button);
$row->add_cell($cell);
$table->add_row($row);

$tabstrip->add_html_element($table);

$tabstrip->add_html_element(new button($cmdOk, 'javascript:document.video_form.submit();'));



$form->add_html_element($tabstrip);

echo $form->get_html();
?>
<script type="text/javascript" language="javascript">
function SetUrl(url, foo, bar, name)
{
	document.video_form.src.value=url;
}
</script>
<?php
require_once ($GO_THEME->theme_path."footer.inc");
