<?php
/**
 * @copyright Copyright Intermesh 2006
 * @version $Revision: 1.46 $ $Date: 2006/04/12 15:09:08 $
 * 
 * @author Merijn Schering <mschering@intermesh.nl>

   This program is protected by copyright law and the Group-Office Professional license.

   You should have received a copy of the Group-Office Proffessional license
   along with Group-Office; if not, write to:
   
   Intermesh
   Reitscheweg 37
   5232BX Den Bosch
   The Netherlands   
   
   info@intermesh.nl
   
 * @package E-mail viewer
 * @category Filesystem
 */


require_once("../../Group-Office.php");

//authenticate the user
$GO_SECURITY->authenticate();

require_once($GO_CONFIG->class_path."mail/mimeDecode.class.inc");
require_once($GO_LANGUAGE->get_language_file('email'));

if(isset($_REQUEST['path']))
{
	$path = smart_stripslashes($_REQUEST['path']);
	$params['input'] = file_get_contents($path);
}else
{
	
	require_once($GO_CONFIG->class_path."mail/imap.class.inc");
	require_once($GO_MODULES->modules['email']['class_path']."email.class.inc");
	$mail = new imap();
	$email = new email();

	$account = $email->get_account($_REQUEST['account_id']);

	if ($mail->open($account['host'], $account['type'],$account['port'],$account['username'],$account['password'], smart_stripslashes($_REQUEST['mailbox']), 0, $account['use_ssl'], $account['novalidate_cert']))
	{
		$params['input'] = $mail->view_part($_REQUEST['uid'], $_REQUEST['part'], $_REQUEST['transfer']);
		$mail->close();
	}
}

$content = '';
$part_number = 0;
$url_replacements = array();
$attachments = array();

$params['include_bodies'] = true;
$params['decode_bodies'] = true;
$params['decode_headers'] = true;



$structure = Mail_mimeDecode::decode($params);

$from = isset($structure->headers['from']) ? htmlspecialchars($structure->headers['from']) : $_SESSION['GO_SESSION']['email'];

/*$to_arr = isset($structure->headers['to']) ? get_addresses_from_string($structure->headers['to']) : array();
$to = implode(', ', $to_arr);
$cc_arr = isset($structure->headers['cc']) ? get_addresses_from_string($structure->headers['cc']) : array();
$cc = implode(', ', $cc_arr);
$bcc_arr = isset($structure->headers['bcc']) ? get_addresses_from_string($structure->headers['bcc']) : array();
$bcc = implode(', ', $bcc_arr);*/

$to = isset($structure->headers['to']) ? htmlspecialchars($structure->headers['to']) : '';
$cc = isset($structure->headers['cc']) ? htmlspecialchars($structure->headers['cc']) : '';
$bcc = isset($structure->headers['bcc']) ? htmlspecialchars($structure->headers['bcc']) : '';


$subject = empty($structure->headers['subject']) ? $ml_no_subject : $structure->headers['subject'];
$priority=3;
$notification = isset($structure->headers['disposition-notification-to']) ? true : false;

$udate=strtotime($structure->headers['date']);
//loop through all parts
$_SESSION['url_replacements'] = array();


$attachments=array();
$content='';

