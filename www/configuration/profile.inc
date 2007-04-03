<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.11 $ $Date: 2006/10/20 14:08:02 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */


$maintable = new table();
$mainrow = new table_row();

$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($strFirstName.'*:'));
$input = new input('text','first_name',$user['first_name'],true,true);
if(in_array('first_name', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strMiddleName.':'));
$input = new input('text','middle_name',$user['middle_name']);
if(in_array('middle_name', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strLastName.'*:'));
$input = new input('text','last_name', $user['last_name'],true,true);
if(in_array('last_name', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
if(in_array('inititals_title',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($GLOBALS['strTitle'].' / '.$GLOBALS['strInitials'].$end));
$input1 = new input('text','title', $user['title'],true, $required);
$input1->set_attribute('style','width:135px');
$input1->set_attribute('maxlength','12');
if(in_array('title', $disabled_user_fields))
{
	$input1->set_attribute('disabled', 'true');
}

$span = new html_element('span', ' / ');
$span->set_attribute('style', 'width: 20px;text-align:center;');

$input2 = new input('text','initials', $user['initials'],true,$required);
$input2->set_attribute('style','width:135px');
$input2->set_attribute('maxlength','50');
if(in_array('initials', $disabled_user_fields))
{
	$input2->set_attribute('disabled', 'true');
}

$row->add_cell(new table_cell($input1->get_html().$span->get_html().$input2->get_html()));
$table->add_row($row);


$row = new table_row();


if(in_array('sex',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strSex.$end));
if(!in_array('sex', $disabled_user_fields))
{
	$radiogroup = new radiogroup('sex', $user['sex']);
	$male_button = new radiobutton('sex_m', 'M', $required);
	$female_button = new radiobutton('sex_f', 'F', $required);
	
	$row->add_cell(new table_cell($radiogroup->get_option($male_button, $strSexes['M']).$radiogroup->get_option($female_button, $strSexes['F'])));
	$table->add_row($row);
}else {
	$row->add_cell(new table_cell($strSexes[$user['sex']]));
	$table->add_row($row);
}


$row = new table_row();
if(in_array('birthday',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strBirthday.$end));
if(in_array('birthday', $disabled_user_fields))
{
	$row->add_cell(new table_cell($birthday));
}else {
	$row->add_cell(new table_cell($birthday_picker->get_html()));
}
$table->add_row($row);

$row = new table_row();
if(in_array('address',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strAddressAndNo.$end));
$input = new input('text','address', $user['address'], true, $required);
$input->set_attribute('style','width:230px');
$input->set_attribute('maxlength','50');
if(in_array('address', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}

$input1 = new input('text','address_no', $user['address_no'], true, $required);
$input1->set_attribute('style','width:47px');
$input1->set_attribute('maxlength','10');

if(in_array('address_no', $disabled_user_fields))
{
	$input1->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($input->get_html().$input1->get_html()));
$table->add_row($row);

$row = new table_row();
if(in_array('zip',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strZip.$end));
$input = new input('text','zip', $user['zip'], true, $required);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
if(in_array('zip', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
if(in_array('city',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strCity.$end));
$input = new input('text','city', $user['city'], true, $required);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
if(in_array('city', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
if(in_array('state',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strState.$end));
$input = new input('text','state', $user['state'], true, $required);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','30');
if(in_array('state', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
if(in_array('country_id',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strCountry.$end));
$select = new select('country_id', $user['country_id'],false, $required);
$select->add_value('0', $cmdPleaseSelect);
$GO_USERS->get_countries();
while($GO_USERS->next_record())
{
	$select->add_value($GO_USERS->f('id'), $GO_USERS->f('name'));
}
if(in_array('country_id', $disabled_user_fields))
{
	$select->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);


$row = new table_row();
if(in_array('email',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strEmail.'*'));
$input = new input('text','email', $user['email'], true, $required);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
if(in_array('email', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);


$row = new table_row();
if(in_array('home_phone',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
};
$row->add_cell(new table_cell($strPhone.$end));
$input = new input('text','home_phone', $user['home_phone'], true, $required);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
if(in_array('home_phone', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
if(in_array('fax',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strFax.$end));
$input = new input('text','fax', $user['fax'], true, $required);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
if(in_array('fax', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
if(in_array('cellular',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strCellular.$end));
$input = new input('text','cellular', $user['cellular'], true, $required);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
if(in_array('cellular', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$cell = new table_cell($table->get_html());
$cell->set_attribute('valign','top');
$mainrow->add_cell($cell);


$table = new table();

$row = new table_row();
if(in_array('company',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strCompany.$end));
$input = new input('text','company', $user['company'], true, $required);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
if(in_array('company', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
if(in_array('department',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strDepartment.$end));
$input = new input('text','department', $user['department'], true, $required);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
if(in_array('department', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
if(in_array('function',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
};
$row->add_cell(new table_cell($strFunction.$end));
$input = new input('text','function', $user['function'], true, $required);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
if(in_array('function', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
if(in_array('work_address',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strAddress.$end));
$input = new input('text','work_address', $user['work_address'], true, $required);
$input->set_attribute('style','width:230px');
$input->set_attribute('maxlength','50');
if(in_array('work_address', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$input1 = new input('text','work_address_no', $user['work_address_no'], true, $required);
$input1->set_attribute('style','width:47px');
$input1->set_attribute('maxlength','10');

if(in_array('address_no', $disabled_user_fields))
{
	$input1->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($input->get_html().$input1->get_html()));
$table->add_row($row);


$row = new table_row();
if(in_array('work_zip',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strZip.$end));
$input = new input('text','work_zip', $user['work_zip'], true, $required);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
if(in_array('work_zip', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
if(in_array('work_city',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strCity.$end));
$input = new input('text','work_city', $user['work_city'], true, $required);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','50');
if(in_array('work_city', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
if(in_array('work_state',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strState.$end));
$input = new input('text','work_state', $user['work_state'], true, $required);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','30');
if(in_array('work_state', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();

if(in_array('work_country_id',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strCountry.$end));

$select = new select('work_country_id', $user['work_country_id'],false,$required);
$select->add_value('0', $cmdPleaseSelect);
$GO_USERS->get_countries();
while($GO_USERS->next_record())
{
	$select->add_value($GO_USERS->f('id'), $GO_USERS->f('name'));
}
if(in_array('work_country_id', $disabled_user_fields))
{
	$select->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
if(in_array('work_phone',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strPhone.$end));
$input = new input('text','work_phone', $user['work_phone'], true, $required);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
if(in_array('work_phone', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);



$row = new table_row();
if(in_array('work_fax',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strFax.$end));
$input = new input('text','work_fax', $user['work_fax'], true, $required);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
if(in_array('work_fax', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
if(in_array('homepage',$required_fields))
{
	$required=true;
	$end='*:';
}else {
	$end=':';
	$required=false;
}
$row->add_cell(new table_cell($strHomepage.$end));
$input = new input('text','homepage', $user['homepage'], true, $required);
$input->set_attribute('style','width:280px');
$input->set_attribute('maxlength','20');
if(in_array('homepage', $disabled_user_fields))
{
	$input->set_attribute('disabled', 'true');
}
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$cell = new table_cell($table->get_html());
$cell->set_attribute('valign','top');

$mainrow->add_cell($cell);
$maintable->add_row($mainrow);

$tabstrip->add_html_element($maintable);


if ($_SESSION['GO_SESSION']['first_name'] == '' || $_SESSION['GO_SESSION']['last_name'] == '' || $_SESSION['GO_SESSION']['email'] == '')
{
	$input = new input('hidden', 'load_frames', 'true');
	$form->add_html_element($input);
}
$button = new button($cmdSave, "javascript:_save('save_profile')");
$tabstrip->add_html_element($button);
