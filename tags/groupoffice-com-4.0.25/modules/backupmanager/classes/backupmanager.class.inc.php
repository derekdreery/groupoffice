<?php

class backupmanager extends db {

	public function __on_load_listeners($events) {
	}

	/**
	 * Get backup settings
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_settings() {
		$this->query("SELECT * FROM bm_settings");

		if($this->next_record()) {
			return $this->record;
		}
		else {
			return false;
		}
	}

	/**
	 * Save backup settings
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function save_settings($settings) {
		return $this->update_row('bm_settings','id', $settings);
	}

	function get_mysql_config($check_only=false) {
		$files = array(
						array('type'=>'debian', 'file'=>'/etc/mysql/debian.cnf'),
						array('type'=>'servermanager', 'file'=>'/etc/groupoffice/servermanager.inc.php'),
						array('type'=>'backup', 'file'=>'/etc/groupoffice/backupmanager.inc.php')
		);

		for($i=0,$found=false; $i<count($files) && !$found; $i++) {
			$file = $files[$i]['file'];
			if(is_file($file)) {
				$found = true;
				$type = $files[$i]['type'];
			}
		}

		if($found) {
			if($check_only) {
				return true;
			}

			switch($type) {
				case 'debian':
					$lines = file($file);

					for($i=0,$client=false,$found=false; $i<count($lines) && !$found; $i++) {
						$line = $lines[$i];
						if($line=="[client]\n") {
							$client = true;
						}else
						if($line=="[mysql_upgrade]\n") {
							$client = false;
						}

						if($client) {
							if(strpos($line, 'user') === 0) {
								$start = strpos($line, '=')+2;
								$user = substr($line, $start, strlen($line)-$start-1);
							}else
							if(strpos($line, 'password') === 0) {
								$start = strpos($line, '=')+2;
								$password = substr($line, $start, strlen($line)-$start-1);
							}

							if(isset($user) && isset($password)) {
								$found = true;
							}
						}
					}

					break;

				case 'servermanager':
					require_once($file);
					$user = $sm_config['mysql_user'];
					$password = $sm_config['mysql_pass'];
					break;

				case 'backup':
					require_once($file);
					$user = $bm_config['mysql_user'];
					$password = $bm_config['mysql_pass'];
					break;
			}

			if(isset($user) && isset($password)) {
				return array('user' => $user, 'pass' => $password);
			}else {
				return false;
			}
		}else {
			return false;
		}
	}


}