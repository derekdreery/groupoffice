<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

require_once("../../Group-Office.php");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');

require_once($GO_CONFIG->class_path."mail/imap.class.inc");
require_once($GO_MODULES->class_path."email.class.inc");
$mail = new imap();
$email = new email();

$texts = '';
$images = '';

$print = isset($_REQUEST['print']) ? true : false;

$mailbox = isset($_REQUEST['mailbox'])?  $_REQUEST['mailbox'] : "INBOX";
$uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : 0;
$part = isset($_REQUEST['part']) ? $_REQUEST['part'] : '';
$account_id = isset($_REQUEST['account_id']) ? $_REQUEST['account_id'] : 0;
$account = $email->get_account($account_id);

if ($account && $mail->open($account['host'], $account['type'], $account['port'],$account['username'], $account['password'],$mailbox, 0, $account['use_ssl'], $account['novalidate_cert']))
{
	$content = $mail->get_message($uid, 'html', $part);

	if($print)
	{
		require_once($GO_LANGUAGE->get_language_file('email'));

		$subject = isset($content["subject"]) ? $content["subject"] : $ml_no_subject;

		$header = "<!-- Start header added by Group-Office -->\r\n\r\n\r\n\r\n";
		
		$header .= '<table style="margin-bottom:10px;border: 1px solid black;font-family: arial; font-size: 10px;background-color:#f1f1f1;" width="100%">';
		switch ($content["priority"])
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
		'<td>'.$content['from'].'&nbsp;&lt;'.$content['sender'].'&gt;</td>'.
		'</tr><tr><td valign="top"><b>'.$ml_to.':&nbsp;</b></td><td>';

		$to = "";
		if (isset($content["to"]))
		{
			for ($i=0;$i<sizeof($content["to"]);$i++)
			{
				if ($i != 0)
				{
					$to .=", ";
				}
				$to .= $content["to"][$i];
			}
		}
		if ($to == "")
		{
			$to = $ml_no_reciepent;
		}
		$to = htmlspecialchars($to,ENT_QUOTES);
		$header .= $to.'</td></tr>';

		if (isset($content["cc"]) && count($content['cc']))
		{
			$cc = '';
			for ($i=0;$i<sizeof($content["cc"]);$i++)
			{
				if ($i != 0)
				{
					$cc .=", ";
				}
				$cc .= $content["cc"][$i];
			}
			$cc = htmlspecialchars($cc,ENT_QUOTES);

			$header .= '<tr><td valign="top"><b>CC:&nbsp;</b></td><td>'.$cc.'</td></tr>';
		}


		if (isset($content["bcc"]) && count($content['cc']))
		{
			$bcc = '';
			for ($i=0;$i<sizeof($content["bcc"]);$i++)
			{
				if ($i != 0)
				{
					$bcc .=", ";
				}
				$bcc .= $content["bcc"][$i];
			}
			$bcc = htmlspecialchars($bcc,ENT_QUOTES);

			$header .= '<tr><td valign="top"><b>BCC:</b>&nbsp;</td><td>'.$bcc.'</td></tr>';
		}
		$header .=	'<tr><td><b>'.$strDate.':&nbsp;</b></td>'.
		'<td>'.date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], get_time($content['udate'])).'</td>'.
		'</tr></table></td></tr></table>';

		$count = 0;
		$splitter = 0;
		$parts = array_reverse($mail->f("parts"));

		$attachments = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"font-family: arial; font-size: 12px;\"><tr>";

		for ($i=0;$i<count($parts);$i++)
		{
			if ((eregi("ATTACHMENT", $parts[$i]["disposition"]) && $parts[$i]["name"] != '') ||
			(eregi("INLINE", $parts[$i]["disposition"]) && $parts[$i]["name"] != '') ||
			eregi("message/rfc822", $parts[$i]["mime"]) ||
			(eregi("APPLICATION", $parts[$i]["mime"]) && $parts[$i]["name"] != ''))
			{

				if ($parts[$i]["name"] == "")
				{
					$parts[$i]["name"] = $parts[$i]["mime"];
					$pos = strrpos($parts[$i]["name"] ,'/');
					if ($pos)
					{
						$parts[$i]["name"] = substr($parts[$i]["name"],$pos+1,strlen($parts[$i]["name"]));
					}
				}
				$splitter++;
				$count++;

				$attachments .= '<td><img border="0" width="16" height="16" src="'.get_filetype_image(get_extension($parts[$i]["name"])).'" /></td>';
				$attachments .= '<td valign="center" nowrap>&nbsp;'.cut_string($parts[$i]["name"],50).' ('.format_size($parts[$i]["size"]).')</td>';
				$attachments .='<td>;</td>';
			}
			if ($splitter == 3)
			{
				$splitter = 0;
				$attachments .= "</tr><tr>";
			}
		}

		$attachments .= "</tr></table>";

		if ($count>0)
		{
			$header .= '<table style="margin-bottom:10px;border: 1px solid black;font-family: arial; font-size: 12px;background-color:#f1f1f1;" width="100%">'.
			'<tr><td valign="top"><b>'.$ml_attachments.':</b>&nbsp;&nbsp;</td><td width="100%">'.$attachments.'</td></tr></table>';
		}
		
		$header .= "<!-- End header added by Group-Office -->\r\n\r\n\r\n\r\n";
	}


}else
{
	require_once($GO_THEME->theme_path.'header.inc');
	echo '<table border="0" cellpadding="10" width="100%"><tr><td>';
	echo '<p class="Error">'.$ml_connect_failed.' \''.$account['host'].'\' '.$ml_at_port.': '.$account['port'].'</p>';
	echo '<p class="Error">'.imap_last_error().'</p>';
	require_once($GO_THEME->theme_path.'footer.inc');
	exit();
}


