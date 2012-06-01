<?php
require_once('imapauth.config.php');

switch ($_REQUEST['task']) {
	/*
	case 'domains':
		$domains = explode(',',$config[0]['imapauth_combo_domains']);
		$response['results'] = array();
		$response['results'][] = array('domain'=>'','display'=>'no domain');
		foreach ($domains as $domain) {
			$response['results'][] = array('domain'=>'@'.$domain,'display'=>'@'.$domain);
		}
		break;
	 * 
	 */
	case 'default_domain':
		$response['data'] = array('domain'=>'@'.$config[0]['imapauth_default_domain']);
		break;
}
$response['success'] = true;
echo json_encode($response);
?>
