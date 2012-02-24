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

require('../../Group-Office.php');


if(!isset($_REQUEST['task']))
{
	$GO_CONFIG->log=true;
	go_log(LOG_DEBUG, "No task param: ".var_export($_REQUEST, true));
}


$GO_SECURITY->json_authenticate('email');


if(!isset($_REQUEST['task']))
{
	$GO_CONFIG->log=true;
	go_log(LOG_DEBUG, "No task param: ".var_export($_REQUEST, true));
}


if(!isset($_POST['password']) && $_REQUEST['task']!='attachments'){
	//close writing to session so other concurrent requests won't be locked out.
	session_write_close();
}

require_once ($GO_MODULES->modules['email']['class_path']."cached_imap.class.inc.php");
require_once ($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
require_once ($GO_LANGUAGE->get_language_file('email'));
require_once($GO_CONFIG->class_path.'filesystem.class.inc');

$imap = new cached_imap();
$email = new email();

function get_all_mailbox_nodes($account_id, $folder_id) {

	global $lang;

	$response=array();

	$email = new email();

	$email->get_folders($account_id, $folder_id);
	while($email->next_record()) {
		$pos = strrpos($email->f('name'), $email->f('delimiter'));

		if ($pos && $email->f('delimiter') != '') {
			$folder_name = substr($email->f('name'),$pos+1);
		}else {
			$folder_name = $email->f('name');
		}

		$response[] = array(
						'text'=>$folder_name,
						'id'=>'folder_'.$email->f('id'),
						'iconCls'=>'folderIcon',
						'account_id'=>$email->f('account_id'),
						'folder_id'=>$email->f('id'),
						'mailbox'=>$email->f('name'),
						'expanded'=>true,
						'canHaveChildren'=>$email->f('can_have_children'),
						'children'=>get_all_mailbox_nodes($account_id, $email->f('id')),
						'checked'=>$email->f('subscribed')=='1'
		);
	}
	return $response;
}

function get_mailbox_nodes($account_id, $folder_id, $user_id=0) {
	global $lang, $imap, $inbox_new, $GO_SECURITY;

	if(!$user_id)
	{
	    $user_id = $GO_SECURITY->user_id;
	}

	$email = new email();
	$email2 = new email();

	$response = array();

	

	$count = $email->get_subscribed($account_id, $folder_id);

	if($account_id>0 && !$count){
		//empty account folders try to sync

		go_debug('Syncing IMAP folders');
		global $account;
		$email->synchronize_folders($account, $imap);
		$count = $email->get_subscribed($account_id, $folder_id);
	}

	$cap = $imap->get_capability();

	while($record = $email->next_record())
	{
		unset($children);

		if($email->f('name') == 'INBOX') {
			if($count==1 && $record['can_have_children']==1) {
				$children=get_mailbox_nodes(0, $email->f('id'));
			}
			$folder_name = $lang['email']['inbox'];
		}else {
			$pos = strrpos($email->f('name'), $email->f('delimiter'));

			if ($pos && $email->f('delimiter') != '') {
				$folder_name = substr($email->f('name'),$pos+1);
			}else {
				$folder_name = $email->f('name');
			}
		}

		$parentExpanded=false;
		if(!isset($children))
		{
			if($email2->is_folder_expanded($email->f('id'), $user_id))
			{
				$parentExpanded=true;
				$children=get_mailbox_nodes(0, $email->f('id'), $user_id);
			}
		}

		//check for unread mail
		//$unseen = $email->f('unseen');

		//$status = $imap->status($email->f('name'), SA_UNSEEN);//+SA_MESSAGES+SA_RECENT);

		//first time the e-mail is loaded. Let's check the cache
		/*if(!isset($_POST['refresh']))
		{
		if($email->f('unseen')+$status->recent!= $status->unseen || $email->f('msgcount')+$status->recent!= $status->messages)
		{
		go_debug('Clearing dirty cache of folder: '.$email->f('name'));
		$imap->clear_cache($email->f('id'));
		}
		}*/

		$unseen = $imap->get_unseen($email->f('name'));

		if($email->f('name')=='INBOX') {
			$inbox_new += $unseen['count'];
		}

		if ($unseen['count'] > 0) {
			$status_html = '&nbsp;<span class="em-folder-status" id="status_'.$email->f('id').'">('.$unseen['count'].')</span>';
		}else {
			$status_html = '&nbsp;<span class="em-folder-status" id="status_'.$email->f('id').'"></span>';
		}

		switch($folder_name){
			case 'Sent':
				$folder_name=$lang['email']['sent'];
				break;
			case 'Trash':
				$folder_name=$lang['email']['trash'];
				break;
			case 'Drafts':
				$folder_name=$lang['email']['drafts'];
				break;
		}

		if($email2->get_subscribed(0, $email->f('id'))) {
			$response[] = array(
							'text'=>$folder_name.$status_html,
							'name'=>$folder_name,
							'id'=>'folder_'.$email->f('id'),
							'iconCls'=>'folder-default',
							'account_id'=>$email->f('account_id'),
							'folder_id'=>$email->f('id'),
							'canHaveChildren'=>$email->f('can_have_children'),
							'noSelect'=>$email->f('no_select'),
							'unseen'=>$unseen['count'],
							'mailbox'=>$email->f('name'),
							'expanded'=>isset($children),
							'children'=>isset($children) ? $children : null,
							'parentExpanded'=>$parentExpanded,
							'cls'=>$email->f('no_select')==1 ? 'em-tree-node-noselect' : null,
							'aclSupported'=>strpos($cap, 'ACL')
			);
		}else {
			$response[] = array(
							'text'=>$folder_name.$status_html,
							'name'=>$folder_name,
							'id'=>'folder_'.$email->f('id'),
							'iconCls'=>'folder-default',
							'account_id'=>$email->f('account_id'),
							'folder_id'=>$email->f('id'),
							'mailbox'=>$email->f('name'),
							'canHaveChildren'=>$email->f('can_have_children'),
							'noSelect'=>$email->f('no_select'),
							'unseen'=>$unseen['count'],
							'expanded'=>true,
							'children'=>isset($children) ? $children : array(),
							'parentExpanded'=>$parentExpanded,
							'cls'=>$email->f('no_select')==1 ? 'em-tree-node-noselect' : null,
							'aclSupported'=>strpos($cap, 'ACL')
			);
		}
	}
	return $response;
}


function find_alias_and_recipients() {
	global $GO_CONFIG, $email, $account_id, $response, $content, $task;

	require_once($GO_CONFIG->class_path.'mail/RFC822.class.inc');
	$RFC822 = new RFC822();

	$aliases = array();
	$email->get_aliases($account_id, true);
	while($alias=$email->next_record()) {
		$aliases[strtolower($alias['email'])]=$alias['id'];
	}

	$fill_to = $task=='reply_all' || $task=='opendraft';

	//add all recievers from this email
	if (isset($content["to"])) {
		$first = !empty($response['data']['to']);
		for ($i=0;$i<sizeof($content["to"]);$i++) {
			$address = strtolower($content["to"][$i]['email']);
			if (!empty($email)) {
				if(isset($aliases[$address])) {
					$response['data']['alias_id']=$aliases[$address];
				}
 
				if($fill_to && (!isset($aliases[$address]) || $task=='opendraft') && !empty($content["to"][$i]['email']) && strpos($response['data']['to'], $content["to"][$i]['email'])===false) {
					if (!$first) {
						$first = true;
					}else {
						$response['data']['to'] .= ',';
					}
					$response['data']['to'] .= $RFC822->write_address($content["to"][$i]['name'],$content["to"][$i]['email']);
				}
			}
		}
	}

	if (isset($content["cc"]) && count($content["cc"]) > 0) {
		$response['data']['cc']='';
		$first=false;
		for ($i=0;$i<sizeof($content["cc"]);$i++) {
			$address = strtolower($content["cc"][$i]['email']);
			if (!empty($address)) {
				if(isset($aliases[$address])) {
					$response['data']['alias_id']=$aliases[$address];
				}

				if($fill_to && (!isset($aliases[$address]) || $task=='opendraft')) {
					if (!$first) {
						$first = true;
					}else {
						$response['data']['cc'] .= ',';
					}
					$response['data']['cc'] .= $RFC822->write_address($content["cc"][$i]['name'],$content["cc"][$i]['email']);
				}
			}
		}
	}

	if (isset($content["bcc"]) && count($content["bcc"]) > 0) {
		$response['data']['bcc']='';
		$first=false;
		for ($i=0;$i<sizeof($content["bcc"]);$i++) {
			$address = strtolower($content["bcc"][$i]['email']);
			if (!empty($address)) {
				if(isset($aliases[$address])) {
					$response['data']['alias_id']=$aliases[$address];
				}

				if($fill_to && (!isset($aliases[$address]) || $task=='opendraft')) {
					if (!$first) {
						$first = true;
					}else {
						$response['data']['bcc'] .= ',';
					}
					$response['data']['bcc'] .= $RFC822->write_address($content["bcc"][$i]['name'],$content["bcc"][$i]['email']);
				}
			}
		}
	}
}



try {

	$task = $_REQUEST['task'];
	if($task == 'reply' || $task =='reply_all' || $task == 'forward' || $task=='opendraft' || $task=='template') {
		if(!empty($_POST['uid'])) {
			/*
			 * Regular reply in the e-mail client
			*/

			$account_id = $_POST['account_id'];
			$uid = $_POST['uid'];
			$mailbox = $_POST['mailbox'];

			$account = $email->get_account($account_id);
			$imap->set_account($account, $mailbox);

			if(!$account) {
				throw new DatabaseSelectException();
			}

			$content = $imap->get_message_with_body($uid, $task=='forward' || $task=='opendraft', true, false, $_POST['content_type']!='html', $_POST['content_type']=='html');
			if($_POST['content_type']!='html') {
				$content['body']=$content['plain_body'];
			}else {
				$content['body']=$content['html_body'];
			}
			unset($content['html_body'], $content['plain_body']);
		}elseif($task!='template'){
			/*
			 * Reply / forward for a linked message. We need the mailings module to fetch the message.
			*/
			$id = isset($_REQUEST['id']) ? ($_REQUEST['id']) : 0;
			$path = isset($_REQUEST['path']) ? ($_REQUEST['path']) : "";
			$part_number = isset($_REQUEST['part_number']) ? ($_REQUEST['part_number']) : "";
			
			
			if(isset($_REQUEST['file_id'])){
				require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
				$files = new files();
				$file = $files->get_file($_REQUEST['file_id']);
				$path = $files->build_path($file['folder_id']).'/'.$file['name'];
				
			}
				

			require_once($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
			$ml = new mailings();

			$content = $ml->get_message_for_client($id, $path, $part_number, $task=='forward', true);
		}

		//go_debug($content);

		switch($task) {
			case "reply":
			case "reply_all":				
				$response['data']['in_reply_to']=$content['message-id'];
				
				$response['data']['to'] = $content["reply-to"];
				if(stripos($content['subject'],'Re:')===false) {
					$response['data']['subject'] = 'Re: '.$content['subject'];
				}else {
					$response['data']['subject'] = $content['subject'];
				}
				break;

			case "opendraft":
			case "forward":

				if($task == 'opendraft') {
					$response['data']['to']='';//$content['to_string'];
					$response['data']['subject'] = $content['subject'];

				}else {
					if(stripos($content['subject'],'Fwd:')===false) {
						$response['data']['subject'] = 'Fwd: '.$content['subject'];
					}else {
						$response['data']['subject'] = $content['subject'];
					}
				}

				//go_debug($content['attachments']);

				//reattach non-inline attachments
				foreach($content['attachments'] as $attachment) {
					if(empty($attachment['replacement_url']) && !empty($attachment['tmp_file'])){
						$extension = File::get_extension($attachment['name']);
						$response['data']['attachments'][]=array(
											'tmp_name'=>$attachment['tmp_file'],
											'name'=>$attachment['name'],
											'size'=>$attachment["size"],
											'type'=>File::get_filetype_description($extension),
											'extension'=>$extension,
											'human_size'=>Number::format_size($attachment['size'])

							);
					}
				}
				break;
		}

		//if(!empty($uid))
			find_alias_and_recipients();

		if(isset($content)){
			$response['data']['body']=$content['body'];

			if($GO_MODULES->has_module('gnupg')) {
				require_once($GO_MODULES->modules['gnupg']['class_path'].'gnupg.class.inc.php');
				$gnupg = new gnupg();
				$passphrase = !empty($_SESSION['GO_SESSION']['gnupg']['passwords'][$content['sender']]) ? $_SESSION['GO_SESSION']['gnupg']['passwords'][$content['sender']] : '';
				$response['data']['body'] = $gnupg->replace_encoded($response['data']['body'],$passphrase,false);
			}

			if($task=='forward') {
				$om_to = $content["to_string"];
				$om_cc = $content["cc_string"];

				if($_POST['content_type']== 'html') {
					$header_om  = '<br /><br /><font face="verdana" size="2">'.$lang['email']['original_message']."<br />";
					$header_om .= "<b>".$lang['email']['subject'].":&nbsp;</b>".htmlspecialchars($content['subject'], ENT_QUOTES, 'UTF-8')."<br />";
					$header_om .= '<b>'.$lang['email']['from'].": &nbsp;</b>".htmlspecialchars($content['full_from'], ENT_QUOTES, 'UTF-8')."<br />";
					$header_om .= "<b>".$lang['email']['to'].":&nbsp;</b>".htmlspecialchars($om_to, ENT_QUOTES, 'UTF-8')."<br />";
					if(!empty($om_cc)) {
						$header_om .= "<b>CC:&nbsp;</b>".htmlspecialchars($om_cc, ENT_QUOTES, 'UTF-8')."<br />";
					}

					$header_om .= "<b>".$lang['common']['date'].":&nbsp;</b>".date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'],$content["udate"])."<br />";

					$header_om .= "</font><br /><br />";

					$response['data']['body']=$header_om.$response['data']['body'];
					//$response['data']['body'] = '<br /><blockquote style="border:0;border-left: 2px solid #22437f; padding:0px; margin:0px; padding-left:5px; margin-left: 5px; ">'.$header_om.$response['data']['body'].'</blockquote>';
				}else {
					$header_om  = "\n\n".$lang['email']['original_message']."\n";
					$header_om .= $lang['email']['subject'].": ".$content['subject']."\n";
					$header_om .= $lang['email']['from'].": ".$content['full_from']."\n";
					$header_om .= $lang['email']['to'].": ".$om_to."\n";
					if(!empty($om_cc)) {
						$header_om .= "CC: ".$om_cc."\n";
					}

					$header_om .= $lang['common']['date'].": ".date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'],$content["udate"])."\n";
					$header_om .= "\n\n";

					$response['data']['body'] = str_replace("\r",'',$response['data']['body']);
					//$response['data']['body'] = '> '.str_replace("\n","\n> ",$response['data']['body']);

					$response['data']['body'] = $header_om.$response['data']['body'];
				}
			}elseif($task=='reply' || $task=='reply_all') {
				$header_om = sprintf($lang['email']['replyHeader'],
								$lang['common']['full_days'][date('w', $content["udate"])],
								date($_SESSION['GO_SESSION']['date_format'],$content["udate"]),
								date($_SESSION['GO_SESSION']['time_format'],$content["udate"]),
								$content['from']);

				if($_POST['content_type']== 'html') {

					$response['data']['body'] = '<br /><br />'.htmlspecialchars($header_om, ENT_QUOTES, 'UTF-8').'<br /><blockquote style="border:0;border-left: 2px solid #22437f; padding:0px; margin:0px; padding-left:5px; margin-left: 5px; ">'.$response['data']['body'].'</blockquote>';
				}else {
					$response['data']['body'] = str_replace("\r",'',$response['data']['body']);
					$response['data']['body'] = '> '.str_replace("\n","\n> ",$response['data']['body']);

					$response['data']['body'] = "\n\n".$header_om."\n".$response['data']['body'];
				}
			}
		}else
		{
			$response['data']['body']='';
			$response['data']['to']=$_REQUEST['to'];
			$response['data']['attachments']=$content['attachments']=array();
		}



		if(isset($_POST['template_id']) && $_POST['template_id']>0 && $task!='opendraft') {
			$template_id = ($_POST['template_id']);
			$to = isset($response['data']['to']) ? $response['data']['to'] : '';

			$contact_id = isset($_POST['contact_id']) ? $_POST['contact_id'] : 0;
			$template = load_template($template_id, $to, isset($_POST['mailing_group_id']) && $_POST['mailing_group_id']>0, $contact_id);

			$response['data']['body'] = $template['data']['body'].$response['data']['body'];
			$content['attachments']=array_merge($content['attachments'], $template['data']['attachments']);
		}

		$response['data']['inline_attachments']=array();
		foreach($content['attachments'] as $attachment){
			if(!empty($attachment['replacement_url'])){
				$response['data']['inline_attachments'][]=array(
						'id'=>$attachment['id'],
						'tmp_file'=>$attachment['tmp_file'],
						'url'=>$attachment['replacement_url']);
			}else
			{
				///$response['data']['attachments'][]=$attachment;
			}
		}

		if(isset($response['data']['attachments']))
			$response['data']['attachments']=$imap->remove_inline_images($response['data']['attachments']);

		if($_POST['content_type']=='plain') {
			$response['data']['textbody']=$response['data']['body'];
			unset($response['data']['body']);
		}

		$response['success']=true;

	}else {
		switch($_REQUEST['task']) {
			
			case 'usernames':
				
				$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : 0;
				$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : 30;
				$query = !empty($_POST['query']) ? '%'.trim($_POST['query']).'%' : '';
				
				$response['total']=$email->get_usernames($GO_SECURITY->user_id, $query, $start, $limit);
				$response['results']=array();
				
				while($r=$email->next_record()){
					$response['results'][]=$r;
				}				
				
				break;


			case 'init_composer':

				if($GO_MODULES->has_module('mailings')){
					require_once($GO_MODULES->modules['mailings']['class_path'].'templates.class.inc.php');
					$tp = new templates();

					$tp->get_templates_json($response['templates']);
				}

				

				$response['aliases']['total'] = $email->get_all_aliases($GO_SECURITY->user_id);
				$response['aliases']['results']=array();
				while($alias = $email->next_record()) {
					$alias['name']='"'.$alias['name'].'" <'.$alias['email'].'>';
					$alias['html_signature']=String::text_to_html($email->f('signature'));
					$alias['plain_signature']=$email->f('signature');
					unset($alias['signature']);
					$response['aliases']['results'][] = $alias;
				}
				
				$GO_EVENTS->fire_event('init_composer', array(&$response, $email));

				break;



			case 'icalendar_attachment':
				if(!isset($GO_MODULES->modules['calendar']) || !$GO_MODULES->modules['calendar']['read_permission']) {
					throw new Exception(sprintf($lang['common']['moduleRequired'], $lang['email']['calendar']));
				}

				$account = $imap->open_account($_REQUEST['account_id'], $_REQUEST['mailbox']);
				$data = $imap->get_message_part_decoded($_REQUEST['uid'], $_REQUEST['imap_id'], $_REQUEST['encoding']);
				$imap->disconnect();

				require_once($GO_CONFIG->class_path.'Date.class.inc.php');
				require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc.php');
				$cal = new calendar();
				require_once($GO_CONFIG->class_path.'ical2array.class.inc');
				$cal->ical2array = new ical2array();

				$vcalendar = $cal->ical2array->parse_string($data);

				$event=false;
				while($object = array_shift($vcalendar[0]['objects'])) {
					if($object['type'] == 'VEVENT') {
						//go_debug($object);
						$event = $cal->get_event_from_ical_object($object);
						break;
					}
				}

				if(!$event) {
					throw new Exception($lang['common']['selectError']);
				}
				$response=$cal->event_to_json_response($event);
				//go_debug($response);
				$response['success']=true;
				break;

			case 'attachments':

				while($file = array_shift($_SESSION['GO_SESSION']['just_uploaded_attachments'])) {
					$response['results'][]=array(
									'tmp_name'=>$file,
									'name'=>utf8_basename($file),
									'size'=>filesize($file),
									'human_size'=>Number::format_size(filesize($file)),
									'extension'=>File::get_extension($file),
									'type'=>File::get_filetype_description(File::get_extension($file))
					);
				}
				$response['total']=count($response['results']);

				break;

			/*case 'template':
				$template_id=$_REQUEST['template_id'];
				$to=$_REQUEST['to'];
				$contact_id = isset($_POST['contact_id']) ? $_POST['contact_id'] : 0;

				$response = load_template($template_id, $to, isset($_POST['mailing_group_id']) && $_POST['mailing_group_id']>0,$contact_id);

				if($_POST['content_type']=='plain') {
					$response['data']['textbody']=$response['data']['body'];
					unset($response['data']['body']);
				}

				$response['success']=true;
				break;*/

			case 'filters':
				if(isset($_POST['delete_keys'])) {
					$filters = json_decode(($_POST['delete_keys']));

					foreach($filters as $filter_id) {
						$email->delete_filter($filter_id);
					}
					$response['deleteSuccess']=true;
				}
				$response['total']=$email->get_filters(($_POST['account_id']));
				$response['results']=array();
				while($email->next_record(DB_ASSOC)) {
					$response['results'][] = $email->record;
				}

				break;

			case 'filter':

				$response['success']=false;
				$response['data']=$email->get_filter(($_POST['filter_id']));
				if($response['data']) {
					$response['success']=true;
				}

				break;

			case 'message_attachment':
				$account = $imap->open_account($_REQUEST['account_id'], $_REQUEST['mailbox']);
				$data = $imap->get_message_part_decoded($_REQUEST['uid'], $_REQUEST['imap_id'], $_REQUEST['encoding'], $_REQUEST['charset']);

				$response=array();
				$inline_url = $GO_MODULES->modules['email']['url'].'mimepart.php?account_id='.$_REQUEST['account_id'].'&mailbox='.urlencode(($_REQUEST['mailbox'])).'&uid='.($_REQUEST['uid']).'&imap_id='.$_REQUEST['imap_id'].'&encoding='.urlencode($_REQUEST['encoding']);

				require_once($GO_CONFIG->class_path.'mail/Go2Mime.class.inc.php');
				$go2mime = new Go2Mime();

				$response['blocked_images']=0;

				$response = array_merge($response, $go2mime->mime2GO($data, $inline_url,false,false,''));

				break;

			case 'message':

				require_once($GO_CONFIG->class_path.'mail/RFC822.class.inc');
				$RFC822 = new RFC822();

				$account_id = $_REQUEST['account_id'];
				$mailbox = $_REQUEST['mailbox'];
				$uid = $_REQUEST['uid'];

				//$account = $imap->open_account($account_id, $mailbox);
				$account = $email->get_account($account_id);
				$imap->set_account($account, $mailbox);

				//$_POST['plaintext']=1;

				$response = $imap->get_message_with_body($uid, !empty($_POST['create_temporary_attachments']),false,false,!empty($_POST['plaintext']),empty($_POST['plaintext']));

				//go_debug($response);

				if(!empty($_POST['plaintext'])) {
					$response['body']=$response['plain_body'];
				}else {

					$response['attachments']=$imap->remove_inline_images($response['attachments']);

					$response['body']=$response['html_body'];
				}
				unset($response['html_body'], $response['plain_body']);
			
				if(!empty($response['new'])) {
					if($imap->set_unseen_cache(array($uid), false)) {
						if(!empty($response['from_cache']) || stripos($account['host'],'gmail')!==false) {
							$imap->open();
							$imap->set_message_flag(array($uid), "\Seen");
						}
					}
				}
				$response['account_id']=$account_id;

				$response['sender_contact_id']=0;
				if(!empty($_POST['get_contact_id']) && $GO_MODULES->has_module('addressbook')) {
					require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc.php');
					$ab = new addressbook();

					$contact = $ab->get_contact_by_email($response['sender'], $GO_SECURITY->user_id);
					$response['company']= $contact['company_id'] > 0 ? $ab->get_company($contact['company_id']) : false;
					$response['sender_contact_id']=intval($contact['id']);

					if($response['sender_contact_id']) {
						$contact['contact_name'] = String::format_name($contact);
						$response['contact']=$contact;
					}
				}

				$email_sender = $response['sender'];
				$response['date']=date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], $response['udate']);
				$response['blocked_images']=0;
				$response['full_from']=htmlspecialchars($response['full_from'], ENT_COMPAT, 'UTF-8');
				$response['sender']=htmlspecialchars($response['sender'], ENT_COMPAT, 'UTF-8');
				$response['from']=htmlspecialchars($response['from'], ENT_COMPAT, 'UTF-8');
				$response['subject']=htmlspecialchars($response['subject'], ENT_COMPAT, 'UTF-8');
				$response['reply-to']=htmlspecialchars($response['reply-to'], ENT_COMPAT, 'UTF-8');
				for($i=0;$i<count($response['to']);$i++) {
					$response['to'][$i]=array(
									'email'=>htmlspecialchars($response['to'][$i]['email'], ENT_COMPAT, 'UTF-8'),
									'name'=>htmlspecialchars($response['to'][$i]['name'], ENT_COMPAT, 'UTF-8')
					);
				}
				for($i=0;$i<count($response['cc']);$i++) {
					$response['cc'][$i]=array(
									'email'=>htmlspecialchars($response['cc'][$i]['email'], ENT_COMPAT, 'UTF-8'),
									'name'=>htmlspecialchars($response['cc'][$i]['name'], ENT_COMPAT, 'UTF-8')
					);
				}
				for($i=0;$i<count($response['bcc']);$i++) {
					$response['bcc'][$i]=array(
									'email'=>htmlspecialchars($response['bcc'][$i]['email'], ENT_COMPAT, 'UTF-8'),
									'name'=>htmlspecialchars($response['bcc'][$i]['name'], ENT_COMPAT, 'UTF-8')
					);
				}

				//$response['size']=Number::format_size($response['size']);

				if($GO_MODULES->has_module('gnupg')) {
					require_once($GO_MODULES->modules['gnupg']['class_path'].'gnupg.class.inc.php');
					$gnupg = new gnupg();
					$passphrase = !empty($_SESSION['GO_SESSION']['gnupg']['passwords'][$response['sender']]) ? $_SESSION['GO_SESSION']['gnupg']['passwords'][$response['sender']] : '';
					if(isset($_POST['password'])) {
						$passphrase=$_SESSION['GO_SESSION']['gnupg']['passwords'][$response['sender']]=$_POST['password'];
						//$passphrase=$_POST['passphrase'];
					}
					try {
						$response['body'] = $gnupg->replace_encoded($response['body'],$passphrase);
					}
					catch(Exception $e) {
						$m = $e->getMessage();

						if(strpos($m, 'bad passphrase')) {
							$response['askPassword']=true;
							if(isset($_POST['password'])) {
								throw new Exception('Wrong password!');
							}
						}else {
							throw new Exception($m);
						}
					}
				}
				$block_images=true;
				if(!empty($_POST['unblock'])) {
					$block_images=false;
				}elseif($GO_MODULES->has_module('addressbook')) {
					require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc.php');
					$ab = new addressbook();

					$contact = $ab->get_contact_by_email($response['sender'], $GO_SECURITY->user_id);
					$block_images = !is_array($contact);
				}

				if($block_images) {

					$block_url = 'about:blank';//$GO_CONFIG->host.'ext/resources/images/default/s.gif';
					$response['body'] = preg_replace("/<([^a]{1})([^>]*)(https?:[^>'\"]*)/iu", "<$1$2".$block_url, $response['body'], -1, $response['blocked_images']);
				}

				$response['iCalendar'] = array();
				for($i=0; $i<count($response['attachments']); $i++)
				{
					$attachment = $response['attachments'][$i];
					if(($attachment['subtype'] == 'calendar') && ($attachment['extension'] == 'ics'))
					{
						if(!isset($GO_MODULES->modules['calendar']) || !$GO_MODULES->modules['calendar']['read_permission']) {
							throw new Exception(sprintf($lang['common']['moduleRequired'], $lang['email']['calendar']));
						}

						$data = $imap->get_message_part_decoded($uid, $attachment['imap_id'], $attachment['encoding']);

						require_once($GO_CONFIG->class_path.'Date.class.inc.php');
						require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc.php');
						$cal = new calendar();
						require_once($GO_CONFIG->class_path.'ical2array.class.inc');
						$ical2array = new ical2array();
						require_once($GO_LANGUAGE->get_language_file('calendar'));

						$vcalendar = $ical2array->parse_string($data);
						while($object = array_shift($vcalendar[0]['objects']))
						{
							if($object['type'] == 'VEVENT')
							{
								$cal_event = $cal->get_event_from_ical_object($object);								
							}
						}
						if(!isset($cal_event))
						{
							throw new Exception($lang['common']['selectError']);
						}

						$event = $cal->get_event_by_uuid($cal_event['uuid'], $GO_SECURITY->user_id);

						$method = isset($vcalendar[0]['METHOD']['value']) ? $vcalendar[0]['METHOD']['value'] : '';											
						switch($method)
						{
							case 'REPLY':
							case 'REQUEST':

								go_debug($method);
								go_debug('UID: '.$cal_event['uuid']);

								$event_declined = $cal->is_event_declined($cal_event['uuid'], $account['email']);
							
								// reply to an invitation of an existing event							
								if($event)
								{
									if(isset($cal_event['participants']))
									{
										$status_id=0;
										$saved_participant = false;
										//var_dump($account['email']);
										//exit();
										foreach($cal_event['participants'] as $participant_email=>$participant)
										{
											if($participant_email == $account['email'])
											{
												$saved_participant = $cal->is_participant($event['id'], $participant_email);
												$status_id = $participant['status'];
											}
										}

										// check if event has to be updated
//										if($saved_participant)
//										{
											$response['iCalendar']['invitation'] = array(
												'uuid' => $cal_event['uuid'],
												'imap_id' => $attachment['imap_id'],
												'encoding' => $attachment['encoding'],
												'event_id' => $event['id'],
												'email_sender' => $email_sender,
												'email' => $account['email'],
												'event_declined' => $event_declined,
												'last_modified' => $cal_event['mtime'],
												'status_id' => $status_id,
												'is_update'=>$method != 'REQUEST',
												'is_invitation'=>$method == 'REQUEST',
												'is_cancellation'=>false
											);


											if($saved_participant && $cal_event['mtime'] > $saved_participant['last_modified'])
												$response['iCalendar']['feedback'] = $lang['email']['iCalendar_update_available'];
											else
												$response['iCalendar']['feedback'] = $lang['email']['iCalendar_update_old'];
											
//										}else
//										{
//											//$response['iCalendar']['invitation']=array();
//										}
										
										$response['body'] = $cal->event_to_html($cal_event, false, true).'<hr />'.$response['body'];
									}else
									{
										throw new Exception($lang['common']['selectError']);
									}									
								}else
								{
									if($method == 'REQUEST')
									{										
										// invitation to a new event										
										$response['iCalendar']['feedback'] = ($event_declined) ? $lang['email']['iCalendar_event_invitation_declined'] : $lang['email']['iCalendar_event_invitation'];
										$response['iCalendar']['invitation'] = array(
											'uuid' => $cal_event['uuid'],
											'imap_id' => $attachment['imap_id'],
											'encoding' => $attachment['encoding'],
											'email_sender' => $email_sender,
											'email' => $account['email'],
											'event_declined' => $event_declined,
											'is_update'=>$method != 'REQUEST',
											'is_invitation'=>$method == 'REQUEST',
											'is_cancellation'=>false
										);
									}else
									{
										$response['iCalendar']['feedback'] = $lang['email']['iCalendar_event_not_found'];
									}

									$response['body'] = $cal->event_to_html($cal_event, false, true).'<hr />'.$response['body'];
								}
								break;						

							case 'CANCEL':
								
								if($event)
								{
									$response['iCalendar']['feedback'] = $lang['email']['iCalendar_event_cancelled'];
									
									$response['iCalendar']['invitation'] = array(
											'uuid' => $cal_event['uuid'],
											'imap_id' => $attachment['imap_id'],
											'encoding' => $attachment['encoding'],
											'event_id' => $event['id'],
											'email_sender' => $email_sender,
											'email' => $account['email'],
											'event_declined' => false,
											'last_modified' => $cal_event['mtime'],											
											'is_update'=>false,
											'is_invitation'=>false,
											'is_cancellation'=>true
										);

									$response['body'] = $cal->event_to_html($event, false, true).'<hr />'.$response['body'];
								}else
								{
									$response['iCalendar']['feedback'] = $lang['email']['iCalendar_event_not_found'];

									$response['body'] = $cal->event_to_html($cal_event, false, true).'<hr />'.$response['body'];
								}
							
								break;

							default:

								$response['iCalendar']['feedback'] = 'No method given. OOPS!';

								break;						
						}
					}
				}				

				break;

			case 'messages':

				$account_id = isset ($_REQUEST['account_id']) ? $_REQUEST['account_id'] : 0;
				$mailbox = isset ($_REQUEST['mailbox']) ? ($_REQUEST['mailbox']) : 'INBOX';
				$query = isset($_POST['query']) ? trim($_POST['query']) : '';
				$unread = isset($_POST['unread']) && ($_POST['unread'] == 'true') ? true : false;

				if($unread) {
					$query = str_replace(array('UNSEEN', 'SEEN'), array('', ''), $query);
					if ($query == ''){$query .= 'UNSEEN';}else{$query.= ' UNSEEN';}
				}

				$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : 0;
				$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : 30;


				$account = $imap->open_account($account_id, $mailbox);

				//$account = $email->get_account($account_id);
				//$imap->set_account($account, $mailbox);

				if(!empty($_POST['refresh'])) {
					$imap->clear_cache($_POST['folder_id']);
				}

				$response['trash_folder']=$account['trash'];

				$response['trash']=!empty($account['trash']) && strpos($mailbox, $account['trash'])!==false;
				$response['drafts']=!empty($account['drafts']) && strpos($mailbox, $account['drafts'])!==false;
				$response['sent']=!empty($account['sent']) && strpos($mailbox, $account['sent'])!==false;

				if(isset($_POST['delete_keys'])) {
					$messages = json_decode($_POST['delete_keys'], true);

					if($imap->is_imap() && !empty($account['trash']) && $mailbox != $account['trash']) {
						$imap->set_message_flag($messages, "\Seen");
						$response['deleteSuccess']=$imap->move($messages,$account['trash']);
					}else {

						$response['deleteSuccess']=$imap->delete($messages);
					}
					if(!$response['deleteSuccess']) {
						$lasterror = $imap->last_error();
						if(stripos($lasterror,'quota')!==false) {
							$response['deleteFeedback']=$lang['email']['quotaError'];
						}else {
							$response['deleteFeedback']=$lang['common']['deleteError'].":\n\n".$lasterror."\n\n".$lang['email']['disable_trash_folder'];
						}
					}
				}

				if(isset($_POST['action'])) {
					$messages = json_decode($_POST['messages']);
					switch($_POST['action']) {
						case 'move':
							$response['success']=$imap->move($messages, $_REQUEST['to_mailbox']);
							$nocache=true;
							break;
					}
				}

				$sort=isset($_POST['sort']) ? $_POST['sort'] : 'from';

				switch($sort) {
					case 'from':
						$sort_field='FROM';
						break;
					case 'date':
						$sort_field='DATE';
						break;
					case 'subject':
						$sort_field='SUBJECT';
						break;
					case 'size':
						$sort_field='SIZE';
						break;
					default:
						$sort_field='DATE';
				}


				if(($response['sent'] || $response['drafts']) && $sort_field=='FROM') {
					$sort_field='TO';
				}

				$sort_order=isset($_POST['dir']) && $_POST['dir']=='ASC' ? 0 : 1;

				//apply filters
				if(strtoupper($mailbox)=='INBOX') {
					$filters = array();

					//if there are new messages get the filters
					$email->get_filters($account['id']);
					while ($email->next_record()) {
						$filter["field"] = $email->f("field");
						$filter["folder"] = $email->f("folder");
						$filter["keyword"] = $email->f("keyword");
						$filter['mark_as_read'] = ($email->f('mark_as_read') == '1');
						$filters[] = $filter;
					}
					$imap->set_filters($filters);
				}

				$day_start = mktime(0,0,0);
				$day_end = mktime(0,0,0,date('m'),date('d')+1);

				$messages = $imap->get_message_headers_set($start, $limit, $sort_field , $sort_order, $query);

				$response['results']=array();

				require_once($GO_CONFIG->class_path.'mail/RFC822.class.inc');
				$RFC822 = new RFC822();

				foreach($messages as $message) {
					//$message = $messages[$uid];
					unset($message['content-type'], $message['reply-to'], $message['content-transfer-encoding'], $message['disposition-notification-to']);
					if($message['udate']>$day_start && $message['udate']<$day_end) {
						$message['date'] = date($_SESSION['GO_SESSION']['time_format'],$message['udate']);
					}else {
						$message['date'] = date($_SESSION['GO_SESSION']['date_format'],$message['udate']);
					}

					if(empty($message['subject'])) {
						$message['subject']=$lang['email']['no_subject'];
					}

					$message['from'] = ($response['sent'] || $response['drafts']) ? $message['to'] : $message['from'];

					$address = $RFC822->parse_address_list($message['from']);

					$readable_addresses=array();
					for($i=0;$i<count($address);$i++) {
						if(!empty($address[$i]['personal'])) {
							$readable_addresses[]=$address[$i]['personal'];
						}else if(!empty($address[$i])) {
							$readable_addresses[]=$address[$i]['email'];
						}
					}
					$message['from']=implode(',', $readable_addresses);
					$message['from']=htmlspecialchars($message['from'], ENT_QUOTES, 'UTF-8');

					$message['sender'] = empty($address[0]) ? '' : $address[0]['email'];
					$message['subject']=htmlspecialchars($message['subject'], ENT_QUOTES, 'UTF-8');

					if(empty($message['from'])) {
						if($mailbox==$account['drafts']) {
							$message['from'] = $lang['email']['no_recipients_drafts'];
						}else {
							$message['from'] = $lang['email']['no_recipients'];
						}
					}
					$response['results'][]=$message;
				}

				$response['folder_id']=$imap->folder['id'];
				$response['total'] = $imap->sort_count;//selected_mailbox['messages'];

				foreach($imap->touched_folders as $touched_folder) {
					if($touched_folder==$mailbox) {
						//$unseen = $imap->get_unseen();
						//unseen property is set by cached_imap::sort_mailbox() because it needs it to determine if cache is dirty.
						$response['unseen'][$imap->folder['id']]=$imap->selected_mailbox['unseen'];
					}else {

						$folder = $email->get_folder($account_id, $touched_folder);
						$imap->select_mailbox($touched_folder);

						$unseen = $imap->get_unseen();
						$response['unseen'][$imap->folder['id']]=$unseen['count'];
					}
				}

				break;

			case 'tree':
				$email = new email();
				//$account_id=isset($_REQUEST['account_id']) ? ($_REQUEST['account_id']) : 0;
				//$folder_id=isset($_REQUEST['folder_id']) ? ($_REQUEST['folder_id']) : 0;

				if(isset($_REQUEST['node']) && strpos($_REQUEST['node'],'_')) {
					$node = explode('_',$_REQUEST['node']);
					$node_type=$node[0];
					$node_id=$node[1];

					if($node_type == 'folder' && !$email->is_folder_expanded($node_id, $GO_SECURITY->user_id))
					{
						$email->update_folder_state($node_id, $GO_SECURITY->user_id, true);
					}
					if($node_type == 'account' && !$email->is_account_expanded($node_id, $GO_SECURITY->user_id))
					{
						$email->update_account_state($node_id, $GO_SECURITY->user_id, true);
					}
				}else {
					$node_type='root';
					$node_id=0;
				}

				$response=array();
				if($node_type=='root') {
					$email2 = new email();
					$count = $email2->get_accounts($GO_SECURITY->user_id);
					//go_log(LOG_DEBUG, $count);
					while($email2->next_record()) {
						try{
							$account = $imap->open_account($email2->f('id'), 'INBOX', false);
						} catch(Exception $e){
							$account=false;
							$error = $email->human_connect_error($e->getMessage());
						}

						$usage = '';
						$inbox_new=0;
						if($account) {
							if(!empty($_POST['refresh'])) {
								go_debug('refreshing');
								$email->synchronize_folders($account, $imap);
								$imap->clear_cache();
							}

							$text = $email2->f('email');

							$quota = $imap->get_quota();
							if(isset($quota['usage'])) {
								if(!empty($quota['limit'])) {
									$percentage = ceil($quota['usage']*100/$quota['limit']);
									$usage = sprintf($lang['email']['usage_limit'], $percentage.'%', Number::format_size($quota['limit']*1024));
								}	else {
									$usage = sprintf($lang['email']['usage'], Number::format_size($quota['usage']*1024));
								}
							}

							$account_expanded = $email->is_account_expanded($email2->f('id'), $GO_SECURITY->user_id);
							$children = ($account_expanded) ? get_mailbox_nodes($email2->f('id'), 0) : false;

							$imap->disconnect();
						}else {
							$text = $email2->f('email').' ('.$lang['common']['error'].')';
							$children=array();
							$account_expanded=true;
						}

						$node =  array(
										'text'=>$text,
										'name'=>$email2->f('email'),
										'id'=>'account_'.$email2->f('id'),
										'iconCls'=>'folder-account',
										'expanded'=>$account_expanded,
										'account_id'=>$email2->f('id'),
										'folder_id'=>0,
										'mailbox'=>'INBOX',
										'children'=>$children,
										'canHaveChildren'=>$email2->f('type')=='imap',
										'inbox_new'=>$inbox_new,
										'usage'=>$usage,
										'parentExpanded'=>$account_expanded
						);
						if(!$account) {
							$node['qtipCfg'] = array('title'=>$lang['common']['error'], 'text' =>htmlspecialchars($error, ENT_QUOTES, 'UTF-8'));
						}

						$response[]=$node;
					}
				}elseif($node_type=='account') {
					$account = $imap->open_account($node_id);
					$response = get_mailbox_nodes($node_id, 0);
				}	else {
					$folder_id=$node_id;

					$folder = $email->get_folder_by_id($folder_id);
					$account = $imap->open_account($folder['account_id']);

					$response = get_mailbox_nodes(0, $folder_id);
				}
				break;

			case 'tree-edit':
				$email = new email();
				$email2 = new email();

				$account_id = ($_POST['account_id']);
				if(isset($_REQUEST['node']) && strpos($_REQUEST['node'],'_')) {
					$node = explode('_',$_REQUEST['node']);
					$folder_id=$node[1];
				}else {
					$folder_id=0;
				}

				$account = $imap->open_account($account_id);
				if($folder_id==0)
					$email->synchronize_folders($account, $imap);


				$response = get_all_mailbox_nodes($account_id, $folder_id);
				break;

			case 'accounts':

				require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
				$GO_USERS = new GO_USERS();

				if(isset($_POST['delete_keys'])) {
					$response['deleteSuccess']=true;
					try {
						$deleteAccounts = json_decode(($_POST['delete_keys']));

						foreach($deleteAccounts as $account_id) {
							$account = $email->get_account($account_id);

							if(!$GO_SECURITY->has_admin_permission($GO_SECURITY->user_id) && (!$GO_MODULES->modules['email']['write_permission'] || $account['user_id']!=$GO_SECURITY->user_id)) {
								throw new AccessDeniedException();
							}

							$email->delete_account($account_id);
						}
					}catch(Exception $e) {
						$response['deleteSuccess']=false;
						$response['deleteFeedback']=$e->getMessage();
					}
				}
				$response['results']=array();

				//$user_id = !isset($_POST['personal_only']) && $GO_SECURITY->has_admin_permission($GO_SECURITY->user_id) ? 0 : $GO_SECURITY->user_id;

				
				
				$sort = isset ( $_POST['sort'] ) ? $_POST['sort'] : 'email';
				$dir = isset ( $_POST['dir'] ) ? $_POST['dir'] : 'ASC';

				$start = isset ( $_POST['start'] ) ? $_POST['start'] : 0;
				$limit = isset ( $_POST['limit'] ) ? $_POST['limit'] : 0;

				$query = !empty($_POST['query']) ? '%'.$_POST['query'].'%' : '';

				$user_id = $GO_SECURITY->has_admin_permission($GO_SECURITY->user_id) ? 0 : $GO_SECURITY->user_id;

				$response['total'] = $email->get_accounts($user_id,$start, $limit, $sort, $dir,'write', $query);

				while($record = $email->next_record()) {
					$response['results'][] = array(
									'id'=>$email->f('id'),
									'email'=>$email->f('email'),
									'username'=>$email->f('username'),
									'user_name'=>$GO_USERS->get_user_realname($record['user_id']),
									'user_id'=>$email->f('user_id'),
									'host'=>$email->f('host'),
									'smtp_host'=>$email->f('smtp_host'),
									'type'=>$email->f('type'),
									'html_signature'=>String::text_to_html($email->f('signature')),
									'plain_signature'=>$email->f('signature')
					);
				}
				break;

			case 'account':

				require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
				$GO_USERS = new GO_USERS();

				$email = new email();
				$response['success']=false;
				$response['data']=$email->get_account($_POST['account_id']);

				$response['data']=$email->decrypt_account($response['data']);

				if($response['data']) {
					$response['data']['user_name']=$GO_USERS->get_user_realname($response['data']['user_id']);
					$response['data']['mbroot'] = trim($response['data']['mbroot'],'./');

					try {
						$server_response = $email->get_servermanager_mailbox_info($response['data']);

					}catch(Exception $e) {
						go_debug('Connection to postfixadmin failed: '.$e->getMessage());
					}

					if(isset($server_response['data'])) {
						$response['data']['vacation_active']=$server_response['data']['vacation_active'];
						$response['data']['vacation_subject']=$server_response['data']['vacation_subject'];
						$response['data']['vacation_body']=$server_response['data']['vacation_body'];
						$response['data']['forward_to'] = $server_response['data']['forward_to'];
					}else
					{
						if(isset($GO_MODULES->modules['systemusers'])) {
							require_once($GO_MODULES->modules['systemusers']['class_path'].'systemusers.class.inc.php');
							$su = new systemusers();

							$account_id	= $_POST['account_id'];
							$vacation = $su->get_vacation($account_id);

							$user_home_dirs = isset($GO_CONFIG->user_home_dirs) ? $GO_CONFIG->user_home_dirs : '/home/';
							$homedir = $user_home_dirs.$response['data']['username'];
							if(stripos($response['data']['host'],'localhost')===false || !file_exists($homedir)) {
								$response['data']['hidetab'] = true;
							}else {
								$response['data']['vacation_active'] = ($vacation['vacation_active']) ? $vacation['vacation_active'] : 0;
								$response['data']['vacation_subject'] = ($vacation['vacation_subject']) ? $vacation['vacation_subject'] : '';
								$response['data']['vacation_body'] = ($vacation['vacation_body']) ? $vacation['vacation_body'] : '';

							}
						}
					}
					
					$GO_EVENTS->fire_event('load_email_account', array(&$response));
					
					$response['success']=true;
				}
				break;

			case 'all_folders':
				$account_id = ($_POST['account_id']);

				if(isset($_POST['deleteFolders'])) {
					$deleteFolders = json_decode(($_POST['deleteFolders']));
					if(count($deleteFolders)) {
						$account = $imap->open_account($account_id);

						foreach($deleteFolders as $folder_id) {
							if($folder = $email->get_folder_by_id(($folder_id))) {
								if($imap->delete_folder($folder['name'], $account['mbroot'])) {
									$email->delete_folder($account_id, $folder['name']);
								}

							}
						}
					}
				}

				$response['total']=$email->get_folders($account_id);
				$response['data']=array();
				while($email->next_record(DB_ASSOC)) {
					$response['data'][]=array(
									'id'=>$email->f('id'),
									'name'=>$email->f('name'),
									'subscribed'=>$email->f('subscribed')
					);
				}
				$response['success']=true;

				break;

			case 'subscribed_folders':
				$account_id = ($_POST['account_id']);

				$hide_inbox = isset($_POST['hideInbox']) && $_POST['hideInbox']=='true';

				$response['total']=$email->get_subscribed($account_id);
				$response['data']=array();
				while($email->next_record(DB_ASSOC)) {
					if (!$hide_inbox || $email->f('name')!='INBOX') {
						$response['data'][]=array(
										'id'=>$email->f('id'),
										'name'=>$email->f('name')
						);
					}
				}
				$response['success']=true;

				break;


			case 'alias':
				$alias = $email->get_alias($_REQUEST['alias_id']);
				$response['data']=$alias;
				$response['success']=true;
				break;
			case 'aliases':
				if(isset($_POST['delete_keys'])) {
					try {
						$response['deleteSuccess']=true;
						$delete_aliases = json_decode($_POST['delete_keys']);
						foreach($delete_aliases as $alias_id) {
							$email->delete_alias(addslashes($alias_id));
						}
					}catch(Exception $e) {
						$response['deleteSuccess']=false;
						$response['deleteFeedback']=$e->getMessage();
					}
				}

				$response['total'] = $email->get_aliases($_POST['account_id']);
				$response['results']=array();
				while($alias = $email->next_record()) {
					$response['results'][] = $alias;
				}
				break;
			case 'all_aliases':

				$response['total'] = $email->get_all_aliases($GO_SECURITY->user_id);
				$response['results']=array();
				while($alias = $email->next_record()) {
					$alias['name']='"'.$alias['name'].'" <'.$alias['email'].'>';
					$alias['html_signature']=String::text_to_html($email->f('signature'));
					$alias['plain_signature']=$email->f('signature');
					unset($alias['signature']);
					$response['results'][] = $alias;
				}
				
				$GO_EVENTS->fire_event('all_aliases', array(&$response, $email));
				break;


			case 'getacl':

				$account = $imap->open_account($_REQUEST['account_id'], $_REQUEST['mailbox']);

				if(isset($_POST['delete_keys'])) {
					try {
						$response['deleteSuccess']=true;
						$delete_ids = json_decode($_POST['delete_keys']);
						foreach($delete_ids as $id) {
							$imap->delete_acl($_REQUEST['mailbox'], $id);
						}
					}catch(Exception $e) {
						$response['deleteSuccess']=false;
						$response['deleteFeedback']=$e->getMessage();
					}
				}

				
				$response['results']=$imap->get_acl($_REQUEST['mailbox']);

				foreach($response['results'] as &$record){
					$record['read']=strpos($record['permissions'],'r')!==false;
					$record['write']=strpos($record['permissions'],'w')!==false;
					$record['delete']=strpos($record['permissions'],'t')!==false;
					$record['createmailbox']=strpos($record['permissions'],'k')!==false;
					$record['deletemailbox']=strpos($record['permissions'],'x')!==false;
					$record['admin']=strpos($record['permissions'],'a')!==false;
				}
				break;

			case 'create_download_hash':
				global $GO_SECURITY;
				require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
				$GO_USERS = new GO_USERS();
				$user = $GO_USERS->get_user($GO_SECURITY->user_id);
				$response['code'] = md5($user['username'].$user['password'].$_REQUEST['filename']);
				$response['success'] = true;
				break;

			/* {TASKSWITCH} */
		}
	}
}catch(Exception $e) {
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}


/*
 *
 * later for superboxselect implementation
 * 
if(isset($response['data']['to']))
	$response['data']['to[]']=htmlspecialchars($response['data']['to'], ENT_COMPAT, 'UTF-8');

if(isset($response['data']['cc']))
	$response['data']['cc[]']=htmlspecialchars($response['data']['cc'], ENT_COMPAT, 'UTF-8');

if(isset($response['data']['bcc']))
	$response['data']['bcc[]']=htmlspecialchars($response['data']['bcc'], ENT_COMPAT, 'UTF-8');
*/

//var_dump($response);
echo json_encode($response);
