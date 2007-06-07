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

require_once ("../../Group-Office.php");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');



load_basic_controls();
load_control('tooltip');
load_control('overlib');
$overlib = new overlib();

require_once ($GO_CONFIG->class_path."mail/imap.class.inc");
require_once ($GO_MODULES->class_path."email.class.inc");
$mail = new imap();
$email = new email();
$GO_HEADER['nomessages'] = true;
$GO_HEADER['head'] = overlib::get_header();

require_once ($GO_LANGUAGE->get_language_file('email'));

$GO_CONFIG->set_help_url($ml_help_url);

$em_settings = $email->get_settings($GO_SECURITY->user_id);

$link_back = (isset ($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? htmlspecialchars($_REQUEST['link_back']) : $_SERVER['REQUEST_URI'];

$to = '';
$texts = '';
$images = '';

$account_id = isset ($_REQUEST['account_id']) ? $_REQUEST['account_id'] : 0;
$task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';
$mailbox = isset ($_REQUEST['mailbox']) ? smart_stripslashes($_REQUEST['mailbox']) : "INBOX";
$uid = isset ($_REQUEST['uid']) ? $_REQUEST['uid'] : 0;
$return_to = (isset ($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : null;
$link_back = (isset ($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];
$task = (isset ($_REQUEST['task']) && $_REQUEST['task'] != '') ? $_REQUEST['task'] : '';

$part = isset ($_REQUEST['part']) ? $_REQUEST['part'] : '';
$query = isset ($_REQUEST['query']) ? $_REQUEST['query'] : '';
$account = $email->get_account($account_id);

if($account['user_id']!=$GO_SECURITY->user_id)
{
	header('Location: '.$GO_CONFIG->host.'/error_docs/403.php');
	exit();
}


if ($account && $mail->open($account['host'], $account['type'], $account['port'], $account['username'], $account['password'], $mailbox, 0, $account['use_ssl'], $account['novalidate_cert'])) {
	if ($task == 'move_mail') {
		$messages = array ($uid);
		$move_to_mailbox = smart_stripslashes($_REQUEST['move_to_mailbox']);
		if ($mail->move($move_to_mailbox, $messages) && $mail->reopen($move_to_mailbox)) {
			header('Location: '.$GO_MODULES->url.'index.php?account_id='.$account_id.'&mailbox='.urlencode($mailbox));
			exit ();
		}
	}
	//sort messages for determination of previous and next message
	
	//$reverse =  ($_REQUEST['sort_ascending']!='1') ? '1' : '0';
	//$mail->sort($_REQUEST['sort_index'],$reverse, $email_search_query);

	if(!$content = $mail->get_message($uid, 'html', $part))
	{
		$GO_HEADER['body_arguments'] = 'onload="javascript:parent.update_toolbar(true);"';
		require($GO_THEME->theme_path.'header.inc');

		$h1 = new html_element('h1', $ml_get_message_error_title);
		$p = new html_element('p', $ml_get_message_error_text);

		echo $h1->get_html().$p->get_html();
		require($GO_THEME->theme_path.'footer.inc');
		exit();
	}
	$subject = !empty ($content["subject"]) ? $content["subject"] : $ml_no_subject;

} else {
	require_once ($GO_THEME->theme_path.'header.inc');
	echo '<p class="Error">'.$ml_connect_failed.' \''.$account['host'].'\' '.$ml_at_port.': '.$account['port'].'</p>';
	echo '<p class="Error">'.imap_last_error().'</p>';
	require_once ($GO_THEME->theme_path.'footer.inc');
	exit ();
}



$has_contacts_module = (isset ($GO_MODULES->modules['addressbook']) && $GO_MODULES->modules['addressbook']['read_permission']);

if ($has_contacts_module) {
	require_once ($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
	$ab = new addressbook();
}

if ($has_contacts_module) {
	if ($contact = $ab->get_contact_by_email($content['sender'], $GO_SECURITY->user_id)) {
		if ($contact['color'] != '') {
			$style = ' style="color: #'.$contact['color'].';"';
		} else {
			$style = '';
		}
		$from = '<a '.$style.' href="javascript:popup(\''.$GO_MODULES->modules['addressbook']['url'].'contact.php?contact_id='.$contact['id'].'&return_to=javascript:window.close();\');" class="normal" '.' title="'.$strShowProfile.'">'.$content['from'].'</a>';
	} else {
		$name = split_name($content['from']);
		$add_contact_link = $GO_MODULES->modules['addressbook']['url'].'contact.php?email='.$content['sender'].'&first_name='.urlencode($name['first']).'&middle_name='.urlencode($name['middle']).'&last_name='.urlencode($name['last']).'&popup=true';

		if ($em_settings['add_senders'] == '1' && $content["new"] == '1') {
			$ask_to_add = true;
		}

		$from = '<a href="javascript:popup(\''.$add_contact_link.'\');" class="normal" title="'.$strAddContact.'">'.$content['from'].'</a>';
	}
} else {
	$user  = $GO_USERS->get_user_by_email($content['sender']);
	if ($user['id'] && $GO_SECURITY->user_is_visible($user['id'])) {
		$from = '<a href="javascript:popup(\''.$GO_CONFIG->control_url.'user.php?id='.$user['id'].'&popup=true\');" title="'.$strShowProfile.'">'.$content['from'].'</a>';
	} else {
		$from = $content['from'];
	}
}
$from .= '&nbsp;&lt;'.$content['sender'].'&gt;';
//$from .= '&nbsp;<a href="javascript:document.location=\'about:blank\';parent.messages.location.href=\'messages.php?account_id='.$account_id.'&task=set_search_query&from='.$content['sender'].'&uid='.$uid.'\';" title="'.$ml_search_sender.'"><img src="'.$GO_THEME->images['magnifier'].'" border="0" align="middle" /></a>';
?>

<div style="padding:5px;background-color:#c3daf9;">
<table width="100%">

	<?php
	if($content["priority"] > 3)
	{
		echo '<tr><td class="Success" colspan="2"><img src="'.$GO_THEME->images['info'].'" border="0" width="16" height="16" align="absmiddle" />&nbsp;'.$ml_low_priority.'</td></tr>';
	}elseif($content["priority"] < 3 && $content["priority"] > 0)
	{
		echo '<tr><td class="Error" colspan="2"><img src="'.$GO_THEME->images['alert'].'" border="0" width="16" height="16" align="absmiddle" />&nbsp;'.$ml_high_priority.'</td></tr>';
	}
	?>
	<tr>
		<td><b><?php echo $ml_subject; ?>:</b></td>
		<td><?php echo $subject; ?></td>
	</tr>
	<tr>
		<td><b><?php echo $ml_from; ?>:</b></td>
		<td>

		<?php echo $from; ?>
		
		</td>
	</tr>
	<tr>
		<td valign="top"><b><?php echo $ml_to; ?>:</b></td>
		<td>
		<?php
		$to = "";
		if (isset ($content["to"])) {
			for ($i = 0; $i < sizeof($content["to"]); $i ++) {
				if ($i != 0) {
					$to .= ", ";
				}
				$to .= $content["to"][$i];
			}
		}
		if ($to == "") {
			$to = $ml_no_reciepent;
		}
		$to = htmlspecialchars($to, ENT_QUOTES);
		if (strlen($to) > 200) {
			$short_to = cut_string($to, 200);
			$long_to = $to;




			echo '<a href="#" '.$overlib->print_overlib($long_to).'>'.$short_to.'</a>';
		} else {
			echo $to;
		}
?>
		</td>
		</tr>
		<?php

		if (isset ($content["cc"])) {
			$cc = '';
			for ($i = 0; $i < sizeof($content["cc"]); $i ++) {
				if ($i != 0) {
					$cc .= ", ";
				}
				$cc .= $content["cc"][$i];
			}
			$cc = htmlspecialchars($cc, ENT_QUOTES);
			if ($cc != '') {
				echo '<tr><td valign="top"><b>Cc:</b>&nbsp;</td><td>';
				if (strlen($cc) > 200) {
					$short_cc = cut_string($cc, 200);
					$long_cc = $cc;

					echo '<a href="#" '.$overlib->print_overlib($long_cc).'>'.$short_cc.'</a>';
				} else {
					echo $cc;
				}
				echo '</td></tr>';
			}
		}

		if (isset ($content["bcc"])) {
			$bcc = '';
			for ($i = 0; $i < sizeof($content["bcc"]); $i ++) {
				if ($i != 0) {
					$bcc .= ", ";
				}
				$bcc .= $content["bcc"][$i];
			}
			$bcc = htmlspecialchars($bcc, ENT_QUOTES);
			if ($bcc != '') {
				echo '<tr><td valign="top"><b>Bcc:</b></td><td>';
				if (strlen($bcc) > 200) {
					$short_bcc = cut_string($bcc, 200);
					$long_bcc = $bcc;

					echo '<a href="#" '.$overlib->print_overlib($long_bcc).'>'.$short_bcc.'</a>';
				} else {
					echo $bcc;
				}
				echo '</td></tr>';
			}
		}
?>
		<tr>
			<td><b><?php echo $strDate; ?>:</b></td>
			<td><?php echo date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], get_time($content['udate'])); ?></td>
		</tr>
		<tr>
			<td><b><?php echo $ml_size; ?>:</b></td>
			<td><?php echo format_size($content['size']); ?></td>
		</tr>	
		</table>

	</td>
</tr>
<tr>
	<td>
	<?php

	$count = 0;
	$splitter = 0;
	$parts = array_reverse($mail->f("parts"));

	$attachments = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr>";

	$cal_module = $GO_MODULES->get_module('calendar');
	if (!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $cal_module['acl_read']) && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $cal_module['acl_write'])) {
		$cal_module = false;
	}

	$ab_module = $GO_MODULES->get_module('addressbook');
	if (!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $ab_module['acl_read']) && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $ab_module['acl_write'])) {
		$ab_module = false;
	}

	$splitter = 0;

	for ($i = 0; $i < count($parts); $i ++) {
		if (eregi("ATTACHMENT", $parts[$i]["disposition"])  || (eregi("INLINE", $parts[$i]["disposition"]) && $parts[$i]["name"] != '') || $parts[$i]["name"] != ''){
			$extension = get_extension($parts[$i]["name"]);
			if ($extension == 'ics' && $cal_module) {
				$target = '_self';
				$link = "javascript:popup('import_ics.php?account_id=".$account['id']."&mailbox=".urlencode($mailbox)."&uid=".$uid."&part=".$parts[$i]["number"]."&transfer=".$parts[$i]["transfer"]."&mime=".$parts[$i]["mime"]."&filename=".urlencode($parts[$i]["name"])."', '400','80');";
				if ($content["new"] == 1) {
					echo '<script type="text/javascript">'.$link.'</script>';
				}

			}
			elseif ($extension == 'vcf' && $ab_module) {
				$target = '_self';
				$link = "javascript:popup('import_vcf.php?account_id=".$account['id']."&mailbox=".urlencode($mailbox)."&uid=".$uid."&part=".$parts[$i]["number"]."&transfer=".$parts[$i]["transfer"]."&mime=".$parts[$i]["mime"]."&filename=".urlencode($parts[$i]["name"])."', '400','80');";
				if ($content["new"] == 1) {
					echo '<script type="text/javascript">'.$link.'</script>';
				}
			}elseif($extension == 'dat')
			{
				$target = '_self';
				$link = "tnef.php?account_id=".$account['id']."&mailbox=".urlencode($mailbox)."&uid=".$uid."&part=".$parts[$i]["number"]."&transfer=".$parts[$i]["transfer"]."&mime=".$parts[$i]["mime"]."&filename=".urlencode($parts[$i]["name"]);
			} else {
				$target = '_top';
				$link = "attachment.php?account_id=".$account['id']."&mailbox=".urlencode($mailbox)."&uid=".$uid."&part=".$parts[$i]["number"]."&transfer=".$parts[$i]["transfer"]."&mime=".$parts[$i]["mime"]."&filename=".urlencode($parts[$i]["name"]);
			}

			$splitter++;
			$count++;

			$attachments .= '<div style="display:inline;white-space:nowrap;margin-right:3px;">'.
			'<a href="'.$link.'" target="'.$target.'" title="'.$parts[$i]["name"].'"><img style="border:0px; margin-right:3px;" width="16" height="16" src="'.get_filetype_image(get_extension($parts[$i]["name"])).'" align="absmiddle" />'.
			cut_string($parts[$i]["name"], 50).'</a> ('.format_size($parts[$i]["size"]).')';
			if (isset($GO_MODULES->modules['filesystem']) && $GO_MODULES->modules['filesystem']['read_permission']) {
				$attachments .= "&nbsp;<a title=\"".$ml_save_attachment."\" href=\"javascript:popup('save_attachment.php?account_id=".$account['id']."&mailbox=".urlencode($mailbox)."&uid=".$uid."&part=".$parts[$i]["number"]."&transfer=".$parts[$i]["transfer"]."&mime=".$parts[$i]["mime"]."&filename=".urlencode(addslashes($parts[$i]["name"]))."','800','500')\"><img src=\"".$GO_THEME->images['save']."\" border=\"0\" align=\"absmiddle\" /></a>\n";
			}
			$attachments .= ';</div>';

			//couldn't get wrapping to work in IE :(
			if($splitter == 2)
			{
				$splitter=0;
				$attachments .= '<br />';
			}

		}
	}
	if($count > 1 && is_executable($GO_CONFIG->cmd_zip))
	{
		$attachments .= '<div style="display:inline;white-space:nowrap;"><img border="0" width="16" height="16" src="'.get_filetype_image('zip').'" align="absmiddle" />&nbsp;'.
		'<a href="zip_attachments.php?uid='.$uid.'&account_id='.$account['id'].'&mailbox='.$mailbox.'">'.$ml_download_zipped_attachments.'</a></div>';
	}

	$attachments .= "</tr></table>";

	if ($count > 0) {
		echo '<table class="HeaderTable" width="100%" style="margin-top:2px;">'.'<tr><td valign="top"><b>'.$ml_attachments.':</b>&nbsp;&nbsp;</td><td width="100%">'.$attachments.'</td></tr></table>';
	}
	
	echo '<div style="background-color:white;padding:3px;border:1px solid black">';

	$count = 0;
	$splitter = 0;
	$parts = array_reverse($mail->f("parts"));

	//get all text and html content
	for ($i=0;$i<sizeof($parts);$i++)
	//for ($i=0;$i<1;$i++)
	{
		$mime = strtolower($parts[$i]["mime"]);

		//if (($mime == "text/html") || ($mime == "text/plain") || ($mime == "text/enriched"))
		if ($parts[$i]["name"] == '' && ($mime == "text/html" || $mime == "text/plain" || $mime == "text/enriched" || $mime == "unknown/unknown"))
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
					$part = convert_links($part);
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

	echo $texts;//.$images; Don't show iamges automatically.

	if ($content["notification"] != '' && $content["new"] == 1) {
		echo "<script type=\"text/javascript\">\npopup('"."notification.php?notification=".urlencode($content["notification"])."&date=".urlencode(date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], $content['udate']))."&subject=".urlencode($subject)."&to=".urlencode($to)."','500','150');\n</script>\n";
	}

	$mail->close();

	echo '</div></div>';

	if (isset ($ask_to_add)) {
?>
	<script type="text/javascript">
	if(confirm('<?php echo addslashes(sprintf($ml_ask_add_sender, $content['sender'], $content['from'])); ?>'))
	{
		popup('<?php echo $add_contact_link; ?>', '750','550');
	}
	</script>
	<?php

	}

