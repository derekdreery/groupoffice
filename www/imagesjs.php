<?php
require('Group-Office.php');

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

	echo $key.':"'.$image.'"';
	
}
echo '};';