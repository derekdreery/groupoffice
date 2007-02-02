<?php
/**
 * @copyright Copyright Intermesh 2006
 * @version $Revision: 1.75 $ $Date: 2006/11/21 16:25:36 $
 * 
 * @author Merijn Schering <mschering@intermesh.nl>

   This file is part of Group-Office.

   Group-Office is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Group-Office is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Group-Office; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
      
 * @package Addressbook
 * @category Addressbook
 */

require_once ("../../Group-Office.php");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('addressbook');
require_once ($GO_LANGUAGE->get_language_file('addressbook'));

load_basic_controls();
load_control('tooltip');
load_control('date_picker');

require_once ($GO_MODULES->class_path."addressbook.class.inc");
$ab = new addressbook();


$templates_plugin = $GO_MODULES->get_plugin('templates');
if ($templates_plugin) {
	require_once ($templates_plugin['class_path'].'templates.class.inc');
	$tp = new templates();
}

$task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';
$return_to = (isset ($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$link_back = (isset ($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];

$company_id = isset ($_REQUEST['company_id']) ? smart_addslashes($_REQUEST['company_id']) : '0';
$vcf_file = isset ($_REQUEST['vcf_file']) ? $_REQUEST['vcf_file'] : '';

if (isset ($_REQUEST['popup'])) {
	$popup = true;
	$return_to = 'javascript:window.close();';
} else {
	$popup = false;
}

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


//save
switch ($task) {


	case 'save_company' :
		$company['name'] = isset ($_REQUEST['name']) ? smart_addslashes($_REQUEST['name']) : '';
		$company['address'] = isset ($_REQUEST['address']) ? smart_addslashes($_REQUEST['address']) : '';
		$company['address_no'] = isset ($_REQUEST['address_no']) ? smart_addslashes($_REQUEST['address_no']) : '';
		$company['zip'] = isset ($_REQUEST['zip']) ? smart_addslashes($_REQUEST['zip']) : '';
		$company['city'] = isset ($_REQUEST['city']) ? smart_addslashes($_REQUEST['city']) : '';

		if ($company['zip'] != '' && $company['address'] == '' && $company['city'] == '' && $addressinfo = $ab->get_addressinfo($company['zip'])) {
			$company['address'] = $addressinfo['street'];
			$company['city'] = $addressinfo['city'];
		}

		$company['state'] = isset ($_REQUEST['state']) ? smart_addslashes($_REQUEST['state']) : '';
		$company['country'] = isset ($_REQUEST['country']) ? smart_addslashes($_REQUEST['country']) : '';

		$company['post_address'] =
		(isset ($_REQUEST['post_address'])  && !empty($_REQUEST['post_address'])) ?
		smart_addslashes($_REQUEST['post_address']) : $company['address'];

		$company['post_address_no'] =
		(isset($_REQUEST['post_address_no']) && !empty($_REQUEST['post_address_no'])) ?
		smart_addslashes($_REQUEST['post_address_no']) :  $company['address_no'];

		$company['post_zip'] =
		(isset ($_REQUEST['post_zip']) && !empty($_REQUEST['post_zip']))
		? smart_addslashes($_REQUEST['post_zip']) : $company['zip'];

		$company['post_city'] =
		(isset ($_REQUEST['post_city']) && !empty($_REQUEST['post_city']))
		? smart_addslashes($_REQUEST['post_city']) : $company['city'];

		if ($company['post_zip'] != '' && $company['post_address'] == '' && $company['post_city'] == '' && $addressinfo = $ab->get_addressinfo($company['post_zip'])) {
			$company['post_address'] = $addressinfo['street'];
			$company['post_city'] = $addressinfo['city'];
		}

		$company['post_state'] =
		( isset ($_REQUEST['post_state']) && !empty($_REQUEST['post_state']))
		? smart_addslashes($_REQUEST['post_state']) : $company['state'];

		$company['post_country'] =
		(isset($_REQUEST['post_country']) && !empty($_REQUEST['post_country']))
		? smart_addslashes($_REQUEST['post_country']) : $company['country'];

		$company['email'] = isset ($_REQUEST['email']) ? smart_addslashes($_REQUEST['email']) : '';
		$company['phone'] = isset ($_REQUEST['phone']) ? smart_addslashes($_REQUEST['phone']) : '';
		$company['fax'] = isset ($_REQUEST['fax']) ? smart_addslashes($_REQUEST['fax']) : '';
		$company['homepage'] = isset ($_REQUEST['homepage']) ? smart_addslashes($_REQUEST['homepage']) : '';
		$company['bank_no'] = isset ($_REQUEST['bank_no']) ? smart_addslashes($_REQUEST['bank_no']) : '';
		$company['vat_no'] = isset ($_REQUEST['vat_no']) ? smart_addslashes($_REQUEST['vat_no']) : '';

		if ($_REQUEST['homepage']  != '' && !eregi('http', $_REQUEST['homepage'])) {
			$company['homepage'] = 'http://'.smart_addslashes($_REQUEST['homepage']);
		}
		$company['addressbook_id']  = $addressbook_id;

		if ($company['name'] == '') {
			$feedback = $error_missing_field;
		} else {
			if ($company_id > 0) {
				$company['id'] = $company_id;
				if ($ab->update_company($company)) {
					if ($_POST['close'] == 'true') {
						if ($popup) {
							echo '<script type="text/javascript">window.close();</script>';
						} else {
							if (isset ($_REQUEST['return_company_id'])) {
								$return_to = add_params_to_url($return_to, 'company_id='.$_REQUEST['company_id']);
							}
							header('Location: '.$return_to);
						}
						exit ();
					}
				} else {
					$feedback = $strSaveError;
				}
			}elseif (!isset($_POST['ignore']) && $existing_company_id = $ab->check_company($GO_SECURITY->user_id, $company)) {
				$link = '<a href="company.php?return_to='.urlencode($return_to).'&company_id='.$existing_company_id.'">'.$ab_here.'</a>';
				$feedback = sprintf($ab_contact_exists, $link);

				$ignore = true;

			} else {

				$company['link_id'] = $GO_LINKS->get_link_id();


				if ($company_id = $ab->add_company($company)) {



					if ($_POST['close'] == 'true') {
						if ($popup) {
							echo '<script type="text/javascript">'.
							'window.close();</script>';
						} else {
							if (isset ($_REQUEST['return_company_id'])) {
								$return_to = add_params_to_url($return_to, 'company_id='.$_REQUEST['company_id']);
							}

							header('Location: '.$return_to);
						}
						exit ();
					}
				} else {
					$feedback = $strSaveError;
				}
			}
		}
		break;

	case 'save_custom_fields':
		require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
		$cf = new custom_fields();

		$cf->save_fields($_POST['company_tabstrip_'.$company_id], $_POST['link_id']);

		if ($_POST['close'] == 'true') {
			if ($popup) {
				echo '<script type="text/javascript">window.close()</script>';
			} else {
				if (isset ($_REQUEST['return_company_id'])) {
					$return_to = add_params_to_url($return_to, 'company_id='.$_REQUEST['company_id']);
				}
				if($return_to != 'javascript:window.close();')
				{
					header('Location: '.$return_to);
				}else
				{
					echo '<script type="text/javascript">'.
					'window.close();</script>';
				}
			}
			exit();
		}
		break;


	case 'save_mailing_groups' :
		$tp->remove_company_from_mailing_groups($company_id);
		if (isset ($_POST['mailing_groups'])) {
			while ($mailing_group_id = array_shift($_POST['mailing_groups'])) {
				$tp->add_company_to_mailing_group($company_id, $mailing_group_id);
			}
		}
		break;
}

$vcard_count = 0;
if (!empty ($vcf_file)) {
	require_once ($GO_MODULES->class_path."vcard.class.inc");
	$vcard = new vcard();
	$vcard_count = $vcard->get_count($vcf_file);
}

//check permissions
if ($company_id > 0 && $company = $ab->get_company($company_id)) {
	$addressbook_id = isset($_REQUEST['addressbook_id']) ? $_REQUEST['addressbook_id'] : $company['addressbook_id'];
	if (!$write_permission = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $company['acl_write'])) {
		$read_permission = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $company['acl_read']);
	}
	$tabstrip = new tabstrip('company_tabstrip_'.$company_id, $strCompany.' '.$ab_id.': '.$company_id, '120', 'company_form', 'vertical');

	$tabstrip->add_tab('profile', $ab_company_properties);
	if ($templates_plugin) {

		if ($company['email'] != '' && $tp->get_mailing_groups($GO_SECURITY->user_id) && $write_permission) {
			$tabstrip->add_tab('mailings', $ab_mailings);
		}
	}

	$tabstrip->add_tab('contacts', $ab_employees);
	$tabstrip->add_tab('links', $strLinks);

	if(isset($GO_MODULES->modules['custom_fields']))
	{
		require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
		$cf = new custom_fields();

		if($cf->get_authorized_categories(3, $GO_SECURITY->user_id))
		{
			while($cf->next_record())
			{
				$tabstrip->add_tab($cf->f('id'), $cf->f('name'));
			}
		}
	}
} else {
	$tabstrip = new tabstrip('company_tabstrip_'.$company_id, $ab_new_company, '120', 'company_form', 'vertical');


	$write_permission = true;
	$read_permission = true;
}

$tabstrip->set_attribute('style', 'width:100%');
$tabstrip->set_return_to($return_to);

if (!$write_permission && !$read_permission) {
	header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
	exit ();
}


$active_tab = isset ($_REQUEST['active_tab']) ? $_REQUEST['active_tab'] : null;
if (isset ($active_tab)) {
	$tabstrip->set_active_tab($active_tab);
} else {
	$link_back .= '&active_tab='.$tabstrip->value;
}


$form = new form('company_form');

if(isset($ignore))
{
	$form->add_html_element(new input('hidden','ignore','true'));
}
$form->add_html_element(new input('hidden','task','',false));
$form->add_html_element(new input('hidden','close','false'));
$form->add_html_element(new input('hidden','return_to',$return_to));
$form->add_html_element(new input('hidden','link_back',$link_back));
$form->add_html_element(new input('hidden','company_id',$company_id, false));
$form->add_html_element(new input('hidden','goto_url','', false));
if ($popup) {
	$form->add_html_element(new input('hidden','popup','true'));
}

if ($company_id == 0 || $task == 'save_company') {
	$company['name'] = isset ($_REQUEST['name']) ? smart_stripslashes($_REQUEST['name']) : '';
	$company['address'] = isset ($_REQUEST['address']) ? smart_stripslashes($_REQUEST['address']) : '';
	$company['address_no'] = isset ($_REQUEST['address_no']) ? smart_stripslashes($_REQUEST['address_no']) : '';
	$company['zip'] = isset ($_REQUEST['zip']) ? smart_stripslashes($_REQUEST['zip']) : '';
	$company['city'] = isset ($_REQUEST['city']) ? smart_stripslashes($_REQUEST['city']) : '';

	if ($company['zip'] != '' && $company['address'] == '' && $company['city'] == '' && $addressinfo = $ab->get_addressinfo($company['zip'])) {
		$company['address'] = $addressinfo['street'];
		$company['city'] = $addressinfo['city'];
	}

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

	if ($company['post_zip'] != '' && $company['post_address'] == '' && $company['post_city'] == '' && $addressinfo = $ab->get_addressinfo($company['post_zip'])) {
		$company['post_address'] = $addressinfo['street'];
		$company['post_city'] = $addressinfo['city'];
	}

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
}
$GO_HEADER['head'] = '';
if ($company_id > 0) {


	$menu = new button_menu();




	if ($company['email'] != '')
	{
		$full_email = '"'.$company['name'].'" <'.$company['email'].'>';

		$menu->add_button('ab_email', $ab_send_message, get_mail_to_href(addslashes($full_email), 0, $company_id));

		if (isset ($GO_MODULES->modules['email']) && $GO_MODULES->modules['email']['read_permission']) {
			$menu->add_button('ml_search', $ab_search_sender,
			$GO_MODULES->modules['email']['url'].'index.php?task=set_search_query&from='.$company['email'].'&return_to='.urlencode($link_back));
		}
	}

	if ($templates_plugin)
	{
		if ($tp->has_oo_templates($GO_SECURITY->user_id))
		{
			$menu->add_button('new_letter', $ab_oo_doc,
			"javascript:popup('".$GO_MODULES->url.'templates/download_oo_template.php?company_id='.$company_id."','','');");
		}

		if ($company['email'] != '' && $tp->get_mailing_groups($GO_SECURITY->user_id) && $write_permission)
		{
			$tabstrip->add_tab('mailings', $ab_mailings);
		}
	}

	if ($write_permission)
	{
		$tabstrip->add_tab('links', $strLinks);


		$ll_link_back =$link_back;
		if(!strstr($ll_link_back, 'event_strip'))
		{
			$ll_link_back=add_params_to_url($link_back,'active_tab=links');
		}

		$menu->add_button('link', $strCreateLink, $GO_LINKS->search_link($company['link_id'], 3, 'opener.document.location=\''.$ll_link_back.'\';'));

		if($tabstrip->get_active_tab_id() == 'links')
		{
			load_control('links_list');
			$links_list = new links_list($company['link_id'], 'company_form', $ll_link_back);
			$GO_HEADER['head'] .= $links_list->get_header();

			$menu->add_button(
			'unlink',
			$cmdUnlink,
			$links_list->get_unlink_handler());

			$menu->add_button(
			'delete_big',
			$cmdDelete,
			$links_list->get_delete_handler());
		}


	}

	$menu->add_button(
	'upload',
	$cmdAttachFile,
	$GO_MODULES->modules['filesystem']['url'].'link_upload.php?path=companies/'.$company_id.'&link_id='.$company['link_id'].'&link_type=3&return_to='.urlencode($ll_link_back));



	//create contact directory with same permissions as project
	if(!file_exists($GO_CONFIG->file_storage_path.'companies/'.$company_id))
	{
		mkdir_recursive($GO_CONFIG->file_storage_path.'companies/'.$company_id);
	}
	require_once($GO_CONFIG->class_path.'filesystem.class.inc');
	$fs = new filesystem();
	if(!$fs->find_share($GO_CONFIG->file_storage_path.'companies/'.$company_id))
	{
		$fs->add_share($addressbook['user_id'], $GO_CONFIG->file_storage_path.'companies/'.$contact_id,'company',$addressbook['acl_read'], $addressbook['acl_write']);
	}


	if (isset($GO_MODULES->modules['notes']) && $GO_MODULES->modules['notes']['read_permission'])
	{
		$menu->add_button('ab_notes', $strNewNote, $GO_MODULES->modules['notes']['url'].'note.php?link_id='.$company['link_id'].'&link_type=3&link_text='.urlencode('('.$ab_company.') '.$company['name']).'&return_to='.urlencode($link_back));
	}
	if (isset($GO_MODULES->modules['calendar']) && $GO_MODULES->modules['calendar']['read_permission'])
	{
		$menu->add_button('cal_compose', $strNewEvent, $GO_MODULES->modules['calendar']['url'].'event.php?link_id='.$company['link_id'].'&link_type=3&link_text='.urlencode('('.$ab_company.') '.$company['name']).'&return_to='.urlencode($link_back));
	}
	if (isset($GO_MODULES->modules['todos']) && $GO_MODULES->modules['todos']['read_permission'])
	{
		$menu->add_button('todos_new', $strNewTodo, $GO_MODULES->modules['calendar']['url'].'event.php?todo=1&link_id='.$company['link_id'].'&link_type=3&link_text='.urlencode('('.$ab_company.') '.$company['name']).'&return_to='.urlencode($link_back));
	}

	if($tabstrip->get_active_tab_id() == 'contacts')
	{
		load_control('datatable');
		$datatable = new datatable('contacts_table', false, 'company_form');
		$datatable->set_attribute('style','width:100%');

		$GO_HEADER['head'] = $datatable->get_header();

		if($datatable->task == 'delete')
		{
			foreach($datatable->selected as $contact_id)
			{
				$contact['id'] = $contact_id;
				$contact['company_id'] = 0;
				$ab->update_contact($contact);
			}
		}

		$empl_menu = new button_menu();

		if ($write_permission)
		{
			$empl_menu->add_button('add_contact', 	$ab_add_new,
			$GO_MODULES->url.'contact.php?company_id='.$company_id.
			'&addressbook_id='.$addressbook_id.'&return_to='.
			rawurlencode($link_back));

			$empl_menu->add_button('addressbook', 	$ab_add_existing,
			$ab->select_contacts('', $GO_MODULES->url.
			'add_employees.php?company_id='.$company_id, 'true', 'false', 'false', 'true', 'false','false', 'id'));

			$empl_menu->add_button('delete_big', 	$cmdDelete,
			$datatable->get_delete_handler());

		}

		$form->add_html_element($empl_menu);
	}else
	{
		$form->add_html_element($menu);
	}
}

if($tabstrip->get_active_tab_id() == 'profile' || $tabstrip->get_active_tab_id() == '')
{
	$GO_HEADER['body_arguments'] = 'onload="document.company_form.name.focus();"';
}
$GO_HEADER['head'] .= tooltip::get_header();
$GO_HEADER['head'] .= date_picker::get_header();
require_once ($GO_THEME->theme_path."header.inc");




if ($tabstrip->get_active_tab_id() > 0) {
	$catagory_id = $tabstrip->get_active_tab_id();
	$active_tab_id = 'custom_fields';
} else {
	$active_tab_id = $tabstrip->get_active_tab_id();
}

switch ($active_tab_id) {
	case 'mailings' :
		require_once ($templates_plugin['path'].'mailing_groups.inc');
		break;

	case 'custom_fields' :


		$form->add_html_element(new input('hidden', 'link_id', $company['link_id']));
		if($cf_table = $cf->get_fields_table($tabstrip->get_active_tab_id(), $company['link_id']))
		{
			$tabstrip->add_html_element($cf_table);

			if ($write_permission) {
				$tabstrip->add_html_element(new button($cmdOk, "javascript:_save('save_custom_fields', 'true');"));
				$tabstrip->add_html_element(new button($cmdApply, "javascript:_save('save_custom_fields', 'false')"));
			}
		}
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."';"));
		break;

	case 'contacts' :
		echo $ab->enable_contact_selector();
		require_once ('company_contacts.inc');
		break;

	case 'links' :
		$tabstrip->add_html_element($links_list);
		break;

	default :
		if ($vcard_count > 0 && $company_id == 0) {
			if ($record = $vcard->get_vcard_contact(0)) {
				$company = $record['company'];
				$company['addressbook_id'] = $addressbook_id;
			}
		}
		if ($write_permission) {
			require_once ('edit_company.inc');
		} else {
			require_once ('show_company.inc');
		}
		break;
}

$form->add_html_element($tabstrip);
echo $form->get_html();
?>
<script type="text/javascript">
function _save(task, close)
{
	document.company_form.task.value = task;
	document.company_form.close.value = close;
	document.company_form.submit();
}

</script>
<?php
require_once ($GO_THEME->theme_path."footer.inc");
