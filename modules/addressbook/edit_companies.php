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

require_once ($GO_MODULES->class_path."addressbook.class.inc");
$ab = new addressbook();



$task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';
$return_to = 'index.php';
$link_back = (isset ($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];
$company_id = isset ($_REQUEST['company_id']) ? smart_addslashes($_REQUEST['company_id']) : '0';
$ab_settings = $ab->get_settings($GO_SECURITY->user_id);

$addressbook_id = $company['addressbook_id'] = isset($_REQUEST['addressbook_id']) ? $_REQUEST['addressbook_id'] : $ab_settings['addressbook_id'];
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

	


	if(!value_is_empty($_REQUEST['name']))
	{
		$company['name'] = smart_addslashes($_REQUEST['name']);
	}
	if(!value_is_empty($_REQUEST['address']))
	{
		$company['address'] = smart_addslashes($_REQUEST['address']);
	}
	if(!value_is_empty($_REQUEST['vat_no']))
	{
		$company['vat_no'] = smart_addslashes($_REQUEST['vat_no']);
	}
	if(!value_is_empty($_REQUEST['bank_no']))
	{
		$company['bank_no'] = smart_addslashes($_REQUEST['bank_no']);
	}
	if(!value_is_empty($_REQUEST['homepage']))
	{
		$company['homepage'] = smart_addslashes($_REQUEST['homepage']);
	}
	if(!value_is_empty($_REQUEST['fax']))
	{
		$company['fax'] = smart_addslashes($_REQUEST['fax']);
	}
	if(!value_is_empty($_REQUEST['phone']))
	{
		$company['phone'] =smart_addslashes(date_to_db_date($_REQUEST['phone']));
	}
	if(!value_is_empty($_REQUEST['email']))
	{
		$company['email'] = smart_addslashes($_REQUEST['email']);
	}
	if(!value_is_empty($_REQUEST['post_country']))
	{
		$company['post_country'] =smart_addslashes($_REQUEST['post_country']);
	}
	if(!value_is_empty($_REQUEST['post_state']))
	{
		$company['post_state'] = smart_addslashes($_REQUEST['post_state']);
	}
	
	if(!value_is_empty($_REQUEST['post_city']))
	{
		$company['post_city'] =smart_addslashes($_REQUEST['post_city']);
	}
	if(!value_is_empty($_REQUEST['post_zip']))
	{
		$company['post_zip'] =smart_addslashes($_REQUEST['wopost_ziprk_fax']);
	}
	if(!value_is_empty($_REQUEST['post_address_no']))
	{
		$company['post_address_no'] =smart_addslashes($_REQUEST['post_address_no']);
	}
	if(!value_is_empty($_REQUEST['country']))
	{
		$company['country'] = smart_addslashes($_REQUEST['country']);
	}
	if(!value_is_empty($_REQUEST['state']))
	{
		$company['state'] =smart_addslashes($_REQUEST['state']);
	}
	if(!value_is_empty($_REQUEST['address']))
	{
		$company['address'] = smart_addslashes($_REQUEST['address']);
	}
	if(!value_is_empty($_REQUEST['address_no']))
	{
		$company['address_no'] = smart_addslashes($_REQUEST['address_no']);
	}
	if(!value_is_empty($_REQUEST['city']))
	{
		$company['city'] = smart_addslashes($_REQUEST['city']);
	}
	if(!value_is_empty($_REQUEST['zip']))
	{
		$company['zip'] = smart_addslashes($_REQUEST['zip']);
	}



	foreach($_POST['companies']['selected'] as $company_id)
	{
		$company['id'] = $company_id;

		$existing_company = $ab->get_company($company_id);
		
		if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $existing_company['acl_write']))
		{
			$feedback = $strAccessDenied;
			break;
		}
		

		if(value_is_empty($existing_company['link_id']))
		{
			$company['link_id'] = $GO_LINKS->get_link_id();
		}else {
			$company['link_id'] = $existing_company['link_id'];
		}

		$ab->update_company($company);


		if(isset($GO_MODULES->modules['custom_fields']))
		{

			require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
			$cf = new custom_fields();
			$cf2 = new custom_fields();

			$cf->get_values(3, $company['link_id']);

			$cf2->get_authorized_categories(3, $GO_SECURITY->user_id);
			while($cf2->next_record())
			{
				$cf->save_fields($cf2->f('id'), $company['link_id'],array(),true);
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





$company['name'] = isset ($_REQUEST['name']) ? smart_stripslashes($_REQUEST['name']) : '';
$company['address'] = isset ($_REQUEST['address']) ? smart_stripslashes($_REQUEST['address']) : '';
$company['address_no'] = isset ($_REQUEST['address_no']) ? smart_stripslashes($_REQUEST['address_no']) : '';
$company['zip'] = isset ($_REQUEST['zip']) ? smart_stripslashes($_REQUEST['zip']) : '';
$company['city'] = isset ($_REQUEST['city']) ? smart_stripslashes($_REQUEST['city']) : '';
$company['state'] = isset ($_REQUEST['state']) ? smart_stripslashes($_REQUEST['state']) : '';
$company['country'] = isset ($_REQUEST['country']) ? smart_stripslashes($_REQUEST['country']) : '';
$company['post_address'] =
(isset ($_REQUEST['post_address'])  && !empty($_REQUEST['post_address'])) ?
smart_stripslashes($_REQUEST['post_address']) : $company['address'];

$company['post_address_no'] =
(isset($_REQUEST['post_address_no']) && !empty($_REQUEST['post_address_no'])) ?
smart_stripslashes($_REQUEST['post_address_no']) :  $company['address_no'];

$company['post_zip'] =
(isset ($_REQUEST['post_zip']) && !empty($_REQUEST['post_zip']))
? smart_stripslashes($_REQUEST['post_zip']) : $company['zip'];

$company['post_city'] =
(isset ($_REQUEST['post_city']) && !empty($_REQUEST['post_city']))
? smart_stripslashes($_REQUEST['post_city']) : $company['city'];

$company['post_state'] =
( isset ($_REQUEST['post_state']) && !empty($_REQUEST['post_state']))
? smart_stripslashes($_REQUEST['post_state']) : $company['state'];

$company['post_country'] =
(isset($_REQUEST['post_country']) && !empty($_REQUEST['post_country']))
? smart_stripslashes($_REQUEST['post_country']) : $company['country'];

$company['email'] = isset ($_REQUEST['email']) ? smart_stripslashes($_REQUEST['email']) : '';

$company['phone'] = isset ($_REQUEST['phone']) ? smart_stripslashes($_REQUEST['phone']) : '';
$company['fax'] = isset ($_REQUEST['fax']) ? smart_stripslashes($_REQUEST['fax']) : '';
$company['homepage'] = isset ($_REQUEST['homepage']) ? smart_stripslashes($_REQUEST['homepage']) : '';
$company['bank_no'] = isset ($_REQUEST['bank_no']) ? smart_stripslashes($_REQUEST['bank_no']) : '';
$company['vat_no'] = isset ($_REQUEST['vat_no']) ? smart_stripslashes($_REQUEST['vat_no']) : '';

if(!isset($company['company_id']))
{
	$company['company_id'] = isset ($_REQUEST['company_id']) ? $_REQUEST['company_id'] : 0;
}



require_once ($GO_THEME->theme_path."header.inc");

$form = new form('edit_companies_form');
$form->add_html_element(new input('hidden','task','',false));
$form->add_html_element(new input('hidden','return_to',$return_to));
$form->add_html_element(new input('hidden','link_back',$link_back));

$tabstrip = new tabstrip('edit_companies_tabstrip', $ab_edit_selected);
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to($return_to);


if (isset($feedback))
{
	$p = new html_element('p', $feedback);
	$p->set_attribute('class','Error');
	$tabstrip->add_html_element($p);
}

$form->add_html_element(new input('hidden','id_array', 'companies'));

$edit_count = count($_POST[$_REQUEST['id_array']]['selected']);
foreach($_POST[$_REQUEST['id_array']]['selected'] as $company_id)
{
	$form->add_html_element(new input('hidden','companies[selected][]', $company_id));
}


$p = new html_element('p',sprintf($ab_edit_selected_warning, $edit_count, $ab_companies));
$tabstrip->add_html_element($p);

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



if(isset($GO_MODULES->modules['custom_fields']))
{
	require($GO_LANGUAGE->get_language_file('custom_fields'));
	require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
	$cf = new custom_fields();
	$cf2=new custom_fields();

	$cf2->get_authorized_categories(3,$GO_SECURITY->user_id);

	while($cf2->next_record())
	{

		$row =new table_row();
		$cell = new table_cell('<br /><b>'.$cf2->f('name').'</b>');
		$cell->set_attribute('colspan','2');
		$row->add_cell($cell);
		$table->add_row($row);

		if($cf_table = $cf->get_fields_table($cf2->f('id')))
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
	document.company_form.goto_url.value=goto_url;
	document.company_form.task.value='activate_linking';
	document.company_form.submit();
}
function select_addressbook(addressbook_id)
{
	document.forms[0].addressbook_id.value=addressbook_id;
	document.forms[0].submit();
}
</script>
<?php
require_once ($GO_THEME->theme_path."footer.inc");
