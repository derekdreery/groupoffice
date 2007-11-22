<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.141 $ $Date: 2006/11/28 13:10:16 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
require_once ("../../Group-Office.php");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('addressbook');
require_once ($GO_LANGUAGE->get_language_file('addressbook'));
load_basic_controls();
load_control('date_picker');
load_control('tooltip');
load_control('color_selector');


$page_title = $contact_profile;
require_once ($GO_MODULES->class_path."addressbook.class.inc");
$ab = new addressbook();



$task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';
$return_to = 'index.php';
$link_back = (isset ($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];
$contact_id = isset ($_REQUEST['contact_id']) ? smart_addslashes($_REQUEST['contact_id']) : '0';
$ab_settings = $ab->get_settings($GO_SECURITY->user_id);

$addressbook_id = $contact['addressbook_id'] = isset($_REQUEST['addressbook_id']) ? $_REQUEST['addressbook_id'] : $ab_settings['addressbook_id'];
if($addressbook_id == 0)
{
	$addressbook = $ab->get_addressbook();
	$addressbook_id = $addressbook['id'];
}

if($ab_settings['addressbook_id'] != $addressbook_id)
{
	$ab_settings['addressbook_id'] = $addressbook_id;
	$ab->update_settings($ab_settings);
}

function value_is_empty($value=null)
{
	if(isset($value) && $value!='')
	{
		return false;
	}
	return true;
}

if($task=='save')
{

	if(!value_is_empty($_REQUEST['first_name']))
	{
		$contact['first_name'] = smart_addslashes($_REQUEST['first_name']);
	}
	if(!value_is_empty($_REQUEST['middle_name']))
	{
		$contact['middle_name'] = smart_addslashes($_REQUEST['middle_name']);
	}
	if(!value_is_empty($_REQUEST['last_name']))
	{
		$contact['last_name'] = smart_addslashes($_REQUEST['last_name']);
	}
	if(!value_is_empty($_REQUEST['initials']))
	{
		$contact['initials'] = smart_addslashes($_REQUEST['initials']);
	}
	if(!value_is_empty($_REQUEST['title']))
	{
		$contact['title'] = smart_addslashes($_REQUEST['title']);
	}
	if(!value_is_empty($_REQUEST['sex']))
	{
		$contact['sex'] = smart_addslashes($_REQUEST['sex']);
	}
	if(!value_is_empty($_REQUEST['birthday']))
	{
		$contact['birthday'] =smart_addslashes(date_to_db_date($_REQUEST['birthday']));
	}
	if(!value_is_empty($_REQUEST['email']))
	{
		$contact['email'] = smart_addslashes($_REQUEST['email']);
	}
	if(!value_is_empty($_REQUEST['email2']))
	{
		$contact['email2'] = smart_addslashes($_REQUEST['email2']);
	}
	if(!value_is_empty($_REQUEST['email3']))
	{
		$contact['email3'] = smart_addslashes($_REQUEST['email3']);
	}
	if(!value_is_empty($_REQUEST['work_phone']))
	{
		$contact['work_phone'] =smart_addslashes($_REQUEST['work_phone']);
	}
	if(!value_is_empty($_REQUEST['home_phone']))
	{
		$contact['home_phone'] = smart_addslashes($_REQUEST['home_phone']);
	}
	if(!value_is_empty($_REQUEST['fax']))
	{
		$contact['fax'] =smart_addslashes($_REQUEST['fax']);
	}
	if(!value_is_empty($_REQUEST['work_fax']))
	{
		$contact['work_fax'] =smart_addslashes($_REQUEST['work_fax']);
	}
	if(!value_is_empty($_REQUEST['cellular']))
	{
		$contact['cellular'] =smart_addslashes($_REQUEST['cellular']);
	}
	if(!value_is_empty($_REQUEST['country']))
	{
		$contact['country'] = smart_addslashes($_REQUEST['country']);
	}
	if(!value_is_empty($_REQUEST['state']))
	{
		$contact['state'] =smart_addslashes($_REQUEST['state']);
	}
	if(!value_is_empty($_REQUEST['address']))
	{
		$contact['address'] = smart_addslashes($_REQUEST['address']);
	}
	if(!value_is_empty($_REQUEST['address_no']))
	{
		$contact['address_no'] = smart_addslashes($_REQUEST['address_no']);
	}
	if(!value_is_empty($_REQUEST['city']))
	{
		$contact['city'] = smart_addslashes($_REQUEST['city']);
	}
	if(!value_is_empty($_REQUEST['zip']))
	{
		$contact['zip'] = smart_addslashes($_REQUEST['zip']);
	}
	if(!value_is_empty($_REQUEST['company_id']))
	{

		$contact['company_id'] = smart_addslashes($_REQUEST['company_id']);
	}


	if(!value_is_empty($_REQUEST['department']))
	{
		$contact['department'] = smart_addslashes($_REQUEST['department']);
	}
	if(!value_is_empty($_REQUEST['function']))
	{
		$contact['function'] = smart_addslashes($_REQUEST['function']);
	}
	if(!value_is_empty($_REQUEST['comment']))
	{
		$contact['comment'] = smart_addslashes($_REQUEST['comment']);
	}
	if(!value_is_empty($_REQUEST['color']))
	{
		$contact['color'] = smart_addslashes($_REQUEST['color']);
	}

	foreach($_POST['contacts']['selected'] as $contact_id)
	{
		$contact['id'] = $contact_id;

		$existing_contact = $ab->get_contact($contact_id);
		if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $existing_contact['acl_write']))
		{
			$feedback = $strAccessDenied;
			break;
		}

		if(value_is_empty($existing_contact['link_id']))
		{
			$contact['link_id'] = $GO_LINKS->get_link_id();
		}else {
			$contact['link_id'] = $existing_contact['link_id'];
		}

		$ab->update_contact($contact);


		if(isset($GO_MODULES->modules['custom_fields']))
		{

			require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
			$cf = new custom_fields();
			$cf2 = new custom_fields();

			$cf->get_values(2, $contact['link_id']);

			$cf2->get_authorized_categories(2, $GO_SECURITY->user_id);
			while($cf2->next_record())
			{
				$cf->save_fields($cf2->f('id'), $contact['link_id'],array(),true);
			}
		}
	}
	if(!isset($feedback))
	{
		header('Location: '.$return_to);
		exit();
	}
}







