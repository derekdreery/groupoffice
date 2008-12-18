<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: action.php 1524 2008-12-03 09:35:45Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require_once("../../Group-Office.php");

$allowed_scripts = explode(',',$GO_CONFIG->allow_unsafe_scripts);
if(!in_array('modules/addressbook/cms.php', $allowed_scripts))
{
	die('Access denied');
}

if(isset($_POST['language']))
{
	$GO_LANGUAGE->set_language($_POST['language']);
}

require_once($GO_LANGUAGE->get_language_file('addressbook'));
require($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
$ab = new addressbook();

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : null;

try
{
	switch($task)
	{
		case 'add_contact':			
			
			$contact_id = isset($_REQUEST['contact_id']) ? ($_REQUEST['contact_id']) : 0;
				
			$addressbook = $ab->get_addressbook_by_name($_REQUEST['addressbook']);
			if(!$addressbook)
			{
				throw new Exception('Addressbook not found!');
			}

			$credentials = array (
				'first_name','middle_name','last_name','title','initials','sex','email',
				'email2','email3','home_phone','fax','cellular','comment','address','address_no',
				'zip','city','state','country','company','department','function','work_phone',
				'work_fax','salutation'
				);

				$contact_credentials['email_allowed']='1';
				
				foreach($credentials as $key)
				{
					$contact_credentials[$key] = isset($_REQUEST[$key]) ? $_REQUEST[$key] : '';
				}

				if(is_array($contact_credentials['comment']))
				{
					$comments='';
					foreach($contact_credentials['comment'] as $key=>$value)
					{
						$comments .= trim($key).":\n".trim($value)."\n\n";
					}
					$contact_credentials['comment']=$comments;
				}
				
				//$required=array('email', 'first_name', 'last_name');
				foreach($_REQUEST['required'] as $key)
				{
					if(empty($contact_credentials[$key]))
					{
						throw new Exception($lang['common']['missingField']);
					}
				}			
				
				$contact_credentials['addressbook_id']=$addressbook['id'];

				$response['success'] = true;
					
				if(!empty($contact_credentials['company']) && empty($contact_credentials['company_id']))
				{
					if(!$contact_credentials['company_id'] = $ab->get_company_id_by_name($contact_credentials['company'], $contact_credentials['addressbook_id']))
					{
						$company['addressbook_id'] = $contact_credentials['addressbook_id'];
						$company['name'] = $contact_credentials['company']; // bedrijfsnaam
						$company['user_id'] = $GO_SECURITY->user_id;
						$contact_credentials['company_id'] = $ab->add_company($company);
					}
				}

				if(!empty($contact_credentials['birthday']))
				$contact_credentials['birthday'] = Date::to_db_date($contact_credentials['birthday'], false);

				unset($contact_credentials['company']);

				$response['contact_id'] = $contact_id = $ab->add_contact($contact_credentials);
				if(!$contact_id)
				{
					throw new Exception($lang['comon']['saveError']);
				}
					
				if($GO_MODULES->modules['files'])
				{
					require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc');
					$fs = new files();

					$response['files_path']='contacts/'.$contact_id;
					$full_path = $GO_CONFIG->file_storage_path.$response['files_path'];
					$fs->check_share($full_path, $GO_SECURITY->user_id, $addressbook['acl_read'], $addressbook['acl_write'],true);
				}

				if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission'])
				{
					require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();

					$cf->update_fields($GO_SECURITY->user_id, $contact_id, 2, $_REQUEST, true);
				}

				if(isset($GO_MODULES->modules['mailings']) && $GO_MODULES->modules['mailings']['read_permission'] && isset($_REQUEST['mailings']))
				{
					require($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
					$ml = new mailings();
						
					foreach($_REQUEST['mailings'] as $mailing_name)
					{
						$mailing=$ml->get_mailing_group_by_name($mailing_name);
						if(!$mailing)
						{
							throw new Exception('Address list not found!');
						}
						$ml->add_contact_to_mailing_group($contact_id, $mailing['id']);
					}
				}
				
				$user = $GO_USERS->get_user($addressbook['user_id']);
				
				$body = $lang['addressbook']['newContactFromSite'].'<br /><a href="go:showContact('.$contact_id.');">'.$lang['addressbook']['clickHereToView'].'</a>';

				require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
				$swift = new GoSwift($user['email'], $lang['addressbook']['newContactAdded']);
				$swift->set_body($body);
				$swift->sendmail($GO_CONFIG->webmaster_email, $GO_CONFIG->title);
				break;

	}
}
catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']= false;
}

echo json_encode($response);
?>