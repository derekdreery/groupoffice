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
$GO_MODULES->authenticate('email');
load_basic_controls();
require_once($GO_LANGUAGE->get_language_file('email'));
require_once ($GO_MODULES->class_path."email.class.inc");
$email = new email();
$account = $email->get_account(0);
$mailbox = $email->get_folder($account['id'], 'INBOX');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
//$GO_THEME->load_module_theme('email');
echo $GO_THEME->get_stylesheet('email');
require($GO_CONFIG->root_path.'default_head.inc');
?>
<script type="text/javascript" src="language/en.js"></script>
<script type="text/javascript" src="email.js"></script>
<script type="text/javascript">

Ext.EventManager.onDocumentReady(
function(){
	email.init(<?php echo $account['id']; ?>, <?php echo $mailbox['id']; ?>, 'INBOX');
}, email, true);
</script>
</head>
<body>
<div id="north">
	<div id="emailtb"></div>
</div>
<div id="west">
	<div id="email-tree"></div>
</div>
<div id="inner-layout">
	<div id="email-grid"></div>
	<div id="preview" style="background-color:#c3daf9;height:100%"></div>
</div>
</body>
</html>
