<?php
require('../../Group-Office.php');

$GO_SECURITY->authenticate();

$GO_MODULES->authenticate('email');

if(count($_SESSION['GO_SESSION']['email_module']['unknown_reciepents']) > 0)
{
	
	require($GO_LANGUAGE->get_language_file('email'));
	//var_dump($_SESSION['GO_SESSION']['email_module']['unknown_reciepents']);
	$return_to=$_SERVER['PHP_SELF'];
	$contact = array_shift($_SESSION['GO_SESSION']['email_module']['unknown_reciepents']);


	$url = $GO_MODULES->modules['addressbook']['url'].'contact.php';
	$url = add_params_to_url($url, 'feedback='.urlencode($ml_unknown_reciepent));
	$url = add_params_to_url($url, 'return_to='.urlencode($return_to));
	foreach($contact as $field=>$value)
	{
		$url = add_params_to_url($url, urlencode($field).'='.urlencode($value));
	}
	header('Location: '.$url);
}else {
	require_once($GO_THEME->theme_path.'header.inc');
	echo "<script type=\"text/javascript\">\r\nwindow.close();\r\n</script>\r\n";
	require_once($GO_THEME->theme_path.'footer.inc');
}