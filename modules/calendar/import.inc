<?php
$form->add_html_element(new input('hidden', 'MAX_FILE_SIZE', $GO_CONFIG->max_file_size));

if(isset($feedback))
{
	$p = new html_element('p',$feedback);
	$p->set_attribute('class','Error');
	$tabstrip->add_html_element($p);
}
$p = new html_element('p',$cal_import_text);
$tabstrip->add_html_element($p);
	
$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($cal_filename.'*: '));
$input = new input('file', 'ical_file',$calendar['name']);
$input->set_attribute('maxlength','100');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$tabstrip->add_html_element($table);
$tabstrip->add_html_element(new button($cmdOk, 'javascript:upload();'));
$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."'"));


$div = new html_element('div');
$div->set_attribute('id','status');
$div->set_attribute('class','Success');

$tabstrip->add_html_element($div);
