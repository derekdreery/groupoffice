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
require_once($GO_LANGUAGE->get_language_file('addressbook'));
require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc.php');
$ab = new addressbook;

$feedback = null;

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : null;

try
{
	switch($task)
	{
		case 'save_contact':
			$contact_id = isset($_REQUEST['contact_id']) ? ($_REQUEST['contact_id']) : 0;

			$credentials = array (
				'first_name','middle_name','last_name','title','initials','sex','email',
				'email2','email3','home_phone','fax','cellular','comment','address','address_no',
				'zip','city','state','country','company','department','function','work_phone',
				'work_fax','addressbook_id','salutation'
				);

				$contact_credentials['email_allowed']=isset($_POST['email_allowed']) ? '1' : '0';
				foreach($credentials as $key)
				{
					$contact_credentials[$key] = isset($_REQUEST[$key]) ? $_REQUEST[$key] : '';
				}

				$contact_credentials['company_id'] = !empty($_REQUEST['company_id']) ? $_REQUEST['company_id'] : 0;


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

				if(!empty($_POST['birthday']))
				$contact_credentials['birthday'] = Date::to_db_date($_POST['birthday'], false);
				


				unset($contact_credentials['company']);
				if ($contact_id < 1)
				{
					$contact_id = $ab->add_contact($contact_credentials, $addressbook);

					if(!$contact_id)
					{
						$result['feedback'] = $lang['comon']['saveError'];
						$result['success'] = false;
					} else {
						$result['contact_id'] =  $contact_id;
					}
						
		
					$insert=true;
					
				} else {						
					$contact_credentials['id'] = $contact_id;
										
					if(!$ab->update_contact($contact_credentials, $addressbook, $old_contact))
					{
						$result['feedback'] = $lang['comon']['saveError'];
						$result['success'] = false;
					}
					
					$insert=false;
				}

				if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission'])
				{
					require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();
						
					$cf->update_fields($GO_SECURITY->user_id, $contact_id, 2, $_POST, $insert);
				}
			

				if($GO_MODULES->has_module('mailings'))
				{
					require_once($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
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
				
				$GO_EVENTS->fire_event('save_contact', array($contact_credentials));


				echo json_encode($result);
				break;
		case 'save_company':
			$company_id = isset($_REQUEST['company_id']) ? ($_REQUEST['company_id']) : 0;

			$credentials = array (
				'addressbook_id','name','address','address_no','zip','city','state','country',
				'post_address','post_address_no','post_city','post_state','post_country','post_zip','phone',
				'fax','email','homepage','bank_no','vat_no','comment'
				);
					
				$company_credentials['email_allowed']=isset($_POST['email_allowed']) ? '1' : '0';
				foreach($credentials as $key)
				{
					$company_credentials[$key] = isset($_REQUEST[$key]) ? ($_REQUEST[$key]) : null;
				}
				
				if(!empty($company_credentials['homepage']) && !strpos($company_credentials['homepage'],'://'))
				{
					$company_credentials['homepage']='http://'.$company_credentials['homepage'];
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
					$result['company_id'] = $company_id = $ab->add_company($company_credentials, $addressbook);
					$insert=true;

				} else {
					# update
					$company_credentials['id'] = $company_id;
					
					$ab->update_company($company_credentials, $addressbook, $old_company);
					$insert=false;

				}
					
				if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission'])
				{
					require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();

					$cf->update_fields($GO_SECURITY->user_id, $company_id, 3, $_POST, $insert);
				}
					
					
				if($GO_MODULES->has_module('mailings'))
				{
					require_once($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
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
				
				$GO_EVENTS->fire_event('save_company', array($company_credentials));


				echo json_encode($result);
				break;
					
		case 'save_addressbook':
			$addressbook_id = isset($_REQUEST['addressbook_id']) ? ($_REQUEST['addressbook_id']) : 0;
			
			$name = isset($_REQUEST['name']) ? ($_REQUEST['name']) : null;

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
					
					$user_id = isset($_REQUEST['user_id']) ? ($_REQUEST['user_id']) : $GO_SECURITY->user_id;

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
					
					$addressbook['id']=$addressbook_id;
					
					if(isset($_REQUEST['user_id']))
						$addressbook['user_id']=$user_id;
						
					$addressbook['name']=$name;
					$ab->update_addressbook($addressbook);					
				}
			}

			echo json_encode($result);
			break;
		case 'upload':
			$addressbook_id = isset($_REQUEST['addressbook_id']) ? ($_REQUEST['addressbook_id']) : 0;
			$import_filetype = isset($_REQUEST['import_filetype']) ? ($_REQUEST['import_filetype']) : null;
			$import_file = isset($_FILES['import_file']['tmp_name']) ? ($_FILES['import_file']['tmp_name']) : null;
			$separator	= isset($_REQUEST['separator']) ? ($_REQUEST['separator']) : ',';
			$quote	= isset($_REQUEST['quote']) ? ($_REQUEST['quote']) : '"';

			$result['success'] = true;
			//$result['feedback'] = $feedback;

			//go_log(LOG_DEBUG, var_export($_FILES,true));
			//go_log(LOG_DEBUG, var_export($_POST,true));

			$_SESSION['GO_SESSION']['addressbook']['import_file'] = $GO_CONFIG->tmpdir.uniqid(time());

			move_uploaded_file($import_file, $_SESSION['GO_SESSION']['addressbook']['import_file']);

			switch($import_filetype)
			{
				case 'vcf':
					require_once ($GO_MODULES->path."classes/vcard.class.inc.php");
					$vcard = new vcard();
					$result['success'] = $vcard->import($_SESSION['GO_SESSION']['addressbook']['import_file'], $GO_SECURITY->user_id, ($_POST['addressbook_id']));
					break;
				case 'csv':

					$fp = fopen($_SESSION['GO_SESSION']['addressbook']['import_file'], 'r');

					if (!$fp || !$addressbook = $ab->get_addressbook($addressbook_id)) {
						unlink($_SESSION['GO_SESSION']['addressbook']['import_file']);
						throw new Exception($lang['comon']['selectError']);
					} else {
						//fgets($fp, 4096);

						if (!$record = fgetcsv($fp, 4096, $separator, $quote))
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
				
				

			echo json_encode($result);
			break;
				case'import':
					$addressbook_id = isset($_REQUEST['addressbook_id']) ? ($_REQUEST['addressbook_id']) : 0;
					$separator	= isset($_REQUEST['separator']) ? ($_REQUEST['separator']) : ',';
					$quote	= isset($_REQUEST['quote']) ? ($_REQUEST['quote']) : '"';
					$import_type = isset($_REQUEST['import_type']) ? ($_REQUEST['import_type']) : '';
					$import_filetype = isset($_REQUEST['import_filetype']) ? ($_REQUEST['import_filetype']) : '';

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
								throw new Exception($lang['comon']['selectError']);
							}

							fgets($fp, 4096);
							while (!feof($fp))
							{
								$record = fgetcsv($fp, 4096, $separator, $quote);

								$new_id=0;

								if ($import_type == 'contacts')
								{
									if ((isset ($record[$_POST['first_name']]) && $record[$_POST['first_name']] != "") || (isset ($record[$_POST['last_name']]) && $record[$_POST['last_name']] != ''))
									{
										$contact['title'] = isset ($record[$_POST['title']]) ? trim($record[$_POST['title']]) : '';
										$contact['first_name'] = isset ($record[$_POST['first_name']]) ? trim($record[$_POST['first_name']]) : '';
										$contact['middle_name'] = isset ($record[$_POST['middle_name']]) ? trim($record[$_POST['middle_name']]) : '';
										$contact['last_name'] = isset ($record[$_POST['last_name']]) ? trim($record[$_POST['last_name']]) : '';
										$contact['initials'] = isset ($record[$_POST['initials']]) ? trim($record[$_POST['initials']]) : '';
										$contact['sex'] = isset ($record[$_POST['sex']]) ? trim($record[$_POST['sex']]) : 'M';
										$contact['birthday'] = isset ($record[$_POST['birthday']]) ? trim($record[$_POST['birthday']]) : '';
										$contact['email'] = isset ($record[$_POST['email']]) ? String::get_email_from_string($record[$_POST['email']]) : '';
										$contact['email2'] = isset ($record[$_POST['email2']]) ? String::get_email_from_string($record[$_POST['email2']]) : '';
										$contact['email3'] = isset ($record[$_POST['email3']]) ? String::get_email_from_string($record[$_POST['email3']]) : '';
										$contact['work_phone'] = isset ($record[$_POST['work_phone']]) ? trim($record[$_POST['work_phone']]) : '';
										$contact['home_phone'] = isset ($record[$_POST['home_phone']]) ? trim($record[$_POST['home_phone']]) : '';
										$contact['fax'] = isset ($record[$_POST['fax']]) ? trim($record[$_POST['fax']]) : '';
										$contact['work_fax'] = isset ($record[$_POST['work_fax']]) ? trim($record[$_POST['work_fax']]) : '';
										$contact['cellular'] = isset ($record[$_POST['cellular']]) ? trim($record[$_POST['cellular']]) : '';
										$contact['country'] = isset ($record[$_POST['country']]) ? trim($record[$_POST['country']]) : '';
										$contact['state'] =  isset($record[$_POST['state']]) ? trim($record[$_POST['state']]) : '';
										$contact['city'] = isset ($record[$_POST['city']]) ? trim($record[$_POST['city']]) : '';
										$contact['zip'] = isset ($record[$_POST['zip']]) ? trim($record[$_POST['zip']]) : '';
										$contact['address'] = isset ($record[$_POST['address']]) ? trim($record[$_POST['address']]) : '';
										$contact['address_no'] = isset ($record[$_POST['address_no']]) ? trim($record[$_POST['address_no']]) : '';
										$company_name = isset ($record[$_POST['company_name']]) ? trim($record[$_POST['company_name']]) : '';
										$contact['department'] = isset ($record[$_POST['department']]) ? trim($record[$_POST['department']]) : '';
										$contact['function'] = isset ($record[$_POST['function']]) ? trim($record[$_POST['function']]) : '';
										$contact['salutation'] = isset ($record[$_POST['salutation']]) ? trim($record[$_POST['salutation']]) : '';
										$contact['comment'] = isset ($record[$_POST['comment']]) ? trim($record[$_POST['comment']]) : '';

										if ($company_name != '') {
											$contact['company_id'] = $ab->get_company_id_by_name($company_name, $addressbook_id);
												
											if(!$contact['company_id'])
											{
												$company['addressbook_id']=$addressbook_id;
												$company['name']=$company_name;

												$contact['company_id']=$ab->add_company($company);
											}
										}else {
											$contact['company_id']=0;
										}

										$contact['addressbook_id'] = $addressbook_id;
										$new_id=$ab->add_contact($contact);
										$new_type=2;
									}
								} else {
									if (isset ($record[$_POST['name']]) && $record[$_POST['name']] != '')
									{
										$company['name'] = trim($record[$_POST['name']]);

										if (!$ab->get_company_by_name($_POST['addressbook_id'], $company['name']))
										{
											$company['email'] = isset ($record[$_POST['email']]) ? String::get_email_from_string($record[$_POST['email']]) : '';
											$company['phone'] = isset ($record[$_POST['phone']]) ? trim($record[$_POST['phone']]) : '';
											$company['fax'] = isset ($record[$_POST['fax']]) ? trim($record[$_POST['fax']]) : '';
											$company['country'] = isset ($record[$_POST['country']]) ? trim($record[$_POST['country']]) : '';
											$company['state'] = isset ($record[$_POST['state']]) ? trim($record[$_POST['state']]) : '';
											$company['city'] = isset ($record[$_POST['city']]) ? trim($record[$_POST['city']]) : '';
											$company['zip'] = isset ($record[$_POST['zip']]) ? trim($record[$_POST['zip']]) : '';
											$company['address'] = isset ($record[$_POST['address']]) ? trim($record[$_POST['address']]) : '';
											$company['address_no'] = isset ($record[$_POST['address_no']]) ? trim($record[$_POST['address_no']]) : '';
											$company['post_country'] = isset ($record[$_POST['post_country']]) ? trim($record[$_POST['post_country']]) : '';
											$company['post_state'] = isset ($record[$_POST['post_state']]) ? trim($record[$_POST['post_state']]) : '';
											$company['post_city'] = isset ($record[$_POST['post_city']]) ? trim($record[$_POST['post_city']]) : '';
											$company['post_zip'] = isset ($record[$_POST['post_zip']]) ? trim($record[$_POST['post_zip']]) : '';
											$company['post_address'] = isset ($record[$_POST['post_address']]) ? trim($record[$_POST['post_address']]) : '';
											$company['post_address_no'] = isset ($record[$_POST['post_address_no']]) ? trim($record[$_POST['post_address_no']]) : '';
											$company['homepage'] = isset ($record[$_POST['homepage']]) ? trim($record[$_POST['homepage']]) : '';
											$company['bank_no'] = isset ($record[$_POST['bank_no']]) ? trim($record[$_POST['bank_no']]) : '';
											$company['vat_no'] = isset ($record[$_POST['vat_no']]) ? trim($record[$_POST['vat_no']]) : '';
											$company['addressbook_id']  = $_POST['addressbook_id'];

											$new_id=$ab->add_company($company);
											$new_type=3;
										}
									}
								}

								if($new_id>0)
								{
									if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission'])
									{
										require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
										$cf = new customfields();
										$customfields = $cf->get_authorized_fields($GO_SECURITY->user_id, $new_type);
											
										$cf_record=array('link_id'=>$new_id);
										foreach($customfields as $field)
										{
											if(isset($_POST[$field['name']]))
												$cf_record[$field['name']]=$record[$_POST[$field['name']]];
										}
										$cf->insert_row('cf_'.$new_type,$cf_record);
									}
								}
							}
							break;
					}
					echo json_encode($result);
					break;
					
					
				case 'save_contacts':							
					
					$contacts = json_decode(($_POST['items']));
					$abook_id = isset($_REQUEST['book_id']) ? ($_REQUEST['book_id']) : 0;
					
					$addressbook = $ab->get_addressbook($abook_id);
					if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_write']))
					{
						throw new AccessDeniedException();
					}

					$result['success'] = true;
					$result['feedback'] = $feedback;
				
					for($i=0; $i<count($contacts); $i++)
					{
						$contact['id'] = $contacts[$i];
						if($contact['id'] > 0)
						{
							$old_contact = $ab->get_contact($contact['id']);
							if(($old_contact['addressbook_id'] != $abook_id) && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $old_contact['acl_write']))
							{
								throw new AccessDeniedException();
							}
							$contact['addressbook_id'] = $abook_id;
							$contact['company_id'] = $old_contact['company_id'];
							$contact['last_name'] = $old_contact['last_name'];
							
							if(!$ab->update_contact($contact, $addressbook))
							{
								$result['feedback'] = $lang['common']['saveError'];
								$result['success'] = false;
							}							
						}						
					}					
					echo json_encode($result);
					break;
					
				case 'save_companies':							
					
					$companies = json_decode(($_POST['items']));
					$abook_id = isset($_REQUEST['book_id']) ? ($_REQUEST['book_id']) : 0;
				
					$addressbook = $ab->get_addressbook($abook_id);
					if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_write']))
					{
						throw new AccessDeniedException();
					}

					$result['success'] = true;
					$result['feedback'] = $feedback;
				
					for($i=0; $i<count($companies); $i++)
					{
						$company['id'] = $companies[$i];
						if($company['id'] > 0)
						{
							$old_company = $ab->get_company($company_id);							
							if(($old_company['addressbook_id'] != $abook_id) && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $old_company['acl_write']))
							{
								throw new AccessDeniedException();
							}
						
							$company['addressbook_id'] = $abook_id;
							$company['last_name'] = $old_company['last_name'];							
							if(!$ab->update_company($company, $addressbook))
							{
								$result['feedback'] = $lang['common']['saveError'];
								$result['success'] = false;
							}							
						}						
					}					
					echo json_encode($result);
					break;
				
	}
}
catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']= false;

	echo json_encode($response);

}
?>