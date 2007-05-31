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


require_once ("../../../../Group-Office.php");

//authenticate the user
$GO_SECURITY->authenticate();

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('cms');

require_once ($GO_THEME->theme_path."header.inc");


require_once($GO_MODULES->path.'plugins/search.class.inc');
$cms_search = new cms_search();
?>
<script type="text/javascript" language="javascript">
var oEditor = opener.FCKeditorAPI.GetInstance('content') ;
oEditor.InsertHtml('<cms:plugin plugin_id="search"><?php echo $cms_search->get_name(); ?></cms:plugin>');
window.close();
</script>
<?php
require_once ($GO_THEME->theme_path."footer.inc");