function get_parts($structure, $part_number_prefix='')
{
	global $attachments, $content, $charset, $GO_CONFIG, $path;

	if (isset($structure->parts))
	{
		$part_number=0;
		foreach ($structure->parts as $part) {

			//text part and no attachment so it must be the body
			if($structure->ctype_primary=='multipart' && $structure->ctype_secondary=='alternative' && 
				$part->ctype_primary == 'text' && $part->ctype_secondary=='plain')
				{
				
					continue;
				}
			if ($part->ctype_primary == 'text' && (!isset($part->disposition) || $part->disposition != 'attachment') && empty($part->d_parameters['filename']))
			{
				if (eregi('plain', $part->ctype_secondary))
				{
					$content_part = nl2br($part->body);
				}else
				{
					$content_part = $part->body;
				}
				if(isset($part->ctype_parameters['charset']) && $part->ctype_parameters['charset']!=$charset)
				{
					$content_part = iconv($part->ctype_parameters['charset'], $charset, $content_part);
				}
				$content .= $content_part;
			}/*elseif($part->ctype_primary == 'multipart')
			{
				foreach($part->parts as $multipart)
				{
					if(eregi('html', $multipart->ctype_secondary))
					{
						$content_part = $multipart->body;
						if(isset($multipart->ctype_parameters['charset']) && $multipart->ctype_parameters['charset']!=$charset)
						{
							$content_part = iconv($multipart->ctype_parameters['charset'], $charset, $content);
						}
						$content .= $content_part;
					}
				}
			}*/else {
				//store attachements in the attachments array
				if (!empty($part->d_parameters['filename']) && empty($part->headers['content-id']))
				{
					$mime_attachment['filesize'] = strlen($part->body);
					$mime_attachment['filename'] = $part->d_parameters['filename'];
					$mime_attachment['mime_type'] = $part->ctype_primary.'/'.$part->ctype_secondary;
					$mime_attachment['content-transfer-encoding'] = $part->headers['content-transfer-encoding'];
					$mime_attachment['part_number'] = $part_number_prefix.$part_number;
					$mime_attachment['content-disposition'] = isset($part->disposition) ? $part->disposition : '';
					$mime_attachment['content-id'] = isset($part->headers['content-id']) ? $part->headers['content-id'] : '';
					$attachments[] = $mime_attachment;

				}elseif(isset($part->headers['content-id']))
				{
					$content_id = trim($part->headers['content-id']);
					if ($content_id != '')
					{
						if (strpos($content_id,'>'))
						{
							$content_id = substr($part->headers['content-id'], 1,strlen($part->headers['content-id'])-2);
						}
						$content_id = $content_id;

						//$path = 'mimepart.php?path='.urlencode($path).'&part_number='.$part_number;
						//replace inline images identified by a content id with the url to display the part by Group-Office
						$url_replacement['id'] = $content_id;
						if(isset($_REQUEST['path']))
						{
							$url_replacement['url'] = $GO_CONFIG->control_url.'mimeviewer/mimepart.php?path='.urlencode($path).'&amp;part_number='.$part_number;
						}else 
						{
							$url_replacement['url'] = $GO_CONFIG->control_url.'mimeviewer/mimepart.php?account_id='.$account['id'].'&mailbox='.urlencode(smart_stripslashes($_REQUEST['mailbox'])).'&uid='.smart_stripslashes($_REQUEST['uid']).'&part='.$_REQUEST['part'].'&transfer='.urlencode($_REQUEST['transfer']).'&part_number='.$part_number;
						}
						$_SESSION['url_replacements'][] = $url_replacement;
					}
				}
			}
			$part_number++;
			if(isset($part->parts))
			{
				get_parts($part, $part_number_prefix.$part_number.'.');
			}
			
		}
	}elseif(isset($structure->body))
	{
		//convert text to html
		if (eregi('plain', $structure->ctype_secondary))
		{
			$text_part = nl2br($structure->body);
		}else
		{
			$text_part = $structure->body;
		}
		if(isset($structure->ctype_parameters['charset']) && $structure->ctype_parameters['charset']!=$charset)
		{
			$text_part = iconv($structure->ctype_parameters['charset'], $charset, $text_part);
		}
		$content .= $text_part;

	}
	
	
}

get_parts($structure);
//var_dump($structure);
unset($structure);

//replace inline images with the url to display the part by Group-Office
if (isset($_SESSION['url_replacements']))
{
	for ($i=0;$i<count($_SESSION['url_replacements']);$i++)
	{
		$content = str_replace('cid:'.$_SESSION['url_replacements'][$i]['id'], $_SESSION['url_replacements'][$i]['url'], $content);
	}
}





$header = "<!-- Start header added by Group-Office -->\r\n\r\n\r\n\r\n";

