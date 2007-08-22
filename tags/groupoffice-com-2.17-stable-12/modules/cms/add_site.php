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


//load Group-Office
require_once("../../Group-Office.php");

//authenticate the user
$GO_SECURITY->authenticate();

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('cms');

//load the CMS module class library
require_once($GO_MODULES->class_path.'cms.class.inc');
$cms = new cms();

//get the language file
require_once($GO_LANGUAGE->get_language_file('cms'));

$site_id = isset($_REQUEST['site_id']) ? $_REQUEST['site_id'] : 0;
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

$search_word_id = isset($_REQUEST['search_word_id']) ? $_REQUEST['search_word_id'] : 0;

$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : 'index.php';
$sites = isset($_POST['sites']) ? '&sites='.$_POST['sites'] : '';
$link_back = $_SERVER['PHP_SELF'].'?site_id='.$site_id.$sites.'&return_to='.urlencode($return_to);

$root_publish_path = $GO_CONFIG->get_setting('cms_publish_path');

if($task == 'save_site')
{
	$domain = $cms->prepare_domain(smart_addslashes(trim($_POST['domain'])));
	$webmaster = smart_addslashes(trim($_POST['webmaster']));
	$allow_properties = isset($_POST['allow_properties']) ? '1' : '0';
	$multilingual= isset($_POST['multilingual']) ? '1' : '0';
	$template_id = isset($_POST['template_id']) ? $_POST['template_id'] : '0';
	$name = isset($_POST['name']) ? smart_addslashes($_POST['name']) : '';
	$title = isset($_POST['title']) ? smart_addslashes($_POST['title']) : '';
	$keywords= isset($_POST['keywords']) ? smart_addslashes($_POST['keywords']) : '';
	$description= isset($_POST['description']) ? smart_addslashes($_POST['description']) : '';	
	$language_code = isset($_POST['language_code']) ? $_POST['language_code'] : $GO_LANGUAGE->language['code'];
	$sort_order= isset($_POST['sort_order']) ? smart_addslashes($_POST['sort_order']) : '';	
	
	if ($domain == '' || $name == '' || $title == '' || $webmaster == '')
	{
		$feedback= '<p class="Error">'.$error_missing_field.'</p>';
	}else
	{			
		if (!$cms->get_site_by_domain($domain))
		{
			if (isset($_POST['secure']))
			{
				if (!$acl_read = $GO_SECURITY->get_new_acl('cms read: '.$domain))
				{
					die($strAclError);
				}
			}else
			{
				$acl_read = 0;
			}

			if (!$acl_write = $GO_SECURITY->get_new_acl('cms write: '.$domain))
			{
				$GO_SECURITY->delete_acl($acl_read);
				die($strAclError);
			}

			if (!$GO_SECURITY->add_user_to_acl($GO_SECURITY->user_id, $acl_write))
			{
				$GO_SECURITY->delete_acl($acl_read);
				$GO_SECURITY->delete_acl($acl_write);
				die($strAclError);
			}

			if($site_id = $cms->add_site(
						$GO_SECURITY->user_id,
						$domain,
						$webmaster,
						$allow_properties, 
						$multilingual,
						$name,
						$title,
						$description,
						$keywords,												
						$acl_read,
						$acl_write,
						$_POST['template_id'],
						$language_code))
			{
				header('Location: '.$return_to);
				exit();

			}else
			{
				$GO_SECURITY->delete_acl($acl_read);
				$GO_SECURITY->delete_acl($acl_write);
				$feedback = '<p class="Error">'.$strSaveError.'</p>';

			}
		}else
		{
			$feedback = '<p class="Error">'.$cms_site_exists.'</p>';
		}
	}
}

//set the page title for the header file
$page_title = $lang_modules['cms'];

$domain = isset($_POST['domain']) ? smart_stripslashes($_POST['domain']) : '';
$webmaster = isset($_POST['webmaster']) ? smart_stripslashes($_POST['webmaster']) : '';
$secure_check = isset($_POST['secure']) ? true : false;
$allow_properties_check = isset($_POST['allow_properties'])  ? true : false;
$multilingual_check = isset($_POST['multilingual'])  ? true : false;
$template_id = isset($_POST['template_id']) ? $_POST['template_id'] : '';		
$name = isset($_POST['name']) ? smart_stripslashes($_POST['name']) : '';
$title = isset($_POST['title']) ? smart_stripslashes($_POST['title']) : '';
$keywords= isset($_POST['keywords']) ? smart_stripslashes($_POST['keywords']) : '';
$description= isset($_POST['description']) ? smart_stripslashes($_POST['description']) : '';	
$sort_order= isset($_POST['sort_order']) ? smart_stripslashes($_POST['sort_order']) : '';
$language_code = isset($_POST['language_code']) ? $_POST['language_code'] : $GO_LANGUAGE->language['code'];
	
