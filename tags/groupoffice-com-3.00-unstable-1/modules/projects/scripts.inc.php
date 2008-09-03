<?php
if(isset($GO_MODULES->modules['customfields']))
{
	require($GO_LANGUAGE->get_language_file('projects'));
	require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
	$cf = new customfields();
	echo $cf->get_javascript(5, $lang['projects']['projects']);
}

$max_projects = isset($GO_CONFIG->max_projects) ?$GO_CONFIG->max_projects : 0;

echo '<script type="text/javascript">GO.projects.max_projects='.$max_projects.';</script>';