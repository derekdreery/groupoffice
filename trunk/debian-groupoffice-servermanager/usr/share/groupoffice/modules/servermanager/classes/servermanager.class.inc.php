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


if(file_exists($GLOBALS['GO_CONFIG']->root_path.'modules/professional/check.php'))
	require_once($GLOBALS['GO_CONFIG']->root_path.'modules/professional/check.php');

class servermanager extends db {

	/*
	 * Don't change this variable or pro installations won't work anymore!
	 */
	var $pro_modules = array('mailings','projects','gota','sync','customfields','billing','tickets');

	public function __on_load_listeners($events) {
		$events->add_listener('update_user', __FILE__, 'servermanager', 'update_user');
	}

	public function get_all_config_files($roots){
		global $GO_CONFIG;

		require_once($GLOBALS['GO_CONFIG']->class_path.'filesystem.class.inc');
		$fs = new filesystem();

		$configs=array();
		$config_files=array();

		if(file_exists('/etc/groupoffice/config.php')){
			$configs[]=array('name'=>'servermanager','conf'=>'/etc/groupoffice/config.php');
			$config_files[]='/etc/groupoffice/config.php';
		}
		foreach($roots as $root) {
			$folders = $fs->get_folders($root);

			echo 'Finding config files in '.$root."\n";

			//var_dump($folders);

			

			foreach($folders as $folder) {

				$config=false;

				$conf = '/etc/groupoffice/'.$folder['name'].'/config.php';
				if(file_exists($conf)) {
					$config=array('name'=>$folder['name'], 'conf'=>$conf);
				}else {
					$conf = $folder['path'].'/html/groupoffice/config.php';
					if(file_exists($conf)) {
						$config=array('name'=>$folder['name'], 'conf'=>$conf);
					}else
					{
						$conf = $folder['path'].'/config.php';
						if(file_exists($conf)) {
							$config=array('name'=>$folder['name'], 'conf'=>$conf);
						}
					}
				}
				if($config && !in_array($config['conf'], $config_files)){
					$config_files[]=$config['conf'];
					$configs[]=$config;
				}
			}
		}
		return $configs;
	}

	/**
	 *
	 * Creates a report about the usage. Don't mess with this because the professional
	 * installations will fail if you change things here.
	 *
	 * @global <type> $GO_CONFIG
	 * @global <type> $GO_MODULES
	 * @param <type> $name
	 * @param <type> $config_file
	 * @param <type> $sc
	 *
	 */


