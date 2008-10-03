<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: json.php 2954 2008-09-03 11:35:34Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require('../../Group-Office.php');


$GO_SECURITY->json_authenticate('email');

//ini_set('display_errors','off');

require_once ($GO_CONFIG->class_path."mail/imap.class.inc");
require_once ($GO_MODULES->modules['email']['class_path']."email.class.inc");
require_once ($GO_LANGUAGE->get_language_file('email'));
require_once($GO_CONFIG->class_path.'filesystem.class.inc');

$imap = new imap();
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
	global $lang, $imap, $inbox_new;

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

		$status = $imap->status($email->f('name'), SA_ALL);

		if($email->f('name')=='INBOX')
		{
			$inbox_new += $status->unseen;
		}

		if ($status->unseen > 0)
		{
			$status_html = '&nbsp;<span id="status_'.$email->f('id').'">('.$status->unseen.')</span>';
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
				'unseen'=>$status->unseen,
				'mailbox'=>$email->f('name')
			);
		}else {
			$response[] = array(
				'text'=>$folder_name.$status_html,
				'id'=>'folder_'.$email->f('id'),
				'iconCls'=>'folder-default',
				'account_id'=>$email->f('account_id'),
				'folder_id'=>$email->f('id'),
				'mailbox'=>$email->f('name'),
				'unseen'=>$status->unseen,
				'expanded'=>true,
				'children'=>array()
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


	$response['data'] = $go2mime->mime2GO($template['content'], $GO_MODULES->modules['mailings']['url'].'mime_part.php?template_id='.$template_id, true);

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
					$values = array_map('htmlspecialchars', $ab->Record);
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


		$account_id = smart_addslashes($_POST['account_id']);
		$uid = smart_addslashes($_POST['uid']);
		$mailbox = smart_stripslashes($_POST['mailbox']);



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

							if ($content["to"][$i] != "" && $account['email']!=$email && !in_array($email,$addresses))
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

							if ($content["cc"][$i] != "" && $account['email']!=$email && !in_array($email,$addresses))
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

								if ($content["to"][$i] != "" && !in_array(String::get_email_from_string($content["to"][$i]),$addresses))
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
								if ($content["cc"][$i] != "" && !in_array(String::get_email_from_string($content["cc"][$i]),$addresses))
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
					$header_om .= "<b>".$lang['email']['subject'].":&nbsp;</b>".addslashes($subject)."<br />";
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
				$template_id = smart_addslashes($_POST['template_id']);
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

				$template_id=smart_addslashes($_REQUEST['template_id']);
				$to=smart_addslashes($_REQUEST['to']);



				$response = load_template($template_id, $to, isset($_POST['mailing_group_id']) && $_POST['mailing_group_id']>0);

				$response['success']=true;
				break;

			case 'filters':

				if(isset($_POST['delete_keys']))
				{
					$filters = json_decode(smart_stripslashes($_POST['delete_keys']));

					foreach($filters as $filter_id)
					{
						$email->delete_filter($filter_id);
					}
					$response['deleteSuccess']=true;
				}
				$response['total']=$email->get_filters(smart_addslashes($_POST['account_id']));
				$response['results']=array();
				while($email->next_record(MYSQL_ASSOC))
				{
					$response['results'][] = $email->Record;
				}

				break;

			case 'filter':

				$response['success']=false;
				$response['data']=$email->get_filter(smart_addslashes($_POST['filter_id']));
				if($response['data'])
				{
					$response['success']=true;
				}

				break;

			case 'message':

				$account_id = smart_stripslashes($_REQUEST['account_id']);
				$mailbox = smart_stripslashes($_REQUEST['mailbox']);
				$uid = $_REQUEST['uid'];

				$account = connect($account_id, $mailbox);

				$response = $imap->get_message($uid);

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
				$response['size']=Number::format_size($response['size']);

				$parts = array_reverse($imap->f("parts"));


				/*
				 * Sometimes clients send multipart/alternative but there's only a text part. FIrst check if there's
				 * a html alternative to display
				 */
				$html_alternative=false;
				for($i=0;$i<count($parts);$i++)
				{
					if(strtolower($parts[$i]['mime'])=='text/html' && strtolower($parts[$i]['type'])=='alternative')
					{
						$html_alternative=true;
					} 
				}


				$response['body']='';

				$attachments=array();

				if(count($parts)==0 && eregi('text/html', $response['content_type']))
				{
					$default_mime = 'text/html';
				}else
				{
					$default_mime = 'text/plain';
				}

				while($part = array_shift($parts))
				{

					$mime = isset($part["mime"]) ? strtolower($part["mime"]) : $default_mime;



					//go_log(LOG_DEBUG, $part['name'].' -> '.$mime);

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
								$part_body = String::convert_html($part_body);
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
								$mailbox = isset ($_REQUEST['mailbox']) ? smart_stripslashes($_REQUEST['mailbox']) : 'INBOX';
								$query = isset($_POST['query']) ? smart_stripslashes($_POST['query']) : '';

								$account = connect($account_id, $mailbox);

								$response['drafts']=$account['drafts']==$mailbox;


								if(isset($_POST['delete_keys']))
								{

									$messages = json_decode(smart_stripslashes($_POST['delete_keys']));

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
									$messages = json_decode(smart_stripslashes($_POST['messages']));
									switch($_POST['action'])
									{
										/*
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
										 */
										case 'move':

											$from_mailbox = smart_stripslashes($_REQUEST['from_mailbox']);
											$to_mailbox = smart_stripslashes($_REQUEST['to_mailbox']);
											$response['success']=$imap->move($to_mailbox, $messages);

											$touched_folders[]=$to_mailbox;

											//var_dump($response['success']);
											//echo $to_mailbox;
											//exit();

											break;
									}


									//BAD DOES SECOND CONNECT
									//sync folder statuses
									//$email->cache_account_status($account);
								}






								if(!isset($folder))
								{
									$folder = $email->get_folder($account['id'],$mailbox);
								}

								$sort_field=isset($_POST['sort']) && $_POST['sort']=='from' ? SORTFROM : SORTARRIVAL;
								$sort_order=isset($_POST['dir']) && $_POST['dir']=='ASC' ? 0 : 1;

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

								//$date = getdate();

								$day_start = mktime(0,0,0);
								$day_end = mktime(0,0,0,date('m'),date('d')+1);



								function get_messages($start, $limit)
								{
									global $imap, $mailbox, $GO_THEME, $filters, $day_start, $day_end, $lang, $account, $touched_folders;

									$imap->get_messages($start, $limit);

									//require($GO_CONFIG->class_path.'mail/RFC822.class.inc');
									$RFC822 = new RFC822();

									$messages=array();

									$filtered=0;

									//while($imap->next_message(($account['examine_headers']=='1' || isset($_POST['examine_headers']))))
									while($imap->next_message(true))
									{
										$continue=false;
										if(strtoupper($mailbox)=='INBOX' && $imap->f('new'))
										{
											for ($i=0;$i<sizeof($filters);$i++)
											{
												$field = $imap->f($filters[$i]["field"]);
												if (!is_array($field))
												{
													$field = array($field);
												}
												for ($x=0;$x<sizeof($field);$x++)
												{
													if (stristr($field[$x], $filters[$i]["keyword"]))
													{
														$move_messages = array($imap->f("uid"));

														if($filters[$i]['mark_as_read'])
														{
															$ret = $imap->set_message_flag($mailbox, $move_messages, "\\Seen");
														}
														if ($imap->move($filters[$i]["folder"], $move_messages))
														{
															if(!in_array($filters[$i]["folder"], $touched_folders))
															{
																$touched_folders[]=$filters[$i]["folder"];
															}

															$response['total']--;
															$continue = true;
															break;
														}
													}
												}
													
											}
										}

										if ($continue)
										{
											$filtered++;
											continue;
										}


										//echo $imap->f('subject').' - ';

										if($imap->f('udate')>$day_start && $imap->f('udate')<$day_end)
										{
											$date = date($_SESSION['GO_SESSION']['time_format'],$imap->f('udate'));
										}else
										{
											$date = date($_SESSION['GO_SESSION']['date_format'],$imap->f('udate'));
										}


										$subject = $imap->f('subject');
										if(empty($subject))
										{
											$subject=$lang['email']['no_subject'];
										}

										$from = $mailbox == $account['sent'] ? implode(', ', $imap->f('to')) : $imap->f('from');

										//go_log(LOG_DEBUG, $mailbox.' = '.$account['sent']);

										$messages[]=array(
							'uid'=>$imap->f('uid'),
							'new'=>$imap->f('new'),
							'subject'=>$subject,
							'from'=>htmlspecialchars($from, ENT_QUOTES, 'UTF-8'),
							'size'=>Number::format_size($imap->f('size')),
							'date'=>$date,
							'attachments'=>$imap->f('attachments'),
							'flagged'=>$imap->f('flagged'),
							'answered'=>$imap->f('answered'),
							'priority'=>$imap->f('priority')
										);
									}

									if($filtered>0)
									{//echo $start+$offset-$filtered;
										//some messages were filtered away. We need to get some more.
										$extra_messages = get_messages($start+$limit, $filtered);
										//echo ($start+$limit-$filtered).' '.$filtered;
										//	var_dump($extra_messages);
										$messages = array_merge($messages, $extra_messages);
									}

									return $messages;
								}

								$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : 0;
								$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : 0;


								$response['results'] = get_messages($start, $limit);


								//Show unseen status

								if(!in_array($mailbox, $touched_folders))
								$touched_folders[]=$mailbox;

								foreach($touched_folders as $touched_folder)
								{
									$status = $imap->status($touched_folder, SA_UNSEEN);
									$folder = $email->get_folder($account_id, addslashes($touched_folder));
									//$cached_folder = $email->cache_folder_status($imap, $account_id, $touched_folder);

									if(isset($status->unseen))
									$response['unseen'][$folder['id']]=$status->unseen;
								}
								break;

										case 'tree':

											$email = new email();



											//$account_id=isset($_REQUEST['account_id']) ? smart_addslashes($_REQUEST['account_id']) : 0;
											//$folder_id=isset($_REQUEST['folder_id']) ? smart_addslashes($_REQUEST['folder_id']) : 0;

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

													$inbox_new=0;
													if($account)
													{
														$text = $email2->f('email');
														$children = get_mailbox_nodes($email2->f('id'), 0);
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
						'inbox_new'=>$inbox_new
													);
													
													$imap->close();													
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


											$account_id = smart_addslashes($_POST['account_id']);
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
													$deleteAccounts = json_decode(smart_stripslashes($_POST['delete_keys']));

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
											$response['data']=$email->get_account(smart_addslashes($_POST['account_id']));


											if($response['data'])
											{
												$user = $GO_USERS->get_user($response['data']['user_id']);
												$response['data']['user_name']=String::format_name($user['last_name'],$user['first_name'], $user['middle_name']);


												if(isset($GO_MODULES->modules['serverclient']))
												{
													require_once($GO_MODULES->modules['serverclient']['class_path'].'serverclient.class.inc.php');
													$sc = new serverclient();

													foreach($sc->domains as $domain)
													{
														if(strpos($response['data']['username'], '@'.$domain))
														{

															$sc->login();

															$params=array(
															//'sid'=>$sc->sid,
																'task'=>'serverclient_get_mailbox',
																'username'=>$response['data']['username'],
																'password'=>$response['data']['password']														
															);
															$server_response = $sc->send_request($GO_CONFIG->serverclient_server_url.'modules/postfixadmin/json.php', $params);
															//go_log(LOG_DEBUG, var_export($server_response, true));
															$server_response = json_decode($server_response, true);
															if(isset($server_response['success']))
															{
																$response['data']['vacation_active']=$server_response['data']['vacation_active'];
																$response['data']['vacation_subject']=$server_response['data']['vacation_subject'];
																$response['data']['vacation_body']=$server_response['data']['vacation_body'];
															}
															break;
														}
													}
												}



												$response['success']=true;
											}


											break;

										case 'all_folders':
											$account_id = smart_addslashes($_POST['account_id']);

											if(isset($_POST['deleteFolders']))
											{


												$deleteFolders = json_decode(smart_stripslashes($_POST['deleteFolders']));
												if(count($deleteFolders))
												{
													$account = connect($account_id);

													foreach($deleteFolders as $folder_id)
													{
														if($folder = $email->get_folder_by_id(smart_addslashes($folder_id)))
														{
															if($imap->delete_folder($folder['name'], $account['mbroot']))
															{
																$email->delete_folder($account_id, addslashes($folder['name']));
															}

														}
													}
													//$imap->close();
												}
											}

											$response['total']=$email->get_folders($account_id);
											$response['data']=array();
											while($email->next_record(MYSQL_ASSOC))
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
											$account_id = smart_addslashes($_POST['account_id']);

											$hide_inbox = isset($_POST['hideInbox']) && $_POST['hideInbox']=='true';

											$response['total']=$email->get_subscribed($account_id);
											$response['data']=array();
											while($email->next_record(MYSQL_ASSOC))
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