if(isset($_REQUEST['feedback']))
{
	$feedback=smart_stripslashes($_REQUEST['feedback']);
}





$contact['first_name'] = isset ($_REQUEST['first_name']) ? smart_stripslashes($_REQUEST['first_name']) : '';
$contact['middle_name'] = isset ($_REQUEST['middle_name']) ? smart_stripslashes($_REQUEST['middle_name']) : '';
$contact['last_name'] = isset ($_REQUEST['last_name']) ? smart_stripslashes($_REQUEST['last_name']) : '';
$contact['initials'] = isset ($_REQUEST['initials']) ? smart_stripslashes($_REQUEST['initials']) : '';
$contact['title'] = isset ($_REQUEST['title']) ? smart_stripslashes($_REQUEST['title']) : '';
$contact['sex'] = isset ($_REQUEST['sex']) ? smart_stripslashes($_REQUEST['sex']) : '';
$birthday = isset ($_REQUEST['birthday']) ? smart_stripslashes($_REQUEST['birthday']) : '';
$contact['email'] = isset ($_REQUEST['email']) ? smart_stripslashes($_REQUEST['email']) : '';
$contact['email2'] = isset ($_REQUEST['email2']) ? smart_stripslashes($_REQUEST['email2']) : '';
$contact['email3'] = isset ($_REQUEST['email3']) ? smart_stripslashes($_REQUEST['email3']) : '';
$contact['work_phone'] = isset ($_REQUEST['work_phone']) ? smart_stripslashes($_REQUEST['work_phone']) : '';
$contact['home_phone'] = isset ($_REQUEST['home_phone']) ? smart_stripslashes($_REQUEST['home_phone']) : '';
$contact['fax'] = isset ($_REQUEST['fax']) ? smart_stripslashes($_REQUEST['fax']) : '';
$contact['work_fax'] = isset ($_REQUEST['work_fax']) ? smart_stripslashes($_REQUEST['work_fax']) : '';
$contact['cellular'] = isset ($_REQUEST['cellular']) ? smart_stripslashes($_REQUEST['cellular']) : '';
$contact['country'] = isset ($_REQUEST['country']) ? smart_stripslashes($_REQUEST['country']) : '';
$contact['state'] = isset ($_REQUEST['state']) ? smart_stripslashes($_REQUEST['state']) : '';
$contact['address'] = isset ($_REQUEST['address']) ? smart_stripslashes($_REQUEST['address']) : '';
$contact['address_no'] = isset ($_REQUEST['address_no']) ? smart_stripslashes($_REQUEST['address_no']) : '';
$contact['city'] = isset ($_REQUEST['city']) ? smart_stripslashes($_REQUEST['city']) : '';
$contact['zip'] = isset ($_REQUEST['zip']) ? smart_stripslashes($_REQUEST['zip']) : '';

