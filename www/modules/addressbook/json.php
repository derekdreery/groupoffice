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

require($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
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
		case 'search_sender':
			
			$email = ($_POST['email']);
			
			$contact = $ab->get_contact_by_email($email, $GO_SECURITY->user_id);
			
			if($contact)
			{
				$response['contact_id']=$contact['id'];
			}else
			{
				$response['contact_id']=0;
			}
			$response['success']=true;
			echo json_encode($response);
			break;
		
		/* all-contacts */
		case 'contacts':
			
			
			if(isset($_POST['mailings_filter']))
			{
				$mailings_filter = json_decode(($_POST['mailings_filter']), true);				
				$GO_CONFIG->save_setting('mailings_filter', implode(',',$mailings_filter), $GO_SECURITY->user_id);
			}else
			{	
				$mailings_filter = $GO_CONFIG->get_setting('mailings_filter', $GO_SECURITY->user_id);
				$mailings_filter = (empty($mailings_filter) || !isset($_POST['enable_mailings_filter'])) ? array() : explode(',', $mailings_filter);
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
				$query = !empty($query) ? '%'.$query.'%' : '';
			}
				
				
			$response['results']=array();
			$response['total'] = $ab->search_contacts(
			$GO_SECURITY->user_id,
			$query,
			$field,
			$addressbook_id,
			$start,
			$limit,
			false,
			$sort,
			$dir,
			false,
			$query_type,
			$mailings_filter
			);

			while($ab->next_record())
			{
				$ab->record['name'] = String::format_name($ab->f('last_name'), $ab->f('first_name'), $ab->f('middle_name'));

				$response['results'][] = $ab->record;
			}

			echo json_encode($response);
			break;

			/* all compagnies */
		case 'companies':
			
			if(isset($_POST['mailings_filter']))
			{
				$mailings_filter = json_decode(($_POST['mailings_filter']), true);				
				$GO_CONFIG->save_setting('mailings_filter', implode(',',$mailings_filter), $GO_SECURITY->user_id);
			}else
			{	
				$mailings_filter = $GO_CONFIG->get_setting('mailings_filter', $GO_SECURITY->user_id);
				$mailings_filter = (empty($mailings_filter) || !isset($_POST['enable_mailings_filter'])) ? array() : explode(',', $mailings_filter);
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

			$response['results'] = array();
			$response['total'] = $ab->search_companies(
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
			$mailings_filter
			);

			while($ab->next_record())
			{
				$response['results'][] = $ab->record;
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
				try{
					$add_contacts = json_decode(($_POST['add_contacts']));

					foreach($add_contacts as $id)
					{
						$contact['id']=$id;
						$contact['company_id']=$company_id;

						$ab->update_contact($contact);
					}
				}
				catch (Exception $e)
				{

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

				if($task == 'load_contact_with_items')
				{
					$response['data']['comment']=String::text_to_html($response['data']['comment']);
				}

				if($response['data']['birthday'] == '0000-00-00')
				{
					$response['data']['birthday'] = '';
				} else {
					$response['data']['birthday'] = Date::format($response['data']['birthday'], false);
				}
				
				
				require($GO_LANGUAGE->get_base_language_file('countries'));
				$response['data']['country']=isset($countries[$response['data']['country']]) ? $countries[$response['data']['country']] : $response['data']['country'];
			
				

				if($response['data']['company_id'] > 0)
				{
					$company = $ab->get_company($response['data']['company_id']);
					$response['data']['company_name'] = $company['name'];
				} else {
					$response['data']['company_name'] = '';
				}
					
				$response['success']=true;
				
				
				if(isset($GO_MODULES->modules['files']))
				{
					require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc');
					$fs = new files();
	
					$response['data']['files_path']='contacts/'.$response['data']['id'];
	
					$full_path = $GO_CONFIG->file_storage_path.$response['data']['files_path'];
					if(!file_exists($full_path))
					{
						$fs->mkdir_recursive($full_path);
	
						if(!$fs->get_folder(addslashes($full_path)))
						{
							$folder['user_id']=$response['data']['user_id'];
							$folder['path']=addslashes($full_path);
							$folder['visible']='0';
							$folder['acl_read']=$response['data']['acl_read'];
							$folder['acl_write']=$response['data']['acl_write'];
	
							$fs->add_folder($folder);
						}
					}
				}				
			
			}
				
			if($task == 'load_contact')
			{
				if(isset($GO_MODULES->modules['customfields']))
				{
					require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();
					$values = $cf->get_values($GO_SECURITY->user_id, 2, $contact_id);
					$response['data']=array_merge($response['data'], $values);
				}

				if(isset($GO_MODULES->modules['mailings']))
				{
					require($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
					
					$ml = new mailings();
					$ml2 = new mailings();
						
					$ml->get_authorized_mailing_groups('write', $GO_SECURITY->user_id, 0,0);
					while($ml->next_record())
					{
						$response['data']['mailing_'.$ml->f('id')]=$ml2->contact_is_in_group($contact_id, $ml->f('id'));
					}
				}

				echo json_encode($response);
				break;
			}
			
			
			if(isset($GO_MODULES->modules['customfields']))
			{
				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
				$response['data']['customfields']=$cf->get_all_fields_with_values($GO_SECURITY->user_id, 2, $contact_id);
			}
			
			if(isset($GO_MODULES->modules['comments']))
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
				$response['data']['files']=$fs->get_content_json($full_path);
			}else
			{
				$response['data']['files']=array();				
			}
				
				
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
				}

				require($GO_LANGUAGE->get_base_language_file('countries'));
				$response['data']['country']=isset($countries[$response['data']['country']]) ? $countries[$response['data']['country']] : $response['data']['country'];
				$response['data']['post_country']=isset($countries[$response['data']['post_country']]) ? $countries[$response['data']['post_country']] : $response['data']['post_country'];
			
				$response['data']['links'] = array();
				$response['success']=true;
				
				
				
				if(isset($GO_MODULES->modules['files']))
				{
					require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc');
					$fs = new files();
	
					$response['data']['files_path']='companies/'.$response['data']['id'];
	
					$full_path = $GO_CONFIG->file_storage_path.$response['data']['files_path'];
					if(!file_exists($full_path))
					{
						$fs->mkdir_recursive($full_path);
	
						if(!$fs->get_folder(addslashes($full_path)))
						{
							$folder['user_id']=$response['data']['user_id'];
							$folder['path']=addslashes($full_path);
							$folder['visible']='0';
							$folder['acl_read']=$response['data']['acl_read'];
							$folder['acl_write']=$response['data']['acl_write'];
	
							$fs->add_folder($folder);
						}
					}
				}				
			}
				
		
				
				
				
				
			if($task == 'load_company')
			{
				if(isset($GO_MODULES->modules['customfields']))
				{
					require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();
					$values = $cf->get_values($GO_SECURITY->user_id, 3, $company_id);
					$response['data']=array_merge($response['data'], $values);
				}
				
				if(isset($GO_MODULES->modules['mailings']))
				{
					require($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
					$ml = new mailings();
					$ml2 = new mailings();
						
					$ml->get_authorized_mailing_groups('write', $GO_SECURITY->user_id, 0,0);
					while($ml->next_record())
					{
						$response['data']['mailing_'.$ml->f('id')]=$ml2->company_is_in_group($company_id, $ml->f('id'));
					}
				}
				echo json_encode($response);
				break;
			}
				
			
				
			if(isset($GO_MODULES->modules['customfields']))
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
				$response['data']['files']=$fs->get_content_json($full_path);
			}else
			{
				$response['data']['files']=array();				
			}
			
			
			if(isset($GO_MODULES->modules['comments']))
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
					'acl_write' => $ab->f('acl_write')
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