	function create_report($name, $config_file, $sc=false) {

		global $GO_CONFIG;

		echo "Creating report for ".$name."\n";

		$config=array();
		if(!file_exists($config_file)) {
			echo "Warning: ".$config_file." does not exist\n";
			return false;
		}
		require($config_file);

		$installation=array();
		$installation['name']=$name;
		$installation['ctime']=time();
		$installation['comment']='';
		$installation['config_file']=$config_file;

		if($installation['name']=='servermanager'){
			$installation['status']='ignore';
		}

		$features=array();

		$db2 = new db();
		$db2->halt_on_error='report';

		if(empty($config['db_name'])) {
			echo 'Warning: empty db_name in '.$conf['conf']."\n";
		}else {
			$db2->halt_on_error = "no";
			$use = $db2->query("USE `".$config['db_name']."`");
			$db2->halt_on_error='report';
			if(!$use) {
				$installation['comment'] .= 'Database '.$config['db_name'].' not found for: '.$name;
			}else {
				$users_table='';

				$db2->query("SHOW TABLE STATUS FROM `".$config['db_name']."`;");

				$projects_table='';
				$billing_table='';

				$installation['billing']=0;

				$modules_table='modules';
				$installation['database_usage']=0;
				while($db2->next_record(DB_BOTH)) {

					if($db2->f(0)=='go_users') {
						$users_table='go_users';
					}

					if($db2->f(0)=='go_modules') {
						$modules_table='go_modules';
					}


					if($db2->f(0)=='users') {
						$users_table='users';
					}

					if($db2->f(0)=='pm_projects' || $db2->f(0)=='pmProjects') {
						$projects_table=$db2->f(0);
					}

					if($db2->f(0)=='bs_orders') {
						$billing_table=$db2->f(0);
						$installation['billing']=1;
					}

					$installation['database_usage']+=$db2->f('Data_length');
					$installation['database_usage']+=$db2->f('Index_length');
				}

				$installation['decimal_separator']=$config['default_decimal_separator'];
				$installation['thousands_separator']=$config['default_thousands_separator'];
				$installation['date_format']=Date::get_dateformat($config['default_date_format'], $config['default_date_separator']);

				$installation['mail_domains']=isset($config['serverclient_domains']) ? $config['serverclient_domains'] : '';
				$installation['file_storage_usage']=File::get_directory_size($config['file_storage_path']);
				$installation['max_users']=$config['max_users'];

				if(empty($users_table)) {
					echo "Warning: No users table found in ".$conf['conf']."\n";
					$this->query('USE `'.$this->database.'`');
				}else {

					$installation['database_usage']=$installation['database_usage']/1024;

					$db2->query("SELECT * FROM $modules_table WHERE id IN ('".implode("','", $this->pro_modules)."')");
					$installation['professional']=$db2->next_record() ? '1' : '0';


					$sql = "SELECT count(*) AS count_users, MIN(registration_time) AS install_time, MAX(lastlogin) AS lastlogin, SUM(logins) AS total_logins FROM  $users_table";
					$db2->query($sql);
					$record = $db2->next_record();
					if($record){
						foreach($record as $key=>$value) {
							if(empty($value)) {
								$record[$key]=0;
							}
						}
					}else
					{
						//can go wrong when table structure changes
						$record=array();
					}

					$installation = array_merge($record, $installation);

					$sql = "SELECT sex, email, first_name, middle_name, last_name, country FROM $users_table WHERE id=1";
					$db2->query($sql);
					$record = $db2->next_record();

					$installation['admin_email']=empty($record['email']) ? '' : $record['email'];
					$installation['admin_name']=String::format_name($record);
					$installation['admin_country']=empty($record['country']) ? '' : $record['country'];

					$middle = $db2->f('middle_name') == '' ? ' ' : ' '.$db2->f('middle_name').' ';

					$sex = $db2->f('sex')=='' ? 'M' : $db2->f('sex');
					$installation['admin_salutation']=$GLOBALS['lang']['common']['default_salutation'][$sex].$middle.$db2->f('last_name');


					if(!empty($billing_table)) {
						$sql = "SELECT count(*) AS count FROM bs_orders";
						$db2->query($sql);
						$db2->next_record();
						$features[]='orders:'.$db2->f('count');
					}

					if(!empty($projects_table)) {
						$sql = "SELECT count(*) AS count FROM $projects_table";
						$db2->query($sql);
						$db2->next_record();
						$features[]='projects:'.$db2->f('count');
					}
					$installation['features']=implode(',', $features);



					$installation['mailbox_usage']=0;

					if(!empty($GLOBALS['GO_CONFIG']->serverclient_server_url) && !empty($config['serverclient_domains'])) {
						if(!$sc) {
							global $GO_MODULES;
							require_once($GLOBALS['GO_MODULES']->modules['serverclient']['class_path'].'serverclient.class.inc.php');
							$sc = new serverclient();
						}

						$sc->login();

						$params=array(
								'task'=>'serverclient_get_usage',
								'domains'=>$config['serverclient_domains']
						);

						$response = $sc->send_request($sc->server_url.'modules/postfixadmin/json.php', $params);
						$response = json_decode($response, true);

						//debug(var_export($response, true));


						foreach($response['domains'] as $domain) {
							$installation['mailbox_usage']+=$domain['usage'];
						}
					}

					if($users_table=='go_users') {
						$db2->query("REPLACE INTO go_settings VALUES(0, 'usage_date', '".time()."')");
						$db2->query("REPLACE INTO go_settings VALUES(0, 'mailbox_usage', '".$installation['mailbox_usage']."')");
						$db2->query("REPLACE INTO go_settings VALUES(0, 'file_storage_usage', '".$installation['file_storage_usage']."')");
						$db2->query("REPLACE INTO go_settings VALUES(0, 'database_usage', '".$installation['database_usage']."')");
					}
				}
			}
			$this->query('USE `'.$this->database.'`');

			//var_dump($installation);

			$old_installation = $this->get_installation_by_name($installation['name']);



			if($old_installation) {
				$installation['id']=$old_installation['id'];
				$this->update_installation($installation);

				$old_installation = $this->get_installation($installation['id']);

				switch($old_installation['status']) {
					case 'ignore':
					//do nothing

						break;

					case 'warntrial1':
					//send mail after 30 days
						$time = Date::date_add(time(), -10);
						if($old_installation['status_change_time']<$time) {
							$this->change_status($old_installation, 'warntrial2');
						}
						break;
					case 'warntrial2':
					//send mail after 40 days
						$time = Date::date_add(time(), -10);
						$time2 = Date::date_add(time(), -40);
						if($old_installation['status_change_time']<$time || $old_installation['install_time']<$time2) {
							$this->delete_installation($old_installation['id']);
						}
						break;

					case 'trial':
					//send mail after 20 days
						$time = Date::date_add(time(), -20);
						if($old_installation['status_change_time']<$time) {
							$this->change_status($old_installation, 'warntrial1');
						}
						break;
				}


			}else {
				$installation['status']='ignore';
				$this->add_installation($installation);
			}


		}

	}


