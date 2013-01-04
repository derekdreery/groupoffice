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

	function init_customfields_types(){
//		global $GO_MODULES, $customfield_types;
//		require_once($GO_MODULES->modules['users']['class_path'].'user_customfield_type.class.inc.php');
//		$customfield_types['user']=new user_customfield_type(array());
	}

	public function get_register_email(){
		global $GO_CONFIG, $GO_LANGUAGE, $lang;
		$r=array(
			'register_email_subject' => $GO_CONFIG->get_setting('register_email_subject'),
			'register_email_body' => $GO_CONFIG->get_setting('register_email_body')
		);

		$GO_LANGUAGE->require_language_file('users');
		
		if(!$r['register_email_subject']){
			$r['register_email_subject']=$lang['users']['register_email_subject'];
		}
		if(!$r['register_email_body']){
			$r['register_email_body']=$lang['users']['register_email_body'];
		}
		return $r;
	}

	public static function check_database(){
		global $GO_CONFIG, $GO_MODULES, $GO_SECURITY;

		$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";

		echo 'User folders'.$line_break;



		if(isset($GO_MODULES->modules['files']))
		{

			require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();
			$GO_USERS->get_users();

			$last_acl_id=0;

			while($GO_USERS->next_record())
			{
				$home_dir = 'users/'.$GO_USERS->f('username');

				File::mkdir($GO_CONFIG->file_storage_path.$home_dir);

				$folder = $files->resolve_path($home_dir,true,1,'1');

				$up_folder=array();
				$up_folder['id']=$folder['id'];
				if(empty($folder['acl_id']) || $folder['acl_id']==$last_acl_id)
				{
					echo "Sharing users/".$GO_USERS->f('username').$line_break;
					
					$up_folder['acl_id']=$GO_SECURITY->get_new_acl('files', $GO_USERS->f('id'));
				}else
				{
					$GO_SECURITY->chown_acl($folder['acl_id'], $GO_USERS->f('id'));
				}
				$up_folder['user_id']=$GO_USERS->f('id');
				$up_folder['readonly']='1';
				$up_folder['visible']='1';
				$files->update_folder($up_folder);


				$last_acl_id=$folder['acl_id'];
				

				//correct user_id on child folders
				echo "Applying user_id recursively".$line_break;
				$files->update_child_folders_recursively($up_folder['id'], array('user_id'=>$GO_USERS->f('id')));
				//$files->set_readonly($folder['id']);

				$home_dir = 'adminusers/'.$GO_USERS->f('username');

				File::mkdir($GO_CONFIG->file_storage_path.$home_dir);

				$folder = $files->resolve_path($home_dir,true,1,'1');

				$up_folder=array();
				$up_folder['id']=$folder['id'];
				if(empty($folder['acl_id']) || $folder['acl_id']==$last_acl_id)
				{
					echo "Sharing adminusers/".$GO_USERS->f('username').$line_break;
					
					$up_folder['acl_id']=$GO_SECURITY->get_new_acl('files', 1);	
				}
				$up_folder['visible']='0';
				$up_folder['readonly']='1';
				$files->update_folder($up_folder);
				//$files->set_readonly($folder['id']);

				$up_user['id']=$GO_USERS->f('id');
				$up_user['files_folder_id']=$folder['id'];
				$files->update_row('go_users', 'id', $up_user);
			}
		}

		if(isset($GO_MODULES->modules['customfields'])){
			$db = new db();
			echo "Deleting non existing custom field records.".$line_break.$line_break;
			$db->query("delete from cf_8 where link_id not in (select id from go_users);");
		}


		echo 'Done'.$line_break.$line_break;
	}

	function load_settings($response)
	{
		global $GO_MODULES, $GO_CONFIG;

		$user_id = isset($_REQUEST['user_id']) ? ($_REQUEST['user_id']) : 0;

		require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();

		$user = $GO_USERS->get_user($user_id);
		$response['data']=array_merge($response['data'], $user);
		$response['data']['birthday']=Date::format($response['data']['birthday'], false);
		$response['data']['start_module_name'] = isset($GO_MODULES->modules[$response['data']['start_module']]['humanName']) ? $GO_MODULES->modules[$response['data']['start_module']]['humanName'] : '';
		$response['data']['registration_time'] = Date::get_timestamp($response['data']['registration_time']);
		$response['data']['lastlogin'] = Date::get_timestamp($response['data']['lastlogin']);
	}

	function save_settings()
	{		
		global $lang, $GO_CONFIG, $GO_LANGUAGE;

		$user['id'] = isset($_POST['user_id']) ? trim($_POST['user_id']) : 0;

		require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();

		if(isset($_POST['first_name']))
		{
			$user['first_name'] = $_POST['first_name'];
			$user['middle_name'] = $_POST['middle_name'];
			$user['last_name'] = $_POST['last_name'];			
			$user['title'] = $_POST["title"];
			$user['initials'] = $_POST["initials"];
			$user['sex'] = $_POST["sex"];
			$user['birthday'] = Date::to_db_date($_POST['birthday']);
			$user['address'] = $_POST["address"];
			$user['address_no'] = $_POST["address_no"];
			$user['zip'] = $_POST["zip"];
			$user['city'] = $_POST["city"];
			$user['state'] = $_POST["state"];
			$user['country'] = $_POST["country"];

			$user['email'] = $_POST["email"];
			$user['home_phone'] = $_POST["home_phone"];
			$user['fax'] = $_POST["fax"];
			$user['cellular'] = $_POST["cellular"];						
			
			$user['company'] = $_POST["company"];
			$user['department'] = $_POST["department"];
			$user['function'] = $_POST["function"];
			$user['work_address'] = $_POST["work_address"];
			$user['work_address_no'] = $_POST["work_address_no"];
			$user['work_zip'] = $_POST["work_zip"];
			$user['work_city'] = $_POST["work_city"];
			$user['work_state'] = $_POST["work_state"];
			$user['work_country'] = $_POST["work_country"];
			$user['work_phone'] = $_POST["work_phone"];
			$user['work_fax'] = $_POST["work_fax"];
			$user['homepage'] = $_POST["homepage"];		

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
      $user['mute_reminder_sound'] = isset($_POST["mute_reminder_sound"]) ? '1' : '0';
      $user['mute_new_mail_sound'] = isset($_POST["mute_new_mail_sound"]) ? '1' : '0';
      $user['show_smilies'] = isset($_POST["show_smilies"]) ? '1' : '0';
      $user['mute_sound'] = isset($_POST["mute_sound"]) ? '1' : '0';
			$user['mail_reminders'] = isset($_POST["mail_reminders"]) ? '1' : '0';
			$user['popup_reminders'] = isset($_POST["popup_reminders"]) ? '1' : '0';
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
			require_once($GO_CONFIG->class_path.'base/auth.class.inc.php');
			$GO_AUTH = new GO_AUTH();
			
			if(!$GO_AUTH->authenticate($_SESSION['GO_SESSION']['username'], $_POST['current_password']))
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
		global $GO_CONFIG;
		require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();

		$users = new users();

		$sql = "SELECT id FROM go_users";
		$users->query($sql);

		while($record=$users->next_record())
		{
			$GO_USERS->cache_user($record['id']);
		}
	}

	public function get_users($group_id) {
		$sql = "SELECT * FROM go_users_groups ug ".
			"INNER JOIN go_users u ".
			"ON ug.user_id=u.id ".
			"WHERE ug.group_id='$group_id' ";
		$this->query($sql);
		return $this->num_rows();
	}

	public function get_user($id) {
		$sql = "SELECT * FROM go_users WHERE id='$id'";
		$this->query($sql);
		if ($this->num_rows()==1)
			return $this->next_record();
		else
			return false;
	}

}