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
require_once ($GO_MODULES->modules['email']['class_path']."email.class.inc");
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
				'children'=>get_all_mailbox_nodes($account_id, $email->f('id')),
				'checked'=>$email->f('subscribed')=='1'
				);
	}
	return $response;
}

function get_mailbox_nodes($account_id, $folder_id){
	global $lang, $imap, $inbox_new, $usage;

	$email = new email();
	$email2 = new email();

	$response = array();

	$email->get_subscribed($account_id, $folder_id);
	while($email->next_record())
	{
		if($email->f('name') == 'INBOX')
		{
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

		$status = $imap->status($email->f('name'), SA_UNSEEN);
		
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
				'id'=>'folder_'.$email->f('id'),
				'iconCls'=>'folder-default',
				'account_id'=>$email->f('account_id'),
				'folder_id'=>$email->f('id'),
				'unseen'=>$unseen,
				'mailbox'=>$email->f('name'),
				'usage'=>$usage
			);
		}else {
			$response[] = array(
				'text'=>$folder_name.$status_html,
				'id'=>'folder_'.$email->f('id'),
				'iconCls'=>'folder-default',
				'account_id'=>$email->f('account_id'),
				'folder_id'=>$email->f('id'),
				'mailbox'=>$email->f('name'),
				'unseen'=>$unseen,
				'expanded'=>true,
				'children'=>array(),
				'usage'=>$usage
			);
		}
	}
	return $response;
}


