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

require_once("Group-Office.php");
$GO_SECURITY->authenticate();
?>
<div id="links_grid"></div>
<script type="text/javascript">
var linksGrid = new GroupOffice.linksGrid('links_grid', {link_id: <?php echo $_REQUEST['link_id']; ?>});
linksGrid.render();
</script>