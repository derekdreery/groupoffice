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

require('../../Group-Office.php');

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');

load_basic_controls();
load_control('datatable');
load_control('tooltip');

require_once ($GO_CONFIG->class_path."mail/imap.class.inc");
require_once ($GO_MODULES->class_path."email.class.inc");
require_once ($GO_LANGUAGE->get_language_file('email'));
$mail = new imap();
$email = new email();


$account_id = isset ($_REQUEST['account_id']) ? $_REQUEST['account_id'] : 0;
$task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';
$uid = isset ($_REQUEST['uid']) ? $_REQUEST['uid'] : 0;
$mailbox = isset ($_REQUEST['mailbox']) ? smart_stripslashes($_REQUEST['mailbox']) : 'INBOX';
$link_back = $GO_MODULES->url.'messages.php?account_id='.$account_id.'&mailbox='.urlencode($mailbox);

$refresh_treeview = false;
$first_uid = false;

$em_settings = $email->get_settings($GO_SECURITY->user_id);

//search query parameters
$from = isset ($_REQUEST['from']) ? smart_stripslashes(trim($_REQUEST['from'])) : '';
$to = isset ($_REQUEST['to']) ? smart_stripslashes(trim($_REQUEST['to'])) : '';
$subject = isset ($_REQUEST['subject']) ? smart_stripslashes(trim($_REQUEST['subject'])) : '';
$cc = isset ($_REQUEST['cc']) ? smart_stripslashes(trim($_REQUEST['cc'])) : '';
$body = isset ($_REQUEST['body']) ? smart_stripslashes(trim($_REQUEST['body'])) : '';
$before = isset ($_REQUEST['before']) ? smart_stripslashes(trim($_REQUEST['before'])) : '';
$since = isset ($_REQUEST['since']) ? smart_stripslashes(trim($_REQUEST['since'])) : '';
$before = isset ($_REQUEST['before']) ? $_REQUEST['before'] : '';
$since = isset ($_REQUEST['since']) ? $_REQUEST['since'] : '';
$flagged = isset ($_REQUEST['flagged']) ? $_REQUEST['flagged'] : '';
$answered = isset ($_REQUEST['answered']) ? $_REQUEST['answered'] : '';
$seen = isset($_REQUEST['seen']) ? $_REQUEST['seen'] : '';


$datatable = new datatable('messages_table');

if ($task == 'set_search_query' || !isset ($_SESSION['email_search_query'])) {
	$_SESSION['email_search_query'] = $mail->build_search_query($subject, $from, $to, $cc, $body, $before, $since, $before, $since, $flagged, $answered, $seen);
	$datatable->start=0;
}



if (!$account = $email->get_account($account_id)) {
	$account = $email->get_account(0);
}
if ($account) {
	if (!$mail->open($account['host'], $account['type'], $account['port'], $account['username'], $account['password'], $mailbox, 0, $account['use_ssl'], $account['novalidate_cert'])) {
		$GO_HEADER['body_arguments'] = 'onload="parent.treeview.location.href=\'treeview.php\';"';
		require($GO_THEME->theme_path.'header.inc');
		echo '<p class="Error">'.$ml_connect_failed.' \''.$account['host'].'\' '.$ml_at_port.': '.$account['port'].'</p>';
		echo '<p class="Error">'.imap_last_error().'</p>';
		require_once ($GO_THEME->theme_path.'footer.inc');
		exit ();
	}
}



//check for the addressbook module
if (isset($GO_MODULES->modules['addressbook']) && $GO_MODULES->modules['addressbook']['read_permission'])
{
	require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
	$ab = new addressbook();
}



if(isset($_POST['form_action']) && $_POST['form_action'] == 'empty_folder')
{
	$mail->sort();
	$mail->delete($mail->sort);
	$refresh_treeview = true;
}

/*
show from address in normal mail folders and show to in sent items folders
*/
$show = "from";
$get_to_addresses = ($mailbox=='INBOX');
if ($mail->is_imap() && $account['sent'] != '')
{
	if (strpos($mailbox, $account['sent']) === 0)
	{
		$show = "to";
	}
}





if(isset($_GET['mailbox']))
{
	$datatable->start=0;
}

$th = new table_heading();
$datatable->add_column($th);

if ($show == "from")
{
	$datatable->add_column(new table_heading($ml_from, SORTFROM));
}else
{
	$datatable->add_column(new table_heading($ml_to, SORTTO));
}
$th = new table_heading();
$datatable->add_column($th);

