<?php
if(isset($_POST['archive_files']))
{
	$files = $_POST['archive_files'];
}elseif(isset($_POST['fs_list']['selected']))
{
	$files = $_POST['fs_list']['selected'];
}else
{
	$files = array();
}

while($file = array_shift($files))
{
	$form->add_html_element(new input('hidden','archive_files[]', $file));	
}


$tabstrip = new tabstrip('go_archiver', $fb_compress);
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to("javascript:document.forms[0].task.value='';document.forms[0].submit();");

if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $tabstrip->add_html_element($p);
}
$compression_type = isset($_POST['compression_type']) ? $_POST['compression_type'] : 'zip';

$name = isset($_POST['name']) ? $_POST['name'] : '';

$table = new table();
$row = new table_row();
$row->add_cell(new table_cell($strName.':*'));

$input = new input('text','name',$name);
$input->set_attribute('style','width:300px');

$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell($fb_compression_type.':');
$cell->set_attribute('valign','top');
$row->add_cell($cell);

$cell = new table_cell();
$radiogroup = new radiogroup('compression_type', $compression_type);
if(is_executable($GO_CONFIG->cmd_zip))
{
	$cell->innerHTML .= $radiogroup->get_option(new radiobutton('zip', 'zip'),'zip').'<br />';
}
if(is_executable($GO_CONFIG->cmd_tar))
{
	$cell->innerHTML .= $radiogroup->get_option(new radiobutton('gz', 'gz'), 'gzip');
}
$row->add_cell($cell);
$table->add_row($row);

$tabstrip->add_html_element($table);

$tabstrip->add_html_element(new button($cmdOk, "javascript:document.forms[0].task.value='save_archive';document.forms[0].submit();"));
$tabstrip->add_html_element(new button($cmdCancel, "javascript:document.forms[0].task.value='';document.forms[0].submit();"));

$form->add_html_element($tabstrip);
?>
<script type="text/javascript">

</script>
