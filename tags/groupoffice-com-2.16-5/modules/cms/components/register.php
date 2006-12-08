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

//modules user get's access to.
$modules_read[] = 'products';
$modules_read[] = 'filesystem';

$modules_write = array();

//user groups the user will be added to.
$user_groups = array();

//Start module of the user
$start_module = 'products';

//Theme for the user
$theme = 'Professional';

//Visible to all users?
$visible = false;


require_once("../../../Group-Office.php");
require_once($GO_LANGUAGE->get_base_language_file('users'));
require_once($GO_LANGUAGE->get_base_language_file('common'));

$datepicker = new date_picker();
$GO_HEADER['head'] = $datepicker->get_header();

$page_title = $registration_title;
require_once($GO_THEME->theme_path."header.inc");

require_once($GO_CONFIG->class_path."validate.class.inc");
$val = new validate();

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
  $first_name = smart_addslashes(trim($_POST['first_name']));
  $middle_name = smart_addslashes(trim($_POST['middle_name']));
  $last_name = smart_addslashes(trim($_POST['last_name']));
  $initials = smart_addslashes($_POST["initials"]);
  $birthday = smart_addslashes($_POST["birthday"]);
  $email = smart_addslashes($_POST["email"]);
  $home_phone = smart_addslashes($_POST["home_phone"]);
  $fax = smart_addslashes($_POST["fax"]);
  $cellular = smart_addslashes($_POST["cellular"]);
  $country = smart_addslashes($_POST["country"]);
  $state = smart_addslashes($_POST["state"]);
  $city = smart_addslashes($_POST["city"]);
  $zip = smart_addslashes($_POST["zip"]);
  $address = smart_addslashes($_POST["address"]);
  $company = smart_addslashes($_POST["company"]);  

  $pass1 = smartstrip($_POST["pass1"]);
  $pass2 = smartstrip($_POST["pass2"]);
  $username = smart_addslashes($_POST['username']);

  $val->error_required = $error_required;
  $val->error_min_length = $error_min_length;
  $val->error_max_length = $error_max_length;
  $val->error_expression = $error_email;

  $val->name="first_name";
  $val->input=$first_name;
  $val->max_length=50;
  $val->required=true;
  $val->validate_input();

  $val->name="last_name";
  $val->input=$last_name;
  $val->max_length=50;
  $val->required=true;
  $val->validate_input();
  
  $val->name="address";
  $val->input=$last_name;
  $val->max_length=50;
  $val->required=true;
  $val->validate_input();

  $val->name="zip";
  $val->input=$last_name;
  $val->max_length=50;
  $val->required=true;
  $val->validate_input();
  
  $val->name="city";
  $val->input=$last_name;
  $val->max_length=50;
  $val->required=true;
  $val->validate_input();
  
  $val->name="state";
  $val->input=$last_name;
  $val->max_length=50;
  $val->required=true;
  $val->validate_input();
  
  $val->name="country";
  $val->input=$last_name;
  $val->max_length=50;
  $val->required=true;
  $val->validate_input();
  
  $val->name="username";
  $val->input=$username;
  $val->min_length=3;
  $val->max_length=20;
  $val->required=true;
  $val->validate_input();

  $val->name="pass1";
  $val->input=$pass1;
  $val->min_length=3;
  $val->max_length=20;
  $val->required=true;
  $val->validate_input();

  $val->name="pass2";
  $val->input=$pass2;
  $val->min_length=3;
  $val->max_length=20;
  $val->required=true;
  $val->validate_input();


  $val->name="email";
  $val->input=$_POST['email'];
  $val->max_length=75;

  if (!isset($_POST['create_email']))
  {
    $val->required=true;
  }

  $val->expression="^([a-z0-9]+)([._-]([a-z0-9]+))*[@]([a-z0-9]+)([._-]([a-z0-9]+))*[.]([a-z0-9]){2}([a-z0-9])?([a-z0-9])?$";
  $val->validate_input();

  $val->error_match = $error_match_pass;
  $val->name="pass1";
  $val->match1=$_POST['pass1'];
  $val->match2=$_POST['pass2'];
  $val->validate_input();

  if (!$val->validated)
  {
    $error ="<p class='Error'>".$errors_in_form."</p>";
    //check if username already exists
  }elseif($GO_USERS->get_profile_by_username($_POST['username']))
  {
    $error = "<p class='Error'>".$error_username_exists."</p>";
    //check if email is already registered
  }elseif($GO_USERS->email_exists($_POST['email']))
  {
    $error =  "<p class='Error'>".$error_email_exists."</p>";
  }else
  {
    $birthday = date_to_db_date($_POST['birthday']);

    $email = ($_POST['email'] == '') ? $_POST['username'].'@'.$GO_CONFIG->inmail_host : $_POST['email'];

    //register the new user. function returns new user_id or -1 on failure.
    if ($new_user_id = $GO_USERS->add_user($username,$pass1, $first_name,
	  $middle_name, $last_name, $initials, '', $_POST['sex'],
	  $birthday, $email, '',
	  $home_phone, $fax, $cellular, $country, $state,
	  $city, $zip, $address, $company, '',
	  '', '', '', '',
	  '', '', '', '',
	  $_POST['language'], $theme, '', $visible
	  ))
    {

      //send email to the user with password
      $registration_mail_body = str_replace("%sex%", $sir_madam[$_POST['sex']], $registration_mail_body);
      $registration_mail_body = str_replace("%last_name%", $_POST['last_name'], $registration_mail_body);
      $registration_mail_body = str_replace("%middle_name%", $middle_name, $registration_mail_body);
      $registration_mail_body = str_replace("%first_name%", $_POST['first_name'], $registration_mail_body);
      $registration_mail_body = str_replace("%username%",$_POST['username'], $registration_mail_body);
      $registration_mail_body = str_replace("%password%",$_POST['pass1'], $registration_mail_body);
      $registration_mail_body .= "\n\n".$GO_CONFIG->full_url;
      sendmail($email,  $GO_CONFIG->webmaster_email, $GO_CONFIG->title, $registration_mail_subject, $registration_mail_body);

      //used for professional version
      //$user_count = $GO_USERS->get_users();
      //sendmail('info@intermesh.nl',  $GO_CONFIG->webmaster_email, $GO_CONFIG->title, 'User count for '.$GO_CONFIG->full_url.': '.$user_count, '');

			if(isset($user_groups))
			{
	      while($group_id = array_shift($user_groups))
	      {
		$GO_GROUPS->add_user_to_group($new_user_id, $group_id);
	      }
	    }

      //set module permissions
	    while($module_name = array_shift($modules_read))
			{
			  if($module = $GO_MODULES->get_module($module_name))
			  {
			    $GO_SECURITY->add_user_to_acl($new_user_id, $module['acl_read']);
			  }
			}
	
			while($module_name = array_shift($modules_write))
			{
			  if($module = $GO_MODULES->get_module($module_name))
			  {
			    $GO_SECURITY->add_user_to_acl($new_user_id, $module['acl_write']);
			  }
			}	

      //create Group-Office home directory
      $old_umask = umask(000);
      mkdir($GO_CONFIG->file_storage_path.$username, $GO_CONFIG->create_mode);
      umask($old_umask);

      //confirm registration to the user and exit the script so the form won't load
      echo $registration_success." <b>".$email."</b>";
      echo '<br /><br />';
      $button = new button($cmdLogin, "javascript:popup('".$GO_CONFIG->host."');");
      echo '</td></tr></table>';
      require_once($GO_THEME->theme_path."footer.inc");
      exit();
    }else
    {
      $error = "<p class=\"Error\">".$registration_failure."</p>";
    }
  }
}
if ($GO_USERS->max_users_reached())
{	
  echo '<h1>'.$max_user_limit.'</h1>'.$max_users_text;
  require_once($GO_THEME->theme_path."footer.inc");
  exit();
}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" name="register">
<?php if (isset($error)) echo $error; ?>
<table border="0">
<tr>
	<td valign="top">	
	<table border="0" cellpadding="0" cellspacing="3">
	<tr>
	<td align="right" nowrap>
	<?php echo $strCompany; ?>:&nbsp;
	</td>
	<td>
	<input type="text" class="textbox"  name="company" size="40" value="<?php if(isset($_POST['company'])) echo $_POST['company']; ?>" maxlength="50">
	</td>
	</tr>
	<?php
	if (isset($val->error["first_name"]))
	{
	?>
	<tr>
		<td class="Error" colspan="2">
			<?php echo $val->error["first_name"]; ?>
		</td>
	</tr>
	<?php
	}
	?>
	<tr>
	<td align="right" nowrap>
	<?php echo $strFirstName; ?>*:&nbsp;
	</td>
	<td>
	<input type="text" class="textbox"  name="first_name" size="40" value="<?php if(isset($_POST['first_name'])) echo $_POST['first_name']; ?>" maxlength="50">
	</td>
	</tr>
	<tr>
	<td align="right" nowrap>
	<?php echo $strMiddleName; ?>:&nbsp;
	</td>
	<td>
	<input type="text" class="textbox"  name="middle_name" size="40" value="<?php if(isset($_POST['middle_name'])) echo $_POST['middle_name']; ?>" maxlength="50">
	</td>
	</tr>
	<?php
	if (isset($val->error["last_name"]))
	{
	?>
	<tr>
		<td class="Error" colspan="2">
			<?php echo $val->error["last_name"]; ?>
		</td>
	</tr>
	<?php
	}
	?>
	<tr>
	<td align="right" nowrap>
	<?php echo $strLastName; ?>*:&nbsp;
	</td>
	<td>
	<input type="text" class="textbox"  name="last_name" size="40" value="<?php if(isset($_POST['last_name'])) echo $_POST['last_name']; ?>" maxlength="50">
	</td>
	</tr>
	<tr heigth="25">
	<td align="right" nowrap><?php echo $strInitials; ?>:&nbsp;</td>
	<td width="100%"><input type="text" class="textbox"  name="initials" size="40" maxlength="50" value="<?php if(isset($_REQUEST['initials'])) echo $_REQUEST['initials']; ?>"></td>
	</tr>
	<tr>
	<td align="right" nowrap><?php echo $strSex; ?>:</td>
	<td>
	<?php
	$sex = isset($_REQUEST['sex']) ? $_REQUEST['sex'] : 'M';
	$radiolist = new radio_list('sex', $sex);
	$radiolist->add_option('M', $strSexes['M']);
	echo '&nbsp;';
	$radiolist->add_option('F', $strSexes['F']);
	?>
	</td>
	</tr>
	<tr>
	<td align="right" nowrap><?php echo $strBirthday; ?>:</td>
	<td>
	<?php
	$birthday = isset($_REQUEST['birthday']) ? $_REQUEST['birthday'] : '';
	$datepicker->print_date_picker('birthday', $GO_CONFIG->date_formats[0], $birthday);
	?>
	</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<?php
	if (isset($val->error["address"]))
	{
	?>
	<tr>
		<td class="Error" colspan="2">
			<?php echo $val->error["address"]; ?>
		</td>
	</tr>
	<?php
	}
	?>
	<tr>
	<td align="right" nowrap>
	<?php echo $strAddress; ?>*:&nbsp;
	</td>
	<td>
	<input type="text" class="textbox"  name="address" size="40" value="<?php  if(isset($_POST['address'])) echo $_POST['address']; ?>" maxlength="100">
	</td>
	</tr>
	<?php
	if (isset($val->error["zip"]))
	{
	?>
	<tr>
		<td class="Error" colspan="2">
			<?php echo $val->error["zip"]; ?>
		</td>
	</tr>
	<?php
	}
	?>
	<tr>
	<td align="right" nowrap>
	<?php echo $strZip; ?>*:&nbsp;
	</td>
	<td>
	<input type="text" class="textbox"  name="zip" size="40" value="<?php if(isset($_POST['zip'])) echo $_POST['zip']; ?>" maxlength="20">
	</td>
	</tr>
	<?php
	if (isset($val->error["city"]))
	{
	?>
	<tr>
		<td class="Error" colspan="2">
			<?php echo $val->error["city"]; ?>
		</td>
	</tr>
	<?php
	}
	?>
	<tr>
	<td align="right" nowrap>
	<?php echo $strCity; ?>*:&nbsp;
	</td>
	<td>
	<input type="text" class="textbox"  name="city" size="40" value="<?php if(isset($_POST['city'])) echo $_POST['city']; ?>" maxlength="50">
	</td>
	</tr>
	<?php
	if (isset($val->error["state"]))
	{
	?>
	<tr>
		<td class="Error" colspan="2">
			<?php echo $val->error["state"]; ?>
		</td>
	</tr>
	<?php
	}
	?>
	<tr>
	<td align="right" nowrap>
	<?php echo $strState; ?>*:&nbsp;
	</td>
	<td>
	<input type="text" class="textbox"  name="state" size="40" value="<?php if(isset($_POST['state'])) echo $_POST['state']; ?>" maxlength="50">
	</td>
	</tr>
	<?php
	if (isset($val->error["country"]))
	{
	?>
	<tr>
		<td class="Error" colspan="2">
			<?php echo $val->error["country"]; ?>
		</td>
	</tr>
	<?php
	}
	?>
	<tr>
	<td align="right" nowrap>
	<?php echo $strCountry; ?>:&nbsp;
	</td>
	<td>
	<input type="text" class="textbox"  name="country" size="40" value="<?php if(isset($_POST['country'])) echo $_POST['country']; ?>" maxlength="50">
	</td>
	</tr>
	</table>
