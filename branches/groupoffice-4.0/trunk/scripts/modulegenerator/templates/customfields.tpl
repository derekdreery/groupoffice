if(isset($GLOBALS['GO_MODULES']->modules['customfields']))
{
	require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
	$cf = new customfields();
	echo $cf->get_javascript({link_type}, ${prefix}_{friendly_multiple});
}