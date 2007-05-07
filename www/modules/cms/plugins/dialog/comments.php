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
	
	$allow_unregistered = isset($_POST['allow_unregistered']) ? 'true' : 'false';
	
	require_once($GO_MODULES->path.'plugins/comments.class.inc');
	$cms_comments = new cms_comments();	

	$html = '<cms:plugin email="'.smart_stripslashes($_POST['email']).'" allow_unregistered="'.$allow_unregistered.'" plugin_id="comments">'.
	htmlspecialchars($cms_comments->get_name()).'</cms:plugin><br /><br />';	
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

$form = new form('comments_form');
$form->add_html_element(new input('hidden','task', 'insert'));

$tabstrip = new tabstrip('select_plugin_tabstrip', $cms_insert_comments);
$tabstrip->set_attribute('style','width:100%');

if (isset($feedback))
{
	$p = new html_element('p', $feedback);
	$p->set_attribute('class', 'Error');			
	$tabstrip->add_html_element($p);
}



$table = new table();
$row = new table_row();
$checkbox = new checkbox('allow_unregistered', 'allow_unregistered','true',$cms_unregistered_comments, isset($_POST['allow_unregistered']));
$cell = new table_cell($checkbox);
$cell->set_attribute('colspan','2');
$row->add_cell($cell);		
$table->add_row($row);

$row = new table_row();
$cell = new table_cell($strEmail.':');
$cell->set_attribute('style', 'whitespace:nowrap;');
$row->add_cell($cell);		
$cell = new table_cell();
$email = isset($_REQUEST['email']) ? smart_stripslashes($_REQUEST['email']) : $_SESSION['GO_SESSION']['email'];
$input = new input('text', 'email', $email);
$input->set_attribute('style','width:200px');
$cell->add_html_element($input);
$row->add_cell($cell);
$table->add_row($row);


$tabstrip->add_html_element($table);

$tabstrip->innerHTML .= '<br />';

$tabstrip->add_html_element(new button($cmdOk, 'javascript:document.comments_form.submit();'));



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
