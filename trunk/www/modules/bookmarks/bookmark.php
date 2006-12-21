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

require_once ("../../Group-Office.php");
$GO_SECURITY->authenticate();
require_once ($GO_LANGUAGE->get_language_file('bookmarks'));

$GO_MODULES->authenticate('bookmarks');

load_basic_controls();
load_control('tabtable');

require_once ($GO_MODULES->path.'classes/bookmarks.class.inc');
$bookmarks = new bookmarks();

$bookmark_id = isset ($_REQUEST['bookmark_id']) ? smart_addslashes($_REQUEST['bookmark_id']) : 0;

$task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';

switch ($task) {
	case 'save' :

		$URL = smart_addslashes(trim($_REQUEST['URL']));
		$name = smart_addslashes(trim($_REQUEST['name']));
		$invalid[] = "\"";
		$invalid[] = "&";
		$invalid[] = "?";

		if (!validate_input($name, $invalid)) {
			$feedback = "<p class=\"Error\">".$invalid_chars.": \" & ?</p>";
		} else {
			if ($URL != "" && $name != "") {
				/*if (!eregi('(^http[s]*:[/]+)(.*)', $URL))
				{
				  $URL= "http://".$URL;
				}*/

				$new_window = isset ($_REQUEST['new_window']) ? $_REQUEST['new_window'] : 0;

				if ($bookmark_id > 0) {
					if (!$bookmarks->update_bookmark($bookmark_id, $_POST['catagory_id'], $URL, $name, $new_window)) {
						$feedback = "<p class=\"Error\">".$strSaveError."</p>";
					} else {
						if ($_POST['close'] == 'true') {
							header('Location: '.$GO_MODULES->url);
							exit ();
						}
					}
				} else {

					$acl_read = $GO_SECURITY->get_new_acl('bookmarks');
					$acl_write = $GO_SECURITY->get_new_acl('bookmarks');
					if ($acl_read > 0 && $acl_write > 0) {
						if (!$bookmark_id = $bookmarks->add_bookmark($GO_SECURITY->user_id, $_POST['catagory_id'], $URL, $name, $new_window, $acl_read, $acl_write)) {
							$GO_SECURITY->delete_acl($acl_read);
							$GO_SECURITY->delete_acl($acl_write);
							$feedback = "<p class=\"Error\">".$strSaveError."</p>";
						} else {
							if (!isset ($_POST['private']) && $catagory = $bookmarks->get_catagory($_POST['catagory_id'])) {
								$GO_SECURITY->copy_acl($catagory['acl_write'], $acl_write);
								$GO_SECURITY->copy_acl($catagory['acl_read'], $acl_read);
							}

							if (!$GO_SECURITY->user_in_acl($GO_SECURITY->user_id, $acl_write)) {
								$GO_SECURITY->add_user_to_acl($GO_SECURITY->user_id, $acl_write);
							}

							if ($_POST['close'] == 'true') {
								header('Location: '.$GO_MODULES->url);
								exit ();
							}
						}
					}
				}
			} else {
				$feedback = "<p class=\"Error\">".$error_missing_field."</p>";
			}
		}
		break;
}
require_once ($GO_THEME->theme_path."header.inc");
?>
<form name="add" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="close" value="false" />
<input type="hidden" name="task" value="" />
<input type="hidden" value="<?php echo $bookmark_id; ?>" name="bookmark_id" />
  <?php

if ($bookmark_id > 0 && $bookmark = $bookmarks->get_bookmark($bookmark_id)) {
	$catagory_id = $bookmark['catagory_id'];
	$name = $bookmark['name'];
	$URL = $bookmark['URL'];
	$check = $bookmark['new_window'] == '1' ? true : false;

	$tabtable = new tabtable('bookmarks_tab', $name, '500', '400', '120', '', true);
	$tabtable->add_tab('properties', $strProperties);
	$tabtable->add_tab('read_permissions', $strReadRights);
	$tabtable->add_tab('write_permissions', $strWriteRights);

} else {
	$catagory_id = isset ($_REQUEST['catagory_id']) ? $_REQUEST['catagory_id'] : '0';
	$name = isset ($_POST['name']) ? $_POST['name'] : '';
	$URL = isset ($_POST['URL']) ? $_POST['URL'] : 'http://';
	$check = isset ($_POST['new_window']) ? true : false;
	$private = isset ($_POST['private']) ? true : false;

	$tabtable = new tabtable('bookmarks_tab', $bm_add_bookmark, '100%', '', '120', '', true);
}

$tabtable->print_head($GO_MODULES->url.'index.php');

switch ($tabtable->get_active_tab_id()) {
	case 'read_permissions' :
		print_acl($bookmark["acl_read"]);
		echo '<br />';
		$button = new button($cmdClose, "javascript:document.location='".$GO_MODULES->url."';");
		echo $button->get_html();
		break;

	case 'write_permissions' :
		print_acl($bookmark["acl_write"]);
		echo '<br />';
		$button = new button($cmdClose, "javascript:document.location='".$GO_MODULES->url."';");
		echo $button->get_html();
		break;

	default :

		if (isset ($feedback))
			echo $feedback;
?>
      <table border="0" cellpadding="0" cellspacing="3">
      <?php

		if ($bookmarks->get_catagories($GO_SECURITY->user_id, true)) {
			load_control('dropbox');
			$dropbox = new dropbox();
			$dropbox->add_value('0', $bm_catagory_other);
			while ($bookmarks->next_record()) {
				$dropbox->add_value($bookmarks->f('id'), $bookmarks->f('name'));
			}

			echo '<tr><td>'.$bm_catagory.':</td><td>';
			$dropbox->print_dropbox('catagory_id', $catagory_id);
			echo '</td></tr>';
		} else {
			echo '<input type="hidden" name="catagory_id" value="'.$catagory_id.'" />';
		}
?>

	<tr>
	<td><?php echo $strName; ?>:</td>
	<td><input type="text" class="textbox" size="50" name="name" maxlength="50" value="<?php echo htmlspecialchars($name); ?>" /></td>
	</tr>
	<tr>
	<td>URL:</td>
	<td><input type="text" class="textbox" size="50" name="URL" maxlength="200" value="<?php echo htmlspecialchars($URL); ?>" /></td>
	</tr>
	<tr>
	<td colspan="2">
	<?php

		$checkbox = new checkbox('new_window','new_window', 'true', $bm_new_window, $check);
		echo $checkbox->get_html();
?>
	</td>
	</tr>
	<?php

		if ($bookmark_id == 0) {
			echo '<tr><td colspan="2">';
			$checkbox = new checkbox('private', 'private', 'true', $bm_private, $private);
			echo $checkbox->get_html();
			echo '</td></tr>';
		}
?>
	<tr>
	<td colspan="2">
	<br />
	<?php

		$button = new button($cmdOk, "javascript:document.forms[0].task.value='save';document.forms[0].close.value='true';document.forms[0].submit();");
		echo $button->get_html();
		$button = new button($cmdApply, "javascript:document.forms[0].task.value='save';document.forms[0].submit();");
		echo $button->get_html();
		$button = new button($cmdClose, "javascript:document.location='".$GO_MODULES->url."index.php'");
		echo $button->get_html();
?>
	</td>
	</tr>
	</table>
	<?php
		break;
}
?>
</form>
<?php
$tabtable->print_foot();
require_once ($GO_THEME->theme_path."footer.inc");