$header .= '<table style="margin-bottom:10px;border: 1px solid black;font-family: arial; font-size: 10px;background-color:#f1f1f1;" width="100%">';
switch ($priority)
{
	case "4":
		$header .= '<tr><td><table border="0" cellpadding="1" cellspacing="1"><tr><td><img src="'.$GO_THEME->images['info'].'" border="0" width="16" height="16" /></td><td class="Success">'.$ml_low_priority.'</td></tr></table></td></tr>';
		break;

	case "2":
		$header .= '<tr><td><table border="0" cellpadding="1" cellspacing="1"><tr><td><img src="'.$GO_THEME->images['info'].'" border="0" width="16" height="16" /></td><td class="Error">'.$ml_high_priority.'</td></tr></table></td></tr>';
		break;
}
$header .= '<tr><td><table border="0" cellpadding="1" cellspacing="0" style="font-family: arial; font-size: 12px;">'.
'<tr><td><b>'.$ml_subject.':&nbsp;</b></td>'.
'<td>'.$subject.'</td></tr><tr>'.
'<td><b>'.$ml_from.':&nbsp;</b></td>'.
'<td>'.$from.'</td>';
if(!empty($to))
{
	$header .= '</tr><tr><td valign="top"><b>'.$ml_to.':&nbsp;</b></td><td>'.$to.'</td></tr>';
}
if(!empty($cc))
{
	$header .= '</tr><tr><td valign="top"><b>CC:&nbsp;</b></td><td>'.$cc.'</td></tr>';
}
if(!empty($bcc))
{
	$header .= '</tr><tr><td valign="top"><b>BCC:&nbsp;</b></td><td>'.$bcc.'</td></tr>';
}
$header .= '<tr><td><b>'.$strDate.':&nbsp;</b></td>'.
'<td>'.date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], get_time($udate)).'</td>'.
'</tr></table></td></tr></table>';

$count = 0;
$splitter = 0;


$attachments_str = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"font-family: arial; font-size: 12px;\"><tr>";

foreach($attachments as $attachment)
{

	if ($attachment["filename"] == "")
	{
		$attachment["filename"] = $attachment["mime_type"];
		$pos = strrpos($attachment["filename"] ,'/');
		if ($pos)
		{
			$attachment["filename"] = substr($attachment["filename"],$pos+1,strlen($attachment["filename"]));
		}
	}
	$splitter++;
	$count++;
	
	
	if(isset($_REQUEST['path']))
	{
		$href = $GO_CONFIG->control_url.'mimeviewer/mimepart.php?path='.urlencode($path).'&amp;part_number='.$attachment["part_number"];
	}else 
	{
		$href = $GO_CONFIG->control_url.'mimeviewer/mimepart.php?account_id='.$account['id'].'&mailbox='.urlencode(smart_stripslashes($_REQUEST['mailbox'])).'&uid='.smart_stripslashes($_REQUEST['uid']).'&part='.$_REQUEST['part'].'&transfer='.urlencode($_REQUEST['transfer']).'&part_number='.$attachment["part_number"];
	}

	$attachments_str .= '<td><img border="0" width="16" height="16" src="'.get_filetype_image(get_extension($attachment["filename"])).'" /></td>';
	$attachments_str .= '<td valign="center" nowrap>&nbsp;<a style="color:black;" href="'.$href.'">'.cut_string($attachment["filename"],50).'</a> ('.format_size($attachment["filesize"]).')</td>';
	$attachments_str .='<td>;</td>';

	if ($splitter == 3)
	{
		$splitter = 0;
		$attachments_str .= "</tr><tr>";
	}
}

$attachments_str .= "</tr></table>";

if ($count>0)
{
	$header .= '<table style="margin-bottom:10px;border: 1px solid black;font-family: arial; font-size: 12px;background-color:#f1f1f1;" width="100%">'.
	'<tr><td valign="top"><b>'.$ml_attachments.':</b>&nbsp;&nbsp;</td><td width="100%">'.$attachments_str.'</td></tr></table>';
}

$header .= "<!-- End header added by Group-Office -->\r\n\r\n\r\n\r\n";




if($pos = strpos(strtolower($content), '<body'))
{
	$end_body_pos = strpos($content, '>', $pos);
	$first_part = substr($content, 0, $end_body_pos+1);
	$last_part = substr($content, $end_body_pos+2);
	$content = $first_part.$header.$last_part;
}else
{
	$content = $header.$content;
}


echo $content;
?>
