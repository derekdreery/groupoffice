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
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('users');
load_basic_controls();
require_once($GO_LANGUAGE->get_language_file('users'));
?>
<html>
<head>
<title><?php echo $GO_CONFIG->title.' - '.$lang_modules['users']; ?></title>
<?php
require($GO_CONFIG->root_path.'default_head.inc');
$GO_THEME->load_module_theme('users');
echo $GO_THEME->get_stylesheet('users');
?>
<script type="text/javascript" src="language/en.js"></script>
<script type="text/javascript" src="users.js"></script>
<script type="text/javascript" src="../../links.js"></script>
</head>
<body>
<base href="<?php echo $GO_MODULES->modules['users']['url']; ?>">

<div id="center">
	<div id="toolbar"></div>
	<div id="grid"></div>
</div>

<div id="userdialog">
	<div class="x-dlg-hd"><?php echo $strUser; ?></div>	
	    <div id="box-bd" class="x-dlg-bd">	   
		    <div id="properties" class="x-dlg-tab">
		    <div id="toolbar"></div>
			<div id="profileContent"></div>
			<div id="links_tab" class="x-dlg-tab">
				<div id="linkstoolbar"></div>
				<div id="links_grid_div"></div>
			</div>
			<div id="access" class="x-dlg-tab"></div>
			<div id="lookandfeel" class="x-dlg-tab"></div>
			<div id="regional" class="x-dlg-tab"></div>
	    </div>
	</div>
</div>



<?php
if(isset($_REQUEST['user_id']))
{
	echo 'Ext.onReady(function(){user.showDialog('.$_REQUEST['user_id'].');});';
}

?>
</body>
</html>