$datatable->add_column(new table_heading($ml_subject, SORTSUBJECT));
$datatable->add_column(new table_heading($ml_size, SORTSIZE));
$datatable->add_column(new table_heading($strDate, SORTARRIVAL));

//when this is post request delete selected messages
if ($_SERVER['REQUEST_METHOD'] == "POST" && count($datatable->selected))
{
	switch ($_POST['form_action'])
	{
		case 'delete':
			if ($mailbox == $account['trash'] || $account['type'] == 'pop3' || $account['trash'] == '')
			{
				$mail->delete($datatable->selected);
			}else
			{
				$mail->set_message_flag($mailbox, $datatable->selected, "\\Seen");
				$mail->move($account['trash'], $datatable->selected);

				if($em_folder = $email->get_folder($account['id'], addslashes($account['trash'])))
				{
					$status = $mail->status($em_folder['name'], SA_ALL);
					$update_folder['id']=$em_folder['id'];
					$update_folder['unseen'] = $status->unseen;
					$update_folder['msgcount'] = $status->messages;
					$email->__update_folder($em_folder);
					$refresh_treeview = true;
				}
			}
			if($account['type'] == 'pop3')	$mail->reopen($mailbox);
			break;

		case 'move':
			$mail->move(smart_stripslashes($_POST['folder']), $datatable->selected);
			if($account['type'] == 'pop3')	$mail->reopen($mailbox);

			if($em_folder = $email->get_folder($account['id'], smart_addslashes($_POST['folder'])))
			{
				$status = $mail->status($em_folder['name'], SA_ALL);
				$update_folder['id']=$em_folder['id'];
				$update_folder['unseen'] = $status->unseen;
				$update_folder['msgcount'] = $status->messages;
				$email->__update_folder($update_folder);
				$refresh_treeview = true;
			}
			break;

		case 'set_flag':
			switch($_POST['flag'])
			{
				case 'read':
					$mail->set_message_flag($mailbox, $datatable->selected, "\\Seen");
					$refresh_treeview = true;
					break;

				case 'unread':
					$mail->set_message_flag($mailbox, $datatable->selected, "\\Seen", "reset");
					$refresh_treeview = true;
					break;

				case 'flag':
					$mail->set_message_flag($mailbox, $datatable->selected, "\\Flagged");
					break;

				case 'clear_flag':
					$mail->set_message_flag($mailbox, $datatable->selected, "\\Flagged", "reset");
					break;
			}
			break;
	}
}
if($mailbox == $account['sent'])
{
	$email_search_query = str_replace('FROM', 'TO', $_SESSION['email_search_query']);
}else
{
	$email_search_query = $_SESSION['email_search_query'];
}

$sort_field = ($datatable->sort_index == SORTARRIVAL && !$mail->is_imap()) ? SORTDATE : $datatable->sort_index;
$sort_order = $datatable->sort_ascending ? 0 : 1;
$msg_count = $mail->sort($sort_field , $sort_order, $email_search_query);

