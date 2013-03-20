<?php

require_once("../../Group-Office.php");
$GLOBALS['GO_SECURITY']->json_authenticate('backupmanager');

require_once ($GLOBALS['GO_MODULES']->modules['backupmanager']['class_path'] . 'backupmanager.class.inc.php');

$backupmanager = new backupmanager();
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

require($GLOBALS['GO_LANGUAGE']->get_language_file('backupmanager'));

try {
	switch ($task) {
		case 'disable_running':
			$settings['id'] = 1;
			$settings['running'] = (isset($_REQUEST['running'])) ? $_REQUEST['running'] : '';
			echo "disable running";
			$response['success'] = $backupmanager->save_settings($settings);

			break;


		case 'save_settings':

			// TO-DO: check input before updating. Strip trailing slashes from folders.
			$settings['id'] = 1;
			$settings['rmachine'] = (isset($_REQUEST['rmachine']) && $_REQUEST['rmachine']) ? $_REQUEST['rmachine'] : '';
			$settings['rport'] = (isset($_REQUEST['rport']) && intval($_REQUEST['rport'])) ? $_REQUEST['rport'] : '';
			$settings['ruser'] = (isset($_REQUEST['ruser']) && $_REQUEST['ruser']) ? $_REQUEST['ruser'] : '';
			$settings['rtarget'] = (isset($_REQUEST['rtarget']) && $_REQUEST['rtarget']) ? $_REQUEST['rtarget'] : '';
			$settings['sources'] = (isset($_REQUEST['sources']) && $_REQUEST['sources']) ? $_REQUEST['sources'] : '';
			$settings['rotations'] = (isset($_REQUEST['rotations']) && intval($_REQUEST['rotations'])) ? $_REQUEST['rotations'] : '';
			$settings['emailaddress'] = (isset($_REQUEST['emailaddress']) && $_REQUEST['emailaddress']) ? $_REQUEST['emailaddress'] : '';
			$settings['emailsubject'] = (isset($_REQUEST['emailsubject']) && $_REQUEST['emailsubject']) ? $_REQUEST['emailsubject'] : '';

			if (!$settings['rmachine'] || !$settings['rport'] || !$settings['ruser'] || !$settings['rtarget'] || !$settings['sources'] || !$settings['rotations'] || !$settings['emailaddress'] || !$settings['emailsubject']) {
				throw new Exception($lang['common']['missingField']);
			}

			if (!$backupmanager->save_settings($settings)) {
				$response['feedback'] = $lang['backupmanager']['save_error'];
			} else
			if (!$backupmanager->get_mysql_config(true)) {
				$response['feedback'] = $lang['backupmanager']['no_mysql_config'];
			}

			$response['success'] = isset($response['feedback']) ? false : true;

			break;


		case 'scp_key':

			$rpassword = (isset($_REQUEST['rpassword']) && $_REQUEST['rpassword']) ? $_REQUEST['rpassword'] : '';
			$rmachine = (isset($_REQUEST['rmachine']) && $_REQUEST['rmachine']) ? $_REQUEST['rmachine'] : '';
			$rport = (isset($_REQUEST['rport']) && $_REQUEST['rport']) ? $_REQUEST['rport'] : '';
			$ruser = (isset($_REQUEST['ruser']) && $_REQUEST['ruser']) ? $_REQUEST['ruser'] : '';

			if (!$rpassword || !$rmachine || !$rport || !$ruser) {
				throw new Exception($lang['common']['missingField']);
			}

			require_once($GLOBALS['GO_MODULES']->modules['backupmanager']['class_path'] . 'phpseclib/Net/SSH2.php');
			$ssh = new Net_SSH2($rmachine, $rport);

			if ($ssh->login($ruser, $rpassword)) {
				if (!file_exists($GLOBALS['GO_CONFIG']->file_storage_path . '.ssh/id_rsa.pub')) {
					if (!file_exists($GLOBALS['GO_CONFIG']->file_storage_path . '.ssh')) {
						mkdir($GLOBALS['GO_CONFIG']->file_storage_path . '.ssh', 0700);
					}

					exec('ssh-keygen -q -f ' . $GLOBALS['GO_CONFIG']->file_storage_path . '.ssh/id_rsa -N "" -P ""', $o, $r);
				}

				$key_content = file_get_contents($GLOBALS['GO_CONFIG']->file_storage_path . '.ssh/id_rsa.pub');

				// check of doelmap bestaat
				$test_targetdir = trim($ssh->exec("test -d '" . escapeshellarg($_REQUEST['rtarget']) . "' || echo 'false'"));
				//var_dump($test_targetdir);
				if ($test_targetdir == 'false') {
					$response['feedback'] = $lang['backupmanager']['target_does_not_exist'];
				}

				if ($key_content) {
					$ssh->exec("umask 077; test -d .ssh || mkdir .ssh && touch .ssh/authorized_keys && chmod 600 .ssh/authorized_keys;");
					$ssh->exec('echo "' . $key_content . '" >> .ssh/authorized_keys');
				} else {
					$response['feedback'] = $lang['backupmanager']['empty_key'];
				}
			} else {
				$response['feedback'] = $lang['backupmanager']['connection_error'];
			}

			if (!isset($response['feedback'])) {
				$settings['id'] = 1;
				$settings['running'] = 1;

				$response['success'] = $backupmanager->save_settings($settings);
				$GLOBALS['GO_CONFIG']->save_setting('backupmanager_first_run', true);
			} else {
				$response['success'] = false;
			}
			break;
	}
} catch (Exception $e) {
	$response['feedback'] = $e->getMessage();
	$response['success'] = false;
}

echo json_encode($response);