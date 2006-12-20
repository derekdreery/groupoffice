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
require_once ($GO_LANGUAGE->get_language_file('gallery'));
require_once ($GO_LANGUAGE->get_language_file('cms'));

$task = isset($_REQUEST['task']) ? smart_stripslashes($_REQUEST['task']) : '';

require_once($GO_MODULES->modules['gallery']['class_path'].'gallery.class.inc');
$ig = new gallery();


if($task=='insert')
{	
	$gallery=$ig->get_gallery($_POST['gallery_id']);
	
	
	$html = '<cms:plugin gallery_id="'.$gallery['id'].'" plugin_id="gallery">'.
		$ig_gallery.': '.htmlspecialchars($gallery['name']).'</cms:plugin><br /><br />';	
	?>
	
	<script type="text/javascript" language="javascript">
	var oEditor = opener.FCKeditorAPI.GetInstance('content') ;
	oEditor.InsertHtml('<?php echo addslashes($html); ?>');
	window.close();
	</script>
	
	<?php
	exit();
}


require_once ($GO_THEME->theme_path."header.inc");

$form = new form('gallery_form');
$form->add_html_element(new input('hidden','task'));
$form->add_html_element(new input('hidden','gallery_id'));


$tabstrip = new tabstrip('select_plugin_tabstrip', $ig_insert_gallery);
$tabstrip->set_attribute('style','width:100%');


$ig->get_authorized_galleries($GO_SECURITY->user_id);
while($ig->next_record())
{
	$link = new hyperlink('javascript:insertGallery('.$ig->f('id').');',$ig->f('name'));
	$link->set_attribute('class','selectableItem');
	$tabstrip->add_html_element($link);
}		





$form->add_html_element($tabstrip);

echo $form->get_html();
?>
<script type="text/javascript" language="javascript">
function insertGallery(gallery_id)
{
	document.gallery_form.gallery_id.value=gallery_id;
	document.gallery_form.task.value='insert';
	document.gallery_form.submit();
}
</script>
<?php
require_once ($GO_THEME->theme_path."footer.inc");
