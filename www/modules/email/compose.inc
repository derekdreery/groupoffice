<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.139 $ $Date: 2006/11/27 16:22:41 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

//replace signatures when user changed his mail account
$old_signature = isset ($_REQUEST['old_signature']) ? $_REQUEST['old_signature'] : '';
if ($sendaction == 'change_mail_from' && $old_signature != '') {
	$mail_body = str_replace($old_signature, $signature, $mail_body);
}

//check for the templates plugin
if ($sendaction == 'load_template') {
	require_once ($GO_CONFIG->class_path.'mail/mimeDecode.class.inc');

	$template_body = '';

	if ($template_id > 0) {
		//if contact_id is not set but email is check if there's contact info available
		if ($mail_to != '' && $contact_id == 0) {
			if ($contact = $ab->get_contact_by_email($mail_to, $GO_SECURITY->user_id)) {
				$contact_id = $contact['id'];
			}
		}

		$template = $tp->get_template($template_id);

		//get the raw mime message and decode it.

		$part_number = 0;
		$url_replacements = array ();
		$attachments = array ();

		$params['include_bodies'] = true;
		$params['decode_bodies'] = true;
		$params['decode_headers'] = true;
		$params['input'] = $template['content'];

		$structure = Mail_mimeDecode :: decode($params);
		$notification_check = isset ($structure->headers['disposition-notification-to']) ? true : false;

		$_SESSION['url_replacements'] = array ();

		//loop through all parts
		if (isset ($structure->parts)) {
			foreach ($structure->parts as $part) {
				$disposition = isset ($part->disposition) ? $part->disposition : '';

				//text part and no attachment so it must be the body
				if ($part->ctype_primary == 'text' && $disposition != 'attachment') {
					//convert text to html
					if (eregi('plain', $part->ctype_primary)) {
						$text_part = nl2br($part->body);
					} else {
						$text_part = $part->body;
					}
					$template_body .= $text_part;
				} else {
					//save attachments to a temporarily file
					$tmp_file = $GO_CONFIG->tmpdir.md5(uniqid(time()));

					$fp = fopen($tmp_file, "w");
					fwrite($fp, $part->body);
					fclose($fp);

					$filename = isset ($part->d_parameters['filename']) ? $part->d_parameters['filename'] : '';

					$content_id = isset ($part->headers['content-id']) ? trim($part->headers['content-id']) : '';
					if ($content_id != '') {
						if (strpos($content_id, '>')) {
							$content_id = substr($part->headers['content-id'], 1, strlen($part->headers['content-id']) - 2);
						}

						//replace inline images identified by a content id with the url to display the part by Group-Office
						$url_replacement['id'] = $content_id;
						$url_replacement['url'] = $ab_module['url'].'templates/mime_part.php?template_id='.$template_id.'&amp;part_number='.$part_number;
						$_SESSION['url_replacements'][] = $url_replacement;
					}
					$email->register_attachment($tmp_file, $filename, strlen($part->body), $part->ctype_primary.'/'.$part->ctype_secondary, $disposition, $content_id);
				}
				$part_number ++;
			}
		}
		elseif (isset ($structure->body)) {
			//convert text to html
			if (eregi('plain', $structure->ctype_primary)) {
				$text_part = nl2br($structure->body);
			} else {
				$text_part = $structure->body;
			}
			$template_body .= $text_part;

		}
		unset ($structure);
		//replace inline images with the url to display the part by Group-Office
		if (isset ($_SESSION['url_replacements'])) {
			for ($i = 0; $i < count($_SESSION['url_replacements']); $i ++) {
				$template_body = str_replace('cid:'.$_SESSION['url_replacements'][$i]['id'], $_SESSION['url_replacements'][$i]['url'], $template_body);
			}
		}

		//get the addressbook language file
		require_once ($GO_LANGUAGE->get_language_file('addressbook'));
		if ($report_id == 0) {
			if ($company_id > 0) {
				$template_body = $tp->replace_company_data_fields($template_body, $company_id);
			} else {
				$template_body = $tp->replace_data_fields($template_body, $contact_id);
			}
		}

		if ($pos = strpos($mail_body, '<body')) {
			$end_body_pos = strpos($mail_body, '>', $pos);
			$first_part = substr($mail_body, 0, $end_body_pos);
			$last_part = substr($mail_body, $end_body_pos +1);
			$mail_body = $first_part.$template_body.$last_part;
		} else {
			$mail_body = $template_body.$mail_body;
		}
	}
}
?>
<form name="sendform" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="sendaction" value="" />
<input type="hidden" name="show_cc" value="<?php echo $show_cc; ?>" />
<input type="hidden" name="show_bcc" value="<?php echo $show_bcc; ?>" />
<?php
if ($uid > 0) {
	echo '<input type="hidden" name="uid" value="'.$uid.'" />';
	echo '<input type="hidden" name="mailbox" value="'.$_REQUEST['mailbox'].'" />';
	echo '<input type="hidden" name="action" value="'.$_REQUEST['action'].'" />';

}
echo '<input type="hidden" name="old_signature" value="'.htmlspecialchars($signature).'" />';
?>
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $GO_CONFIG->max_attachment_size; ?>" />
<table cellspacing="2" cellpadding="1" style="border:0;width:100%;height:97%;">
<?php
if (isset ($feedback))
echo '<tr><td colspan="99">'.$feedback.'</td></tr>';

