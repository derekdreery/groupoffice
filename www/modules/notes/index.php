<?php
/**
 * @copyright Copyright Intermesh 2007
 * @author Merijn Schering <mschering@intermesh.nl>
 * 
 * This file is part of Group-Office.
 * 
 * Group-Office is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 * 
 * See file /LICENSE.GPL
 */

require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('notes');
load_basic_controls();
require_once($GO_LANGUAGE->get_language_file('notes'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?php echo $GO_CONFIG->title.' - '.$lang_modules['notes']; ?></title>
<?php
require($GO_CONFIG->root_path.'default_head.inc');
require($GO_CONFIG->root_path.'default_scripts.inc');
?>
<script type="text/javascript" src="language/<?php echo $no_js_lang; ?>"></script>
<script type="text/javascript" src="note.js"></script>
<script type="text/javascript" src="notes.js"></script>
<script type="text/javascript" src="../../javascript/windows/linksDialog.js"></script>
<script type="text/javascript" src="../../javascript/panels/linksPanel.js"></script>
</head>
<body>
<div id="notedialog" style="visibility:hidden">
	<div class="x-window-header"><?php echo $no_note; ?></div>
</div>
<?php
if(isset($_REQUEST['note_id']))
{
	echo '<script type="text/javascript">Ext.onReady(function(){Note.showDialog('.$_REQUEST['note_id'].');});</script>';
}
?>
</body>
</html>