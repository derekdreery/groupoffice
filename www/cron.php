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

 /*
  * Run a cron job every 5 minutes on this file. Add this to /etc/cron.d/groupoffice :
  *
  * 0,5,10,15,20,25,30,35,40,45,50,55 * * * * root php /path/to/go/cron.php /path/to/config.php
  */

if(isset($argv[1]))
	define('CONFIG_FILE', $argv[1]);

require_once('Group-Office.php');
require_once($GO_CONFIG->class_path.'base/reminder.class.inc.php');
require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');

require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

$GO_USERS->get_users('id');
while($user = $GO_USERS->next_record())
{	
	if($user['mail_reminders'])
	{		
		$rm = new reminder();		
		if($rm->get_reminders($user['id'], true))
		{
			require($GO_LANGUAGE->get_base_language_file('common',$user['language']));
			foreach($GO_MODULES->modules as $module)
			{
				$lang_file = $GO_LANGUAGE->get_language_file($module['id'],$user['language']);

				if(!empty($lang_file))
				require($lang_file);
			}

			$rm2 = new reminder();
			while($reminder = $rm->next_record())
			{
				$type_name = isset($lang['link_type'][$reminder['link_type']]) ? $lang['link_type'][$reminder['link_type']] : $lang['common']['unknown'];				
				$subject = $lang['common']['reminder'].' - '.$type_name;
				$time = ($reminder['vtime']) ? $reminder['vtime'] : $reminder['time'];

				$date_format = Date::get_dateformat($user['date_format'], $user['date_separator']);
				$time_format = $user['time_format'];

				$body = $lang['common']['time'].': '.date($date_format.' '.$time_format, $time)."\n";
				$body .= $lang['common']['name'].': '.str_replace('<br />', ', ', $reminder['name'])."\n";

				$swift =& new GoSwift($user['email'], $subject);
				$swift->set_from($GO_CONFIG->webmaster_email, $GO_CONFIG->title);
				$swift->set_body($body, 'plain');
				$swift->sendmail();

				echo "Email send to: ".$user['email']." (subject: ".$subject.")\n";
				$rm2->update_reminder(array('id'=>$rm->record['id'],'mail_send'=>'1'), false);
			}
		}
	}
}

$GO_EVENTS->fire_event('cronjob');

?>
