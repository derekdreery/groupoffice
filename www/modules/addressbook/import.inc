<?php
$form->add_html_element(new input('hidden', 'MAX_FILE_SIZE', $GO_CONFIG->max_file_size));
$form->set_attribute('enctype','multipart/form-data');
$tabstrip->add_html_element(new html_element('p', $contacts_import_text));

$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($ab_file_type));

$file_type = isset($_REQUEST['file_type']) ? $_REQUEST['file_type'] : 'csv';
$select = new select('file_type', $file_type);
$select->set_attribute('onchange','javascript:document.addressbook_form.submit();');
$select->add_value('csv', "CSV (Comma Separated Values)");
$select->add_value('vcf', "VCF (vCard)");
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($fbFilename.'*: '));
$input = new input('file', 'import_file');
$input->set_attribute('style', 'width:300px');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$import_type = isset($_REQUEST['import_type']) ? $_REQUEST['import_type'] : 'contacts';
$row = new table_row();
$row->add_cell(new table_cell($ab_import_type.': '));
$radiogroup = new radiogroup('import_type', $import_type);
if($file_type=='csv')
{
	$radiogroup->set_attribute('disabled','true');
}
$contacts_button = new radiobutton('contacts', 'contacts');
$companies_button = new radiobutton('companies', 'companies');
$row->add_cell(new table_cell($radiogroup->get_option($contacts_button,  $contacts_contacts).$radiogroup->get_option($companies_button, $ab_companies)));
$table->add_row($row);

$tabstrip->add_html_element($table);

$tabstrip->add_html_element(new button($cmdOk, 'javascript:upload();'));
$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."'"));

$div = new html_element('div');
$div->set_attribute('class','Success');
$tabstrip->add_html_element($div);
?>