if ($msg_count > 0)
{
	if (strtolower($mailbox) == "inbox")
	{
		$email_filter = array();
		$subject_filter = array();

		$filters = array();

		if(!empty($account['spamtag']) && !empty($account['spam']))
		{
			$filter['field'] = 'subject';
			$filter['folder'] = $account['spam'];
			$filter['keyword'] = $account['spamtag'];
			$filter['mark_as_read'] = false;
			$filters[] = $filter;
		}
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
	$mail->get_messages($datatable->start, $datatable->offset);
	$row_count = 0;
	while($mail->next_message($get_to_addresses))
	{

		$row = new table_row($mail->f('uid'));

		if($em_settings['show_preview'] == '1')
		{
			$row->set_attribute('onclick', 'javascript:open_message(event, '.$mail->f('uid').', this);table_select(event, \''.
			$datatable->form_name.'\',\''.
			$datatable->attributes['id'].'\',\''.
			addslashes($row->attributes['id']).'\','.
			($datatable->multiselect ? 'true' : 'false').','.
			$_SESSION['GO_SESSION']['use_checkbox_select'].');');
		}
		if($account['drafts'] != '' && $mailbox == $account['drafts'])
		{
			$row->set_attribute('ondblclick', "javascript:popup('send.php?mail_from=".
			$account['id']."&uid=".$mail->f('uid')."&mailbox=".urlencode($mailbox).
			"&action=open','".$GO_CONFIG->composer_width."','".
			$GO_CONFIG->composer_height."');");
		}else
		{
			if($em_settings['show_preview'] == '0')
			{
				$row->set_attribute('ondblclick', 'javascript:table_clear_old_class('.$mail->f('uid').');open_message(event, '.$mail->f('uid').', this);table_select(event, \''.
				$datatable->form_name.'\',\''.
				$datatable->attributes['id'].'\',\''.
				addslashes($row->attributes['id']).'\','.
				($datatable->multiselect ? 'true' : 'false').','.
				$_SESSION['GO_SESSION']['use_checkbox_select'].');parent.toggle_navigation(false);');
			}else
			{
				$row->set_attribute('ondblclick', 'javascript:parent.toggle_navigation(false);');
			}
		}


		$row_count++;
		$continue = false;
		//check if message is new and apply users filters to new messages only in the inbox folder.
		if ($mail->f('new') == 1)
		{
			if (strtolower($mailbox) == "inbox")
			{
				for ($i=0;$i<sizeof($filters);$i++)
				{
					if ($filters[$i]["folder"])
					{
						$field = $mail->f($filters[$i]["field"]);
						if (!is_array($field))
						{
							if (stristr($field, $filters[$i]["keyword"]) !== false)
							{
								$messages[] = $mail->f("uid");
								if($filter['mark_as_read'])
								{
									$mail->set_message_flag($mailbox, $messages, "\\Seen");
								}
								if ($mail->move($filters[$i]["folder"], $messages))
								{
									$continue = true;
									$refresh = true;
									break;
								}
							}
						}else
						{
							for ($x=0;$x<sizeof($field);$x++)
							{
								if (stristr($field[$x], $filters[$i]["keyword"]))
								{
									$messages[] = $mail->f("uid");
									if($filter['mark_as_read'])
									{
										$mail->set_message_flag($mailbox, $messages, "\\Seen");
									}
									if ($mail->move($filters[$i]["folder"], $messages))
									{
										$continue = true;
										$refresh = true;
										break;
									}
								}
							}
						}
					}
				}
			}

			if ($continue)
			{
				continue;
			}

			$row->set_attribute('class','NewMail');
			$img = new image('mail');
		}else	if ($mail->f('answered'))
		{
			$img = new image('mail_repl');
		}else
		{
			$img = new image('mail');
		}
		$img->set_attribute('style', 'border:0px;width:16px;height:16px;margin-right:3px;');
		$img->set_attribute('valign', 'middle');

		//display message
		$subject = $mail->f('subject') ? $mail->f('subject') : $ml_no_subject;
		$short_subject = cut_string($subject, 45);
		if ($show == "from")
		{
			$short_from = cut_string($mail->f('from'), 30);
		}else
		{
			$to = '';
			$to_array = $mail->f("to");

			for ($i=0;$i<sizeof($to_array);$i++)
			{
				if ($i != 0)
				{
					$to .= ", ";
				}
				$to .= enc_utf8($to_array[$i]);
			}
			if ($to == "")
			{
				$to = $ml_no_reciepent;
			}
			$to = htmlspecialchars($to, ENT_QUOTES);
			$short_to = cut_string($to, 50);
		}
		if ($mail->f('flagged') == '1')
		{
			$flag = new image('flag');
			$flag->set_attribute('style', 'border:0px;width:16px;height:16px;margin-right:3px;');
			$flag->set_attribute('valign', 'middle');
			$flag = $flag->get_html();
		}else
		{
			$flag = '&nbsp;';
		}
		$row->set_tooltip(new tooltip(htmlspecialchars($mail->f('from'))."&nbsp;&lt;".htmlspecialchars($mail->f("sender"))."&gt;<br />".htmlspecialchars($subject), '', 'ol_width=300'));

		$row->add_cell(new table_cell($img->get_html()));

		if ($show=="from")
		{
			$cell = new table_cell($short_from);

			if(isset($ab) && $contact = $ab->get_contact_by_email($mail->f("sender"), $GO_SECURITY->user_id))
			{
				$cell->set_attribute('style','color: #'.$contact['color'].';white-space:nowrap');
			}else
			{
				$cell->set_attribute('style','white-space:nowrap');
			}
			$row->add_cell($cell);
		}else
		{
			$cell = new table_cell($short_to);
			$cell->set_attribute('style','white-space:nowrap');
			$row->add_cell($cell);
		}

		$cell = new table_cell($flag);
		$cell->set_attribute('style','width:10px;');
		$row->add_cell($cell);

		$cell = new table_cell($short_subject);
		$cell->set_attribute('style','white-space:nowrap');
		$row->add_cell($cell);

		$cell = new table_cell(format_size($mail->f('size')));
		$cell->set_attribute('style','white-space:nowrap');
		$row->add_cell($cell);

		$cell = new table_cell(date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], get_time($mail->f('udate'))));
		$cell->set_attribute('style','white-space:nowrap');
		$row->add_cell($cell);

		$datatable->add_row($row);

		if(!$first_uid)
		{
			$first_uid = $mail->f('uid');
		}
	}
}

