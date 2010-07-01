<?php

/**
 * @package GroupOffice
 * @version 1.0
 */
/*
  Plugin Name: GroupOffice
  Plugin URI: http://www.group-office.com
  Description: Connect Group-Office and Wordpress
  Author: Merijn Schering
  Version: 1.0
  Author URI: http://www.intermesh.nl/en/
 */

function groupoffice_unserializesession($data) {
	$vars = preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/',
									$data, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	for ($i = 0; $vars[$i]; $i++)
		$result[$vars[$i++]] = unserialize($vars[$i]);
	return $result;
}

function groupoffice() {

	global $current_user;


	if (isset($_REQUEST['GO_SID'])) {
		//determine WordPress user account to impersonate

		$fname = session_save_path() . "/sess_" . $_REQUEST['GO_SID'];
		if (file_exists($fname)) {
			$data = file_get_contents($fname);
			$data = groupoffice_unserializesession($data);
			$_SESSION['GO_SESSION'] = $data['GO_SESSION'];
		}
	}

	//var_dump($_SESSION['GO_SESSION']['username']);
	//$_SESSION['GO_SESSION']['username']='admin';

	if (isset($_SESSION['GO_SESSION']['username']) && (!is_user_logged_in() || $current_user->user_login !=$_SESSION['GO_SESSION']['username'])) {
		//get user's ID
		$user = get_userdatabylogin($_SESSION['GO_SESSION']['username']);

		if($user){
			$user_id = $user->ID;
		}else
		{
			$user_id = groupoffice_add_user($_SESSION['GO_SESSION']);
		}

		wp_set_current_user($user_id, $_SESSION['GO_SESSION']['username']);
		wp_set_auth_cookie($user_id);
		do_action('wp_login', $_SESSION['GO_SESSION']['username']);

	}
	if (isset($_REQUEST['GO_SID'])) {
		wp_redirect(admin_url());
		exit();
	}
}

function groupoffice_add_user($go_session) {

	include_once( ABSPATH . 'wp-includes/registration.php' );

	$user_id = wp_insert_user(array(
							"user_login" => $go_session['username'],
							"first_name" => $go_session['first_name'],
							"last_name" => $go_session['last_name'],
							"user_pass" => uniqid(time()),
							"user_email" => $go_session['email'])
	);
	if (!$user_id) {
		return false;
	} else {
		//wp_new_user_notification($user_id, $password);
		//$complete++;

		$ruser = new WP_User($user_id);
		$ruser->set_role('editor');
		return $user_id;
	}
}

add_action('init', 'groupoffice');
