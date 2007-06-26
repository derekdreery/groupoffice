<?php
require('Group-Office.php');

if(isset($_REQUEST['modules']))
{
	$modules = explode(',', $_REQUEST['modules']);

	foreach($modules as $module)
	{
		$GO_THEME->load_module_theme($module);
	}
}
echo 'var GOimages = {';

$first=true;
foreach($GO_THEME->images as $key=>$image)
{
	if(!$first)
	{
		echo ',';
	}else {
		$first=false;
	}

	echo '"'.$key.'":"'.$image.'"';

}
echo '};';