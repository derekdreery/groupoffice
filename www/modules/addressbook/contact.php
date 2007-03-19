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
$return_to = (isset ($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : '';
$link_back = (isset ($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];
$contact_id = isset ($_REQUEST['contact_id']) ? smart_addslashes($_REQUEST['contact_id']) : '0';
$vcf_file = isset ($_REQUEST['vcf_file']) ? smart_addslashes($_REQUEST['vcf_file']) : '';

if (isset ($_REQUEST['popup'])) {
	$popup = true;
	$return_to = 'javascript:window.close();';
} else {
	$popup = false;
}
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

$templates_plugin = $GO_MODULES->get_plugin('templates');
if ($templates_plugin) {
	require_once ($templates_plugin['class_path'].'templates.class.inc');
	$tp = new templates();
}

$vcard_count = 0;
if (!empty ($vcf_file)) {
	require_once ($GO_MODULES->class_path."vcard.class.inc");
	$vcard = new vcard();
	$vcard_count = $vcard->get_count($vcf_file);
	if ($vcard_count > 0) {
		if ($record = $vcard->get_vcard_contact(0)) {
			$company = $record['company'];
		}
	}
}

if(isset($_REQUEST['feedback']))
{
	$feedback=smart_stripslashes($_REQUEST['feedback']);
}

$require = 'edit_contact.inc';

switch ($task) {

	
	case 'save' :
	
		$contact['first_name'] = isset ($_REQUEST['first_name']) ? smart_addslashes($_REQUEST['first_name']) : '';
		$contact['middle_name'] = isset ($_REQUEST['middle_name']) ? smart_addslashes($_REQUEST['middle_name']) : '';
		$contact['last_name'] = isset ($_REQUEST['last_name']) ? smart_addslashes($_REQUEST['last_name']) : '';
		$contact['initials'] = isset ($_REQUEST['initials']) ? smart_addslashes($_REQUEST['initials']) : '';
		$contact['title'] = isset ($_REQUEST['title']) ? smart_addslashes($_REQUEST['title']) : '';
		$contact['sex'] = isset ($_REQUEST['sex']) ? smart_addslashes($_REQUEST['sex']) : 'M';
		$contact['birthday'] = isset ($_REQUEST['birthday']) ? date_to_db_date(smart_addslashes($_POST['birthday'])) : '';
		$contact['email'] = isset ($_REQUEST['email']) ? smart_addslashes($_REQUEST['email']) : '';
		$contact['email2'] = isset ($_REQUEST['email2']) ? smart_addslashes($_REQUEST['email2']) : '';
		$contact['email3'] = isset ($_REQUEST['email3']) ? smart_addslashes($_REQUEST['email3']) : '';
		$contact['work_phone'] = isset ($_REQUEST['work_phone']) ? smart_addslashes($_REQUEST['work_phone']) : '';
		$contact['home_phone'] = isset ($_REQUEST['home_phone']) ? smart_addslashes($_REQUEST['home_phone']) : '';
		$contact['fax'] = isset ($_REQUEST['fax']) ? smart_addslashes($_REQUEST['fax']) : '';
		$contact['work_fax'] = isset ($_REQUEST['work_fax']) ? smart_addslashes($_REQUEST['work_fax']) : '';
		$contact['cellular'] = isset ($_REQUEST['cellular']) ? smart_addslashes($_REQUEST['cellular']) : '';
		$contact['country'] = isset ($_REQUEST['country']) ? smart_addslashes($_REQUEST['country']) : '';
		$contact['state'] = isset ($_REQUEST['state']) ? smart_addslashes($_REQUEST['state']) : '';
		$contact['address'] = isset ($_REQUEST['address']) ? smart_addslashes($_REQUEST['address']) : '';
		$contact['address_no'] = isset ($_REQUEST['address_no']) ? smart_addslashes($_REQUEST['address_no']) : '';
		$contact['city'] = isset ($_REQUEST['city']) ? smart_addslashes($_REQUEST['city']) : '';
		$contact['zip'] = isset ($_REQUEST['zip']) ? smart_addslashes($_REQUEST['zip']) : '';

		$contact['company_id'] = isset ($_REQUEST['company_id']) ? $_REQUEST['company_id'] : 0;
		
		
		if($contact['company_id']==0 && !empty($_POST['company_name'])) {
			$company['name'] = smart_addslashes($_POST['company_name']);
			$company['addressbook_id']=$contact['addressbook_id'];
			$company['link_id']=$GO_LINKS->get_link_id();
			$contact['company_id'] = $_POST['company_id'] = $ab->add_company($company);
		}

		if ($contact['zip'] != '' && $contact['address'] == '' && $contact['city'] == '' && 
			$contact['state'] == '' && $contact['country'] == '' && 
			$addressinfo = $ab->get_addressinfo($contact['zip'])) {

			$contact['address'] = addslashes($addressinfo['street']);
			$contact['city'] = addslashes($addressinfo['city']);
			$contact['state'] = addslashes($addressinfo['state']);
			$contact['country'] = addslashes($addressinfo['country']);
		}
		
		$contact['department'] = isset ($_REQUEST['department']) ? smart_addslashes($_REQUEST['department']) : '';
		$contact['function'] = isset ($_REQUEST['function']) ? smart_addslashes($_REQUEST['function']) : '';
		$contact['comment'] = isset ($_REQUEST['comment']) ? smart_addslashes($_REQUEST['comment']) : '';
		$contact['color'] = isset ($_REQUEST['color']) ? smart_addslashes($_REQUEST['color']) : '000000';
		$contact['source_id'] = isset ($_REQUEST['source_id']) ? $_REQUEST['source_id'] : '0';
		


		if ($contact['first_name'] == '' && $contact['last_name'] == '') {
			$feedback = $error_missing_field;
		} else {
			//translate the given birthdayto gmt unix time		
			if ($contact_id > 0) {
				$contact['id'] = $contact_id;
				if ($ab->update_contact($contact)) {
					if ($_POST['close'] == 'true') {
						if ($popup) {
							echo '<script type="text/javascript">window.close()</script>';
						} else {
							if (isset ($_REQUEST['return_contact_id'])) {
								$return_to = add_params_to_url($return_to, 'contact_id='.$_REQUEST['contact_id']);
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
						exit ();
					}
				} else {
					$feedback = $strSaveError;
				}
			}
			elseif (!isset($_POST['ignore']) && 
				$existing_contact_id = $ab->check_contact($GO_SECURITY->user_id, 
				$contact)) {
				$link = '<a href="contact.php?return_to='.urlencode($return_to).'&contact_id='.$existing_contact_id.'">'.$ab_here.'</a>';
				$feedback = sprintf($ab_contact_exists, $link);
				
				$ignore = true;
			} else {
				
				$contact['user_id'] = $GO_SECURITY->user_id;				
				$contact['link_id'] = $GO_LINKS->get_link_id();				

				
				if ($contact_id = $ab->add_contact($contact)) {
					$link_back .= '&contact_id='.$contact_id;
					
					
					if(isset($GO_MODULES->modules['custom_fields']))
					{
						
						require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
						$cf = new custom_fields();			
						$cf2 = new custom_fields();
						
						$cf->insert_cf_row(2, $contact['link_id']);
				
						$cf2->get_authorized_categories(2, $GO_SECURITY->user_id);
						while($cf2->next_record())
						{
							$cf->save_fields($cf2->f('id'), $contact['link_id']);
						}
					}
					
		
					
					if ($_POST['close'] == 'true') {
						if ($popup) {
							echo '<script type="text/javascript">'.
								 'window.close();</script>';
							} else {
							if (isset ($_REQUEST['return_contact_id'])) {
								$return_to = add_params_to_url($return_to, 'contact_id='.$contact_id);
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
						exit ();
					}
				} else {
					$feedback = $strSaveError;
				}
			}
		}

		break;

	/*case 'save_custom_fields' :
		if (isset ($_POST['fields'])) {
			require_once ($custom_fields_plugin['class_path'].'custom_fields.class.inc');
			$cf = new custom_fields('ab_custom_contact_fields');

			$cf->update_record($contact_id, $_POST['fields'], $_POST['values']);

			if ($_POST['close'] == 'true') {
				if ($popup) {
					echo '<script type="text/javascript">window.close()</script>';
				} else {
					if (isset ($_REQUEST['return_contact_id'])) {
						$return_to = add_params_to_url($return_to, 'contact_id='.$_REQUEST['contact_id']);
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
				exit ();
			}
		}
		break;*/
		
		case 'save_custom_fields':
			require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
			$cf = new custom_fields();			
			
			$cf->save_fields($_POST['contact_tabstrip_'.$contact_id], $_POST['link_id']);
		
			if ($_POST['close'] == 'true') {
				if ($popup) {
					echo '<script type="text/javascript">window.close()</script>';
				} else {
					if (isset ($_REQUEST['return_contact_id'])) {
						$return_to = add_params_to_url($return_to, 'contact_id='.$_REQUEST['contact_id']);
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
		$tp->remove_contact_from_mailing_groups($contact_id);
		if (isset ($_POST['mailing_groups'])) {
			while ($mailing_group_id = array_shift($_POST['mailing_groups'])) {
				$tp->add_contact_to_mailing_group($contact_id, $mailing_group_id);
			}
		}

		if ($_POST['close'] == 'true') {
			if ($popup) {
				echo '<script type="text/javascript">window.close()</script>';
			} else {
				if (isset ($_REQUEST['return_contact_id'])) {
					$return_to = add_params_to_url($return_to, 'contact_id='.$_REQUEST['contact_id']);
				}
				header('Location: '.$return_to);
			}
			exit ();
		}
		break;

	case 'start_timer' :
		$active_tab = 'links';
		break;


}

if ($contact_id > 0) {

	$contact = $ab->get_contact($contact_id);
	
	if(empty($contact['link_id']))
	{
		$update_contact['id'] = $contact_id;
		$update_contact['link_id'] = $contact['link_id']= $GO_LINKS->get_link_id();
		$ab->update_contact($update_contact);
	}
		
	$write_permission = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $contact["acl_write"]);
	if (!$write_permission && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $contact["acl_read"])) {
		Header("Location: ".$GO_CONFIG->host."error_docs/403.php");
		exit ();
	}
	if (!$write_permission) {
		$require = 'show_contact.inc';
	}
	$birthday = $contact['birthday'] > 0 ? db_date_to_date($contact['birthday']) : '';
	$addressbook_id = isset ($_REQUEST['addressbook_id']) ? $_REQUEST['addressbook_id'] : $contact['addressbook_id'];
	$addressbook=$ab->get_addressbook($addressbook_id);
}

if (isset ($_REQUEST['user_id']) && $_REQUEST['user_id'] > 0) {
	$contact = $GO_USERS->get_user($_REQUEST['user_id']);

	if ($ab->user_is_contact($GO_SECURITY->user_id, $_REQUEST['user_id'])) {
		$feedback = $contact_exist_warning;
		$contact['source_id'] = "";
	} else {
		$contact['source_id'] = $_REQUEST['user_id'];
	}
	$contact['email2'] = isset ($_REQUEST['email2']) ? smart_stripslashes($_REQUEST['email2']) : '';
	$contact['email3'] = isset ($_REQUEST['email3']) ? smart_stripslashes($_REQUEST['email3']) : '';
	$contact['address_no'] = isset ($_REQUEST['address_no']) ? $_REQUEST['address_no'] : '';
	$contact['comment'] = isset ($_REQUEST['comment']) ? $_REQUEST['comment'] : '';
	$contact['company_name'] = $contact['company'];
	$contact['company_id'] = $ab->get_company_id_by_name($contact['company'], $addressbook_id );
	$birthday = $contact['birthday'] > 0 ? db_date_to_date($contact['birthday']) : '';
	$require = 'edit_contact.inc';
	
	
	//}elseif (($contact_id == 0 || $task != '') && $task != 'save_custom_fields')
}
elseif ($contact_id == 0 || $task == 'save') {
	$require = 'edit_contact.inc';;
	$contact['first_name'] = isset ($_REQUEST['first_name']) ? smart_stripslashes($_REQUEST['first_name']) : '';
	$contact['middle_name'] = isset ($_REQUEST['middle_name']) ? smart_stripslashes($_REQUEST['middle_name']) : '';
	$contact['last_name'] = isset ($_REQUEST['last_name']) ? smart_stripslashes($_REQUEST['last_name']) : '';
	$contact['initials'] = isset ($_REQUEST['initials']) ? smart_stripslashes($_REQUEST['initials']) : '';
	$contact['title'] = isset ($_REQUEST['title']) ? smart_stripslashes($_REQUEST['title']) : '';
	$contact['sex'] = isset ($_REQUEST['sex']) ? smart_stripslashes($_REQUEST['sex']) : 'M';
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
}

if ($task == 'update') {
	$contact = $GO_USERS->get_user($contact['source_id']);
	$contact["source_id"] = $_REQUEST['source_id'];
	$contact['comment'] = $_REQUEST['comment'];
	$contact['addressbook_id'] = $_REQUEST['addressbook_id'];
	$contact['company_id'] = $_REQUEST['company_id'];
	$contact['address_no'] = $_REQUEST['address_no'];
	$birthday = $contact['birthday'] > 0 ? db_date_to_date($contact['birthday']) : '';
}

$birthday_picker = new date_picker('birthday', $_SESSION['GO_SESSION']['date_format'], $birthday);
$GO_HEADER['head'] = $birthday_picker->get_header();
$GO_HEADER['head'] .= tooltip::get_header();
$GO_HEADER['head'] .= color_selector::get_header();


$form = new form('contact_form');

//set this var if the user wants to ignore that a similar contact already exists.
if(isset($ignore))
{
	$form->add_html_element(new input('hidden', 'ignore', 'true'));
}

$form->add_html_element(new input('hidden', 'source_id', $contact["source_id"]));
$form->add_html_element(new input('hidden', 'task', '', false));
$form->add_html_element(new input('hidden', 'close', 'false'));
$form->add_html_element(new input('hidden', 'return_to', $return_to));
$form->add_html_element(new input('hidden', 'link_back', $link_back));
$form->add_html_element(new input('hidden', 'contact_id', $contact_id, false));
$form->add_html_element(new input('hidden', 'goto_url', '', false));

if (isset ($vcf_file)) {
	$form->add_html_element(new input('hidden', 'vcf_file', $vcf_file));
}
if ($popup) {
	$form->add_html_element(new input('hidden', 'popup', 'true'));
}
if (isset ($_REQUEST['return_contact_id'])) {
	$form->add_html_element(new input('hidden', 'return_contact_id', '1'));
}


$title = $contact_id > 0 ? $ab_contact.' '.$ab_id.': '.$contact_id : $contacts_add;

$menu = new button_menu();
$tabstrip = new tabstrip('contact_tabstrip_'.$contact_id, $title, '120', 'contact_form', 'vertical');
$tabstrip->set_attribute('style', 'width:100%');
$tabstrip->set_return_to($return_to);

$active_tab = isset ($_REQUEST['active_tab']) ? $_REQUEST['active_tab'] : null;
if (isset ($active_tab)) {
	$tabstrip->set_active_tab($active_tab);
}

if ($contact_id > 0) {
	
	
	$tabstrip->add_tab('profile', $contact_profile);

	if ($contact['email'] != '') {
		$middle_name = $contact['middle_name'] == '' ? '' : $contact['middle_name'].' ';
		$name = $contact['first_name'].' '.$middle_name.$contact['last_name'];
		$full_email = '"'.$name.'" <'.$contact['email'].'>';
		
		$menu->add_button('ab_email', $ab_send_message, get_mail_to_href(addslashes($full_email), $contact_id));
			

		if (isset ($GO_MODULES->modules['email']) && $GO_MODULES->modules['email']['read_permission']) {
			$menu->add_button('ml_search', $ab_search_sender, 
				$GO_MODULES->modules['email']['url'].'index.php?task=set_search_query&from='.$contact['email'].'&return_to='.urlencode($link_back));	
		}
	}

	if ($templates_plugin) {
		if ($tp->has_oo_templates($GO_SECURITY->user_id)) {
			$menu->add_button('new_letter', $ab_oo_doc, 
				"javascript:popup('".$GO_MODULES->url.'templates/download_oo_template.php?contact_id='.$contact_id."','','');");	
		}

		/*if ($contact['email'] != '' && $tp->get_mailing_groups($GO_SECURITY->user_id) && $write_permission) {
			$tabstrip->add_tab('mailings', $ab_mailings);
		}*/
	}


	$tabstrip->add_tab('links', $strLinks);
	if(isset($GO_MODULES->modules['custom_fields']))
	{
		require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
		$cf = new custom_fields();
		
		if($cf->get_authorized_categories(2, $GO_SECURITY->user_id))
		{
			while($cf->next_record())
			{
				$tabstrip->add_tab($cf->f('id'), $cf->f('name'));
			}
		}
	}
	
	$ll_link_back =$link_back;
	if(!strstr($ll_link_back, 'event_strip'))
	{
		$ll_link_back=add_params_to_url($link_back,'contact_tabstrip_'.$contact_id.'=links');		
	}
	
	$menu->add_button('link', $strCreateLink, $GO_LINKS->search_link($contact['link_id'], 2, 'opener.document.location=\''.$ll_link_back.'\';'));
	
	if($tabstrip->get_active_tab_id() == 'links')
	{
		load_control('links_list');
		
		$links_list = new links_list($contact['link_id'], 'contact_form', $link_back);	
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
	
	if(isset($GO_MODULES->modules['filesystem']) && $GO_MODULES->modules['filesystem']['read_permission'])
	{
		$menu->add_button(
			'upload',
			$cmdAttachFile,
			$GO_MODULES->modules['filesystem']['url'].'link_upload.php?path=contacts/'.$contact_id.'&link_id='.$contact['link_id'].'&link_type=2&return_to='.urlencode($ll_link_back));
	
		//create contact directory with same permissions as project
		if(!file_exists($GO_CONFIG->file_storage_path.'contacts/'.$contact_id))
		{
			mkdir_recursive($GO_CONFIG->file_storage_path.'contacts/'.$contact_id);
		}
		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem();
		if(!$fs->find_share($GO_CONFIG->file_storage_path.'contacts/'.$contact_id))
		{
			$fs->add_share($addressbook['user_id'], $GO_CONFIG->file_storage_path.'contacts/'.$contact_id,'contact',$addressbook['acl_read'], $addressbook['acl_write']);
		}
	}
	if (isset($GO_MODULES->modules['notes']) && $GO_MODULES->modules['notes']['read_permission'])
	{
		$menu->add_button('ab_notes', $strNewNote, $GO_MODULES->modules['notes']['url'].'note.php?link_id='.$contact['link_id'].'&link_type=2&link_text='.urlencode('('.$ab_contact.') '.format_name($contact['last_name'],$contact['first_name'], $contact['middle_name'])).'&return_to='.urlencode($link_back));
	}  
	if (isset($GO_MODULES->modules['calendar']) && $GO_MODULES->modules['calendar']['read_permission'])
	{
		$menu->add_button('cal_compose', $strNewEvent, $GO_MODULES->modules['calendar']['url'].'event.php?link_id='.$contact['link_id'].'&link_type=2&link_text='.urlencode('('.$ab_contact.') '.format_name($contact['last_name'],$contact['first_name'], $contact['middle_name'])).'&return_to='.urlencode($link_back));
	}  
	if (isset($GO_MODULES->modules['todos']) && $GO_MODULES->modules['todos']['read_permission'])
	{
		$menu->add_button('todos_new', $strNewTodo, $GO_MODULES->modules['calendar']['url'].'event.php?todo=1&link_id='.$contact['link_id'].'&link_type=2&link_text='.urlencode('('.$ab_contact.') '.format_name($contact['last_name'],$contact['first_name'], $contact['middle_name'])).'&return_to='.urlencode($link_back));
	}  
}


if($tabstrip->get_active_tab_id() == 'profile' || $tabstrip->get_active_tab_id() == '')
{
	$GO_HEADER['body_arguments'] = 'onload="document.contact_form.first_name.focus();"';	
}

require_once ($GO_THEME->theme_path."header.inc");

$form->add_html_element($menu);


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
			if(empty($contact['link_id']))
			{
			  $update_contact['id'] = $contact_id;
			  $update_contact['link_id'] = $contact['link_id'] = $GO_LINKS->get_link_id();
			  $ab->update_contact($update_contact);
			}

			$form->add_html_element(new input('hidden', 'link_id', $contact['link_id']));
			if($cf_table = $cf->get_fields_table($tabstrip->get_active_tab_id(), $contact['link_id']))
			{
				$tabstrip->add_html_element($cf_table);
				
				if ($write_permission) {
					$tabstrip->add_html_element(new button($cmdOk, "javascript:_save('save_custom_fields', 'true');"));
					$tabstrip->add_html_element(new button($cmdApply, "javascript:_save('save_custom_fields', 'false')"));				
				}			
			}
			$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."';"));
		break;

	case 'links' :
			$tabstrip->add_html_element($links_list);
		break;

	default :
		if ($vcard_count > 0 && $contact_id == 0) {
			if ($record = $vcard->get_vcard_contact(0)) {
				$contact = $record['contact'];
				$contact['addressbook_id'] = $addressbook_id;
				//$birthday = !empty ($contact['birthday']) ? date("d-m-Y", strtotime($contact['birthday'])) : "";
			}
		}
		require_once ($require);
		break;
}

$form->add_html_element($tabstrip);
echo $form->get_html();
?>
<script type="text/javascript" language="javascript">
function _save(task, close)
{
	document.forms[0].close.value=close;
	document.forms[0].task.value=task;
	document.forms[0].submit();
}
function activate_linking(goto_url)
{
	document.contact_form.goto_url.value=goto_url;
	document.contact_form.task.value='activate_linking';
	document.contact_form.submit();
}
</script>
<?php
require_once ($GO_THEME->theme_path."footer.inc");
