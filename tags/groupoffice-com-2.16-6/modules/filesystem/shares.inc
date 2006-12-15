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

$tabtable = new tabtable('shares_tab', $fbShares, '100%', '');
$tabtable->print_head($_SERVER['PHP_SELF']."?path=".$urlencoded_path);

if(isset($feedback)) echo $feedback; 
?>

<table border="0" cellpadding="4" cellspacing="0">
<?php
if (isset($_REQUEST['delete_share']))
{
	$fs->delete_share(smart_stripslashes($_REQUEST['delete_share']));
}
$count = $fs->get_shares($GO_SECURITY->user_id);

while($fs->next_record())
{
	if (file_exists($fs->f('path')))
	{
		$display_path = '/'.str_replace($GO_CONFIG->file_storage_path,'',$fs->f('path'));
		echo '<tr><td><img width="16" height="16" border="0" src="'.$GO_THEME->images['folder'].'" /></td>';
		echo '<td><a href="'.$_SERVER['PHP_SELF'].'?task=properties&path='.urlencode($fs->f('path')).'">'.$display_path.'</a></td>';
		echo "<td><a href='javascript:confirm_action(\"".$_SERVER['PHP_SELF']."?task=shares&path=".urlencode($path)."&delete_share=".urlencode($fs->f('path'))."\",\"".rawurlencode($strDeletePrefix."'".$display_path."'".$strDeleteSuffix)."\")' title=\"".$strDeleteItem." '".$display_path."'\"><img src=\"".$GO_THEME->images['delete']."\" border=\"0\"></a></td></tr>\n";
	}else
	{
		$fs2 = new filesystem();
		$fs2->delete_share($fs->f('path'));
		$count--;
	}
}
$text = ($count == 1) ? $fbShare : $fbShares;
echo '<tr><td colspan="2"><i>'.$count.' '.$text.'</td></tr>';
?>
</table>
</td>
</tr>
<tr>
<td colspan="2">
<br />
<?php
$button = new button($cmdBack, "javascript:document.location='".$_SERVER['PHP_SELF']."?path=".$urlencoded_path."';");
echo $button->get_html();
?>
</td>
</tr>
</table>
<?php
$tabtable->print_foot();
?>
