<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.8 $ $Date: 2005/07/01 10:11:12 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once("../../Group-Office.php");

load_basic_controls();

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	require_once($GO_CONFIG->class_path."mail/imap.class.inc");
	require_once($GO_MODULES->class_path."email.class.inc");

	$mail = new imap();
	$email = new email();
	$account = $email->get_account($_POST['account_id']);
	
	if ($mail->open($account['host'], $account['type'],$account['port'],$account['username'],$account['password'], $_POST['mailbox'], 0, $account['use_ssl'], $account['novalidate_cert']))
	{	
		$file = $mail->view_part($_POST['uid'], $_POST['part'], $_POST['transfer']);
		$mail->close();
	
		if($file != '')
		{
		  $tmp_file = $GO_CONFIG->tmpdir.md5(uniqid(time()));
		  if (!$fp = fopen($tmp_file, 'w+'))
		  {
		    exit("Failed to open temporarily file");
		  }elseif(!fwrite($fp, $file))
		  {
		    exit("Failed to write to temporarily file");
		  }elseif($_POST['download'] == 'true')
		  {
		    fclose($fp);
			$browser = detect_browser();
			header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
			if ($browser['name'] == 'MSIE')
			{
			  header('Content-Type: application/download');
			  header('Content-Disposition: attachment; filename="'.$_REQUEST['filename'].'"');
			  header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			  header('Pragma: public');
			}else
			{
			  header('Content-Type: '.$_REQUEST['mime']);
			  header('Pragma: no-cache');
			  header('Content-Disposition: attachment; filename="'.$_REQUEST['filename'].'"');
			}
			header('Content-Transfer-Encoding: binary');
			echo ($file);
		    exit();
		  }else
		  {
		  	$ab_module = $GO_MODULES->get_module('addressbook');
	    
		    fclose($fp);
		    echo '<script type="text/javascript">';
		    echo 'opener.location="'.$ab_module['url'].'contact.php?vcf_file='.urlencode($tmp_file).'&return_to="+escape(opener.location);';
		    echo 'window.close();';
		    echo '</script>';
		    exit();
		  }
		}else
		{
			exit($strDataError);
		}
	}else
	{
		echo $strDataError;
	}
}
require_once($GO_LANGUAGE->get_language_file('email'));

require_once($GO_THEME->theme_path.'header.inc');
?>
<form method="post" name="vcard" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="account_id" value="<?php echo $_REQUEST['account_id']; ?>" />
<input type="hidden" name="mailbox" value="<?php echo $_REQUEST['mailbox']; ?>" />
<input type="hidden" name="uid" value="<?php echo $_REQUEST['uid']; ?>" />
<input type="hidden" name="part" value="<?php echo $_REQUEST['part']; ?>" />
<input type="hidden" name="transfer" value="<?php echo $_REQUEST['transfer']; ?>" />
<input type="hidden" name="mime" value="<?php echo $_REQUEST['mime']; ?>" />
<input type="hidden" name="filename" value="<?php echo $_REQUEST['filename']; ?>" />
<input type="hidden" name="download" value="" />
<p><?php echo $ml_appointment_vcf; ?></p>

<?php
$button = new button($cmdYes, "javascript:document.forms[0].download.value='';document.forms[0].submit();");
echo $button->get_html();
$button = new button($cmdNo, "javascript:document.forms[0].download.value='true';document.forms[0].submit();");
echo $button->get_html();
$button = new button($cmdClose, 'window.close()');
echo $button->get_html();
?>
</form>
<?php
require_once($GO_THEME->theme_path.'footer.inc');
