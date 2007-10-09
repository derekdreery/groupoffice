<?php
/**
 * @copyright Copyright Intermesh 2007
 * @version 1.0
 *
 * @author Merijn Schering <mschering@intermesh.nl>

   This file is part of Group-Office.

   Group-Office is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Group-Office is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Group-Office; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 * @package Users

 */

require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('notes');
load_basic_controls();
require_once($GO_LANGUAGE->get_language_file('notes'));

$post_action = isset($_REQUEST['post_action']) ? $_REQUEST['post_action'] : '';
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$link_back = (isset($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? htmlspecialchars($_REQUEST['link_back']) : $_SERVER['REQUEST_URI'];

?>
<html>
<head>
<title><?php echo $GO_CONFIG->title.' - '.$lang_modules['notes']; ?></title>
<?php
require($GO_CONFIG->root_path.'default_head.inc');
require($GO_CONFIG->root_path.'default_scripts.inc');
?>
<script type="text/javascript" src="<?php echo $no_js_lang; ?>"></script>
<script type="text/javascript" src="notes.js"></script>
<script type="text/javascript" src="../../controls/linksDialog.js"></script>
<script type="text/javascript" src="../../controls/linksPanel.js"></script>

</head>
<body>

<div id="notedialog">
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