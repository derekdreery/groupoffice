<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.168 $ $Date: 2006/11/21 16:25:38 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');
require_once($GO_LANGUAGE->get_language_file('email'));

load_basic_controls();
load_control('htmleditor');
load_control('tooltip');
load_control('dropbox');

require_once($GO_CONFIG->class_path."mail/phpmailer/class.phpmailer.php");
require_once($GO_CONFIG->class_path."mail/phpmailer/class.smtp.php");
require_once($GO_CONFIG->class_path."html2text.class.inc");
require_once($GO_MODULES->class_path."email.class.inc");
$email = new email();

require_once($GO_CONFIG->class_path.'mail/RFC822.class.inc');
$RFC822 = new RFC822();   

//load personal settings
$em_settings = $email->get_settings($GO_SECURITY->user_id);

//Check for templates plugin
$tp_plugin = $GO_MODULES->get_plugin('templates', 'addressbook');
if ($tp_plugin)
{
	require_once($tp_plugin['class_path'].'templates.class.inc');
	$tp = new templates();
}

//check for the addressbook module
$ab_module = isset($GO_MODULES->modules['addressbook']) ? $GO_MODULES->modules['addressbook'] : false;
if (!$ab_module || !$ab_module['read_permission'])
{
	$ab_module = false;
}else
{
	require_once($ab_module['class_path'].'addressbook.class.inc');
	$ab = new addressbook();
}

function close()
{
	if(count($_SESSION['GO_SESSION']['email_module']['unknown_reciepents'])>0)
	{
		header('Location: add_unknown_reciepents.php');	
	}else {
		global $GO_THEME, $GO_CONFIG;
		require_once($GO_THEME->theme_path.'header.inc');
		echo "<script type=\"text/javascript\">\r\nwindow.close();\r\n</script>\r\n";
		require_once($GO_THEME->theme_path.'footer.inc');
	}
	exit();
					
}
//send to contacts in a report?
$report_id = isset($_REQUEST['report_id']) ? $_REQUEST['report_id'] : 0;

$mail_subject = isset($_REQUEST['mail_subject']) ? smart_stripslashes($_REQUEST['mail_subject']) : '';
$mail_body = isset($_REQUEST['mail_body']) ? smart_stripslashes($_REQUEST['mail_body']) : '<font face="Arial"><br /></font>';
$mail_from = isset($_REQUEST['mail_from']) ? $_REQUEST['mail_from'] : 0;
$mail_to = isset($_REQUEST['mail_to']) ? smart_stripslashes($_REQUEST['mail_to']) : '';
$mail_cc = isset($_REQUEST['mail_cc']) ? smart_stripslashes($_REQUEST['mail_cc']) : '';
$mail_bcc = isset($_REQUEST['mail_bcc']) ? smart_stripslashes($_REQUEST['mail_bcc']) : '';
$uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : 0;
$show_cc = ((isset($_POST['show_cc']) && $_POST['show_cc'] == 'true') || !empty($mail_cc)) ? 'true' : 'false';
$show_bcc = ((isset($_POST['show_bcc']) && $_POST['show_bcc'] == 'true') || !empty($mail_bcc)) ? 'true' : 'false';


$html_mail_head = '<html><head><meta http-equiv=Content-Type content="text/html;charset='.$charset.'"><meta content="Group-Office '.$GO_CONFIG->version.'" name="GENERATOR"></head><body>';
$html_mail_foot = '</body></html>';

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$notification_check = isset($_POST['notification']) ? true : false;
}else
{
	$notification_check = $em_settings['request_notification'];
}


$htmleditor = new htmleditor('mail_body') ;

if(!$wysiwyg = $htmleditor->IsCompatible())
{
	$content_type= 'text/PLAIN';
}else
{
	$content_type= isset($_POST['content_type']) ? $_POST['content_type'] : $em_settings['send_format'];
}



$page_title = $ml_compose;
$sendaction = isset($_REQUEST['sendaction']) ? $_REQUEST['sendaction'] : '';
$attachments_size = 0;

