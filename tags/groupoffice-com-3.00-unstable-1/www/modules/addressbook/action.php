<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: action.php 2942 2008-09-02 12:24:54Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require_once("../../Group-Office.php");
$GO_SECURITY->json_authenticate('addressbook');
require_once($GO_LANGUAGE->get_language_file('addressbook'));
require($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
$ab = new addressbook;

$feedback = null;

$task = isset($_REQUEST['task']) ? smart_addslashes($_REQUEST['task']) : null;

try
{
	switch($task)
	{
		case 'save_contact':
			$contact_id = isset($_REQUEST['contact_id']) ? smart_addslashes($_REQUEST['contact_id']) : 0;

			$credentials = array (
				'first_name','middle_name','last_name','title','initials','sex','birthday','email',
				'email2','email3','home_phone','fax','cellular','comment','address','address_no',
				'zip','city','state','country','company','company_id','department','function','work_phone',
				'work_fax','addressbook_id','salutation', 'email_allowed'
				);
					
				foreach($credentials as $key)
				{
					$contact_credentials[$key] = isset($_REQUEST[$key]) ? smart_addslashes($_REQUEST[$key]) : null;
				}
				
				
				$addressbook = $ab->get_addressbook($contact_credentials['addressbook_id']);
				if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_write']))
				{
					throw new AccessDeniedException();
				}
					
				if($contact_id > 0)
				{
					$old_contact = $ab->get_contact($contact_id);

					if(($old_contact['addressbook_id'] != $contact_credentials['addressbook_id']) && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $old_contact['acl_write']))
					{
						throw new AccessDeniedException();
					}
				}

					

				$result['success'] = true;
				$result['feedback'] = $feedback;
					
			
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
				unset($contact_credentials['company']);
				if ($contact_id < 1)
				{					
					$contact_id = $ab->add_contact($contact_credentials);

					if(!$contact_id)
					{
						$result['feedback'] = $strSaveError;
						$result['success'] = false;
					} else {
						$result['contact_id'] =  $contact_id;
					}
					
					if($GO_MODULES->modules['files'])
					{
						require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc');
						$fs = new files();
	
						$result['files_path']='contacts/'.$contact_id;
							
						$full_path = $GO_CONFIG->file_storage_path.$result['files_path'];
						if(!file_exists($full_path))
						{
							$fs->mkdir_recursive($full_path);
								
							$folder['user_id']=$GO_SECURITY->user_id;
							$folder['path']=addslashes($full_path);
							$folder['visible']='0';
							$folder['acl_read']=$addressbook['acl_read'];
							$folder['acl_write']=$addressbook['acl_write'];
								
							$fs->add_folder($folder);
						}
					}
				} else {
					
					$contact_credentials['id'] = $contact_id;
					$contact_credentials['birthday'] = Date::to_db_date($contact_credentials['birthday'], false);

					if($old_contact['addressbook_id']!=$contact_credentials['addressbook_id'])
					{
						$ab->move_contacts_company($contact_credentials['company_id'], $old_contact['addressbook_id'], $contact_credentials['addressbook_id']);
					}

					if(!$ab->update_contact($contact_credentials))
					{
						$result['feedback'] = $strSaveError;
						$result['success'] = false;
					}
				}
				
				
				if(isset($GO_MODULES->modules['customfields']))
				{
					require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();
					
					$cf->update_fields($GO_SECURITY->user_id, $contact_id, 2, $_POST);
				}
				
				if(isset($GO_MODULES->modules['mailings']))
				{
					require($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
					$ml = new mailings();
					$ml2 = new mailings();
					
					$ml->get_authorized_mailing_groups('write', $GO_SECURITY->user_id, 0,0);
					while($ml->next_record())
					{
						$is_in_group = $ml2->contact_is_in_group($contact_id, $ml->f('id'));
						$should_be_in_group = isset($_POST['mailing_'.$ml->f('id')]);
						
						if($is_in_group && !$should_be_in_group)
						{
							$ml2->remove_contact_from_group($contact_id, $ml->f('id'));
						}
						if(!$is_in_group && $should_be_in_group)
						{
							$ml2->add_contact_to_mailing_group($contact_id, $ml->f('id'));
						}						
					}					
				}
				

				echo json_encode($result);
				break;
		case 'save_company':
			$company_id = isset($_REQUEST['company_id']) ? smart_addslashes($_REQUEST['company_id']) : 0;

			$credentials = array (
				'addressbook_id','name','address','address_no','zip','city','state','country',
				'post_address','post_address_no','post_city','post_state','post_country','post_zip','phone',
				'fax','email','homepage','bank_no','vat_no','comment', 'email_allowed'
				);
					
			foreach($credentials as $key)
			{
				$company_credentials[$key] = isset($_REQUEST[$key]) ? smart_addslashes($_REQUEST[$key]) : null;
			}
			
			$addressbook = $ab->get_addressbook($company_credentials['addressbook_id']);
					
			if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_write']))
			{
				throw new AccessDeniedException();
			}
			
			if($company_id > 0)
			{
				$old_company = $ab->get_company($company_id);

				if(($old_company['addressbook_id'] != $company_credentials['addressbook_id']) && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $old_company['acl_write']))
				{				
					throw new AccessDeniedException();
				}
			}
			
					
			$result['success'] = true;
			$result['feedback'] = $feedback;

			if ($company_id < 1)
			{
				# insert
				$result['company_id'] = $company_id = $ab->add_company($company_credentials);
				
				
				if($GO_MODULES->modules['files'])
				{
					require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc');
					$fs = new files();

					$result['files_path']='companies/'.$company_id;
						
					$full_path = $GO_CONFIG->file_storage_path.$result['files_path'];
					if(!file_exists($full_path))
					{
						$fs->mkdir_recursive($full_path);
							
						$folder['user_id']=$GO_SECURITY->user_id;
						$folder['path']=addslashes($full_path);
						$folder['visible']='0';
						$folder['acl_read']=$addressbook['acl_read'];
						$folder['acl_write']=$addressbook['acl_write'];
							
						$fs->add_folder($folder);
					}
				}
				
			} else {
				# update
				$company_credentials['id'] = $company_id;

				if($old_company['addressbook_id'] != $company_credentials['addressbook_id'])
				{
					$ab->move_contacts_company($company_credentials['id'], $old_company['addressbook_id'], $company_credentials['addressbook_id']);
				}

				$ab->update_company($company_credentials);
				
			}
			
			if(isset($GO_MODULES->modules['customfields']))
			{
				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
				
				$cf->update_fields($GO_SECURITY->user_id, $company_id, 3, $_POST);
			}
			
			
			if(isset($GO_MODULES->modules['mailings']))
			{
				require($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
				$ml = new mailings();
				$ml2 = new mailings();
				
				$ml->get_authorized_mailing_groups('write', $GO_SECURITY->user_id, 0,0);
				while($ml->next_record())
				{
					$is_in_group = $ml2->company_is_in_group($company_id, $ml->f('id'));
					$should_be_in_group = isset($_POST['mailing_'.$ml->f('id')]);
					
					if($is_in_group && !$should_be_in_group)
					{
						$ml2->remove_company_from_group($company_id, $ml->f('id'));
					}
					if(!$is_in_group && $should_be_in_group)
					{
						$ml2->add_company_to_mailing_group($company_id, $ml->f('id'));
					}						
				}					
			}
		
				
			echo json_encode($result);
			break;
			
		case 'save_addressbook':
			$addressbook_id = isset($_REQUEST['addressbook_id']) ? smart_addslashes($_REQUEST['addressbook_id']) : 0;
			$user_id = isset($_REQUEST['user_id']) ? smart_addslashes($_REQUEST['user_id']) : $GO_SECURITY->user_id;
			$name = isset($_REQUEST['name']) ? smart_addslashes($_REQUEST['name']) : null;
				
			$result['success'] = true;
			$result['feedback'] = $feedback;

			if (empty($name))
			{
				throw new Exception($lang['common']['missingField']);
			} else {
				$existing_ab = $ab->get_addressbook_by_name($name);
					
				if ($addressbook_id < 1)
				{
					#insert
					if ($existing_ab)
					{
						throw new Exception($lang['common']['addressbookAlreadyExists']);
					}

					if($existing_ab)
					{
						if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $existing_ab['acl_write']))
						{
							throw new AccessDeniedException();
						}
					}

					if(!$GO_MODULES->modules['addressbook']['write_permission'])
					{
						throw new AccessDeniedException();
					}
						
					$addressbook = $ab->add_addressbook($user_id, $name);
					$result['addressbook_id'] = $addressbook['addressbook_id'];
					$result['acl_read'] = $addressbook['acl_read'];
					$result['acl_write'] = $addressbook['acl_write'];
				} else {
					#update
					if ($existing_ab && $existing_ab['id'] != $addressbook_id)
					{
						throw new Exception($lang['common']['addressbookAlreadyExists']);
					}

					if($existing_ab['user_id'] != $user_id)
					{
						$GO_SECURITY->chown_acl($existing_ab['acl_read'], $user_id);
						$GO_SECURITY->chown_acl($existing_ab['acl_write'], $user_id);
					}

					$ab->update_addressbook($addressbook_id, $user_id, $name);
					$result['addressbook_id'] = $addressbook_id;
				}
			}

			echo json_encode($result);
			break;
		case 'upload':
			$addressbook_id = isset($_REQUEST['addressbook_id']) ? smart_addslashes($_REQUEST['addressbook_id']) : 0;
			$import_filetype = isset($_REQUEST['import_filetype']) ? smart_addslashes($_REQUEST['import_filetype']) : null;
			$import_file = isset($_FILES['import_file']['tmp_name']) ? smart_addslashes($_FILES['import_file']['tmp_name']) : null;
			$seperator	= isset($_REQUEST['seperator']) ? smart_stripslashes($_REQUEST['seperator']) : ',';
			$quote	= isset($_REQUEST['quote']) ? smart_stripslashes($_REQUEST['quote']) : '"';
				
			$result['success'] = true;
			$result['feedback'] = $feedback;
				
			//go_log(LOG_DEBUG, var_export($_FILES,true));
			//go_log(LOG_DEBUG, var_export($_POST,true));
				
			$_SESSION['GO_SESSION']['addressbook']['import_file'] = $GO_CONFIG->tmpdir.uniqid(time());
				
			move_uploaded_file($import_file, $_SESSION['GO_SESSION']['addressbook']['import_file']);
				
			switch($import_filetype)
			{
				case 'vcf':
					require_once ($GO_MODULES->path."classes/vcard.class.inc");
					$vcard = new vcard();
					$success = $vcard->import($_SESSION['GO_SESSION']['addressbook']['import_file'], $GO_SECURITY->user_id, $_POST['addressbook_id']);
					break;
				case 'csv':
						
					$fp = fopen($_SESSION['GO_SESSION']['addressbook']['import_file'], 'r');

					if (!$fp || !$addressbook = $ab->get_addressbook($addressbook_id)) {
						unlink($_SESSION['GO_SESSION']['addressbook']['import_file']);
						throw new Exception($strDataError);
					} else {
						//fgets($fp, 4096);

						if (!$record = fgetcsv($fp, 4096, $seperator, $quote))
						{
							throw new Exception($contacts_import_incompatible);
						}

						fclose($fp);

						$result['list_keys'] = array();
						$result['list_keys'][]=array('id' => -1, 'name' => $lang['addressbook']['notIncluded']);
						for ($i = 0; $i < sizeof($record); $i++)
						{
							$result['list_keys'][]=array('id' => $i, 'name' => $record[$i]);
						}

					}
					break;
			}
				
			go_log(LOG_DEBUG, var_export($result,true));
			echo json_encode($result);
			break;
				case'import':
					$addressbook_id = isset($_REQUEST['addressbook_id']) ? smart_addslashes($_REQUEST['addressbook_id']) : 0;
					$seperator	= isset($_REQUEST['seperator']) ? smart_stripslashes($_REQUEST['seperator']) : ',';
					$quote	= isset($_REQUEST['quote']) ? smart_stripslashes($_REQUEST['quote']) : '"';
					$import_type = isset($_REQUEST['import_type']) ? smart_stripslashes($_REQUEST['import_type']) : '';
					$import_filetype = isset($_REQUEST['import_filetype']) ? smart_stripslashes($_REQUEST['import_filetype']) : '';
						
					$result['success'] = true;
					$result['feedback'] = $feedback;
						
					switch($import_filetype)
					{
						case 'vcf':
								
							break;
						case 'csv':
							$fp = fopen($_SESSION['GO_SESSION']['addressbook']['import_file'], "r");
								
							if (!$fp || !$addressbook = $ab->get_addressbook($addressbook_id))
							{
								unlink($_SESSION['GO_SESSION']['addressbook']['import_file']);
								throw new Exception($strDataError);
							}

							fgets($fp, 4096);
							while (!feof($fp))
							{
								$record = fgetcsv($fp, 4096, $seperator, $quote);

								if ($import_type == 'contacts')
								{
									if ((isset ($record[$_POST['first_name']]) && $record[$_POST['first_name']] != "") || (isset ($record[$_POST['last_name']]) && $record[$_POST['last_name']] != ''))
									{
										$contact['title'] = isset ($record[$_POST['title']]) ? addslashes(trim($record[$_POST['title']])) : '';
										$contact['first_name'] = isset ($record[$_POST['first_name']]) ? addslashes(trim($record[$_POST['first_name']])) : '';
										$contact['middle_name'] = isset ($record[$_POST['middle_name']]) ? addslashes(trim($record[$_POST['middle_name']])) : '';
										$contact['last_name'] = isset ($record[$_POST['last_name']]) ? addslashes(trim($record[$_POST['last_name']])) : '';
										$contact['initials'] = isset ($record[$_POST['initials']]) ? addslashes(trim($record[$_POST['initials']])) : '';
										$contact['sex'] = isset ($record[$_POST['sex']]) ? addslashes(trim($record[$_POST['sex']])) : 'M';
										$contact['birthday'] = isset ($record[$_POST['birthday']]) ? addslashes(trim($record[$_POST['birthday']])) : '';
										$contact['email'] = isset ($record[$_POST['email']]) ? String::get_email_from_string($record[$_POST['email']]) : '';
										$contact['email2'] = isset ($record[$_POST['email2']]) ? String::get_email_from_string($record[$_POST['email2']]) : '';
										$contact['email3'] = isset ($record[$_POST['email3']]) ? String::get_email_from_string($record[$_POST['email3']]) : '';
										$contact['work_phone'] = isset ($record[$_POST['work_phone']]) ? addslashes(trim($record[$_POST['work_phone']])) : '';
										$contact['home_phone'] = isset ($record[$_POST['home_phone']]) ? addslashes(trim($record[$_POST['home_phone']])) : '';
										$contact['fax'] = isset ($record[$_POST['fax']]) ? addslashes(trim($record[$_POST['fax']])) : '';
										$contact['work_fax'] = isset ($record[$_POST['work_fax']]) ? addslashes(trim($record[$_POST['work_fax']])) : '';
										$contact['cellular'] = isset ($record[$_POST['cellular']]) ? addslashes(trim($record[$_POST['cellular']])) : '';
										$contact['country'] = isset ($record[$_POST['country']]) ? addslashes(trim($record[$_POST['country']])) : '';
										$contact['state'] =  isset($record[$_POST['state']]) ? addslashes(trim($record[$_POST['state']])) : '';
										$contact['city'] = isset ($record[$_POST['city']]) ? addslashes(trim($record[$_POST['city']])) : '';
										$contact['zip'] = isset ($record[$_POST['zip']]) ? addslashes(trim($record[$_POST['zip']])) : '';
										$contact['address'] = isset ($record[$_POST['address']]) ? addslashes(trim($record[$_POST['address']])) : '';
										$contact['address_no'] = isset ($record[$_POST['address_no']]) ? addslashes(trim($record[$_POST['address_no']])) : '';
										$company_name = isset ($record[$_POST['company_name']]) ? addslashes(trim($record[$_POST['company_name']])) : '';
										$contact['department'] = isset ($record[$_POST['department']]) ? addslashes(trim($record[$_POST['department']])) : '';
										$contact['function'] = isset ($record[$_POST['function']]) ? addslashes(trim($record[$_POST['function']])) : '';
										$contact['salutation'] = isset ($record[$_POST['salutation']]) ? addslashes(trim($record[$_POST['salutation']])) : '';
										$contact['comment'] = isset ($record[$_POST['comment']]) ? addslashes(trim($record[$_POST['comment']])) : '';

										if ($company_name != '') {
											$contact['company_id'] = $ab->get_company_id_by_name($company_name, $addressbook_id);
										}else {
											$contact['company_id']=0;
										}

										$contact['addressbook_id'] = $addressbook_id;
										$ab->add_contact($contact);

									}
								} else {
									if (isset ($record[$_POST['name']]) && $record[$_POST['name']] != '')
									{
										$company['name'] = addslashes(trim($record[$_POST['name']]));

										if (!$ab->get_company_by_name($_POST['addressbook_id'], $company['name']))
										{
											$company['email'] = isset ($record[$_POST['email']]) ? String::get_email_from_string($record[$_POST['email']]) : '';
											$company['phone'] = isset ($record[$_POST['phone']]) ? addslashes(trim($record[$_POST['phone']])) : '';
											$company['fax'] = isset ($record[$_POST['fax']]) ? addslashes(trim($record[$_POST['fax']])) : '';
											$company['country'] = isset ($record[$_POST['country']]) ? addslashes(trim($record[$_POST['country']])) : '';
											$company['state'] = isset ($record[$_POST['state']]) ? addslashes(trim($record[$_POST['state']])) : '';
											$company['city'] = isset ($record[$_POST['city']]) ? addslashes(trim($record[$_POST['city']])) : '';
											$company['zip'] = isset ($record[$_POST['zip']]) ? addslashes(trim($record[$_POST['zip']])) : '';
											$company['address'] = isset ($record[$_POST['address']]) ? addslashes(trim($record[$_POST['address']])) : '';
											$company['address_no'] = isset ($record[$_POST['address_no']]) ? addslashes(trim($record[$_POST['address_no']])) : '';
											$company['post_country'] = isset ($record[$_POST['post_country']]) ? addslashes(trim($record[$_POST['post_country']])) : '';
											$company['post_state'] = isset ($record[$_POST['post_state']]) ? addslashes(trim($record[$_POST['post_state']])) : '';
											$company['post_city'] = isset ($record[$_POST['post_city']]) ? addslashes(trim($record[$_POST['post_city']])) : '';
											$company['post_zip'] = isset ($record[$_POST['post_zip']]) ? addslashes(trim($record[$_POST['post_zip']])) : '';
											$company['post_address'] = isset ($record[$_POST['post_address']]) ? addslashes(trim($record[$_POST['post_address']])) : '';
											$company['post_address_no'] = isset ($record[$_POST['post_address_no']]) ? addslashes(trim($record[$_POST['post_address_no']])) : '';
											$company['homepage'] = isset ($record[$_POST['homepage']]) ? addslashes(trim($record[$_POST['homepage']])) : '';
											$company['bank_no'] = isset ($record[$_POST['bank_no']]) ? addslashes(trim($record[$_POST['bank_no']])) : '';
											$company['vat_no'] = isset ($record[$_POST['vat_no']]) ? addslashes(trim($record[$_POST['vat_no']])) : '';
											$company['addressbook_id']  = $_POST['addressbook_id'];

											$ab->add_company($company);
										}
									}
								}
							}
							break;
					}
					echo json_encode($result);
					break;
	}
}
catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']= false;

	go_log(LOG_DEBUG, json_encode($response));

	echo json_encode($response);

}
?>