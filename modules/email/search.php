<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1615 $ $Date: 2008-04-25 16:18:36 +0200 (vr, 25 apr 2008) $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
require_once("../../Group-Office.php");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');

load_basic_controls();
load_control('datatable');
load_control('date_picker');
load_control('tabtable');
load_control('dropbox');
load_control('radio_list');

require_once($GO_CONFIG->class_path."mail/imap.class.inc");
require_once($GO_MODULES->class_path."email.class.inc.php");
require_once($GO_LANGUAGE->get_language_file('email'));
$mail = new imap();
$email = new email();

$em_settings = $email->get_settings($GO_SECURITY->user_id);

$account_id = isset($_REQUEST['account_id']) ? $_REQUEST['account_id'] : 0;
$mailbox = isset($_REQUEST['mailbox'])?  ($_REQUEST['mailbox']) : 'INBOX';

if (!$account = $email->get_account($account_id))
{
  $account = $email->get_account(0);
}

if ($account && $account["user_id"] != $GO_SECURITY->user_id)
{
  header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
  exit();
}

$subject = isset($_REQUEST['subject']) ?(trim($_REQUEST['subject'])) : '';
$from = isset($_REQUEST['from']) ? (trim($_REQUEST['from'])) : '';
$to = isset($_REQUEST['to']) ? (trim($_REQUEST['to'])) : '';
$cc = isset($_REQUEST['cc']) ?  (trim($_REQUEST['cc'])) : '';
$body = isset($_REQUEST['body']) ? (trim($_REQUEST['body'])) : '';
$before = isset($_REQUEST['before']) ? (trim($_REQUEST['before'])) : '';
$since = isset($_REQUEST['since']) ? (trim($_REQUEST['since'])) : '';
$before = isset($_REQUEST['before']) ? $_REQUEST['before'] : '';	
$since = isset($_REQUEST['since']) ? $_REQUEST['since'] : '';		
$flagged = isset($_REQUEST['flagged']) ? $_REQUEST['flagged'] : '';	
$answered = isset($_REQUEST['answered']) ? $_REQUEST['answered'] : '';
$seen = isset($_REQUEST['seen']) ? $_REQUEST['seen'] : '';		
$return_to = 'messages.php';


$GO_HEADER['head'] = '<script type="text/javascript" src="'.$GO_MODULES->url.'email.js"></script>';
$GO_HEADER['head'] .= date_picker::get_header();

$GO_HEADER['body_arguments'] = 'onload="document.forms[0].subject.focus();" onkeypress="javascript:executeOnEnter(event, \'window.document.forms[0].submit();\');"';
require_once($GO_THEME->theme_path."header.inc");

echo '<form method="post" action="messages.php" name="email_client">';
echo '<input type="hidden" name="account_id" value="'.$account['id'].'" />';
echo '<input type="hidden" name="task" value="set_search_query" />';

$tabtable = new tabtable('search_tab', $ml_search.' - '.$account['email'], '100%', '');
$tabtable->print_head($return_to);

if(isset($feedback))
{
	echo $feedback;
}

echo '<table border="0"><tr><td valign="top">';
echo '<table border="0">';
if ($account['type'] == "imap")
{
  if ($email->get_subscribed($account['id']) > 0)
  {
    $dropbox = new dropbox();
    $dropbox->add_value('INBOX',$lang['email']['inbox']);
    while ($email->next_record())
    {
      if (!($email->f('attributes')&LATT_NOSELECT))
      {
	$dropbox->add_value($email->f('name'), str_replace('INBOX'.$email->f('delimiter'), '', $email->f('name')));
      }
    }
    echo '<tr><td>'.$ml_folder.':</td><td>';
    $dropbox->print_dropbox('mailbox', $mailbox);
    echo '</td></tr>';
  }
}

echo 	'<tr><td>'.$ml_subject.':</td><td>'.		
'<input type="text" name="subject" size="40" class="textbox" value="'.htmlspecialchars($subject).'" />'.
'</td></tr>';

echo 	'<tr><td>'.$ml_from.':</td><td>'.		
'<input type="text" name="from" size="40" class="textbox" value="'.htmlspecialchars($from).'" />'.
'</td></tr>';

echo 	'<tr><td>'.$ml_to.':</td><td>'.		
'<input type="text" name="to" size="40" class="textbox" value="'.htmlspecialchars($to).'" />'.
'</td></tr>';

echo 	'<tr><td>CC:</td><td>'.		
'<input type="text" name="cc" size="40" class="textbox" value="'.htmlspecialchars($cc).'" />'.
'</td></tr>';

echo 	'<tr><td>'.$ml_body.':</td><td>'.		
'<input type="text" name="body" size="40" class="textbox" value="'.htmlspecialchars($body).'" />'.
'</td></tr>';		

echo '</table></td><td valign="top"><table border="0">';

echo 	'<tr><td>'.$ml_before.':</td><td>';			
$date_picker = new date_picker('before', $_SESSION['GO_SESSION']['date_format'], $before);
echo $date_picker->get_html();
echo '</td></tr>';

echo 	'<tr><td>'.$ml_since.':</td><td>';			
$date_picker = new date_picker('since', $_SESSION['GO_SESSION']['date_format'], $since);
echo $date_picker->get_html();
echo '</td></tr>';

echo '<tr><td>'.$ml_flag.':</td><td>';
$radio_list = new radio_list('flagged', $flagged);
$radio_list->add_option('', $ml_doesnt_matter);
$radio_list->add_option('FLAGGED', $cmdYes);
$radio_list->add_option('UNFLAGGED', $cmdNo);
echo '</td></tr>';

echo '<tr><td>'.$ml_answered.':</td><td>';
$radio_list = new radio_list('answered', $answered);
$radio_list->add_option('', $ml_doesnt_matter);
$radio_list->add_option('ANSWERED', $cmdYes);
$radio_list->add_option('UNANSWERED', $cmdNo);
echo '</td></tr>';

echo '<tr><td>'.$ml_seen.':</td><td>';
$radio_list = new radio_list('seen', $seen);
$radio_list->add_option('', $ml_doesnt_matter);
$radio_list->add_option('SEEN', $cmdYes);
$radio_list->add_option('UNSEEN', $cmdNo);
echo '</td></tr>';
echo '</table>';

echo '</td></tr>';

echo '<tr><td colspan="2">';
$button = new button($cmdSearch, 'javascript:document.forms[0].submit();');
echo $button->get_html();
$button = new button($cmdCancel, "javascript:document.location='$return_to';");
echo $button->get_html();
echo '</td></tr>';
echo '</table>';

$tabtable->print_foot();

require_once($GO_THEME->theme_path."footer.inc");
