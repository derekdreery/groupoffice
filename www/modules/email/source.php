<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 * @since Group-Office 1.0
 */


require_once("../../Group-Office.php");

$GO_SECURITY->html_authenticate();

$account_id = ($_REQUEST['account_id']);
$mailbox = ($_REQUEST['mailbox']);
$uid = ($_REQUEST['uid']);

require_once($GO_LANGUAGE->get_language_file('email'));
require_once($GO_CONFIG->class_path."mail/imap.class.inc");
require_once($GO_MODULES->modules['email']['class_path']."email.class.inc.php");

$imap = new imap();
$email = new email();

$account = $email->get_account($_REQUEST['account_id']);

if($account['user_id']!=$GO_SECURITY->user_id)
	exit($lang['common']['access_denied']);

if ($imap->open($account['host'], $account['type'], $account['port'], $account['username'], $account['password'], $mailbox, null, $account['use_ssl'], $account['novalidate_cert']))
{
	$source = $imap->get_source($uid);

	header("Content-type: text/plain; charset: ISO-8559-1");
	header('Content-Disposition: inline; filename="message_source.txt"');	
	echo $source;
}else
{
	echo 'Error';
}

$imap->close();