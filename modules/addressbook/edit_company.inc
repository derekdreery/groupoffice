<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.22 $ $Date: 2006/06/01 11:58:45 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $tabstrip->add_html_element($p);
}

$maintable = new table();
$mainrow = new table_row();

$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($strName.'*:'));
$input = new input('text','name',$company['name']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strPhone.':'));
$input = new input('text','phone', $company['phone']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strFax.':'));
$input = new input('text','fax', $company['fax']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strEmail.':'));
$input = new input('text','email', $company['email']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','75');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);


$row = new table_row();
$cell = new table_cell($strVisitAddress);
$cell->set_attribute('colspan','2');
$cell->set_attribute('style','font-weight:bold;text-align:center');
$row->add_cell($cell);
$table->add_row($row);
	

$row = new table_row();
$row->add_cell(new table_cell($strAddress.':'));
$input = new input('text','address', $company['address']);
$input->set_attribute('style','width:230px');
$input->set_attribute('maxlength','100');

$input1 = new input('text','address_no', $company['address_no']);
$input1->set_attribute('style','width:47px');
$input1->set_attribute('maxlength','10');

$row->add_cell(new table_cell($input->get_html().$input1->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strZip.':'));
$input = new input('text','zip', $company['zip']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCity.':'));
$input = new input('text','city', $company['city']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strState.':'));
$input = new input('text','state', $company['state']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','30');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCountry.':'));
$input = new input('text','country', $company['country']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','30');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);


$cell = new table_cell($table->get_html());
$cell->set_attribute('valign','top');
$mainrow->add_cell($cell);


$table = new table();
$row = new table_row();
$cell = new table_cell($strPostAddress);
$cell->set_attribute('colspan','2');
$cell->set_attribute('style','font-weight:bold;text-align:center');
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strAddress.':'));
$input = new input('text','post_address', $company['post_address'], false);
$input->set_attribute('style','width:230px');
$input->set_attribute('maxlength','100');

$input1 = new input('text','post_address_no', $company['post_address_no'], false);
$input1->set_attribute('style','width:47px');
$input1->set_attribute('maxlength','10');

$row->add_cell(new table_cell($input->get_html().$input1->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strZip.':'));
$input = new input('text','post_zip', $company['post_zip'], false);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCity.':'));
$input = new input('text','post_city', $company['post_city'], false);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strState.':'));
$input = new input('text','post_state', $company['post_state'], false);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','30');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCountry.':'));
$input = new input('text','post_country', $company['post_country'], false);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','30');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);


$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);
		
$row = new table_row();
$row->add_cell(new table_cell($strHomepage.':'));
$input = new input('text','homepage', $company['homepage']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','100');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($ab_bank_no.':'));
$input = new input('text','bank_no', $company['bank_no']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($ab_vat_no.':'));
$input = new input('text','vat_no', $company['vat_no']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','40');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);	

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);


//get the given addressbook_id
if ($addressbook_id > 0)
{
	 $addressbook = $ab->get_addressbook($addressbook_id);
}

//if there was no or a read only addressbook given then change to the first writable
if (!isset($addressbook) || !$addressbook || !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_write']))
{
	$addressbook = $ab->get_first_writable_addressbook();
}

$row = new table_row();
$row->add_cell(new table_cell($ab_addressbook.':'));
$form->add_html_element(new input('hidden', 'addressbook_id', $addressbook['id'], false));
$link = new hyperlink("javascript:popup('select_addressbook.php?callback=select_addressbook&add_null=false&writable_only=true', '300','400');",$addressbook['name']);
$link->set_attribute('style','margin:3px;');
$link->set_attribute('class','normal');	
$row->add_cell(new table_cell($link->get_html()));	
$table->add_row($row);

$cell = new table_cell($table->get_html());
$cell->set_attribute('valign','top');

$mainrow->add_cell($cell);
$maintable->add_row($mainrow);

$tabstrip->add_html_element($maintable);


$tabstrip->add_html_element(new button($cmdOk, "javascript:_save('save_company','true')"));
$tabstrip->add_html_element(new button($cmdApply, "javascript:_save('save_company','false')"));

if($company_id > 0)
{
	$copy_text = htmlspecialchars(addslashes(address_format($company, "\n")));
	$copy_text = str_replace("\n", '\n', $copy_text);

	$tabstrip->add_html_element(new button($cmdCopy, "javascript:copy_clip('".$copy_text."');"));
}

if($popup)
{
	$tabstrip->add_html_element(new button($cmdClose, "javascript:window.close();"));
}else
{
	$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."'"));
}
?>
<script type="text/javascript" language="javascript">
function select_addressbook(addressbook_id)
{
	document.forms[0].addressbook_id.value=addressbook_id;
	document.forms[0].submit();
}
</script>