	function update_user($user) {
		if($user['id']==1 && !empty($user['password'])) {
			global $GO_CONFIG, $GO_MODULES;

			$cmd='sudo '.$GLOBALS['GO_MODULES']->modules['servermanager']['path'].'sudo.php --go_config='.
					$GLOBALS['GO_CONFIG']->get_config_file().' --task=change_admin_password --password="'.$user['password'].'"';

			system($cmd);
		}
	}

	function check_license($config, $existing_installation_name='') {

		if(!function_exists('license_get_max_server_users')){
			/*
			 * Users can break the license mechanism easily in the servermanager
			 * but that doesn't really matter because the installations will
			 * check the license in a hacker proof way.
			 */
			return true;
		}

		$pro = 0;
		if(isset($config['allowed_modules'])) {
			$allowed_modules = explode(',', $config['allowed_modules']);

			foreach($this->pro_modules as $pro_module) {
				if(in_array($pro_module, $allowed_modules)) {
					$pro=1;
					break;
				}
			}
			/*if(in_array('billing', $allowed_modules)) {
				$available_billing_modules = $this->billing_modules_available($existing_installation_name);
				if(!$available_billing_modules) {
					throw new Exception('You don\'t have a billing module left');
				}
			}*/
		}
		if($pro) {
			$available_users = $this->server_users_available($existing_installation_name);
			if($available_users<$config['max_users']) {
				throw new Exception('You don\'t have enough user licenses. You have '.$available_users.' left');
			}
		}



		return true;
	}

	function get_used_licenses() {
		$sql = "SELECT SUM(max_users) AS total_users FROM sm_installations WHERE professional=1";
		$this->query($sql);
		return $this->next_record();
	}

	function get_max_server_users(){
		global $GO_CONFIG;

		$max_users=0;
		if(isset($GLOBALS['GO_CONFIG']->test_servermanager)){
			$max_users=2;
		}elseif(extension_loaded('ionCube Loader') && function_exists('license_get_max_server_users')){
			$max_users=license_get_max_server_users();
		}else
		{
			//Ioncube not used. Must be a very reliable person working with unencoded pro files!
			$max_users=9999999;
		}
		return $max_users;
	}

	function server_users_available($installation_name='') {
		$sql = "SELECT SUM(max_users) AS total_users FROM sm_installations WHERE professional=1";

		if(!empty($installation_name)) {
			$sql .= " AND name!='".$this->escape($installation_name)."'";
		}

		$this->query($sql);
		$report = $this->next_record();

		$max_users=$this->get_max_server_users();

		return $max_users-$report['total_users'];
	}

