<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.14 $ $Date: 2006/11/21 16:25:38 $

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
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$profile = $GO_USERS->get_user($GO_SECURITY->user_id);

	$middle_name = $profile['middle_name'] == '' ? '' : ' '.$profile['middle_name'];
	$body = $ml_displayed.$profile["first_name"]." ".$middle_name.$profile['last_name']." <".$profile["email"].">\r\n\r\n";
	$body .= $ml_subject.": ".$_POST['subject']."\r\n".$strDate.": ".$_POST['date'];

	if (!sendmail($_POST['email'], $profile["email"], $profile["first_name"]." ".$profile['last_name'], $ml_notify, $body))
	{
		$feedback = '<p class="Error">'.$ml_send_error.'</p>';
	}else
	{
		echo "<script type=\"text/javascript\">\nwindow.close();\n</script>";
		exit;
	}
}else
{
	$pattern[0]="/(.*)</";
	$pattern[1]="/>/";
	$replace="";
	$email = preg_replace($pattern, $replace, $_REQUEST['notification']);
}

$page_title=$ml_notify;
require_once($GO_THEME->theme_path."header.inc");
?>
<form method="post" name="notify" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="email" value="<?php echo $email; ?>" />
<input type="hidden" name="date" value="<?php echo $_REQUEST['date']; ?>" />
<input type="hidden" name="subject" value="<?php echo $_REQUEST['subject']; ?>" />

<table border="0" cellspacing="0" cellpadding="5" align="center">
<tr>
	<td><img src="<?php echo $GO_THEME->images['questionmark']; ?>" border="0" /></td><td><h2><?php echo $ml_notify; ?></h2></td>
</tr>
</table>
<?php
if (isset($feedback))
{
	echo "<tr><td colspan=\"2\">".$feedback."</td></tr>\n";
}
?>
<table border="0" cellspacing="0" cellpadding="5" align="center">
<tr>
	<td colspan="2">
	<?php echo $ml_ask_notify; ?>
	</td>
</tr>
<tr>
	<td colspan="2" align="center">
	<?php
	$button = new button($cmdOk, "javascript:document.forms[0].submit()");
    echo $button->get_html();
    $button = new button($cmdCancel, "javascript:window.close()");
    echo $button->get_html();
	?>
	</td>
</tr>
</table>
</form>
<?php
require_once($GO_THEME->theme_path."footer.inc");
