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



$go_config = get_option('groupoffice_config');
if(isset($go_config['config_file'])){
	require($go_config['config_file']);
	define('NO_EVENTS', $go_config['config_file']);
	define('CONFIG_FILE', $go_config['config_file']);
	require($config['root_path'].'Group-Office.php');
	if(!WP_DEBUG)
		ini_set('display_errors', 0);
	//ini_set('display_errors', 0);

	define('GROUPOFFICE_CONNECTED', true);
}
//ini_set('display_errors', 1);
//ini_set('error_reporting', E_ALL & ~E_NOTICE);

class groupoffice_connector {

	function __construct() {

		$this->wp_go_config = get_option('groupoffice_config');

		if(file_exists($this->wp_go_config['config_file'])){
			require($this->wp_go_config['config_file']);
			$this->go_config = $config;

			require_once($config['root_path'] . 'classes/database/base_db.class.inc.php');
			require_once($config['root_path'] . 'classes/database/mysql.class.inc.php');
		}
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
					//'post_content' => mysql_real_escape_string ($record['content']),
					'post_title' => mysql_real_escape_string($record['title']),
					'post_status' => 'publish'
			);


			if(!empty($record['post_id'])){
				$existing_post = wp_get_single_post($record['post_id']);				
			}

			if (empty($record['post_id']) || empty($existing_post->ID)) {				
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

	if(!defined('GROUPOFFICE_CONNECTED'))
		return false;
	
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
	global $go;
	$go = new groupoffice_connector();
	$go->sync();

	if (isset($_REQUEST['GO_SID']) && !isset($_REQUEST['no_admin_redirect'])) {
//direct link to wp-admin didn't work so we go to the main page and redirect

		if(!empty($_REQUEST['link_id']) && !empty($_REQUEST['link_type'])){
			$db = $go->get_database();
			$sql = "SELECT post_id FROM wp_posts WHERE id=? AND link_type=?";
			$db->query($sql, 'ii', array($_REQUEST['link_id'], $_REQUEST['link_type']));
			$post = $db->next_record();
			//http://localhost/wordpress/wp-admin/post.php?post=710&action=edit

			if(!empty($post['post_id'])){
				$redirect_to=admin_url().'post.php?post='.$post['post_id'].'&action=edit';
			}

		}
		if(!isset($redirect_to))
			$redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : admin_url();

		wp_redirect($redirect_to);
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
		if (!isset($_REQUEST['redirect_to'])) {
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


function groupoffice_get_contact_form($post_extra_info=false){

	global $current_user, $go_config, $GO_MODULES, $GO_CONFIG;

require_once($GO_CONFIG->class_path.'base/links.class.inc.php');
$GO_LINKS = new GO_LINKS();

	//$go_config = get_option('groupoffice_config');

	$post_title=false;
	if($post_extra_info){

		if(!empty($_REQUEST['contact_post_id'])){
			$_SESSION['last_contact_post_id']=$_REQUEST['contact_post_id'];
			//unset($_SESSION['last_contact_extra']);
		}

		if(!empty($_REQUEST['extra']))
		{
			if(substr($_REQUEST['extra'],0,3)=='b64'){
				$_REQUEST['extra']=base64_decode(substr($_REQUEST['extra'],3));
			}
			$_SESSION['last_contact_extra']=$_REQUEST['extra'];
			//unset($_SESSION['last_contact_post_id']);
		}

		
		if(!empty($_SESSION['last_contact_extra'])){
			$post_title=$_SESSION['last_contact_extra'];
		}elseif(!empty($_SESSION['last_contact_post_id'])){
			$post = get_post ($_SESSION['last_contact_post_id']);
			$post_title=$post->post_title;
		}

		$category='';
		if(!empty($_SESSION['last_contact_post_id'])){
			$categories = get_the_category($_SESSION['last_contact_post_id']);
			$category=strtolower($categories[0]->name);
		}

		//var_dump($_SESSION['last_contact_extra']);
	}

	 //var_dump($post);

	if(!empty($post_title)){

		$replacements['vacature']=$post_title;

		$post_title = date('d-m-Y').": reactie op ".$post_title;
		if($current_user->ID>0){

			global $go;

			$db = $go->get_database();
			$sql = "SELECT contact_id FROM wp_contacts_wp_users w INNER JOIN ab_contacts c ON c.id=w.contact_id WHERE wp_user_id=".intval($current_user->ID);
			$db->query($sql);
			$r = $db->next_record();


			if(!empty($r['contact_id'])){
				
				$comment = "\n\n".mysql_real_escape_string($post_title);
				$sql = "UPDATE ab_contacts SET comment=CONCAT(comment, '$comment') WHERE id=".intval($r['contact_id']);
				$db->query($sql);

				if(!empty($_SESSION['last_contact_post_id'])){

					require_once($GO_CONFIG->class_path.'base/links.class.inc.php');
					$GO_LINKS = new GO_LINKS();

					require_once($GO_MODULES->modules['wordpress']['class_path'].'wordpress.class.inc.php');
					$wp = new wordpress();

					$post = $wp->get_post_by_wp_id($_SESSION['last_contact_post_id']);

					#if($post)
					#	$GO_LINKS->add_link($post['id'], $post['link_type'], $r['contact_id'], 2);
	
					//add to first fase
					global $GO_MODULES;
					require_once ($GO_MODULES->modules['recruity']['class_path']."fase.class.inc.php");
					$fase = new fase();

					$fase->get_fases('',0,1);
					$fr = $fase->next_record();
					$faseid = $fr['id'];

					$var['project_id'] = $post['id'];
					$var['contact_id'] = $r['contact_id'];
					$var['fase_id'] = $faseid;
					$fase->add_contact_project($var);
				}



				$to = get_option('admin_email').',test@intermesh.nl';
				$subject='Reactie op vacature '.$post_title;

				$message='<p>Dit is een geautomatiseerd bericht. Er heeft iemand op een vacature gereageerd. Klik op onderstaande link om de contactgegevens te bekijken.</p><a href="go:showContact('.$r['contact_id'].');">Bekijk gegevens van '.$current_user->first_name.' '.$current_user->last_name.' ('.$current_user->user_email.')</a>';
				$headers="From: Keystaff (Recruity) <noreply@keystaff.nl>\n".
					"Content-Type: text/html";

				wp_mail($to, $subject, $message, $headers);


				//reactie naar klant

				$dir = $GO_CONFIG->file_storage_path.'users/admin/formulieren/';

				$path = $dir.'reactie-vacature-'.$category.'.eml';
				if(!file_exists($path))
					$path=$dir.'reactie-vacature.eml';

				if(file_exists($path)){
					$email = file_get_contents($path);
					require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
					$swift = new GoSwiftImport($email);
					$body=$swift->body;

					foreach($replacements as $key=>$value){
						$body = str_replace('{'.$key.'}', $value, $body);
					}

					require_once($GO_MODULES->modules['addressbook']['path'].'classes/addressbook.class.inc.php');
					$ab = new addressbook();
					$contact=$ab->get_contact($r['contact_id']);

					if(isset($GO_MODULES->modules['mailings'])){
						require_once($GO_MODULES->modules['mailings']['path'].'classes/templates.class.inc.php');
						$tp = new templates();

						$body=$tp->replace_contact_data_fields($body, $contact, false);
					}

					//echo $body;

					$swift->set_body($body, 'html');

					$swift->set_to($contact['email']);
					$swift->sendmail();
				}


				return 'Hartelijk dank. Wij hebben uw reactie ontvangen en nemen spoedig contact met u op.';
			}
		}
	}

	$url = $go_config['full_url'].'modules/recruity/inschrijven.php?wp_user_id='.intval($current_user->ID).'&email='.$current_user->user_email.'&post_title='.urlencode($post_title).'&category='.urlencode($category);

	if(!empty($_SESSION['last_contact_post_id']))
		$url .= '&post_id='.$_SESSION['last_contact_post_id'];

	return  '<iframe frameborder="0" style="width:600px;height:800px" src="'.$url.'"></iframe>';
}

function groupoffice_add_params_to_url($url, $params) {
	if (strpos($url, '?') === false) {
		$url .= '?'.$params;
	} else {
		$url .= '&amp;'.$params;
	}
	return $url;
}












/**
 * Custom login part. Allows to set a custom login screen in the theme.
 * Put the GroupOffice/customlogin/custom-login.css file in your theme.
 * Use the sample images to create the screen.
 */

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



/*
 * Customize from address when Wordpress sends an e-mail.
 */

// new name
function groupoffice_mail_from_name() {
	$name = get_option('blogname');
	$name = esc_attr($name);
	return $name;
}

// new email-adress
function groupoffice_mail_from() {
	$email = 'noreply@'. trim(str_replace('www.','',strtolower($_SERVER['SERVER_NAME'])));

	return $email;
}
add_filter( 'wp_mail_from', 'groupoffice_mail_from' );
add_filter( 'wp_mail_from_name', 'groupoffice_mail_from_name');

