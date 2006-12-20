<?php
/*
Copyright Intermesh 2004
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 12 March 2004

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
require_once($GO_LANGUAGE->get_language_file('bookmarks'));

$GO_MODULES->authenticate('bookmarks');

load_basic_controls();
load_control('tabtable');

require_once($GO_MODULES->path.'classes/bookmarks.class.inc');
$bookmarks = new bookmarks();

//get the language file
require_once($GO_LANGUAGE->get_language_file('bookmarks'));

//where did we come from?
$return_to = $GO_MODULES->url;

//define task
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$catagory_id = isset($_REQUEST['catagory_id']) ? $_REQUEST['catagory_id'] : 0;

//create a tab window
$tabtable = new tabtable('catagories_tab', $strProperties, '100%', '', '120', '', true);

//save catagory
switch ($task)
{
	case 'copy_read_permissions':
		if ($catagory = $bookmarks->get_catagory($catagory_id))
		{
			$bookmarks->get_user_bookmarks($GO_SECURITY->user_id, $catagory_id);
			while ($bookmarks->next_record())
			{
				$GO_SECURITY->copy_acl($catagory['acl_read'], $bookmarks->f('acl_read'));
			}
		}
	break;

	case 'copy_write_permissions':
		if ($catagory = $bookmarks->get_catagory($catagory_id))
		{
			$bookmarks->get_user_bookmarks($GO_SECURITY->user_id, $catagory_id);
			while ($bookmarks->next_record())
			{
				$GO_SECURITY->copy_acl($catagory['acl_write'], $bookmarks->f('acl_write'));
			}
		}
	break;

	case 'save':
		$name = smart_addslashes(trim($_POST['name']));

		if ($name == '')
		{
			$feedback = '<p class="Error">'.$error_missing_field.'</p>';
		}else
		{
			if ($_POST['catagory_id'] > 0)
			{
				if (!$bookmarks->update_catagory($_POST['catagory_id'], $name))
				{
					$feedback = '<p class="Error">'.$strSaveError.'</p>';
				}elseif($_POST['close'] == 'true')
				{
					header('Location: '.$return_to);
					exit();
				}
			}else
			{
				$acl_read = $GO_SECURITY->get_new_acl('catagories');
				$acl_write = $GO_SECURITY->get_new_acl('catagories');

				if (!$catagory_id = $bookmarks->add_catagory($GO_SECURITY->user_id, $name, $acl_read, $acl_write))
				{
					$feedback = '<p class="Error">'.$strSaveError.'</p>';
				}else
				{
					$GO_SECURITY->add_user_to_acl($GO_SECURITY->user_id, $acl_write);
					if (!isset($_POST['private']))
					{
						$GO_SECURITY->add_group_to_acl($GO_CONFIG->group_everyone, $acl_write);
					}

					if($_POST['close'] == 'true')
					{
						header('Location: '.$return_to);
						exit();
					}
				}
			}
		}
	break;
}

//get catagory
if ($catagory_id > 0)
{
	$catagory = $bookmarks->get_catagory($catagory_id);
	$tabtable->add_tab('properties', $strProperties);
	$tabtable->add_tab('read_permissions', $strReadRights);
	$tabtable->add_tab('write_permissions', $strWriteRights);

	$write_perm = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $catagory['acl_write']);
	$read_perm = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $catagory['acl_read']);

	if (!$write_perm && !$read_perm)
	{
		header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
		exit();
	}

}else
{
	$write_perm = true;
	$catagory['name'] = isset($_POST['name']) ? $_POST['name'] : '';
	$private = isset($_POST['private']) ? true : false;
}

//require the header file
require_once($GO_THEME->theme_path.'header.inc');

?>
<script type="text/javascript">
function copy_read_permissions()
{
	document.forms[0].task.value='copy_read_permissions';
	document.forms[0].submit();
}

function copy_write_permissions()
{
	document.forms[0].task.value='copy_write_permissions';
	document.forms[0].submit();
}
</script>
<form name="catagory" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" />
<input type="hidden" name="catagory_id" value="<?php echo $catagory_id; ?>" />
<input type="hidden" name="return_to" value="<?php echo $return_to; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="close" value="false" />
<?php
$tabtable->print_head($return_to);
echo '<br />';
switch($tabtable->get_active_tab_id())
{
	case 'read_permissions':
		if ($catagory['user_id'] == $GO_SECURITY->user_id)
		{
			echo '<a class="normal" href="javascript:copy_read_permissions()">'.$bm_copy_read_permissions.'</a><br /><br />';
		}
		print_acl($catagory["acl_read"]);
		echo '<br />';
		$button = new button($cmdClose, "javascript:document.location='".$return_to."';");
	break;

	case 'write_permissions':
		if ($catagory['user_id'] == $GO_SECURITY->user_id)
		{
			echo '<a class="normal" href="javascript:copy_write_permissions()">'.$bm_copy_write_permissions.'</a><br /><br />';
		}
		print_acl($catagory["acl_write"]);
		echo '<br />';
		$button = new button($cmdClose, "javascript:document.location='".$return_to."';");
	break;

	default:
	?>

	<table border="0">
	<?php
	if (isset($feedback))
	{
		echo '<tr><td colspan="2">'.$feedback.'</td></tr>';
	}
	?>
	<tr>
		<td><?php echo $strName; ?>:</td>
		<td>

		<?php
		$disabled = $write_perm ? '' : 'disabled';
		?>

		<input type="text" class="textbox" size="40" name="name" value="<?php echo htmlspecialchars($catagory['name']); ?>" <?php echo $disabled; ?> />

		</td>
	</tr>
	<?php
	if ($catagory_id == 0)
	{
		echo '<tr><td colspan="2">';
		$checkbox = new checkbox('private', 'private','true', $bm_private, $private);
		echo $checkbox->get_html();
		echo '</td></tr>';
	}
	?>
	<tr>
		<td colspan="2"><br />
		<?php
		if ($write_perm)
		{
			$button = new button($cmdOk, "javascript:document.forms[0].task.value='save';document.forms[0].close.value='true';document.forms[0].submit();");
			echo $button->get_html();
			$button = new button($cmdApply, "javascript:document.forms[0].task.value='save';document.forms[0].submit();");
			echo $button->get_html();
		}
		$button = new button($cmdClose, "javascript:document.location='".$return_to."';");
		echo $button->get_html();
		?>
		</td>
	</tr>
	</table>

	<?php
	break;
}

$tabtable->print_foot();
echo '</form>';
//require the footer file
require_once($GO_THEME->theme_path.'footer.inc');
