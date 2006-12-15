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

require_once("../Group-Office.php");
$GO_SECURITY->authenticate();
require_once($GO_LANGUAGE->get_fallback_base_language_file('groups'));
load_basic_controls();

$count = $GO_GROUPS->get_users_in_group($group_id);
$page_title= $count.' '.$groups_users_in1.' '.$name;
require_once($GO_THEME->theme_path."header.inc");
echo '<table border="0" cellpadding="10"><tr><td>';
echo '<table border="0" cellpadding="0" cellspacing="1">';
echo '<tr><td><h1>'.$page_title.'</h1></td></tr>';

if ($count>0)
{
        while ($GO_GROUPS->next_record())
        {
		echo "<tr height=\"18\">\n";
		if ( $GO_GROUPS->f('name') ) {
			echo "<td>".show_profile($GO_GROUPS->f("id"),$GO_GROUPS->f("name"))."&nbsp;</a></td>\n";
		} else {
			require_once($GO_CONFIG->class_path.'profiles.class.inc');
			$profiles = new profiles();
			if ($profile = $profiles->get_profile($GO_GROUPS->f('user_id'))) {
				echo "<td>".show_profile($GO_GROUPS->f('user_id'),$profile["name"])."&nbsp;</a></td>\n";
			}
		}
		echo "</tr>\n";
	}
}else
{
        echo "<tr><td colspan=\"99\">".$groups_no_users."</td></tr>";
}
echo '<tr><td><br />';
$button = new button($cmdClose, 'javascript:window.close()');
echo $button->get_html();
echo '</td></tr>';
echo "</table></td></tr></table>";
require_once($GO_THEME->theme_path."footer.inc");
?>
