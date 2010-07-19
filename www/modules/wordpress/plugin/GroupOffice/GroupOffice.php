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

//ini_set('error_reporting', E_ALL);


/*$go_config = get_option('groupoffice_config');
if(isset($go_config['config_file'])){
	require($go_config['config_file']);
	define('NO_EVENTS', $go_config['config_file']);
	define('CONFIG_FILE', $go_config['config_file']);
	require($config['root_path'].'Group-Office.php');
	//ini_set('display_errors', 0);
	ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}*/
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_DEPRECATED);

class groupoffice_connector {

	function __construct() {

		$this->wp_go_config = get_option('groupoffice_config');
		require($this->wp_go_config['config_file']);
		$this->go_config = $config;

		require_once($config['root_path'] . 'classes/database/base_db.class.inc.php');
		require_once($config['root_path'] . 'classes/database/mysql.class.inc.php');
	}

	function get_database() {
		$db = new db();
		$db->set_parameters(
						$this->go_config['db_host'],
						$this->go_config['db_name'],
						$this->go_config['db_user'],
						$this->go_config['db_pass']
						//$this->go_config['db_port']
		);

		return $db;
	}

	function sync() {
		include_once(ABSPATH . 'wp-admin/includes/taxonomy.php');
		//include_once(ABSPATH . 'wp-includes/meta.php');

		$db = $this->get_database();
		$db2 = $this->get_database();
		$db3 = $this->get_database();

		$sql = "SELECT * FROM wp_posts " .
						"WHERE publish=1 AND updated=1";

		$db->query($sql);

		$categories = array();

		while ($record = $db->next_record()) {

			/*if (!isset($categories[$record['link_type']])) {
				$db2->query("SELECT * FROM go_settings WHERE name='wp_category_" . $record['link_type'] . "'");
				$setting = $db2->next_record();

				$categories[$record['link_type']] = $setting ? explode(',', $setting['value']) : array();
			}*/


			$post = array(
					'post_content' => mysql_escape_string($record['content']),
					'post_title' => mysql_escape_string($record['title']),
					'post_status' => 'publish'
			);

			if(!empty($record['post_id'])){
				$existing_post = wp_get_single_post($record['post_id']);
				var_dump($existing_post);
			}

			if (empty($record['post_id']) || !$existing_post) {
				
				$post_id = wp_insert_post($post);
			} else {
				$post_id = $post['ID'] = $record['post_id'];

				$new_post_id = wp_update_post($post);
				if ($new_post_id > 0)
					$post_id = $new_post_id;
			}

			$db3->query("SELECT `key`, `value` FROM wp_posts_custom WHERE id=? AND link_type=?",'ii', array($record['id'], $record['link_type']));
			while($c=$db3->next_record())
				update_metadata('post',$post_id, $c['key'], $c['value']);

			//insert post to contact link so we know the post id in Group-Office
			$record['post_id'] = $post_id;
			$record['updated'] = 0;

			$db2->update_row('wp_posts', array('id', 'link_type'), $record);

			if(!empty($record['categories'])){
				$categories=explode(',', $record['categories']);

				wp_create_categories($categories, $post_id);
			}
		}

		$sql = "SELECT * FROM wp_posts p " .
						"WHERE publish=0 AND post_id>0";
		$db->query($sql);

		while ($record = $db->next_record()) {

			wp_delete_post($record['post_id']);
			$record['post_id'] = 0;

			$db2->update_row('wp_posts', array('id', 'link_type'), $record);
		}
	}

}

function groupoffice_unserializesession($data) {
	$vars = preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/',
									$data, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	for ($i = 0; isset($vars[$i]); $i++)
		$result[$vars[$i++]] = unserialize($vars[$i]);
	return $result;
}

