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


require_once ($GO_MODULES->modules['email']['class_path']."cached_imap.class.inc.php");
require_once ($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
require_once ($GO_LANGUAGE->get_language_file('email'));
$imap = new cached_imap();
$email = new email();

//ini_set('display_errors','off');


function add_unknown_recipient($email, $name) {
	global $GO_SECURITY, $GO_USERS, $ab, $response, $RFC822;

	if(isset($ab)) {
		$name_arr = String::split_name($name);

		if($name_arr['first'] == '' && $name_arr['last'] == '') {
			$name_arr['first'] = $email;
			$name=$email;
		}

		if (!$ab->get_contact_by_email($email,$GO_SECURITY->user_id) && !$ab->get_company_by_email($email,$GO_SECURITY->user_id) && !$GO_USERS->get_authorized_user_by_email($GO_SECURITY->user_id, $email)) {
			$contact['name']=htmlspecialchars($RFC822->write_address($name, $email), ENT_COMPAT, 'UTF-8');
			$contact['first_name'] = $name_arr['first'];
			$contact['middle_name'] = $name_arr['middle'];
			$contact['last_name'] = $name_arr['last'];
			$contact['email'] = $email;

			$response['unknown_recipients'][]=$contact;
		}
	}
}

//we are unsuccessfull by default
$response =array('success'=>false);


try {
	switch($_REQUEST['task']) {
		case 'move':

			$start_time = time();

			if(isset($_POST['messages'])) {
				$_SESSION['GO_SESSION']['email_move_messages'] = json_decode($_POST['messages']);
				$_SESSION['GO_SESSION']['email_move_messages_count']=count($_SESSION['GO_SESSION']['email_move_messages']);
			}

			//move to another imap account
			$imap2 = new cached_imap();
			$from_account = $imap->open_account($_POST['from_account_id'], $_POST['from_mailbox']);

			
			$to_account = $email->get_account($_POST['to_account_id']);

			if(!$to_account)
				throw new DatabaseSelectException();

			$imap2->open($to_account, $_POST['to_mailbox']);

			$delete_messages =array();
			while($uid=array_shift($_SESSION['GO_SESSION']['email_move_messages'])) {
				$source = $imap->get_message_part($uid);

				$header = $imap->get_message_header($uid);

				$flags = '\Seen';
				if(!empty($header['flagged'])) {
					$flags .= ' \Flagged';
				}
				if(!empty($header['answered'])) {
					$flags .= ' \Answered';
				}
				if(!empty($header['forwarded'])) {
					$flags .= ' $Forwarded';				}

				if(!$imap2->append_message($_POST['to_mailbox'], $source, $flags)) {
					$imap2->disconnect();
					throw new Exception('Could not move message');
				}

				$delete_messages[]=$uid;
				
				$left = count($_SESSION['GO_SESSION']['email_move_messages']);

				if($left && $start_time+10<time()) {

					$done = $_SESSION['GO_SESSION']['email_move_messages_count']-$left;

					$response['progress']=number_format($done/$_SESSION['GO_SESSION']['email_move_messages_count'],2);
					$response['continue']=true;
					break;
				}
			}
			$imap->delete($delete_messages);

			$imap2->disconnect();
			$imap->disconnect();
			
			$response['success']=true;

			break;

		case 'save_attachment':

			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();

			$account = $imap->open_account($_POST['account_id'], $_POST['mailbox']);
			$data = $imap->get_message_part_decoded($_REQUEST['uid'], $_REQUEST['imap_id'], $_REQUEST['encoding'], false);

			$imap->disconnect();

			if(empty($data)) {
				throw new Exception('Could not fetch message from IMAP server');
			}
			$folder = $files->get_folder($_POST['folder_id']);
			$path = $files->build_path($folder);
			if(!$path) {
				throw new FileNotFoundException();
			}

			$path.='/'.$_POST['filename'];

			if(!file_put_contents($GO_CONFIG->file_storage_path.$path, $data)) {
				throw new Exception('Could not create file');
			}
			$files->import_file($GO_CONFIG->file_storage_path.$path,$folder['id']);

			$response['success']=true;
			break;

		case 'check_mail':
			$email2 = new email();
			$count = $email2->get_accounts($GO_SECURITY->user_id);
			$response['unseen']=array();
			while($email2->next_record()) {
				$account = $imap->open_account($email2->f('id'), 'INBOX', false);
				if($account) {
					$inbox = $email->get_folder($email2->f('id'), 'INBOX');

					$imap->select_mailbox('INBOX');

					$unseen =  $imap->get_unseen();

					$response['status'][$inbox['id']]['unseen'] = $unseen['count'];
					$response['status'][$inbox['id']]['messages'] = $imap->selected_mailbox['messages'];

					$imap->disconnect();
				}else {
					$imap->clear_errors();
				}
			}
			$response['success']=true;
			break;

		case 'empty_folder':
			$account_id = ($_POST['account_id']);
			$mailbox = ($_POST['mailbox']);

			if(empty($mailbox)) {
				throw new DatabaseDeleteException();
			}

			$account = $imap->open_account($account_id, $mailbox);
			$sort = $imap->sort_mailbox();
			$imap->delete($sort);
			
			$response['success']=true;
			break;

		case 'flag_messages':

			$account_id = isset ($_REQUEST['account_id']) ? $_REQUEST['account_id'] : 0;
			$mailbox = isset ($_REQUEST['mailbox']) ? ($_REQUEST['mailbox']) : 'INBOX';


			$account = $imap->open_account($account_id, $mailbox);

			$messages = json_decode($_POST['messages']);
			switch($_POST['action']) {
				case 'mark_as_read':
					$response['success']=$imap->set_message_flag($messages, "\Seen");
					$imap->set_unseen_cache($messages, false);
					break;
				case 'mark_as_unread':
					$response['success']=$imap->set_message_flag($messages, "\Seen", true);
					$imap->set_unseen_cache($messages, true);
					break;
				case 'flag':
					$response['success']=$imap->set_message_flag($messages, "\Flagged");
					$imap->set_flagged_cache($messages, true);
					break;
				case 'unflag':
					$response['success']=$imap->set_message_flag($messages, "\Flagged", true);
					$imap->set_flagged_cache($messages, false);
					break;
			}


			//$cached_folder = $email->cache_folder_status($imap, $account_id, $mailbox);
			//$response['unseen']=$cached_folder['unseen'];

			$unseen = $imap->get_unseen();
			$response['unseen']=$unseen['count'];



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

			for ($n = 0; $n < count($_FILES['attachments']['tmp_name']); $n ++) {
				if (is_uploaded_file($_FILES['attachments']['tmp_name'][$n])) {
					$tmp_file = $dir.File::strip_invalid_chars($_FILES['attachments']['name'][$n]);
					move_uploaded_file($_FILES['attachments']['tmp_name'][$n], $tmp_file);

					$response['files'][] = array(
									'tmp_name'=>$tmp_file,
									'name'=>utf8_basename($tmp_file),
									'size'=>$_FILES['attachments']['size'][$n],
									'type'=>File::get_filetype_description(File::get_extension($_FILES['attachments']['name'][$n]))
					);
				}
			}
			echo json_encode($response);
			exit();

			break;

		case 'notification':

			require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');


			$aliases = array();
			$email->get_aliases($_POST['account_id'], true);
			while($alias=$email->next_record()) {
				$aliases[$alias['email']]=$alias['id'];
			}

			$RFC822 = new RFC822();
			$alias_id=0;
			$to_addresses = $RFC822->parse_address_list($_POST['message_to']);
			foreach($to_addresses as $address) {
				if(isset($aliases[$address['email']])) {
					$alias_id=$aliases[$address['email']];
					break;
				}
			}

			$body = sprintf($lang['email']['notification_body'], $_POST['subject'], Date::get_timestamp(time()));

			$swift = new GoSwift(
							$_POST['notification_to'],
							sprintf($lang['email']['notification_subject'],$_POST['subject']),
							$_POST['account_id'],
							$alias_id,
							3,
							$body
			);

			$response['success']=$swift->sendmail();

			break;

		case 'sendmail':

			$draft = isset($_POST['draft']) && $_POST['draft']=='true';

			if(empty($_POST['to']) && empty($_POST['cc']) && empty($_POST['bcc']) && !$draft) {
				$response['feedback'] = $lang['email']['feedbackNoReciepent'];
			}else {
				try {
					if(isset($GO_MODULES->modules['addressbook']) && $GO_MODULES->modules['addressbook']['read_permission']) {
						require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc.php');
						$ab = new addressbook();
						$response['unknown_recipients']=array();
					}

					require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');

					$swift = new GoSwift(
									$_POST['to'],
									$_POST['subject'],
									0,
									$_POST['alias_id'],
									$_POST['priority']
					);

					if(!empty($_POST['reply_uid']))
						$swift->set_reply_to($_POST['reply_uid'],$_POST['reply_mailbox']);

					if(!empty($_POST['forward_uid']))
						$swift->set_forward_uid($_POST['forward_uid'],$_POST['forward_mailbox']);


					$RFC822 = new RFC822();
					$to_addresses = $RFC822->parse_address_list($_POST['to']);

					//used for gpg encryption
					$all_recipients = array();

					foreach($to_addresses as $address) {
						$all_recipients[]=$address['email'];
						add_unknown_recipient($address['email'], $address['personal']);
					}


					if(!empty($_POST['cc'])) {
						$cc_addresses = $RFC822->parse_address_list($_POST['cc']);

						$swift_addresses=array();
						foreach($cc_addresses as $address) {
							$all_recipients[]=$address['email'];

							add_unknown_recipient($address['email'], $address['personal']);
							$swift_addresses[$address['email']]=$address['personal'];
						}
						$swift->message->setCc($swift_addresses);
					}


					if(!empty($_POST['bcc'])) {
						$bcc_addresses = $RFC822->parse_address_list($_POST['bcc']);
						$swift_addresses=array();
						foreach($bcc_addresses as $address) {
							$all_recipients[]=$address['email'];
							add_unknown_recipient($address['email'], $address['personal']);
							$swift_addresses[$address['email']]=$address['personal'];
						}
						$swift->message->setBcc($swift_addresses);
					}

					/*if(isset($_POST['replace_personal_fields']))
							 {
								require_once $GO_CONFIG->class_path.'mail/swift/lib/classes/Swift/plugins/DecoratorPlugin.php';

								class Replacements extends Swift_Plugin_Decorator_Replacements {
								function getReplacementsFor($address) {
								return array('%email%'=>$address);
								}
								}

								//Load the plugin with the extended replacements class
								$swift->attachPlugin(new Swift_Plugins_DecoratorPlugin(new Replacements()), "decorator");

								}*/

					if($_POST['notification']=='true') {
						$swift->message->setReadReceiptTo($swift->account['email']);
					}

					$body = $_POST['content_type']=='html' ? $_POST['body'] : $_POST['textbody'];

					if($_POST['content_type']=='html') {
						//process inline attachments
						$inline_attachments = json_decode($_POST['inline_attachments'], true);
						foreach($inline_attachments as $inlineAttachment) {
							$tmp_name = $inlineAttachment['tmp_file'];
							if(is_numeric($tmp_name)) {
								require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
								$files = new files();

								$file = $files->get_file($tmp_name);
								$folder = $files->get_folder($file['folder_id']);
								if(!$file || !$folder) {
									throw new FileNotFoundException();
								}
								$tmp_name = $GO_CONFIG->file_storage_path.$files->build_path($folder).'/'.$file['name'];
							}

							if(!file_exists($tmp_name)) {
								throw new FileNotFoundException();
							}

							$img = Swift_EmbeddedFile::fromPath($tmp_name);
							$img->setContentType(File::get_mime($tmp_name));
							$src_id = $swift->message->embed($img);

							//Browsers reformat URL's so a pattern match
							//$body = str_replace($inlineAttachment['url'], $src_id, $body);
							$just_filename = utf8_basename($inlineAttachment['url']);
							$body = preg_replace('/="[^"]*'.preg_quote($just_filename).'"/', '="'.$src_id.'"', $body);
						}
					}

					if(!empty($_POST['encrypt']) && !$draft) {
						require_once ($GO_MODULES->modules['gnupg']['class_path'].'gnupg.class.inc.php');
						$gnupg = new gnupg();

						//$htmlToText = new Html2Text ($body);
						//$textbody = $htmlToText->get_text();

						//$body = $gnupg->encode($body, $all_recipients, $swift->account['email']);
						$body = $gnupg->encode($body, $all_recipients, $swift->account['email']);

						//go_debug($body);

						$swift->message->setMaxLineLength(1000);
						$swift->message->setBody($body, 'text/plain');
						$swift->message->setEncoder(new Swift_Mime_ContentEncoder_RawContentEncoder('8bit'));

						/*$textpart = Swift_MimePart::newInstance($textbody, 'text/plain', 'UTF-8');
								 $textpart->setEncoder(new Swift_Mime_ContentEncoder_PlainContentEncoder('8bit'));
								 $textpart->setMaxLineLength(1000);
								 $swift->message->attach($textpart);*/
					}else {
						$swift->set_body($body, $_POST['content_type']);
					}

					if($GO_MODULES->has_module('files')) {
						require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
						$files = new files();
					}

					if(isset($_POST['attachments'])) {
						$attachments = json_decode($_POST['attachments'],true);

						foreach($attachments as $tmp_name) {
							if(is_numeric($tmp_name)) {
								$file = $files->get_file($tmp_name);
								$folder = $files->get_folder($file['folder_id']);
								if(!$file || !$folder) {
									throw new FileNotFoundException();
								}
								$tmp_name = $GO_CONFIG->file_storage_path.$files->build_path($folder).'/'.$file['name'];
							}

							if(!file_exists($tmp_name)) {
								throw new FileNotFoundException();
							}

							if(!empty($_POST['encrypt']) && $draft) {
								$encoded = $gnupg->encode_file($tmp_name, $all_recipients, $swift->account['email']);

								$attachment = Swift_Attachment::fromPath($encoded,'application/pgp-encoded');
							}else {
								$attachment = Swift_Attachment::fromPath($tmp_name,File::get_mime($tmp_name));
							}
							$swift->message->attach($attachment);
						}
					}

					if($draft) {
						if($swift->account['type']!='imap') {
							throw new Exception($lang['email']['noSaveWithPop3']);
						}
						if(empty($swift->account['drafts'])) {
							throw new Exception($lang['email']['draftsDisabled']);
						}
						$drafts_folder = $swift->account['drafts'];
						if ($imap->open($swift->account, $drafts_folder)) {

							$uid_next = $imap->get_uidnext();

							if($uid_next && $imap->append_message($drafts_folder, $swift->message->toString(),"\Seen")) {
								$response['draft_uid']=$uid_next;
								$response['success']=$response['draft_uid']>0;								
							}

							if(!$response['success']) {
								$up_account['id']=$swift->account['id'];
								$up_account['drafts']='';
								$email->_update_account($up_account);

								$response['feedback']=$lang['email']['noUidNext'];
							}

							if(!empty($_POST['draft_uid'])) {
								$imap->delete(array($_POST['draft_uid']));
							}

							$imap->disconnect();
						}
					}else {

						if(!empty($_POST['draft_uid'])) {
							$swift->set_draft($_POST['draft_uid']);
						}


						$response['success']=$swift->sendmail();

						if(!empty($_POST['link'])) {
							$link_props = explode(':', $_POST['link']);
							$swift->link_to(array(array('link_id'=>$link_props[1],'link_type'=>$link_props[0])));
						}
					}

				} catch (Exception $e) {
					$response['feedback'] = $lang['email']['feedbackSMTPProblem'] . '<br />'.nl2br($e->getMessage());
				}
			}
			break;

		case 'save_filter':

			$filter['id']=$_POST['filter_id'];
			$filter['mark_as_read']=isset($_POST['mark_as_read']) ? '1' : '0';
			$filter['keyword']=$_POST['keyword'];
			$filter['folder']=$_POST['folder'];
			$filter['field']=$_POST['field'];


			if($_POST['filter_id']>0) {
				if ($email->update_filter($filter)) {
					$response['success']=true;
				}else {
					$response['feedback']=$lang['common']['saveError'];
				}
			}else {
				$filter['account_id']=$_POST['account_id'];
				if ($response['filter_id']=$email->add_filter($filter)) {
					$response['success']=true;
				}else {
					$response['feedback']=$lang['common']['saveError'];
				}
			}
			break;

		case 'save_account_folders':

			$up_account['id'] = $_POST['account_id'];
			$up_account['sent'] = isset($_POST['sent']) ? ($_POST['sent']) : '';
			$up_account['trash'] = isset($_POST['trash']) ? ($_POST['trash']) : '';
			$up_account['drafts'] = isset($_POST['drafts']) ? ($_POST['drafts']) : '';
			$up_account['spam'] = isset($_POST['spam']) ? ($_POST['spam']) : '';
			$up_account['spamtag']= ($_POST['spamtag']);

			if(!$response['feedback']=$email->_update_account($up_account)) {
				$response['errors']=$lang['common']['saveError'];
			}

			break;

		case 'add_folder':

			$account = $imap->open_account($_REQUEST['account_id']);

			$new_folder_name = trim($_POST['new_folder_name']);
			if(empty($new_folder_name)) {
				throw new MissingFieldException();
			}

			$delimiter = $imap->get_mailbox_delimiter();
			if(File::has_invalid_chars($new_folder_name) || strpos($new_folder_name, $delimiter)) {
				throw new Exception(sprintf($lang['common']['illegalCharsError'],$delimiter.'\\/?*"<>|'));
			}


			$parent_id=$_REQUEST['folder_id'];
			if($parent_id>0) {
				if($folder = $email->get_folder_by_id($parent_id)) {
					if($email->is_mbroot($folder['name'],$delimiter, $account['mbroot'])) {
						$parent_id=0;
					}
					$new_folder_name=$folder['name'].$delimiter.$new_folder_name;
				}else {
					$response['success']=false;
					$response['feedback']=$lang['comon']['selectError'];
					echo json_encode($response);
					exit();
				}

			}else {
				$new_folder_name=$account['mbroot'].$_POST['new_folder_name'];
			}

			if($imap->create_folder($new_folder_name)) {

				$folder['account_id']=$account['id'];
				$folder['name']=$new_folder_name;
				$folder['parent_id']=$parent_id;
				$folder['subscribed']=1;
				$folder['can_have_children']=1;
				$folder['delimiter']=$delimiter;
				
				
				if($email->add_folder($folder)) {
					$response['success']=true;
				}
			}

			if(!$response['success']) {
				$response['feedback']=$lang['email']['feedbackCreateFolderFailed'];
			}

			break;

		case 'subscribe':

			$account = $imap->open_account($_REQUEST['account_id']);
			if($imap->subscribe($_POST['mailbox'])) {
				$response['success']=$email->subscribe($account['id'], $_POST['mailbox']);
			}


			if(!$response['success']) {
				$response['feedback']=$lang['email']['feedbackSubscribeFolderFailed'];
			}
			break;

		case 'unsubscribe':

			$account = $imap->open_account($_REQUEST['account_id']);
			if($imap->unsubscribe($_POST['mailbox'])) {
				$response['success']=$email->unsubscribe($account['id'], $_POST['mailbox']);
			}


			if(!$response['success']) {
				$response['feedback']=$lang['email']['feedbackUnsubscribeFolderFailed'];
			}
			break;

		case 'subscribtions':

			$account = $imap->open_account(($_REQUEST['account_id']));

			$response['success']=true;
			$newSubscriptions=json_decode(($_POST['subscribtions']), true);
			$curSubscriptions = $imap->get_subscribed('', true);

			//var_dump($newSubscriptions);
			while($newSubscribedFolder = array_shift($newSubscriptions)) {

				$is_subscribed=in_array($newSubscribedFolder['name'], $curSubscriptions);

				$folderName=$newSubscribedFolder['name'];

				$must_be_subscribed=$newSubscribedFolder['subscribed']!='0';

				if(!$is_subscribed && $must_be_subscribed) {
					//echo 'SUBSCRIBE:'.$folderName."\n";
					if($imap->subscribe($folderName)) {
						$email->subscribe($account['id'], $folderName);
					}
				}elseif($is_subscribed && !$must_be_subscribed) {
					//echo 'UNSUBSCRIBE:'.$folderName."\n";
					if($imap->unsubscribe($folderName)) {
						$email->unsubscribe($account['id'], $folderName);
					}
				}
			}

			break;

		case 'delete_folder':

			if($folder = $email->get_folder_by_id($_REQUEST['folder_id'])) {
				$account = $imap->open_account($folder['account_id']);

				if (empty($folder['name']) || $imap->delete_folder($folder['name'], $account['mbroot'])) {
					$response['success']=$email->delete_folder($account['id'], $folder['name']);
				}else {
					$response['feedback']=$lang['email']['feedbackDeleteFolderFailed'];
				}
			}else {
				$response['feedback']=$lang['comon']['selectError'];
			}
			break;
		case 'rename_folder':

			if($folder = $email->get_folder_by_id(($_REQUEST['folder_id']))) {
				$pos = strrpos($folder['name'], $folder['delimiter']);
				if ($pos && $folder['delimiter'] != '') {
					$location = substr($folder['name'],0,$pos+1);

				}else {
					$location = '';
				}

				$new_folder = $location.$_POST['new_name'];

				$imap->open_account($folder['account_id']);

				if ($imap->rename_folder($folder['name'], $new_folder)) {
					$response['success']=$email->rename_folder($folder['account_id'], $folder['name'], $new_folder);

					$email->get_folders_by_path($folder['account_id'], $folder['name']);
					$folder_src_length = strlen($folder['name']);
					while($record = $email->next_record()) {
						$email2 = new email();

						if($new_folder != $record['name']) {
							$folder_name = $new_folder.substr($record['name'], $folder_src_length);
							$email2->rename_folder($folder['account_id'], $record['name'], $folder_name);
						}
					}

				}else {
					$response['feedback']=$lang['common']['saveError'];
				}
			}else {
				$response['feedback']=$lang['comon']['selectError'];
			}
			break;
		case 'move_folder':

			$account_id = isset($_REQUEST['account_id']) ? $_REQUEST['account_id'] : 0;
			$source_id = isset($_REQUEST['source_id']) ? $_REQUEST['source_id'] : 0;
			$target_id = isset($_REQUEST['target_id']) ? $_REQUEST['target_id'] : 0;

			$response['success'] = false;
			if($source_id && $target_id && $account_id) {
				$account = $imap->open_account($account_id);

				$folder_src = $email->get_folder_by_id(substr($source_id, 7));

				$pos = strrpos($folder_src['name'], $folder_src['delimiter']);
				if($pos && $folder_src['delimiter'] != '') {
					$folder_name = substr($folder_src['name'],$pos+1);
				}else {
					$folder_name = $folder_src['name'];
				}

				if('account_'.$account_id == $target_id) {
					$parent_id = 0;
					$path_name=$account['mbroot'].$folder_name;
				}else {
					$folder_dest = $email->get_folder_by_id(substr($target_id, 7));

					$parent_id = $folder_dest['id'];
					$path_name = $folder_dest['name'].$folder_dest['delimiter'].$folder_name;
				}

				if($imap->rename_folder($folder_src['name'],$path_name)) {
					$response['success'] = $email->rename_folder($account_id, $folder_src['name'], $path_name, $parent_id);

					$email->get_folders_by_path($account_id, $folder_src['name']);
					$folder_src_length = strlen($folder_src['name']);
					while($record = $email->next_record()) {
						$response['deb'][] = '1';
						$email2 = new email();

						$folder_name = $path_name.substr($record['name'], $folder_src_length);
						$email2->rename_folder($account_id, $record['name'], $folder_name);
					}
				}else {
					$response['feedback']=$lang['email']['error_move_folder'];
				}
			}
			else {
				$response['feedback']=$lang['comon']['selectError'];
			}
			break;

		case 'syncfolders':


			$account = $imap->open_account($_REQUEST['account_id']);

			$email->synchronize_folders($account, $imap);
			$response['feedback']=true;
			break;

		case 'save_accounts_sort_order':

			$sort_order = json_decode($_POST['sort_order'], true);

			for($i=0;$i<count($sort_order);$i++) {
				$account['id']=$sort_order[$i];
				$account['standard']=$i;
				$email->_update_account($account);
			}
			$response['success']=true;
			break;

		case 'save_account_properties':

			$account['mbroot'] = isset($_POST['mbroot']) ? $_POST['mbroot'] : '';

			if ($_POST['name'] == "" ||	$_POST['email'] == "" ||
							($GO_MODULES->modules['email']['write_permission'] && ($_POST['port'] == "" ||
															$_POST['username'] == "" ||
															$_POST['password'] == "" ||
															$_POST['host'] == "" ||
															$_POST['smtp_host'] == "" ||
															$_POST['smtp_port'] == ""))) {
				$response['feedback'] = $lang['common']['missingField'];
			}else {
				$account['id']=isset($_POST['account_id']) ? ($_POST['account_id']) : 0;

				if(isset($_POST['username'])) {
					$account['mbroot'] = isset($_POST['mbroot']) ? $_POST['mbroot'] : '';
					$account['use_ssl'] = isset($_REQUEST['use_ssl'])  ? '1' : '0';
					//$account['novalidate_cert'] = isset($_REQUEST['novalidate_cert']) ? '1' : '0';
					$account['examine_headers'] = isset($_POST['examine_headers']) ? '1' : '0';
					$account['type']=$_POST['type'];
					$account['host']=$_POST['host'];
					$account['port']=$_POST['port'];
					$account['username']=$_POST['username'];
					$account['password']=$_POST['password'];

					$account['smtp_host']=$_POST['smtp_host'];
					$account['smtp_port']=$_POST['smtp_port'];
					$account['smtp_encryption']=$_POST['smtp_encryption'];
					$account['smtp_username']=$_POST['smtp_username'];
					$account['smtp_password']=$_POST['smtp_password'];
				}
				$account['name']=$_POST['name'];
				$account['email']=$_POST['email'];
				$account['signature']=$_POST['signature'];

				if ($account['id'] > 0) {
					if(isset($_REQUEST['user_id'])) {
						$account['user_id']=$_REQUEST['user_id'];
					}

					$account['sent']=$_POST['sent'];
					$account['drafts']=$_POST['drafts'];
					$account['trash']=$_POST['trash'];

					if($GO_MODULES->modules['email']['write_permission']) {
						if(!$email->update_account($account)) {
							throw new Exception(sprintf($lang['email']['feedbackCannotConnect'],$_POST['host'], $imap->last_error(), $_POST['port']));
						}
					}else {
						if(!$email->_update_account($account)) {
							throw new DatabaseUpdateException();
						}
					}

					$response['success']=true;

					$use_systemusers = true;
					if(isset($GO_MODULES->modules['serverclient'])) {
						require_once($GO_MODULES->modules['serverclient']['class_path'].'serverclient.class.inc.php');
						$sc = new serverclient();

						if(count($sc->domains)) {
							$use_systemusers = false;

							foreach($sc->domains as $domain) {
								if(!$GO_MODULES->modules['email']['write_permission']) {
									$account = $email->get_account($account['id']);
									$account = $email->decrypt_account($account);
								}

								//go_log(LOG_DEBUG, $account['username'].' -> '.$domain);
								if(strpos($account['username'], '@'.$domain)) {
									$sc->login();

									require_once($GO_CONFIG->class_path.'mail/RFC822.class.inc');
									$RFC822 = new RFC822();

									$forward_to=$RFC822->reformat_address_list($_POST['forward_to'],true);
									if($forward_to===false) {
										throw new Exception($lang['common']['invalidEmailError']);
									}


									$params=array(
													//'sid'=>$sc->sid,
													'task'=>'serverclient_set_vacation',
													'username'=>$account['username'],
													'password'=>$account['password'],
													'vacation_active'=>isset($_POST['vacation_active']) ? '1' : '0',
													'vacation_subject'=>$_POST['vacation_subject'],
													'vacation_body'=>$_POST['vacation_body'],
													'forward_to'=>$_POST['forward_to']
									);

									//go_log(LOG_DEBUG, var_export($params, true));

									$server_response = $sc->send_request($GO_CONFIG->serverclient_server_url.'modules/postfixadmin/action.php', $params);

									$server_response = json_decode($server_response, true);

									if(!$server_response['success']) {
										throw new Exception($server_response['feedback']);
									}
									break;
								}

							}
						}
					}

					if(isset($GO_MODULES->modules['systemusers']) && $use_systemusers) {
						exec('whereis vacation', $return);
						if(isset($return[0])) {
							$params = explode(' ', $return[0]);
						}

						$vacation_exec = (isset($params[1])) ? $params[1] : false;
						if(!is_executable($vacation_exec)) {
							require_once ($GO_LANGUAGE->get_language_file('systemusers'));
							//throw new Exception($lang['systemusers']['vacation_not_executable_error']);
						}


						require_once($GO_MODULES->modules['systemusers']['class_path'].'systemusers.class.inc.php');
						$su = new systemusers();

						$vacation['vacation_active'] = isset($_POST['vacation_active']) ? '1' : '0';
						$vacation['vacation_subject'] = isset($_POST['vacation_subject']) ? ($_POST['vacation_subject']) : '';
						$vacation['vacation_body'] = isset($_POST['vacation_body']) ? ($_POST['vacation_body']) : '';
						$vacation['forward_to'] = isset($_POST['forward_to']) ? ($_POST['forward_to']) : '';
						$vacation['account_id'] = $account['id'];

						if($su->get_vacation($vacation['account_id'])) {
							$su->update_vacation($vacation);
						}else {
							$su->add_vacation($vacation);
						}

						$cmd = $GO_CONFIG->cmd_sudo.' '.$GO_MODULES->modules['systemusers']['path'].'sudo.php '.$GO_CONFIG->get_config_file().' set_vacation '.$vacation['account_id'];
						//go_debug($cmd);
						exec($cmd);
					}
				}else {
					$account['user_id']=isset($_REQUEST['user_id']) ? ($_REQUEST['user_id']) : $GO_SECURITY->user_id;


					$account['id'] = $email->add_account($account);
					if(!$account['id']) {
						$response['feedback'] = sprintf($lang['email']['feedbackCannotConnect'],$_POST['host'], $imap->last_error(), $_POST['port']);
					}else {
						$account = $email->get_account($account['id']);
						//$email->synchronize_folders($account);

						$response['success']=true;
						$response['account_id']=$account['id'];
					}
				}
			}
			break;
		case 'save_alias':
			$alias_id=$alias['id']=isset($_POST['alias_id']) ? $_POST['alias_id'] : 0;

			$alias['name']=$_POST['name'];
			$alias['email']=$_POST['email'];
			$alias['signature']=$_POST['signature'];
			if($alias['id']>0) {
				$email->update_alias($alias);
				$response['success']=true;
				$insert=false;
			}else {
				$alias['account_id']=$_POST['account_id'];
				$alias_id= $email->add_alias($alias);
				$response['alias_id']=$alias_id;
				$response['success']=true;
				$insert=true;
			}
			break;

		case 'save_filters_sort_order':

			$sort_order = json_decode(($_POST['sort_order']), true);

			foreach($sort_order as $filter_id=>$sort_index) {
				$filter['id'] = $filter_id;
				$filter['priority']=$sort_index;
				$email->update_filter($filter);
			}
			$success=true;
			break;
		/* {TASKSWITCH} */
	}
}catch(Exception $e) {
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}

echo json_encode($response);