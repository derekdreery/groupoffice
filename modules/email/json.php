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

$GO_SECURITY->json_authenticate('email');

require_once ($GO_CONFIG->class_path."mail/imap.class.inc");
require_once ($GO_MODULES->modules['email']['class_path']."cached_imap.class.inc.php");
require_once ($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
require_once ($GO_LANGUAGE->get_language_file('email'));
require_once($GO_CONFIG->class_path.'filesystem.class.inc');

$imap = new cached_imap();
$email = new email();

function get_all_mailbox_nodes($account_id, $folder_id){

	global $lang;

	$response=array();

	$email = new email();

	$email->get_folders($account_id, $folder_id);
	while($email->next_record())
	{
		$pos = strrpos($email->f('name'), $email->f('delimiter'));

		if ($pos && $email->f('delimiter') != '')
		{
			$folder_name = substr($email->f('name'),$pos+1);
		}else
		{
			$folder_name = $email->f('name');
		}
		$folder_name = imap::utf7_imap_decode($folder_name);

		$response[] = array(
				'text'=>$folder_name,
				'id'=>'folder_'.$email->f('id'),
				'iconCls'=>'folderIcon',
				'account_id'=>$email->f('account_id'),
				'folder_id'=>$email->f('id'),
				'mailbox'=>$email->f('name'),
				'expanded'=>true,
				'canHaveChildren'=>($email->f('attributes') > LATT_NOINFERIORS),
				'children'=>get_all_mailbox_nodes($account_id, $email->f('id')),
				'checked'=>$email->f('subscribed')=='1'
				);
	}
	return $response;
}

function get_mailbox_nodes($account_id, $folder_id){
	global $lang, $imap, $inbox_new;

	$email = new email();
	$email2 = new email();

	$response = array();

	$count = $email->get_subscribed($account_id, $folder_id);
	while($email->next_record())
	{
		if($email->f('name') == 'INBOX')
		{
			if($count==1 && $email->f('attributes') > LATT_NOINFERIORS)
			{
				$children=get_mailbox_nodes(0, $email->f('id'));
			}
			$folder_name = $lang['email']['inbox'];
		}else
		{
			$pos = strrpos($email->f('name'), $email->f('delimiter'));

			if ($pos && $email->f('delimiter') != '')
			{
				$folder_name = substr($email->f('name'),$pos+1);
			}else
			{
				$folder_name = $email->f('name');
			}
			$folder_name = imap::utf7_imap_decode($folder_name);
		}

		//check for unread mail
		//$unseen = $email->f('unseen');

		$status = $imap->status($email->f('name'), SA_UNSEEN);//+SA_MESSAGES+SA_RECENT);

		//first time the e-mail is loaded. Let's check the cache
		/*if(!isset($_POST['refresh']))
		{
		if($email->f('unseen')+$status->recent!= $status->unseen || $email->f('msgcount')+$status->recent!= $status->messages)
		{
		debug('Clearing dirty cache of folder: '.$email->f('name'));
		$imap->clear_cache($email->f('id'));
		}
		}*/

		$unseen = isset($status->unseen) ? $status->unseen : 0;

		if($email->f('name')=='INBOX')
		{
			$inbox_new += $unseen;
		}

		if ($unseen > 0)
		{
			$status_html = '&nbsp;<span id="status_'.$email->f('id').'">('.$unseen.')</span>';
		}else
		{
			$status_html = '&nbsp;<span id="status_'.$email->f('id').'"></span>';
		}

		if($email2->get_subscribed(0, $email->f('id')))
		{
			$response[] = array(
				'text'=>$folder_name.$status_html,
				'name'=>$folder_name,
				'id'=>'folder_'.$email->f('id'),
				'iconCls'=>'folder-default',
				'account_id'=>$email->f('account_id'),
				'folder_id'=>$email->f('id'),
				'canHaveChildren'=>$email->f('attributes') > LATT_NOINFERIORS,
				'unseen'=>$unseen,
				'mailbox'=>$email->f('name'),
				'expanded'=>isset($children),
				'children'=>isset($children) ? $children : null
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
				'canHaveChildren'=>$email->f('attributes') > LATT_NOINFERIORS,
				'unseen'=>$unseen,
				'expanded'=>true,
				'children'=>array()
			);
		}
	}
	return $response;
}


function find_alias_and_recipients()
{
	global $email, $account_id, $response, $content, $task;
	$aliases = array();
	$email->get_aliases($account_id, true);
	while($alias=$email->next_record())
	{
		$aliases[strtolower($alias['email'])]=$alias['id'];
	}

	$fill_to = $task=='reply_all' || $task=='opendraft';

	//add all recievers from this email
	if (isset($content["to"]))
	{
		$first = !empty($response['data']['to']);
		for ($i=0;$i<sizeof($content["to"]);$i++)
		{
			$address = strtolower(String::get_email_from_string($content["to"][$i]));
			if (!empty($email))
			{
				if(isset($aliases[$address]))
				{
					$response['data']['alias_id']=$aliases[$address];
				}

				if($fill_to && (!isset($aliases[$address]) || $task=='opendraft')){
					if (!$first)
					{
						$first = true;
					}else
					{
						$response['data']['to'] .= ',';
					}
					$response['data']['to'] .= $content["to"][$i];
				}
			}
		}
	}
	if (isset($content["cc"]) && count($content["cc"]) > 0)
	{
		$response['data']['cc']='';
		$first=false;
		for ($i=0;$i<sizeof($content["cc"]);$i++)
		{
			$address = String::get_email_from_string($content["cc"][$i]);
			if (!empty($address))
			{
				if(isset($aliases[$address]))
				{
					$response['data']['alias_id']=$aliases[$address];
				}elseif($fill_to){
					if (!$first)
					{
						$first = true;
					}else
					{
						$response['data']['cc'] .= ',';
					}
					$response['data']['cc'] .= $content["cc"][$i];
				}
			}
		}
	}
}



try{

	$task = $_REQUEST['task'];
	if($task == 'reply' || $task =='reply_all' || $task == 'forward' || $task=='opendraft')
	{
		$account_id = $_POST['account_id'];
		$uid = $_POST['uid'];
		$mailbox = $_POST['mailbox'];

		$url_replacements=array();

		$account = connect($account_id, $mailbox);



		if(!$account)
		{
			$response['success']=false;
			$response['feedback']=$strDataError;
		}else
		{

			$content = $imap->get_message($uid);
			$parts = array_reverse($imap->f("parts"));

			//fill in the header fields
			$subject = isset($content['subject']) ? $content['subject'] : $lang['email']['no_subject'];

			$response['attachments']=array();

			switch($task)
			{
				case "reply":
				case "reply_all":
					$response['data']['to'] = $content["reply-to"];
					if(stripos($subject,'Re:')===false)
					{
						$response['data']['subject'] = 'Re: '.$subject;
					}else
					{
						$response['data']['subject'] = $subject;
					}
					break;

				case "opendraft":
				case "forward":

					if($task == 'opendraft')
					{
						$response['data']['to']='';
						$response['data']['subject'] = $subject;

					}else
					{
						if(stripos($subject,'Fwd:')===false)
						{
							$response['data']['subject'] = 'Fwd: '.$subject;
						}else
						{
							$response['data']['subject'] = $subject;
						}
					}

					//reattach non-inline attachments
					for ($i=0;$i<count($parts);$i++)
					{
						//var_dump($parts[$i]);
						if($imap->part_is_attachment($parts[$i]))
						{
							$file = $imap->view_part($uid, $parts[$i]["number"], $parts[$i]["transfer"]);

							$name = $parts[$i]['name'] != '' ? $parts[$i]['name'] : 'attach_'.$i;

							$dir=$GO_CONFIG->tmpdir.'attachments/';

							filesystem::mkdir_recursive($dir);

							$tmp_file = $dir.File::strip_invalid_chars($name);

							$fp = fopen($tmp_file,"wb");
							fwrite ($fp,$file);
							fclose($fp);

							$response['data']['attachments'][]=array(
							'tmp_name'=>$tmp_file,
							'name'=>$name,
							'size'=>$parts[$i]["size"],
							'type'=>File::get_filetype_description(File::get_extension($name))
							);

						}
					}

					break;
			}

			find_alias_and_recipients();


			//reatach inline attachements
			for ($i=0;$i<count($parts);$i++)
			{
				if ($parts[$i]["id"] != '')// && eregi("inline", $parts[$i]["disposition"]))
				{
					$file = $imap->view_part($uid, $parts[$i]["number"], $parts[$i]["transfer"]);

					$dir=$GO_CONFIG->tmpdir.'attachments/';
					filesystem::mkdir_recursive($dir);

					$tmp_file = !empty($parts[$i]["name"]) ? $dir.File::strip_invalid_chars($parts[$i]["name"]) : $dir.uniqid(time());

					$fp = fopen($tmp_file,"wb");
					fwrite ($fp,$file);
					fclose($fp);

					if (strpos($parts[$i]["id"],'>'))
					{
						$parts[$i]["id"] = substr($parts[$i]["id"], 1,strlen($parts[$i]["id"])-2);
					}

					//Content-ID's that need to be replaced with urls when message is send

					//replace inline images identified by a content id with the url to display the part by Group-Office
					$url_replacement['id'] = $parts[$i]["id"];
					$url_replacement['url'] = $GO_MODULES->modules['email']['url']."attachment.php?account_id=".$account_id."&amp;mailbox=".$mailbox."&amp;uid=".$uid."&amp;part=".$parts[$i]["number"]."&amp;transfer=".$parts[$i]["transfer"]."&amp;mime=".$parts[$i]["mime"]."&amp;filename=".urlencode($parts[$i]["name"]);
					$url_replacement['tmp_file'] = $tmp_file;

					$url_replacements[] = $url_replacement;
				}
			}



			$response['data']['body']='';



			//remove alternative body part
			$new_parts=array();
			for($i=0;$i<count($parts);$i++)
			{
				$mime = strtolower($parts[$i]["mime"]);

				if(strpos($mime, $_POST['content_type']) || (strtolower($parts[$i]['type'])!='alternative' &&  strtolower($parts[$i]['type'])!='related'))
					$new_parts[]=$parts[$i];
			}

			$parts=$new_parts;

			//debug($parts);

			if($GO_MODULES->has_module('gnupg'))
			{
				require_once($GO_MODULES->modules['gnupg']['class_path'].'gnupg.class.inc.php');
				$gnupg = new gnupg();
				$sender = String::get_email_from_string($content['from']);
				$passphrase = !empty($_SESSION['GO_SESSION']['gnupg']['passwords'][$sender]) ? $_SESSION['GO_SESSION']['gnupg']['passwords'][$sender] : '';
			}


			//$html_message_count = 0;
			for ($i=0;$i<count($parts);$i++)
			{
				$mime = strtolower($parts[$i]["mime"]);

				//var_dump($parts[$i]);

				if (empty($response['data']['body']) &&
					(stripos($parts[$i]["disposition"],'attachment')===false) &&
					(stripos($mime,'html')!==false || stripos($mime,'plain')!==false || $mime == "text/enriched" || $mime == "unknown/unknown"))
				{
					switch ($mime)
					{
						case 'text/plain':
							$text_part = trim($imap->view_part($uid, $parts[$i]["number"], $parts[$i]["transfer"], $parts[$i]['charset']));

							if($GO_MODULES->has_module('gnupg'))
								$text_part = $gnupg->replace_encoded($text_part,$passphrase,false);

							$response['data']['body'] .= $_POST['content_type']=='html' ? String::text_to_html($text_part, false) : $text_part;
							break;

						case 'text/html':
							$html_part = trim($imap->view_part($uid, $parts[$i]["number"], $parts[$i]["transfer"], $parts[$i]['charset']));

							if($GO_MODULES->has_module('gnupg'))
								$html_part = $gnupg->replace_encoded($html_part,$passphrase);

							$response['data']['body'] .= $_POST['content_type']=='html' ? String::convert_html($html_part) : String::html_to_text($html_part);
							break;

						case 'text/enriched':
							$html_part = String::enriched_to_html(trim($imap->view_part($uid,$parts[$i]["number"], $parts[$i]["transfer"], $parts[$i]['charset'])), false);

							if($GO_MODULES->has_module('gnupg'))
								$html_part = $gnupg->replace_encoded($html_part,$passphrase);

							$response['data']['body'] .= $html_part;
							break;
					}
				}
			}

			if($response['data']['body'] != '')
			{
				//replace inline images with the url to display the part by Group-Office
				for ($i=0;$i<count($url_replacements);$i++)
				{
					$response['data']['body'] = str_replace('cid:'.$url_replacements[$i]['id'], $url_replacements[$i]['url'], $response['data']['body']);
				}
			}



			if($task=='forward')
			{
				$om_to = isset($content['to']) ? implode(',',$content["to"]) : $lang['email']['no_recipients'];
				$om_cc = isset($content['cc']) ? implode(',',$content["cc"]) : '';

				if($_POST['content_type']== 'html')
				{
					$header_om  = '<br /><br /><font face="verdana" size="2">'.$lang['email']['original_message']."<br />";
					$header_om .= "<b>".$lang['email']['subject'].":&nbsp;</b>".htmlspecialchars($subject, ENT_QUOTES, 'UTF-8')."<br />";
					$header_om .= '<b>'.$lang['email']['from'].": &nbsp;</b>".htmlspecialchars($content['from'], ENT_QUOTES, 'UTF-8')."<br />";
					$header_om .= "<b>".$lang['email']['to'].":&nbsp;</b>".htmlspecialchars($om_to, ENT_QUOTES, 'UTF-8')."<br />";
					if(!empty($om_cc))
					{
						$header_om .= "<b>CC:&nbsp;</b>".htmlspecialchars($om_cc, ENT_QUOTES, 'UTF-8')."<br />";
					}

					$header_om .= "<b>".$lang['common']['date'].":&nbsp;</b>".date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'],$content["udate"])."<br />";

					$header_om .= "</font><br /><br />";

					$response['data']['body']=$header_om.$response['data']['body'];
					//$response['data']['body'] = '<br /><blockquote style="border:0;border-left: 2px solid #22437f; padding:0px; margin:0px; padding-left:5px; margin-left: 5px; ">'.$header_om.$response['data']['body'].'</blockquote>';
				}else
				{
					$header_om  = "\n\n".$lang['email']['original_message']."\n";
					$header_om .= $lang['email']['subject'].": ".$subject."\n";
					$header_om .= $lang['email']['from'].": ".$content['from']."\n";
					$header_om .= $lang['email']['to'].": ".$om_to."\n";
					if(!empty($om_cc))
					{
						$header_om .= "CC: ".$om_cc."\n";
					}

					$header_om .= $lang['common']['date'].": ".date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'],$content["udate"])."\n";
					$header_om .= "\n\n";

					$response['data']['body'] = str_replace("\r",'',$response['data']['body']);
					//$response['data']['body'] = '> '.str_replace("\n","\n> ",$response['data']['body']);

					$response['data']['body'] = $header_om.$response['data']['body'];
				}
			}elseif($task=='reply' || $task=='reply_all')
			{
				$header_om = sprintf($lang['email']['replyHeader'],
					$lang['common']['full_days'][date('w', $content["udate"])],
					date($_SESSION['GO_SESSION']['date_format'],$content["udate"]),
					date($_SESSION['GO_SESSION']['time_format'],$content["udate"]),
					$content['from']);

				if($_POST['content_type']== 'html')
				{

					$response['data']['body'] = '<br /><br />'.htmlspecialchars($header_om, ENT_QUOTES, 'UTF-8').'<br /><blockquote style="border:0;border-left: 2px solid #22437f; padding:0px; margin:0px; padding-left:5px; margin-left: 5px; ">'.$response['data']['body'].'</blockquote>';
				}else
				{
					$response['data']['body'] = str_replace("\r",'',$response['data']['body']);
					$response['data']['body'] = '> '.str_replace("\n","\n> ",$response['data']['body']);

					$response['data']['body'] = "\n\n".$header_om."\n".$response['data']['body'];
				}
			}


			$response['data']['inline_attachments']=$url_replacements;


			if(isset($_POST['template_id']) && $_POST['template_id']>0)
			{
				$template_id = ($_POST['template_id']);
				$to = isset($response['data']['to']) ? $response['data']['to'] : '';
				$template = load_template($template_id, $to);

				$response['data']['body'] = $template['data']['body'].$response['data']['body'];
				$response['data']['inline_attachments']=array_merge($response['data']['inline_attachments'], $template['data']['inline_attachments']);
			}

			if($_POST['content_type']=='plain')
			{
				$response['data']['textbody']=$response['data']['body'];
				unset($response['data']['body']);
			}

			$response['success']=true;
		}
	}else
	{
		switch($_REQUEST['task'])
		{
			case 'icalendar_attachment':
				if(!isset($GO_MODULES->modules['calendar']) || !$GO_MODULES->modules['calendar']['read_permission'])
				{
					throw new Exception(sprintf($lang['common']['moduleRequired'], $lang['email']['calendar']));
				}


				$account = connect($_REQUEST['account_id'], $_REQUEST['mailbox']);
				$data = $imap->view_part($_REQUEST['uid'], $_REQUEST['part'], $_REQUEST['transfer']);


				require_once($GO_CONFIG->class_path.'Date.class.inc.php');
				require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc.php');
				$cal = new calendar();


				require_once($GO_CONFIG->class_path.'ical2array.class.inc');
				$ical2array = new ical2array();

				$vcalendar = $ical2array->parse_string($data);

				$event=false;
				while($object = array_shift($vcalendar[0]['objects']))
				{
					if($object['type'] == 'VEVENT')
					{
						$event = $cal->get_event_from_ical_object($object);
						break;
					}
				}

				if(!$event)
				{
					throw new Exception($lang['common']['selectError']);
				}
				$response=$cal->event_to_json_response($event);
				//debug($response);
				$response['success']=true;
				break;

			case 'attachments':

				while($file = array_shift($_SESSION['GO_SESSION']['just_uploaded_attachments']))
				{
					$response['results'][]=array(
						'tmp_name'=>$file,
						'name'=>utf8_basename($file),
						'size'=>filesize($file),
						'type'=>File::get_filetype_description(File::get_extension($file))
					);
				}
				$response['total']=count($response['results']);

				break;

			case 'template':
				$template_id=$_REQUEST['template_id'];
				$to=$_REQUEST['to'];

				$response = load_template($template_id, $to, isset($_POST['mailing_group_id']) && $_POST['mailing_group_id']>0);

				if($_POST['content_type']=='plain')
				{
					$response['data']['textbody']=$response['data']['body'];
					unset($response['data']['body']);
				}

				$response['success']=true;
				break;

			case 'filters':
				if(isset($_POST['delete_keys']))
				{
					$filters = json_decode(($_POST['delete_keys']));

					foreach($filters as $filter_id)
					{
						$email->delete_filter($filter_id);
					}
					$response['deleteSuccess']=true;
				}
				$response['total']=$email->get_filters(($_POST['account_id']));
				$response['results']=array();
				while($email->next_record(DB_ASSOC))
				{
					$response['results'][] = $email->record;
				}

				break;

			case 'filter':

				$response['success']=false;
				$response['data']=$email->get_filter(($_POST['filter_id']));
				if($response['data'])
				{
					$response['success']=true;
				}

				break;

			case 'message_attachment':
				$account = connect($_REQUEST['account_id'], $_REQUEST['mailbox']);
				$data = $imap->view_part($_REQUEST['uid'], $_REQUEST['part'], $_REQUEST['transfer']);

				$response=array();
				$inline_url = $GO_MODULES->modules['email']['url'].'mimepart.php?account_id='.$_REQUEST['account_id'].'&mailbox='.urlencode(($_REQUEST['mailbox'])).'&uid='.($_REQUEST['uid']).'&part='.$_REQUEST['part'].'&transfer='.urlencode($_REQUEST['transfer']);


				require_once($GO_CONFIG->class_path.'mail/Go2Mime.class.inc.php');
				$go2mime = new Go2Mime();

				$response['blocked_images']=0;

				$response = array_merge($response, $go2mime->mime2GO($data, $inline_url,false,''));

				break;

			case 'message':

				$account_id = $_REQUEST['account_id'];
				$mailbox = $_REQUEST['mailbox'];
				$uid = $_REQUEST['uid'];

				$account = connect($account_id, $mailbox);

				$response = $imap->get_message($uid);

				if($imap->set_unseen_cache(array($uid), false))
				{
					if(stripos($account['host'],'gmail')!==false)
					{
						$imap->set_message_flag($mailbox, array($uid), "\\Seen");
					}
				}

				if(!$response)
				{
					throw new Exception($lang['email']['errorGettingMessage']);
				}

				//debug($response);

				if(empty($response["subject"]))
				{
					$response['subject']= $lang['email']['no_subject'];
				}
				$response['subject']= htmlspecialchars($response['subject'], ENT_QUOTES, 'UTF-8');;


				$response['account_id']=$account_id;
				$response['full_from']=htmlspecialchars($response['from'], ENT_QUOTES, 'UTF-8');

				$RFC822 = new RFC822();
				$address = $RFC822->parse_address_list($response['from']);
				$response['sender']=isset($address[0]['email']) ? htmlspecialchars($address[0]['email'], ENT_QUOTES, 'UTF-8') : '';
				$response['from']=isset($address[0]['personal']) ? htmlspecialchars($address[0]['personal'], ENT_QUOTES, 'UTF-8') : '';

				$response['sender_contact_id']=0;
				if(!empty($_POST['get_contact_id']) && $GO_MODULES->has_module('addressbook'))
				{
					require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc.php');
					$ab = new addressbook();

					$contact = $ab->get_contact_by_email($response['sender'], $GO_SECURITY->user_id);
					$response['sender_contact_id']=intval($contact['id']);

					if($response['sender_contact_id'])
					{
							$contact['contact_name'] = String::format_name($contact);
							$response['contact']=$contact;
					}             
				}

				if(!empty($response['to']))
				{
					$to=array();
					foreach($response['to'] as $address)
					{
						$address=$RFC822->parse_address_list($address);
						$to[] = array('email'=>htmlspecialchars($address[0]['email'], ENT_QUOTES, 'UTF-8'),
						'name'=>htmlspecialchars($address[0]['personal'], ENT_QUOTES, 'UTF-8'));
					}
					$response['to']=$to;
				}else
				{
					$response['to']=array('email'=>'', 'name'=> $lang['email']['no_recipients']);
				}


				$cc=array();
				if(!empty($response['cc']))
				{
					foreach($response['cc'] as $address)
					{
						$address=$RFC822->parse_address_list($address);
						$cc[] = array('email'=>htmlspecialchars($address[0]['email'], ENT_QUOTES, 'UTF-8'),
						'name'=>htmlspecialchars($address[0]['personal'], ENT_QUOTES, 'UTF-8'));
					}
				}
				$response['cc']=$cc;

				$bcc=array();
				if(!empty($response['bcc']))
				{
					foreach($response['bcc'] as $address)
					{
						$address=$RFC822->parse_address_list($address);
						$bcc[] = array('email'=>htmlspecialchars($address[0]['email'], ENT_QUOTES, 'UTF-8'),
						'name'=>htmlspecialchars($address[0]['personal'], ENT_QUOTES, 'UTF-8'));
					}
				}
				$response['bcc']=$bcc;

				$response['date']=date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], $response['udate']);
				//$response['size']=Number::format_size($response['size']);

				$parts = array_reverse($imap->f("parts"));

				//debug($parts);
				/*
				*
				 * Sometimes clients send multipart/alternative but there's only a text part. FIrst check if there's
				 * a html alternative to display
				 */
				$html_alternative=false;
				if(empty($_POST['plaintext']))
				{
					for($i=0;$i<count($parts);$i++)
					{
						if(stripos($parts[$i]['mime'],'html')!==false && (strtolower($parts[$i]['type'])=='alternative' || strtolower($parts[$i]['type'])=='related'))
						{
							$html_alternative=true;
						}
					}
				}

				//debug($html_alternative);

				$response['body']='';

				$attachments=array();

				if(stripos($response['content_type'],'html')!==false)
				{
					$default_mime = 'text/html';
				}else
				{
					$default_mime = 'text/plain';
				}

				$part_count = count($parts);
				if($part_count==1)
				{
					//if there's only one part use the message parameters.
					if(stripos($parts[0]['mime'],'plain')!==false)
						$parts[0]['mime']=$default_mime;

						//debug($response['content_transfer_encoding']);
					  //debug($parts[0]['transfer']);

					if(!empty($response['content_transfer_encoding']) && (empty($parts[0]['transfer']) || strtolower($parts[0]['transfer'])=='7bit' || strtolower($parts[0]['transfer'])=='8bit'))
						$parts[0]['transfer']=$response['content_transfer_encoding'];
				}

				//block remote URL's if contacts is unknown
				$response['blocked_images']=0;
				if(!isset($_POST['unblock']))
				{
					require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc.php');
					$ab = new addressbook();

					$contact = $ab->get_contact_by_email($response['sender'], $GO_SECURITY->user_id);
					$block = !is_array($contact);
				}else
				{
					$block=false;
				}

				while($part = array_shift($parts))
				{
					$mime = isset($part["mime"]) ? strtolower($part["mime"]) : $default_mime;

					//some clients just send html
					if($mime=='html')
					{
						$mime = 'text/html';
					}

					/*debug($mime);
					debug($html_alternative);
					debug($part['type']);
					debug($part["disposition"]);
					debug('-----');*/

					if (empty($response['body']) &&
					(stripos($part["disposition"],'attachment')===false) &&
					(
						(stripos($mime,'html')!==false && empty($_POST['plaintext'])) ||
						(stripos($mime,'plain')!==false && (!$html_alternative || strtolower($part['type'])!='alternative')) || $mime == "text/enriched" || $mime == "unknown/unknown"))
					{
						//debug('ja');
						$part_body = $imap->view_part($uid, $part["number"], $part["transfer"], $part["charset"]);

						//debug($part_body);

						switch($mime)
						{
							case 'unknown/unknown':
							case 'text/plain':
								$uuencoded_attachments = $imap->extract_uuencoded_attachments($part_body);


								$part_body = empty($_POST['plaintext']) ? String::text_to_html($part_body) : $part_body;

								for($i=0;$i<count($uuencoded_attachments);$i++)
								{
									$attachment = $uuencoded_attachments[$i];
									$attachment['number']=$part['number'];
									unset($attachment['data']);
									$attachment['uuencoded_partnumber']=$i+1;

									$attachments[]=$attachment;
								}

								break;

							case 'text/html':
								$part_body = String::convert_html($part_body, $block, $response['blocked_images']);
								break;

							case 'text/enriched':
								$part_body = String::enriched_to_html($part_body);
								break;
						}

						/*go_log(LOG_DEBUG, $part["name"]);

						if(!empty($response['body']))
						{
						if (!empty($part["name"]))
						{
						$response['body'] .= "<p align=\"center\">--- ".$part["name"]." ---</p>";
						}elseif($response['body'] != '')
						{
						$response['body'] .= '<br /><br /><br />';
						}
						}*/

						//Without this trim here Internet explorer mysteriously failed on some
						//particular mails.
						$response['body'] .= trim($part_body);
					}else
					{
						$attachments[]=$part;
					}
				}

				//When a mail is saved as a task/appointment/etc. the attachments will be saved temporarily
				if(!empty($_POST['create_temporary_attachments']))
				{
					$tmp_dir = $GO_CONFIG->tmpdir.'temporary_attachments/';
					if(!is_dir($tmp_dir))
					{
						mkdir($tmp_dir);
					}
				}


				if($GO_MODULES->has_module('gnupg'))
				{
					require_once($GO_MODULES->modules['gnupg']['class_path'].'gnupg.class.inc.php');
					$gnupg = new gnupg();
					$passphrase = !empty($_SESSION['GO_SESSION']['gnupg']['passwords'][$response['sender']]) ? $_SESSION['GO_SESSION']['gnupg']['passwords'][$response['sender']] : '';
					if(isset($_POST['passphrase']))
					{
						$passphrase=$_SESSION['GO_SESSION']['gnupg']['passwords'][$response['sender']]=$_POST['passphrase'];
					}
					try{
						$response['body'] = $gnupg->replace_encoded($response['body'],$passphrase);
					}
					catch(Exception $e)
					{
						$m = $e->getMessage();

						if(strpos($m, 'bad passphrase'))
						{
							$response['askPassphrase']=true;
							if(isset($_POST['passphrase']))
							{
								throw new Exception('Wrong passphrase!');
							}
						}else
						{
							throw new Exception($m);
						}
					}
				}


				//debug(var_export($attachments, true));

				//$response['event']=false;
				$response['attachments']=array();
				$index=0;
				for ($i = 0; $i < count($attachments); $i ++) {

					if(stripos($attachments[$i]['mime'],'calendar')!==false && empty($attachments[$i]['name']))
					{
						$attachments[$i]['name']=$lang['email']['event'].'.ics';
					}

					if (!empty($attachments[$i]["id"]))
					{
						//when an image has an id it belongs somewhere in the text we gathered above so replace the
						//source id with the correct link to display the image.

						$tmp_id = $attachments[$i]["id"];
						if (strpos($tmp_id,'>'))
						{
							$tmp_id = substr($attachments[$i]["id"], 1,strlen($attachments[$i]["id"])-2);
						}
						$id = "cid:".$tmp_id;

						$url = $GO_MODULES->modules['email']['url']."attachment.php?account_id=".$account['id']."&mailbox=".urlencode($mailbox)."&amp;uid=".$uid."&amp;part=".$attachments[$i]["number"]."&amp;transfer=".$attachments[$i]["transfer"]."&amp;mime=".$attachments[$i]["mime"]."&amp;filename=".urlencode($attachments[$i]["name"]);

						if(strpos($response['body'], $id))
						{
							$response['body'] = str_replace($id, $url, $response['body']);
						}else
						{
							//id was not found in body so add it as attachment later
							unset($attachments[$i]['id']);
						}
					}

					if ($imap->part_is_attachment($attachments[$i])){

						$attachment = $attachments[$i];

						$attachment['index']=$index;
						$attachment['extension']=File::get_extension($attachments[$i]["name"]);

						if(!empty($_POST['create_temporary_attachments']))
						{
							$tmp_file = $tmp_dir.uniqid(time());
							$data = $imap->view_part($uid, $attachment['number'], $attachment['transfer']);
							if($data && file_put_contents($tmp_file, $data))
							{
								$attachment['tmp_file']=$tmp_file;
							}
						}

						$response['attachments'][]=$attachment;
						$index++;
					}
				}



				break;

			case 'messages':

				$account_id = isset ($_REQUEST['account_id']) ? $_REQUEST['account_id'] : 0;
				$mailbox = isset ($_REQUEST['mailbox']) ? ($_REQUEST['mailbox']) : 'INBOX';
				$query = isset($_POST['query']) ? ($_POST['query']) : '';
				$unread = isset($_POST['unread']) && ($_POST['unread'] == 'true') ? true : false;

				if($unread) {
					$query = str_replace(array('UNSEEN', 'SEEN'), array('', ''), $query);
					$query .= ' UNSEEN';
				}

				$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : 0;
				$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : 30;


				$account = connect($account_id, $mailbox);

				if(!empty($_POST['refresh'])) {
					$imap->clear_cache($_POST['folder_id']);
				}

				$response['trash_folder']=$account['trash'];

				$response['trash']=!empty($account['trash']) && strpos($mailbox, $imap->utf7_imap_encode($account['trash']))!==false;
				$response['drafts']=!empty($account['drafts']) && strpos($mailbox, $imap->utf7_imap_encode($account['drafts']))!==false;
				$response['sent']=!empty($account['sent']) && strpos($mailbox, $imap->utf7_imap_encode($account['sent']))!==false;

				if(isset($_POST['delete_keys'])) {
					$messages = json_decode($_POST['delete_keys']);

					$imap->set_message_flag($mailbox, $messages, "\\Seen");
					if($imap->is_imap() && !empty($account['trash']) && $imap->utf7_imap_decode($mailbox) != $account['trash']) {
						$response['deleteSuccess']=$imap->move($imap->utf7_imap_encode($account['trash']), $messages);
					}else {

						$response['deleteSuccess']=$imap->delete($messages);
					}
					if(!$response['deleteSuccess']) {
						$lasterror = $imap->last_error();
						if(stripos($lasterror,'quota')!==false) {
							$response['deleteFeedback']=$lang['email']['quotaError'];
						}else {
							$response['deleteFeedback']=$lang['common']['deleteError'].':<br /><br />'.$lasterror.'<br /><br />'.$lang['email']['disable_trash_folder'];
						}
					}
				}

				if(isset($_POST['action'])) {
					$messages = json_decode($_POST['messages']);
					switch($_POST['action']) {
						case 'move':
							$from_mailbox = $_REQUEST['from_mailbox'];
							$to_mailbox = $_REQUEST['to_mailbox'];

							$response['success']=$imap->move($to_mailbox,$messages);

							$nocache=true;
							break;
					}
				}
				
				$sort=isset($_POST['sort']) ? $_POST['sort'] : 'from';

				switch($sort)
				{
					case 'from':
					$sort_field=SORTFROM;
					break;
					case 'date':
					$sort_field=SORTDATE;
					break;
					case 'subject':
					$sort_field=SORTSUBJECT;
					break;
					default:
					$sort_field=SORTDATE;
				}

				//if($sort_field == SORTDATE && $imap->is_imap())
				//$sort_field = SORTARRIVAL;

				if(($response['sent'] || $response['drafts']) && $sort_field==SORTFROM) {
					$sort_field=SORTTO;
				}

				$sort_order=isset($_POST['dir']) && $_POST['dir']=='ASC' ? 0 : 1;

				//$uids = $imap->get_message_uids();

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

				$messages = $imap->get_message_headers($start, $limit, $sort_field , $sort_order, $query);


				//filtering might have changed the uid list
				$uids = $imap->get_uids_subset($start, $limit);

				//debug($uids);
				//debug($messages);

				$response['results']=array();

				foreach($uids as $uid) {
					$message = $messages[$uid];
					unset($message['content_type'], $message['reply_to'], $message['content_transfer_encoding'], $message['notification']);
					if($message['udate']>$day_start && $message['udate']<$day_end) {
						$message['date'] = date($_SESSION['GO_SESSION']['time_format'],$message['udate']);
					}else {
						$message['date'] = date($_SESSION['GO_SESSION']['date_format'],$message['udate']);
					}

					$subject = $imap->f('subject');
					if(empty($message['subject'])) {
						$message['subject']=$lang['email']['no_subject'];
					}

					$message['from'] = ($response['sent'] || $response['drafts']) ? $message['to'] : $message['from'];

					$RFC822 = new RFC822();
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

					$message['sender'] = empty($address[0]) ? '' : $address[0]['email'];

					$message['from']=htmlspecialchars($message['from'], ENT_QUOTES, 'UTF-8');
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
				$response['total'] = count($imap->sort);

				foreach($imap->touched_folders as $touched_folder) {
					if($touched_folder==$mailbox) {
						$response['unseen'][$imap->folder['id']]=$imap->unseen;
					}else {
					//$response=array();
						$status = $imap->status($touched_folder, SA_UNSEEN);
						$folder = $email->get_folder($account_id, $touched_folder);

						if(isset($status->unseen))
							$response['unseen'][$folder['id']]=$status->unseen;
					}
				}

				break;

										case 'tree':
											$email = new email();
											//$account_id=isset($_REQUEST['account_id']) ? ($_REQUEST['account_id']) : 0;
											//$folder_id=isset($_REQUEST['folder_id']) ? ($_REQUEST['folder_id']) : 0;

											if(isset($_REQUEST['node']) && strpos($_REQUEST['node'],'_'))
											{
												$node = explode('_',$_REQUEST['node']);
												$node_type=$node[0];
												$node_id=$node[1];
											}else {
												$node_type='root';
												$node_id=0;
											}

											$response=array();
											if($node_type=='root')
											{
												$email2 = new email();
												$count = $email2->get_accounts($GO_SECURITY->user_id);
												//go_log(LOG_DEBUG, $count);
												while($email2->next_record())
												{
													$account = connect($email2->f('id'), 'INBOX', false);

													$usage = '';
													$inbox_new=0;
													if($account)
													{
														if(!empty($_POST['refresh']))
														{
															debug('refreshing');
															$email->synchronize_folders($account, $imap);
															$imap->clear_cache();
														}

														$text = $email2->f('email');

														/*$server_response = $email->get_servermanager_mailbox_info($account);
														 if(isset($server_response['success']))
														 {
															$usage .= Number::format_size($server_response['data']['usage']*1024);

															if($server_response['data']['quota']>0)
															{
															$percentage = ceil($server_response['data']['usage']*100/$server_response['data']['quota']);
															$usage .= '/'.Number::format_size($server_response['data']['quota']*1024).' ('.$percentage.'%)';
															}
															}*/

														$quota = $imap->get_quota();
														if(isset($quota['usage']))
														{
															if(!empty($quota['limit']))
															{
																$percentage = ceil($quota['usage']*100/$quota['limit']);
																$usage = sprintf($lang['email']['usage_limit'], $percentage.'%', Number::format_size($quota['limit']*1024));
															}	else
															{
																$usage = sprintf($lang['email']['usage'], Number::format_size($quota['usage']*1024));
															}
														}

														/*$root_folder=false;
														if(!empty($account['mbroot']))
														{
															$lastchar = substr($account['mbroot'],-1);
															if($lastchar=='.' || $lastchar == '/')
															{
																$root_folder = $email->get_folder($account['id'], substr($account['mbroot'],0,-1));
															}
														}

														if($root_folder)
														{
															$children = get_mailbox_nodes(0, $root_folder['id']);
														}else
														{*/
															$children = get_mailbox_nodes($email2->f('id'), 0);
														//}

														$imap->close();
													}else
													{
														$text = $email2->f('email').' ('.$lang['common']['error'].')';
														$children=array();
													}

													$node =  array(
														'text'=>$text,
														'name'=>$email2->f('email'),
														'id'=>'account_'.$email2->f('id'),
														'iconCls'=>'folder-account',
														'expanded'=>true,
														'account_id'=>$email2->f('id'),
														'folder_id'=>0,
														'mailbox'=>'INBOX',
														'children'=>$children,
														'canHaveChildren'=>$email2->f('type')=='imap',
														'inbox_new'=>$inbox_new,
														'usage'=>$usage
													);
													if(!$account)
													{
														$node['qtipCfg'] = array('title'=>$lang['common']['error'], 'text' =>htmlspecialchars($imap->last_error(), ENT_QUOTES, 'UTF-8'));
													}

													$response[]=$node;
												}
											}elseif($node_type=='account')
											{
												$account = connect($node_id);
												$response = get_mailbox_nodes($node_id, 0);
											}	else
											{
												$folder_id=$node_id;

												$folder = $email->get_folder_by_id($folder_id);
												$account = connect($folder['account_id']);

												$response = get_mailbox_nodes(0, $folder_id);
											}
											break;

										case 'tree-edit':
											$email = new email();
											$email2 = new email();

											$account_id = ($_POST['account_id']);
											if(isset($_REQUEST['node']) && strpos($_REQUEST['node'],'_'))
											{
												$node = explode('_',$_REQUEST['node']);
												$folder_id=$node[1];
											}else
											{
												$folder_id=0;
											}

											$account = $email->get_account($account_id);
											if($folder_id==0)
												$email->synchronize_folders($account);


											$response = get_all_mailbox_nodes($account_id, $folder_id);
											break;

										case 'accounts':

											if(isset($_POST['delete_keys']))
											{
												$response['deleteSuccess']=true;
												try{
													$deleteAccounts = json_decode(($_POST['delete_keys']));

													foreach($deleteAccounts as $account_id)
													{
														$account = $email->get_account($account_id);

														if(!$GO_SECURITY->has_admin_permission($GO_SECURITY->user_id) && (!$GO_MODULES->modules['email']['write_permission'] || $account['user_id']!=$GO_SECURITY->user_id))
														{
															throw new AccessDeniedException();
														}

														$email->delete_account($account_id);
													}
												}catch(Exception $e)
												{
													$response['deleteSuccess']=false;
													$response['deleteFeedback']=$e->getMessage();
												}
											}
											$response['results']=array();

											$user_id = !isset($_POST['personal_only']) && $GO_SECURITY->has_admin_permission($GO_SECURITY->user_id) ? 0 : $GO_SECURITY->user_id;

											$sort = isset ( $_POST['sort'] ) ? $_POST['sort'] : 'email';
											$dir = isset ( $_POST['dir'] ) ? $_POST['dir'] : 'ASC';

											$start = isset ( $_POST['start'] ) ? $_POST['start'] : 0;
											$limit = isset ( $_POST['limit'] ) ? $_POST['limit'] : 0;
											$response['total'] = $email->get_accounts($user_id,$start, $limit, $sort, $dir);

											while($record = $email->next_record())
											{
												$response['results'][] = array(
															'id'=>$email->f('id'),
															'email'=>$email->f('email'),
															'username'=>$email->f('username'),
															'user_name'=>String::format_name($record),
															'user_id'=>$email->f('user_id'),
															'host'=>$email->f('host'),
															'type'=>$email->f('type'),
															'html_signature'=>String::text_to_html($email->f('signature')),
															'plain_signature'=>$email->f('signature')
												);
											}
											break;

										case 'account':
											$email = new email();											
											$response['success']=false;
											$response['data']=$email->get_account($_POST['account_id']);

											if($response['data'])
											{
												$user = $GO_USERS->get_user($response['data']['user_id']);
												$response['data']['user_name']=String::format_name($user['last_name'],$user['first_name'], $user['middle_name']);

												try{
													$server_response = $email->get_servermanager_mailbox_info($response['data']);
												}catch(Exception $e){
													go_log(LOG_DEBUG, 'Connection to postfixadmin failed: '.$e->getMessage());
												}

												if(is_array($server_response))
												{
													$response['data']['vacation_active']=$server_response['data']['vacation_active'];
													$response['data']['vacation_subject']=$server_response['data']['vacation_subject'];
													$response['data']['vacation_body']=$server_response['data']['vacation_body'];		
												}elseif(isset($GO_MODULES->modules['systemusers']))
												{
													require_once($GO_MODULES->modules['systemusers']['class_path'].'systemusers.class.inc.php');
													$su = new systemusers();

													$account_id	= $_POST['account_id'];
													$vacation = $su->get_vacation($account_id);

													$user_home_dirs = isset($GO_CONFIG->user_home_dirs) ? $GO_CONFIG->user_home_dirs : '/home/';
													$homedir = $user_home_dirs.$response['data']['username'];
													if(stripos($response['data']['host'],'localhost')===false || !file_exists($homedir))
													{
														$response['data']['hidetab'] = true;
													}else
													{
														$response['data']['vacation_active'] = ($vacation['vacation_active']) ? $vacation['vacation_active'] : 0;
														$response['data']['vacation_subject'] = ($vacation['vacation_subject']) ? $vacation['vacation_subject'] : '';
														$response['data']['vacation_body'] = ($vacation['vacation_body']) ? $vacation['vacation_body'] : '';
													}
												}
												$response['success']=true;
											}
											break;

										case 'all_folders':
											$account_id = ($_POST['account_id']);

											if(isset($_POST['deleteFolders']))
											{
												$deleteFolders = json_decode(($_POST['deleteFolders']));
												if(count($deleteFolders))
												{
													$account = connect($account_id);

													foreach($deleteFolders as $folder_id)
													{
														if($folder = $email->get_folder_by_id(($folder_id)))
														{
															if($imap->delete_folder($folder['name'], $account['mbroot']))
															{
																$email->delete_folder($account_id, $folder['name']);
															}

														}
													}
												}
											}

											$response['total']=$email->get_folders($account_id);
											$response['data']=array();
											while($email->next_record(DB_ASSOC))
											{
												$response['data'][]=array(
				'id'=>$email->f('id'),
				'name'=>imap::utf7_imap_decode($email->f('name')),
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
											while($email->next_record(DB_ASSOC))
											{
												if ($email->f('attributes') != LATT_NOSELECT && (!$hide_inbox || $email->f('name')!='INBOX'))
												{
													$response['data'][]=array(
					'id'=>$email->f('id'),
					'name'=>imap::utf7_imap_decode($email->f('name'))
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
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_aliases = json_decode($_POST['delete_keys']);
					foreach($delete_aliases as $alias_id)
					{
						$email->delete_alias(addslashes($alias_id));
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}

			$response['total'] = $email->get_aliases($_POST['account_id']);
			$response['results']=array();
			while($alias = $email->next_record())
			{
				$response['results'][] = $alias;
			}
			break;
		case 'all_aliases':

			$response['total'] = $email->get_all_aliases($GO_SECURITY->user_id);
			$response['results']=array();
			while($alias = $email->next_record())
			{
				$alias['name']='"'.$alias['name'].'" <'.$alias['email'].'>';
				$alias['html_signature']=String::text_to_html($email->f('signature'));
				$alias['plain_signature']=$email->f('signature');
				unset($alias['signature']);
				$response['results'][] = $alias;
			}
			break;

/* {TASKSWITCH} */
		}
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





//var_dump($response);
echo json_encode($response);
