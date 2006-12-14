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

//load Group-Office
require_once("../../Group-Office.php");

//authenticate the user
$GO_SECURITY->authenticate();

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('filesystem', true);

load_basic_controls();
load_control('datagrid');

//require the header file. This will draw the logo's and the menu
require_once($GO_THEME->theme_path."header.inc");
echo '<form name="fs_statuses_form" method="post" action="'.$_SERVER['PHP_SELF'].'">';
$sql = "SELECT * FROM fs_statuses ORDER BY id ASC";
$datagrid = new datagrid('fs_statuses_form','fs_statuses', 'id', $sql);
$datagrid->add_column('name', $strName, 'text');
echo $datagrid->print_datagrid();
echo '</form>';
?>
<script type="text/javascript">
	var DropBox = opener.document.forms[0].status_id;
	DropBox.options.length=0;
	<?php
	require_once($GO_CONFIG->class_path."filesystem.class.inc");
	$fs = new filesystem();
	$fs->get_statuses();	
	while($fs->next_record())
	{		
		echo "var opt = opener.document.createElement('option');";
		echo "opt.text = '".addslashes($fs->f('name'))."';";
		echo "opt.value = '".$fs->f('id')."';";		
		echo "DropBox.options[DropBox.options.length] = opt;";
	}
	?>	
</script>

<?php
require_once($GO_THEME->theme_path."footer.inc");
?>

