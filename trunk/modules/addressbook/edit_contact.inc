<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.61 $ $Date: 2006/11/21 16:25:36 $
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
$cell = new table_cell();
$input = new input('text','first_name',$contact['first_name']);
$input->set_attribute('style','width:120px');
$input->set_attribute('maxlength','50');
$cell->add_html_element($input);

$input = new input('text','middle_name',$contact['middle_name']);
$input->set_attribute('style','width:34px');
$input->set_attribute('maxlength','50');
$cell->add_html_element($input);

$input = new input('text','last_name', $contact['last_name']);
$input->set_attribute('style','width:120px');
$input->set_attribute('maxlength','50');
$cell->add_html_element($input);

$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strTitle.' / '.$strInitials.':'));
$input1 = new input('text','title', $contact['title']);
$input1->set_attribute('style','width:135px');
$input1->set_attribute('maxlength','12');

$span = new html_element('span', ' / ');
$span->set_attribute('style', 'width: 20px;text-align:center;');

$input2 = new input('text','initials', $contact['initials']);
$input2->set_attribute('style','width:135px');
$input2->set_attribute('maxlength','50');

$row->add_cell(new table_cell($input1->get_html().$span->get_html().$input2->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strSex.':'));
$radiogroup = new radiogroup('sex', $contact['sex']);
$male_button = new radiobutton('sex_m', 'M');
$female_button = new radiobutton('sex_f', 'F');

$row->add_cell(new table_cell($radiogroup->get_option($male_button, $strSexes['M']).$radiogroup->get_option($female_button, $strSexes['F'])));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strBirthday.':'));
$row->add_cell(new table_cell($birthday_picker->get_html()));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);







$row = new table_row();
$row->add_cell(new table_cell($strEmail.' 1:'));
$input = new input('text','email', $contact['email']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);
$row = new table_row();
$row->add_cell(new table_cell($strEmail.' 2:'));
$input = new input('text','email2', $contact['email2']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);
$row = new table_row();
$row->add_cell(new table_cell($strEmail.' 3:'));
$input = new input('text','email3', $contact['email3']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strPhone.':'));
$input = new input('text','home_phone', $contact['home_phone']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strFax.':'));
$input = new input('text','fax', $contact['fax']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCellular.':'));
$input = new input('text','cellular', $contact['cellular']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($ab_comment.':'));
$input = new input('text','comment', $contact['comment']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);


$cell = new table_cell($table->get_html());
$cell->set_attribute('valign','top');
$mainrow->add_cell($cell);


$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($strAddress.':'));
$input = new input('text','address', $contact['address']);
$input->set_attribute('style','width:230px');
$input->set_attribute('maxlength','50');

$input1 = new input('text','address_no', $contact['address_no']);
$input1->set_attribute('style','width:47px');
$input1->set_attribute('maxlength','10');

$row->add_cell(new table_cell($input->get_html().$input1->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strZip.':'));
$input = new input('text','zip', $contact['zip']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCity.':'));
$input = new input('text','city', $contact['city']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strState.':'));
$input = new input('text','state', $contact['state']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','30');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCountry.':'));
$input = new input('text','country', $contact['country']);
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
$row->add_cell(new table_cell($strCompany.':'));

load_control('autocomplete');
$xml_fields=array(
	'name'=>'company_name',
	'id'=>'company_id',
	'phone'=>'work_phone',
	'fax'=>'work_fax',
	'email'=>'email'
	);

$contact['company_id'];
$company = $ab->get_company($contact['company_id']);
$company_name = isset($company['name']) ? $company['name'] : '';
	
$company_autocomplete = new autocomplete(
	'company_id',
	false,
	'company_name',
	$company_name,
	'contact_form',
	$GO_MODULES->modules['addressbook']['url'].'search_company_xml.php',
	'company',
	$xml_fields,	
	false	
	);
$company_autocomplete->add_bound_hidden_value('company_id', $contact['company_id'], $GO_MODULES->modules['addressbook']['url'].'company.php?return_to='.urlencode($link_back).'&company_id=');
	
$row->add_cell(new table_cell($company_autocomplete->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strDepartment.':'));
$input = new input('text','department', $contact['department']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strFunction.':'));
$input = new input('text','function', $contact['function']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strWorkphone.':'));
$input = new input('text','work_phone', $contact['work_phone']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strWorkFax.':'));
$input = new input('text','work_fax', $contact['work_fax']);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($contacts_color.':'));

$contact['color'] = isset($contact['color']) ? $contact['color'] : '000000';
$color_selector = new color_selector('color', 'color', $contact['color']);
$row->add_cell(new table_cell($color_selector->get_html()));
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


$tabstrip->add_html_element(new button($cmdOk, "javascript:_save('save','true');"));
$tabstrip->add_html_element(new button($cmdApply, "javascript:_save('save','false');"));

if ($contact["source_id"] > 0 && $contact_id > 0)
{
	$tabstrip->add_html_element(new button($cmdUpdate, 'javascript:update()'));
}
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

?>
			
<script type="text/javascript" language="javascript">
function update()
{
	document.forms[0].task.value = "update";
	document.forms[0].submit();
}
function select_addressbook(addressbook_id)
{
	document.forms[0].addressbook_id.value=addressbook_id;
	document.forms[0].submit();
}


</script>