if ($count > 1) {
	echo '<tr><td>'.$ml_from.':</td><td colspan="2">';
	$dropbox = new dropbox();
	$dropbox->add_arrays($ids, $names);
	$dropbox->print_dropbox('mail_from', $mail_from, 'onchange="javascript:change_mail_from()"');
	echo '</td></tr>';
}
elseif ($count > 0) {
	echo '<input type="hidden" name="mail_from" value="'.$ids[0].'" />';
}
if($report_id > 0)
{
	require($GO_MODULES->modules['reports']['class_path'].'reports.class.inc');
	$reports = new reports();
}

if ($report_id > 0 && $report = $reports->get_report($report_id)) {
	
	require($GO_LANGUAGE->get_language_file('reports'));
	
	echo '<input type="hidden" name="report_id" value="'.$report_id.'" />';
	echo '<tr><td>'.$ml_to.': </td><td>'.$re_report.': '.$report['name'].'</td></tr>';
} else {
?>
    <tr>
    <td valign="top">
    <?php

    if ($ab_module) {
    	echo '<a class="normal" href="'.$ab->select_contacts('document.sendform.mail_to', $GO_CONFIG->control_url.'select/add.php').'">';

    	echo '<img src="'.$GO_THEME->images['search_addressbook'].'" width="16" height="16" border="0" align="absmiddle" '.'style="padding-right: 3px;" />'.$ml_to.'</a> :&nbsp;';
    } else {
    	echo $ml_to.':&nbsp;';
    }

    if ($show_cc == 'false' || $show_bcc == 'false') {
    	echo '<span id="show_buttons">(';

    	if ($show_cc == 'false') {
    		echo '<a class="normal" id="cc_opener" href="javascript:show_cc();">CC</a>';
    	}
    	if ($show_cc == 'false' && $show_bcc == 'false') {
    		echo '<span id="sep">&nbsp;|&nbsp;</span>';
    	}
    	if ($show_bcc == 'false') {
    		echo '<a class="normal" id="bcc_opener" href="javascript:show_bcc();">BCC</a>';
    	}
    	echo ')</span>';
    }
?>
      </td>
      <td colspan="2">
      
      <?php 
      /*<textarea onfocus="document.onkeypress = kH;" onkeydown="actb_checkkey(event);" onkeyup="actb_tocomplete(this,event,autocomplete_contacts);" onblur="actb_removedisp(this);document.onkeypress='';" class="textbox" type="text" name="mail_to" style="width:100%;height:46px;"><?php echo (isset($mail_to) ? htmlspecialchars($mail_to) : ''); ?></textarea>
      */

      if(isset($GO_MODULES->modules['addressbook']) && $GO_MODULES->modules['addressbook']['read_permission'])
      {
      	require($GO_MODULES->modules['addressbook']['class_path'].'email_autocomplete.class.inc');

      	$autocomplete = new email_autocomplete(
      	'mail_to',
      	'mail_to',
      	$mail_to,
      	'sendform'
      	);

      	$autocomplete->set_attribute('style','width:100%;height:46px;z-index:9001');
      	echo $autocomplete->get_html();
      }else {
      	$textarea=new textarea('mail_to', $mail_to);
      	$textarea->set_attribute('style','width:100%;height:46px;z-index:9001');
      	echo $textarea->get_html();
      }
      ?>
      </td>
      </tr>
      <tr <?php if($show_cc == 'false') echo 'style="display:none;"'; ?> id="cc_row">
      <td valign="top">
      <?php

      if ($ab_module) {
      	echo '<a class="normal" href="'.$ab->select_contacts('document.sendform.mail_cc', $GO_CONFIG->control_url.'select/add.php').'">';

      	echo '<img src="'.$GO_THEME->images['search_addressbook'].'" width="16" height="16" border="0" align="absmiddle" '.'style="padding-right: 3px;" />CC</a> :';
      } else {
      	echo 'CC :';
      }
?>
	</td>
	<td colspan="2">	
	<?php
	if(isset($GO_MODULES->modules['addressbook']) && $GO_MODULES->modules['addressbook']['read_permission'])
	{
		$autocomplete = new email_autocomplete(
		'mail_cc',
		'mail_cc',
		$mail_cc,
		'sendform'
		);

		$autocomplete->set_attribute('style','width:100%;height:46px;z-index:9000');
		echo $autocomplete->get_html();
	}else {
		$textarea=new textarea('mail_to', $mail_to);
		$textarea->set_attribute('style','width:100%;height:46px;z-index:9001');
		echo $textarea->get_html();
	}

  ?>
	</td>
	</tr>
	<tr <?php if($show_bcc == 'false') echo 'style="display:none;"'; ?> id="bcc_row">
	<td valign="top">
	<?php

	if ($ab_module) {
		echo '<a class="normal" href="'.$ab->select_contacts('document.sendform.mail_bcc', $GO_CONFIG->control_url.'select/add.php').'">';

		echo '<img src="'.$GO_THEME->images['search_addressbook'].'" width="16" height="16" border="0" align="absmiddle" '.'style="padding-right: 3px;" />BCC</a> :';
	} else {
		echo 'BCC :';
	}
?>
	  </td>
	  <td colspan="2">
	  <?php


	  if(isset($GO_MODULES->modules['addressbook']) && $GO_MODULES->modules['addressbook']['read_permission'])
	  {
	  	$autocomplete = new email_autocomplete(
	  	'mail_bcc',
	  	'mail_bcc',
	  	$mail_bcc,
	  	'sendform'
	  	);

	  	$autocomplete->set_attribute('style','width:100%;height:46px;z-index:9000');
	  	echo $autocomplete->get_html();
	  }else {
	  	$textarea=new textarea('mail_to', $mail_to);
	  	$textarea->set_attribute('style','width:100%;height:46px;z-index:9001');
	  	echo $textarea->get_html();
	  }
	  ?>
	  </tr>
	  
	  	
	  <?php

} //endif report_id
?>
<tr>
<td ><?php echo $ml_subject ?> : </td>
<td colspan="2"><input class="textbox" type="text" name="mail_subject" style="width:100%" value="<?php echo (isset($mail_subject) ? htmlspecialchars($mail_subject) : '') ?>" /></td>
</tr>
<tr>
	<td valign="top"><?php echo $ml_priority ?> : </td>
	<td valign="top">
	<?php

	$priority = isset ($_POST['priority']) ? $_POST['priority'] : '3';

	$dropbox = new dropbox();
	$dropbox->add_value('1', $ml_high);
	$dropbox->add_value('3', $ml_normal);
	$dropbox->add_value('5', $ml_low);
	$dropbox->print_dropbox('priority', $priority);
	echo '</td><td align="right">';
	$checkbox = new checkbox('notification','notification', 'true', $ml_notification, $notification_check);
	echo $checkbox->get_html();
