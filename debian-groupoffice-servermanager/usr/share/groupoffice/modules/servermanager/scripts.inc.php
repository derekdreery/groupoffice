<?php

$stable=false;
if(!isset($GO_SCRIPTS_JS)){
	$stable=true;
	$GO_SCRIPTS_JS='';
}

$GO_SCRIPTS_JS .= 'GO.servermanager.config={};';
if(file_exists('/etc/groupoffice/servermanager.inc.php')){
	require('/etc/groupoffice/servermanager.inc.php');

	foreach($default_config as $key=>$value)
	{
		$GO_SCRIPTS_JS .= 'GO.servermanager.config["'.$key.'"]="'.$value.'";';
	}

	if($stable)
		echo '<script type="text/javascript">'.$GO_SCRIPTS_JS.'</script>';
}