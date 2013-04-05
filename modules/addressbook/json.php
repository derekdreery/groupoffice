<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require_once("../../Group-Office.php");
$GLOBALS['GO_SECURITY']->json_authenticate('addressbook');

require_once($GLOBALS['GO_MODULES']->modules['addressbook']['class_path'].'addressbook.class.inc.php');
$ab = new addressbook;

$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'name';
$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : $_SESSION['GO_SESSION']['max_rows_list'];
$query = !empty($_REQUEST['query']) ? ($_REQUEST['query']) : null;
$field = isset($_REQUEST['field']) ? ($_REQUEST['field']) : '';

$clicked_letter = isset($_REQUEST['clicked_letter']) ? ($_REQUEST['clicked_letter']) : false;

$contact_id = isset($_REQUEST['contact_id']) ? ($_REQUEST['contact_id']) : null;
$company_id = isset($_REQUEST['company_id']) ? ($_REQUEST['company_id']) : null;
$addressbook_id = isset($_REQUEST['addressbook_id']) ? ($_REQUEST['addressbook_id']) : null;

$task = isset($_REQUEST['task']) ? ($_REQUEST['task']) : 'null';

$records = array();
try
{
	switch($task)
	{
		case 'fields':
			
			require($GLOBALS['GO_LANGUAGE']->get_language_file('addressbook'));
			
			if($_POST['type']=='contacts')
			{			
				$response['results']=array(
					array('field'=>'ab_contacts.name', 'label'=>$lang['common']['name'], 'type'=>'text'),
					array('field'=>'ab_contacts.title', 'label'=>$lang['common']['title'], 'type'=>'text'),
					array('field'=>'ab_contacts.first_name', 'label'=>$lang['common']['firstName'], 'type'=>'text'),
					array('field'=>'ab_contacts.middle_name', 'label'=>$lang['common']['middleName'], 'type'=>'text'),
					array('field'=>'ab_contacts.last_name', 'label'=>$lang['common']['lastName'], 'type'=>'text'),
					array('field'=>'ab_contacts.initials', 'label'=>$lang['common']['initials'], 'type'=>'text'),
					array('field'=>'ab_contacts.sex', 'label'=>$lang['common']['sex'], 'type'=>'text'),
					array('field'=>'ab_contacts.birthday', 'label'=>$lang['common']['birthday'], 'type'=>'date'),
					array('field'=>'ab_contacts.email', 'label'=>$lang['common']['email'], 'type'=>'text'),
					array('field'=>'ab_contacts.country', 'label'=>$lang['common']['country'], 'type'=>'country'),
					array('field'=>'ab_contacts.iso_address_format', 'label'=>$lang['common']['address_format'], 'type'=>'address_format'),
					array('field'=>'ab_contacts.state', 'label'=>$lang['common']['state'], 'type'=>'text'),
					array('field'=>'ab_contacts.city', 'label'=>$lang['common']['city'], 'type'=>'text'),
					array('field'=>'ab_contacts.zip', 'label'=>$lang['common']['zip'], 'type'=>'text'),
					array('field'=>'ab_contacts.address', 'label'=>$lang['common']['address'], 'type'=>'text'),
					array('field'=>'ab_contacts.address_no', 'label'=>$lang['common']['addressNo'], 'type'=>'text'),
					array('field'=>'ab_contacts.home_phone', 'label'=>$lang['common']['phone'], 'type'=>'text'),
					array('field'=>'ab_contacts.work_phone', 'label'=>$lang['common']['workphone'], 'type'=>'text'),
					array('field'=>'ab_contacts.fax', 'label'=>$lang['common']['name'], 'fax'=>'text'),
					array('field'=>'ab_contacts.work_fax', 'label'=>$lang['common']['workFax'], 'type'=>'text'),
					array('field'=>'ab_contacts.cellular', 'label'=>$lang['common']['cellular'], 'type'=>'text'),
					array('field'=>'ab_companies.name', 'label'=>$lang['common']['company'], 'type'=>'text'),
					array('field'=>'ab_contacts.department', 'label'=>$lang['common']['department'], 'type'=>'text'),
					array('field'=>'ab_contacts.function', 'label'=>$lang['common']['function'], 'type'=>'text'),
					array('field'=>'ab_contacts.comment', 'label'=>$lang['addressbook']['comment'], 'type'=>'textarea'),
					array('field'=>'ab_contacts.salutation', 'label'=>$lang['common']['salutation'], 'type'=>'text')			
				);
				
				$link_type=2;
			}else
			{
				$response['results']=array(
					array('field'=>'ab_companies.name', 'label'=>$lang['common']['name'], 'type'=>'text'),
					array('field'=>'ab_companies.name2', 'label'=>$lang['common']['name2'], 'type'=>'text'),
					array('field'=>'ab_companies.title', 'label'=>$lang['common']['title'], 'type'=>'text'),
					array('field'=>'ab_companies.email', 'label'=>$lang['common']['email'], 'type'=>'text'),
					array('field'=>'ab_companies.country', 'label'=>$lang['common']['country'], 'type'=>'country'),
					array('field'=>'ab_companies.iso_address_format', 'label'=>$lang['common']['address_format'], 'type'=>'address_format'),
					array('field'=>'ab_companies.state', 'label'=>$lang['common']['state'], 'type'=>'text'),
					array('field'=>'ab_companies.city', 'label'=>$lang['common']['city'], 'type'=>'text'),
					array('field'=>'ab_companies.zip', 'label'=>$lang['common']['zip'], 'type'=>'text'),
					array('field'=>'ab_companies.address', 'label'=>$lang['common']['address'], 'type'=>'text'),
					array('field'=>'ab_companies.address_no', 'label'=>$lang['common']['addressNo'], 'type'=>'text'),
					
						array('field'=>'ab_companies.post_country', 'label'=>$lang['common']['postCountry'], 'type'=>'country'),
					array('field'=>'ab_companies.post_state', 'label'=>$lang['common']['postState'], 'type'=>'text'),
					array('field'=>'ab_companies.post_city', 'label'=>$lang['common']['postCity'], 'type'=>'text'),
					array('field'=>'ab_companies.post_zip', 'label'=>$lang['common']['postZip'], 'type'=>'text'),
					array('field'=>'ab_companies.post_address', 'label'=>$lang['common']['postAddress'], 'type'=>'text'),
					array('field'=>'ab_companies.post_address_no', 'label'=>$lang['common']['postAddressNo'], 'type'=>'text'),
					
					array('field'=>'ab_companies.phone', 'label'=>$lang['common']['phone'], 'type'=>'text'),
					array('field'=>'ab_companies.fax', 'label'=>$lang['common']['name'], 'fax'=>'text'),
					
					array('field'=>'ab_companies.comment', 'label'=>$lang['addressbook']['comment'], 'type'=>'textarea')
								
				);
				$link_type=3;
			}
			
			if($GLOBALS['GO_MODULES']->has_module('customfields'))
			{
				require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
				
				$fields = $cf->get_authorized_fields($GLOBALS['GO_SECURITY']->user_id, $link_type);
				while($field = array_shift($fields))
				{
					if($field['datatype']!='heading' && $field['datatype']!='function')
					{
						$f = array('field'=>'cf_'.$link_type.'.'.$field['dataname'], 'label'=>$field['name'], 'type'=>$field['datatype']);
						
						if($f['type']=='select')
						{
							$f['type']=$field['name'];
							$f['options']=array();
							$cf->get_select_options($field['id']);
							while($cf->next_record())
							{
								$f['options'][]=array($cf->f('text'));
							}
						}
						
						$response['results'][]=$f;
					}
				}
			}
			//go_debug($response);
			
			echo json_encode($response);
			break;
		
		case 'search_sender':
			
			$response['results']=array();
			$response['total'] = $ab->get_contacts_by_email($_POST['email'], $GLOBALS['GO_SECURITY']->user_id);
			
			$ab2 = new addressbook();
			while($record=$ab->next_record())
			{
				$addressbook = $ab2->get_addressbook($record['addressbook_id']);
				$contact['id']=$record['id'];
				$contact['name']=String::format_name($record).' ('.$addressbook['name'].')';
				
				$response['results'][]=$contact;
			}
			echo json_encode($response);
			break;
		

		case 'contacts':

			require_once($GLOBALS['GO_LANGUAGE']->get_language_file('addressbook'));
		
			$response['data']['write_permission'] = false;

			if(!empty($_POST['no_addressbooks_filter'])){
				$books=array();
			}elseif(isset($_POST['books']))
			{
				$books = json_decode($_POST['books'], true);
				if(empty($_POST['disable_filter_save']))
					$GLOBALS['GO_CONFIG']->save_setting('addressbook_books_filter',implode(',', $books), $GLOBALS['GO_SECURITY']->user_id);
			}else
			{
				$books = $GLOBALS['GO_CONFIG']->get_setting('addressbook_books_filter', $GLOBALS['GO_SECURITY']->user_id);
				$books = ($books) ? explode(',',$books) : array();
			}
			
			if(!isset($_POST['enable_mailings_filter']))
			{
				$mailings_filter=array();
			}elseif(isset($_POST['mailings_filter']))
			{
				$mailings_filter = json_decode($_POST['mailings_filter'], true);				
				$GLOBALS['GO_CONFIG']->save_setting('mailings_filter', implode(',',$mailings_filter), $GLOBALS['GO_SECURITY']->user_id);
			}else
			{	
				$mailings_filter = $GLOBALS['GO_CONFIG']->get_setting('mailings_filter', $GLOBALS['GO_SECURITY']->user_id);
				$mailings_filter = empty($mailings_filter) ? array() : explode(',', $mailings_filter);
			}
			$readable_books = array();
			$writable_books = array();
			
			if(count($books))
			{				
				$response['data']['permission_level'] = $permission_level = 0;
				foreach($books as $book_id)
				{
					$book = $ab->get_addressbook($book_id);

					$permission_level = $GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $book['acl_id']);
					if($permission_level)
					{
						$readable_books[] = $book_id;
					}
					if($permission_level >= GO_SECURITY::DELETE_PERMISSION)
					{
						$writable_books[] = $book_id;
					}

					if($permission_level > $response['data']['permission_level'])
					{
						$response['data']['permission_level'] = $permission_level;
					}
				}

				$response['data']['write_permission']=$response['data']['permission_level']>1;
				if(!$response['data']['permission_level'])
				{
					throw new AccessDeniedException();
				}
			}

			if(isset($_POST['delete_keys']))
			{
				try{
					$delete_contacts = json_decode($_POST['delete_keys']);
					$contacts_deleted = array();
					foreach($delete_contacts as $contact_id)
					{
						$contact = $ab->get_contact($contact_id);
						if(in_array($contact['addressbook_id'], $writable_books))
						{
							$ab->delete_contact($contact_id, $contact);
							$GLOBALS['GO_EVENTS']->fire_event('contact_delete', array($contact));
							$contacts_deleted[] = $contact_id;
						}
					}
					if(!count($contacts_deleted))
					{
						throw new AccessDeniedException();
					}
					if(count($delete_contacts) != count($contacts_deleted))
					{
						$response['feedback'] = $lang['addressbook']['incomplete_delete_contacts'];
					}
					$response['deleteSuccess']=true;

				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}		

			$query_type = 'LIKE';
			if(!empty($clicked_letter))
			{
				$field = $_SESSION['GO_SESSION']['sort_name'];
				if($clicked_letter=='[0-9]')
				{
					$query = '^[0-9].*$';
					$query_type = 'REGEXP';
				}else
				{
					$query= $clicked_letter.'%';
				}
			} else {
				$field = '';
				$query = !empty($query) ? '%'.$query.'%' : '';
			}
			
			//temporarily disabled until replaced by a secure component.
			$advancedQuery = '';//empty($_POST['advancedQuery']) ? '' : $_POST['advancedQuery'];
							
			
			$response['total']=$ab->search_contacts(
			$GLOBALS['GO_SECURITY']->user_id,
			$query,
			$field,
			$readable_books,
			$start,
			$limit,
			!empty($_POST['require_email']),
			$sort,
			$dir,
			false,
			$query_type,
			$mailings_filter,
			$advancedQuery
			);

			if($GLOBALS['GO_MODULES']->has_module('customfields')) {
				require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
			}else
			{
				$cf=false;
			}

			$response['results']=array();

			while($record = $ab->next_record())
			{
				addressbook::format_contact_record($record, $cf);
				$record['cf'] = $record['id'].':'.trim($record['name']);//special field used by custom fields. They need an id an value in one.
				$response['results'][] = $record;
			}

			echo json_encode($response);
			break;

		case 'search_email_contacts':

			//this case is used when adding an unknown e-mail address to a contact
			//return writable only.

			$response['total']=$ab->search_contacts_email(
			$GLOBALS['GO_SECURITY']->user_id,
			$query,
			$start,
			$limit,
			$sort,
			$dir
			);

			$response['results']=array();

			$addressbooks=array();

			$ab2 = new addressbook();

			while($record = $ab->next_record())
			{
				if(!isset($addressbooks[$record['addressbook_id']]))
					$addressbooks[$record['addressbook_id']]=$ab2->get_addressbook($record['addressbook_id']);

				$record['ab_name']=$addressbooks[$record['addressbook_id']]['name'];
				$record['name']=String::format_name($record);
				$response['results'][] = $record;
			}
			echo json_encode($response);

			break;

		case 'companies':

			require_once($GLOBALS['GO_LANGUAGE']->get_language_file('addressbook'));

			$response['data']['write_permission'] = false;
			if(!empty($_POST['addressbook_id'])){
				$books[]=$_POST['addressbook_id'];
			}else	if(isset($_POST['books']))
			{
				$books = json_decode($_POST['books'], true);
				$GLOBALS['GO_CONFIG']->save_setting('addressbook_books_filter',implode(',', $books), $GLOBALS['GO_SECURITY']->user_id);
			} elseif (!empty($_POST['no_addressbooks_filter'])) {
			
				$books = array();
				
			} else {
				$books = $GLOBALS['GO_CONFIG']->get_setting('addressbook_books_filter', $GLOBALS['GO_SECURITY']->user_id);
				$books = ($books) ? explode(',',$books) : array();
			}
			
			if(!isset($_POST['enable_mailings_filter']))
			{
				$mailings_filter=array();
			}elseif(isset($_POST['mailings_filter']))
			{
				$mailings_filter = json_decode(($_POST['mailings_filter']), true);				
				$GLOBALS['GO_CONFIG']->save_setting('mailings_filter', implode(',',$mailings_filter), $GLOBALS['GO_SECURITY']->user_id);
			}else
			{	
				$mailings_filter = $GLOBALS['GO_CONFIG']->get_setting('mailings_filter', $GLOBALS['GO_SECURITY']->user_id);
				$mailings_filter = empty($mailings_filter) ? array() : explode(',', $mailings_filter);
			}

			if(count($books))
			{
				$readable_books = array();
				$writable_books = array();
				$response['data']['permission_level'] = $permission_level = 0;
				foreach($books as $book_id)
				{
					$book = $ab->get_addressbook($book_id);

					$permission_level = $GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $book['acl_id']);
					if($permission_level)
					{
						$readable_books[] = $book_id;
					}
					if($permission_level >= GO_SECURITY::DELETE_PERMISSION)
					{
						$writable_books[] = $book_id;
					}

					if($permission_level > $response['data']['permission_level'])
					{
						$response['data']['permission_level'] = $permission_level;
					}
				}

				$response['data']['write_permission']=$response['data']['permission_level']>1;
				if(!$response['data']['permission_level'])
				{
					throw new AccessDeniedException();
				}
			}

			if(isset($_POST['delete_keys']))
			{
				try{
					$delete_companies = json_decode($_POST['delete_keys']);
					$companies_deleted = array();
					foreach($delete_companies as $company_id)
					{
						$company = $ab->get_company($company_id);
						if(in_array($company['addressbook_id'], $writable_books))
						{
							$ab->delete_company($company_id);
							$companies_deleted[] = $company_id;
						}
					}
					if(!count($companies_deleted))
					{
						throw new AccessDeniedException();
					}
					if(count($delete_companies) != count($companies_deleted))
					{
						$response['feedback'] = $lang['addressbook']['incomplete_delete_companies'];
					}
					$response['deleteSuccess']=true;

				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}

			$query_type = 'LIKE';
			if(!empty($clicked_letter))
			{
				$field = 'name';
				if($clicked_letter=='[0-9]')
				{
					$query = '^[0-9].*$';
					$query_type = 'REGEXP';
				}else
				{
					$query= $clicked_letter.'%';
				}
			} else {
				$field = isset($_POST['field']) ? $_POST['field'] :'';
				//$field='';
				$query = !empty($query) ? '%'.$query.'%' : '';
			}
			
			//temporarily disabled until replaced with secure component
			$advancedQuery = '';//empty($_POST['advancedQuery']) ? '' : $_POST['advancedQuery'];			
			
			$response['total']=$ab->search_companies(
			$GLOBALS['GO_SECURITY']->user_id,
			$query,
			$field,
			$books,
			$start,
			$limit,
			!empty($_POST['require_email']),
			$sort,
			$dir,
			$query_type,
			$mailings_filter,
			$advancedQuery
			);

			$response['results'] = array();
			while($record = $ab->next_record())
			{
				addressbook::format_company_record($record);
				$response['results'][] = $record;
			}		

			echo json_encode($response);
			break;
				
			/* loadEmployees */
		case 'load_employees':
			$result['success'] = false;
				
			$company = $ab->get_company($company_id);

			if($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $company['acl_id'])<GO_SECURITY::WRITE_PERMISSION)
			{
				throw new AccessDeniedException();
			}
				
			if(isset($_POST['delete_keys']))
			{
				$response['deleteSuccess'] = true;
				try{
					$delete_contacts = json_decode(($_POST['delete_keys']));

					foreach($delete_contacts as $id)
					{
						$contact['id']=$id;
						$contact['company_id']=0;

						$ab->update_contact($contact);
					}
				}
				catch (Exception $e)
				{
					$response['deleteFeedback'] = $strDeleteError;
					$response['deleteSuccess'] = false;
				}
			}
				
			if(isset($_POST['add_contacts']))
			{
				$add_contacts = json_decode(($_POST['add_contacts']));

				foreach($add_contacts as $id)
				{
					$contact['id']=$id;
					$contact['company_id']=$company_id;

					$ab->update_contact($contact);
				}			
			}

			$field = isset($_REQUEST['field']) ? ($_REQUEST['field']) : 'name';
				
			$response['results'] = array();
			$response['total'] = $ab->get_company_contacts($company_id, $field, $dir, $start, $limit);

			while($ab->next_record())
			{
				$name = String::format_name($ab->f('last_name'), $ab->f('first_name'), $ab->f('middle_name'));
				$record = array(
					'id' => $ab->f('id'),
					'name' => $name,
					'function' => $ab->f('function'),
					'department' => $ab->f('department'),
					'phone' => $ab->f('work_phone'),
					'email' => $ab->f('email')
				);

				$response['results'][] = $record;
			}

			echo json_encode($response);
			break;

			/* loadContact */
		case 'load_contact_with_items':
		case 'load_contact':
			$response['success']=false;

			$response['data'] = $ab->get_contact($contact_id);

			$perm_lvl = $GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $response['data']['acl_id']);
			$response['data']['write_permission']=$perm_lvl>1;
			if(!$perm_lvl)
			{
				throw new AccessDeniedException();
			}
				
			if($response['data'])
			{
				
				$response['data']['full_name'] = String::format_name($response['data']['last_name'], $response['data']['first_name'], $response['data']['middle_name']);

				require($GLOBALS['GO_LANGUAGE']->get_base_language_file('countries'));
				if($task == 'load_contact_with_items')
				{
					$response['data']['comment']=String::text_to_html($response['data']['comment']);
					$response['data']['country']=isset($countries[$response['data']['country']]) ? $countries[$response['data']['country']] : $response['data']['country'];
				}

				if($response['data']['birthday'] == '0000-00-00')
				{
					$response['data']['birthday'] = '';
				} else {
					$response['data']['birthday'] = Date::format($response['data']['birthday'], false);
				}

				//if($response['data']['salutation'] == '')
					//$response['data']['salutation'] = $response['data']['default_salutation'];
				
				
				if($response['data']['company_id'] > 0 && $company = $ab->get_company($response['data']['company_id']))
				{					
					$response['data']['company_name'] = $company['name'];
					$response['data']['company_name2'] = $company['name2'];
				} else {
					$response['data']['company_name'] = '';
					$response['data']['company_name2'] = '';
				}

				$values = array('address_no', 'address', 'zip', 'city', 'state', 'country');

				$response['data']['formatted_address'] = $response['data']['address_format'];

				foreach($values as $val)
					$response['data']['formatted_address'] = str_replace('{'.$val.'}', $response['data'][$val], $response['data']['formatted_address']);

				$response['data']['formatted_address'] = preg_replace("/(\r\n)+|(\n|\r)+/", "<br />", $response['data']['formatted_address']);
				$response['data']['google_maps_link']='http://maps.google.com/maps?q=';

				$response['data']['google_maps_link']=google_maps_link($response['data']['address'], $response['data']['address_no'], $response['data']['city'], $response['data']['country']);

				if($GLOBALS['GO_MODULES']->has_module('customfields'))
				{
					$ab2 = new addressbook();
					$response['data'] = $ab2->cf_categories_to_record($response['data'],'addressbook_id');
				}

				$response['success']=true;	
			}

			if(file_exists($GLOBALS['GO_CONFIG']->file_storage_path.'contacts/contact_photos/'.$response['data']['id'].'.jpg'))
			{
				$response['data']['photo_src'] = $GLOBALS['GO_MODULES']->modules['addressbook']['url'].'photo.php?contact_id='.$response['data']['id'].'&mtime='.time();
			} else {
				$response['data']['photo_src'] = false;
			}
				
			if($task == 'load_contact')
			{
				if(isset($GLOBALS['GO_MODULES']->modules['customfields']) && $GLOBALS['GO_MODULES']->modules['customfields']['read_permission'])
				{
					require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();
					$values = $cf->get_values($GLOBALS['GO_SECURITY']->user_id, 2, $contact_id);
					$response['data']=array_merge($response['data'], $values);
				}

				if($GLOBALS['GO_MODULES']->has_module('mailings'))
				{
					require_once($GLOBALS['GO_MODULES']->modules['mailings']['class_path'].'mailings.class.inc.php');
					
					$ml = new mailings();
					$ml2 = new mailings();
						
					$count = $ml->get_authorized_mailing_groups('write', $GLOBALS['GO_SECURITY']->user_id, 0,0);

					
					while($ml->next_record())
					{						
						$response['data']['mailing_'.$ml->f('id')]=$ml2->contact_is_in_group($contact_id, $ml->f('id')) ? true : false;
					}
				}

				$GLOBALS['GO_EVENTS']->fire_event('load_contact', array(&$response, $task));

				echo json_encode($response);
				break;
			}

			load_standard_info_panel_items($response, 2);
			
			if(!isset($response['data']['iso_address_format']) || $response['data']['iso_address_format'] == '')
				$response['data']['iso_address_format'] = $response['data']['default_iso_address_format'];

			$GLOBALS['GO_EVENTS']->fire_event('load_contact', array(&$response, $task));

			echo json_encode($response);
			break;

			/*
			 case 'loadContactDetails':
			 echo json_encode($result);
			 break;
			 */
			/* loadCompany */
		case 'load_company_with_items':
		case 'load_company':
			$response['success']=false;

			$response['data'] = $ab->get_company($company_id);
			
			$perm_lvl = $GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $response['data']['acl_id']);
			$response['data']['write_permission']=$perm_lvl>1;
			if(!$perm_lvl)
			{
				throw new AccessDeniedException();
			}
				
			if($response['data'])
			{
				if($task == 'load_company_with_items')
				{
					$response['data']['comment']=String::text_to_html($response['data']['comment']);
					
					require($GLOBALS['GO_LANGUAGE']->get_base_language_file('countries'));
					$response['data']['country']=isset($countries[$response['data']['country']]) ? $countries[$response['data']['country']] : $response['data']['country'];
					$response['data']['post_country']=isset($countries[$response['data']['post_country']]) ? $countries[$response['data']['post_country']] : $response['data']['post_country'];				
				}

				$values = array('address_no', 'address', 'zip', 'city', 'state', 'country');

				$response['data']['formatted_address'] = $response['data']['address_format'];

				foreach($values as $val)
					$response['data']['formatted_address'] = str_replace('{'.$val.'}', $response['data'][$val], $response['data']['formatted_address']);

				$response['data']['formatted_address'] = preg_replace("/(\r\n)+|(\n|\r)+/", "<br />", $response['data']['formatted_address']);

				$response['data']['google_maps_link']=google_maps_link($response['data']['address'], $response['data']['address_no'], $response['data']['city'], $response['data']['country']);


				$values = array('post_address_no', 'post_address', 'post_zip', 'post_city', 'post_state', 'post_country');

				$response['data']['post_formatted_address'] = $response['data']['post_address_format'];

				foreach($values as $val)
					$response['data']['post_formatted_address'] = str_replace('{'.substr($val, 5).'}', $response['data'][$val], $response['data']['post_formatted_address']);

				$response['data']['post_formatted_address'] = preg_replace("/(\r\n)+|(\n|\r)+/", "<br />", $response['data']['post_formatted_address']);

				$response['data']['post_google_maps_link']=google_maps_link($response['data']['post_address'], $response['data']['post_address_no'], $response['data']['post_city'], $response['data']['post_country']);

				if($GLOBALS['GO_MODULES']->has_module('customfields'))
				{
					$ab2 = new addressbook();
					$response['data'] = $ab2->cf_categories_to_record($response['data'],'addressbook_id');
				}

				$response['success']=true;		
			}		
				
			if($task == 'load_company')
			{
				if($GLOBALS['GO_MODULES']->has_module('customfields'))
				{
					require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();
					$values = $cf->get_values($GLOBALS['GO_SECURITY']->user_id, 3, $company_id);
					$response['data']=array_merge($response['data'], $values);
				}
				
				if($GLOBALS['GO_MODULES']->has_module('mailings'))
				{
					require_once($GLOBALS['GO_MODULES']->modules['mailings']['class_path'].'mailings.class.inc.php');
					$ml = new mailings();
					$ml2 = new mailings();
						
					$ml->get_authorized_mailing_groups('write', $GLOBALS['GO_SECURITY']->user_id, 0,0);
					while($ml->next_record())
					{
						$response['data']['mailing_'.$ml->f('id')]=$ml2->company_is_in_group($company_id, $ml->f('id')) ? true : false;
					}
				}

				$GLOBALS['GO_EVENTS']->fire_event('load_company', array(&$response, $task));
				echo json_encode($response);
				
				break;
			}						

			$ab->get_company_contacts($response['data']['id']);
			$response['data']['employees']=array();
			while($ab->next_record())
			{
				$response['data']['employees'][]=array(
					'id'=>$ab->f('id'),
					'name'=>String::format_name($ab->record),
					'function'=>$ab->f('function'),
					'email'=>$ab->f('email')					
				);
			}				
				
			load_standard_info_panel_items($response, 3);

			$GLOBALS['GO_EVENTS']->fire_event('load_company', array(&$response, $task));
				
				
			echo json_encode($response);
			break;

		case 'init':
			
			$response['addressbooks']['total'] = $ab->get_user_addressbooks($GLOBALS['GO_SECURITY']->user_id, $start, $GLOBALS['GO_CONFIG']->nav_page_size, $sort, $dir);

			if($response['addressbooks']['total']==0)
			{
				$ab->get_addressbook();
				$response['addressbooks']['total'] = $ab->get_user_addressbooks($GLOBALS['GO_SECURITY']->user_id, $start, $GLOBALS['GO_CONFIG']->nav_page_size, $sort, $dir);
			}

			$books = $GLOBALS['GO_CONFIG']->get_setting('addressbook_books_filter', $GLOBALS['GO_SECURITY']->user_id);
			$books = ($books) ? explode(',',$books) : array();

			while($record = $ab->next_record())
			{
				$record['checked']=in_array($record['id'], $books);

				if($GLOBALS['GO_MODULES']->has_module('customfields'))
				{
					$ab2 = new addressbook();
					$record = $ab2->cf_categories_to_record($record);
				}

				$response['addressbooks']['results'][]=$record;
			}

			if($GLOBALS['GO_MODULES']->has_module('mailings')){

				$selected_mailings = $GLOBALS['GO_CONFIG']->get_setting('mailings_filter', $GLOBALS['GO_SECURITY']->user_id);
				$selected_mailings = empty($selected_mailings) ? array() : explode(',', $selected_mailings);

				require_once($GLOBALS['GO_MODULES']->modules['mailings']['class_path'].'mailings.class.inc.php');
				$ml = new mailings();

				$response['readable_addresslists']['total'] = $ml->get_authorized_mailing_groups('read', $GLOBALS['GO_SECURITY']->user_id);
				$response['readable_addresslists']['results'] = array();

				while($mailing = $ml->next_record()){
					$mailing['checked']=in_array($mailing['id'], $selected_mailings);
					$response['readable_addresslists']['results'][]=$mailing;
				}

				$response['writable_addresslists']['total'] = $ml->get_authorized_mailing_groups('write', $GLOBALS['GO_SECURITY']->user_id);
				$response['writable_addresslists']['results'] = array();

				while($mailing = $ml->next_record()){
					$response['writable_addresslists']['results'][]=$mailing;
				}
			}

			echo json_encode($response);

			break;

			/* get all readable addressbooks */
		case 'addressbooks':

			require($GLOBALS['GO_LANGUAGE']->get_language_file('addressbook'));

			$auth_type = isset($_POST['auth_type']) ?$_POST['auth_type'] : 'read';

			$query = !empty($_REQUEST['query']) ? '%'.$_REQUEST['query'].'%' : '';

			$response['results'] = array();
				
			if($auth_type=='read')
			{			
				$response['total'] = $ab->get_user_addressbooks($GLOBALS['GO_SECURITY']->user_id, $start, $limit, $sort, $dir, $query);
				
				if($response['total']==0)
				{
					$ab->get_addressbook();
					$response['total'] = $ab->get_user_addressbooks($GLOBALS['GO_SECURITY']->user_id, $start, $limit, $sort, $dir, $query);
				}
			}else
			{

				if(isset($_POST['delete_keys']))
				{
					try
					{
						$response['deleteSuccess']=true;
						$delete_addressbooks = json_decode(($_POST['delete_keys']));
						foreach($delete_addressbooks as $book_id)
						{
							$addressbook = $ab->get_addressbook($book_id);
							
							if(($GLOBALS['GO_MODULES']->modules['addressbook']['permission_level'] < GO_SECURITY::WRITE_PERMISSION) || ($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $addressbook['acl_id']) < GO_SECURITY::DELETE_PERMISSION))
							{
								throw new AccessDeniedException();
							}

							$ab->delete_addressbook($book_id);
						}
					}catch(Exception $e)
					{
						$response['deleteSuccess']=false;
						$response['deleteFeedback']=$e->getMessage();
					}
				}

				$response['total'] = $ab->get_writable_addressbooks($GLOBALS['GO_SECURITY']->user_id, $start, $limit, $sort, $dir, $query);
				if($response['total']==0 && empty($query))
				{
					$ab->get_addressbook(0);
					$response['total'] = $ab->get_writable_addressbooks($GLOBALS['GO_SECURITY']->user_id, $start, $limit, $sort, $dir);
				}
					
			}
				
			$books = $GLOBALS['GO_CONFIG']->get_setting('addressbook_books_filter', $GLOBALS['GO_SECURITY']->user_id);
			$books = ($books) ? explode(',',$books) : array();

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();
				
			$first_record = true;
			while($ab->next_record())
			{
				if($first_record)
				{
					if(!count($books))
					{
						$books[] = $ab->f('id');
						$GLOBALS['GO_CONFIG']->save_setting('addressbook_books_filter',$ab->f('id'), $GLOBALS['GO_SECURITY']->user_id);
					}

					$first_record = false;
				}				
				
				$record = array(
					'id' => $ab->f('id'),
					'user_id' => $ab->f('user_id'),
					'name' => $ab->f('name'),
					'owner' => $GO_USERS->get_user_realname($ab->f('user_id')),
					'acl_id' => $ab->f('acl_id'),
					'default_iso_address_format' => $ab->f('default_iso_address_format'),
					'default_salutation' => $ab->f('default_salutation'),
					'checked' => in_array($ab->f('id'), $books)
				);

				if($GLOBALS['GO_MODULES']->has_module('customfields'))
				{
					$ab2 = new addressbook();
					$record = $ab2->cf_categories_to_record($record);
				}

				$response['results'][] = $record;
			}

			echo json_encode($response);
			break;

		case 'addressbooks_string':

			require_once($GLOBALS['GO_CONFIG']->class_path.'mail/RFC822.class.inc');
			$RFC822 = new RFC822();
			$abs = explode(',', $_REQUEST['addressbooks']);

			$response = '';
			foreach($abs as $ab_id)
			{
				$ab->get_contacts($ab_id);
				while($contact = $ab->next_record())
				{
					if(!empty($contact['email'])) {
						$name = !empty($contact['middle_name']) ?
							$contact['first_name'].' '.$contact['middle_name'].' '.$contact['last_name'] :
							$contact['first_name'].' '.$contact['last_name'];
						$response .= $RFC822->write_address(String::format_name($name), $contact['email']).', ';
					}
				}
			}
			echo $response;
			break;

		case 'ab_fields':

			require($GLOBALS['GO_LANGUAGE']->get_language_file('addressbook'));

			$ab->query("SHOW COLUMNS FROM ab_contacts");
			$contact_types = array();
			while ($ab->next_record()) {
				$contact_types[$ab->record['Field']] = getSimpleType($ab->record['Type']);
			}

			$ab->query("SHOW COLUMNS FROM ab_companies");
			$company_types = array();
			while ($ab->next_record()) {
				$company_types[$ab->record['Field']] = getSimpleType($ab->record['Type']);
			}


			if($_POST['type']=='contacts')
			{

				$response['results']=array(
					//array('field'=>'ab_contacts.name', 'label'=>$lang['common']['name'], 'type'=>$contact_types['name']),
					array('field'=>'ab_contacts.title', 'label'=>$lang['common']['title'], 'type'=>$contact_types['title']),
					array('field'=>'ab_contacts.first_name', 'label'=>$lang['common']['firstName'], 'type'=>$contact_types['first_name']),
					array('field'=>'ab_contacts.middle_name', 'label'=>$lang['common']['middleName'], 'type'=>$contact_types['middle_name']),
					array('field'=>'ab_contacts.last_name', 'label'=>$lang['common']['lastName'], 'type'=>$contact_types['last_name']),
					array('field'=>'ab_contacts.initials', 'label'=>$lang['common']['initials'], 'type'=>$contact_types['initials']),
					array('field'=>'ab_contacts.sex', 'label'=>$lang['common']['sex'], 'type'=>$contact_types['sex']),
					array('field'=>'ab_contacts.birthday', 'label'=>$lang['common']['birthday'], 'type'=>$contact_types['birthday']),
					array('field'=>'ab_contacts.email', 'label'=>$lang['common']['email'], 'type'=>$contact_types['email']),
					array('field'=>'ab_contacts.country', 'label'=>$lang['common']['country'], 'type'=>$contact_types['country']),
//					array('field'=>'ab_contacts.iso_address_format', 'label'=>$lang['common']['address_format'], 'type'=>$contact_types['iso_address_format']),
					array('field'=>'ab_contacts.state', 'label'=>$lang['common']['state'], 'type'=>$contact_types['state']),
					array('field'=>'ab_contacts.city', 'label'=>$lang['common']['city'], 'type'=>$contact_types['city']),
					array('field'=>'ab_contacts.zip', 'label'=>$lang['common']['zip'], 'type'=>$contact_types['zip']),
					array('field'=>'ab_contacts.address', 'label'=>$lang['common']['address'], 'type'=>$contact_types['address']),
					array('field'=>'ab_contacts.address_no', 'label'=>$lang['common']['addressNo'], 'type'=>$contact_types['address_no']),
					array('field'=>'ab_contacts.home_phone', 'label'=>$lang['common']['phone'], 'type'=>$contact_types['home_phone']),
					array('field'=>'ab_contacts.work_phone', 'label'=>$lang['common']['workphone'], 'type'=>$contact_types['work_phone']),
					array('field'=>'ab_contacts.fax', 'label'=>$lang['common']['fax'], 'fax'=>$contact_types['fax']),
					array('field'=>'ab_contacts.work_fax', 'label'=>$lang['common']['workFax'], 'type'=>$contact_types['work_fax']),
					array('field'=>'ab_contacts.cellular', 'label'=>$lang['common']['cellular'], 'type'=>$contact_types['cellular']),
					array('field'=>'ab_companies.name', 'label'=>$lang['common']['company'], 'type'=>$company_types['name']),
					array('field'=>'ab_contacts.department', 'label'=>$lang['common']['department'], 'type'=>$contact_types['department']),
					array('field'=>'ab_contacts.function', 'label'=>$lang['common']['function'], 'type'=>$contact_types['function']),
					array('field'=>'ab_contacts.comment', 'label'=>$lang['addressbook']['comment'], 'type'=>$contact_types['comment']),
					array('field'=>'ab_contacts.salutation', 'label'=>$lang['common']['salutation'], 'type'=>$contact_types['salutation'])
				);

				$model="GO_Addressbook_Model_Contact";
			}else
			{
				$response['results']=array(
					array('field'=>'ab_companies.name', 'label'=>$lang['common']['name'], 'type'=>$company_types['name']),
					array('field'=>'ab_companies.name2', 'label'=>$lang['common']['name2'], 'type'=>$company_types['name2']),
					//array('field'=>'ab_companies.title', 'label'=>$lang['common']['title'], 'type'=>$company_types['title']),
					array('field'=>'ab_companies.email', 'label'=>$lang['common']['email'], 'type'=>$company_types['email']),
					array('field'=>'ab_companies.country', 'label'=>$lang['common']['country'], 'type'=>$company_types['country']),
//					array('field'=>'ab_companies.iso_address_format', 'label'=>$lang['common']['address_format'], 'type'=>$company_types['iso_address_format']),
					array('field'=>'ab_companies.state', 'label'=>$lang['common']['state'], 'type'=>$company_types['state']),
					array('field'=>'ab_companies.city', 'label'=>$lang['common']['city'], 'type'=>$company_types['city']),
					array('field'=>'ab_companies.zip', 'label'=>$lang['common']['zip'], 'type'=>$company_types['zip']),
					array('field'=>'ab_companies.address', 'label'=>$lang['common']['address'], 'type'=>$company_types['address']),
					array('field'=>'ab_companies.address_no', 'label'=>$lang['common']['addressNo'], 'type'=>$company_types['address_no']),

					array('field'=>'ab_companies.post_country', 'label'=>$lang['common']['postCountry'], 'type'=>$company_types['post_country']),
					array('field'=>'ab_companies.post_state', 'label'=>$lang['common']['postState'], 'type'=>$company_types['post_state']),
					array('field'=>'ab_companies.post_city', 'label'=>$lang['common']['postCity'], 'type'=>$company_types['post_city']),
					array('field'=>'ab_companies.post_zip', 'label'=>$lang['common']['postZip'], 'type'=>$company_types['post_zip']),
					array('field'=>'ab_companies.post_address', 'label'=>$lang['common']['postAddress'], 'type'=>$company_types['post_address']),
					array('field'=>'ab_companies.post_address_no', 'label'=>$lang['common']['postAddressNo'], 'type'=>$company_types['post_address_no']),

					array('field'=>'ab_companies.phone', 'label'=>$lang['common']['phone'], 'type'=>$company_types['phone']),
					array('field'=>'ab_companies.fax', 'label'=>$lang['common']['name'], 'type'=>$company_types['fax']),

					array('field'=>'ab_companies.comment', 'label'=>$lang['addressbook']['comment'], 'type'=>$company_types['comment'])

				);
				$model="GO_Addressbook_Model_Company";
			}

				if (isset($GLOBALS['GO_MODULES']->modules['customfields'])) {
				require_once($GO_CONFIG->root_path.'GO.php');

				$stmt = GO_Customfields_Model_Category::model()->findByModel($model);
				while($category = $stmt->fetch()){
					$fstmt = $category->fields();
					while($field = $fstmt->fetch()){
						$arr=$field->getAttributes();
						$arr['dataname']=$field->columnName();
						$fields[]=$arr;
						if(empty($field->exclude_from_grid))
								$response['results'][] = array('id'=>$arr['id'], 'field'=>$field->columnName() ,'custom'=>true,'name' => $arr['name'] . ':' . $arr['name'],'label' => $arr['name'] . ':' . $arr['name'], 'value' => '`cf:' . $category->name . ':' . $arr['name'] . '`', 'type' => $arr['datatype']);
					}
				}
			}

			echo json_encode($response);
			break;

		case 'sqls':

			if(isset($_POST['delete_keys']))
			{
				try{
					$delete_sqls = json_decode($_POST['delete_keys']);

					foreach($delete_sqls as $id)
					{
						$ab->delete_sql($id);
					}
					$response['deleteSuccess'] = true;
				}
				catch (Exception $e)
				{
					$response['deleteFeedback'] = $e->getMessage();
					$response['deleteSuccess'] = false;
				}
			}

			$response['total'] = $ab->get_sqls($GLOBALS['GO_SECURITY']->user_id, $_POST['companies']);
			$response['results'] = array();

			while($ab->next_record())
				$response['results'][] = $ab->record;
			
			$response['success'] = true;
			echo json_encode($response);
			break;

		case 'addressbook_cf_categories':

			$GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php';
			$cf = new customfields();

			if (empty($_REQUEST['addressbook_id'])) {
				require_once($GLOBALS['GO_LANGUAGE']->get_language_file('addressbook'));
				throw new Exception($lang['addressbook']['no_addressbook_id']);
			}
			$addressbook_id = $_REQUEST['addressbook_id'];

			$response = array('data'=>array());

			$authorized_contact_categories = array();
			$cf->get_authorized_categories(2, $GLOBALS['GO_SECURITY']->user_id);
			while ($record = $cf->next_record()) {
				$authorized_contact_categories[] = $record['id'];
			}
			$response['data']['limit_contacts'] = $ab->check_addressbook_category_limit($addressbook_id,2);
			$ab->get_allowed_categories($addressbook_id,2);
			while ($record = $ab->next_record()) {
				if (in_array($record['category_id'],$authorized_contact_categories)) {
					$response['data']['cat_2_'.$record['category_id']] = 'on';
				}
			}

			$authorized_company_categories = array();
			$cf->get_authorized_categories(3, $GLOBALS['GO_SECURITY']->user_id);
			while ($record = $cf->next_record()) {
				$authorized_company_categories[] = $record['id'];
			}
			$response['data']['limit_companies'] = $ab->check_addressbook_category_limit($addressbook_id,3);
			$ab->get_allowed_categories($addressbook_id,3);
			while ($record = $ab->next_record()) {
				if (in_array($record['category_id'],$authorized_company_categories)) {
					$response['data']['cat_3_'.$record['category_id']] = 'on';
				}
			}

			$response['data']['addressbook_id'] = $addressbook_id;
			$response['success'] = true;
			echo json_encode($response);
			break;
			
	}
}
catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
	echo json_encode($response);

}

function getSimpleType($type) {
	$pos = strpos($type,"(");
	return substr($type,0,$pos);
}
?>