?>
	</td>
</tr>
<tr>
	<td>
	<?php

	if ($wysiwyg) {
		echo $ml_format.':&nbsp;</td><td>';
		$dropbox = new dropbox();
		$dropbox->add_value('text/PLAIN', $ml_text_format);
		$dropbox->add_value('text/HTML', $ml_html_format);
		$dropbox->print_dropbox('content_type', $content_type, 'onchange="javascript:change_format()"');
	} else {
		echo '</td><td><input type="hidden" value="text/PLAIN" name="content_type" />';
	}
?>
	</td>
	<td align="right">
	<span id="attachments_text"><?php
	$count = 0;
	$tooltip = '';
	if(isset($_SESSION['attach_array']))
	{
		for ($i = 0; $i < count($_SESSION['attach_array']); $i ++) {
			if ($_SESSION['attach_array'][$i]['disposition'] == 'attachment') {
				$extension = get_extension($_SESSION['attach_array'][$i]['file_name']);
				$tooltip .= '<div><img border="0" width="16" height="16" src="'.get_filetype_image($extension).'" align="middle" />'.
				'&nbsp;'.$_SESSION['attach_array'][$i]['file_name'].'</div>';

				$count ++;
			}
		}
	}

	if($tooltip != '')
	{
		load_control('overlib');
		$overlib=new overlib();
		echo '<a href="javascript:open_attachments();" '.$overlib->print_overlib(htmlspecialchars($tooltip), $ml_attachments).'>';
	}
	if ($count == 1) {
		echo '1 '.$ml_attachment_added;
	} else {
		echo $count.' '.$ml_attachments_added;
	}
	if($tooltip != '')
	{
		echo '</a>';
	}
