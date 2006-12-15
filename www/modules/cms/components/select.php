<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is part of the Group-Office Professional license
*/

//load Group-Office
require_once("../../../Group-Office.php");

//authenticate the user
$GO_SECURITY->authenticate();

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('cms');

require_once($GO_THEME->theme_path."header.inc");

require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem(true);

$plugin = $GO_MODULES->get_plugin('components');

$files = $fs->get_files($plugin['path']);
echo '<table border="0">';
echo '<tr><td width="16"></td>';
echo '<td><h3>'.$strName.'</h3></td>';
echo '<td colspan="2"></td></tr>';


while($file = array_shift($files))
{
	if($file['name'] != 'select.php')
	{
		echo '<tr><td width="16" height="16"><img src="'.$GO_THEME->images['site'].'" border="0" widht="16" height="16" /></td>';
		echo '<td><a class="normal" href="javascript:insert_file(\''.urlencode($plugin['url'].$file['name'].'?site_id='.$_SESSION['site_id']).'\');">'.strip_extension($file['name']).'</a></td>';
	}
}

$forms_path = $GO_CONFIG->local_path.'/cms/'.$_SESSION['site_id'].'/forms/';
mkdir_recursive($forms_path);

$files = $fs->get_files($forms_path);
while($file = array_shift($files))
{
	echo '<tr><td width="16" height="16"><img src="'.$GO_THEME->images['site'].'" border="0" widht="16" height="16" /></td>';
	echo '<td><a class="normal" href="javascript:insert_file(\''.urlencode($GO_CONFIG->local_url.$file['name'].'?site_id='.$_SESSION['site_id']).'\');">'.strip_extension($file['name']).'</a></td>';
}

echo '</table><br />';
$button = new button($cmdClose, "javascript:window.close();");

?>
<script type="text/javascript">

function insert_file(url)
{
	var oEditor = opener.FCKeditorAPI.GetInstance('content') ;
	oEditor.InsertHtml('<iframe frameborder="0" src="'+url+'" style="width:500px;height:400px;border:0px;"></iframe>');
	window.close();
}
</script>

<?php
require_once($GO_THEME->theme_path."footer.inc");
?>
