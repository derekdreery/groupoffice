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

require_once($GO_LANGUAGE->get_language_file('mailings'));
require_once($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
$ml = new mailings();
require_once($GO_MODULES->modules['mailings']['class_path'].'templates.class.inc.php');
$tp = new templates();

$feedback = null;

$task = isset($_REQUEST['task']) ? ($_REQUEST['task']) : null;

if($task !='create_oodoc_from_template')
	session_write_close();

try {
	switch($task) {
		
		case 'sendmail':

			if(empty($_POST['mailing_group_id'])) {
				throw new Exception($lang['email']['feedbackNoReciepent']);
			}else {
				try {

					require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');

					$swift = new GoSwift(
									"",
									$_POST['subject'],
									0,
									$_POST['alias_id'],
									$_POST['priority']
					);

					$RFC822 = new RFC822();

					if($_POST['notification']=='true') {
						$swift->message->setReadReceiptTo($swift->account['email']);
					}

					$body = $_POST['content_type']=='html' ? $_POST['body'] : $_POST['textbody'];

					if($GO_MODULES->has_module('files')) {
						require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
						$files = new files();
					}

					//process inline attachments
					$inline_attachments = json_decode($_POST['inline_attachments'], true);
					foreach($inline_attachments as $inlineAttachment) {
						require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
						$files = new files();

						$tmp_name = $inlineAttachment['tmp_file'];
						if(!empty($inlineAttachment['temp'])){
							$tmp_name= $GO_CONFIG->tmpdir.'attachments/'.$tmp_name;
						}elseif(is_numeric($tmp_name)) {
							$file = $files->get_file($tmp_name);
							$folder = $files->get_folder($file['folder_id']);
							if(!$file || !$folder) {
								throw new FileNotFoundException();
							}
							$tmp_name = $GO_CONFIG->file_storage_path.$files->build_path($folder).'/'.$file['name'];
						}

						$img = Swift_EmbeddedFile::fromPath($tmp_name);
						$img->setContentType(File::get_mime($tmp_name));
						$src_id = $swift->message->embed($img);

						//Browsers reformat URL's so a pattern match
						//$body = str_replace($inlineAttachment['url'], $src_id, $body);
						$just_filename = utf8_basename($inlineAttachment['url']);
						$body = preg_replace('/="[^"]*'.preg_quote($just_filename).'"/', '="'.$src_id.'"', $body);
					}

					$swift->set_body($body, $_POST['content_type']);

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
							$attachment = Swift_Attachment::fromPath($tmp_name,File::get_mime($tmp_name));
							$swift->message->attach($attachment);
						}
					}


					$mailing['alias_id']=$_POST['alias_id'];
					$mailing['user_id']=$GO_SECURITY->user_id;
					$mailing['subject']=$_POST['subject'];
					$mailing['ctime']=time();
					$mailing['status']=0;
					$mailing['sent']=0;
					$mailing['errors']=0;
					$mailing['total']=0;
					$mailing['mailing_group_id']=$_POST['mailing_group_id'];
					$mailing['message_path']=$GO_CONFIG->file_storage_path.'mailings/'.$GO_SECURITY->user_id.'_'.date('Ymd_Gis').'.eml';

					if(!is_dir(dirname($mailing['message_path'])))
						mkdir(dirname($mailing['message_path']), 0755, true);

					file_put_contents($mailing['message_path'], $swift->message->toString());

					$mailing_id = $ml->add_mailing($mailing);

					$ml->launch_mailing($mailing_id);


					$response['success']=true;
					echo json_encode($response);

				} catch (Exception $e) {
					require($GO_LANGUAGE->get_language_file('email'));
					$response['feedback'] = $lang['email']['feedbackUnexpectedError'] . $e->getMessage();
					echo json_encode($response);
				}
			}



			break;


		case 'save_mailing':

			$mailing['id'] = isset($_REQUEST['mailing_id']) ? ($_REQUEST['mailing_id']) : 0;
			if(isset($_REQUEST['user_id']))
				$mailing['user_id'] = ($_REQUEST['user_id']);

			$mailing['name'] = isset($_REQUEST['name']) ? ($_REQUEST['name']) : '';
			$mailing['default_salutation'] = isset($_REQUEST['default_salutation']) ? ($_REQUEST['default_salutation']) : '';

			$response['success'] = true;
			$response['feedback'] = $feedback;

			$existing_mailing = $ml->get_mailing_group_by_name($mailing['name']);

			if(!isset($mailing['name'])) {
				throw new Exception($lang['common']['missingField']);
			} else {
				if($mailing['id'] < 1) {
					#insert
					if($existing_mailing) {
						throw new Exception($lang['mailings']['mailingAlreadyExists']);
					}

					if(!$GO_MODULES->modules['mailings']['write_permission']) {
						throw new AccessDeniedException();
					}

					$mailing['acl_id'] = $GO_SECURITY->get_new_acl('mailings');

					$response['mailing_id'] = $ml->add_mailing_group($mailing);
				} else {
					#update
					if ($existing_mailing && ($existing_mailing['id'] != $mailing['id'])) {
						throw new Exception($lang['mailings']['mailingAlreadyExists']);
					}

					if($existing_mailing) {
						if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $existing_mailing['acl_id'])<2) {
							throw new AccessDeniedException();
						}
					}

					if(isset($_REQUEST['user_id']) && $existing_mailing['user_id'] != $mailing['user_id']) {
						$GO_SECURITY->chown_acl($existing_mailing['acl_id'], $mailing['user_id']);
					}

					$ml->update_mailing_group($mailing);
				}
			}

			echo json_encode($response);
			break;


		case 'save_email_template':
			$template['id'] = isset($_REQUEST['email_template_id']) ? ($_REQUEST['email_template_id']) : 0;
			$template['name'] = isset($_REQUEST['name']) ? ($_REQUEST['name']) : 0;
			$template['type'] = '0';
			$template['content'] = isset($_REQUEST['body']) ? ($_REQUEST['body']) : '';

			$notification = isset($_REQUEST['notification']) ? ($_REQUEST['notification']) : '"';

			$response['success'] = true;
			$response['feedback'] = $feedback;

			$user_id = isset($_POST['user_id']) ? ($_POST['user_id']) : $GO_SECURITY->user_id;

			if(isset($_POST['user_id'])) {
				$template['user_id'] = ($_POST['user_id']);
			}


			$existing_template = $tp->get_template_by_name($user_id, $template['name']);

			if(!isset($template['name']) || !isset($template['type'])) {
				throw new MissingFieldException();
			} else {

				$inline_attachments = isset($_REQUEST['inline_attachments']) ? json_decode(($_REQUEST['inline_attachments']), true) : array();

				if(!is_array($inline_attachments))
					$inline_attachments=array();

				#variables only needed during update email template
				$params['include_bodies'] = true;
				$params['decode_bodies'] = true;
				$params['decode_headers'] = true;
				$params['input'] = $existing_template['content'];

				require_once($GO_CONFIG->class_path.'mail/Go2Mime.class.inc.php');
				$go2mime = new Go2Mime();

				if(isset($_POST['notification'])) {
					$go2mime->set_notification($_SESSION['GO_SESSION']['email']);
				}


				$structure = Mail_mimeDecode::decode($params);

				$new_inline_attachments=array();

				//sometimes people attach the same image twice so check if the same URL is not found at the same position
				$unique=array();

				foreach($inline_attachments as $inline_attachment) {

					//do basename on the url because different browsers reformat the URL relative or absolute.
					$pos = strpos($template['content'], utf8_basename($inline_attachment['url']));
					if($pos !== false && !in_array($pos.':'.$inline_attachment['url'], $unique)) {

						$unique[]=$pos.':'.$inline_attachment['url'];

						if(isset($inline_attachment['imap_id']) && $inline_attachment['imap_id']>0) {
							$inline_attachment['data']=$structure->parts[$inline_attachment['imap_id']]->body;
							$inline_attachment['filename']=$structure->parts[$inline_attachment['imap_id']]->d_parameters['filename'];
							$inline_attachment['content_type']=$structure->parts[$inline_attachment['imap_id']]->ctype_primary.'/'.$structure->parts[$inline_attachment['imap_id']]->ctype_secondary;
							$new_inline_attachments[]=$inline_attachment;

						}else if(!empty($inline_attachment['tmp_file'])){

							
							if(!empty($inline_attachment['temp'])){
								$tmp_name= $GO_CONFIG->tmpdir.'attachments/'.$inline_attachment['tmp_file'];
							}else
							{
								$tmp_name = $GO_CONFIG->file_storage_path.$inline_attachment['tmp_file'];
							}

							$inline_attachment['tmp_file']=$tmp_name;
							$new_inline_attachments[]=$inline_attachment;
						}
					}
				}

				//go_debug($new_inline_attachments);

				$go2mime->set_body($template['content']);
				$go2mime->set_inline_attachments($new_inline_attachments);

				$template['content'] = $go2mime->build_mime();

				//go_debug($template['content']);

				if($template['id'] < 1) {
					#insert
					if($existing_template) {
						throw new Exception($lang['mailings']['templateAlreadyExists']);
					}

					if(!$GO_MODULES->modules['mailings']['read_permission']) {
						throw new AccessDeniedException();
					}
					$template['user_id']=$GO_SECURITY->user_id;
					$template['acl_id'] = $response['acl_id'] = $GO_SECURITY->get_new_acl('templates');
					$response['email_template_id'] = $template['id'] = $tp->add_template($template);
				} else {
					#update

					if ($existing_template && ($existing_template['id'] != $template['id'])) {
						throw new Exception($lang['mailings']['templateAlreadyExists']);
					}

					if($existing_template) {
						if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $existing_template['acl_id'])<2) {
							throw new AccessDeniedException();
						}
					}

					if(isset($template['user_id']) && $existing_template['user_id'] != $template['user_id']) {
						$GO_SECURITY->chown_acl($existing_template['acl_id'], $template['user_id']);
					}

					$tp->update_template($template);
				}
			}

			$mail = $go2mime->mime2GO(stripslashes($template['content']), $GO_MODULES->modules['mailings']['url'].'mimepart.php?template_id='.$template['id']);
			//$response['inline_attachments']=$mail['inline_attachments'];

			//go_debug($mail);

			$response['inline_attachments']=array();
			foreach($mail['attachments'] as $attachment){
				if(!empty($attachment['replacement_url'])){
					$response['inline_attachments'][]=array(
							'id'=>$attachment['id'],
							'tmp_file'=>$attachment['tmp_file'],
							'imap_id'=>$attachment['imap_id'],
							'url'=>$attachment['replacement_url']);
				}
			}
			
			$response['body']=$mail['body'];


			echo json_encode($response);
			break;
	}
}

catch(Exception $e) {
	if(defined('IMAP_CONNECTED')) {
		$imap->close();
	}

	$response['feedback']=$e->getMessage();
	$response['success']=false;

	echo json_encode($response);
}

?>