?></span>
	<?php

	$button = new button($ml_attachments, "javascript:open_attachments();");
	echo $button->get_html();
?>
	</td>
</tr>
<tr>
	<td colspan="3" height="100%" width="100%">
	<?php
	if($content_type == 'text/HTML')
	{
		$htmleditor->Value		= $mail_body;
		$htmleditor->SetConfig('CustomConfigurationsPath', $GO_MODULES->modules['email']['url'].'fckconfig.js');
		$htmleditor->ToolbarSet='email';
		$htmleditor->SetConfig('ImageBrowser',true);
		$htmleditor->SetConfig('ImageBrowserURL', $GO_MODULES->modules['email']['url'].'select_image.php');
		echo $htmleditor->CreateHtml() ;
	}else
	{
		$textarea = new textarea('mail_body', $mail_body);
		$textarea->set_attribute('style','width:100%;height:100%;');
		echo $textarea->get_html();
	}
	?>
	</td>
</tr>
	<td colspan="3" style="text-align:center">
	<?php
	$button = new button($ml_send, "javascript:send()");
	echo $button->get_html();
	if (isset ($mail_from) && $account = $email->get_account($mail_from)) {
		if ($account['drafts'] != '') {
			$button = new button($cmdSave, "javascript:save_draft()");
			echo $button->get_html();
		}
	}
	$button = new button($cmdCancel, "javascript:window.close()");
	echo $button->get_html();
		?>
	</td>
</tr>
</table>
</form>
<?php
if ($ab_module) {
	echo $ab->enable_contact_selector();
}
?>
<script type="text/javascript" language="javascript">
var attach_popup;

function show_cc()
{

	var cc_row = get_object('cc_row');

	cc_row.style.display='';

	document.sendform.show_cc.value='true';

	var cc_opener = get_object('cc_opener');
	cc_opener.style.display='none';
	var sep;
	if(sep = get_object('sep'))
	{
		sep.style.display='none';
	}


	if(document.sendform.show_bcc.value=='true')
	{
		var show_buttons = get_object('show_buttons');
		show_buttons.style.display='none';
	}
}

