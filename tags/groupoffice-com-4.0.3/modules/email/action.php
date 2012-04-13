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

// check is needed for SWFUpload
//if(isset($_REQUEST['groupoffice']))
//{
//	session_id($_REQUEST['groupoffice']);
//}

require_once("../../Group-Office.php");

$GO_SECURITY->json_authenticate('email');

//close writing to session so other concurrent requests won't be locked out.
session_write_close();

require_once ($GO_MODULES->modules['email']['class_path']."cached_imap.class.inc.php");
require_once ($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
require_once ($GO_LANGUAGE->get_language_file('email'));
$imap = new cached_imap();
$email = new email();

//ini_set('display_errors','off');


function add_unknown_recipient($email, $name) {
	global $GO_SECURITY, $ab, $response, $RFC822, $GO_CONFIG;

	require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
	$GO_USERS = new GO_USERS();

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
	if(!isset($_REQUEST['task'])){
		//probably too large upload
		throw new Exception(sprintf($lang['common']['upload_file_to_big'], ini_get('upload_max_filesize')));
	}

	switch($_REQUEST['task']) {

		case 'upload_attachment':

			$response['success']=true;

			$last_dir = uniqid(date('is'),true);
			$dir = $GO_CONFIG->tmpdir.'attachments/'.$last_dir.'/';
			require_once($GO_CONFIG->class_path.'filesystem.class.inc');
			filesystem::mkdir_recursive($dir);

			if(isset($_FILES['Filedata']))
			{
				$file = $_FILES['Filedata'];
			}else
			{
				$file['name'] = $_FILES['attachments']['name'][0];
				$file['tmp_name'] = $_FILES['attachments']['tmp_name'][0];
				$file['size'] = $_FILES['attachments']['size'][0];
			}
			
			if(is_uploaded_file($file['tmp_name']))
			{

				$file['name']=File::strip_invalid_chars($file['name']);
				$tmp_file = $dir.$file['name'];
				move_uploaded_file($file['tmp_name'], $tmp_file);
				if (substr($dir,0,1)=='/')
					$dir = substr($dir,1,strlen($dir)-2);

				$extension = File::get_extension($file['name']);
				$response['file'] = array(
					'tmp_name'=>$tmp_file,
					'name'=>$file['name'],
					'last_dir'=>$last_dir,
					'size'=>$file['size'],
					'type'=>File::get_filetype_description($extension),
					'extension'=>$extension,
					'human_size'=>Number::format_size($file['size'])
				);
			}

			echo json_encode($response);
			exit();

			break;


		case 'move':

			$start_time = time();
			
			$messages= json_decode($_POST['messages'], true);
			$total = $_POST['total'];

			//move to another imap account
			$imap2 = new cached_imap();
			$from_account = $imap->open_account($_POST['from_account_id'], $_POST['from_mailbox']);


			$to_account = $email->get_account($_POST['to_account_id']);

			if(!$to_account)
				throw new DatabaseSelectException();

			$imap2->open($to_account, $_POST['to_mailbox']);

			$delete_messages =array();
			while($uid=array_shift($messages)) {
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

				$left = count($messages);

				if($left && $start_time-5<time()) {

					$done = $total-$left;

					$response['messages']=$messages;
					$response['progress']=number_format($done/$total,2);
				
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

			$folder = $files->get_folder($_POST['folder_id']);
			$path = $files->build_path($folder);
			if(!$path) {
				throw new FileNotFoundException();
			}

			$path.='/'.$_POST['filename'];
			
			if(file_exists($GO_CONFIG->file_storage_path.$path)){
				throw new Exception("File exists");
			}
			
			
			require_once($GO_CONFIG->class_path."mail/mimeDecode.class.inc");
			if(!empty($_REQUEST['filepath'])){
				//message is cached on disk
				$msgpath = $GO_CONFIG->file_storage_path.$_REQUEST['filepath'];

				if(File::path_leads_to_parent($msgpath) || !file_exists($msgpath)){
					die('Invalid request');
				}
				$params['input'] = file_get_contents($msgpath);
				$params['include_bodies'] = true;
				$params['decode_bodies'] = true;
				$params['decode_headers'] = false;

				$part = Mail_mimeDecode::decode($params);

				$parts_arr = explode('.',$_REQUEST['imap_id']);
				for($i=0;$i<count($parts_arr);$i++) {
					if(isset($part->parts[$parts_arr[$i]])){
						$part = $part->parts[$parts_arr[$i]];
					}else{
						go_debug('Mime part not found!');
						go_debug($_REQUEST);
						die('Part not found');
					}
				}	
				file_put_contents($GO_CONFIG->file_storage_path.$path,$part->body);
				

			}else
			{
				$account = $imap->open_account($_POST['account_id'], $_POST['mailbox']);
				$imap->save_to_file($_REQUEST['uid'], $GO_CONFIG->file_storage_path.$path, $_REQUEST['imap_id'], $_REQUEST['encoding']);			
				$imap->disconnect();
			}
			
			require_once($GO_CONFIG->root_path.'GO.php');
			$folder = GO_Files_Model_Folder::model()->findByPk($folder['id']);
			$folder->addFile($_POST['filename']);

			//$files->import_file($GO_CONFIG->file_storage_path.$path,$folder['id']);

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
			$last_dir=uniqid(date('is'),true);
			$dir = $GO_CONFIG->tmpdir.'attachments/'.$last_dir.'/';

			require_once($GO_CONFIG->class_path.'filesystem.class.inc');
			filesystem::mkdir_recursive($dir);

			for ($n = 0; $n < count($_FILES['attachments']['tmp_name']); $n ++) {
				if (is_uploaded_file($_FILES['attachments']['tmp_name'][$n])) {										
					$tmp_file = $dir.File::strip_invalid_chars($_FILES['attachments']['name'][$n]);
					move_uploaded_file($_FILES['attachments']['tmp_name'][$n], $tmp_file);

					$extension = File::get_extension($_FILES['attachments']['name'][$n]);
					$response['files'][] = array(
						'tmp_name'=>$tmp_file,
						'name'=>File::strip_invalid_chars($_FILES['attachments']['name'][$n]),
						'size'=>$_FILES['attachments']['size'][$n],
						'type'=>File::get_filetype_description($extension),
						'extension'=>$extension,
						'human_size'=>Number::format_size($_FILES['attachments']['size'][$n]),
						'last_dir'=>$last_dir
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
			
			require($GO_LANGUAGE->get_language_file('email'));

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
						$swift->set_reply_to($_POST['reply_uid'],$_POST['reply_mailbox'], $_POST['in_reply_to']);

					if(!empty($_POST['forward_uid']))
						$swift->set_forward_uid($_POST['forward_uid'],$_POST['forward_mailbox']);


					$RFC822 = new RFC822();
					$to_addresses = $RFC822->parse_address_list($_POST['to']);

					$skip_unknown_recipients = $GO_CONFIG->get_setting('email_skip_unknown_recipients', $GO_SECURITY->user_id);

					//used for gpg encryption
					$all_recipients = array();

					foreach($to_addresses as $address) {
						$all_recipients[]=$address['email'];
						if(!$skip_unknown_recipients)
						    add_unknown_recipient($address['email'], $address['personal']);
					}


					$email_show_cc = (isset($_REQUEST['email_show_cc']) && $_REQUEST['email_show_cc']) ? 1 : 0;
					$email_show_bcc = (isset($_REQUEST['email_show_bcc']) && $_REQUEST['email_show_bcc']) ? 1 : 0;
					$GO_CONFIG->save_setting('email_show_cc', $email_show_cc, $GO_SECURITY->user_id);
					$GO_CONFIG->save_setting('email_show_bcc', $email_show_bcc, $GO_SECURITY->user_id);

					if(!empty($_POST['cc']))
					{
						$cc_addresses = $RFC822->parse_address_list($_POST['cc']);

						$swift_addresses=array();
						foreach($cc_addresses as $address) {
							$all_recipients[]=$address['email'];

							if(!$skip_unknown_recipients)
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

							if(!$skip_unknown_recipients)
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

							if(!empty($inlineAttachment['temp'])){
								$tmp_name= $GO_CONFIG->tmpdir.'attachments/'.$tmp_name;
							}elseif(is_numeric($tmp_name)) {
								require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
								$files = new files();

								$file = $files->get_file($tmp_name);
								//throw new Exception(var_export($file, true));
								$folder = $files->get_folder($file['folder_id']);
								if(!$file || !$folder) {
									//throw new FileNotFoundException();
									continue;
								}
								$tmp_name = $GO_CONFIG->file_storage_path.$files->build_path($folder).'/'.$file['name'];
							}

							if(file_exists($tmp_name)) {
								//Browsers reformat URL's so a pattern match
								$just_filename = utf8_basename($inlineAttachment['url']);
								if(preg_match('/="([^"]*'.preg_quote($just_filename).')"/',$body,$matches)){
									//go_debug($matches);
									$img = Swift_EmbeddedFile::fromPath($tmp_name);
									$img->setContentType(File::get_mime($tmp_name));
									$src_id = $swift->message->embed($img);

									//Browsers reformat URL's so a pattern match
									$body = str_replace($matches[1], $src_id, $body);
								}
							}
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

						if($_POST['content_type']=='html') {
							$body = '<html><head><style>.em-message p{margin:0px}</style></head><body><div class="em-message">'.$body.'</div></body></html>';
						}

						$swift->set_body($body, $_POST['content_type']);
					}

					if($GO_MODULES->has_module('files')) {
						require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
						$files = new files();
					}

					if(isset($_POST['attachments'])) {
						$attachments = json_decode($_POST['attachments'],true);
						$attachments_tmp_names = array();

						foreach($attachments as $tmp_name) {
							if(is_numeric($tmp_name)) {
								$from_go=true;
								$file = $files->get_file($tmp_name);
								$folder = $files->get_folder($file['folder_id']);
								if(!$file || !$folder) {
									throw new FileNotFoundException();
								}
								$tmp_name = $GO_CONFIG->file_storage_path.$files->build_path($folder).'/'.$file['name'];
							}else
							{
								$from_go=false;
								$attachments_tmp_names[] = $tmp_name;
							}

							if(!file_exists($tmp_name)) {
								throw new FileNotFoundException();
							}

							if(!empty($_POST['encrypt']) && !$draft) {
								$encoded = $gnupg->encode_file($tmp_name, $all_recipients, $swift->account['email']);

								$attachment = Swift_Attachment::fromPath($encoded,'application/pgp-encoded');
							}else {
								$attachment = Swift_Attachment::fromPath($tmp_name,File::get_mime($tmp_name));
//								if(!$from_go)
//									$attachment->setFilename(substr(utf8_basename($tmp_name),27));
							}
							$swift->message->attach($attachment);
						}
					}

					//throw new Exception(htmlspecialchars(var_export($swift->message->toString(), true)));

					if($draft) {

						if(!empty($_POST['save_to_path'])){
							//save e-mail to disk
							$full_path = $GO_CONFIG->file_storage_path.$_POST['save_to_path'];

							file_put_contents($full_path, $swift->message->toString());

							if(!file_exists($full_path)){
								throw new Exception('Failed to save file');
							}
							$response['success']=true;

						}else
						{
							//save e-mail in IMAP drafts folder

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
						}
					}else {

						if(!empty($_POST['draft_uid'])) {
							$swift->set_draft($_POST['draft_uid']);
						}						
						
						$GO_EVENTS->fire_event("sendmail",array(&$swift));


						$response['success']=$swift->sendmail();

						if(!empty($_POST['link'])) {
							$link_props = explode(':', $_POST['link']);
							$swift->link_to($link_props[0],$link_props[1]);
						}

						if(isset($attachments_tmp_names))
						{
							foreach($attachments_tmp_names as $tmp_name)
							{
								if(file_exists($tmp_name))
								{
									unlink($tmp_name);
								}
							}
						}
					}

				} catch (Exception $e) {
					$response['feedback'] = nl2br($e->getMessage());
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
						$response['is_mbroot'] = true;
					}
					$new_folder_name=$folder['name'].$delimiter.$new_folder_name;
				}else {
					$response['success']=false;
					$response['feedback']=$lang['common']['selectError'];
					echo json_encode($response);
					exit();
				}

			}else {
				if(!empty($account['mbroot']))
					$new_folder_name=trim($account['mbroot'],$delimiter).$delimiter.$_POST['new_folder_name'];
				else
					$new_folder_name=$_POST['new_folder_name'];
			}

			if($imap->create_folder($new_folder_name)) {
				$folder=array();
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
					$email2 = new email();
					while($record = $email->next_record()) {
						if(($new_folder != $record['name']) && (strstr($record['name'], $folder['name'].'.')))
						{
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
			$count = count($sort_order);

			for($i=0;$i<$count;$i++) {
				
				$as['account_id']=$sort_order[$i];
				$as['user_id']=$GO_SECURITY->user_id;
				$as['order']=$count-$i;

				$email->update_account_order($as);
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

						$old_account = $email->get_account($account['id']);
						if($old_account['user_id']!=$account['user_id']){
							$GO_SECURITY->chown_acl($old_account['acl_id'], $account['user_id']);
						}

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

								if(strpos($account['email'], '@'.$domain)) {
									$sc->login();

									require_once($GO_CONFIG->class_path.'mail/RFC822.class.inc');
									$RFC822 = new RFC822();

									$forward_to=$RFC822->reformat_address_list($_POST['forward_to'],true);
									if($forward_to===false) {
										throw new Exception($lang['common']['invalidEmailError']);
									}

									//For LDAP auth with usernames without the domain in it.
									if(!strpos($account['username'],'@')){
										$arr= explode('@',$account['email']);
										$account['username'] .= '@'.$arr[1];
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

									$server_response = $sc->send_request($GO_CONFIG->serverclient_server_url.'modules/postfixadmin/action.php', $params);

									$decoded = json_decode($server_response, true);
									
									if(!isset($decoded['success']))
										throw new Exception('Serverclient Could not connect to '.$GO_CONFIG->serverclient_server_url.': '.$server_response);

									if(!$decoded['success']) {
										throw new Exception($decoded['feedback']);
									}
									break;
								}

							}
						}
					}
				}else {
					$account['user_id']=isset($_REQUEST['user_id']) ? ($_REQUEST['user_id']) : $GO_SECURITY->user_id;

					$account['acl_id']=$response['acl_id']=$GO_SECURITY->get_new_acl('email', $account['user_id']);

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
				
				$GO_EVENTS->fire_event('save_email_account', array(&$account, $email, &$response));
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

		case 'save_skip_unknown_recipients':

		    $value = (isset($_POST['checked']) && $_POST['checked'] == 'true') ? '1' : '0';
		    $GO_CONFIG->save_setting('email_skip_unknown_recipients', $value, $GO_SECURITY->user_id);

		    $response['success'] = true;
		    break;


		case 'update_state':

		    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		    $new_state = (isset($_REQUEST['open']) && $_REQUEST['open'] == 'true') ? true : false;
		    $folder = (isset($_REQUEST['folder']) && $_REQUEST['folder'] == 'true') ? true : false;

		    if($folder)
		    {
			    $current_state = $email->is_folder_expanded($id, $GO_SECURITY->user_id);
			    if($current_state != $new_state)
			    {
				    $email->update_folder_state($id, $GO_SECURITY->user_id, $new_state);
			    }

		    }else
		    {
			    $current_state = $email->is_account_expanded($id, $GO_SECURITY->user_id);
			    if($current_state != $new_state)
			    {
				    $email->update_account_state($id, $GO_SECURITY->user_id, $new_state);
			    }
		    }

		    $response['success'] = true;
		    break;

	    case 'icalendar_process_invitation':

			require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			if(!isset($GO_MODULES->modules['calendar']) || !$GO_MODULES->modules['calendar']['read_permission']) {
				throw new Exception(sprintf($lang['common']['moduleRequired'], $lang['email']['calendar']));
			}

			require_once($GO_CONFIG->class_path.'Date.class.inc.php');
			require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc.php');
			$cal = new calendar();

			$status_id = (isset($_REQUEST['status_id']) && $_REQUEST['status_id']) ? $_REQUEST['status_id'] : -1;
			$email_sender = (isset($_REQUEST['email_sender']) && $_REQUEST['email_sender']) ? $_REQUEST['email_sender'] : '';
			$email = (isset($_REQUEST['email']) && $_REQUEST['email']) ? $_REQUEST['email'] : '';

			$create_event = true;
			if($status_id == 2)
			{
				//User wants to decline
				go_debug($_REQUEST['uuid'].' : '.$email);
				
				if(!$cal->is_event_declined($_REQUEST['uuid'], $email))
				{

					$response['success'] = $cal->add_declined_event_uid(array('uid'=>$_REQUEST['uuid'], 'email' => $email));
				}else
				{
					$response['success'] = true;
				}

				$create_event = false;
			}

			$event_id = (isset($_REQUEST['event_id']) && $_REQUEST['event_id']) ? $_REQUEST['event_id'] : 0;
			$calendar_id = (isset($_REQUEST['cal_id']) && $_REQUEST['cal_id']) ? $_REQUEST['cal_id'] : 0;
			$calendars = array();

			if($create_event)
			{
				if($event_id && !$calendar_id)
				{
					$old_event = $cal->get_event($event_id);
					if($old_event)
					{
						$calendar_id = $old_event['calendar_id'];
					}
				}

				$defcal = $cal->get_default_calendar($GO_SECURITY->user_id);

				$calendars[]=array(
					'id' => $defcal['id'],
					'name' => $defcal['name']
				);

				if(!$calendar_id)
				{
					$cal->get_authorized_calendars($GO_SECURITY->user_id);
					while($cal->next_record())
					{
						if($cal->f('id')!=$defcal['id']){
							$calendars[] = array(
								'id' => $cal->f('id'),
								'name' => $cal->f('name')
							);
						}
					}
				}
				
			}

			if($create_event && (count($calendars) > 1))
			{
				
				//present calendar selection dialog
				$response['status_id']=$status_id;
				$response['calendars'] = $calendars;
				$response['success']=false;
				break;
			}else
			{
				$account = $imap->open_account($_REQUEST['account_id'], $_REQUEST['mailbox']);
				$data = $imap->get_message_part_decoded($_REQUEST['message_uid'], $_REQUEST['imap_id'], $_REQUEST['encoding']);
				$imap->disconnect();

				require_once($GO_CONFIG->class_path.'ical2array.class.inc');
				$cal->ical2array = new ical2array();

				$vcalendar = $cal->ical2array->parse_string($data);

				$event=false;
				//$event_object;
				while($object = array_shift($vcalendar[0]['objects']))
				{
					if($object['type'] == 'VEVENT')
					{
						$event = $cal->get_event_from_ical_object($object);
						
						
						//this might be an update for a specific recurrence ID
						if(!empty($object['RECURRENCE-ID']['value'])){
							$timezone_id = isset($object['RECURRENCE-ID']['params']['TZID']) ? $object['RECURRENCE-ID']['params']['TZID'] : '';
							$exception_date = $cal->ical2array->parse_date($object['RECURRENCE-ID']['value'],$timezone_id);
						}
						
						break;
					}
				}
				if(!$event)
				{
					throw new Exception($lang['common']['selectError']);
				}
			}
				
			if($create_event)
			{

				if($cal->is_event_declined($_REQUEST['uuid'], $email))
				{
					$cal->delete_declined_event_uid($_REQUEST['uuid'], $email);
				}

				if(!$calendar_id)
				{
					$calendar_id = $calendars[0]['id'];
				}

				$event['calendar_id'] = $calendar_id;
				//$participants = ;


				$organizer_email = false;
				foreach($event['participants'] as $participant_email=>&$participant)
				{
					if($status_id > -1 && $participant_email == $email)
					{
						$participant['status']=$status_id;

						//touch timestamp so invitations will update the original event
						$event['mtime']=time();
					}

					if(isset($participant['is_organizer']) && $participant['is_organizer'])
					{
						$organizer_email = $participant_email;
						if(!String::validate_email($organizer_email))
							$organizer_email=$_POST['email_sender'];

					}
				}
				
				go_debug("Existing event id: ".$event_id);

				if($event_id)
				{
					if(!empty($exception_date)){
						
						$old_event = $cal->get_event($event_id);
						
						$exception_date=getdate($exception_date);
						$old_date = getdate($old_event['start_time']);					
			
						$exception['time']= mktime($old_date['hours'],$old_date['minutes'], 0,$exception_date['mon'],$exception_date['mday'],$exception_date['year']);						
						
						if(!$cal->is_exception($event_id, $exception['time'])){
							$exception['event_id']=$event_id;						
							$cal->add_exception($exception);

							$event['exception_for_event_id']=$event_id;
							$event['uuid']=$old_event['uuid'];
							$event_id = $cal->add_event($event);
						}else
						{
							//get the specific recurring item
							$recurrence_event = $cal->get_event_by_uuid($old_event['uuid'], $GO_SECURITY->user_id, $calendar_id, $exception['time']);
							$event['id'] = $recurrence_event['id'];
							$cal->update_event($event, false, $recurrence_event);
						}
						
					}else
					{
					
						$event['id'] = $event_id;
						$method = isset($vcalendar[0]['METHOD']['value']) ? $vcalendar[0]['METHOD']['value'] : '';
						if($method=='REPLY'){
							unset($event['name'],$event['location'], $event['description']);
						}
						$cal->update_event($event, false, $old_event);
					}
				}else
				{
					$event_id = $cal->add_event($event);
				}




				if($event_id)
				{
					if($status_id>-1){
						$status_name = $cal->get_participant_status_name($status_id);

						if($organizer_email)
						{
							require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
							$swift = new GoSwift(
								$organizer_email,
								$lang['calendar']['invitation'].' '.$lang['calendar']['statuses'][$status_name].': '.$event['name']
							);

							//create ics attachment
							require_once ($GO_MODULES->modules['calendar']['class_path'].'go_ical.class.inc');
							$ical = new go_ical('2.0', false, 'REPLY');
							$ical->dont_use_quoted_printable = true;

							$ics_string = $ical->export_event($event_id, $email);

							$swift->set_body($cal->event_to_html($event, false, true));
							$swift->message->attach(new Swift_MimePart($ics_string, 'text/calendar; name="calendar.ics"; METHOD="REPLY"'));

							$swift->set_from($_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name']);

							if(!$swift->sendmail(true)) {
								throw new Exception('Could not send invitation');
							}
						}
					}else
					{
						$response['updated']=true;
					}

					$response['success']=true;
				}else
				{
					$response['success']=false;
				}
			}else
			{
				
				$status_name = $cal->get_participant_status_name($status_id);

				$organizer_email = false;
				$ids = array();
				foreach($event['participants'] as $participant_email=>$participant)
				{
					if(isset($participant['is_organizer']) && $participant['is_organizer'])
					{
						$organizer_email = $participant_email;
						break;
					}
				}

				if(!String::validate_email($organizer_email))
					$organizer_email=$_POST['email_sender'];

				if(!isset($lang['calendar'])) {
					global $GO_LANGUAGE;
					$GO_LANGUAGE->require_language_file('calendar');
				}

				require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
				$swift = new GoSwift
				(
					$organizer_email,
					$lang['calendar']['invitation'].' '.$lang['calendar']['statuses'][$status_name].': '.$event['name']
				);

				//create ics attachment
				require_once ($GO_MODULES->modules['calendar']['class_path'].'go_ical.class.inc');

				$ical = new go_ical('2.0', false, 'REPLY');
				$ical->dont_use_quoted_printable = true;

				$object['DTSTAMP']=date($ical->datetime_format);

				$ics_string = $ical->export_event($object, $email);

				$swift->set_body($cal->event_to_html($event, false, true));
				$swift->message->attach(new Swift_MimePart($ics_string, 'text/calendar; name="calendar.ics"; METHOD="REPLY"'));

				$swift->set_from($_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name']);

				if(!$swift->sendmail(true)) {
					throw new Exception('Could not send invitation');
				}

				$response['success'] = true;

			}

		break;

	case 'setacl':

				$account = $imap->open_account($_REQUEST['account_id'], $_REQUEST['mailbox']);

				$perms='';

				//lrwstipekxacd

				if(isset($_POST['read'])){
					$perms .='lrs';
				}

				if(isset($_POST['write'])){
					$perms .='wip';
				}

				if(isset($_POST['delete'])){
					$perms .='te';
				}

				if(isset($_POST['createmailbox'])){
					$perms .='k';
				}
				if(isset($_POST['deletemailbox'])){
					$perms .='x';
				}
				if(isset($_POST['admin'])){
					$perms .='a';
				}

				$response['success']=$imap->set_acl($_REQUEST['mailbox'], $_REQUEST['identifier'], $perms);
				break;

	case 'delete_old_mails':

		$account_id = $_POST['account_id'];
		$account_record = $email->get_account($account_id);
		
		// User has access to email account or not, there is no other distinction with email account rights.
		if (!$GO_SECURITY->has_permission($GO_SECURITY->user_id,$account_record['acl_id']))
			throw new AccessDeniedException();

//		function get_child_folders($account_id,$folder_id) {
//			$children = array();
//			$email2 = new email();
//			$email2->get_mailboxes($account_id,$folder_id);
//			while ($mb = $email2->next_record()) {
//				$children[] = $mb['name'];
//				$children = array_merge($children,get_child_folders($account_id,$mb['id']));
//			}
//			return $children;
//		}

		global $GO_LANGUAGE;
		require_once ($GO_LANGUAGE->get_language_file('email'));

		$before_timestamp = Date::to_unixtime($_POST['until_date']);
		if (empty($before_timestamp))
			throw new Exception($lang['email']['untilDateError'].': '.$_POST['until_date']);

		$date_string = date('d',$before_timestamp).'-'.date('M',$before_timestamp).'-'.date('Y',$before_timestamp);

		$total = !empty($_POST['total']) ? $_POST['total'] : false;
		$n_deleted = !empty($_POST['n_deleted']) ? $_POST['n_deleted'] : 0;
		$apply_to_children = false; // $_POST['apply_to_children']!='false' && $_POST['apply_to_children']!=false && !empty($_POST['apply_to_children']);
		$mailbox_name = $_POST['mailbox'];
		$uids = json_decode($_POST['uids']);
		$id_array = explode('_',$_POST['id']); $folder_id = $id_array[1];

		$account = $imap->open_account($account_id, $mailbox_name);

		if (!empty($apply_to_children)) {
			$folders = $imap->get_folder_tree($mailbox_name);
		} else {
			$folders = array($mailbox_name => $mailbox_name);
		}

		if (empty($uids)) {
			foreach ($folders as $folder_name=>$value) {
				$imap->select_mailbox($folder_name);
				$new_uids = $imap->sort_mailbox('ARRIVAL',false,'BEFORE "'.$date_string.'"');
				foreach ($new_uids as $k=>$v) {
					$new_uids[$k] = array($folder_name,$v);
				}
				$uids = array_merge($new_uids,$uids);
			}
		}

		if ($total===false) {
			$total = count($uids);
		}

		$end_time = time() + 10;

		$current_mailbox = '';
		while ($uid = array_shift($uids)) {
			if (time()<=$end_time) {
				if (strcmp($current_mailbox,$uid[0])!=0) {
					$current_mailbox=$uid[0];
					$imap->select_mailbox($uid[0]);
				}
				$imap->delete(array($uid[1]));
				$n_deleted++;
			} else {
				$uids[] = $uid;
				break;
			}
		}

		$response['total'] = $total;
		$response['uids'] = json_encode($uids);
		$response['progress']= !empty($total) ? number_format($n_deleted/$total,2) : 1.00;
		$response['nDeleted'] = $n_deleted;
		$response['success'] = true;

		break;

	case 'log_deletion':

		$n_deleted = !empty($_POST['n_deleted']) ? $_POST['n_deleted'] : 0;

		if ($n_deleted>0 && !empty($GO_MODULES->modules['log'])) {
			$before_timestamp = Date::to_unixtime($_POST['until_date']);
			if (empty($before_timestamp))
				throw new Exception($lang['email']['untilDateError'].': '.$_POST['until_date']);
			$date_string = date('d',$before_timestamp).'-'.date('M',$before_timestamp).'-'.date('Y',$before_timestamp);
			$apply_to_children = false; //$_POST['apply_to_children']!='false' && $_POST['apply_to_children']!=false && !empty($_POST['apply_to_children']);
			$mailbox_name = $_POST['mailbox'];
			$account_id = $_POST['account_id'];

			$account = $imap->open_account($account_id, $mailbox_name);

			global $GO_CONFIG;
			require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
			$search = new search();
			//$with_children_txt = !empty($apply_to_children) ? '(including subfolders) ' : '';
			$before_txt = 'from before '.$date_string;
			$search->log(0, 9, 'Deleted '.$n_deleted.' emails '.$before_txt.' from mailbox '.$mailbox_name.' '.$with_children_txt.'of account '.$account['username'].'.');
		}

		$response['success'] = true;

		break;

		/* {TASKSWITCH} */
	}
}catch(Exception $e) {
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}

echo json_encode($response);