</td>
<td valign="top">
	<table border="0" cellpadding="0" cellspacing="3">
	<tr>
	<td align="right" nowrap>
	<?php echo $strPhone; ?>:&nbsp;
	</td>
	<td>
	<input type="text" class="textbox"  name="home_phone" size="40" value="<?php if(isset($_POST['home_phone'])) echo $_POST['home_phone']; ?>" maxlength="20">
	</td>
	</tr>
	<tr>
	<td align="right" nowrap>
	<?php echo $strCellular; ?>:&nbsp;
	</td>
	<td>
	<input type="text" class="textbox"  name="cellular" size="40" value="<?php if(isset($_POST['cellular'])) echo $_POST['cellular']; ?>" maxlength="20">
	</td>
	</tr>
	<tr>
	<td align="right" nowrap>
	<?php echo $strFax; ?>:&nbsp;
	</td>
	<td>
	<input type="text" class="textbox"  name="fax" size="40" value="<?php if(isset($_POST['fax'])) echo $_POST['fax']; ?>" maxlength="20">
	</td>
	</tr>
	<?php
	if (isset($val->error["email"]))
	{
	?>
	<tr>
		<td colspan="2" class="Error">
			<?php echo $val->error["email"]; ?>
		</td>
	</tr>
	<?php } ?>
	<tr>
	<td align="right" nowrap valign="top">
	<?php echo $strEmail; ?>*:&nbsp;
	</td>
	<td class="small">
	<input type="text" class="textbox"  name="email" size="40" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>" maxlength="75"><br />
	</td>
	</tr>
	<tr>
	
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
	<?php
	if (isset($val->error["username"]))
	{
	?>
	<tr>
	        <td colspan="2" class="Error">
	                <?php echo $val->error["username"]; ?>
	        </td>
	</tr>
	<?php } ?>
	
	<tr>
		<td align="right" nowrap>
	        <?php echo $strUsername; ?>*:&nbsp;
	      	</td>
	      	<td width="100%">
	        <input type="text" class="textbox"  name="username" size="30" value="<?php if(isset($username)) echo stripslashes($username); ?>" maxlength="20">
	       </td>
	</tr>
	<?php
	if (isset($val->error["pass1"]))
	{
	?>
	<tr>
	        <td colspan="2" class="Error">
	                <?php echo $val->error["pass1"]; ?>
	        </td>
	</tr>
	<?php } ?>
	
	<tr>
		<td align="right" nowrap>
	        <?php echo $strPassword; ?>*:&nbsp;
	      	</td>
	      	<td>
	        <input type=password class="textbox" name=pass1 size="30" maxlength="20" value="<?php if(isset($pass1)) echo stripslashes($pass1); ?>">
	        </td>
	</tr>
	<?php
	if (isset($val->error["pass2"]))
	{
	?>
	<tr>
	        <td colspan="2" class="Error">
	                <?php echo $val->error["pass2"]; ?>
	        </td>
	</tr>
	<?php } ?>
	<tr>
		<td align="right" nowrap>
	        <?php echo $strPasswordConfirm; ?>*:&nbsp;
	      	</td>
	      	<td>
	        <input type="password" class="textbox" name="pass2" size="30" maxlength="20" value="<?php if(isset($pass2)) echo stripslashes($pass2); ?>">
	        </td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td nowrap align="right">
			<?php echo $reg_language; ?>:&nbsp;
		</td>
		<td>
			<?php
			$dropbox= new dropbox();
			$languages = $GO_LANGUAGE->get_languages();
			while($language = array_shift($languages))
			{
				$dropbox->add_value($language['code'], $language['description']);
			}
			$dropbox->print_dropbox("language", $GO_CONFIG->language);
			?>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
<td colspan="2">
<?php	
$button = new button($cmdOk, 'javascript:document.forms[0].submit()');	
echo '&nbsp;&nbsp;';
$button = new button($cmdReset, 'javascript:document.forms[0].reset()');
?>
</td>
</tr>
</table>
<?php
require_once($GO_THEME->theme_path."footer.inc");
?>
