<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require_once("../../Group-Office.php");
$GO_SECURITY->json_authenticate('mailings');

require_once($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
$ml = new mailings();
require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc.php');
$ab = new addressbook();

require_once($GO_MODULES->modules['mailings']['class_path'].'templates.class.inc.php');
$tp = new templates();

$query = !empty($_REQUEST['query']) ? ($_REQUEST['query']) : null;
$field = isset($_REQUEST['field']) ? ($_REQUEST['field']) : null;

$task = isset($_REQUEST['task']) ? ($_REQUEST['task']) : 'null';

$records = array();

try
{
	switch($task)
	{
		case 'sendcmsfile': 
			
			require_once ($GO_MODULES->modules['cms']['class_path'].'cms.class.inc.php');
			$cms = new cms();
			
			$file = $cms->get_file($_POST['file_id']);
			$folder = $cms->get_folder($file['folder_id']);
			$site = $cms->get_site($folder['site_id']);
			
			$tpl = $GO_MODULES->modules['cms']['path'].'templates/'.$site['template'].'/mailings/mailing.tpl';
			if(file_exists($tpl))
			{
				require_once($GO_CONFIG->class_path.'smarty/Smarty.class.php');
				
				$path = $cms->build_path($folder['id'], $site['root_folder_id']).$file['name'];				
				
				$smarty = new Smarty();
				$smarty->template_dir=$GO_MODULES->modules['cms']['path'].'templates/'.$site['template'];
				$smarty->compile_dir=$GO_CONFIG->tmpdir.'cms/'.$site['id'].'/templates_c';
				if(!is_dir($smarty->compile_dir))
					mkdir($smarty->compile_dir,0755, true);
					
				if(substr($site['domain'],-1,1)!='/')
					$site['domain'].='/';
					
				$smarty->assign('viewurl', 'http://'.$site['domain'].$path);
				$smarty->assign('signoffurl', 'http://'.substr($site['domain'],0,-1).$GO_MODULES->modules['mailings']['url'].'signoff.php?mailing_group_id='.$_POST['mailing_group_id'].'&site_id='.$site['id'].'&type=%type%&id=%id%&hash=%hash%');
				$smarty->assign('settingsurl', 'http://'.substr($site['domain'],0,-1).$GO_MODULES->modules['mailings']['url'].'settings.php?site_id='.$site['id'].'&type=%type%&id=%id%&hash=%hash%');
				$smarty->assign('file', $file);
				
				$response['data']['body'] = $smarty->fetch($tpl);
				
			}else
			{
				$response['data']['body'] = $file['content'];
			}
			
			$response['success']=true;
			
			$response['data']['subject']=$file['name'];
			echo json_encode($response);
		break;
		
		
		case 'linked_message':
			$id = isset($_REQUEST['id']) ? ($_REQUEST['id']) : 0;

			if(isset($_POST['file_id'])){
				require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
				$files = new files();
				$file= $files->get_file($_POST['file_id']);
				$path = $files->build_path($file['folder_id']).'/'.$file['name'];
			}else
			{
				$path = isset($_REQUEST['path']) ? ($_REQUEST['path']) : "";
			}

			$part_number = isset($_REQUEST['part_number']) ? ($_REQUEST['part_number']) : "";
			$response = $ml->get_message_for_client($id, $path, $part_number);

			
			
			echo json_encode($response);
			break;
			
		case 'authorized_templates':


			$tp->get_templates_json($response);

			echo json_encode($response);
			break;

			/* Returns all writeable templates */
		case 'writable_templates':
			if(isset($_POST['delete_keys']))
			{
				$response['deleteSuccess'] = true;

				$delete_templates = json_decode(($_POST['delete_keys']));

				foreach($delete_templates as $id)
				{
					if (!$tp->delete_template($id))
					{
						$response['deleteFeedback'] = $lang['comon']['saveError'];
						$response['deleteSuccess'] = false;
						break;
					}
				}
			}

			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'name';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
			$start = !empty($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = !empty($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';


			$response['total'] = $tp->get_writable_templates($GO_SECURITY->user_id, $start, $limit, $sort, $dir);

			require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$response['results'] = array();
			while($tp->next_record())
			{
				$name=$GO_USERS->get_user_realname($tp->f('user_id'));

				$record = array(
					'id' => $tp->f('id'),
					'user_id' => $tp->f('user_id'),
					'owner' =>$name,
					'name' => $tp->f('name'),
					'extension' => $tp->f('extension'),
					'type' => $tp->f('type'),
					'acl_id' => $tp->f('acl_id')
				);

				$response['results'][] = $record;
			}

			echo json_encode($response);
			break;

		case 'email_template':
			$template_id = isset($_POST['email_template_id']) ? ($_POST['email_template_id'])  : 0;

			$response['data'] = $tp->get_template($template_id);


			if($response['data'])
			{
				if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $response['data']['acl_id'])<2)
				{
					throw new AccessDeniedException();
				}

				$response['success'] = true;

				require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
				$GO_USERS = new GO_USERS();

				$response['data']['user_name']=$GO_USERS->get_user_realname($response['data']['user_id']);

				require_once($GO_CONFIG->class_path.'mail/Go2Mime.class.inc.php');
				$go2mime = new Go2Mime();

				$response['data'] = array_merge($response['data'], $go2mime->mime2GO($response['data']['content'], $GO_MODULES->modules['mailings']['url'].'mimepart.php?template_id='.$template_id));

				$response['data']['inline_attachments']=array();
				foreach($response['data']['attachments'] as $attachment){
					if(!empty($attachment['replacement_url'])){
						$response['data']['inline_attachments'][]=array(
								'id'=>$attachment['id'],
								'tmp_file'=>$attachment['tmp_file'],
								'imap_id'=>$attachment['imap_id'],
								'url'=>$attachment['replacement_url']);
					}else
					{
						///$response['data']['attachments'][]=$attachment;
					}
				}
				unset($response['data']['attachments']);
				unset($response['data']['content']);
			}

			echo json_encode($response);
			break;

		case 'sent_mailings':

			if(!empty($_POST['pause_mailing_id'])){				
				$up_mailing['status']=3;
				$up_mailing['id']=$_POST['pause_mailing_id'];
				$ml->update_mailing($up_mailing);
			}

			if(!empty($_POST['start_mailing_id'])){
				$up_mailing['status']=1;
				$up_mailing['id']=$_POST['start_mailing_id'];
				$ml->update_mailing($up_mailing);
				$ml->launch_mailing($_POST['start_mailing_id']);
			}

			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'ctime';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'DESC';
			$start = !empty($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = !empty($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';

			$user_id = $GO_MODULES->modules['mailings']['write_permission'] ? 0 : $GO_SECURITY->user_id;
			
			if($sort=="user_name")
				$sort="ctime";


			$response['total'] = $ml->get_mailings($_POST['mailing_group_id'],$user_id, $start, $limit, $sort, $dir);
			$response['results'] = array();
				
			$lang['mailings']['statuses']=array(
				'0'=>'Waiting to start',
				'1'=>'In progress',
				'2'=>'Completed',
				'3'=>'Paused'
				);

			require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();
					
			while($record =$ml->next_record())
			{
				$record['user_name']=$GO_USERS->get_user_realname($record['user_id']);

				$record['ctime']=Date::get_timestamp($record['ctime']);

				$record['hide_pause']=$record['status']==3 || $record['status']==2;
				$record['hide_play']=$record['status']!=3;
				$record['message_path']=str_replace($GO_CONFIG->file_storage_path,'', $record['message_path']);

				$record['status']=$lang['mailings']['statuses'][$record['status']];
				$response['results'][] = $record;
			}

			echo json_encode($response);
			break;

				/* Returns all mailinglists */
		case 'mailings':
				
			$auth_type = isset($_POST['auth_type']) ? $_POST['auth_type'] : 'read';
				
			if(isset($_POST['delete_keys']))
			{
				$response['deleteSuccess'] = true;

				$delete_mailings = json_decode(($_POST['delete_keys']));

				foreach($delete_mailings as $id)
				{
					if (!$ml->delete_mailing_group($id))
					{
						$response['deleteFeedback'] = $lang['comon']['saveError'];
						$response['deleteSuccess'] = false;
						break;
					}
				}			

				//delete user filter settings because they might filter on a non-existing list now.
				$GO_CONFIG->delete_setting('mailings_filter');
			}
			
			$selected_mailings = $GO_CONFIG->get_setting('mailings_filter', $GO_SECURITY->user_id);			
			$selected_mailings = empty($selected_mailings) ? array() : explode(',', $selected_mailings);

			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'name';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
			$start = !empty($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = !empty($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';


			$response['total'] = $ml->get_authorized_mailing_groups($auth_type, $GO_SECURITY->user_id, $start, $limit, $sort, $dir);
			$response['results'] = array();

			require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();
				
			while($ml->next_record())
			{
				$name=$GO_USERS->get_user_realname($ml->f('user_id'));

				$record = array(
					'id' => $ml->f('id'),
					'user_id' => $ml->f('user_id'),
					'owner' =>$name,
					'name' => $ml->f('name'),					
					'acl_id' => $ml->f('acl_id'),
					'checked'=>in_array($ml->f('id'), $selected_mailings)
				);

				$response['results'][] = $record;
			}
				
			echo json_encode($response);
			break;
		case 'mailing':
			$mailing_id = isset($_POST['mailing_id']) ? ($_POST['mailing_id'])  : 0;
				
			$record = $ml->get_mailing_group($mailing_id);

			if($record)
			{
				$response['success'] = true;

				require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
				$GO_USERS = new GO_USERS();

				$response['data']=$record;
				$response['data']['user_name']=$GO_USERS->get_user_realname($response['data']['user_id']);
			}
				
			echo json_encode($response);
			break;
			
		case 'mailing_group_string': 
		
			require_once($GO_CONFIG->class_path.'mail/RFC822.class.inc');
			$RFC822 = new RFC822();
			$groups = explode(',', $_REQUEST['mailing_groups']);
	
			$response = '';
			
			foreach($groups as $group_id)
			{
				$ml->get_contacts_from_mailing_group($group_id);
				while($contact = $ml->next_record())
				{
					if(!empty($contact['email']))					
						$response .= $RFC822->write_address(String::format_name($contact), $contact['email']).', ';
				}
				$ml->get_companies_from_mailing_group($group_id);
				while($company = $ml->next_record())
				{
					if(!empty($company['email']))					
						$response .= $RFC822->write_address($company['name'], $company['email']).', ';
				}
				
				$ml->get_users_from_mailing_group($group_id);
				while($user = $ml->next_record())
				{
					if(!empty($user['email']))					
						$response .= $RFC822->write_address(String::format_name($user), $user['email']).', ';
				}
			}			
			echo $response;
		break;

		case 'mailing_contacts':
			$mailing_id = isset($_REQUEST['mailing_id']) ? ($_REQUEST['mailing_id']) : 0;
			
			if(isset($_POST['add_addressbook_id']))
			{				
				$ml->add_addressbook_contacts_to_mailing_group(($_POST['add_addressbook_id']), $mailing_id);				
			}
			
			
			if(isset($_POST['add_keys']))
			{
				$add_keys = json_decode(($_POST['add_keys']));

				foreach($add_keys as $id)
				{
					if(!$ml->contact_is_in_group($id, $mailing_id))
					{
						if (!$ml->add_contact_to_mailing_group($id, $mailing_id))
						{
							$response['deleteFeedback'] = $lang['common']['saveError'];
							$response['deleteSuccess'] = false;
							break;
						}
					}
				}
			}

			if(!empty($_POST['add_search_result'])){				
				//use saved export query to add the results
				//echo $_SESSION['GO_SESSION']['export_queries']['search_contacts']['query'];
				$db = new db();
				$db->query($_SESSION['GO_SESSION']['export_queries']['search_contacts']['query']);
				while($contact = $db->next_record()){
					
					if(!$ml->contact_is_in_group($contact['id'], $mailing_id))
					{
						if (!$ml->add_contact_to_mailing_group($contact['id'], $mailing_id))
						{
							$response['deleteFeedback'] = $lang['common']['saveError'];
							$response['deleteSuccess'] = false;
							break;
						}
					}
				}
			}
				
			if(isset($_POST['delete_keys']))
			{
				$delete_keys = json_decode(($_POST['delete_keys']));
				$response['deleteSuccess']=true;

				foreach($delete_keys as $id)
				{
					if($ml->contact_is_in_group($id, $mailing_id))
					{
						if ($ml->remove_contact_from_mailing_groups($id))
						{
							$response['deleteFeedback'] = $lang['common']['saveError'];
							$response['deleteSuccess'] = false;
							break;
						}
					}
				}
			}


			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
			$start = !empty($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = !empty($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';

			$response['total'] = $ml->get_contacts_from_mailing_group($mailing_id, $start, $limit, 'name', $dir);
			$response['results'] = array();
				
			while($ml->next_record())
			{
				$record = $ml->record;

				$contact = $ab->get_contact($ml->f('id'));
				$company = $ab->get_company($contact['company_id']);

				$record['name'] = String::format_name($ml->f('last_name'), $ml->f('first_name'), $ml->f('middle_name'));
				$record['home_phone'] = $contact['home_phone'];
				$record['work_phone'] = $contact['work_phone'];
				$record['cellular']  = $contact['cellular'];
				$record['company_name'] = !empty($company) ? $company['name'] : '';

				$response['results'][] = $record;
			}
				
			echo json_encode($response);
			break;


		case 'mailing_companies':
			$mailing_id = isset($_REQUEST['mailing_id']) ? ($_REQUEST['mailing_id']) : 0;
			
			if(isset($_POST['add_addressbook_id']))
			{				
				$ml->add_addressbook_companies_to_mailing_group(($_POST['add_addressbook_id']), $mailing_id);				
			}

			if(isset($_POST['add_keys']))
			{
				$add_keys = json_decode(($_POST['add_keys']));

				foreach($add_keys as $id)
				{
					if(!$ml->company_is_in_group($id, $mailing_id))
					{
						if (!$ml->add_company_to_mailing_group($id, $mailing_id))
						{
							$response['deleteFeedback'] = $lang['common']['saveError'];
							$response['deleteSuccess'] = false;
							break;
						}
					}
				}
			}

			if(!empty($_POST['add_search_result'])){
				//use saved export query to add the results
				//echo $_SESSION['GO_SESSION']['export_queries']['search_contacts']['query'];
				$db = new db();
				$db->query($_SESSION['GO_SESSION']['export_queries']['search_companies']['query']);
				while($company = $db->next_record()){

					if(!$ml->company_is_in_group($company['id'], $mailing_id))
					{
						if (!$ml->add_company_to_mailing_group($company['id'], $mailing_id))
						{
							$response['deleteFeedback'] = $lang['common']['saveError'];
							$response['deleteSuccess'] = false;
							break;
						}
					}
				}
			}
				
			if(isset($_POST['delete_keys']))
			{
				$delete_keys = json_decode(($_POST['delete_keys']));
				$response['deleteSuccess']=true;

				foreach($delete_keys as $id)
				{
					if($ml->company_is_in_group($id, $mailing_id))
					{
						if ($ml->remove_company_from_mailing_groups($id))
						{
							$response['deleteFeedback'] = $lang['common']['saveError'];
							$response['deleteSuccess'] = false;
							break;
						}
					}
				}
			}

			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'name';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
			$start = !empty($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = !empty($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';


			$response['total'] = $ml->get_companies_from_mailing_group($mailing_id, $start=0, $offset=0, $sort, $dir);

			$response['results'] = array();
				
			while($ml->next_record())
			{
				$name = $ml->f('name');
				$company = $ab->get_company($ml->f('id'));

				$record = array(
					'id' => $ml->f('id'),
					'name' =>$name,
					'email' => $ml->f('email'),
					'homepage' => $company['homepage'],
					'phone' => $company['phone'],
					'fax' => $company['fax']
				);

				$response['results'][] = $record;
			}
				
			echo json_encode($response);
			break;



		case 'mailing_users':
			$mailing_id = isset($_REQUEST['mailing_id']) ? ($_REQUEST['mailing_id']) : 0;
				
			if(isset($_POST['add_keys']))
			{
				$add_keys = json_decode(($_POST['add_keys']));

				foreach($add_keys as $id)
				{
					if(!$ml->user_is_in_group($id, $mailing_id))
					{
						if (!$ml->add_user_to_mailing_group($id, $mailing_id))
						{
							$response['deleteFeedback'] = $lang['common']['saveError'];
							$response['deleteSuccess'] = false;
							break;
						}
					}
				}
			}

		
				
			if(isset($_POST['delete_keys']))
			{
				$delete_keys = json_decode(($_POST['delete_keys']));
				$response['deleteSuccess']=true;

				foreach($delete_keys as $id)
				{
					if($ml->user_is_in_group($id, $mailing_id))
					{
						if ($ml->remove_user_from_mailing_groups($id))
						{
							$response['deleteFeedback'] = $lang['comon']['saveError'];
							$response['deleteSuccess'] = false;
							break;
						}
					}
				}
			}

			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
			$start = !empty($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = !empty($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';

			$response['total'] = $ml->get_users_from_mailing_group($mailing_id, $start, $limit, 'name', $dir);
			$response['results'] = array();
				
			while($ml->next_record())
			{
				$name = String::format_name($ml->f('last_name'), $ml->f('first_name'), $ml->f('middle_name'));

				$record = array(
					'id' => $ml->f('id'),
					'name' =>$name,
					'email' => $ml->f('email')
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