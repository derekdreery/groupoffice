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
$GO_SECURITY->authenticate(true);

load_basic_controls();
load_control('tabtable');

$module = $GO_MODULES->get_module($_REQUEST['module_id']);

$page_title=$module['id'];

require_once($GO_THEME->theme_path."header.inc");
?>
<form method="post" name="permissions" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="close" value="false" />
<input type="hidden" name="module_id" value="<?php echo $_REQUEST['module_id']; ?>" />
<?php

$tabtable = new tabtable('module_tab', $module['id'],'100%','','120','',true);
$tabtable->add_tab('read', $strReadRights);
$tabtable->add_tab('write', $strWriteRights);

$tabtable->print_head();

switch($tabtable->get_active_tab_id())
{	
	case 'read':
		print_acl($module["acl_read"]);
	break;
	
	case 'write':
		print_acl($module["acl_write"]);
	break;
}
echo '<br /><br />';
$button = new button($cmdClose, 'javascript:window.close()');
echo $button->get_html();

$tabtable->print_foot();
?>
</form>
<?php
require_once($GO_THEME->theme_path."footer.inc");
?>