function load_template($template_id, $to, $keep_tags=false)
{
	global $GO_CONFIG, $GO_MODULES, $GO_LANGUAGE, $GO_SECURITY, $GO_USERS;

	require_once ($GO_CONFIG->class_path.'mail/mimeDecode.class.inc');
	require($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
	require($GO_MODULES->modules['mailings']['class_path'].'templates.class.inc.php');

	$ab = new addressbook();
	$tp = new templates();

	$template_body = '';

	$template = $tp->get_template($template_id);

	require_once($GO_CONFIG->class_path.'mail/Go2Mime.class.inc.php');
	$go2mime = new Go2Mime();
	$response['data'] = $go2mime->mime2GO($template['content'], $GO_MODULES->modules['mailings']['url'].'mimepart.php?template_id='.$template_id, true);
	

	if(!$keep_tags)
	{
		$values=array();
		$contact_id=0;
		//if contact_id is not set but email is check if there's contact info available
		if (!empty($to)) {

			if ($contact = $ab->get_contact_by_email($to, $GO_SECURITY->user_id)) {

				$values = array_map('htmlspecialchars', $contact);
			}elseif($user = $GO_USERS->get_user_by_email($to))
			{
				$values = array_map('htmlspecialchars', $user);
			}else
			{
				$ab->search_companies($GO_SECURITY->user_id, $to, 'email');
				if($ab->next_record())
				{
					$values = array_map('htmlspecialchars', $ab->record);
				}
			}
		}
		$tp->replace_fields($response['data']['body'], $values);
	}

	$response['data']['to']=$to;
	return $response;
}

try{

	$task = $_REQUEST['task'];
	if($task == 'reply' || $task =='reply_all' || $task == 'forward' || $task=='opendraft')
	{

		$account_id = ($_POST['account_id']);
		$uid = ($_POST['uid']);
		$mailbox = ($_POST['mailbox']);

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
					$response['data']['to'] = $content["reply_to"];
					if(!eregi('Re:', $subject))
					{
						$response['data']['subject'] = 'Re: '.$subject;
					}else
					{
						$response['data']['subject'] = $subject;
					}

					break;

				case "reply_all":

					$response['data']['to'] = $content["reply_to"];
					if(!eregi('Re:', $subject))
					{
						$response['data']['subject'] = 'Re: '.$subject;
					}else
					{
						$response['data']['subject'] = $subject;
					}

					//add all recievers from this email
					if (isset($content["to"]))
					{
						for ($i=0;$i<sizeof($content["to"]);$i++)
						{
							$email = String::get_email_from_string($content["to"][$i]);

							if ($content["to"][$i] != "" && $account['email']!=$email)
							{
								$response['data']['to'] .= ",".$content["to"][$i];
							}
						}
					}
					if (isset($content["cc"]) && count($content["cc"]) > 0)
					{
						$response['data']['cc']='';
						for ($i=0;$i<sizeof($content["cc"]);$i++)
						{
							$email = String::get_email_from_string($content["cc"][$i]);

							if ($content["cc"][$i] != "" && $account['email']!=$email)
							{
								if (!isset($first))
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

					break;

				case "opendraft":
				case "forward":
						
					if($task == 'opendraft')
					{
						$response['data']['to']='';
						if (isset($content["to"]))
						{
							for ($i=0;$i<sizeof($content["to"]);$i++)
							{

								if ($content["to"][$i] != "")
								{
									if (!isset($first))
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
						unset($first);
						if (isset($content["cc"]))
						{
							$response['data']['cc']='';
							for ($i=0;$i<sizeof($content["cc"]);$i++)
							{
								if ($content["cc"][$i] != "")
								{
									if (!isset($first))
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
						$response['data']['subject'] = $subject;
						
					}else
					{
						if(!eregi('Fwd:', $subject))
						{
							$response['data']['subject'] = 'Fwd: '.$subject;
						}else
						{
							$response['data']['subject'] = $subject;
						}
					}

					//reattach non-online attachments
					for ($i=0;$i<count($parts);$i++)
					{
						//var_dump($parts[$i]);
						if($imap->is_attachment($parts[$i]))
						{
							$file = $imap->view_part($uid, $parts[$i]["number"], $parts[$i]["transfer"]);

							$name = $parts[$i]['name'] != '' ? $parts[$i]['name'] : 'attach_'.$i;

							$dir=$GO_CONFIG->tmpdir.'attachments/';

							filesystem::mkdir_recursive($dir);

							$tmp_file = $dir.$name;

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


			//reatach inline attachements
			for ($i=0;$i<count($parts);$i++)
			{
				if ($parts[$i]["id"] != '')// && eregi("inline", $parts[$i]["disposition"]))
				{
					$file = $imap->view_part($uid, $parts[$i]["number"], $parts[$i]["transfer"]);

					$dir=$GO_CONFIG->tmpdir.'attachments/';
					filesystem::mkdir_recursive($dir);

					$tmp_file = !empty($parts[$i]["name"]) ? $dir.$parts[$i]["name"] : $dir.uniqid(time());

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

			$html_message_count = 0;
			for ($i=0;$i<count($parts);$i++)
			{
				$mime = strtolower($parts[$i]["mime"]);

				if (!eregi("attachment", $parts[$i]["disposition"]))
				{
					switch ($mime)
					{
						case 'text/plain':
							if(strtolower($parts[$i]['type'])!='alternative')
							{
								$html_part = String::text_to_html($imap->view_part($uid,
								$parts[$i]["number"], $parts[$i]["transfer"], $parts[$i]['charset']), false);
								$response['data']['body'] .= $html_part;
							}
							break;

						case 'text/html':
							$html_part = String::convert_html($imap->view_part($uid,
							$parts[$i]["number"], $parts[$i]["transfer"], $parts[$i]['charset']));
							$response['data']['body'] .= $html_part;
							break;

						case 'text/enriched':
							$html_part = String::enriched_to_html($imap->view_part($uid,
							$parts[$i]["number"], $parts[$i]["transfer"], $parts[$i]['charset']), false);
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

			if($task!='opendraft')
			{
				$header_om  = '<font face="verdana" size="2">'.$lang['email']['original_message']."<br />";
				$om_to = '';
				if (isset($content))
				{
					$header_om .= "<b>".$lang['email']['subject'].":&nbsp;</b>".$subject."<br />";
					$header_om .= '<b>'.$lang['email']['from'].": &nbsp;</b>".$content['from'].' &lt;'.$content["sender"]."&gt;<br />";
					if (isset($content['to']))
					{
						for ($i=0;$i<sizeof($content["to"]);$i++)
						{
							if ($i!=0)	$om_to .= ',';
							$om_to .= $content["to"][$i];
						}
					}else
					{
						$om_to=$lang['email']['no_recipients'];
					}
					$header_om .= "<b>".$lang['email']['to'].":&nbsp;</b>".htmlspecialchars($om_to)."<br />";
					$om_cc = '';
					if (isset($content['cc']))
					{
						for ($i=0;$i<sizeof($content["cc"]);$i++)
						{
							if ($i!=0)	$om_cc .= ',';
							$om_cc .= $content["cc"][$i];
						}
					}
					if($om_cc != '')
					{
						$header_om .= "<b>CC:&nbsp;</b>".htmlspecialchars($om_cc)."<br />";
					}
	
					$header_om .= "<b>".$lang['common']['date'].":&nbsp;</b>".date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'],$content["udate"])."<br />";
				}
				$header_om .= "</font><br /><br />";
	
	
				$response['data']['body'] = '<br /><blockquote style="border:0;border-left: 2px solid #22437f; padding:0px; margin:0px; padding-left:5px; margin-left: 5px; ">'.$header_om.$response['data']['body'].'</blockquote>';
			}
				
			
			$response['data']['inline_attachments']=$url_replacements;


			if(isset($_POST['template_id']) && $_POST['template_id']>0)
			{
				$template_id = ($_POST['template_id']);
				$template = load_template($template_id, $response['data']['to']);

				$response['data']['body'] = $template['data']['body'].$response['data']['body'];
				$response['data']['inline_attachments']=array_merge($response['data']['inline_attachments'], $template['data']['inline_attachments']);
			}
			$response['success']=true;
		}
	}else
	{
		switch($_REQUEST['task'])
		{
			case 'attachments':

				while($file = array_shift($_SESSION['GO_SESSION']['just_uploaded_attachments']))
				{
					$response['results'][]=array(
						'tmp_name'=>$file,
						'name'=>utf8_basename($file),
						'size'=>Number::format_size(filesize($file)),
						'type'=>File::get_filetype_description(File::get_extension($file))					
					);
				}
				$response['total']=count($files);

				break;

			case 'template':
				$template_id=$_REQUEST['template_id'];
				$to=$_REQUEST['to'];

				$response = load_template($template_id, $to, isset($_POST['mailing_group_id']) && $_POST['mailing_group_id']>0);

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
			$account_id = ($_REQUEST['account_id']);
			$mailbox = ($_REQUEST['mailbox']);
			$uid = $_REQUEST['uid'];
			$transfer = $_REQUEST['transfer'];
			$part = $_REQUEST['part'];
			$part_number = isset($_REQUEST['part_number']) ? ($_REQUEST['part_number']) : "";

			$account = connect($account_id, $mailbox);

			$data = $imap->view_part($uid, $part, $transfer);
			$response=array();
			$inline_url = $GO_MODULES->modules['mailings']['url'].'mimepart.php?account_id='.$_REQUEST['account_id'].'&mailbox='.urlencode(($_REQUEST['mailbox'])).'&uid='.($_REQUEST['uid']).'&part='.$_REQUEST['part'].'&transfer='.urlencode($_REQUEST['transfer']);
		
				
			require_once($GO_CONFIG->class_path.'mail/Go2Mime.class.inc.php');
			$go2mime = new Go2Mime();
			
			$response['blocked_images']=0;
			
			$response = array_merge($response, $go2mime->mime2GO($data, $inline_url,false, $part_number));

			break;

			case 'message':

				$account_id = ($_REQUEST['account_id']);
				$mailbox = ($_REQUEST['mailbox']);
				$uid = $_REQUEST['uid'];

				$account = connect($account_id, $mailbox);

				$response = $imap->get_message($uid);
					
				if(!$response)
				{
					throw new Exception($lang['email']['errorGettingMessage']);
				}
				
				//debug($response);
				
				if(empty($response["subject"]))
				{
					$response['subject']= $lang['email']['no_subject'];
				}

				$response['account_id']=$account_id;
				$response['full_from']=$response['from'].'&nbsp;&lt;'.$response['sender'].'&gt;';

				if (isset ($response['to'])) {
					$to = implode(', ',$response['to']);
				}
				if (empty($to)) {
					$to = $lang['email']['no_recipients'];
				}
				$response['to'] = htmlspecialchars($to, ENT_QUOTES, 'UTF-8');

				$cc='';
				if (isset ($response['cc'])) {
					$cc = implode(', ',$response['cc']);
				}
				$response['cc'] = htmlspecialchars($cc, ENT_QUOTES, 'UTF-8');

				$bcc='';
				if (isset ($response['bcc'])) {
					$bcc = implode(', ',$response['bcc']);
				}
				$response['bcc'] = htmlspecialchars($bcc, ENT_QUOTES, 'UTF-8');

				$response['date']=date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], $response['udate']);
				//$response['size']=Number::format_size($response['size']);

				$parts = array_reverse($imap->f("parts"));


				/*
				 * Sometimes clients send multipart/alternative but there's only a text part. FIrst check if there's
				 * a html alternative to display
				 */
				$html_alternative=false;
				for($i=0;$i<count($parts);$i++)
				{
					if(strtolower($parts[$i]['mime'])=='text/html' && (strtolower($parts[$i]['type'])=='alternative' || strtolower($parts[$i]['type'])=='related'))
					{
						$html_alternative=true;
					} 
				}


				$response['body']='';

				$attachments=array();

				if(/*count($parts)==0 && */eregi('text/html', $response['content_type']))
				{
					$default_mime = 'text/html';
				}else
				{
					$default_mime = 'text/plain';
				}

				$part_count = count($parts);
				
				//block remote URL's if contacts is unknown
				$response['blocked_images']=0;				
				if(!isset($_POST['unblock']))
				{
					require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
					$ab = new addressbook();
					
					$contact = $ab->get_contact_by_email($response['sender'], $GO_SECURITY->user_id);
					$block = !is_array($contact);
				}else
				{
					$block=false;
				}
				
				
				while($part = array_shift($parts))
				{
					$mime = isset($part["mime"]) && $part_count>1 ? strtolower($part["mime"]) : $default_mime;

					if (empty($response['body']) && ($part["name"] == '' || eregi('inline', $part["disposition"]))  && ($mime == "text/html" ||
					($mime == "text/plain" && (!$html_alternative || strtolower($part['type'])!='alternative')) ||
					$mime == "text/enriched" ||
					$mime == "unknown/unknown"))
					{
						$part_body = $imap->view_part($uid, $part["number"], $part["transfer"], $part["charset"]);

						switch($mime)
						{
							case 'unknown/unknown':
							case 'text/plain':

								$part_body = String::text_to_html($part_body);

								break;

							case 'text/html':
								$part_body = String::convert_html($part_body, $block, $response['blocked_images']);
								//	$part = convert_links($part);
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

						$response['body'] .= $part_body;
					}else
					{
						$attachments[]=$part;
					}
				}

				//debug(var_export($attachments, true));

				$response['attachments']=array();
				$index=0;
				for ($i = 0; $i < count($attachments); $i ++) {
					if (
					(
					eregi("ATTACHMENT", $attachments[$i]["disposition"])  ||
					($attachments[$i]["name"] != '' && empty($attachments[$i]["id"])
					)
					&& !($attachments[$i]['type']=='APPLEDOUBLE' && $attachments[$i]['mime']== 'application/APPLEFILE')
					)
					){

						$attachment = $attachments[$i];

						$attachment['index']=$index;
						$attachment['extension']=File::get_extension($attachments[$i]["name"]);
						$response['attachments'][]=$attachment;
						$index++;
						//}elseif (eregi("inline",$attachments[$i]["disposition"]) && !empty($attachments[$i]["id"]))
					}elseif (!empty($attachments[$i]["id"]))
					{
						//when an image has an id it belongs somewhere in the text we gathered above so replace the
						//source id with the correct link to display the image.
						if ($attachments[$i]["id"] != '')
						{
							$tmp_id = $attachments[$i]["id"];
							if (strpos($tmp_id,'>'))
							{
								$tmp_id = substr($attachments[$i]["id"], 1,strlen($attachments[$i]["id"])-2);
							}
							$id = "cid:".$tmp_id;

							$url = $GO_MODULES->modules['email']['url']."attachment.php?account_id=".$account['id']."&mailbox=".urlencode($mailbox)."&amp;uid=".$uid."&amp;part=".$attachments[$i]["number"]."&amp;transfer=".$attachments[$i]["transfer"]."&amp;mime=".$attachments[$i]["mime"]."&amp;filename=".urlencode($attachments[$i]["name"]);
							$response['body'] = str_replace($id, $url, $response['body']);
						}
					}
				}
				break;

							case 'messages':

								$touched_folders=array();

								$account_id = isset ($_REQUEST['account_id']) ? $_REQUEST['account_id'] : 0;
								$mailbox = isset ($_REQUEST['mailbox']) ? ($_REQUEST['mailbox']) : 'INBOX';
								$query = isset($_POST['query']) ? ($_POST['query']) : '';
								
								$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : 0;
								$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : 30;
								
								$nocache=!empty($query);

								$account = connect($account_id, $mailbox);
								$response['drafts']=$account['drafts']==$mailbox;

								if(isset($_POST['delete_keys']))
								{
									$messages = json_decode(($_POST['delete_keys']));

									$imap->set_message_flag($mailbox, $messages, "\\Seen");

									if($mailbox != $account['trash'])
									{
										$response['deleteSuccess']=$imap->move($account['trash'], $messages);
									}else {
										$response['deleteSuccess']=$imap->delete($messages);
									}
									if(!$response['deleteSuccess'])
									{
										$response['deleteFeedback']=$lang['common']['deleteError'];
									}
								}


								if(isset($_POST['action']))
								{
									$messages = json_decode($_POST['messages']);
									switch($_POST['action'])
									{
										case 'move':
											$from_mailbox = ($_REQUEST['from_mailbox']);
											$to_mailbox = ($_REQUEST['to_mailbox']);
											$response['success']=$imap->move($to_mailbox, $messages);

											$touched_folders[]=$to_mailbox;
											
											$nocache=true;
											break;
									}
								}

								if(!isset($folder))
								{
									$folder = $email->get_folder($account['id'],$mailbox);
								}

								$sort_field=isset($_POST['sort']) && $_POST['sort']=='from' ? SORTFROM : SORTARRIVAL;
								$sort_order=isset($_POST['dir']) && $_POST['dir']=='ASC' ? 0 : 1;
								
								require_once($GO_CONFIG->class_path.'cache.class.inc.php');
								$cache = new cache();
				
								$current_folder_status = $imap->status($mailbox, SA_UNSEEN+SA_MESSAGES);

								$imap->check_cache($account, $current_folder_status->unseen, $current_folder_status->messages);
								
								$response['total'] = $imap->sort($sort_field , $sort_order, $query);

								//apply filters
								if($response['total']>0 && strtoupper($mailbox)=='INBOX')
								{
									$filters = array();

									//if there are new messages get the filters
									$email->get_filters($account['id']);
									while ($email->next_record())
									{
										$filter["field"] = $email->f("field");
										$filter["folder"] = $email->f("folder");
										$filter["keyword"] = $email->f("keyword");
										$filter['mark_as_read'] = ($email->f('mark_as_read') == '1');
										$filters[] = $filter;
									}
								}

								$day_start = mktime(0,0,0);
								$day_end = mktime(0,0,0,date('m'),date('d')+1);								
								
								$uids = $imap->get_message_uids($start, $limit);
								
								$messages = $imap->get_message_headers($uids, $folder['id']);
								
								$response['results']=array();
								
								foreach($uids as $uid)
								{
									$message = $messages[$uid];
									if($message['udate']>$day_start && $message['udate']<$day_end)
									{
										$message['date'] = date($_SESSION['GO_SESSION']['time_format'],$message['udate']);
									}else
									{
										$message['date'] = date($_SESSION['GO_SESSION']['date_format'],$message['udate']);
									}

									$subject = $imap->f('subject');
									if(empty($message['subject']))
									{
										$message['subject']=$lang['email']['no_subject'];
									}

									$message['from'] = ($mailbox == $account['sent'] || $mailbox == $account['drafts']) ? $message['to'] : $message['from'];

									if(empty($message['from']))
									{
										if($mailbox==$account['drafts'])
										{
											$message['from'] = $lang['email']['no_recipients_drafts'];
										}else
										{
											$message['from'] = $lang['email']['no_recipients'];
										}
									}									
									$response['results'][]=$message;										
								}								
							
								if(!in_array($mailbox, $touched_folders))
									$touched_folders[]=$mailbox;

								foreach($touched_folders as $touched_folder)
								{
									$status = $touched_folder=='INBOX' ? $current_folder_status : $imap->status($touched_folder, SA_UNSEEN);
									$folder = $email->get_folder($account_id, $touched_folder);

									if(isset($status->unseen))
										$response['unseen'][$folder['id']]=$status->unseen;
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
											if($node_id==0)
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
														
														$children = get_mailbox_nodes($email2->f('id'), 0);
														
														$imap->close();
													}else
													{
														$text = $email2->f('email').' (Error!)';
														$children=array();
													}

													$response[] = array(
														'text'=>$text,
														'id'=>'account_'.$email2->f('id'),
														'iconCls'=>'folder-account',
														'expanded'=>true,
														'account_id'=>$email2->f('id'),
														'folder_id'=>0,
														'mailbox'=>'INBOX',
														'children'=>$children,
														'inbox_new'=>$inbox_new,
														'usage'=>$usage
													);
												}
											}else
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

											$response['total'] = $email->get_accounts($user_id);


											while($email->next_record())
											{
												$user = $GO_USERS->get_user($email->f('user_id'));

												$response['results'][] = array(
					'id'=>$email->f('id'),
					'email'=>$email->f('email'),
					'user_name'=>String::format_name($user),
					'user_id'=>$email->f('user_id'),
					'host'=>$email->f('host'),
					'type'=>$email->f('type')
												);
											}
											break;

										case 'account':
											$email = new email();
											$response['success']=false;
											$response['data']=$email->get_account(($_POST['account_id']));

											if($response['data'])
											{
												$user = $GO_USERS->get_user($response['data']['user_id']);
												$response['data']['user_name']=String::format_name($user['last_name'],$user['first_name'], $user['middle_name']);
												
												$server_response = $email->get_servermanager_mailbox_info($response['data']);
												if(isset($server_response['success']))									
												{
													$response['data']['vacation_active']=$server_response['data']['vacation_active'];
													$response['data']['vacation_subject']=$server_response['data']['vacation_subject'];
													$response['data']['vacation_body']=$server_response['data']['vacation_body'];
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

echo json_encode($response);