if(isset($refresh))
{
	header('Location: '.add_params_to_url($link_back, 'refreshed=true'));
	exit();
}elseif(isset($_GET['refreshed']))
{
	$email->cache_account_status($account);
	$refresh_treeview=true;
}

$status = $mail->status($mailbox, SA_ALL);
$mailbox_msg_count = $status->messages;
$unseen = $status->unseen;
$datatable->set_pagination($msg_count);

$email_folder = $email->get_folder($account['id'], addslashes($mailbox));

$update_folder['id'] =$email_folder['id'];
$update_folder['unseen'] = $unseen;
$update_folder['msgcount'] = $msg_count;
$email->__update_folder($update_folder);

if ($msg_count == 0)
{
	if ($email_folder['attributes']&LATT_NOSELECT)
	{
		$row = new table_row();
		$cell = new table_cell($ml_no_mailbox);
		$cell->set_attribute('colspan','99');
		$cell->set_attribute('style','height:18px;');
		$row->add_cell($cell);
		$datatable->add_row($row);
	}else
	{
		$row = new table_row();
		$cell = new table_cell($ml_no_messages);
		$cell->set_attribute('colspan','99');
		$cell->set_attribute('style','height:18px;');
		$row->add_cell($cell);
		$datatable->add_row($row);
	}
}else
{
	$row = new table_row();
	$row->set_attribute('class','small');

	$cell = new table_cell($mailbox_msg_count.' '.$ml_messages.'&nbsp;&nbsp;&nbsp;');
	if ($mail->is_imap())
	{
		$cell->innerHTML .= '('.$unseen.' '.$ml_new.')';
	}
	$cell->set_attribute('colspan','99');
	$cell->set_attribute('style','height:18px;');
	$row->add_cell($cell);
	$datatable->add_footer($row);
}


$GO_HEADER['nomessages'] = true;
$GO_HEADER['body_arguments'] = 'onload="initializeDocument();"';
$GO_HEADER['head'] = tooltip::get_header();
$GO_HEADER['head'] .= $datatable->get_header();
$GO_HEADER['head'] .= '<script type="text/javascript" src="'.$GO_MODULES->url.'email.js"></script>';
require($GO_THEME->theme_path.'header.inc');

$form = new form('email_form');
//$form->add_html_element(new input('hidden', 'empty_mailbox'));
$form->add_html_element(new input('hidden', 'link_back', $link_back));
$form->add_html_element(new input('hidden', 'account_id', $account_id));
$form->add_html_element(new input('hidden', 'form_action'));
$form->add_html_element(new input('hidden', 'mailbox', $mailbox));

if ($_SESSION['email_search_query'] != '') {
	$div = new html_element('div');
	$div->set_attribute('class', 'headerTable');
	$div->set_attribute('style', 'padding:2px;');

	$img = new image('info');
	$img->set_attribute('style', 'border:0;margin-right:3px;');
	$img->set_attribute('align','absmiddle');

	$link = new hyperlink($_SERVER['PHP_SELF'].'?account_id='.$account['id'].'&mailbox='.urlencode($mailbox).'&task=set_search_query', $img->get_html().$ml_search_active);

	$div->add_html_element($link);

	$form->add_html_element($div);
}

$table = new table();
$table->set_attribute('style','width:100%');
$table->set_attribute('cellpadding','0');
$table->set_attribute('cellspacing','0');

$row = new table_row();

$cell = new table_cell();

require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();
if(eregi('localhost', $account['host']) && $quota = $fs->get_quota($account['username']))
{
	$percentage = number_format(($quota['used']/$quota['total'])*100);
	$text = $percentage.'% '.$ml_used_of.' '.format_size($quota['total']);
	load_control('statusbar');
	$statusbar = new statusbar($quota['used'], $quota['total']);

	$st_table = new table();
	//$table->set_attribute('cellpadding','0');
	$st_table->set_attribute('cellspacing','0');

	$tr = new table_row();

	$tr->add_cell(new table_cell($statusbar->get_html()));

	$td = new table_cell($text);
	$tr->add_cell($td);
	$st_table->add_row($tr);

	$cell->add_html_element($st_table);
}