function groupoffice() {

	global $current_user;

	$GO_SID = false;

	if(isset($_REQUEST['GO_SID']))
		$GO_SID=$_REQUEST['GO_SID'];
	//elseif(isset($_SESSION['GO_SESSION']['GO_SID']))
		//$GO_SID=$_SESSION['GO_SESSION']['GO_SID'];

	//var_dump($GO_SID);

//import Group-Office session data
	if ($GO_SID) {
		$fname = session_save_path() . "/sess_" . $GO_SID;
		if (file_exists($fname)) {
			$data = file_get_contents($fname);
			$data = groupoffice_unserializesession($data);
			$_SESSION['GO_SESSION'] = $data['GO_SESSION'];
			$_SESSION['GO_SESSION']['GO_SID']=$GO_SID;

			$site_data['full_url'] = $_SESSION['GO_SESSION']['full_url'];
			$site_data['config_file'] = $_SESSION['GO_SESSION']['config_file'];

			update_option('groupoffice_config', $site_data);

//var_dump($_SESSION['GO_SESSION']);
		}else
		{
			exit("Can't read Group-Office session data");
		}
	}

	$auto_login_username =  false;

	//var_dump($_SESSION['GO_SESSION']);

	if(isset($_SESSION['GO_SESSION']['wp_autologin_username']))
		$auto_login_username=$_SESSION['GO_SESSION']['wp_autologin_username'];
	elseif(isset($_SESSION['GO_SESSION']['username']))
		$auto_login_username=$_SESSION['GO_SESSION']['username'];


//Create and login Group-Office user
	if ($auto_login_username && (!is_user_logged_in() || $current_user->user_login != $auto_login_username)) {
//get user's ID
		$user = get_userdatabylogin($auto_login_username);

		if ($user) {
			$user_id = $user->ID;
		} else {
			$user_id = groupoffice_add_user($_SESSION['GO_SESSION']);
		}

#		var_dump($_SESSION['GO_SESSION']);

		wp_set_current_user($user_id, $auto_login_username);
		wp_set_auth_cookie($user_id);
		do_action('wp_login', $auto_login_username);
	}

	$go = new groupoffice_connector();
	$go->sync();

	if (isset($_REQUEST['GO_SID']) && !isset($_REQUEST['no_admin_redirect'])) {
//direct link to wp-admin didn't work so we go to the main page and redirect
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




add_action('login_form', 'groupoffice_redirect_after_login');

function groupoffice_redirect_after_login() {
		global $redirect_to;
		if (!isset($_GET['redirect_to'])) {
				$redirect_to = isset($_SESSION['go_last_permalink']) ? $_SESSION['go_last_permalink'] : get_option('siteurl');
			//$redirect_to=groupoffice_get_permalink_by_name('Inschrijven');
		}
}

function groupoffice_get_permalink_by_name($page_name)
{
	global $post;
	global $wpdb;
	$pageid = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '".$page_name."' AND post_status='publish'");

	return get_permalink($pageid);
}

if(strpos(basename($_SERVER['PHP_SELF']),'wp-')===false)
	$_SESSION['go_last_permalink']=the_permalink();


function groupoffice_get_contact_form($post_id=-1){

	global $current_user;

	if($post_id>-1)
		$_SESSION['last_contact_post_id']=$post_id;

	if(!empty($_SESSION['last_contact_post_id'])){
		$post = get_post ($_SESSION['last_contact_post_id']);
	 //var_dump($post);

		if($current_user->ID>0){

			$db = new db();
			$sql = "SELECT contact_id FROM wp_contacts_wp_users WHERE wp_user_id=".intval($current_user->ID);
			$db->query($sql);
			$r = $db->next_record();

			if(!empty($r['contact_id'])){
				$to = get_option('admin_email');
				$subject='Reactie op vacature '.$post->post_title;

				$message='<a href="go:showContact('.$r['contact_id'].');">Bekijk gegevens van '.$current_user->first_name.' '.$current_user->last_name.' ('.$current_user->user_email.')</a>';
				$headers="From: Keystaff (Recruity) <noreply@keystaff.nl>\n".
					"Content-Type: text/html";

				wp_mail($to, $subject, $message, $headers);

				return 'Hartelijk dank. Wij hebben uw reactie ontvangen en nemen spoedig contact met u op.';
			}
		}
	}
	$go_config = get_option('groupoffice_config');
	//var_dump($go_config);


	$url = $go_config['full_url'].'modules/recruity/inschrijven.php?wp_user_id='.intval($current_user->ID).'&email='.$current_user->user_email.'&post_title='.urlencode($post->post_title);
	return  '<iframe style="width:600px;height:800px" src="'.$url.'"></iframe>';
}

function groupoffice_add_params_to_url($url, $params) {
	if (strpos($url, '?') === false) {
		$url .= '?'.$params;
	} else {
		$url .= '&amp;'.$params;
	}
	return $url;
}












function groupoffice_custom_login() {

	echo  '<link rel="stylesheet" type="text/css" href="' . get_template_directory_uri().'/custom-login.css" />';

}

function groupoffice_change_wp_login_url() {

   return  bloginfo('url');

}

function groupoffice_change_wp_login_title() {

    return 'Powered by Recuity';

}

add_action('login_head', 'groupoffice_custom_login');
add_filter('login_headerurl', 'groupoffice_change_wp_login_url');
add_filter('login_headertitle', 'groupoffice_change_wp_login_title');