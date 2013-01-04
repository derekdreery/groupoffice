#!/usr/bin/php
<?php
chdir(dirname(__FILE__));
define('CONFIG_FILE', $argv[1]);

require_once('../../Group-Office.php');

$task = $argv[2];
$user_home_dirs = isset($GO_CONFIG->user_home_dirs) ? $GO_CONFIG->user_home_dirs : '/home/';


function get_string($output, $status)
{
	$lines = count($output);
	if($lines)
	{
		$str = '';
		for($i=0; $i<$lines; $i++)
		{
			$str .= $output[$i]."<br />";
		}
	}else
	{
		$str = 'Error code: '. $status;
	}

	return $str;
}


switch($task)
{
	case 'add_user':

		$username = $argv[3];
		$password = $argv[4];

		exec('useradd -m '.$username. ' 2>&1', $output, $status);
		if($status)
		{		
			exit(get_string($output, $status));
		}
			
		exec('echo '.$username.':'.$password.' | '.$GO_CONFIG->cmd_chpasswd);

		if(!empty($GO_CONFIG->cmd_edquota) && !empty($GO_CONFIG->quota_protouser))
		{
			exec($GO_CONFIG->cmd_edquota.' -p '.$GO_CONFIG->quota_protouser.' '.$GO_CONFIG->id.'_'.$username);
		}

		break;

	case 'update_user':

		require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();

		$user_id = $argv[3];
		$password = $argv[4];
		$GO_USERS->get_user($user_id);
		$username = $GO_USERS->f('username');

		//strip domain from username
		$arr = explode('@', $username);
		$username = $arr[0];

		exec('echo '.$username.':'.$password.' | '.$GO_CONFIG->cmd_chpasswd.' 2>&1', $output, $status);
		if($status)
		{
			exit(get_string($output, $status));
		}

		if(isset($GO_MODULES->modules['email'])){
			require_once($GO_MODULES->modules['email']['class_path'].'email.class.inc.php');
			$email = new email();

			$email->update_password('localhost', $username, $password);
		}
		
		break;

	case 'delete_user':

		$username = $argv[3];
		
		exec('userdel '.$username.' 2>&1', $output, $status);
		if($status)
		{
			exit(get_string($output, $status));
		}

		break;

	case 'set_vacation':

		$account_id=$argv[3];

		require_once($GO_CONFIG->class_path.'mail/RFC822.class.inc');
		$RFC822 = new RFC822();

		require_once($GO_MODULES->modules['email']['class_path'].'email.class.inc.php');
		$email = new email();

		require_once($GO_MODULES->modules['systemusers']['class_path'].'systemusers.class.inc.php');
		$su = new systemusers();
		
		require_once($GO_MODULES->modules['systemusers']['path'].'vacation_functions.php');

		$account = $email->get_account($account_id);
		if($account)
		{
			$homedir = $user_home_dirs.$account['username'];

			if (file_exists ($homedir)) {
				$vacation_file = $homedir.'/.vacation.msg';
				$db_file = $homedir.'/.vacation.db';
				$forward_file = $homedir.'/.forward';

				$vacation = $su->get_vacation($account_id);

				// remove vacation reference in forward file if module is being uninstalled
				if(isset($argv[4]) && $argv[4] == 'uninstall')
					$vacation['vacation_active'] = 0;
		
				if($vacation['vacation_active'])
				{
					$vacation_file_contents =
					'From: '.$RFC822->write_address($account['name'], $account['email'])."\n".
					'Subject: '.$vacation['vacation_subject']."\n\n".$vacation['vacation_body'];

					/* update .forward file */
					excludeVacation ($forward_file, $account['username'], $account['email']);
					includeVacation ($forward_file, $account['username'], $account['email']);

					

					writeFile ($vacation_file, $vacation_file_contents);
					/* update vacation database */
					//copy ($empty_db_file, $db_file);

					chown($vacation_file, $account['username']);
					//chown($db_file, $account['username']);				

					chmod($vacation_file, 0640);
				}else {
					/* update .forward file */
					excludeVacation ($forward_file, $account['username'], $account['email']);
					if(file_exists($vacation_file)) unlink($vacation_file);
					if(file_exists($db_file)) unlink($db_file);
				}

				go_debug($forward_file);

				removeForward($forward_file);
				if(!empty($vacation['forward_to']))
					includeForward($forward_file, $vacation['forward_to'], $account['username']);

				chown($forward_file, $account['username']);
				chmod($forward_file, 0640);
				
			}
		}

		break;

}

?>
