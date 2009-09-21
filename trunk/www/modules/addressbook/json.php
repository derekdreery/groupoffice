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
$GO_SECURITY->json_authenticate('addressbook');

require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc.php');
$ab = new addressbook;

$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'name';
$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
$query = isset($_REQUEST['query']) ? ($_REQUEST['query']) : null;
$field = isset($_REQUEST['field']) ? ($_REQUEST['field']) : 'name';

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
			
			require($GO_LANGUAGE->get_language_file('addressbook'));
			
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
			
			if($GO_MODULES->has_module('customfields'))
			{
				
				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
				
				$fields = $cf->get_authorized_fields($GO_SECURITY->user_id, $link_type);
				while($field = array_shift($fields))
				{
					if($field['datatype']!='heading' && $field['datatype']!='function')
					{
						$f = array('field'=>'cf_'.$link_type.'.'.$field['name'], 'label'=>$field['label'], 'type'=>$field['datatype']);
						
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
			
			echo json_encode($response);
			break;
		
		case 'search_sender':
			
			$response['results']=array();
			$response['total'] = $ab->get_contacts_by_email($_POST['email'], $GO_SECURITY->user_id);
			
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
			
			if(!isset($_POST['enable_mailings_filter']))
			{
				$mailings_filter=array();
			}elseif(isset($_POST['mailings_filter']))
			{
				$mailings_filter = json_decode($_POST['mailings_filter'], true);				
				$GO_CONFIG->save_setting('mailings_filter', implode(',',$mailings_filter), $GO_SECURITY->user_id);
			}else
			{	
				$mailings_filter = $GO_CONFIG->get_setting('mailings_filter', $GO_SECURITY->user_id);
				$mailings_filter = empty($mailings_filter) ? array() : explode(',', $mailings_filter);
			}

			if(isset($_POST['delete_keys']))
			{
				$response['deleteSuccess'] = true;
				try{
					$delete_contacts = json_decode(($_POST['delete_keys']));

					foreach($delete_contacts as $id)
					{
						$contact = $ab->get_contact($id);
						if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $contact['acl_write']))
						{
							throw new AccessDeniedException();
						}

						$ab->delete_contact($id);
					}
				}
				catch (Exception $e)
				{
					$response['deleteFeedback'] = $e->getMessage();
					$response['deleteSuccess'] = false;
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
			
			$advancedQuery = '';
			if(!empty($_POST['advancedQuery']))
			{
				$aq = json_decode($_POST['advancedQuery'], true);				
				foreach($aq as $field=>$value)
				{
					if(!empty($advancedQuery))
					{
						$advancedQuery .= ' AND ';
					}
					if($field=='ab_contacts.name')
					{
						$field = 'CONCAT(first_name,middle_name,last_name)';
					}else
					{
						$field = $ab->escape($field);
					}
					$advancedQuery .= $field.' LIKE \'%'.$ab->escape($value).'%\'';
				}			
			}
				
				
			$response['results']=array();
			$response['total']=$ab->search_contacts(
			$GO_SECURITY->user_id,
			$query,
			$field,
			$addressbook_id,
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

			while($record = $ab->next_record())
			{
				addressbook::format_contact_record($record);
				$response['results'][] = $record;
			}

			echo json_encode($response);
			break;

		case 'companies':
			
			if(!isset($_POST['enable_mailings_filter']))
			{
				$mailings_filter=array();
			}elseif(isset($_POST['mailings_filter']))
			{
				$mailings_filter = json_decode(($_POST['mailings_filter']), true);				
				$GO_CONFIG->save_setting('mailings_filter', implode(',',$mailings_filter), $GO_SECURITY->user_id);
			}else
			{	
				$mailings_filter = $GO_CONFIG->get_setting('mailings_filter', $GO_SECURITY->user_id);
				$mailings_filter = empty($mailings_filter) ? array() : explode(',', $mailings_filter);
			}
			
			if(isset($_POST['delete_keys']))
			{
				$response['deleteSuccess'] = true;
				try{
					$delete_companies = json_decode(($_POST['delete_keys']));

					foreach($delete_companies as $id)
					{
						$company = $ab->get_company($id);
						if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $company['acl_write']))
						{
							throw new AccessDeniedException();
						}

						$ab->delete_company($id);
					}
				}
				catch (Exception $e)
				{
					$response['deleteFeedback'] = $e->getMessage();
					$response['deleteSuccess'] = false;
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
				$field = '';
				$query = '%'.$query.'%';
			}
			
			$advancedQuery = '';
			if(!empty($_POST['advancedQuery']))
			{
				$aq = json_decode($_POST['advancedQuery'], true);				
				foreach($aq as $field=>$value)
				{
					if(empty($advancedQuery))
					{
						$advancedQuery .= ' AND ';
					}
					$advancedQuery .= $ab->escape($field).' LIKE \''.$ab->escape($value).'\'';
				}			
			}

			$response['results'] = array();
			$response['total']=$ab->search_companies(
			$GO_SECURITY->user_id,
			$query,
			$field,
			$addressbook_id,
			$start,
			$limit,
			false,
			$sort,
			$dir,
			$query_type,
			$mailings_filter,
			$advancedQuery
			);

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

			if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $company['acl_write']))
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
				
			$response['data']['write_permission']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, $response['data']['acl_write']);
			if(!$response['data']['write_permission'] && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $response['data']['acl_read']) && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $response['data']['acl_write']))
			{
				throw new AccessDeniedException();
			}
				
			if($response['data'])
			{
				$response['data']['full_name'] = String::format_name($response['data']['last_name'], $response['data']['first_name'], $response['data']['middle_name']);

				require($GO_LANGUAGE->get_base_language_file('countries'));
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
				} else {
					$response['data']['company_name'] = '';
				}

				$values = array('address_no', 'address', 'zip', 'city', 'state', 'country');

				$response['data']['formatted_address'] = $response['data']['address_format'];

				foreach($values as $val)
					$response['data']['formatted_address'] = str_replace('{'.$val.'}', $response['data'][$val], $response['data']['formatted_address']);

				$response['data']['formatted_address'] = preg_replace("/(\r\n)+|(\n|\r)+/", "<br />", $response['data']['formatted_address']);
				$response['data']['google_maps_link']='http://maps.google.com/maps?q=';

				$response['data']['google_maps_link']=google_maps_link($response['data']['address'], $response['data']['address_no'], $response['data']['city'], $response['data']['country']);

				$response['success']=true;	
			}
				
			if($task == 'load_contact')
			{
				if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission'])
				{
					require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();
					$values = $cf->get_values($GO_SECURITY->user_id, 2, $contact_id);
					$response['data']=array_merge($response['data'], $values);
				}

				if($GO_MODULES->has_module('mailings'))
				{
					require_once($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
					
					$ml = new mailings();
					$ml2 = new mailings();
						
					$count = $ml->get_authorized_mailing_groups('write', $GO_SECURITY->user_id, 0,0);

					
					while($ml->next_record())
					{						
						$response['data']['mailing_'.$ml->f('id')]=$ml2->contact_is_in_group($contact_id, $ml->f('id')) ? true : false;
					}
				}

				echo json_encode($response);
				break;
			}			
			
			if($GO_MODULES->has_module('customfields'))
			{
				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
				$response['data']['customfields']=$cf->get_all_fields_with_values($GO_SECURITY->user_id, 2, $contact_id);
			}
			
			if($GO_MODULES->has_module('comments'))
			{
				require_once ($GO_MODULES->modules['comments']['class_path'].'comments.class.inc.php');
				$comments = new comments();
				
				$response['data']['comments']=$comments->get_comments_json($response['data']['id'], 2);
			}
				
			$response['data']['links'] = array();
			/* loadContactDetails - contact sidepanel */
				
				
			require_once($GO_CONFIG->class_path.'/base/search.class.inc.php');
			$search = new search();
				
			$links_json = $search->get_latest_links_json($GO_SECURITY->user_id, $response['data']['id'], 2);
				
			$response['data']['links']=$links_json['results'];
			
			if(isset($GO_MODULES->modules['files']))
			{
				require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
				$fs = new files();						
				$response['data']['files']=$fs->get_content_json($response['data']['files_folder_id']);
			}else
			{
				$response['data']['files']=array();				
			}
			
			if(!isset($response['data']['iso_address_format']) || $response['data']['iso_address_format'] == '')
				$response['data']['iso_address_format'] = $response['data']['default_iso_address_format'];
				
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
			
			$response['data']['write_permission']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, $response['data']['acl_write']);
			if(!$response['data']['write_permission'] && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $response['data']['acl_read']) && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $response['data']['acl_write']))
			{
				throw new AccessDeniedException();
			}
				
			if($response['data'])
			{
				if($task == 'load_company_with_items')
				{
					$response['data']['comment']=String::text_to_html($response['data']['comment']);
					
					require($GO_LANGUAGE->get_base_language_file('countries'));
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
				
				$response['data']['links'] = array();
				$response['success']=true;		
			}		
				
			if($task == 'load_company')
			{
				if($GO_MODULES->has_module('customfields'))
				{
					require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();
					$values = $cf->get_values($GO_SECURITY->user_id, 3, $company_id);
					$response['data']=array_merge($response['data'], $values);
				}
				
				if($GO_MODULES->has_module('mailings'))
				{
					require_once($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
					$ml = new mailings();
					$ml2 = new mailings();
						
					$ml->get_authorized_mailing_groups('write', $GO_SECURITY->user_id, 0,0);
					while($ml->next_record())
					{
						$response['data']['mailing_'.$ml->f('id')]=$ml2->company_is_in_group($company_id, $ml->f('id')) ? true : false;
					}
				}
				echo json_encode($response);
				break;
			}			
				
			if($GO_MODULES->has_module('customfields'))
			{
				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
				$response['data']['customfields']=$cf->get_all_fields_with_values($GO_SECURITY->user_id, 3, $company_id);
			}				

			$ab->get_company_contacts($response['data']['id']);
			$response['data']['employees']=array();
			while($ab->next_record())
			{
				$response['data']['employees'][]=array(
					'id'=>$ab->f('id'),
					'name'=>String::format_name($ab->record),
					'email'=>$ab->f('email')					
				);
			}
				
				
				
				
			$response['data']['links'] = array();
			/* loadCompanyDetails - company sidepanel */
				
				
			require_once($GO_CONFIG->class_path.'/base/search.class.inc.php');
			$search = new search();
				
			$links_json = $search->get_latest_links_json($GO_SECURITY->user_id, $response['data']['id'], 3);
				
			$response['data']['links']=$links_json['results'];
			
			if(isset($GO_MODULES->modules['files']))
			{
				require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
				$fs = new files();	
				$response['data']['files']=$fs->get_content_json($response['data']['files_folder_id']);
			}else
			{
				$response['data']['files']=array();				
			}			
			
			if($GO_MODULES->has_module('comments'))
			{
				require_once ($GO_MODULES->modules['comments']['class_path'].'comments.class.inc.php');
				$comments = new comments();
				
				$response['data']['comments']=$comments->get_comments_json($response['data']['id'], 3);
			}
				
				
			echo json_encode($response);
			break;

			/* get all readable addressbooks */
		case 'addressbooks':

			require($GO_LANGUAGE->get_language_file('addressbook'));
				
			$auth_type = isset($_POST['auth_type']) ?$_POST['auth_type'] : 'read';

			$response['results'] = array();
				
			if($auth_type=='read')
			{
				$record = array(
					'id' => '0',
					'name' => $lang['addressbook']['allAddressbooks']
				);
				$response['results'][] = $record;

				$response['total'] = $ab->get_user_addressbooks($GO_SECURITY->user_id, $start, $limit, $sort, $dir);
				
				if($response['total']==0)
				{
					$ab->get_addressbook();
					$response['total'] = $ab->get_user_addressbooks($GO_SECURITY->user_id, $start, $limit, $sort, $dir);
				}
			}else
			{
				try{
					if(isset($_POST['delete_keys']))
					{
						$response['deleteSuccess'] = true;
							
						$delete_addressbooks = json_decode(($_POST['delete_keys']));
							
						foreach($delete_addressbooks as $id)
						{
							$ab->delete_addressbook($id);
						}
					}
				}
				catch (Exception $e)
				{
					$response['deleteFeedback'] = $e->getMessage();
					$response['deleteSuccess'] = false;
				}

				$response['total'] = $ab->get_writable_addressbooks($GO_SECURITY->user_id, $start, $limit, $sort, $dir);
				if($response['total']==0)
				{
					$ab->get_addressbook();
					$response['total'] = $ab->get_writable_addressbooks($GO_SECURITY->user_id, $start, $limit, $sort, $dir);
				}
					
			}
				

				

			while($ab->next_record())
			{
				$user = $GO_USERS->get_user($ab->f('user_id'));
				$user_name = String::format_name($user['last_name'], $user['first_name'], $user['middle_name']);

				$record = array(
					'id' => $ab->f('id'),
					'user_id' => $ab->f('user_id'),
					'name' => $ab->f('name'),
					'owner' => $user_name,
					'acl_read' => $ab->f('acl_read'),
					'acl_write' => $ab->f('acl_write'),
					'default_iso_address_format' => $ab->f('default_iso_address_format'),
					'default_salutation' => $ab->f('default_salutation')
				);
					
				$response['results'][] = $record;
			}

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
?>