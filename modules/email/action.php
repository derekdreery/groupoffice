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
$GO_SECURITY->json_authenticate('email');


require_once ($GO_CONFIG->class_path."mail/imap.class.inc");
require_once ($GO_MODULES->modules['email']['class_path']."email.class.inc");
require_once ($GO_LANGUAGE->get_language_file('email'));
$imap = new imap();
$email = new email();

//ini_set('display_errors','off');


function add_unknown_recipient($email, $name)
{
	global $GO_SECURITY, $GO_USERS, $ab, $response, $RFC822;

	if(isset($ab))
	{
		$name_arr = String::split_name($name);

		if($name_arr['first'] == '' && $name_arr['last'] == '')
		{
			$name_arr['first'] = $email;
			$name=$email;
		}

		if (!$ab->get_contact_by_email(addslashes($email),$GO_SECURITY->user_id) && !$GO_USERS->get_authorized_user_by_email($GO_SECURITY->user_id, addslashes($email)))
		{
			$contact['name']=htmlspecialchars($RFC822->write_address($name, $email), ENT_COMPAT, 'UTF-8');
			$contact['first_name'] = addslashes($name_arr['first']);
			$contact['middle_name'] = addslashes($name_arr['middle']);
			$contact['last_name'] = addslashes($name_arr['last']);
			$contact['email'] = addslashes($email);

			$response['unknown_recipients'][]=$contact;
		}
	}
}

//we are unsuccessfull by default
$response =array('success'=>false);