$count = 0;
$splitter = 0;
$parts = array_reverse($mail->f("parts"));

//get all text and html content
for ($i=0;$i<sizeof($parts);$i++)
{
	$mime = strtolower($parts[$i]["mime"]);

	//if (($mime == "text/html") || ($mime == "text/plain") || ($mime == "text/enriched"))
	if (($mime == "text/html") || ($mime == "text/plain") || ($mime == "text/enriched") || $mime == "unknown/unknown")
	{
		//$mail_charset = $parts[$i]['charset'];

		$part = $mail->view_part($uid, $parts[$i]["number"], $parts[$i]["transfer"], $parts[$i]["charset"]);

		switch($mime)
		{
			case 'unknown/unknown':
			case 'text/plain':
			$part = text_to_html($part);
			break;

			case 'text/html':
			$part = convert_html($part);
			break;

			case 'text/enriched':
			$part = enriched_to_html($part);
			break;
		}


		if ($parts[$i]["name"] != '')
		{
			$texts .= "<p class=\"normal\" align=\"center\">--- ".$parts[$i]["name"]." ---</p>";
		}elseif($texts != '')
		{
			$texts .= '<br /><br /><br />';
		}

		$texts .= $part;
	}
}

//Content-ID's that need to be replaced with urls when message needs to be reproduced
$replace_url = array();
$replace_id = array();
//preview all images

for ($i=0;$i<sizeof($parts);$i++)
{
	if (eregi("image",$parts[$i]["mime"]))
	{
		//when an image has an id it belongs somewhere in the text we gathered above so replace the
		//source id with the correct link to display the image.
		if ($parts[$i]["id"] != '')
		{
			$tmp_id = $parts[$i]["id"];
			if (strpos($tmp_id,'>'))
			{
				$tmp_id = substr($parts[$i]["id"], 1,strlen($parts[$i]["id"])-2);
			}
			$id = "cid:".$tmp_id;
			$url = "attachment.php?account_id=".$account['id']."&mailbox=".urlencode($mailbox)."&amp;uid=".$uid."&amp;part=".$parts[$i]["number"]."&amp;transfer=".$parts[$i]["transfer"]."&amp;mime=".$parts[$i]["mime"]."&amp;filename=".urlencode($parts[$i]["name"]);
			$texts = str_replace($id, $url, $texts);
		}else
		{
			$images .= "<br /><p class=\"normal\" align=\"center\">--- ".$parts[$i]["name"]." ---</p><div align=\"center\"><img src=\"attachment.php?account_id=".$account['id']."&mailbox=".urlencode($mailbox)."&uid=".$uid."&part=".$parts[$i]["number"]."&transfer=".$parts[$i]["transfer"]."&mime=".$parts[$i]["mime"]."&filename=".urlencode($parts[$i]["name"])."\" border=\"0\" /></div>";
		}
	}
}
header('Content-Type: text/html; charset='.$charset);

if($print)
{
	if($pos = strpos(strtolower($texts), '<body'))
	{
		$end_body_pos = strpos($texts, '>', $pos);
		$first_part = substr($texts, 0, $end_body_pos);
		$last_part = substr($texts, $end_body_pos+1);
		$texts = $first_part.$header.$last_part;
	}else
	{
		$texts = $header.$texts;
	}
}

echo $texts.$images;

if ($print)
{
	echo "\n<script type=\"text/javascript\">\nwindow.print();\n</script>\n";
}
