<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.4 $ $Date: 2005/07/14 12:34:19 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
$company = array_map('htmlspecialchars', $company);
$company = array_map('empty_to_stripe', $company);
 
if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $tabstrip->add_html_element($p);
}

$maintable = new table();
$maintable->set_attribute('style','width:100%;');
$mainrow = new table_row();

$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($strName.':'));
$row->add_cell(new table_cell($company['name']));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strPhone.':'));
$row->add_cell(new table_cell($company['phone']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strFax.':'));
$row->add_cell(new table_cell($company['fax']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strEmail.':'));
$row->add_cell(new table_cell($company['email']));
$table->add_row($row);


$row = new table_row();
$cell = new table_cell($strVisitAddress);
$cell->set_attribute('colspan','2');
$cell->set_attribute('style','font-weight:bold;text-align:center');
$row->add_cell($cell);
$table->add_row($row);
	

$row = new table_row();
$row->add_cell(new table_cell($strAddress.':'));
$row->add_cell(new table_cell($company['address'].' '.$company['address_no']));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strZip.':'));
$row->add_cell(new table_cell($company['zip']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCity.':'));
$row->add_cell(new table_cell($company['city']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strState.':'));
$row->add_cell(new table_cell($company['state']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCountry.':'));
$row->add_cell(new table_cell($company['country']));
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
$row->add_cell(new table_cell($company['post_address'].' '.$company['post_address_no']));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strZip.':'));
$row->add_cell(new table_cell($company['post_zip']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCity.':'));
$row->add_cell(new table_cell($company['post_city']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strState.':'));
$row->add_cell(new table_cell($company['post_state']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCountry.':'));
$row->add_cell(new table_cell($company['post_country']));
$table->add_row($row);


$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);
		
$row = new table_row();
$row->add_cell(new table_cell($strHomepage.':'));
$row->add_cell(new table_cell($company['homepage']));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($ab_bank_no.':'));
$row->add_cell(new table_cell($company['bank_no']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($ab_vat_no.':'));
$row->add_cell(new table_cell($company['vat_no']));
$table->add_row($row);	

$cell = new table_cell($table->get_html());
$cell->set_attribute('valign','top');

$mainrow->add_cell($cell);
$maintable->add_row($mainrow);

$tabstrip->add_html_element($maintable);


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
