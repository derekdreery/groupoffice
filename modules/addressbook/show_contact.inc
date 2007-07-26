<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.9 $ $Date: 2005/07/14 12:34:19 $
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

$contact = array_map('htmlspecialchars', $contact);
$contact = array_map('empty_to_stripe', $contact);

$maintable = new table();
$maintable->set_attribute('style','width:100%;');
$mainrow = new table_row();

$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($strFirstName.':'));
$row->add_cell(new table_cell($contact['first_name']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strMiddleName.':'));
$row->add_cell(new table_cell($contact['middle_name']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strLastName.':'));
$row->add_cell(new table_cell($contact['last_name']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strTitle.' / '.$strInitials.':'));
$span = new html_element('span', ' / ');
$span->set_attribute('style', 'width: 20px;text-align:center;');
$row->add_cell(new table_cell( $contact['title'].$span->get_html().$contact['initials']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strSex.':'));
$radiogroup = new radiogroup('sex', $contact['sex']);
$male_button = new radiobutton('sex_m', 'M');
$female_button = new radiobutton('sex_f', 'F');

$row->add_cell(new table_cell($strSexes[$contact['sex']]));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strBirthday.':'));
$birthday = $contact['birthday'] > 0 ? date($_SESSION['GO_SESSION']['date_format'], $contact['birthday']) : '';
$row->add_cell(new table_cell($birthday ));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strAddress.':'));
$row->add_cell(new table_cell($contact['address'].' '.$contact['address_no']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strZip.':'));
$row->add_cell(new table_cell($contact['zip']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCity.':'));
$row->add_cell(new table_cell($contact['city']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strState.':'));
$row->add_cell(new table_cell($contact['state']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCountry.':'));
$row->add_cell(new table_cell($contact['country']));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($ab_comment.':'));
$row->add_cell(new table_cell($contact['comment']));
$table->add_row($row);

$cell = new table_cell($table->get_html());
$cell->set_attribute('valign','top');
$mainrow->add_cell($cell);

$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($strEmail.':'));
$row->add_cell(new table_cell($contact['email']));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strPhone.':'));
$row->add_cell(new table_cell($contact['home_phone']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strFax.':'));
$row->add_cell(new table_cell($contact['fax']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCellular.':'));
$row->add_cell(new table_cell($contact['cellular']));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strCompany.':'));
$row->add_cell(new table_cell(show_company($contact['company_id'])));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strDepartment.':'));
$row->add_cell(new table_cell($contact['department']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strFunction.':'));
$row->add_cell(new table_cell($contact['function']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strWorkphone.':'));
$row->add_cell(new table_cell($contact['work_phone']));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strWorkFax.':'));
$row->add_cell(new table_cell($contact['work_fax']));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);




$cell = new table_cell($table->get_html());
$cell->set_attribute('valign','top');


$mainrow->add_cell($cell);
$maintable->add_row($mainrow);

$tabstrip->add_html_element($maintable);

if ($contact_id > 0)
{
	$tabstrip->add_html_element(new button($cmdVcard, "document.location='export.php?contact_id=$contact_id';"));

	$copy_text = htmlspecialchars(addslashes(address_format($contact, "\n")));
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
