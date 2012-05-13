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
				require_once($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
				$ml = new mailings();
				$ml->remove_user_from_group($user['id'], $_REQUEST['mailing_group_id']);
				
				$smarty->assign('signedoff', true);
			}
		break;	
		
	case 'company':
			$company = $ab->get_company($_GET['id']);
			
			if($_REQUEST['hash']!=md5($company['id'].$company['ctime']))
			{
				die('Access denied!');
			}			
			$smarty->assign('email', $company['email']);
			
			if($_SERVER['REQUEST_METHOD']=='POST')
			{
				require_once($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
				$ml = new mailings();
				$ml->remove_company_from_group($company['id'], $_REQUEST['mailing_group_id']);
				
				$smarty->assign('signedoff', true);
			}
		break;	
		
	case 'contact':
			$contact = $ab->get_contact($_GET['id']);
			
			if($_REQUEST['hash']!=md5($contact['id'].$contact['ctime']))
			{
				die('Access denied!');
			}			
			$smarty->assign('email', $contact['email']);
			
			if($_SERVER['REQUEST_METHOD']=='POST')
			{
				require_once($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
				$ml = new mailings();
				$ml->remove_contact_from_group($contact['id'], $_REQUEST['mailing_group_id']);
				
				$smarty->assign('signedoff', true);
			}
		break;	
		
	default:
		die('Request error');
	break;
}




echo $smarty->fetch('mailings/signoff.tpl');
?>