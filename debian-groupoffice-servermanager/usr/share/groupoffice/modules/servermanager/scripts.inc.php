<?php

$GO_SCRIPTS_JS .= 'GO.servermanager.config={};';
require('/etc/groupoffice/servermanager.inc.php');

foreach($default_config as $key=>$value)
{
	$GO_SCRIPTS_JS .= 'GO.servermanager.config["'.$key.'"]="'.$value.'";';
}