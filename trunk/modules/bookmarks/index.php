<?php
/*
Copyright Intermesh 2004
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003
Version: 2.0 Release date: 12 March 2004

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

$columns = 3;

require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
require_once($GO_LANGUAGE->get_language_file('bookmarks'));
load_basic_controls();
load_control('tabtable');
load_control('dropbox');
$GO_MODULES->authenticate('bookmarks');
require_once($GO_MODULES->path.'classes/bookmarks.class.inc');


$bookmarks = new bookmarks();
$bookmarks2 = new bookmarks();

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

if (isset($_REQUEST['delete_catagory']))
{
	if ($catagory = $bookmarks->get_catagory($_REQUEST['delete_catagory']))
	{
		if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $catagory['acl_write']))
		{
			if ($bookmarks->delete_catagory($_REQUEST['delete_catagory']))
			{
				$GO_SECURITY->delete_acl($catagory['acl_write']);
				$GO_SECURITY->delete_acl($catagory['acl_read']);
			}
		}
	}
}
if (isset($_POST['bookmarks']))
{
	switch($task)
	{
    case 'delete':
			while($bookmark_id = array_shift($_POST['bookmarks']))
			{
				if ($bookmark = $bookmarks->get_bookmark($bookmark_id))
				{
					if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $bookmark['acl_write']))
					{
						if ($bookmarks->delete_bookmark($GO_SECURITY->user_id, $bookmark_id))
						{
							$GO_SECURITY->delete_acl($bookmark['acl_read']);
							$GO_SECURITY->delete_acl($bookmark['acl_write']);
						}
					}
				}
			}
		break;

		case 'move_bookmarks':
			while($bookmark_id = array_shift($_POST['bookmarks']))
			{
				$bookmarks->move_bookmark($bookmark_id, $_POST['move_to_catagory']);
			}
		break;
	}
}
require_once($GO_THEME->theme_path."header.inc");
?>
<form method="post" name="bookmarks_form" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="task" />

<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td class="ModuleIcons">
	<a class="small" href="bookmark.php"><img src="<?php echo $GO_THEME->images['bm_add_bookmark_big']; ?>" border="0" height="32" width="32" /><br /><?php echo $bm_add_bookmark; ?></a></td>
	</td>
	<td class="ModuleIcons">
	<?php
	echo '<a class="small" href="catagory.php"><img src="'.$GO_THEME->images['bm_catagories'].'" border="0" height="32" width="32" /><br />'.$bm_add_catagory.'</a></td>';
	?>
	<td class="ModuleIcons">
	<a class="small" href="javascript:confirm_delete()"><img src="<?php echo $GO_THEME->images['delete_big']; ?>" border="0" height="32" width="32" /><br /><?php echo $cmdDelete; ?></a></td>
	</td>
</table>

<table border="0" width="100%" cellspacing="15">
<?php
if ($bookmarks->get_catagories($GO_SECURITY->user_id, true))
{
	$dropbox = new dropbox();
	$dropbox->add_value('', $bm_move_to_catagory);
	$dropbox->add_value('0', $bm_catagory_other);
	while($bookmarks->next_record())
	{
		$dropbox->add_value($bookmarks->f('id'), $bookmarks->f('name'));
	}
	echo '<tr><td colspan="3">';
	$dropbox->print_dropbox('move_to_catagory', '', 'onchange="javascript:move_bookmarks()"');
	echo '</td></tr>';
}
?>
<?php
$catagory_count = $bookmarks->get_catagories($GO_SECURITY->user_id);
$column_count = 0;
while($bookmarks->next_record())
{
	$catagory_write = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $bookmarks->f('acl_write'));
	if ($column_count == 0)
	{
		echo '<tr>';
	}

	echo '<td valign="top" width="33%">';

	$title = $bookmarks->f('name');

	if ($catagory_write)
	{
			$title .= '&nbsp;<a href="bookmark.php?catagory_id='.
					$bookmarks->f('id').'"><img src="'.
					$GO_THEME->images['bm_add_bookmark'].'" border="0"></a>'.

					'<a class="normal" href="catagory.php?catagory_id='.
							$bookmarks->f("id").'"><img src="'.$GO_THEME->images['edit'].
							'" border="0"></a>'.

					"<a href='javascript:confirm_action(\"".
					$_SERVER['PHP_SELF']."?delete_catagory=".$bookmarks->f('id').
					"\",\"".rawurlencode($strDeletePrefix."'".addslashes($bookmarks->f('name')).
					"' ".$bm_and_all_contents." ".$strDeleteSuffix)."\")' title=\"".$strDeleteItem." '".
					htmlspecialchars($bookmarks->f('name'))."'\"><img src=\"".$GO_THEME->images['delete'].
					"\" border=\"0\"></a>";
		}

	$tabtable = new tabtable('tab_'.$bookmarks->f('id'), $title, '100%', '0', '120', '');
	$tabtable->print_head();

	if ($bookmark_count = $bookmarks2->get_bookmarks($GO_SECURITY->user_id, $bookmarks->f('id')))
	{
		echo '<table border="0" width="100%">';
		while($bookmarks2->next_record())
		{
			$bookmark_write = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $bookmarks2->f('acl_write'));
			$target = $bookmarks2->f('new_window') == '1' ? '_blank' : '_self';
			echo '<tr>';
			if ($bookmark_write)
			{
				echo	'<td><input type="checkbox" name="bookmarks[]" value="'.
							$bookmarks2->f('id') .'" id="'.
							htmlspecialchars($bookmarks2->f('name')).'" /></td>';
			}else
			{
				echo '<td>&nbsp;</td>';
			}
			echo '<td width="100%"><a target="'.$target.'" class="normal" href="'.
						htmlspecialchars($bookmarks2->f("URL")).'">'.
						htmlspecialchars($bookmarks2->f("name")).'</a></td>';

			if ($bookmark_write)
			{
				echo '<td width="16"><a class="normal" href="bookmark.php?bookmark_id='.
							$bookmarks2->f("id").'"><img src="'.$GO_THEME->images['edit'].
							'" border="0"></a></td>';
			}else
			{
				echo '<td>&nbsp;</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
	}else
	{
		echo $bm_no_bookmarks_in_catagory;
	}
	$tabtable->print_foot();

	echo '</td>';

	$column_count++;

	if ($column_count == $columns)
	{
		echo '</tr>';
		$column_count = 0;
	}
}
//catgory 0
if ($column_count == '0')
{
	echo '<tr>';
}
echo '<td valign="top">';

if ($catagory_count > 0)
{
	$title = $bm_catagory_other;

	if ($catagory_write)
	{
		$title .= '&nbsp;<a href="bookmark.php?catagory_id='.
		$bookmarks->f('id').'"><img src="'.
		$GO_THEME->images['bm_add_bookmark'].'" border="0"></a>';
	}


	$tabtable = new tabtable('tab_0', $title, '100%', '', '120', '');
}else
{
	$tabtable = new tabtable('tab_0', $lang_modules['bookmarks'], '100%', '', '120', '');

}
$tabtable->print_head();

if ($bookmark_count = $bookmarks->get_bookmarks($GO_SECURITY->user_id, 0))
{
	echo '<table border="0" width="100%">';
	while($bookmarks->next_record())
	{
		$bookmark_write = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $bookmarks->f('acl_write'));

		$target = $bookmarks->f('new_window') == '1' ? '_blank' : '_self';
		echo '<tr>';
		if ($bookmark_write)
		{
			echo	'<td><input type="checkbox" name="bookmarks[]" value="'.$bookmarks->f('id') .'" id="'.htmlspecialchars($bookmarks->f('name')).'" /></td>';
		}else
		{
			echo '<td>&nbsp;</td>';
		}
		echo '<td width="100%"><a target="'.$target.'" class="normal" href="'.$bookmarks->f("URL").'">'.htmlspecialchars($bookmarks->f("name")).'</a></td>';

		if ($bookmark_write)
		{
			echo '<td width="16"><a class="normal" href="bookmark.php?bookmark_id='.$bookmarks->f("id").'"><img src="'.$GO_THEME->images['edit'].'" border="0"></a></td>';
		}else
		{
			echo '<td>&nbsp;</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
}else
{
	if ($catagory_count > 0)
	{
		echo $bm_no_bookmarks_in_catagory;
	}else
	{
		echo $bm_no_bookmarks;
	}
}
$tabtable->print_foot();
echo '</td>';

$column_count++;

for ($i=$column_count;$i<$columns;$i++)
{
	echo '<td>&nbsp;</td>';
}
echo '</tr>';
?>
</table>
</form>
<script type="text/javascript">

function move_bookmarks()
{
	document.forms[0].task.value='move_bookmarks';
	document.forms[0].submit();
}

function confirm_delete()
{
	var count = 0;
	var name = new String;
	for (var i=0;i<document.forms[0].elements.length;i++)
	{
		if(document.forms[0].elements[i].type == 'checkbox' && document.forms[0].elements[i].name != 'dummy')
		{
			if (document.forms[0].elements[i].checked == true)
			{
				count++;
				name = document.forms[0].elements[i].id;
			}
		}
	}
	switch (count)
	{
		case 0:
			alert("<?php echo $bm_no_select; ?>");
		break;

		case 1:
			if (confirm("<?php echo $strDeletePrefix; ?> '"+name+"' <?php echo $strDeleteSuffix; ?>"))
			{
				document.forms[0].task.value="delete";
				document.forms[0].submit();
			}
		break;

		default:
			if (confirm("<?php echo $strDeletePrefix.$strThis; ?> "+count+" <?php echo $bm_bookmarks.$strDeleteSuffix; ?>"))
			{
				document.forms[0].task.value="delete";
				document.forms[0].submit();
			}
		break;
	}
}
</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
?>
