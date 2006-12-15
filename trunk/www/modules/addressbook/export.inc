<?php
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

$export_type = isset($_REQUEST['export_type']) ? $_REQUEST['export_type'] : 'contacts';
$row = new table_row();
$row->add_cell(new table_cell($ab_export_type.': '));
$radiogroup = new radiogroup('export_type', $export_type);
if($file_type=='csv')
{
	$radiogroup->set_attribute('disabled','true');
}
$contacts_button = new radiobutton('contacts', 'contacts');
$companies_button = new radiobutton('companies', 'companies');
$row->add_cell(new table_cell($radiogroup->get_option($contacts_button,  $contacts_contacts).$radiogroup->get_option($companies_button, $ab_companies)));
$table->add_row($row);

$tabstrip->add_html_element($table);

$seperator = isset($_POST['seperator']) ? $_POST['seperator'] : ',';
$quote = isset($_POST['quote']) ? $_POST['quote'] : '"';
$crlf = isset($_POST['crlf']) ? $_POST['crlf'] : '\r\n';

if($file_type == 'csv') {
	$table = new table();
	$row = new table_row();
	$row->add_cell(new table_cell($contacts_seperator_text.': '));
	$input = new input('text', 'seperator',$seperator);
	$input->set_attribute('maxlength','10');
	$input->set_attribute('style', 'width:30px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	$row = new table_row();
	$row->add_cell(new table_cell($contacts_quote.': '));
	$input = new input('text', 'quote',$quote);
	$input->set_attribute('maxlength','10');
	$input->set_attribute('style', 'width:30px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	$row = new table_row();
	$row->add_cell(new table_cell($contacts_clrf.': '));
	$input = new input('text', 'crlf',$crlf);
	$input->set_attribute('maxlength','10');
	$input->set_attribute('style', 'width:30px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
	$tabstrip->add_html_element($table);
}

$tabstrip->add_html_element(new button($cmdOk, "javascript:document.forms[0].task.value='export';document.forms[0].submit();javascript:document.forms[0].task.value='';"));
$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."'"));
?>
