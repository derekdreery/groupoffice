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
?>
<html>
<head>
<title><?php echo $GO_CONFIG->title.' - '.$lang_modules['email']; ?></title>
<?php
require($GO_CONFIG->root_path.'default_head.inc');
?>
<script type="text/javascript" src="language/en.js"></script>
<script type="text/javascript" src="email.js"></script>
</head>
<body>

<div id="west">
<div id="email-tree"></div>
</div>
<div id="center">
	<div id="emailtb"></div>
	<div id="email-grid"></div>
</div>
<div id="east" style="background-color:#c3daf9;height:100%"></div>
</body>
</html>
