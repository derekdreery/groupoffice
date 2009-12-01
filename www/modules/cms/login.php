<?php
require_once('../../Group-Office.php');

require_once($GO_MODULES->modules['cms']['class_path'].'cms.class.inc.php');
require_once($GO_MODULES->modules['cms']['class_path'].'output.class.inc.php');
require_once($GO_MODULES->modules['cms']['class_path'].'cms_smarty.class.inc.php');
$cms = new cms();
$co = new cms_output();

if(!empty($_REQUEST['file_id']))
{
	$co->set_by_id($_REQUEST['file_id'], 0);
}else
{
	$co->load_site();
}
$smarty = new cms_smarty($co);


$cancel_url = isset($_REQUEST['cancel_url'])  ? ($_REQUEST['cancel_url']) : $_SERVER['HTTP_REFERER'];
$success_url=isset($_REQUEST['success_url'])  ? ($_REQUEST['success_url']) : $cancel_url;

$smarty->assign('cancel_url', $cancel_url);
$smarty->assign('success_url', $success_url);

if($_SERVER['REQUEST_METHOD']=='POST')
{	
	$username = ($_POST['username']);
	$password = ($_POST['password']);
	
	if (!$GO_AUTH->login($username, $password))
	{				
		$smarty->assign('failed', true);
	}else {

		if(strpos($success_url, 'login.php') || strpos($success_url, 'logout.php')){
			require_once($GO_MODULES->modules['cms']['path'].'smarty_plugins/function.cms_href.php');
			$success_url = str_replace('&amp;', '&', smarty_function_cms_href(array('path'=>''), $smarty));
		}

		header('Location: '.$success_url);
		exit();
	}
}
echo $co->replace_urls($smarty->fetch('auth/login.tpl'));
