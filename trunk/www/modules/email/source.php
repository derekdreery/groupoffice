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
require_once($GO_MODULES->modules['email']['class_path']."cached_imap.class.inc.php");
require_once($GO_MODULES->modules['email']['class_path']."email.class.inc.php");

$imap = new cached_imap();
$email = new email();

$account =connect($_REQUEST['account_id'], $_REQUEST['mailbox']);

if($account['user_id']!=$GO_SECURITY->user_id)
	exit($lang['common']['access_denied']);


header("Content-type: text/plain; charset: US-ASCII");
header('Content-Disposition: inline; filename="message_source.txt"');	

$imap->get_message_part_start($_REQUEST['uid']);
while($line = $imap->get_message_part_line()){
	echo $line;
}


