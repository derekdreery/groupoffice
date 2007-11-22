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

//delete groups if requested
if($write_permission && isset($_POST['delete_group_id']) && $_POST['delete_group_id'] > 0)
{
	$ab->delete_group($_POST['delete_group_id']);
}

$id = isset($_REQUEST['edit_id']) ? $_REQUEST['edit_id'] : 0;

$form->add_html_element(new input('hidden', 'id', $id));
$form->add_html_element(new input('hidden', 'delete_group_id', '0'));

$menu = new button_menu();
$menu

?>
<a class="normal" href="group.php?addressbook_id=<?php echo $addressbook_id; ?>&return_to=<?php echo urlencode($link_back); ?>"><?php echo $cmdAdd; ?></a>
<br /><br />
<?php
echo '<table border="0" cellpadding="4" cellspacing="0">';
$count = $ab->get_groups($addressbook_id);

if ($count > 0)
{
	while ($ab->next_record())
	{
		echo '<tr><td>'.$ab->f('name').'</td>';
		if ($write_permission)
		{
			echo '<td><a href="group.php?group_id='.$ab->f("id").'&addressbook_id='.
						$addressbook_id.'&return_to='.urlencode($link_back).'" title="'.
						$strEdit.' \''.$ab->f("name").'\'"><img src="'.
						$GO_THEME->images['edit'].'" border="0" /></a></td>';

			echo "<td>&nbsp;<a href='javascript:confirm_delete(\"".$ab->f("id").
						"\",\"".rawurlencode($strDeletePrefix."'".
						htmlspecialchars($ab->f("name"))."'".$strDeleteSuffix).
						"\")' title=\"".$strDeleteItem." '".htmlspecialchars($ab->f("name")).
						"'\"><img src=\"".$GO_THEME->images['delete'].
						"\" border=\"0\"></a></td>\n";
		}
		echo '</tr>';
	}
}else
{
	echo "<tr><td>".$groups_no_groups."</td></tr>";
}
?>
</table>

<script type="text/javascript" language="javascript">
function invert_selection()
{
	for (var i=0;i<document.forms[0].elements.length;i++)
	{
		if(document.forms[0].elements[i].type == 'checkbox')
		{
			document.forms[0].elements[i].checked = !(document.forms[0].elements[i].checked);
		}
	}
}

function confirm_delete(group_id, message)
{
	if (confirm(unescape(message)))
	{
		document.forms[0].delete_group_id.value = group_id;
		document.forms[0].submit();
	}
}
</script>
