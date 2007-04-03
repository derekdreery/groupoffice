<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.21 $ $Date: 2006/08/30 09:40:41 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
require_once("../../Group-Office.php");

$GO_SECURITY->authenticate();

$GO_MODULES->authenticate('filesystem');
require_once($GO_LANGUAGE->get_language_file('filesystem'));
require_once($GO_CONFIG->class_path."mail/imap.class.inc");
require_once($GO_MODULES->modules['email']['class_path']."email.class.inc");
$mail = new imap();
$email = new email();

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$filename= isset($_REQUEST['filename']) ? $_REQUEST['filename'] : '';
if ($filename != '' && $_SERVER['REQUEST_METHOD'] != 'POST')
{
	$filename = stripslashes($filename);
	$_SESSION['email_tmp_file'] = $GO_CONFIG->tmpdir.$filename;
}

if ($filename == '')
{
	$filename = basename($_SESSION['email_tmp_file']);
}else
{
	$filename = smart_stripslashes($filename);
}

if (isset($task) && $task == 'GO_HANDLER')
{
	require_once($GO_CONFIG->class_path.'filesystem.class.inc');
	$fs = new filesystem();

	if (file_exists(smart_stripslashes($_REQUEST['fs_list']['path']).'/'.$filename))
	{
		$feedback = '<p class="Error">'.$fbNameExists.'</p>';

	}elseif(!$fs->has_write_permission($GO_SECURITY->user_id, smart_stripslashes($_REQUEST['fs_list']['path'])))
	{
		$feedback = '<p class="Error">'.$strAccessDenied.': '.smart_stripslashes($_REQUEST['fs_list']['path']).'</p>';
	}else
	{
		$new_path = smart_stripslashes($_REQUEST['fs_list']['path']).'/'.$filename;
		if ($fs->move($_SESSION['email_tmp_file'], $new_path))
		{
			$old_umask = umask(000);
			chmod($new_path, $GO_CONFIG->create_mode);
			umask($old_umask);
			unset($_SESSION['tmp_account_id']);
			unset($_SESSION['email_tmp_file']);

			echo "<script type=\"text/javascript\" language=\"javascript\">\n";
			echo "window.close()\n";
			echo "</script>\n";
		}else
		{
			$feedback = '<p class="Error">'.$strSaveError.'</p>';
		}
	}
}
if (isset($_REQUEST['account_id']))
{
	$_SESSION['tmp_account_id'] = $_REQUEST['account_id'];
}

$account = $email->get_account($_SESSION['tmp_account_id']);

if (!file_exists($_SESSION['email_tmp_file']) && !is_dir($_SESSION['email_tmp_file']))
{
	if ($mail->open($account['host'], $account['type'],$account['port'],$account['username'],$account['password'], $_REQUEST['mailbox'], 0, $account['use_ssl'], $account['novalidate_cert']))
	{
		$data = $mail->view_part($_REQUEST['uid'], $_REQUEST['part'], $_REQUEST['transfer']);
		$mail->close();
		$fp = fopen($_SESSION['email_tmp_file'],"w+");
		fputs ($fp, $data, strlen($data));
		fclose($fp);
	}else
	{
		die('Could not connect to mail server!');
	}
}
require_once('../../Group-Office.php');

$module = $GO_MODULES->get_module('email');
$GO_HANDLER = $_SERVER['PHP_SELF'];
$GO_CONFIG->window_mode = 'popup';
$mode = 'save';
$module = $GO_MODULES->get_module('filesystem');
require_once($module['path'].'index.php');
