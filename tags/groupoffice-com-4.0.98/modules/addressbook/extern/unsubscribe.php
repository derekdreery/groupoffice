<?php

require_once('../../../Group-Office.php');
require($GLOBALS['GO_LANGUAGE']->get_language_file('mailings'));
//$GLOBALS['GO_LANGUAGE']->set_language('nl');

$data['addresslist_id'] = $_REQUEST['addresslist_id'];
$data['recipient_type'] = $_REQUEST['recipient_type'];
$data['recipient_id'] = $_REQUEST['recipient_id'];
$data['hash'] = $_REQUEST['hash'];

$delete_success = false;

if (!isset($GLOBALS['GO_MODULES']->modules['mailings'])) {
	$error = sprintf($lang['common']['moduleRequired'], $lang['mailings']['name']);
	$data['type'] = null;
	$data['id'] = -1;
} else if (isset($_POST['unsubscribe'])) {

	require_once ($GLOBALS['GO_MODULES']->modules['mailings']['class_path'].'mailings.class.inc.php');
	$mailings = new mailings();
	require_once ($GLOBALS['GO_MODULES']->modules['mailings']['class_path'].'templates.class.inc.php');
	$tp = new templates();
	require_once ($GLOBALS['GO_MODULES']->modules['addressbook']['class_path'].'addressbook.class.inc.php');
	$ab = new addressbook();

	switch ($data['recipient_type']) {
		case 'contact':

			$contact=$ab->get_contact($data['recipient_id']);

			if($tp->get_unsubscribe_hash($contact['ctime'], $data['addresslist_id'],'contact', $contact['id'])!=$data['hash']){
				die('Invalid request');
			}

			$delete_success = $mailings->remove_contact_from_group($data['recipient_id'],$data['addresslist_id']);
			break;
		case 'user':

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$user=$GO_USERS->get_user($data['recipient_id']);
			if($tp->get_unsubscribe_hash($user['registration_time'], $data['addresslist_id'],'user', $user['id'])!=$data['hash']){
				die('Invalid request');
			}

			$delete_success = $mailings->remove_user_from_group($data['recipient_id'],$data['addresslist_id']);
			break;
		case 'company':

			$company=$ab->get_company($data['recipient_id']);
			if($tp->get_unsubscribe_hash($company['ctime'], $data['addresslist_id'],'company', $company['id'])!=$data['hash']){
				die('Invalid request');
			}

			$delete_success = $mailings->remove_company_from_group($data['recipient_id'],$data['addresslist_id']);
			break;
	}

}


$template_path = isset($GLOBALS['GO_CONFIG']->mailings_template_path) ? $GLOBALS['GO_CONFIG']->mailings_template_path : $GLOBALS['GO_MODULES']->modules['mailings']['path'].'extern/template/';
include_once($template_path.'unsubscribe_tpl.php');

?>