//require the header file. This will draw the logo's and the menu
require_once($GO_THEME->theme_path."header.inc");
echo '<form name="cms" method="post" action="'.$_SERVER['PHP_SELF'].'">';
echo '<input type="hidden" name="site_id" value="'.$site_id.'" />';
echo '<input type="hidden" name="return_to" value="'.htmlspecialchars($return_to).'" />';
$tabtable = new tabtable('properties', $cms_new_site, '100%', '400');
$tabtable->print_head(htmlspecialchars($return_to));

if ($cms->get_authorized_templates($GO_SECURITY->user_id) == 0)
{
	echo '<br />';
	echo $cms_no_themes;
	echo '<br /><br />';
	if($GO_MODULES->write_permission)
	{
		$button = new button($cmdOk, "javascript:document.location='".$GO_MODULES->url."index.php?tabindex=templates.inc';");
	}
}else
{
	if(isset($feedback)) echo $feedback;
	?>
		<input type="hidden" name="task" />
		<table border="0" cellpadding="4" cellspacing="0">
		<tr>
			<td>
			<?php echo $cms_domain; ?>*:
			</td>
		<td>
		<input type="text" class="textbox" name="domain" value="<?php echo htmlspecialchars($domain); ?>" maxlength="100" style="width: 250" />
		</td>
		</tr>	
		<tr>
			<td>
			<?php echo $strName; ?>*:
			</td>
			<td>
			<input type="text" class="textbox" name="name" value="<?php echo htmlspecialchars($name); ?>" maxlength="100" style="width: 250" />
			</td>
		</tr>
		<tr>
			<td>
			<?php echo $cms_webmaster; ?>*:
			</td>
			<td>
			<input type="text" class="textbox" name="webmaster" value="<?php echo htmlspecialchars($webmaster); ?>" maxlength="100" style="width: 250" />
			</td>
		</tr>
		<tr>
			<td nowrap>
			<?php echo $cms_language; ?>:
			</td>
			<td>
			<?php
			$dropbox= new dropbox();
			$languages = $GO_LANGUAGE->get_languages();
			while($language = array_shift($languages))
			{
				$dropbox->add_value($language['code'], $language['description']);
			}
			$dropbox->print_dropbox("language_code", $language_code);
			?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo $cms_theme; ?>:
			</td>
			<td>
			<?php
			$dropbox=new dropbox();
			while ($cms->next_record())
			{
				if ((isset($site) && $cms->f('id') == $site['template_id']) || 
					$GO_SECURITY->has_permission($GO_SECURITY->user_id, $cms->f('acl_read')) || 
					$GO_SECURITY->has_permission($GO_SECURITY->user_id, $cms->f('acl_write')))
				{
					$dropbox->add_value($cms->f('id'), $cms->f('name'));
				}
			}
			$dropbox->print_dropbox('template_id', $template_id);
			?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<?php
			$checkbox = new checkbox('secure', 'true', $cms_use_go_auth, $secure_check);
			echo '<br />';
			$checkbox = new checkbox('allow_properties', 'true', $cms_allow_properties, $allow_properties_check);
			echo '<br />';
			$checkbox = new checkbox('multilingual', 'true', $cms_multilingual, $multilingual_check);
			?>
			</td>
		</tr>	
		<tr>
			<td>
			<?php echo $cms_title; ?>*:
			</td>
			<td>
			<input type="text" class="textbox" name="title" value="<?php echo htmlspecialchars($title); ?>" maxlength="100" style="width: 250" />
			</td>
		</tr>
		<tr>
			<td valign="top">
			<?php echo $strDescription; ?>:
			</td>
			<td>
			<textarea class="textbox" name="description" style="width: 250" rows="5"><?php echo htmlspecialchars($description); ?></textarea>
			</td>
		</tr>
		<tr>
			<td valign="top">
			<?php echo $cms_keywords; ?>:
			</td>
			<td>
			<textarea class="textbox" name="keywords" style="width: 250" rows="5"><?php echo htmlspecialchars($keywords); ?></textarea>
			</td>
		</tr>

		<tr>
		<td colspan="2">
		<br />
		<?php
		$button = new button($cmdOk, "javascript:save_site()");
		echo $button->get_html();
		$button = new button($cmdCancel, "javascript:document.location='".htmlspecialchars($return_to)."';");
		echo $button->get_html();
		?>
		</td>
		</tr>
		</table>
		<script type="text/javascript">


	function save_site()
	{
		document.forms[0].task.value='save_site';
		document.forms[0].submit();
	}
	document.forms[0].domain.focus();
	</script>
		<?php

}
		
$tabtable->print_foot();
echo '</form>';
require_once($GO_THEME->theme_path."footer.inc");
