<?php
/*
Copyright Intermesh 2004
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

require_once("Group-Office.php");
load_basic_controls();
require_once($GO_LANGUAGE->get_base_language_file('login'));

if(file_exists($GO_CONFIG->root_path.'login_screens/'.$GO_CONFIG->login_screen.'/login_header.inc'))
{
	require_once($GO_CONFIG->root_path.'login_screens/'.$GO_CONFIG->login_screen.'/login_header.inc');
}

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if($_POST['email'] == '')
	{
		$feedback = '<p class="Error">'.$error_missing_field.'</p>';
	}elseif($user = $GO_USERS->get_user_by_email(smart_addslashes($_POST['email'])))
	{
		$new_password = $GO_USERS->random_password();
		$GO_USERS->update_password($user['id'],$new_password);
		
		$mail_body = sprintf($login_lost_password_mail_body, $GO_CONFIG->title, $GO_CONFIG->full_url, $user['username'],$new_password);
		
		sendmail(smart_stripslashes($_POST['email']),
		$GO_CONFIG->webmaster_email,
		$GO_CONFIG->title, 
		$login_new_password,
		$mail_body);
		
		echo '<p class="Success">'.sprintf($login_lost_password_success, smart_stripslashes($_POST['email'])).'</p>';
		$button = new button($cmdContinue, "document.location='index.php';");
		if(file_exists($GO_CONFIG->root_path.'login_screens/'.$GO_CONFIG->login_screen.'/login_footer.inc'))
		{
			require_once($GO_CONFIG->root_path.'login_screens/'.$GO_CONFIG->login_screen.'/login_footer.inc');
		}
		exit();		
	}else 
	{
		$feedback = '<p class="Error">'.sprintf($login_lost_password_failed, smart_stripslashes($_POST['email'])).'</p>';
	}
}

$form = new form('lost_password');

$form->add_html_element(new html_element('h1',$login_lost_password_title));
$form->add_html_element(new html_element('p', $login_lost_password_text));
$input = new input('text', 'email');
$input->set_attribute('style','width:300px;');
$form->add_html_element(new html_element('p',$strEmail.': '.$input->get_html()));
$form->add_html_element(new button($cmdOk, "javascript:document.forms[0].submit();"));
$form->add_html_element(new button($cmdCancel, "javascript:document.location='index.php';"));
echo $form->get_html();
?>
<script type="text/javascript">
document.forms[0].email.focus();
</script>
<?php 
if(file_exists($GO_CONFIG->root_path.'login_screens/'.$GO_CONFIG->login_screen.'/login_footer.inc'))
{
	require_once($GO_CONFIG->root_path.'login_screens/'.$GO_CONFIG->login_screen.'/login_footer.inc');
}
?>
