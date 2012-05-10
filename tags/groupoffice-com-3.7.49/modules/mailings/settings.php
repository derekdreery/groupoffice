<?php
require('../../Group-Office.php');

require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc.php');
require_once($GO_MODULES->modules['cms']['class_path'].'cms.class.inc.php');
require_once($GO_MODULES->modules['cms']['class_path'].'output.class.inc.php');
require_once($GO_MODULES->modules['cms']['class_path'].'cms_smarty.class.inc.php');
$cms = new cms();
$co = new cms_output();
$ab = new addressbook();

$co->set_by_path($_REQUEST['site_id'], '', '/');
$smarty = new cms_smarty($co);

switch($_GET['type'])
{
	case 'user':

			require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$user = $GO_USERS->get_user($_GET['id']);
			
			if($_REQUEST['hash']!=md5($user['id'].$user['ctime']))
			{
				die('Access denied!');
			}			
			$smarty->assign('email', $user['email']);
			
			if($_SERVER['REQUEST_METHOD']=='POST')
			{
				$u['id']=$user['id'];
				$user['email']=$u['email']=$_POST['email'];
				
				if($GO_USERS->update_profile($u))
				{				
					$smarty->assign('success', $lang['common']['dataSaved']);
				}else
				{
					$smarty->assign('error', $lang['common']['saveError']);
				}
			}
		break;	
		
	case 'company':
			$company = $ab->get_company($_GET['id']);
			
			if($_REQUEST['hash']!=md5($company['id'].$company['ctime']))
			{
				die('Access denied!');
			}			
			
			
			if($_SERVER['REQUEST_METHOD']=='POST')
			{
				$u['id']=$company['id'];
				$company['email']=$u['email']=$_POST['email'];
				
				if($ab->update_company($u))
				{				
					$smarty->assign('success', $lang['common']['dataSaved']);
				}else
				{
					$smarty->assign('error', $lang['common']['saveError']);
				}
			}
			
			$smarty->assign('email', $company['email']);
		break;	
		
	case 'contact':
			$contact = $ab->get_contact($_GET['id']);
			
			if($_REQUEST['hash']!=md5($contact['id'].$contact['ctime']))
			{
				die('Access denied!');
			}				
			
			if($_SERVER['REQUEST_METHOD']=='POST')
			{
				$u['id']=$contact['id'];
				$contact['email']=$u['email']=$_POST['email'];
				
				if($ab->update_contact($u))
				{				
					$smarty->assign('success', $lang['common']['dataSaved']);
				}else
				{
					$smarty->assign('error', $lang['common']['saveError']);
				}
			}
			
			$smarty->assign('email', $contact['email']);
		break;	
		
	default:
		die('Request error');
	break;
}

echo $smarty->fetch('mailings/settings.tpl');
?>