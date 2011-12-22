<?php

require( dirname(__FILE__) . '/../../../wp-load.php' );
require_once( ABSPATH . WPINC . '/registration.php');

$errors = new WP_Error();

$sanitized_user_login = sanitize_user($_POST['username']);
$user_email = apply_filters('user_registration_email', $_POST['email']);

$response = array('success'=>true, 'error'=>'', 'user_id'=>0);

try{
	// Check the username
	if ($sanitized_user_login == '') {
		throw new Exception(__('<strong>ERROR</strong>: Please enter a username.'));
	} elseif (!validate_username($user_login)) {
		throw new Exception( __('<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.'));
		$sanitized_user_login = '';
	} elseif (username_exists($sanitized_user_login)) {
		throw new Exception( __('<strong>ERROR</strong>: This username is already registered, please choose another one.'));
	}

	// Check the e-mail address
	if ($user_email == '') {
		throw new Exception( __('<strong>ERROR</strong>: Please type your e-mail address.'));
	} elseif (!is_email($user_email)) {
		throw new Exception(__('<strong>ERROR</strong>: The email address isn&#8217;t correct.'));
		$user_email = '';
	} elseif (email_exists($user_email)) {
		throw new Exception(__('<strong>ERROR</strong>: This email is already registered, please choose another one.'));
	}

	do_action('register_post', $sanitized_user_login, $user_email, $errors);

	$user_pass = wp_generate_password();
	$response['password']=$user_pass;

	$response['user_id'] = wp_create_user($sanitized_user_login, $user_pass, $user_email);
	if (!$response['user_id']) {
		throw new Exception(sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !'), get_option('admin_email')));
	}

	

	update_user_option($user_id, 'default_password_nag', true, true); //Set up the Password change nag.

	//wp_new_user_notification($user_id, $user_pass);
}
catch(Exception $e){

	//var_dump($e);

	$response['error']=strip_tags($e->getMessage());
	$response['user_id']=0;
	$response['success']=false;
}
echo json_encode($response);
