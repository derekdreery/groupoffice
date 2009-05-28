<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

class users extends db
{
	public function __on_load_listeners($events){
		$events->add_listener('load_settings', __FILE__, 'users', 'load_settings');
		$events->add_listener('save_settings', __FILE__, 'users', 'save_settings');
		$events->add_listener('build_search_index', __FILE__, 'users', 'build_search_index');
		$events->add_listener('check_database', __FILE__, 'users', 'check_database');
	}

	public static function check_database(){
		global $GO_CONFIG, $GO_MODULES, $GO_USERS, $GO_SECURITY;

		$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";

		echo 'User folders'.$line_break;



		if(isset($GO_MODULES->modules['files']))
		{

			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();
			$GO_USERS->get_users();

			while($GO_USERS->next_record())
			{
				$home_dir = 'users/'.$GO_USERS->f('username');

				if(!is_dir($GO_CONFIG->file_storage_path.$home_dir))
				{
					mkdir($GO_CONFIG->file_storage_path.$home_dir, $GO_CONFIG->folder_create_mode, true);
				}

				$folder = $files->resolve_path($home_dir,true,1,'1');

				if(empty($folder['acl_read']))
				{
					echo "Sharing users/".$GO_USERS->f('username').$line_break;

					$up_folder['id']=$folder['id'];
					$up_folder['acl_read']=$GO_SECURITY->get_new_acl('files', $GO_USERS->f('id'));
					$up_folder['acl_write']=$GO_SECURITY->get_new_acl('files', $GO_USERS->f('id'));

					$files->update_folder($up_folder);
				}
				$files->set_readonly($folder['id']);

				$home_dir = 'adminusers/'.$GO_USERS->f('username');

				if(!is_dir($GO_CONFIG->file_storage_path.$home_dir))
				{
					mkdir($GO_CONFIG->file_storage_path.$home_dir, $GO_CONFIG->folder_create_mode, true);
				}

				$folder = $files->resolve_path($home_dir,true,1,'1');

				if(empty($folder['acl_read']))
				{
					echo "Sharing adminusers/".$GO_USERS->f('username').$line_break;

					$up_folder['id']=$folder['id'];
					$up_folder['acl_read']=$GO_SECURITY->get_new_acl('files', 1);
					$up_folder['acl_write']=$GO_SECURITY->get_new_acl('files', 1);

					$files->update_folder($up_folder);
				}
				$files->set_readonly($folder['id']);
			}
		}


		echo 'Done'.$line_break.$line_break;
	}

	function load_settings($response)
	{
		global $GO_USERS, $GO_MODULES;

		$user_id = isset($_REQUEST['user_id']) ? ($_REQUEST['user_id']) : 0;

		$user = $GO_USERS->get_user($user_id);
		$response['data']=array_merge($response['data'], $user);
		$response['data']['birthday']=Date::format($response['data']['birthday'], false);
		$response['data']['start_module_name'] = isset($GO_MODULES->modules[$response['data']['start_module']]['humanName']) ? $GO_MODULES->modules[$response['data']['start_module']]['humanName'] : '';
		$response['data']['registration_time'] = Date::get_timestamp($response['data']['registration_time']);
		$response['data']['lastlogin'] = Date::get_timestamp($response['data']['lastlogin']);
	}

	function save_settings()
	{
		global $GO_USERS, $lang, $GO_CONFIG;

		$user['id'] = isset($_POST['user_id']) ? (trim($_POST['user_id'])) : 0;

		if(isset($_POST['first_name']))
		{
			$user['first_name'] = $_POST['first_name'];
			$user['middle_name'] = $_POST['middle_name'];
			$user['last_name'] = $_POST['last_name'];
			$user['email'] = $_POST["email"];
			//$user['enabled'] = isset($_POST['enabled']) ? '1' : '0' ;
			$user['title'] = $_POST["title"];


			$user['initials'] = $_POST["initials"];
			$user['birthday'] = Date::to_db_date($_POST['birthday']);
			//$user['work_phone'] = $_POST["work_phone"];
			$user['home_phone'] = $_POST["home_phone"];
			$user['fax'] = $_POST["fax"];
			$user['cellular'] = $_POST["cellular"];
			$user['country'] = $_POST["country"];
			$user['state'] = $_POST["state"];
			$user['city'] = $_POST["city"];
			$user['zip'] = $_POST["zip"];
			$user['address'] = $_POST["address"];
			$user['address_no'] = $_POST["address_no"];
			//$user['department'] = $_POST["department"];
			//$user['function'] = $_POST["function"];
			$user['company'] = $_POST["company"];
			/*$user['work_country'] = $_POST["work_country"];
			$user['work_state'] = $_POST["work_state"];
			$user['work_city'] = $_POST["work_city"];
			$user['work_zip'] = $_POST["work_zip"];
			$user['work_address'] = $_POST["work_address"];
			$user['work_address_no'] = $_POST["work_address_no"];
			$user['work_fax'] = $_POST["work_fax"];
			$user['homepage'] = $_POST["homepage"];*/
			$user['sex'] = $_POST["sex"];

			if(empty($user['email']) || empty($user['first_name']) || empty($user['last_name']))
			{
				throw new MissingFieldException();
			}

			if (!String::validate_email($user['email'])) {
				throw new Exception($lang['common']['invalidEmailError']);
			}

			$existing_email_user = $GO_CONFIG->allow_duplicate_email ? false : $GO_USERS->get_user_by_email($user['email']);

			if ($existing_email_user && ($user['id'] == 0 || $existing_email_user['id'] != $user['id'])) {
				require($GLOBALS['GO_LANGUAGE']->get_language_file('users'));
				throw new Exception($lang['users']['error_email_exists']);
			}
		}


		if(isset($_POST['max_rows_list']))
		{
			if(isset($_POST['theme']))
				$user['theme'] = $_POST["theme"];


			$user['language'] = ($_POST["language"]);
			$user['max_rows_list'] = ($_POST["max_rows_list"]);
			$user['sort_name'] = ($_POST["sort_name"]);
			$user['start_module'] = ($_POST["start_module"]);
			$user['mute_sound'] = isset($_POST["mute_sound"]) ? '1' : '0';
		}

		if(isset($_POST['language']))
		{
			$user['language']=$_POST['language'];
			$user['first_weekday'] = ($_POST["first_weekday"]);
			$user['date_format'] = ($_POST["date_format"]);
			$user['date_separator'] = ($_POST["date_separator"]);
			$user['decimal_separator'] = ($_POST["decimal_separator"]);
			$user['thousands_separator'] = ($_POST["thousands_separator"]);
			$user['time_format'] = ($_POST["time_format"]);
			$user['timezone'] = ($_POST["timezone"]);
			$user['currency'] = ($_POST["currency"]);

			$user['list_separator'] = $_POST["list_separator"];
			$user['text_separator'] = $_POST["text_separator"];
		}


		if (!empty($_POST["password1"]) || !empty($_POST["password2"]))
		{
			if(!$GO_USERS->check_password(($_POST['current_password'])))
			{
				throw new Exception($lang['common']['badPassword']);
			}

			if($_POST["password1"] != $_POST["password2"])
			{
				throw new Exception($lang['common']['passwordMatchError']);
			}
			if(!empty($_POST["password2"]))
			{
				$user['password']=($_POST["password2"]);
			}
		}

		$GO_USERS->update_user($user);

	}

	public function build_search_index()
	{
		global $GO_USERS;

		$users = new users();

		$sql = "SELECT id FROM go_users";
		$users->query($sql);

		while($record=$users->next_record())
		{
			$GO_USERS->cache_user($record['id']);
		}
	}

}