try{
	switch($_REQUEST['task'])
	{

		case 'empty_folder':
			$account_id = smart_addslashes($_POST['account_id']);
			$mailbox = smart_stripslashes($_POST['mailbox']);

			if(empty($mailbox))
			{
				throw new DatabaseDeleteException();
			}

			$account = connect($account_id, $mailbox);

			$imap->sort();
			$imap->delete($imap->sort);

			$response['success']=true;
			break;

		case 'flag_messages':

			$account_id = isset ($_REQUEST['account_id']) ? $_REQUEST['account_id'] : 0;
			$mailbox = isset ($_REQUEST['mailbox']) ? smart_stripslashes($_REQUEST['mailbox']) : 'INBOX';


			$account = connect($account_id, $mailbox);

			$messages = json_decode(smart_stripslashes($_POST['messages']));
			switch($_POST['action'])
			{
				case 'mark_as_read':
					$response['success']=$imap->set_message_flag($mailbox, $messages, "\\Seen");
					break;
				case 'mark_as_unread':
					$response['success']=$imap->set_message_flag($mailbox, $messages, "\\Seen", "reset");
					break;
				case 'flag':
					$response['success']=$imap->set_message_flag($mailbox, $messages, "\\Flagged");
					break;
				case 'unflag':
					$response['success']=$imap->set_message_flag($mailbox, $messages, "\\Flagged", "reset");
					break;
			}


			//$cached_folder = $email->cache_folder_status($imap, $account_id, $mailbox);
			//$response['unseen']=$cached_folder['unseen'];

			$status = $imap->status($mailbox, SA_UNSEEN);
			if(isset($status->unseen))
			$response['unseen']=$status->unseen;



			if(!$response['success'])
			$response['feedback']=$lang['common']['saveError'];


			break;


				case 'attach_file':
					//var_dump($_FILES);
					$response['success']=true;

					$response['files']=array();

					//$response['debug']=$_FILES['attachments'];
					$dir = $GO_CONFIG->tmpdir.'attachments/';

					require_once($GO_CONFIG->class_path.'filesystem.class.inc');
					filesystem::mkdir_recursive($dir);

					for ($n = 0; $n < count($_FILES['attachments']['tmp_name']); $n ++)
					{
						if (is_uploaded_file($_FILES['attachments']['tmp_name'][$n]))
						{
							$tmp_file = $dir.smart_stripslashes($_FILES['attachments']['name'][$n]);
							move_uploaded_file($_FILES['attachments']['tmp_name'][$n], $tmp_file);

							$response['files'][] = array(
					'tmp_name'=>$tmp_file,
					'name'=>smart_stripslashes($_FILES['attachments']['name'][$n]),
					'size'=>Number::format_size($_FILES['attachments']['size'][$n]),
					'type'=>File::get_filetype_description(File::get_extension($_FILES['attachments']['name'][$n]))					
							);
						}

					}
					echo json_encode($response);
					exit();

					break;

				case 'notification':

					require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');

					$body = sprintf($lang['email']['notification_body'], smart_stripslashes($_POST['subject']), Date::get_timestamp(time()));

					$swift =& new GoSwift(
					smart_stripslashes($_POST['notification_to']),
					sprintf($lang['email']['notification_subject'],smart_stripslashes($_POST['subject'])),
					smart_addslashes($_POST['account_id']),
					3,
					$body
					);

					$response['success']=$swift->sendmail();

					break;

				case 'sendmail':

					if(empty($_POST['to']) && empty($_POST['cc']) && empty($_POST['draft']))
					{
						$response['feedback'] = $lang['email']['feedbackNoReciepent'];
					}else
					{


						try {


							if(isset($GO_MODULES->modules['addressbook']) && $GO_MODULES->modules['addressbook']['read_permission'])
							{
								require($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
								$ab = new addressbook();
								$response['unknown_recipients']=array();
							}

							require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');

							$swift =& new GoSwift(
								smart_stripslashes($_POST['to']),
								smart_stripslashes($_POST['subject']),
								smart_addslashes($_POST['account_id']),
								smart_stripslashes($_POST['priority'])
							);


							if(!empty($_POST['reply_uid']))
							$swift->set_reply_to(smart_stripslashes($_POST['reply_uid']),smart_stripslashes($_POST['reply_mailbox']));


							$RFC822 = new RFC822();



							$to_addresses = $RFC822->parse_address_list(smart_stripslashes($_POST['to']));

							foreach($to_addresses as $address)
							{
								add_unknown_recipient($address['email'], $address['personal']);
							}


							if(!empty($_POST['cc']))
							{
								$cc_addresses = $RFC822->parse_address_list(smart_stripslashes($_POST['cc']));
									
								foreach($cc_addresses as $address)
								{
									add_unknown_recipient($address['email'], $address['personal']);
									$swift->recipients->addCc($address['email'], $address['personal']);
								}
							}
							if(!empty($_POST['bcc']))
							{
								$bcc_addresses = $RFC822->parse_address_list(smart_stripslashes($_POST['bcc']));

								foreach($bcc_addresses as $address)
								{
									add_unknown_recipient($address['email'], $address['personal']);
									$swift->recipients->addBcc($address['email'], $address['personal']);
								}
							}

							if(isset($_POST['replace_personal_fields']))
							{
								require_once $GO_CONFIG->class_path.'mail/swift/lib/Swift/Plugin/Decorator.php';

								class Replacements extends Swift_Plugin_Decorator_Replacements {
									function getReplacementsFor($address) {
										return array('%email%'=>$address);
									}
								}

								//Load the plugin with the extended replacements class
								$swift->attachPlugin(new Swift_Plugin_Decorator(new Replacements()), "decorator");

							}

							if($_POST['notification']=='true')
							{
								$swift->message->requestReadReceipt($swift->account['email']);
							}

							$body = smart_stripslashes($_POST['body']);
							//process inline attachments
							$inline_attachments = json_decode(smart_stripslashes($_POST['inline_attachments']), true);
							foreach($inline_attachments as $inlineAttachment)
							{
								$tmp_name = $inlineAttachment['tmp_file'];
								if($tmp_name[0]!='/')
								{
									$tmp_name = $GO_CONFIG->file_storage_path.$tmp_name;
								}

								$img =& new Swift_Message_Image(new Swift_File($tmp_name),utf8_basename($tmp_name), mime_content_type($tmp_name));
								$src_id = $swift->message->attach($img);
								$body = str_replace($inlineAttachment['url'], $src_id, $body);
							}

							$swift->set_body($body);

							if(isset($_POST['attachments']))
							{
								$attachments = json_decode(smart_stripslashes($_POST['attachments'],true));

								foreach($attachments as $tmp_name)
								{
									if($tmp_name[0]!='/')
									{
										$tmp_name = $GO_CONFIG->file_storage_path.$tmp_name;
									}
									$file =& new Swift_File($tmp_name);
									//$file = file_get_contents($tmp_name);
									$attachment =& new Swift_Message_Attachment($file,utf8_basename($tmp_name), mime_content_type($tmp_name));
									$swift->message->attach($attachment);

								}
							}
								
							if(!empty($_POST['draft']))
							{
								if ($imap->open(
									$swift->account['host'],
									$swift->account['type'],
									$swift->account['port'],
									$swift->account['username'],
									$swift->account['password'],
									$swift->account['drafts'],
									0,
									$swift->account['use_ssl'],
									$swift->account['novalidate_cert'])) {					

									$response['success']=$imap->append_message($swift->account['drafts'], $swift->get_data(),"\\Seen");
									
									if(!$response['success'])
									{
										$response['feedback']=$imap->last_error();
									}
									
								}
							}else
							{
								$log =& Swift_LogContainer::getLog();
								$log->setLogLevel(2);

								$response['success']=$swift->sendmail(null,null, isset($_POST['replace_personal_fields']));

								if(!empty($_POST['link']))
								{
									$link_props = explode(':', $_POST['link']);
									$swift->link_to(array(
									array(
											'link_id'=>smart_addslashes($link_props[1]),
											'link_type'=>smart_addslashes($link_props[0])
									)
									)
									);
								}

								if(!$response['success'])
								{
									$response['feedback']='An error ocurred. The server returned: <br /><br />';
									$response['feedback'].=nl2br($log->dump(true));
								}
							}

						} catch (Swift_ConnectionException $e) {
							$response['feedback'] = $lang['email']['feedbackSMTPProblem'] . $e->getMessage();
						} catch (Swift_Message_MimeException $e) {
							$response['feedback'] = $lang['email']['feedbackUnexpectedError'] . $e->getMessage();
						}
					}
					break;
						
				case 'save_filter':

					$filter['id']=smart_addslashes($_POST['filter_id']);
					$filter['mark_as_read']=isset($_POST['mark_as_read']) ? '1' : '0';
					$filter['keyword']=smart_addslashes($_POST['keyword']);
					$filter['folder']=smart_addslashes($_POST['folder']);
					$filter['field']=smart_addslashes($_POST['field']);


					if($_POST['filter_id']>0)
					{
						if ($email->update_filter($filter))
						{
							$response['success']=true;
						}else
						{
							$response['feedback']=$strSaveError;
						}
					}else
					{
						$filter['account_id']=smart_addslashes($_POST['account_id']);
						if ($response['filter_id']=$email->add_filter($filter))
						{
							$response['success']=true;
						}else
						{
							$response['feedback']=$strSaveError;
						}
					}
					break;

				case 'save_account_folders':

					$up_account['id'] = $_POST['account_id'];
					$up_account['sent'] = isset($_POST['sent']) ? smart_addslashes($_POST['sent']) : '';
					$up_account['trash'] = isset($_POST['trash']) ? smart_addslashes($_POST['trash']) : '';
					$up_account['drafts'] = isset($_POST['drafts']) ? smart_addslashes($_POST['drafts']) : '';
					$up_account['spam'] = isset($_POST['spam']) ? smart_addslashes($_POST['spam']) : '';
					$up_account['spamtag']= smart_addslashes($_POST['spamtag']);

					if(!$response['feedback']=$email->_update_account($up_account))
					{
						$response['errors']=$strSaveError;
					}

					break;

				case 'add_folder':

					$account = connect(smart_addslashes($_REQUEST['account_id']));

					$delimiter = $imap->get_mailbox_delimiter();
					$parent_id=smart_addslashes($_REQUEST['folder_id']);
					if($parent_id>0)
					{
						if($folder = $email->get_folder_by_id(smart_addslashes($parent_id)))
						{
							$new_folder_name=$folder['name'].$delimiter.$imap->utf7_imap_encode(smart_stripslashes($_POST['new_folder_name']));
						}else {
							$response['feedback']=false;
							$response['errors']=$strDataError;
							echo json_encode($response);
							exit();
						}

					}else {
						$new_folder_name=$imap->utf7_imap_encode(smart_stripslashes($_POST['new_folder_name']));
					}





					if($imap->create_folder($new_folder_name, $delimiter))
					{
						if($email->add_folder($account['id'], addslashes($new_folder_name), $parent_id, 1,$delimiter))
						{
							$response['success']=true;
						}
					}
					//$imap->close();

					if(!$response['success'])
					{
						$response['feedback']=$lang['email']['feedbackCreateFolderFailed'];
					}

					break;

				case 'subscribe':

					$account = connect(smart_addslashes($_REQUEST['account_id']));
					$mailbox = $imap->utf7_imap_encode(smart_stripslashes($_POST['mailbox']));
					if($imap->subscribe($mailbox))
					{
						$response['success']=$email->subscribe($account['id'], addslashes($mailbox));
					}
					//$imap->close();

					if(!$response['success'])
					{
						$response['feedback']=$lang['email']['feedbackSubscribeFolderFailed'];
					}
					break;

				case 'unsubscribe':

					$account = connect(smart_addslashes($_REQUEST['account_id']));
					$mailbox = $imap->utf7_imap_encode(smart_stripslashes($_POST['mailbox']));
					if($imap->unsubscribe($mailbox))
					{
						$response['success']=$email->unsubscribe($account['id'], addslashes($mailbox));
					}
					//$imap->close();

					if(!$response['success'])
					{
						$response['feedback']=$lang['email']['feedbackUnsubscribeFolderFailed'];
					}
					break;

				case 'subscribtions':

					$account = connect(smart_addslashes($_REQUEST['account_id']));

					$response['success']=true;
					$newSubscriptions=json_decode(smart_stripslashes($_POST['subscribtions']), true);
					$curSubscriptions = $imap->get_subscribed('', true);

					//var_dump($newSubscriptions);
					while($newSubscribedFolder = array_shift($newSubscriptions))
					{

						$is_subscribed=in_array($newSubscribedFolder['name'], $curSubscriptions);

						$folderName=$imap->utf7_imap_encode($newSubscribedFolder['name']);

						$must_be_subscribed=$newSubscribedFolder['subscribed']!='0';

						if(!$is_subscribed && $must_be_subscribed)
						{
							//echo 'SUBSCRIBE:'.$folderName."\n";
							if($imap->subscribe($folderName))
							{
								$email->subscribe($account['id'], addslashes($folderName));
							}
						}elseif($is_subscribed && !$must_be_subscribed)
						{
							//echo 'UNSUBSCRIBE:'.$folderName."\n";
							if($imap->unsubscribe($folderName))
							{
								$email->unsubscribe($account['id'], addslashes($folderName));
							}
						}
					}
					//$imap->close();
					break;

				case 'delete_folder':

					if($folder = $email->get_folder_by_id(smart_addslashes($_REQUEST['folder_id'])))
					{
						$account = connect($folder['account_id']);



						if ($imap->delete_folder($folder['name'], $account['mbroot']))
						{
							$response['success']=$email->delete_folder($account['id'], addslashes($folder['name']));
						}else {
							$response['feedback']=$ml_delete_folder_error;
						}
						//$imap->close();
					}else {
						$response['feedback']=$strDataError;
					}
					break;
				case 'rename_folder':

					if($folder = $email->get_folder_by_id(smart_addslashes($_REQUEST['folder_id'])))
					{
						$pos = strrpos($folder['name'], $folder['delimiter']);
						if ($pos && $folder['delimiter'] != '')
						{
							$location = substr($folder['name'],0,$pos+1);

						}else
						{
							$location = '';
						}

						$new_folder = $location.$imap->utf7_imap_encode(smart_stripslashes($_POST['new_name']));

						connect($folder['account_id']);

						//echo $folder['name'].' -> '.$new_folder;
						if ($imap->rename_folder($folder['name'], $new_folder))
						{
							$response['success']=$email->rename_folder($folder['account_id'], addslashes($folder['name']), addslashes($new_folder));
						}else {
							$response['feedback']=$strSaveError;
						}
						//$imap->close();
					}else {
						$response['feedback']=$strDataError;
					}
					break;

				case 'syncfolders':

					$account = $email->get_account($_REQUEST['account_id']);
					$email->synchronize_folders($account);
					$response['feedback']=true;
					break;

				case 'save_accounts_sort_order':

					$sort_order = json_decode(smart_stripslashes($_POST['sort_order']), true);

					foreach($sort_order as $account_id=>$sort_index)
					{
						$account['id']=$account_id;
						$account['standard']=$sort_index;
						$email->_update_account($account);
					}
					$success=true;
					break;

				case 'save_account_properties':



					$account['mbroot'] = isset($_POST['mbroot']) ? addslashes($imap->utf7_imap_encode(smart_stripslashes($_POST['mbroot']))) : '';
					if ($_POST['name'] == "" ||
					$_POST['email'] == "" ||
					$_POST['port'] == "" ||
					$_POST['username'] == "" ||
					$_POST['password'] == "" ||
					$_POST['host'] == "" ||
					$_POST['smtp_host'] == "" ||
					$_POST['smtp_port'] == "")
					{
						$response['feedback'] = $lang['common']['missingField'];

					}else
					{
						$account['id']=isset($_POST['account_id']) ? smart_addslashes($_POST['account_id']) : 0;
						$account['mbroot'] = isset($_POST['mbroot']) ? smart_addslashes($_POST['mbroot']) : '';
						$account['use_ssl'] = isset($_REQUEST['use_ssl'])  ? $_REQUEST['use_ssl'] : '0';
						$account['novalidate_cert'] = isset($_REQUEST['novalidate_cert']) ? $_REQUEST['novalidate_cert'] : '0';
						$account['examine_headers'] = isset($_POST['examine_headers']) ? '1' : '0';
						$account['type']=smart_addslashes($_POST['type']);
						$account['host']=smart_addslashes($_POST['host']);
						$account['port']=smart_addslashes($_POST['port']);
						$account['username']=smart_addslashes($_POST['username']);
						$account['password']=smart_addslashes($_POST['password']);
						$account['name']=smart_addslashes($_POST['name']);
						$account['email']=smart_addslashes($_POST['email']);
						//$account['signature']=smart_addslashes($_POST['signature']);

						$account['smtp_host']=smart_addslashes($_POST['smtp_host']);
						$account['smtp_port']=smart_addslashes($_POST['smtp_port']);
						$account['smtp_encryption']=smart_addslashes($_POST['smtp_encryption']);
						$account['smtp_username']=smart_addslashes($_POST['smtp_username']);
						$account['smtp_password']=smart_addslashes($_POST['smtp_password']);



						if ($account['id'] > 0)
						{
							if(isset($_REQUEST['user_id']))
							{
								$account['user_id']=smart_addslashes($_REQUEST['user_id']);
							}

							$account['sent']=smart_addslashes($_POST['sent']);
							$account['drafts']=smart_addslashes($_POST['drafts']);
							$account['trash']=smart_addslashes($_POST['trash']);

							if(!$email->update_account($account))
							{
								$response['feedback'] = $ml_connect_failed.' '.
								$_POST['host'].' '.$ml_at_port.': '.$_POST['port'].' '.$email->last_error;
							}else
							{
								$response['success']=true;
							}


							if(isset($GO_MODULES->modules['serverclient']))
							{
								require_once($GO_MODULES->modules['serverclient']['class_path'].'serverclient.class.inc.php');
								$sc = new serverclient();

								foreach($sc->domains as $domain)
								{

									//go_log(LOG_DEBUG, $account['username'].' -> '.$domain);
									if(strpos($account['username'], '@'.$domain))
									{
										$sc->login();

										$params=array(
										//'sid'=>$sc->sid,
									'task'=>'serverclient_set_vacation',
									'username'=>$account['username'],
									'password'=>$account['password'],
									'vacation_active'=>isset($_POST['vacation_active']) ? '1' : '0',
									'vacation_subject'=>smart_stripslashes($_POST['vacation_subject']),
									'vacation_body'=>smart_stripslashes($_POST['vacation_body'])													
										);

										//go_log(LOG_DEBUG, var_export($params, true));

										$server_response = $sc->send_request($GO_CONFIG->serverclient_server_url.'modules/postfixadmin/action.php', $params);

										$server_response = json_decode($server_response, true);

										if(!$server_response['success'])
										{
											throw new Exception($server_response['feedback']);
										}
										break;
									}

								}
							}

						}else
						{
							$account['user_id']=isset($_REQUEST['user_id']) ? smart_stripslashes($_REQUEST['user_id']) : $GO_SECURITY->user_id;


							$account['id'] = $email->add_account($account);
							if(!$account['id'])
							{
								$response['feedback'] = $ml_connect_failed.' '.
								$_POST['host'].' '.$ml_at_port.': '.$_POST['port'].' '.$email->last_error;
							}else
							{
								$account = $email->get_account($account['id']);
								$email->synchronize_folders($account);

								$response['success']=true;
								$response['account_id']=$account['id'];
							}
						}
					}
					break;
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
if(defined('IMAP_CONNECTED'))
{
	$imap->close();
}
echo json_encode($response);