if(isset(GO::modules()->modules['customfields']))
{
	require_once(GO::modules()->modules['customfields']['class_path'].'customfields.class.inc.php');
	$cf = new customfields();
	echo $cf->get_javascript({link_type}, ${prefix}_{friendly_multiple});
}