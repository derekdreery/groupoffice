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
		global $GO_USERS, $lang;
		
		$user['id'] = isset($_POST['user_id']) ? (trim($_POST['user_id'])) : 0;
		
		if(isset($_POST['first_name']))
		{
			$user['first_name'] = $_POST['first_name'];
			$user['middle_name'] = $_POST['middle_name'];
			$user['last_name'] = $_POST['last_name'];
			$user['email'] = $_POST["email"];
			$user['enabled'] = isset($_POST['enabled']) ? '1' : '0' ;
			$user['title'] = $_POST["title"];


			$user['initials'] = $_POST["initials"];
			$user['birthday'] = Date::to_db_date($_POST['birthday']);
			$user['work_phone'] = $_POST["work_phone"];
			$user['home_phone'] = $_POST["home_phone"];
			$user['fax'] = $_POST["fax"];
			$user['cellular'] = $_POST["cellular"];
			$user['country_id'] = $_POST["country_id"];
			$user['state'] = $_POST["state"];
			$user['city'] = $_POST["city"];
			$user['zip'] = $_POST["zip"];
			$user['address'] = $_POST["address"];
			$user['address_no'] = $_POST["address_no"];
			$user['department'] = $_POST["department"];
			$user['function'] = $_POST["function"];
			$user['company'] = $_POST["company"];
			$user['work_country_id'] = $_POST["work_country_id"];
			$user['work_state'] = $_POST["work_state"];
			$user['work_city'] = $_POST["work_city"];
			$user['work_zip'] = $_POST["work_zip"];
			$user['work_address'] = $_POST["work_address"];
			$user['work_address_no'] = $_POST["work_address_no"];
			$user['work_fax'] = $_POST["work_fax"];
			$user['homepage'] = $_POST["homepage"];
			$user['sex'] = $_POST["sex"];

			if(empty($user['email']) || empty($user['first_name']) || empty($user['last_name']))
			{
				throw new MissingFieldException();
			}

			if (!String::validate_email($user['email'])) {
				throw new Exception($error_email);
			}

			$existing_email_user = $GO_CONFIG->allow_duplicate_email ? false : $GO_USERS->get_user_by_email($user['email']);

			if ($existing_email_user && ($user_id == 0 || $existing_email_user['id'] != $user_id)) {
				{
					throw new Exception($error_email_exists);
				}
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

		if($_POST['language'])
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
	
	public function __on_build_search_index()
	{
		global $GO_USERS;
		
		$sql = "SELECT id FROM go_users";
		$this->query($sql);
		
		while($record=$this->next_record())
		{
			$GO_USERS->cache_user($record['id']);
		}

		/* {ON_BUILD_SEARCH_INDEX_FUNCTION} */
	}
	
	function __on_check_database(){
		/*global $GO_CONFIG, $GO_MODULES, $GO_LANGUAGE;
		
		echo 'Checking users folder permissions<br />';

		if(isset($GO_MODULES->modules['files']))
		{
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$fs = new files();

			$sql = "SELECT last_name, id FROM go_users";
			$this->query($sql);
			while($this->next_record())
			{
				echo 'Checking '.$this->f('last_name').'<br />';				
				$full_path = $GO_CONFIG->file_storage_path.'users/'.$this->f('id');
				$fs->check_share($full_path, 1, $GO_MODULES->modules['users']['acl_read'], $GO_MODULES->modules['users']['acl_write']);
			}
		}
		echo 'Done<br /><br />';>*/
	}
}