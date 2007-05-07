<?php
/*
   Annienetwerk 2004
   Author: Rogier Koning <info@annienetwerk.nl>
   Version: 1.0 Release date: 08 May 2004

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.
 */
require('../../../Group-Office.php');
require($GO_LANGUAGE->get_language_file('cms'));

$cms_module = $GO_MODULES->get_module('cms');

if(isset($_REQUEST['site_id']) && $cms_module)
{
  require($cms_module['class_path'].'cms.class.inc');
  require($cms_module['class_path'].'cms_site.class.inc');
  $cms_site = new cms_site($_REQUEST['site_id']);
}

$email_to = $GO_CONFIG->webmaster_email;
if(isset($cms_site) && $cms_site)
{
  echo $cms_site->generate_header();
  
  if($site_owner = $GO_USERS->get_user($cms_site->site['user_id']))
  {
  	$email_to = $site_owner['email'];
  } 
}else
{  
  require($GO_THEME->theme_path."header.inc");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  $bedrijf_from = smart_stripslashes(trim($_POST['bedrijf_from']));
  $name_from = smart_stripslashes(trim($_POST['name_from']));
  $functie_from = smart_stripslashes(trim($_POST['functie_from']));
  $straat_from = smart_stripslashes(trim($_POST['straat_from']));
  $postcode_from = smart_stripslashes(trim($_POST['postcode_from']));
  $woonplaats_from = smart_stripslashes(trim($_POST['woonplaats_from']));
  $telefoon_from = smart_stripslashes(trim($_POST['telefoon_from']));
  $email_from = smart_stripslashes(trim($_POST['email_from']));
  $subject = smart_stripslashes(trim($_POST['subject']));  
  $mail_body = 'Bedrijfsnaam: '.$bedrijf_from."\r\n".
  			   'Naam: '.$name_from."\r\n".
  			   'Functie: '.$functie_from."\r\n\r\n".
  			   'Straat: '.$straat_from."\r\n".
  			   'Postcode: '.$postcode_from."\r\n".
  				'Woonplaats: '.$woonplaats_from."\r\n".
  				'Telefoon: '.$telefoon_from."\r\n".
  				'E-mail: '.$email_from."\r\n\r\n".
  				'Onderwerp: '.$subject."\r\n\r\n".
				smart_stripslashes(trim($_POST['mail_body']));
  
  if ($bedrijf_from == '' || $name_from == '' || $functie_from == '' || $straat_from == '' || $postcode_from == '' || $woonplaats_from == '' || $telefoon_from == '' || $email_from == '' || $subject == '' || $mail_body == '')
  {
    $feedback = '<p class="Error">'.$error_missing_field.'</p>';
  }elseif(!validate_email($email_from))
  {
    $feedback = '<p class="Error">'.$error_email.'</p>';
  }else
  {
    if(!sendmail($email_to, $email_from, $name_from, $subject, $mail_body))
	{
	  $feedback = '<p class="Error">'.$cms_sendmail_error.'</p>';
	}else
	{
	  echo $cms_sendmail_success;
	  require($GO_THEME->theme_path."footer.inc");
	  exit();	
	}    
  }
}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" name="register">
<input type="hidden" name="site_id" value="<?php echo $_REQUEST['site_id']; ?>" />
<?php if (isset($feedback)) echo $feedback; ?>

<table border="0">
<tr>
<td align="right" nowrap>
<?php echo $strCompany; ?>:
</td>
<td>
<input type="text" name="bedrijf_from" size="40" style="width: 360px;" value="<?php if(isset($_POST['bedrijf_from'])) echo $_POST['bedrijf_from']; ?>" maxlength="50">
</td>
</tr>

<tr>
<td align="right" nowrap>
<?php echo $strName; ?>:
</td>
<td>
<input type="text" name="name_from" size="40" style="width: 360px;" value="<?php if(isset($_POST['name_from'])) echo $_POST['name_from']; ?>" maxlength="50">
</td>
</tr>

<tr>
<td align="right" nowrap>
<?php echo $strFunction ?>:
</td>
<td>
<input type="text" name="functie_from" size="40" style="width: 360px;" value="<?php if(isset($_POST['functie_from'])) echo $_POST['functie_from']; ?>" maxlength="50">
</td>
</tr>

<tr>
<td align="right" nowrap>
<?php echo $strAddress; ?>:
</td>
<td>
<input type="text" name="straat_from" size="40" style="width: 360px;" value="<?php if(isset($_POST['straat_from'])) echo $_POST['straat_from']; ?>" maxlength="75">
</td>
</tr>

<tr>
<td align="right" nowrap>
<?php echo $strZip; ?>:
</td>
<td>
<input type="text" name="postcode_from" size="40" style="width: 150px;" value="<?php if(isset($_POST['postcode_from'])) echo $_POST['postcode_from']; ?>" maxlength="50">
</td>
</tr>

<tr>
<td align="right" nowrap>
<?php echo $strCity; ?>:
</td>
<td>
<input type="text" name="woonplaats_from" size="40" style="width: 360px;" value="<?php if(isset($_POST['woonplaats_from'])) echo $_POST['woonplaats_from']; ?>" maxlength="50">
</td>
</tr>


<tr>
<td align="right" nowrap>
<?php echo $strPhone; ?>:
</td>
<td>
<input type="text" name="telefoon_from" size="40" style="width: 360px;" value="<?php if(isset($_POST['telefoon_from'])) echo $_POST['telefoon_from']; ?>" maxlength="50">
</td>
</tr>

<tr>
<td align="right" nowrap valign="top">
<?php echo $strEmail; ?>:
</td>
<td class="small">
<input type="text" name="email_from" style="width: 360px;" value="<?php if(isset($_POST['email_from'])) echo $_POST['email_from']; ?>" maxlength="75"><br />
</td>
</tr>
<tr>
<td align="right" nowrap valign="top">
<?php echo $cms_subject; ?>:
</td>
<td class="small">
<input type="text" name="subject" style="width: 360px;" value="<?php if(isset($_POST['subject'])) echo $_POST['subject']; ?>" maxlength="75"><br />
</td>
</tr>
<tr>
<td align="right" nowrap valign="top">
<?php echo $cms_message; ?>:
</td>
<td>
<textarea name="mail_body" style="width: 360px;height: 220px;"><?php if(isset($_POST['mail_body'])) echo $_POST['mail_body']; ?></textarea>
</td>
</tr>
<tr>
<td colspan="2" align="center">
<input type="submit" value="<?php echo $cmdOk; ?>" style="width:100px"; />
&nbsp;&nbsp;
<input type="reset" value="<?php echo $cmdReset; ?>" style="width:100px"; />
</td>
</tr>
</table>
<?php
if(isset($cms_site) && $cms_site)
{
  $cms_site->generate_footer();
}else
{
  require($GO_THEME->theme_path."footer.inc");
}
