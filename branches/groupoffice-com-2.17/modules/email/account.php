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

load_basic_controls();


require_once($GO_CONFIG->class_path."mail/imap.class.inc");
require_once($GO_MODULES->class_path."email.class.inc");
require_once($GO_LANGUAGE->get_language_file('email'));
$mail = new imap();
$email = new email();

$GO_CONFIG->set_help_url($ml_help_url);

$account_id = isset($_REQUEST['account_id']) ? $_REQUEST['account_id'] :0;
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

$return_to = (isset($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ?
$_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$link_back = (isset($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ?
$_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];

if(isset($_REQUEST['feedback'])) $feedback = $_REQUEST['feedback'];

//delete accounts if requested
switch ($task)
{
	case 'save_vacation':
		$account['id']=$account_id;
		$account['enable_vacation'] = isset($_POST['enable_vacation']) ? '1' : '0';
		$account['vacation_subject'] = isset($_POST['vacation_subject']) ? smart_addslashes($_POST['vacation_subject']) : '';
		$account['vacation_text'] = isset($_POST['vacation_text']) ? smart_addslashes($_POST['vacation_text']) : '';
		
		$email->_update_account($account);
		
		exec($GO_CONFIG->cmd_sudo.' '.$GO_CONFIG->root_path.'action.php set_vacation '.$account_id);
		
		if (isset($_POST['close']) && $_POST['close'] == 'true')
		{
			header('Location: '.$return_to);
			exit();
		}
		break;
		break;
	case 'save_folders':

		$account = $email->get_account($account_id);

		if ($account && $mail->open($account['host'], $account['type'],
		$account['port'],$account['username'],
		$account['password'],'INBOX', 0, $account['use_ssl'], $account['novalidate_cert']))
		{
			$subscribed = $mail->get_subscribed();
			$subscribed_names = array();
			if (isset($_POST['use']))
			{
				while($mailbox = array_shift($subscribed))
				{
					$subscribed_names[] = $mailbox['name'];

					$search_name = get_magic_quotes_gpc() ? addslashes($mailbox['name']) : $mailbox['name'];
					if (!in_array($search_name, $_POST['use']))
					{
						if($mail->unsubscribe($mailbox['name']))
						{
							$email->unsubscribe($account['id'], addslashes($mailbox['name']));
						}
					}
				}

				for ($i=0;$i<count($_POST['use']);$i++)
				{
					$must_be_subscribed = smart_stripslashes($_POST['use'][$i]);

					if (!in_array($must_be_subscribed, $subscribed_names))
					{
						if($mail->subscribe($must_be_subscribed))
						{
							$email->subscribe($account['id'], addslashes($must_be_subscribed));
						}
					}
				}
			}else
			{
				while($mailbox = array_shift($subscribed))
				{
					if($mail->unsubscribe($mailbox['name']))
					{
						$email->unsubscribe($account['id'], addslashes($mailbox['name']));
					}
				}
			}

			$up_account['id'] = $account_id;
			$up_account['sent'] = isset($_POST['sent']) ? smart_addslashes($_POST['sent']) : '';
			$up_account['trash'] = isset($_POST['trash']) ? smart_addslashes($_POST['trash']) : '';
			$up_account['drafts'] = isset($_POST['drafts']) ? smart_addslashes($_POST['drafts']) : '';
			$up_account['spam'] = isset($_POST['spam']) ? smart_addslashes($_POST['spam']) : '';
			$up_account['spamtag']= smart_addslashes($_POST['spamtag']);

			$email->_update_account($up_account);

			if (isset($_POST['new_name']))
			{
				$new_name = smart_stripslashes(trim($_POST['new_name']));
				$old_name = smart_stripslashes(trim($_POST['old_name']));
				$location = smart_stripslashes(trim($_POST['location']));
				if ($new_name == '')
				{
					$feedback = '<p class="Error">'.$error_missing_field.'</p>';
				}else
				{
					if ($mail->rename_folder($old_name, $location.utf7_imap_encode($new_name)))
					{
						$email->rename_folder($account_id, addslashes($old_name), addslashes($location.utf7_imap_encode($new_name)));
						//synchronise Group-Office with the IMAP server

					}
				}
			}
			$mail->close();
		}

		if (isset($_POST['close']) && $_POST['close'] == 'true')
		{
			header('Location: '.$return_to);
			exit();
		}
		break;

	case 'save_account':

		$task = 'account';
		$mbroot = isset($_POST['mbroot']) ? smart_addslashes($_POST['mbroot']) : '';
		if ($_POST['name'] == "" ||
		$_POST['mail_address'] == "" ||
		$_POST['port'] == "" ||
		$_POST['email_user'] == "" ||
		$_POST['email_pass'] == "" ||
		$_POST['host'] == "")
		{
			$feedback = $error_missing_field;
		}else
		{
			$use_ssl = isset($_REQUEST['use_ssl'])  ? $_REQUEST['use_ssl'] : '0';
			$novalidate_cert = isset($_REQUEST['novalidate_cert']) ? $_REQUEST['novalidate_cert'] : '0';



			/*$sent = $_POST['type'] == 'pop3' ? '' : smart_addslashes($_POST['sent']);
			$trash = $_POST['type'] == 'pop3' ? '' : smart_addslashes($_POST['trash']);
			$drafts = $_POST['type'] == 'pop3' ? '' : smart_addslashes($_POST['drafts']);*/

			$examine_headers = isset($_POST['examine_headers']) ? '1' : '0';
			$auto_check = isset($_POST['auto_check']) ? '1' : '0';
			if ($account_id > 0)
			{
				$account_user_id=isset($_REQUEST['account_user_id']) ? smart_stripslashes($_REQUEST['account_user_id']) : 0;

				if(!$email->update_account($account_id, $_POST['type'],
				smart_addslashes($_POST['host']),
				$_POST['port'], $use_ssl, $novalidate_cert,
				smart_addslashes(utf7_imap_encode($mbroot)),
				smart_addslashes($_POST['email_user']),
				smart_addslashes($_POST['email_pass']),
				smart_addslashes($_POST['name']),
				smart_addslashes($_POST['mail_address']),
				smart_addslashes($_POST['signature']),
				$examine_headers,$account_user_id, $auto_check))
				{
					$feedback = '<p class="Error">'.$ml_connect_failed.' \''.$_POST['host'].
					'\' '.$ml_at_port.': '.$_POST['port'].'</p>';
					$feedback .= '<p class="Error">'.$email->last_error.'</p>';
				}else
				{
					if($_POST['close'] == 'true')
					{
						header('Location: '.$return_to);
						exit();
					}
				}
			}else
			{
				$account_user_id=isset($_REQUEST['account_user_id']) ? smart_stripslashes($_REQUEST['account_user_id']) : $GO_SECURITY->user_id;

				if(!$account_id = $email->add_account($account_user_id,
				$_POST['type'],
				smart_addslashes($_POST['host']),
				$_POST['port'],
				$use_ssl,
				$novalidate_cert,
				$mbroot,
				smart_addslashes($_POST['email_user']),
				smart_addslashes($_POST['email_pass']),
				smart_addslashes($_POST['name']),
				smart_addslashes($_POST['mail_address']),
				smart_addslashes($_POST['signature']),
				$examine_headers, $auto_check))
				{
					$feedback = '<p class="Error">'.$ml_connect_failed.' \''.
					$_POST['host'].'\' '.$ml_at_port.': '.$_POST['port'].'</p>'.
					'<p class="Error">'.$email->last_error.'</p>';
				}else
				{
					$goto_folders=true;
				}
			}
		}
}



if (isset($account_id))
{
	$title = $ml_edit_account;
}else
{
	$title = $ml_new_account;
}

$tabstrip = new tabstrip('account_tab', $title,'140');
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to($return_to);


if($account_id > 0)
{
	$account = $email->get_account($account_id);
}else
{
	$account = false;
}

if(!$account && !$GO_MODULES->write_permission)
{
	exit($strAccessDenied);
}elseif($account && $account['user_id']!=$GO_SECURITY->user_id && !$GO_SECURITY->has_admin_permission($GO_SECURITY->user_id)) {
	exit($strAccessDenied);
}

$tabstrip->add_tab('properties',$strProperties);


if($account)
{
	$tabstrip->add_tab('folders',$ml_folders);
	if($account['type'] == "imap")
	{
		$tabstrip->add_tab('filters',$ml_filters);
	}

	if(eregi('localhost', $account['host']) && $GO_CONFIG->email_vacation=='system_vacation')
	{
		$tabstrip->add_tab('vacation',$ml_vacation);
	}
}

if(isset($goto_folders))
{
	$tabstrip->set_active_tab('folders');
}

if($tabstrip->get_active_tab_id() == 'properties')
{
	//$GO_HEADER['body_arguments'] = 'onkeypress="executeOnEnter(\'document.forms[0].save_account()\');"';
}elseif($tabstrip->get_active_tab_id() == 'filters')
{
	load_control('datatable');
	$GO_HEADER['head'] = datatable::get_header();
}
require_once($GO_THEME->theme_path."header.inc");

$form = new form('email_client');
$form->add_html_element(new input('hidden','task','',false));
$form->add_html_element(new input('hidden','close','false'));
$form->add_html_element(new input('hidden','return_to',$return_to));
$form->add_html_element(new input('hidden','link_back',$link_back));
$form->add_html_element(new input('hidden','account_id',$account_id, false));

switch($tabstrip->get_active_tab_id())
{
	case 'vacation':
		$table = new table();
		$row = new table_row();
		$cell = new table_cell();
		$cell->set_attribute('colspan','2');
		$checkbox = new checkbox('enable_vacation','enable_vacation', true, $ml_automatic_reply, $account['enable_vacation']);
		$checkbox->set_attribute('onclick', "javascript:toggle_table(this.checked);");
		$cell->add_html_element($checkbox);


		$subtable = new table('vacation_table');
		if($account['enable_vacation'] == '0')
		{
			$subtable->set_attribute('style','display:none');
		}

		$subrow = new table_row();
		$subrow->add_cell(new table_cell($ml_subject.':'));
		$input = new input('text', 'vacation_subject', $account['vacation_subject']);
		$input->set_attribute('style','width:400px;');
		$input->set_attribute('maxlength','100');
		$subrow->add_cell(new table_cell($input->get_html()));
		$subtable->add_row($subrow);

		$subrow = new table_row();
		$subcell = new table_cell($strText.':');
		$subcell->set_attribute('valign','top');
		$subrow->add_cell($subcell);
		$textarea = new textarea('vacation_text', $account['vacation_text']);
		$textarea->set_attribute('style','width:400px;height:100px;');
		$subrow->add_cell(new table_cell($textarea->get_html()));
		$subtable->add_row($subrow);

		$cell->add_html_element($subtable);

		$row->add_cell($cell);
		$table->add_row($row);
		
		$tabstrip->add_html_element($table);
		
		$tabstrip->add_html_element(new button($cmdOk, "javascript:_save('save_vacation', 'true');"));
		$tabstrip->add_html_element(new button($cmdApply, "javascript:_save('save_vacation', 'false');"));
		$tabstrip->add_html_element(new button($cmdCancel,'javascript:document.location=\''.$return_to.'\''));
		
		$tabstrip->innerHTML .= '
			<script type="text/javascript">
			function toggle_table(visible)
			{
				var table = get_object(\'vacation_table\');
				if(visible)
				{	
					table.style.display=\'\';
					document.forms[0].vacation_subject.focus();
				}else
				{
					table.style.display=\'none\';
				}
			}
			</script>';
		break;
	case 'folders':
		require('folders.inc');
		break;

	case 'filters':
		require('filters.inc');
		break;

	default:
		require('account.inc');
		break;
}

?>
<script type="text/javascript" language="javascript">

function _save(task, close)
{
	document.forms[0].task.value = task;
	document.forms[0].close.value = close;
	document.forms[0].submit();
}
</script>
<?php
$form->add_html_element($tabstrip);
echo $form->get_html();
require_once($GO_THEME->theme_path."footer.inc");
