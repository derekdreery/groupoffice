#!/usr/bin/php
<?php
/**
 * @copyright Copyright &copy; Intermesh 2007
 * @version 1.0 20/07/2007
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

 * @package Framework
 */

require_once('Group-Office.php');

$task = $argv[1];

switch($task)
{
	case 'set_vacation':

		$account_id=$argv[2];
		
		require($GO_CONFIG->class_path.'mail/RFC822.class.inc');
		$RFC822 = new RFC822();

		$email_module = $GO_MODULES->get_module('email');
		require_once($email_module['class_path'].'email.class.inc');
		$email = new email();

		require($email_module['path'].'vacation_functions.inc');

		$account = $email->get_account($account_id);

		if($account)
		{
			$homedir = $GO_CONFIG->user_home_dirs.$account['username'];


			if (file_exists ($homedir)) {
				$vacation_file = $homedir.'/.vacation.msg';
				$db_file = $homedir.'/.vacation.db';
				$forward_file = $homedir.'/.forward';

				/* kristov: determine path to empty vacation database */
				//$empty_db_file = $GO_CONFIG->root_path.'files/.vacation.db';

				if($account['enable_vacation'])
				{				
					$vacation_file_contents =
					'From: '.$RFC822->write_address($account['name'], $account['email'])."\n".
					'Subject: '.$account['vacation_subject']."\n\n".
					$account['vacation_text'];

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
