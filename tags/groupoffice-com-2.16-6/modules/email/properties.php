<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.19 $ $Date: 2006/11/21 16:25:38 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
require_once("../../Group-Office.php");

load_basic_controls();

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');
require_once($GO_LANGUAGE->get_language_file('email'));
require_once($GO_CONFIG->class_path."mail/imap.class.inc");
require_once($GO_MODULES->class_path."email.class.inc");
$mail = new imap();
$email = new email();

$account = $email->get_account($_REQUEST['account_id']);

if ($mail->open($account['host'], $account['type'], $account['port'], $account['username'], $account['password'], $_REQUEST['mailbox'], 0, $account['use_ssl'], $account['novalidate_cert']))
{
	//$content = $mail->get_message($_REQUEST['uid']);
	$source = $mail->get_source($_REQUEST['uid']);
}
$page_title=$fbProperties;
require_once($GO_THEME->theme_path."header.inc");
?>

<h1><?php echo $em_source; ?></h1>

<pre style="background-color:#f1f1f1;border:1px solid black;padding:5px;">
<?php
	echo $source;
?>
</pre>

<?php
require_once($GO_THEME->theme_path."footer.inc");
?>