	/*function billing_modules_available($installation_name='') {
		$sql = "SELECT SUM(billing) AS total_billing FROM sm_reports";

		if(!empty($installation_name)) {
			$sql .= " WHERE name='".$this->escape($installation_name)."'";
		}

		$this->query($sql);
		$report = $this->next_record();

		if(file_exists('/etc/groupoffice/license.inc.php'))
			require('/etc/groupoffice/license.inc.php');
		else
			$max['billing']=0;

		return $max['billing']-$report['total_billing'];
	}*/

	/**
	 * Add a New trial
	 *
	 * @param Array $new_trial Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_new_trial($new_trial) {

		$new_trial['ctime']=time();

		$time = Date::date_add(time(), -2);
		$sql = "DELETE FROM sm_new_trials WHERE ctime<$time";
		$this->query($sql);

		if($this->insert_row('sm_new_trials', $new_trial)) {
			return true;
		}
		return false;
	}
	/**
	 * Update a New trial
	 *
	 * @param Array $new_trial Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_new_trial($new_trial) {
		return $this->update_row('sm_new_trials', 'id', $new_trial);
	}

	/**
	 * Delete a New trial
	 *
	 * @param Int $new_trial_id ID of the new_trial
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_new_trial($name) {
		return $this->query("DELETE FROM sm_new_trials WHERE name='".$this->escape($name)."'");
	}



	function get_new_trial_by_name($name) {
		$this->query("SELECT * FROM sm_new_trials WHERE name='".$this->escape($name)."'");
		if($this->next_record()) {
			return $this->record;
		}
		return false;
	}

	function get_new_trial_by_key($key) {
		$this->query("SELECT * FROM sm_new_trials WHERE `key`='".$this->escape($key)."'");
		if($this->next_record()) {
			return $this->record;
		}
		return false;
	}


	function write_config($file, $values) {

		require($file);

		if(!isset($config))
			$config=array();

		foreach($values as $key=>$value) {
			$config[$key]=$value;
		}

		$config_data = "<?php\n";
		foreach($config as $key=>$value) {
			if($value===true) {
				$config_data .= '$config[\''.$key.'\']=true;'."\n";
			}elseif($value===false) {
				$config_data .= '$config[\''.$key.'\']=false;'."\n";
			}else {
				$config_data .= '$config[\''.$key.'\']="'.str_replace('"','\\"', $value).'";'."\n";
			}
		}
		return file_put_contents($file, $config_data);
	}

	/**
	 * Add a Report
	 *
	 * @param Array $report Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_report($report) {
		if($this->insert_row('sm_reports', $report)) {
			return true;
		}
		return false;
	}
	/**
	 * Update a Report
	 *
	 * @param Array $report Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_report($report) {
		return $this->update_row('sm_reports', 'name', $report);
	}

	/**
	 * Delete a Report
	 *
	 * @param Int $report_id ID of the report
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_report($name) {
		return $this->query("DELETE FROM sm_reports WHERE name='".$this->escape($name)."'");
	}

	/**
	 * Gets a Report record
	 *
	 * @param Int $report_id ID of the report
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_report($name) {
		$this->query("SELECT * FROM sm_reports WHERE name='".$this->escape($name)."'");
		if($this->next_record()) {
			return $this->record;
		}else {
			return false;
		}
	}



	/**
	 * Gets all Reports
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_reports($query, $sortfield='id', $sortorder='ASC', $start=0, $offset=0) {
		$sql = "SELECT sm_reports.*, (file_storage_usage+mailbox_usage+database_usage) AS total_usage FROM sm_reports ";

		if(!empty($query)) {
			$sql .= " WHERE name LIKE '".$this->escape($query)."'";
		}

		$sql .= " ORDER BY ".$this->escape($sortfield." ".$sortorder);
		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0) {
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}

	/**
	 * Add a Installation
	 *
	 * @param Array $installation Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_installation($installation) {
		$installation['ctime']=$installation['mtime']=time();

		$installation['status_change_time']=time();



		$installation['id']=$this->nextid('sm_installations');
		if($this->insert_row('sm_installations', $installation)) {
			return $installation['id'];
		}
		return false;
	}
	/**
	 * Update a Installation
	 *
	 * @param Array $installation Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_installation($installation, $update_field='id') {
		$installation['mtime']=time();

		return $this->update_row('sm_installations', $update_field, $installation);
	}

	/**
	 * Delete a Installation
	 *
	 * @param Int $installation_id ID of the installation
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_installation($installation_id) {
		global $GO_CONFIG, $GO_MODULES;


		$installation = $this->get_installation($installation_id);
		$cmd = 'sudo '.$GLOBALS['GO_MODULES']->modules['servermanager']['path'].'sudo.php --go_config='.$GLOBALS['GO_CONFIG']->get_config_file().' --task=remove --name='.$installation['name'];

		exec($cmd, $output, $return_var);

		if($return_var!=0) {
			throw new Exception(implode('<br />', $output));
		}

		return $this->query("DELETE FROM sm_installations WHERE id=".$this->escape($installation_id));
	}

	/**
	 * Gets a Installation record
	 *
	 * @param Int $installation_id ID of the installation
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_installation($installation_id) {
		$this->query("SELECT * FROM sm_installations WHERE id=".$this->escape($installation_id));
		if($this->next_record()) {
			return $this->record;
		}else {
			throw new DatabaseSelectException();
		}
	}
	/**
	 * Gets a Installation record by the name field
	 *
	 * @param String $name Name of the installation
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_installation_by_name($name) {
		$this->query("SELECT * FROM sm_installations WHERE name='".$this->escape($name)."'");
		if($this->next_record()) {
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets all Installations
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_installations($query='', $sortfield='id', $sortorder='ASC', $start=0, $offset=0) {
		$sql = "SELECT sm_installations.*, (file_storage_usage+mailbox_usage+database_usage) AS total_usage FROM sm_installations ";

		if(!empty($query)) {
			$sql .= " WHERE name LIKE '".$this->escape($query)."'";
		}

		$sql .= " ORDER BY ".$this->escape($sortfield." ".$sortorder);
		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0) {
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}

	function get_status_message($status) {
		global $GO_MODULES;

		$file = $GLOBALS['GO_MODULES']->modules['servermanager']['path'].'templates/'.$status.'.txt';

		$text = file_get_contents($file);

		$lines = explode("\n", $text);

		$message['subject'] = array_shift($lines);
		$message['body']=trim(implode("\n", $lines));

		return $message;
	}


	function change_status($installation, $status) {
		global $GO_LANGUAGE, $GO_MODULES,$GO_CONFIG;

		require('/etc/groupoffice/servermanager.inc.php');

		require_once($GLOBALS['GO_CONFIG']->class_path.'mail/GoSwift.class.inc.php');

		$message = $this->get_status_message($status);
		if($message && !empty($installation['admin_email'])) {

			$_body = str_replace('{admin_salutation}', $installation['admin_salutation'], $message['body']);
			$_body = str_replace('{name}',$installation['name'], $_body);

			$swift = new GoSwift($installation['admin_email'], $message['subject'],0,0,'3',$_body);
			if(!empty($sm_config['bcc_email']))
				$swift->message->addBcc($sm_config['bcc_email']);
			$swift->set_from($sm_config['sender_email'], $sm_config['sender_name']);
			$swift->sendmail();


			//Interesting prospect!
			if($status=='warntrial1' && $installation['total_logins']>10 && $installation['last_login']>Date::date_add(time(),-7) ){

				$body='Name: '.$installation['name'].'<br />'.
				'Admin name: '.$installation['admin_salutation'].'<br />'.
				'Admin e-mail: '.$installation['admin_email'].'<br />';

				$swift = new GoSwift('info@intermesh.nl', 'Interesting prospect with trial',0,0,'3',$body);
				$swift->set_from($sm_config['sender_email'], $sm_config['sender_name']);
				$swift->sendmail();
			}

		}

		$sql = "UPDATE sm_installations SET status='".$this->escape($status)."', status_change_time='".time()."' WHERE id=".$this->escape($installation['id']);

		$this->query($sql);
	}



	/* {CLASSFUNCTIONS} */

}
