<?php
if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}


require('../../www/Group-Office.php');
/*
require_once ($GO_MODULES->modules['projects']['class_path']."projects.class.inc.php");
//require_once ($GO_LANGUAGE->get_language_file('projects'));
$projects = new projects();

$projects->get_types();
$type = $projects->next_record();

$projects->get_statuses();
$status = $projects->next_record();

for($i=0;$i<1000;$i++)
{
	unset($project);
	$project['name']='test_'.$i;
	$project['status_id']=$status['id'];
	$project['type_id']=$type['id'];

	$projects->add_project($project, $type);
}*/


//files::build_search_index();

for($i=1;$i<=50;$i++)
{
	$discount = pow(0.95,$i-1);
	if($discount<0.3)
	{
		exit();
		$discount = 0.3;
	}
	echo $i.' = '.ceil($i*10*$discount)."\n";
}
?>
