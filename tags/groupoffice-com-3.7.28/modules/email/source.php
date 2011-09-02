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

$account = $imap->open_account($_REQUEST['account_id'], $_REQUEST['mailbox']);

if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id,$account['acl_id'])) {
	die($lang['common']['accessDenied']);
}

header("Content-type: text/plain; charset: US-ASCII");
header('Content-Disposition: inline; filename="message_source.txt"');

/*
 * Somehow fetching a message with an empty message part which should fetch it
 * all doesn't work. (http://tools.ietf.org/html/rfc3501#section-6.4.5)
 *
 * That's why I first fetch the header and then the text.
 */
$header = $imap->get_message_part($_REQUEST['uid'], 'HEADER')."\r\n\r\n";
$size = $imap->get_message_part_start($_REQUEST['uid'],'TEXT');

header('Content-Length: '.strlen($header).$size);

echo $header;

$count=0;
while($line = $imap->get_message_part_line()){
	echo $line;
}