if ($contact['zip'] != '' && $contact['address'] == '' && $contact['city'] == '' && $addressinfo = $ab->get_addressinfo($contact['zip'])) {
	$contact['address'] = $addressinfo['street'];
	$contact['city'] = $addressinfo['city'];
}

$contact['department'] = isset ($_REQUEST['department']) ? smart_stripslashes($_REQUEST['department']) : '';
$contact['function'] = isset ($_REQUEST['function']) ? smart_stripslashes($_REQUEST['function']) : '';
$contact['comment'] = isset ($_REQUEST['comment']) ? smart_stripslashes($_REQUEST['comment']) : '';
$contact['color'] = isset ($_REQUEST['color']) ? smart_stripslashes($_REQUEST['color']) : '000000';
$contact['source_id'] = isset ($_REQUEST['source_id']) ? $_REQUEST['source_id'] : '';


if(!isset($contact['company_id']))
{
	$contact['company_id'] = isset ($_REQUEST['company_id']) ? $_REQUEST['company_id'] : 0;
}



$birthday_picker = new date_picker('birthday', $_SESSION['GO_SESSION']['date_format'], $birthday);
$GO_HEADER['head'] = $birthday_picker->get_header();
$GO_HEADER['head'] .= color_selector::get_header();
require_once ($GO_THEME->theme_path."header.inc");

$form = new form('edit_contacts_form');
$form->add_html_element(new input('hidden','task','',false));
$form->add_html_element(new input('hidden','return_to',$return_to));
$form->add_html_element(new input('hidden','link_back',$link_back));

$tabstrip = new tabstrip('edit_contacts_tabstrip', $ab_edit_selected);
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to($return_to);


if (isset($feedback))
{
	$p = new html_element('p', $feedback);
	$p->set_attribute('class','Error');
	$tabstrip->add_html_element($p);
}

$form->add_html_element(new input('hidden','id_array', 'contacts'));

$edit_count = count($_POST[$_REQUEST['id_array']]['selected']);
foreach($_POST[$_REQUEST['id_array']]['selected'] as $contact_id)
{
	$form->add_html_element(new input('hidden','contacts[selected][]', $contact_id));
}


$p = new html_element('p',sprintf($ab_edit_selected_warning, $edit_count, $ab_contacts));
$tabstrip->add_html_element($p);

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
$nochange_button = new radiobutton('dontchange', '');
$male_button = new radiobutton('sex_m', 'M');
$female_button = new radiobutton('sex_f', 'F');

$row->add_cell(new table_cell($radiogroup->get_option($nochange_button, $ab_dontchange).$radiogroup->get_option($male_button, $strSexes['M']).$radiogroup->get_option($female_button, $strSexes['F'])));
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




if(isset($GO_MODULES->modules['custom_fields']))
{
	require($GO_LANGUAGE->get_language_file('custom_fields'));
	require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
	$cf = new custom_fields();
	$cf2=new custom_fields();

	$cf2->get_authorized_categories(2,$GO_SECURITY->user_id);

	while($cf2->next_record())
	{

		$row =new table_row();
		$cell = new table_cell('<br /><b>'.$cf2->f('name').'</b>');
		$cell->set_attribute('colspan','2');
		$row->add_cell($cell);
		$table->add_row($row);

		if($cf_table = $cf->get_fields_table($cf2->f('id'),0,array(),true))
		{
			$table->rows=array_merge($table->rows, $cf_table->rows);
		}
	}
}




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
'edit_contacts_form',
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





$tabstrip->add_html_element(new button($cmdOk, "javascript:_save('save');"));
$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."'"));


$form->add_html_element($tabstrip);
echo $form->get_html();
?>
<script type="text/javascript" language="javascript">
function _save(task)
{

	document.forms[0].task.value=task;
	document.forms[0].submit();
}
function activate_linking(goto_url)
{
	document.contact_form.goto_url.value=goto_url;
	document.contact_form.task.value='activate_linking';
	document.contact_form.submit();
}
function select_addressbook(addressbook_id)
{
	document.forms[0].addressbook_id.value=addressbook_id;
	document.forms[0].submit();
}
</script>
<?php
require_once ($GO_THEME->theme_path."footer.inc");