function show_bcc()
{
	var bcc_row = get_object('bcc_row');
	bcc_row.style.display='';

	var bcc_opener = get_object('bcc_opener');
	bcc_opener.style.display='none';

	document.sendform.show_bcc.value='true';

	var sep;
	if(sep = document.getElementById('sep'))
	{
		sep.style.display='none';
	}
	if(document.sendform.show_cc.value=='true')
	{
		var show_buttons = get_object('show_buttons');
		show_buttons.style.display='none';
	}
}

function validate()
{
	if (window.RegExp)
	{
		var reg = new RegExp("[0-9A-Za-z]+","g");
		if (!reg.test(document.sendform.mail_to.value) && !reg.test(document.sendform.mail_cc.value) && !reg.test(document.sendform.mail_bcc.value))
		{
			alert("<?php echo $ml_to_empty ?>");
			document.sendform.mail_to.focus();
			return (false);
		}
	}
	return (true);
}

function save_draft()
{
	<?php

	if (isset ($uid) && $uid > 0) {
		echo 'document.sendform.uid.value="'.$uid.'";';
	}

	?>
	document.sendform.sendaction.value='save_draft';
	document.sendform.submit();

}

function send()
{
	if (document.sendform.sendaction.value != 'send')
	{


		if(attach_popup && !attach_popup.closed)
		{
			if(attach_popup.document.forms[0].task.value == 'viewing')
			{
				attach_popup.close();
			}else
			{
				alert('<?php echo htmlspecialchars(addslashes($ml_please_close_attachments)); ?>');
				attach_popup.focus();
				return false;
			}

		}
		<?php
		if ($report_id == 0) {
			echo 'if (validate()){';
		} else {
			echo "document.sendform.action = '".$GO_MODULES->modules['reports']['url']."emailing.php';";
		}
		?>
		if(document.sendform.mail_subject.value!='' || confirm("<?php echo addslashes(htmlspecialchars($ml_confirm_no_subject)); ?>"))
		{
			document.sendform.sendaction.value = "send";
			<?php
			if (isset ($uid) && $uid > 0) {
				echo 'document.sendform.uid.value="'.$uid.'";';
			}
			?>
			document.sendform.submit();
		}
		<?php if ($report_id == 0) echo '}'; ?>
	}

}

function attach()
{
	if (document.sendform.mail_att.value != "")
	{
		document.sendform.sendaction.value = "add";
		document.sendform.submit();
	}
}

function change_format()
{
	if (confirm('<?php echo htmlspecialchars(addslashes($ml_loose_changes)); ?>'))
	{
		<?php
		if (isset ($uid) && $uid > 0) {
			echo 'document.sendform.uid.value="'.$uid.'";';
		}
		?>
		document.sendform.mail_body.value='';
		document.sendform.sendaction.value = 'change_format';
		document.sendform.submit();
	}
}

function change_mail_from()
{
	document.sendform.sendaction.value = 'change_mail_from';
	document.sendform.submit();
}

function delete_attach()
{
	document.sendform.sendaction.value = "delete";
	document.sendform.submit();
}


function open_attachments()
{
	var centered;
	var width = 640;
	var height = 400;

	x = (screen.availWidth - width) / 2;
	y = (screen.availHeight - height) / 2;

	centered =',width=' + width + ',height=' + height + ',left=' + x + ',top=' + y + ',scrollbars=yes,resizable=yes,status=yes';

	attach_popup = window.open('attachments.php', 'attachments', centered);
	if (!attach_popup.opener) attach_popup.opener = self;
	attach_popup.focus();
}


function smart_focus()
{
	if(document.sendform.mail_to.value!='')
	{
		var oEditor = FCKeditorAPI.GetInstance('mail_body') ;

		//try
		//{
		//oEditor.MakeEditable();
		//}
		//catch (e) {}
		oEditor.Focus();
	}else
	{
		<?php 
		if($report_id > 0)
		{
			echo 'document.sendform.mail_to.focus();';
		}
		?>
	}
}
function close_attachments()
{
	if(eval(attach_popup))
	{
		attach_popup.close();
	}
}
</script>
