#!/usr/bin/php
<?php

require_once('../../Group-Office.php');

$task = $argv[1];
$user_home_dirs = isset($GO_CONFIG->user_home_dirs) ? $GO_CONFIG->user_home_dirs : '/home/';

switch($task)
{
	case 'set_vacation':

		$account_id=$argv[2];

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
				if(isset($argv[3]) && $argv[3] == 'uninstall')
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
					chown($forward_file, $account['username']);

					chmod($vacation_file, 0640);
					//chmod($db_file, 0640);
					chmod($forward_file, 0640);

				}
				else {
					/* update .forward file */
					excludeVacation ($forward_file, $account['username'], $account['email']);
					if(file_exists($vacation_file)) unlink($vacation_file);
					if(file_exists($db_file)) unlink($db_file);
				}
			}
		}

		break;

}

?>