$row->add_cell($cell);

$cell = new table_cell();
$cell->set_attribute('align','right');

$h3 = new html_element('h3');

$h3->innerHTML = $account['email'].' - ';
if ($mailbox == $account['mbroot'] || $mailbox == 'INBOX') {
	$h3->innerHTML .=  $ml_inbox;
}elseif ($account['mbroot'] != '') {
	$h3->innerHTML .= utf7_imap_decode(str_replace($account['mbroot'], '', $mailbox));
} else {
	$h3->innerHTML .=  utf7_imap_decode($mailbox);
}

$h3->innerHTML .= ' ('.$mailbox_msg_count;

if($mail->is_imap())
{
	$h3->innerHTML .= '/'.$unseen.')';
}else
{
	$h3->innerHTML .= ')';
}

$cell->add_html_element($h3);
$row->add_cell($cell);

$table->add_row($row);

$row = new table_row();

$cell = new table_cell();
$cell->set_attribute('style', 'text-align:right;white-space: nowrap');
$cell->set_attribute('colspan', '2');

if ($account['type'] == "imap")
{
	if ($email->get_subscribed($account['id']) > 0)
	{
		$select = new select('folder', '');
		$select->set_attribute('onchange' , 'javascript:move_mail()');

		$select->add_value('', $ml_move_mail);
		while ($email->next_record())
		{
			if (!($email->f('attributes')&LATT_NOSELECT) && $email->f('name') != $mailbox)
			{
				if($email->f('name') == 'INBOX')
				{
					$select->add_value('INBOX',$ml_inbox);
				}else
				{
					$select->add_value($email->f('name'), utf7_imap_decode(str_replace('INBOX'.$email->f('delimiter'), '', $email->f('name'))));
				}
			}
		}
		$cell->add_html_element($select);
	}

	$select = new select('flag', '');
	$select->set_attribute('onchange' , 'javascript:set_flag()');
	$select->add_value('', $ml_mark);
	$select->add_value('read', $ml_markread);
	$select->add_value('unread', $ml_markunread);
	$select->add_value('flag', $ml_flag);
	$select->add_value('clear_flag', $ml_clearflag);

	$cell->add_html_element($select);
}
$row->add_cell($cell);
$table->add_row($row);

$form->add_html_element($table);
$form->add_html_element($datatable);

echo $form->get_html();
?>
<script type="text/javascript">	
function confirm_delete()
{
	document.forms[0].form_action.value='delete';
	parent.close_message_frame_for_delete();
	<?php
	$confirm =  (empty($account['trash']) || $account['trash']==$mailbox);
	echo $datatable->get_delete_handler(0,$confirm); ?>
}

function initializeDocument()
{

	//Refresh treeview so that the mail checker doesn't alert about new mail the
	//user has already seen
	<?php
	if(!$refresh_treeview) echo 'var loc = parent.treeview.location.toString();if(loc.substring(loc.length-10) == \'blank.html\'){';
	?>
	parent.treeview.location.href='treeview.php';
	<?php
	if(!$refresh_treeview) echo '}';
	?>
	parent.update_toolbar(false);
	parent.message.location.replace('blank.html');
}
var last_selected= 0;
var start_point = 0;

function open_message(evt, uid, row)
{
	evt=evt||false;
	if (navigator.userAgent.toLowerCase().indexOf('mac')>=0) {
		var ctrlPressed = (evt && evt.altKey);
	} else {
		var ctrlPressed = (evt && evt.ctrlKey);
	}
	var shiftPressed = (evt && evt.shiftKey);

	if(!ctrlPressed && !shiftPressed &&
	!(
	parent.message.location.href.indexOf('message.php')>-1 &&
	parent.message.document.forms["email_form"].uid.value==uid
	)
	)
	{
		row.className='';
		<?php
		echo 'parent.message.location="message.php?account_id='.$account['id'].'&uid="+uid+"&mailbox='.urlencode($mailbox).'&sort_index='.$datatable->sort_index.'&sort_ascending='.$datatable->sort_ascending.'";';
		?>
	}
}

function confirm_empty_folder(account_id, folder, message)
{
	if(confirm(message))
	{
		document.forms[0].account_id.value=account_id;
		document.forms[0].mailbox.value=folder;
		document.forms[0].form_action.value='empty_folder';
		document.forms[0].submit();
	}
}
</script>
<?php
require($GO_THEME->theme_path.'footer.inc');
