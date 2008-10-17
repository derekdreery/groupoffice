<?php
/**
 * @copyright Copyright &copy; Intermesh 2003
 * @version $Revision: 1615 $ $Date: 2008-04-25 16:18:36 +0200 (vr, 25 apr 2008) $
 *
 * @author Merijn Schering <mschering@intermesh.nl>

 This file is part of Group-Office.

 Group-Office is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Group-Office is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Group-Office; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 * @package Users

 */

class users extends db
{
	function __on_load_settings(&$response)
	{
		global $GO_USERS, $GO_MODULES;
		
		$user_id = isset($_REQUEST['user_id']) ? smart_addslashes($_REQUEST['user_id']) : 0;

		$user = $GO_USERS->get_user($user_id);
		$response['data']=array_merge($response['data'], $user);
		$response['data']['birthday']=Date::format($response['data']['birthday'], false);
		$response['data']['start_module_name'] = isset($GO_MODULES->modules[$response['data']['start_module']]['humanName']) ? $GO_MODULES->modules[$response['data']['start_module']]['humanName'] : '';
		$response['data']['registration_time'] = Date::get_timestamp($response['data']['registration_time']);
		$response['data']['lastlogin'] = Date::get_timestamp($response['data']['lastlogin']);
	}

	function __on_save_settings()
	{
		global $GO_USERS, $lang;
		
		$user['id'] = isset($_POST['user_id']) ? smart_addslashes(trim($_POST['user_id'])) : 0;
		
		if(isset($_POST['first_name']))
		{
			$user['first_name'] = smart_addslashes(trim($_POST['first_name']));
			$user['middle_name'] = smart_addslashes(trim($_POST['middle_name']));
			$user['last_name'] = smart_addslashes(trim($_POST['last_name']));
			$user['email'] = smart_addslashes($_POST["email"]);
			$user['enabled'] = (isset($_POST['enabled'])) ? '1' : '0' ;
			$user['title'] = smart_addslashes($_POST["title"]);


			$user['initials'] = smart_addslashes($_POST["initials"]);
			$user['birthday'] = Date::to_db_date(smart_addslashes($_POST['birthday']));
			$user['work_phone'] = smart_addslashes($_POST["work_phone"]);
			$user['home_phone'] = smart_addslashes($_POST["home_phone"]);
			$user['fax'] = smart_addslashes($_POST["fax"]);
			$user['cellular'] = smart_addslashes($_POST["cellular"]);
			$user['country_id'] = smart_addslashes($_POST["country_id"]);
			$user['state'] = smart_addslashes($_POST["state"]);
			$user['city'] = smart_addslashes($_POST["city"]);
			$user['zip'] = smart_addslashes($_POST["zip"]);
			$user['address'] = smart_addslashes($_POST["address"]);
			$user['address_no'] = smart_addslashes($_POST["address_no"]);
			$user['department'] = smart_addslashes($_POST["department"]);
			$user['function'] = smart_addslashes($_POST["function"]);
			$user['company'] = smart_addslashes($_POST["company"]);
			$user['work_country_id'] = smart_addslashes($_POST["work_country_id"]);
			$user['work_state'] = smart_addslashes($_POST["work_state"]);
			$user['work_city'] = smart_addslashes($_POST["work_city"]);
			$user['work_zip'] = smart_addslashes($_POST["work_zip"]);
			$user['work_address'] = smart_addslashes($_POST["work_address"]);
			$user['work_address_no'] = smart_addslashes($_POST["work_address_no"]);
			$user['work_fax'] = smart_addslashes($_POST["work_fax"]);
			$user['homepage'] = smart_addslashes($_POST["homepage"]);
			$user['sex'] = smart_addslashes($_POST["sex"]);

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


		if(isset($_POST['theme']))
		{
			$user['theme'] = smart_addslashes($_POST["theme"]);

			$user['language'] = smart_addslashes($_POST["language"]);
			$user['max_rows_list'] = smart_addslashes($_POST["max_rows_list"]);
			$user['sort_name'] = smart_addslashes($_POST["sort_name"]);
			$user['start_module'] = smart_addslashes($_POST["start_module"]);
		}

		if($_POST['language'])
		{
			$user['language']=smart_addslashes($_POST['language']);
			$user['first_weekday'] = smart_addslashes($_POST["first_weekday"]);
			$user['date_format'] = smart_addslashes($_POST["date_format"]);
			$user['date_seperator'] = smart_addslashes($_POST["date_seperator"]);
			$user['decimal_seperator'] = smart_addslashes($_POST["decimal_seperator"]);
			$user['thousands_seperator'] = smart_addslashes($_POST["thousands_seperator"]);
			$user['time_format'] = smart_addslashes($_POST["time_format"]);
			$user['timezone'] = smart_addslashes($_POST["timezone"]);
			$user['currency'] = smart_addslashes($_POST["currency"]);
		}


		if (!empty($_POST["password1"]) || !empty($_POST["password2"]))
		{
			if(!$GO_USERS->check_password(smart_stripslashes($_POST['current_password'])))
			{
				throw new Exception($lang['common']['badPassword']);
			}

			if($_POST["password1"] != $_POST["password2"])
			{
				throw new Exception($lang['common']['passwordMatchError']);
			}
			if(!empty($_POST["password2"]))
			{
				$user['password']=smart_stripslashes($_POST["password2"]);
			}
		}

		$GO_USERS->update_user($user);

	}

	function __on_search($last_sync_time=0)
	{
		global $GO_MODULES, $GO_LANGUAGE, $GO_USERS;

		require($GO_LANGUAGE->get_language_file('users'));

		$sql = "SELECT DISTINCT go_users.*  FROM go_users WHERE mtime>$last_sync_time";

		$this->query($sql);

		$search = new search();

		while($this->next_record())
		{
			$cache['id']=$this->f('id');
			$cache['user_id']=1;
			$cache['name'] = addslashes(String::format_name($this->f('last_name'),$this->f('first_name'),$this->f('middle_name')));
			$cache['link_type']=8;
			$cache['description']='';
			$cache['type']=$us_user;
			$cache['keywords']=addslashes($search->record_to_keywords($this->Record)).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');
			$cache['module']='users';
			$cache['acl_read']=$GO_MODULES->modules['users']['acl_read'];
			$cache['acl_write']=$GO_MODULES->modules['users']['acl_write'];
				
			$search->cache_search_result($cache);
		}
	}
	
	function __on_check_database(){
		global $GO_CONFIG, $GO_MODULES, $GO_LANGUAGE;
		
		echo 'Checking users folder permissions<br />';

		if(isset($GO_MODULES->modules['files']))
		{
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc');
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
		echo 'Done<br /><br />';
	}
}