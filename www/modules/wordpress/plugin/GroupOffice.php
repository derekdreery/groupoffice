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


$go_config = get_option('groupoffice_config');
require($go_config['config_file']);
define('NO_EVENTS', $go_config['config_file']);
define('CONFIG_FILE', $go_config['config_file']);
require($config['root_path'].'Group-Office.php');
ini_set('display_errors', 0);


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
						$this->go_config['db_pass'],
						$this->go_config['db_port']
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
					'post_content' => $record['content'],
					'post_title' => $record['title'],
					'post_status' => 'publish'
			);

			if (empty($record['post_id'])) {
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
	for ($i = 0; $vars[$i]; $i++)
		$result[$vars[$i++]] = unserialize($vars[$i]);
	return $result;
}

function groupoffice() {

	global $current_user;

//import Group-Office session data
	if (isset($_REQUEST['GO_SID'])) {
		$fname = session_save_path() . "/sess_" . $_REQUEST['GO_SID'];
		if (file_exists($fname)) {
			$data = file_get_contents($fname);
			$data = groupoffice_unserializesession($data);
			$_SESSION['GO_SESSION'] = $data['GO_SESSION'];

			$site_data['full_url'] = $_SESSION['GO_SESSION']['full_url'];
			$site_data['config_file'] = $_SESSION['GO_SESSION']['config_file'];

			update_option('groupoffice_config', $site_data);

//var_dump($_SESSION['GO_SESSION']);
		}
	}

//Create and login Group-Office user
	if (isset($_SESSION['GO_SESSION']['username']) && (!is_user_logged_in() || $current_user->user_login != $_SESSION['GO_SESSION']['username'])) {
//get user's ID
		$user = get_userdatabylogin($_SESSION['GO_SESSION']['username']);

		if ($user) {
			$user_id = $user->ID;
		} else {
			$user_id = groupoffice_add_user($_SESSION['GO_SESSION']);
		}

		wp_set_current_user($user_id, $_SESSION['GO_SESSION']['username']);
		wp_set_auth_cookie($user_id);
		do_action('wp_login', $_SESSION['GO_SESSION']['username']);
	}

	$go = new groupoffice_connector();
	$go->sync();

	if (isset($_REQUEST['GO_SID'])) {
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
	$sql = "SELECT ID FROM $wpdb->posts WHERE post_name = '".$page_name."'";
	$pageid = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$page_name."'");

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
	 var_dump($post);
	}

	$go_config = get_option('groupoffice_config');


	$url = $go_config['full_url'].'modules/recruity/inschrijven.php?wp_user_id='.intval($current_user->ID).'&email='.$current_user->user_email;
	return  '<iframe style="width:600px;height:600px" src="'.$url.'"></iframe>';
}