function add_unknown_reciepent($email, $name, $addressbook_id)
{
	global $GO_SECURITY, $GO_USERS, $ab;

	$name_arr = split_name($name);

	if($name_arr['first'] == '' && $name_arr['last'] == '')
	{
		$name_arr['first'] = $email;
	}

	

	if (!$ab->get_contact_by_email(addslashes($email),$GO_SECURITY->user_id) && !$GO_USERS->get_authorized_user_by_email($GO_SECURITY->user_id, addslashes($email)))
	{
		$contact['addressbook_id'] = $addressbook_id;
		$contact['first_name'] = addslashes($name_arr['first']);
		$contact['middle_name'] = addslashes($name_arr['middle']);
		$contact['last_name'] = addslashes($name_arr['last']);
		$contact['email'] = addslashes($email);		
		//$ab->add_contact($contact);
		$_SESSION['GO_SESSION']['email_module']['unknown_reciepents'][]=$contact;
	}
}

if($sendaction == 'save_draft')
{
	$save_draft = true;
	$sendaction ='send';
}
switch ($sendaction)
{
	case 'send':
		
		if (!isset($_POST['mail_from']))
		{
			$profile = $GO_USERS->get_user($GO_SECURITY->user_id);
			$middle_name = $profile['middle_name'] == '' ? '' : $profile['middle_name'].' ';
			$name = $profile['first_name'].' '.$middle_name.$profile['last_name'];
		}else
		{
			$profile = $email->get_account($_POST['mail_from']);
			$name = $profile["name"];
		}

		$mail = new PHPMailer();
		$mail->CharSet=$charset;
		$mail->PluginDir = $GO_CONFIG->class_path.'mail/phpmailer/';
		$mail->SetLanguage($php_mailer_lang, $GO_CONFIG->class_path.'mail/phpmailer/language/');

		switch($GO_CONFIG->mailer)
		{
			case 'smtp':
				$mail->Host = $GO_CONFIG->smtp_server;
				$mail->Port = $GO_CONFIG->smtp_port;
				$mail->IsSMTP();	
				if(!empty($GO_CONFIG->smtp_username))
				{
					$mail->SMTPAuth=true;
					$mail->Username=$GO_CONFIG->smtp_username;			
					$mail->Password = $GO_CONFIG->smtp_password;
				}		  
				break;			
			case 'qmail':
				$mail->IsQmail();
				break;			
			case 'sendmail':
				$mail->IsSendmail();
				break;
			case 'mail':
				$mail->IsMail();
				break;
		}
		$mail->Priority = $_POST['priority'];
		$mail->Sender     = $profile["email"];    
		$mail->From     = $profile["email"];
		$mail->FromName = $name;
		$mail->AddReplyTo($profile["email"],$name);
		$mail->WordWrap = 76;
		//$mail->Encoding = "quoted-printable";

		if (isset($_POST['notification']))
		{
			$mail->ConfirmReadingTo = $profile["email"];
		}
		$html_message = $content_type == 'text/HTML' ? true : false;
		$mail->IsHTML($html_message);
		$mail->Subject = smart_stripslashes(trim($mail_subject));

		if (isset($_SESSION['url_replacements']))
		{
			while($url_replacement = array_shift($_SESSION['url_replacements']))
			{
				$mail_body=str_replace($url_replacement['url'], "cid:".$url_replacement['id'], $mail_body);
			}
			unset($_SESSION['url_replacements']);
		}

		// Getting the attachments
		if (isset($_SESSION['attach_array']))
		{
			for ($i=0;$i<count($_SESSION['attach_array']);$i++)
			{
				// If the temporary file exists, attach it
				$tmp_file = stripslashes($_SESSION['attach_array'][$i]['tmp_file']);
				if (file_exists($tmp_file))
				{
					if ($_SESSION['attach_array'][$i]['disposition'] == 'attachment' || strpos($mail_body, $_SESSION['attach_array'][$i]['content_id']))
					{
						if ($_SESSION['attach_array'][$i]['disposition'] == 'attachment')
						{
							$mail->AddAttachment($tmp_file, $_SESSION['attach_array'][$i]['file_name'], 'base64',  $_SESSION['attach_array'][$i]['file_mime']) ;
						}else
						{
							$mail->AddEmbeddedImage($tmp_file, $_SESSION['attach_array'][$i]['content_id'], imap_8bit($_SESSION['attach_array'][$i]['file_name']), 'base64',  $_SESSION['attach_array'][$i]['file_mime']);
						}
					}
				}
			}
		}
		$_SESSION['GO_SESSION']['email_module']['unknown_reciepents']=array();
		
		$mail_to_array = $RFC822->parse_address_list($mail_to);
		foreach ($mail_to_array as $to_address)
		{     	
			$mail->AddAddress($to_address['email'], $to_address['personal']);

			if ($em_settings['add_recievers'] > 0 && $ab_module)
			{
				add_unknown_reciepent($to_address['email'], $to_address['personal'], $em_settings['add_recievers']);
			}
		}
		$mail_cc_array = $RFC822->parse_address_list($mail_cc);
		foreach ($mail_cc_array as $cc_address)
		{
			$mail->AddCC($cc_address['email'], $cc_address['personal']);

			if ($em_settings['add_recievers'] > 1 && $ab_module)
			{
				add_unknown_reciepent($cc_address['email'], $cc_address['personal'], $em_settings['add_recievers']);
			}
		}

		$mail_bcc_array = $RFC822->parse_address_list($mail_bcc);
		foreach ($mail_bcc_array as $bcc_address)
		{
			$mail->AddBCC($bcc_address['email'], $bcc_address['personal']);

			if ($em_settings['add_recievers'] > 1 && $ab_module)
			{
				add_unknown_reciepent($bcc_address['email'], $bcc_address['personal'], $em_settings['add_recievers']);
			}
		}
		
		if(function_exists('iconv') && $em_settings['charset'] != $charset)
		{
			$mail->CharSet=$em_settings['charset'];
		}

		if ($html_message)
		{
			if($mail->CharSet!=$charset)
			{
				$html_mail_head = str_replace($charset, $mail->CharSet, $html_mail_head);
			}			
			$mail->Body = $html_mail_head.$mail_body.$html_mail_foot;
			$htmlToText = new Html2Text ($mail_body);
			$mail->AltBody = $htmlToText->get_text();		
			
		}else
		{
			$mail->Body = $mail_body;
		}
		
		if($mail->CharSet!=$charset)
		{
			$mail->recode($charset);
		}
		

		if(isset($save_draft))
		{
			//set Line enidng to \r\n for Cyrus IMAP
			$mail->LE = "\r\n";
			$mime = $mail->GetMime();
			require_once($GO_CONFIG->class_path."mail/imap.class.inc");
			$imap_stream = new imap();

			$account = $email->get_account($_REQUEST['mail_from']);

			if ($imap_stream->open($account["host"], "imap", $account["port"], $account["username"], $account["password"], $account['drafts'], 0, $account['use_ssl'], $account['novalidate_cert']))
			{
				if ($imap_stream->append_message($account['drafts'], $mime,"\\Draft \\Seen"))
				{
					if (isset($_SESSION['attach_array']))
					{
						while($attachment = array_shift($_SESSION['attach_array']))
						{
							@unlink($attachment->tmp_file);
						}
					}
					// We need to unregister the attachments array
					unset($_SESSION['attach_array']);

					$imap_stream->close();
					require_once($GO_THEME->theme_path."header.inc");
					echo "<script type=\"text/javascript\">\r\nwindow.close();\r\n</script>\r\n";
					require_once($GO_THEME->theme_path."footer.inc");
					exit();
				}else
				{
					$feedback = '<p class="Error">'.$ml_save_draft_error.' '.$mail->ErrorInfo.'</p>';
				}
			}
		}else
		{
			if(!$mail->Send())
			{
				$feedback = '<p class="Error">'.$ml_send_error.' '.$mail->ErrorInfo.'</p>';
			}else
			{
				//set Line enidng to \r\n for Cyrus IMAP
				$mail->LE = "\r\n";
				$mime = $mail->GetMime();


				if (isset($_SESSION['attach_array']))
				{
					while($attachment = array_shift($_SESSION['attach_array']))
					{
						@unlink($attachment->tmp_file);
					}
				}
				// We need to unregister the attachments array
				unset($_SESSION['attach_array']);

				if (isset($profile["type"]) && $profile["type"] == "imap")
				{
					$sent_folder = $profile['sent'];
					if ($sent_folder != '')
					{
						require_once($GO_CONFIG->class_path."mail/imap.class.inc");
						$imap_stream = new imap();
						if (!empty($mime) && $imap_stream->open($profile["host"], "imap", $profile["port"], $profile["username"], $profile["password"], $sent_folder, 0, $profile['use_ssl'], $profile['novalidate_cert']))
						{
							if ($imap_stream->append_message($sent_folder, $mime,"\\Seen"))
							{
								if (isset($_REQUEST['action']))
								{
									if($_REQUEST['action']== "reply" || $_REQUEST['action'] == "reply_all")
									{
										$uid_arr = array($_REQUEST['uid']);
										$imap_stream->set_message_flag($_POST['mailbox'], $uid_arr, "\\Answered");
									}
								}

								$imap_stream->close();
								close();
							}
						}
						require_once($GO_THEME->theme_path."header.inc");
						echo "<script type=\"text/javascript\">\r\nalert('".$ml_sent_items_fail."');\r\nwindow.close();\r\n</script>\r\n";
						require_once($GO_THEME->theme_path.'footer.inc');
						exit();
					}else
					{
						close();
					}
				}else
				{
					close();
				}
			}
		}

		break;
}

