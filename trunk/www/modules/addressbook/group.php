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

require_once("../../Group-Office.php");
$post_action = isset($post_action) ? $post_action : '';

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('addressbook');
require_once($GO_LANGUAGE->get_language_file('addressbook'));

//load contact management class
require_once($GO_MODULES->class_path."addressbook.class.inc");
$ab = new addressbook();

$addressbook_id = isset($_REQUEST['addressbook_id']) ? $_REQUEST['addressbook_id'] : 0;
$group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] : 0;
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

$return_to = (isset($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];

if ($addressbook_id > 0 && $addressbook = $ab->get_addressbook($addressbook_id))
{
	if (!$write_permission = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_write']))
	{
		$read_permission = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_read']);
	}
}

if (!$write_permission && !$read_permission)
{
	header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
	exit();
}

if ($task == 'save_group')
{
	$name = smart_addslashes(trim($_POST['name']));
	if ($name == '')
	{
		$feedback = "<p class=\"Error\">".$error_missing_field."</p>";
	}else
	{
		$existing_group = $ab->get_group_by_name($addressbook_id, $name);
		if ($group_id > 0)
		{
			if (!$existing_group)
			{
				$ab->change_group_name($group_id, $name);
				header('Location: '.$return_to);
				exit();
			}else
			{
				if($existing_group['id'] != $group_id)
				{
					$feedback = "<p class=\"Error\">".$ab_group_exists."</p>";
				}
			}

		}else
		{
			if ($existing_group)
			{
				$feedback = "<p class=\"Error\">".$ab_group_exists."</p>";
			}elseif(!$ab->add_group($_POST['addressbook_id'], $name))
			{
				$feedback = "<p class=\"Error\">".$strSaveError."</p>";
			}else
			{
				header('Location: '.$return_to);
				exit();
			}
		}
	}
}

if ($group_id > 0 && $group = $ab->get_group($group_id))
{
	$name = $group['name'];
	//create a tabbed window
	$tabtable = new tabtable('group', $contacts_group, '400', '120', '120', '', true);
}else
{
	$tabtable = new tabtable('group', $ab_new_group, '400', '120', '120', '', true);
	$name = isset($_REQUEST['name']) ? smart_strip($_REQUEST['name']) : '';
}
require_once($GO_THEME->theme_path."header.inc");
$tabtable->print_head();
?>
<form name="group_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="addressbook_id" value="<?php echo $_REQUEST['addressbook_id']; ?>" />
<input type="hidden" name="group_id" value="<?php echo $_REQUEST['group_id']; ?>" />
<input type="hidden" name="return_to" value="<?php echo $return_to; ?>" />
<input type="hidden" name="task" value="save_group" />

<table border="0" cellspacing="0" cellpadding="4">
<?php if (isset($feedback)) echo '<tr><td colspan="2">'.$feedback.'</td></tr>'; ?>
<tr>
	<td>
	<?php echo $strName; ?>:
	</td>
	<td>
	<?php $name = isset($name) ? htmlspecialchars(stripslashes($name)) : ''; ?>
	<input type="text" class="textbox" name="name" maxlength="50" size="30" value="<?php echo $name; ?>" />
	</td>
</tr>
<tr height="25">
	<td colspan="2">
	<br />
	<?php
	$button = new button($cmdOk, 'javascript:document.forms[0].submit()');
	echo $button->get_html();
	$button = new button($cmdCancel,"javascript:document.location='".$return_to."';");
	echo $button->get_html();
	?>
	</td>
</tr>
</table>

<script type="text/javascript">
document.forms[0].name.focus();
</script>
<?php
$tabtable->print_foot();
require_once($GO_THEME->theme_path."footer.inc");
?>
