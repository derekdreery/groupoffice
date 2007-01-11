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
	$email_to = smart_stripslashes($_POST['email_to']);
	$height = number_to_phpnumber(smart_stripslashes($_POST['height']));
	
	require_once($GO_MODULES->path.'plugins/contact.class.inc');
	$cms_contact = new cms_contact();
	
	if(validate_email($email_to))
	{
		$feedback = $GLOBALS['error_email'];
	}else {
		$html = '<cms:plugin email_to="'.$email_to.'" height="'.$height.'" plugin_id="contact">'.
		htmlspecialchars($cms_contact->get_name()).'</cms:plugin><br /><br />';		
	
		
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

$form = new form('contact_form');
$form->add_html_element(new input('hidden','task', 'insert'));

$tabstrip = new tabstrip('select_plugin_tabstrip', $cms_insert_contactform);
$tabstrip->set_attribute('style','width:100%');

if (isset($feedback))
{
	$p = new html_element('p', $feedback);
	$p->set_attribute('class', 'Error');			
	$tabstrip->add_html_element($p);
}


$table = new table();

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

$row = new table_row();
$cell = new table_cell($cms_message_height.':');
$cell->set_attribute('style', 'whitespace:nowrap;');
$row->add_cell($cell);		
$cell = new table_cell();
$height = isset($_REQUEST['height']) ? smart_stripslashes($_REQUEST['height']) : '150';
$input = new input('text', 'height',  format_number($height,0));
$input->set_attribute('onblur', "javascript:this.value=number_format(this.value, 0, '".$_SESSION['GO_SESSION']['decimal_seperator']."', '".$_SESSION['GO_SESSION']['thousands_seperator']."');");
$input->set_attribute('onfocus','this.select();');
$input->set_attribute('style','width:50px;text-align:right;');
$cell->add_html_element($input);
$row->add_cell($cell);
$table->add_row($row);

$tabstrip->add_html_element($table);

$tabstrip->add_html_element(new button($cmdOk, 'javascript:document.contact_form.submit();'));



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