//if a template id is given then process it
$template_id = isset($_REQUEST['template_id']) ? $_REQUEST['template_id'] : 0;
$contact_id = isset($_REQUEST['contact_id']) ? $_REQUEST['contact_id'] : 0;
$company_id = isset($_REQUEST['company_id']) ? $_REQUEST['company_id'] : 0;




if ($tp_plugin)
{
	$template_count = $tp->get_authorized_templates($GO_SECURITY->user_id, EMAIL_TEMPLATE);
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($_SERVER['REQUEST_METHOD'] != "POST" && $tp_plugin && $template_id == 0 && 
		$template_count > 0 && $action != 'open')
{
	require_once($GO_THEME->theme_path."header.inc");
	echo '<form name="sendform" method="post" action="'.$_SERVER['PHP_SELF'].'">';
	if($uid > 0)
	{
		//echo '<input type="hidden" name="account_id" value="'.$_REQUEST['account_id'].'" />';
		echo '<input type="hidden" name="uid" value="'.$uid.'" />';
		echo '<input type="hidden" name="mailbox" value="'.$_REQUEST['mailbox'].'" />';
		echo '<input type="hidden" name="action" value="'.$_REQUEST['action'].'" />';
	}
	if(isset($_REQUEST['email_file']))
	{
		echo '<input type="hidden" name="email_file" value="true" />';
	}

	echo '<input type="hidden" name="mail_subject" value="'.$mail_subject.'" />';
	echo '<input type="hidden" name="mail_body" value="'.htmlspecialchars(smart_stripslashes($mail_body, true)).'" />';
	echo '<input type="hidden" name="mail_to" value="'.htmlspecialchars($mail_to,ENT_QUOTES).'" />';
	echo '<input type="hidden" name="mail_cc" value="'.$mail_cc.'" />';
	echo '<input type="hidden" name="mail_bcc" value="'.$mail_bcc.'" />';
	echo '<input type="hidden" name="mail_from" value="'.$mail_from.'" />';
	echo '<input type="hidden" name="contact_id" value="'.$contact_id.'" />';
	echo '<input type="hidden" name="company_id" value="'.$company_id.'" />';
	echo '<input type="hidden" name="template_id" />';
	echo '<input type="hidden" name="report_id" value="'.$report_id.'" />';
	echo '<input type="hidden" name="sendaction" value="load_template" />';
	if($notification_check)
	{
		echo '<input type="hidden" name="notification" value="true" />';
	}
	//get the addressbook language file
	require_once($GO_LANGUAGE->get_language_file('addressbook'));
	$tabstrip = new tabstrip('templates_tab', $ab_templates);
	$tabstrip->set_attribute('style','width:100%');
	$tabstrip->add_html_element(new html_element('p', $ab_select_template));

	$link = new hyperlink("javascript:document.forms[0].template_id.value='0';document.forms[0].submit();",$ab_no_template);
	$link->set_attribute('class','selectableItem');
	$tabstrip->add_html_element($link);

	while($tp->next_record())
	{
		$link = new hyperlink('javascript:document.forms[0].template_id.value=\''.$tp->f('id').'\';document.forms[0].submit();',$tp->f('name'));
		$link->set_attribute('class','selectableItem');
		$tabstrip->add_html_element($link);
	}

	$tabstrip->add_html_element(new button($cmdClose, "javascript:window.close()"));
	echo $tabstrip->get_html();

}else
{

	//reset attachments array in case user aborted a message or changed format
	if ($_SERVER['REQUEST_METHOD'] != "POST" || $sendaction=='load_template')
	{
		if (!isset($_REQUEST['email_file']))
		{
			unset($_SESSION['attach_array']);
			unset($_SESSION['url_replacements']);
		}
	}
	$ids=array();
	//get users email accounts to determine from addresses
	$count = $email->get_accounts($GO_SECURITY->user_id);
	while ($email->next_record())
	{
		if ($mail_from == 0)
		{
			$mail_from = $email->f('id');
		}
		$addresses[] = $email->f("email");
		$names[] = $email->f("email")." (".$email->f("name").")";
		$ids[] = $email->f("id");
	}



	$signature = '';
	if ($mail_from > 0)
	{
		$account = $email->get_account($mail_from);

		if ($account['signature'] != '')
		{
			if ($content_type == 'text/HTML')
			{
				$signature = '<br />'.text_to_html($account['signature']).'<br /><br />';
			}else
			{
				$signature = "\r\n".$account['signature']."\r\n\r\n";
			}
		}
	} 

	//if a uid is given then the user is replying or forwarding
	if ($uid > 0 && ($_SERVER['REQUEST_METHOD']  != 'POST' || $sendaction == 'load_template' || $sendaction == 'change_format'))
	{
		//get the original message
		require_once($GO_CONFIG->class_path."mail/imap.class.inc");
		$mail = new imap();

		$account = $email->get_account($mail_from);

		if ($account && $mail->open(
			$account['host'], 
			$account['type'], 
			$account['port'], 
			$account['username'], 
			$account['password'], 
			$_REQUEST['mailbox'], 
			0, 
			$account['use_ssl'], 
			$account['novalidate_cert']))
		{
			$preferred_type = ($content_type=='text/HTML') ? 'html' : 'text';
			$content = $mail->get_message($uid, $preferred_type,"");
			$parts = array_reverse($mail->f("parts"));

			//fill in the header fields
			$subject = isset($content['subject']) ? $content['subject'] : $ml_no_subject;
			switch($_REQUEST['action'])
			{
				case 'open':
					$mail_subject = $subject;
					if (isset($content["to"]))
					{
						for ($i=0;$i<sizeof($content["to"]);$i++)
						{

							if ($content["to"][$i] != "" && !in_array(get_email_from_string($content["to"][$i]),$addresses))
							{
								if (!isset($first))
								{
									$first = true;
								}else
								{
									$mail_to .= ',';
								}
								$mail_to .= $content["to"][$i];
							}
						}
					}
					unset($first);
					if (isset($content["cc"]))
					{
						$show_cc = 'true';
						for ($i=0;$i<sizeof($content["cc"]);$i++)
						{				
							if ($content["cc"][$i] != "" && !in_array(get_email_from_string($content["cc"][$i]),$addresses))
							{
								if (!isset($first))
								{
									$first = true;
								}else
								{
									$mail_cc .= ',';
								}
								$mail_cc .= $content["cc"][$i];
							}
						}
					}
					if($sendaction != 'change_format')
					{
						for ($i=0;$i<count($parts);$i++)
						{
							if (eregi("attachment", $parts[$i]["disposition"]) ||
									($parts[$i]["id"] == '' && eregi("inline", $parts[$i]["disposition"]))
									&& !eregi("message/RFC822", $parts[$i]["mime"]))
							{
								$file = $mail->view_part($uid, $parts[$i]["number"], $parts[$i]["transfer"]);

								$name = $parts[$i]['name'] != '' ? $parts[$i]['name'] : 'attach_'.$i;

								$tmp_file = $GO_CONFIG->tmpdir.md5(uniqid(time()));
								$fp = fopen($tmp_file,"wb");
								fwrite ($fp,$file);
								fclose($fp);
								$email->register_attachment($tmp_file, $parts[$i]["name"], $parts[$i]["size"], $parts[$i]["mime"], 'attachment', $parts[$i]["id"]);
							}
						}
					}
				break;
				
				case "reply":
					$mail_to = $content["reply_to"];
					if(!eregi('Re:', $subject))
					{
						$mail_subject = 'Re: '.$subject;
					}else
					{
						$mail_subject = $subject;
					}
				break;

				case "reply_all":
					$mail_to = $content["reply_to"];
					if(!eregi('Re:', $subject))
					{
						$mail_subject = 'Re: '.$subject;
					}else
					{
						$mail_subject = $subject;
					}
	
					//add all recievers from this email
					if (isset($content["to"]))
					{						
						for ($i=0;$i<sizeof($content["to"]);$i++)
						{
							if ($content["to"][$i] != "" && !in_array(get_email_from_string($content["to"][$i]),$addresses))
							{
								$mail_to .= ",".$content["to"][$i];
							}
						}
					}
					if (isset($content["cc"]) && count($content["cc"]) > 0)
					{
						$show_cc = 'true';
						for ($i=0;$i<sizeof($content["cc"]);$i++)
						{
							if ($content["cc"][$i] != "" && !in_array(get_email_from_string($content["cc"][$i]),$addresses))
							{
								if (!isset($first))
								{
									$first = true;
								}else
								{
									$mail_cc .= ',';
								}
								$mail_cc .= $content["cc"][$i];
							}
						}
					}

				break;

				case "forward":
					//reattach attachments
					if(!eregi('Fwd:', $subject))
					{
						$mail_subject = 'Fwd: '.$subject;
					}else
					{
						$mail_subject = $subject;
					}

				if($sendaction != 'change_format')
				{
					for ($i=0;$i<count($parts);$i++)
					{
						if 
						(
							(
								eregi("attachment", $parts[$i]["disposition"]) || 
								($parts[$i]['name']!='' && !eregi("inline", $parts[$i]["disposition"])) ||
								($parts[$i]["id"] == '' && eregi("inline", $parts[$i]["disposition"]))
							)
							&& !eregi("message/RFC822", $parts[$i]["mime"])
						)
						{
							$file = $mail->view_part($uid, $parts[$i]["number"], $parts[$i]["transfer"]);

							$name = $parts[$i]['name'] != '' ? $parts[$i]['name'] : 'attach_'.$i;

							$tmp_file = $GO_CONFIG->tmpdir.md5(uniqid(time()));
							$fp = fopen($tmp_file,"wb");
							fwrite ($fp,$file);
							fclose($fp);
							$email->register_attachment($tmp_file, $parts[$i]["name"], $parts[$i]["size"], $parts[$i]["mime"], 'attachment', $parts[$i]["id"]);
						}
					}
				}
				break;
			}

			//reatach inline attachements
			for ($i=0;$i<count($parts);$i++)
			{
				if ($parts[$i]["id"] != '')// && eregi("inline", $parts[$i]["disposition"]))
				{
					$file = $mail->view_part($uid, $parts[$i]["number"], $parts[$i]["transfer"]);

					$tmp_file = $GO_CONFIG->tmpdir.md5(uniqid(time()));

					$fp = fopen($tmp_file,"wb");
					fwrite ($fp,$file);
					fclose($fp);

					if (strpos($parts[$i]["id"],'>'))
					{
						$parts[$i]["id"] = substr($parts[$i]["id"], 1,strlen($parts[$i]["id"])-2);
					}
					$email->register_attachment($tmp_file, $parts[$i]["name"],
							$parts[$i]["size"], $parts[$i]["mime"], 'inline', $parts[$i]["id"]);
					//Content-ID's that need to be replaced with urls when message is send

					//replace inline images identified by a content id with the url to display the part by Group-Office
					$url_replacement['id'] = $parts[$i]["id"];
					$url_replacement['url'] = $GO_MODULES->modules['email']['url']."attachment.php?account_id=".$mail_from."&amp;mailbox=".$_REQUEST['mailbox']."&amp;uid=".$uid."&amp;part=".$parts[$i]["number"]."&amp;transfer=".$parts[$i]["transfer"]."&amp;mime=".$parts[$i]["mime"]."&amp;filename=".urlencode($parts[$i]["name"]);
					$_SESSION['url_replacements'][] = $url_replacement;
				}
			}

			$html_message_count = 0;
			for ($i=0;$i<count($parts);$i++)
			{
				if($content_type=='text/HTML')
				{
					$mime = strtolower($parts[$i]["mime"]);

					if (!eregi("attachment", $parts[$i]["disposition"]))
					{
						switch ($mime)
						{
							case 'text/plain':
								$html_part = text_to_html($mail->view_part($uid,
											$parts[$i]["number"], $parts[$i]["transfer"], $parts[$i]['charset']), false);
								$mail_body .= $html_part;
								break;

							case 'text/html':
								$html_part = convert_html($mail->view_part($uid,
											$parts[$i]["number"], $parts[$i]["transfer"], $parts[$i]['charset']));
								$mail_body .= $html_part;
								break;

							case 'text/enriched':
								$html_part = enriched_to_html($mail->view_part($uid,
											$parts[$i]["number"], $parts[$i]["transfer"], $parts[$i]['charset']), false);
								$mail_body .= $html_part;
								break;
						}
					}
				}else
				{
					if (strtolower($parts[$i]["mime"]) == "text/plain" &&
							!eregi("ATTACHMENT", $parts[$i]["disposition"]))
					{
						$mail_body .= $mail->view_part($uid, $parts[$i]["number"], $parts[$i]["transfer"], $parts[$i]['charset']);						
					}

					//add html messages as an attachment since we don't have an html editor to display it coreect yet
					if (strtolower($parts[$i]["mime"]) == "text/html" &&
							!eregi("ATTACHMENT", $parts[$i]["disposition"]))
					{
						if ($parts[$i]["name"] == '' && $parts[$i]["mime"] == "text/HTML")
						{
							if ($html_message_count == 0)
								$parts[$i]["name"] = $content["sender"].".html";
							else
								$parts[$i]["name"] = $content["sender"]."(".$html_message_count.").html";

							$html_message_count++;
						}

//						$file = $mail->view_part($uid, $parts[$i]["number"],
//								$parts[$i]["transfer"], $parts[$i]["mime"], $parts[$i]['charset']);
						$file = $mail->view_part($uid, $parts[$i]["number"],
								$parts[$i]["transfer"], $parts[$i]['charset']);

						$tmp_file = $GO_CONFIG->tmpdir.md5(uniqid(time()));

						$fp = fopen($tmp_file,"w");
						fwrite ($fp,$file);
						fclose($fp);

						$email->register_attachment($tmp_file, $parts[$i]["name"],
								$parts[$i]["size"], $parts[$i]["mime"]);
					}
				}
			}

			if ($content_type=='text/HTML')
			{
				if ($mail_body != '')
				{
					//replace inline images with the url to display the part by Group-Office
					if (isset($_SESSION['url_replacements']))
					{
						for ($i=0;$i<count($_SESSION['url_replacements']);$i++)
						{
							$mail_body = str_replace('cid:'.$_SESSION['url_replacements'][$i]['id'], $_SESSION['url_replacements'][$i]['url'], $mail_body);
						}
					}
				}
				if($_REQUEST['action'] != 'open')
				{
					$header_om  = '<font face="verdana" size="2">'.$ml_original_follows."<br />";
					$om_to = '';
					if (isset($content))
					{
						$header_om .= "<b>".$ml_subject.":&nbsp;</b>".addslashes($subject)."<br />";
						$header_om .= '<b>'.$ml_from.": &nbsp;</b>".$content['from'].' &lt;'.$content["sender"]."&gt;<br />";
						if (isset($content['to']))
						{
							for ($i=0;$i<sizeof($content["to"]);$i++)
							{
								if ($i!=0)	$om_to .= ',';
								$om_to .= $content["to"][$i];	
							}
						}else
						{
							$om_to=$ml_no_reciepent;
						}
						$header_om .= "<b>".$ml_to.":&nbsp;</b>".htmlspecialchars($om_to)."<br />";
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

						$header_om .= "<b>".$strDate.":&nbsp;</b>".date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'],$content["udate"])."<br />";
					}
					$header_om .= "</font><br /><br />";
					
					$mail_body = $signature.'<br /><blockquote style="border:0;border-left: 2px solid #22437f; padding:0px; margin:0px; padding-left:5px; margin-left: 5px; ">'.$header_om.$mail_body.'</blockquote>';
				}

			}else
			{
				if($_REQUEST['action'] != 'open')
				{
					$header_om  = $ml_original_follows."\r\n";
					if (isset($content))
					{
						$header_om .= $ml_subject.": ".$subject."\r\n";
						$header_om .= $ml_from.": ".$content['from'].' <'.$content["sender"].">\r\n";
						$om_to = '';
						if (isset($content['to']))
						{
							for ($i=0;$i<sizeof($content["to"]);$i++)
							{
								if ($i!=0)	$om_to .= ',';								
								$om_to .= $content["to"][$i];	
							}
							$header_om .= $ml_to.": ".$om_to."\r\n";
						}else
						{
							$om_to = $ml_no_reciepent;
						} 
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
							$header_om .= "CC: ".$om_cc."\r\n";
						}

						$header_om .= $strDate.": ".date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'],$content["udate"])."\r\n\r\n\r\n";
					}

					if ($html_message_count > 0)
					{
						$mail_body = $ml_html_message_attached."\r\n\r\n".$header_om.$mail_body;
					}else
					{
						$mail_body = $header_om.$mail_body;
					}
					
					$mail_body = $signature.quote($mail_body);
				}
			}
		}
		$mail->close();
	}else
	{
		if($mail_body == '')
		{
			$mail_body = $signature;
		}
	}
	
	$min_height = -195;
	if(count($ids) > 1)
	{
		$min_height -= 25;
	}
	if($show_cc == 'true') $min_height -= 52;
	if($show_bcc == 'true') $min_height -= 52;

	
	$GO_HEADER['body_arguments'] = 'onload="document.sendform.mail_to.focus();" onUnLoad="close_attachments();"';
	$GO_HEADER['head'] = tooltip::get_header();
	require_once($GO_THEME->theme_path."header.inc");
	require_once("compose.inc");
}
require_once($GO_THEME->theme_path."footer.inc");
