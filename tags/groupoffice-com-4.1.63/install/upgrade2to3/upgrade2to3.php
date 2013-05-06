<?php
/*
 * First convert the old database to UTF8 as described in the INSTALL.TXT file
 *
 * Then it's wise to create another backup in case anything goes wrong in this script.
 *
 * Then run this script in the browser or on the command line:
 *
 * php upgrade2to3.php | tee upgrade.log
 *
 */

$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";


if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}

define('NOTINSTALLED', true);

require_once('../../Group-Office.php');
require_once('modules.class.inc.php');
require_once('security.class.inc.php');

$GO_MODULES = new UPGRADE_GO_MODULES();
$GO_SECURITY = new UPGRADE_GO_SECURITY();

require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();


ini_set('max_execution_time', '360');
ini_set('display_errors', 'On');


function update_link($old_link_id, $id, $link_type)
{
	global $module_ids, $line_break;

	if(!empty($id) && !empty($old_link_id))
	{
		echo 'Changing old link_id='.$old_link_id.' into '.$id.' with link_type='.$link_type.$line_break;

		$db = new db();
		$db->halt_on_error = 'report';

		$sql = "UPDATE go_links SET link_id1=".$id.", link_id1_converted='1' WHERE link_id1=".$old_link_id." AND type1=$link_type AND link_id1_converted='0'";
		$db->query($sql);

		$sql = "UPDATE go_links SET link_id2=".$id.", link_id2_converted='1' WHERE link_id2=".$old_link_id." AND type2=$link_type AND link_id2_converted='0'";
		$db->query($sql);

		if(in_array('custom_fields', $module_ids) && in_array($link_type, array(2,3,4,5,8)))
		{
			echo 'Updating custom fields'.$line_break;
			//custom fields conversion
			$sql = "UPDATE cf_$link_type SET link_id=$id, link_id_converted='1' WHERE link_id=$old_link_id AND link_id_converted='0'";
			$db->query($sql);
		}
	}
}

function set_site_id($site_id, $folder_id)
{
	$cms1 = new cms();

	$folder['site_id']=$site_id;
	$folder['id']=$folder_id;
	$cms1->update_folder($folder);


	$cms1->get_folders($folder_id);
	while($cms1->next_record())
	{
		set_site_id($site_id, $cms1->f('id'));
	}
}



$winter = new DateTime('01-01-2008');
$winter_offset = $winter->getOffset();

$summer = new DateTime('01-07-2008');
$summer_offset = $summer->getOffset();

//echo $winter_offset.'/'.$summer_offset;



function add_time($time)
{
	global $summer_offset, $winter_offset;
	if(date('I', $time)== 0)
	{
		$time+=$winter_offset;
	}else
	{
		$time+=$summer_offset;
	}
	return $time;
}

$db = new db();
$db2 = new db();

//suppress duplicate and drop errors
$db->halt_on_error = 'report';
$db->suppress_errors=array(1060, 1091, 1146);

$db2->halt_on_error = 'report';
$db2->suppress_errors=$db->suppress_errors;

$db3 = new db();
$db3->halt_on_error = 'report';
$db3->suppress_errors=$db->suppress_errors;

echo 'Upgrading '.$GLOBALS['GO_CONFIG']->db_name.$line_break;

$db->query("SHOW TABLES");
while($record=$db->next_record(DB_BOTH))
{
	if($record[0]=='go_users')
	{
		die('It seems that database has already been upgraded to version 3.0.');
	}
}

echo 'Framework updates'.$line_break;
flush();

$db->query("RENAME TABLE `modules`  TO `go_modules` ;");
$db2->query("UPDATE go_modules SET id='files' WHERE id='filesystem'");

$sql = "SELECT * FROM go_modules";
$db->query($sql);
while($db->next_record())
{
	$module_name = $db->f('id');
	if($module_name=='custom_fields')
		$module_name='customfields';

	if(is_dir($GLOBALS['GO_CONFIG']->root_path.'modules/'.$module_name))
	{
		$module_ids[]=$db->f('id');
		$modules[$db->f('id')]=$db->record;

		echo $db->f('id').$line_break;
	}
}




$db->query("CREATE TABLE `state` (
`user_id` INT NOT NULL ,
`index` VARCHAR( 50 ) NOT NULL ,
`name` VARCHAR( 50 ) NOT NULL ,
`value` TEXT NOT NULL ,
PRIMARY KEY ( `user_id` , `index` , `name` )
) ENGINE = MYISAM  DEFAULT CHARSET=utf8;;");




$db->query("RENAME TABLE `acl`  TO `go_acl` ;");
$db->query("RENAME TABLE `acl_items`  TO `go_acl_items` ;");
$db->query("RENAME TABLE `db_sequence`  TO `go_db_sequence` ;");
$db->query("RENAME TABLE `countries`  TO `go_countries` ;");
$db->query("RENAME TABLE `groups`  TO `go_groups` ;");
$db->query("RENAME TABLE `links`  TO `go_links` ;");
$db->query("RENAME TABLE `log` TO `go_log` ;");
//$db->query("RENAME TABLE `modules`  TO `go_modules` ;");
$db->query("RENAME TABLE `settings`  TO `go_settings`;");
$db->query("RENAME TABLE `state`  TO `go_state` ;");
$db->query("RENAME TABLE `se_cache`  TO `go_search_cache` ;");
$db->query("RENAME TABLE `se_last_sync_times`  TO `go_search_sync` ;");
$db->query("RENAME TABLE `users`  TO `go_users` ;");
$db->query("RENAME TABLE `users_groups`  TO `go_users_groups` ;");
$db->query("RENAME TABLE `reminders`  TO `go_reminders` ;");

$db->query("update go_db_sequence set seq_name=CONCAT('go_',seq_name) WHERE seq_name IN ('reminders', 'users','state','acl','acl_items','countries','groups','log','modules','settings');");


$db->query("UPDATE `go_search_cache` SET id=link_id WHERE id=0;");
$db->query("ALTER TABLE `go_search_cache` ADD `acl_read` INT NOT NULL ,ADD `acl_write` INT NOT NULL ;");
$db->query("ALTER TABLE `go_search_cache` DROP PRIMARY KEY");
$db->query("ALTER TABLE `go_search_cache` DROP `link_id`");
$db->query("TRUNCATE `go_search_cache`;");
$db->query("ALTER TABLE `go_search_cache` ADD PRIMARY KEY(`id`,`link_type`);");



//links indexes
$db->query("ALTER TABLE `go_links` DROP INDEX `link_id2`");
$db->query("ALTER TABLE `go_links` DROP INDEX `link_id1`");
$db->query("ALTER TABLE `go_links` DROP INDEX `type2`");
$db->query("ALTER TABLE `go_links` DROP INDEX `type1`");

$db->query("ALTER TABLE `go_links` ADD INDEX ( `type2`, `link_id2` )");
$db->query("ALTER TABLE `go_links` ADD INDEX ( `type1`, `link_id1` )");

//temporary fields for link_id conversion
$db->query("ALTER TABLE `go_links` ADD `link_id1_converted` ENUM( '0', '1' ) NOT NULL , ADD `link_id2_converted` ENUM( '0', '1' ) NOT NULL ;");

if(in_array('custom_fields', $module_ids))
{
	$db->query("ALTER TABLE `cf_2` ADD `link_id_converted` ENUM( '0', '1' ) NOT NULL ;");
	$db->query("ALTER TABLE `cf_3` ADD `link_id_converted` ENUM( '0', '1' ) NOT NULL ;");
	$db->query("ALTER TABLE `cf_4` ADD `link_id_converted` ENUM( '0', '1' ) NOT NULL ;");
	$db->query("ALTER TABLE `cf_5` ADD `link_id_converted` ENUM( '0', '1' ) NOT NULL ;");
	$db->query("ALTER TABLE `cf_8` ADD `link_id_converted` ENUM( '0', '1' ) NOT NULL ;");
	$db->query("ALTER TABLE `cf_2` DROP PRIMARY KEY");
	$db->query("ALTER TABLE `cf_3` DROP PRIMARY KEY");
	$db->query("ALTER TABLE `cf_4` DROP PRIMARY KEY");
	$db->query("ALTER TABLE `cf_5` DROP PRIMARY KEY");
	$db->query("ALTER TABLE `cf_8` DROP PRIMARY KEY");
}



$db->query("DELETE FROM go_settings");


//REMINDERS
$db->query("ALTER TABLE `go_reminders` DROP `url`");
$db->query("ALTER TABLE `go_reminders` ADD `link_type` INT NOT NULL AFTER `link_id` ;");


$GLOBALS['GO_MODULES']->load_modules();


//prevent folder creations at this stage
unset($GLOBALS['GO_MODULES']->modules['files']);

//end framework updates


$db->query("UPDATE `go_modules` SET version = ''");
$db->query("ALTER TABLE `go_modules` CHANGE `version` `version` INT NOT NULL");
$db->query("ALTER TABLE `go_modules` DROP `path`");

if (in_array('shipping',$module_ids))
{
	echo 'Shipping updates'.$line_break;
	flush();

	echo 'Updating job links'.$line_break;
	flush();

	$sql = "SELECT id, link_id FROM sh_jobs";
	$db->query($sql);
	while($db->next_record())
	{
		update_link($db->f('link_id'),$db->f('id'), 1);
	}
	echo 'Updating package links'.$line_break;
	flush();

	$sql = "SELECT id, link_id FROM sh_packages";
	$db->query($sql);
	while($db->next_record())
	{
		update_link($db->f('link_id'),$db->f('id'), 1);
	}
	echo 'Updating container links'.$line_break;
	flush();

	$sql = "SELECT id, link_id FROM sh_containers";
	$db->query($sql);
	while($db->next_record())
	{
		update_link($db->f('link_id'),$db->f('id'), 1);
	}

	echo 'Updating shipment links'.$line_break;
	flush();

	$sql = "SELECT id, link_id FROM sh_shipments";
	$db->query($sql);
	while($db->next_record())
	{
		update_link($db->f('link_id'),$db->f('id'), 1);
	}
}

if(in_array('calendar', $module_ids))
{
	echo 'Calendar updates'.$line_break;
	flush();

	require_once('../../modules/calendar/classes/calendar.class.inc.php');

	$db->query('ALTER TABLE `cal_events` ADD `calendar_id` INT NOT NULL AFTER `id` ;');
	$db->query('ALTER TABLE `cal_events` ADD `status` VARCHAR( 20 ) NOT NULL ;');
	$db->query('ALTER TABLE `cal_events` ADD `participants_event_id` INT NOT NULL ;');
	$db->query('UPDATE cal_events SET participants_event_id = id');
	$db->query("ALTER TABLE `cal_events` ADD `private` ENUM( '0', '1' ) NOT NULL ;");
	$db->query("UPDATE cal_events SET private='1' WHERE permissions='3'");

	$db->query("UPDATE cal_events SET status=(SELECT name FROM cal_statuses WHERE id=cal_events.status_id);");



	$GLOBALS['GO_MODULES']->add_module('tasks');

        //prevent folder creations at this stage
        unset($GLOBALS['GO_MODULES']->modules['files']);

	$GLOBALS['GO_SECURITY']->copy_acl($GLOBALS['GO_MODULES']->modules['calendar']['acl_read'], $GLOBALS['GO_MODULES']->modules['tasks']['acl_read']);
	$GLOBALS['GO_SECURITY']->copy_acl($GLOBALS['GO_MODULES']->modules['calendar']['acl_write'], $GLOBALS['GO_MODULES']->modules['tasks']['acl_write']);


	//separate events that are in multiple calendars
	$sql = "SELECT event_id FROM cal_events_calendars GROUP BY event_id HAVING COUNT(calendar_id)>1;";
	$db->query($sql);
	while($db->next_record())
	{
		$sql = "SELECT * FROM cal_events WHERE id=".$db->f('event_id');
		$db2->query($sql);
		if($event = $db2->next_record())
		{
			echo 'Duplicating event '.htmlspecialchars($event['name']).$line_break;
			//flush();

			$sql = "SELECT * FROM cal_events_calendars WHERE event_id=".$event['id'];
			$db2->query($sql);
			$db2->next_record();
			while($db2->next_record())
			{
				$new_event = $event;
				$new_event['calendar_id']=$db2->f('calendar_id');
				$new_event['id']=$db3->nextid('cal_events');

				$db3->insert_row('cal_events', $new_event);
				$db3->query("DELETE FROM cal_events_calendars WHERE event_id=".$event['id']." AND calendar_id=".$db2->f('calendar_id'));
			}
		}

		//todo copy links
	}


	echo 'Updating calendar links'.$line_break;
	flush();

	//update links
	$sql = "SELECT id, link_id FROM cal_events";
	$db->query($sql);
	while($db->next_record())
	{
		update_link($db->f('link_id'),$db->f('id'), 1);
	}



	echo 'Correcting timezone bug in 2.x'.$line_break;

	$sql = "SELECT id, start_time, end_time, repeat_end_time FROM cal_events";

	$db->query($sql);

	while($db->next_record())
	{
		$update = $db->record;

		$update['start_time']=add_time($update['start_time']);
		$update['end_time']=add_time($update['end_time']);

		$update['repeat_end_time']=$update['repeat_end_time']>86400? add_time($update['repeat_end_time']) : 0;

		$db2->update_row('cal_events', 'id', $update);
	}


	require('../../modules/calendar/classes/go_ical.class.inc');


	echo 'Converting recurrence rules'.$line_break;
	//new rrule
	$db->query("ALTER TABLE `cal_events` ADD `rrule` VARCHAR( 50 ) NOT NULL ;");
	$db->query("UPDATE cal_events SET repeat_end_time=0 WHERE repeat_end_time<86400");

	$db->query("SELECT * FROM cal_events WHERE repeat_type!='0'");

	unset($event);
	while($db->next_record())
	{
		$event['id']=$db->f('id');
		$event['rrule']=Date::build_rrule($db->f('repeat_type'), $db->f('repeat_every'), $db->f('repeat_end_time'), $db->record, $db->f('month_time'));

		$db2->update_row('cal_events', 'id', $event);
	}


	$db->query("ALTER TABLE `cal_events` ADD INDEX ( `rrule` )");

	//now update all the new calendar_id fields
	echo 'Updating calendar_id fields in cal_events'.$line_break;

	//very slow:
	//$sql = "UPDATE cal_events SET calendar_id=(SELECT calendar_id FROM cal_events_calendars WHERE event_id=cal_events.id)";
	//$db->query($sql);

	$db->query("SELECT DISTINCT calendar_id, event_id FROM cal_events_calendars");
	while($db->next_record())
	{
		$event = array('id'=>$db->f('event_id'), 'calendar_id'=>$db->f('calendar_id'));
		$db2->update_row('cal_events', 'id', $event);
	}


	echo 'Converting tasks to new separate tasks module'.$line_break;
	require_once($GLOBALS['GO_CONFIG']->root_path.'modules/tasks/classes/tasks.class.inc.php');
	$tasks = new tasks();

	$tasklists=array();

	$tasks->query("SELECT * FROM ta_lists");
	while($tasks->next_record())
	{
		$tasklists[$tasks->f('user_id')]=$tasks->f('id');
	}

	require($GLOBALS['GO_LANGUAGE']->get_language_file('calendar'));

	$count = 0;
	$db->query("SELECT e.*, c.user_id AS cal_user_id FROM cal_events e INNER JOIN cal_calendars c ON c.id=e.calendar_id WHERE todo='1'");

	while($db->next_record())
	{
		if(isset($tasklists[$db->f('cal_user_id')]))
		{
			$todo['tasklist_id']=$tasklists[$db->f('cal_user_id')];
			$todo['user_id']=$db->f('cal_user_id');
			$todo['ctime']=$db->f('ctime');
			$todo['mtime']=$db->f('mtime');
			$todo['start_time']=$db->f('start_time');
			$todo['due_time']=$db->f('end_time');
			$todo['completion_time']=$db->f('completion_time');
			$todo['name']=$db->f('name');

			$todo['description']='';
			if($db->f('location')!='')
				$todo['description']=$lang['calendar']['location'].': '.$db->f('location')."\n\n";

			$todo['description'].=$db->f('description');
			$todo['status']=$db->f('status');
			$todo['rrule']=$db->f('rrule');
			$todo['repeat_end_time']=$db->f('repeat_end_time');


			$todo['id'] = $db2->nextid("ta_tasks");
			$db2->insert_row('ta_tasks', $todo);

			$sql = "UPDATE go_links SET link_id1=".$todo['id'].",type1=12 WHERE link_id1=".$db->f('id')." AND type1=1";
			$db2->query($sql);

			$sql = "UPDATE go_links SET link_id2=".$todo['id'].",type2=12 WHERE link_id2=".$db->f('id')." AND type2=1";
			$db2->query($sql);

			$sql = "DELETE FROM cal_events WHERE id=".$db->f('id');
			$tasks->query($sql);

			$count++;

			echo 'Created task '.$todo['name'].$line_break;

		}else
		{
			echo 'Warning! Task didn\'t have a calendar :'.$todo['name'].' '.date('Ymd G:i', $todo['start_time']).$line_break;
		}
	}



	//drop old fields and tables
	$db->query("ALTER TABLE `cal_events`
  DROP `permissions`,
  DROP `link_id`,
  DROP `todo`,
  DROP `completion_time`,
  DROP `status_id`;");

	$db->query("ALTER TABLE `cal_events`
  DROP `contact_id`,
  DROP `company_id`,
  DROP `project_id`,
  DROP `repeat_type`,
  DROP `repeat_forever`,
  DROP `repeat_every`,
  DROP `mon`,
  DROP `tue`,
  DROP `wed`,
  DROP `thu`,
  DROP `fri`,
  DROP `sat`,
  DROP `sun`,
  DROP `month_time`,
  DROP `custom_fields`;");

	$db->query("ALTER TABLE `cal_events` DROP `timezone`, DROP `DST`;");

	$db->query("DROP TABLE `cal_statuses`");




}




if(in_array('cms', $module_ids))
{
	echo 'CMS updates'.$line_break;
	flush();


	$db->query("DROP TABLE `cms_languages` ;");
	$db->query("DROP TABLE `cms_template_files`");
	$db->query("DROP TABLE `cms_settings`");

	 $db->query("update `cms_template_items` set content=replace(content, 'webshop.class.inc\'', 'webshop.class.inc.php\'');");


	 $cms_module = $GLOBALS['GO_MODULES']->get_module('cms');

		require_once($cms_module['class_path'].'cms.class.inc.php');
		$cms = new cms();

		$cms->query("ALTER TABLE `cms_folders` ADD `site_id` INT NOT NULL ;");
		$cms->query("ALTER TABLE `cms_folders` ADD INDEX ( `site_id` ) ;");

		$cms->get_sites();
		$cms2 = new cms();
		while($cms->next_record())
		{
			set_site_id($cms->f('id'), $cms->f('root_folder_id'));
		}


		$sql = "UPDATE cms_files SET content=replace(content, 'view.php','run.php');";
		$cms->query($sql);
		$sql = "UPDATE cms_files SET name=replace(name, '.html','');";
		$cms->query($sql);



		$cms->query("ALTER TABLE `cms_sites` ADD `template` VARCHAR( 20 ) NOT NULL ;");
		$cms->query("UPDATE `cms_sites` SET template = 'Default';");

		$cms->query("ALTER TABLE `cms_files` ADD `option_values` TEXT NOT NULL ;");
		$cms->query("ALTER TABLE `cms_folders` ADD `option_values` TEXT NOT NULL ;");

		$db->query("ALTER TABLE `cms_files`
		  DROP `extension`,
		  DROP `hot_item`,
		  DROP `hot_item_text`,
		  DROP `template_item_id`,
		  DROP `acl`,
		  DROP `registered_comments`,
		  DROP `unregistered_comments`;");

		$db->query("ALTER TABLE `cms_folders`
		  DROP `multipage`,
		  DROP `template_item_id`;");

		$db->query("ALTER TABLE `cms_sites`
		  DROP `allow_properties`,
		  DROP `publish_style`,
		  DROP `publish_path`,
		  DROP `template_id`;");

		$cms->query("ALTER TABLE `cms_files` ADD `plugin` VARCHAR( 20 ) NOT NULL ;");


}




if(in_array('summary', $module_ids))
{
	echo 'Summary updates'.$line_break;
	flush();

	$db->query("CREATE TABLE `su_rss_feeds` (
`user_id` INT NOT NULL ,
`url` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `user_id` )
) ENGINE = MYISAM  DEFAULT CHARSET=utf8;");

	$db->query("CREATE TABLE `su_notes` (
`user_id` INT NOT NULL ,
`text` TEXT NOT NULL ,
PRIMARY KEY ( `user_id` )
) ENGINE = MYISAM  DEFAULT CHARSET=utf8;");

}



if(in_array('notes', $module_ids))
{
	echo 'Notes updates'.$line_break;
	flush();

	//update links
	$sql = "SELECT id, link_id FROM no_notes";
	$db->query($sql);
	while($db->next_record())
	{
		update_link($db->f('link_id'), $db->f('id'), 4);
	}

	$db->query("ALTER TABLE `no_notes` DROP `link_id`");
	//$db->query("ALTER TABLE `no_notes` DROP `contact_id`");
	//$db->query("ALTER TABLE `no_notes` DROP `company_id`");
	//$db->query("ALTER TABLE `no_notes` DROP `project_id`");
	$db->query("ALTER TABLE `no_notes` DROP `due_date`");
	$db->query("ALTER TABLE `no_notes` DROP `file_path`");


	//$db->query("ALTER TABLE `no_notes` DROP INDEX `link_id_2`");

	$db->query("ALTER TABLE `no_notes` ADD `category_id` INT NOT NULL AFTER `id` ;");
	$db->query("ALTER TABLE `no_notes` ADD INDEX ( `category_id` ) ;");

	$db->query("CREATE TABLE IF NOT EXISTS `no_categories` (
	  `id` int(11) NOT NULL,
	  `user_id` int(11) NOT NULL,
	  `acl_read` int(11) NOT NULL,
	  `acl_write` int(11) NOT NULL,
	  `name` varchar(50) NOT NULL,
	  PRIMARY KEY  (`id`)
	)  DEFAULT CHARSET=utf8;");


	$GO_USERS->get_users();
	while($GO_USERS->next_record())
	{
		$user = $GO_USERS->record;

		$category['id']=$db->nextid('no_categories');
		$category['name']=String::format_name($user);
		$category['user_id']=$user['id'];
		$category['acl_read']=$GLOBALS['GO_SECURITY']->get_new_acl('', $user['id']);
		$category['acl_write']=$GLOBALS['GO_SECURITY']->get_new_acl('', $user['id']);

		$db->insert_row('no_categories', $category);

		$db->query("UPDATE no_notes SET category_id=".$category['id']." WHERE user_id=".$user['id']);
	}
}

if(in_array('projects', $module_ids))
{
	echo 'Projects updates'.$line_break;
	flush();

	//on windows sometimes capitals are lost?!?
	$db->query("RENAME TABLE `pmprojects`  TO `pmProjects` ;");
	$db->query("RENAME TABLE `pmhours`  TO `pmHours` ;");
	$db->query("RENAME TABLE `pmfees`  TO `pmFees` ;");
	$db->query("RENAME TABLE `pmtimers`  TO `pmTimers` ;");

	//update links
	$sql = "SELECT id, link_id FROM pmProjects";
	$db->query($sql);
	while($db->next_record())
	{
		update_link($db->f('link_id'), $db->f('id'), 5);
	}

	$db->query("RENAME TABLE `pmProjects`  TO `pm_projects` ;");
	$db->query("RENAME TABLE `pmHours`  TO `pm_hours` ;");
	$db->query("RENAME TABLE `pmFees`  TO `pm_fees` ;");
	$db->query("RENAME TABLE `pmTimers`  TO `pm_timers` ;");

	$db->query("ALTER TABLE `pm_projects`
  DROP `contact_id`,
  DROP `project_id`,
  DROP `res_user_id`,
  DROP `comments`,
  DROP `start_date`,
  DROP `end_date`,
  DROP `probability`,
  DROP `budget`,
  DROP `billed`,
  DROP `unit_value`,
	DROP `link_id`,
  DROP `calendar_id`;");

	$db->query("ALTER TABLE `pm_projects` CHANGE `status` `status_id` INT( 11 ) NOT NULL");

	$db->query("ALTER TABLE `pm_projects` ADD `active` ENUM( '0', '1' ) NOT NULL ;");

	$db->query("UPDATE pm_hours SET units = ( end_time - start_time - break_time ) /3600");

	$db->query("ALTER TABLE `pm_hours`
  DROP `end_time`,
  DROP `break_time`,
  DROP `event_id`;");

	$db->query("ALTER TABLE `pm_hours` CHANGE `start_time` `date` INT( 11 ) NOT NULL DEFAULT '0'");





	$db->query("CREATE TABLE `pm_milestones` (
	`id` INT NOT NULL ,
	`project_id` INT NOT NULL ,
	`user_id` INT NOT NULL ,
	`completion_time` INT NOT NULL ,
	`due_time` INT NOT NULL ,
	`name` VARCHAR( 100 ) NOT NULL ,
	`description` TEXT NOT NULL ,
	PRIMARY KEY ( `id` ) ,
	INDEX ( `project_id` )
	) ENGINE = MYISAM  DEFAULT CHARSET=utf8;");



}

if(in_array('billing', $module_ids))
{
	echo 'Billing updates'.$line_break;
	flush();


	//update links
	$sql = "SELECT id, link_id FROM bs_orders";
	$db->query($sql);
	while($db->next_record())
	{
		update_link($db->f('link_id'),$db->f('id'), 7);
	}

	$db->query("ALTER TABLE `bs_expenses` DROP `link_id`;");
	$db->query("RENAME TABLE `bs_shipments` TO `bs_batchjobs`;");
	$db->query("RENAME TABLE `bs_shipments_orders` TO `bs_batchjob_orders`;");
	$db->query("ALTER TABLE `bs_templates` ADD `book_id` INT NOT NULL ;");
	$db->query("ALTER TABLE `bs_templates` ADD INDEX ( `book_id` ) ;");
	$db->query("ALTER TABLE `bs_batchjob_orders` CHANGE `shipment_id` `batchjob_id` INT( 11 ) NOT NULL DEFAULT '0';");

	$db->query("ALTER TABLE `bs_orders` DROP `link_id`;");
  $db->query("ALTER TABLE `bs_orders` DROP `template_id`;");

	$db->query("ALTER TABLE `bs_orders` DROP `name`;");
	$db->query("ALTER TABLE `bs_orders` DROP `to`;");
	$db->query("ALTER TABLE `bs_orders` DROP `recur_time` ;");

	$db->query("ALTER TABLE `bs_books` DROP `calendar_id`");
  $db->query("ALTER TABLE `bs_books` DROP `report_statuses`;");

  $db->query("ALTER TABLE `bs_books` DROP `template`;");
  $db->query("ALTER TABLE `bs_books` DROP `payment_termin`");


	//$db->query("ALTER TABLE `bs_languages` DROP `frontpage_template`");

	$db->query("ALTER TABLE `bs_orders` DROP `frontpage_template_id`");


	$db->query("ALTER TABLE `bs_orders` ADD `customer_country` CHAR( 2 ) NOT NULL AFTER `customer_country_id` ;");

	$db->query("update `bs_orders` set customer_country=(select iso_code_2 from go_countries where id=bs_orders.customer_country_id)");

	$db->query("ALTER TABLE `bs_orders` DROP `customer_country_id` ");

	$db->query("SELECT DISTINCT pdf_template_id, book_id
FROM bs_status_languages l
INNER JOIN bs_order_statuses s ON s.id = l.status_id
WHERE pdf_template_id >0");

	while($db->next_record())
	{
		$sql = "UPDATE bs_templates SET book_id=".$db->f('book_id')." WHERE id=".$db->f('pdf_template_id');
		$db2->query($sql);
	}

	$db->query("UPDATE bs_templates SET book_id = ( SELECT id
FROM bs_books
ORDER BY id ASC
LIMIT 0 , 1 )
WHERE book_id =0;");

	$db->query("CREATE TABLE `cf_7` (
`link_id` INT NOT NULL ,
PRIMARY KEY ( `link_id` )
) ENGINE = MYISAM  DEFAULT CHARSET=utf8;");





	$db->query("ALTER TABLE `bs_expenses` ADD `supplier` VARCHAR( 100 ) NOT NULL AFTER `company_id` ");
	$db->query("update `bs_expenses` set supplier=(select name from ab_companies where id=bs_expenses.company_id)");
	$db->query("ALTER TABLE `bs_expenses` DROP `company_id`  ");
	$db->query("ALTER TABLE `bs_expenses` DROP `vat_percentage`");

	$db->query("ALTER TABLE `bs_expenses` ADD `ptime` INT NOT NULL AFTER `btime` ;");
	$db->query("UPDATE bs_expenses SET ptime = btime WHERE paid = '1';");
	$db->query("ALTER TABLE `bs_expenses` DROP `paid`");

	$db->query("ALTER TABLE `bs_orders` ADD `ptime` INT NOT NULL AFTER `btime` ;");
	$db->query("update bs_orders o set ptime=(SELECT MIN(h.ctime) FROM `bs_order_status_history` h inner join bs_order_statuses s on s.id=h.status_id WHERE payment_required='0' AND h.order_id=o.id)");

	$db->query("ALTER TABLE `bs_books` ADD `country` CHAR( 2 ) NOT NULL AFTER `country_id` ;");
	$db->query("update `bs_books` set country=(select iso_code_2 from go_countries where id=bs_books.country_id)");
	$db->query("ALTER TABLE `bs_books` DROP `country_id`");
}

if(in_array('users', $module_ids))
{
	echo 'Users updates'.$line_break;
	flush();

	//update links
	$sql = "SELECT id, link_id FROM go_users";
	$db->query($sql);
	while($db->next_record())
	{
		update_link($db->f('link_id'),$db->f('id'), 8);
	}


	//start users

	$db->query("ALTER TABLE `go_users`
	  DROP `authcode`,
	  DROP `mail_client`,
	  DROP `DST`,
	  DROP `use_checkbox_select`,
	  DROP `link_id`;");

	$db->query("ALTER TABLE `go_users` CHANGE `timezone` `timezone` VARCHAR( 50 ) NOT NULL DEFAULT '0'");

	$db->query("update go_users set country=(select iso_code_2 from go_countries where id=go_users.country_id)");
	$db->query("update go_users set work_country=(select iso_code_2 from go_countries where id=go_users.work_country_id)");


	$db->query("ALTER TABLE `go_users`
  	DROP `country_id`,
  	DROP `work_country_id`;");

	$db->query("ALTER TABLE `go_users` CHANGE `country` `country` CHAR( 2 ) NOT NULL");
	$db->query("ALTER TABLE `go_users` CHANGE `work_country` `work_country` CHAR( 2 ) NOT NULL");

	//end users

}

if(in_array('timeregistration', $module_ids))
{
}

if(in_array('updateserver', $module_ids))
{
	echo 'Updateserver updates'.$line_break;
	flush();


	//update links
	$sql = "SELECT id, link_id FROM us_licenses";
	$db->query($sql);
	while($db->next_record())
	{
		update_link($db->f('link_id'),$db->f('id'), 11);
	}
}


if(in_array('addressbook', $module_ids))
{
	echo 'Addressbook updates'.$line_break;
	flush();


	$db->query('ALTER TABLE `ab_contacts` ADD `salutation` VARCHAR( 50 ) NOT NULL ;');
	$db->query('ALTER TABLE `ab_contacts` CHANGE `comment` `comment` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL');
	$db->query('ALTER TABLE `ab_contacts` ADD `email_allowed` ENUM( \'0\', \'1\' ) NOT NULL ;');
	$db->query('ALTER TABLE `ab_companies` ADD `email_allowed` ENUM( \'0\', \'1\' ) NOT NULL ;');

	$db->query('ALTER TABLE `ab_companies` ADD `comment` TEXT NOT NULL AFTER `homepage` ;');


	//update links
	$sql = "SELECT id, link_id FROM ab_contacts";
	$db->query($sql);
	while($db->next_record())
	{
		update_link($db->f('link_id'),$db->f('id'), 2);
	}
	$sql = "SELECT id, link_id FROM ab_companies";
	$db->query($sql);
	while($db->next_record())
	{
		update_link($db->f('link_id'),$db->f('id'), 3);
	}

	$sql = "SELECT * FROM tp_templates";
	$db->query($sql);
	$r = $db->next_record();

	if(!$r || !isset($r['content'])){
		$db->query("ALTER TABLE `tp_templates` ADD `content` LONGBLOB NOT NULL ;");
		$db->query("UPDATE tp_templates t SET content = ( SELECT content FROM tp_templates_content WHERE id = t.id ) ;");
	}

	//$db->query("DROP TABLE `tp_templates_content`");

	$db->query("RENAME TABLE `tp_mailing_companies`  TO `ab_mailing_companies` ;");
	$db->query("RENAME TABLE `tp_mailing_contacts`  TO `ab_mailing_contacts` ;");
	$db->query("RENAME TABLE `tp_mailing_users`  TO `ab_mailing_users` ;");

	$db->query("RENAME TABLE `tp_templates`  TO `ab_templates`");
	$db->query("RENAME TABLE `tp_mailing_groups`  TO `ab_mailing_groups` ;");


	$fields = array(
			'my_name',
			'date',
			'salutation',
			'first_name',
			'middle_name',
			'last_name',
			'initials',
			'title',
			'email',
			'email2',
			'email3',
			'home_phone',
			'fax',
			'cellular',
			'address',
			'address_no',
			'zip',
			'city',
			'state',
			'country',
			'company',
			'department',
			'function',
			'work_phone',
			'work_fax',
			'work_address',
			'work_address_no',
			'work_zip',
			'work_city',
			'work_state',
			'work_country',
			'work_post_address',
			'work_post_address_no',
			'work_post_zip',
			'work_post_city',
			'work_post_state',
			'work_post_country',
			'homepage');

	foreach($fields as $field)
	{
		$sql = "UPDATE ab_templates SET content=REPLACE(content, '%".$field."%','{".$field."}') WHERE type='0';";
		$db->query($sql);
	}

}



if(in_array('email', $module_ids))
{
	echo 'E-mail updates'.$line_break;
	flush();

	$db->query('ALTER TABLE `em_links` ADD `ctime` INT NOT NULL ;');

	$db->query('RENAME TABLE `emaccounts`  TO `emAccounts` ;');
	$db->query('RENAME TABLE `emfolders`  TO `emFolders` ;');
	$db->query('RENAME TABLE `emfilters`  TO `emFilters` ;');

	$db->query('RENAME TABLE `emAccounts`  TO `em_accounts` ;');
	$db->query('RENAME TABLE `emFolders`  TO `em_folders` ;');
	$db->query('RENAME TABLE `emFilters`  TO `em_filters` ;');

	$db->query("ALTER TABLE `em_accounts` ADD `smtp_host` VARCHAR( 100 ) NOT NULL ,
ADD `smtp_port` INT NOT NULL ,
ADD `smtp_encryption` TINYINT NOT NULL ,
ADD `smtp_username` VARCHAR( 50 ) NOT NULL ,
ADD `smtp_password` VARCHAR( 50 ) NOT NULL ;");


	$db->query("ALTER TABLE `em_accounts`
  DROP `enable_vacation`,
  DROP `vacation_subject`,
  DROP `vacation_text`,
  DROP `forward_enabled`,
  DROP `forward_to`,
  DROP `forward_local_copy`;");

	$db->query("UPDATE em_accounts SET smtp_host='".$GLOBALS['GO_CONFIG']->smtp_server.
		"', smtp_port='".$GLOBALS['GO_CONFIG']->smtp_port."', smtp_username='".$GLOBALS['GO_CONFIG']->smtp_username.
		"', smtp_password='".$GLOBALS['GO_CONFIG']->smtp_password."', smtp_encryption='8'");
}


if(in_array('files', $module_ids))
{
	echo 'Files updates'.$line_break;
	flush();

	$db->query('CREATE TABLE IF NOT EXISTS `fs_file_handlers` (
		`user_id` INT NOT NULL ,
		`extension` CHAR( 4 ) NOT NULL ,
		`handler` VARCHAR( 20 ) NOT NULL ,
		PRIMARY KEY ( `user_id` , `extension` )
		) ENGINE = MYISAM  DEFAULT CHARSET=utf8;');

	$db->query('CREATE TABLE IF NOT EXISTS `fs_templates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `acl_read` int(11) NOT NULL,
  `acl_write` int(11) NOT NULL,
  `content` blob NOT NULL,
  `extension` char(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
	');


	$db->query('RENAME TABLE `fs_shares`  TO `fs_folders` ;');
	$db->query("ALTER TABLE `fs_folders` ADD `visible` ENUM( '0', '1' ) NOT NULL AFTER `path` ;");
	$db->query("UPDATE fs_folders SET visible = '1' WHERE TYPE = 'files' OR TYPE = 'filesystem';");
	$db->query("ALTER TABLE `fs_folders` DROP `type`;");
	$db->query("ALTER TABLE `fs_folders` DROP INDEX `link_id`;");

	$db->query("ALTER TABLE `fs_folders` CHANGE `link_id` `id` INT(11) NOT NULL;");

	$db->query("ALTER TABLE `fs_folders` DROP INDEX `user_id` ;");
	$db->query("ALTER TABLE `fs_folders` ADD INDEX ( `visible` )  ;");
	$db->query("ALTER TABLE `fs_folders` ADD `comments` TEXT NOT NULL ;");

	$db->query("ALTER TABLE `fs_links` CHANGE `link_id` `id` INT(11) NOT NULL;");


	$db->query("ALTER TABLE `fs_links` DROP INDEX `path` ;");

	$db->query("ALTER TABLE `fs_links` DROP PRIMARY KEY");

	$db->query("ALTER TABLE `fs_links` ADD `user_id` INT(11) NOT NULL ;");
	$db->query("ALTER TABLE `fs_links` ADD PRIMARY KEY ( `path`, `id` ) ;");
	$db->query("ALTER TABLE `fs_links` ADD `comments` TEXT NOT NULL ;");
	$db->query("ALTER TABLE `fs_links` ADD `locked_user_id` INT NOT NULL AFTER `path` ;");
	$db->query('RENAME TABLE `fs_links`  TO `fs_files` ;');



}

echo 'Custom fields updates'.$line_break;
	flush();

if(in_array('custom_fields', $module_ids))
{
	//Becuase of a bug some custom field rows might not have been deleted when a contact was deleted
	$db->query("DELETE FROM cf_2 WHERE link_id_converted='0'");
	$db->query("DELETE FROM cf_3 WHERE link_id_converted='0'");
	$db->query("DELETE FROM cf_4 WHERE link_id_converted='0'");
	$db->query("DELETE FROM cf_5 WHERE link_id_converted='0'");
	$db->query("DELETE FROM cf_8 WHERE link_id_converted='0'");
}
//email doesn't need conversion
$db->query("UPDATE go_links SET link_id1_converted='1' WHERE type1=9");
$db->query("UPDATE go_links SET link_id2_converted='1' WHERE type2=9");

//remove dead links
$db->query("DELETE FROM go_links WHERE link_id1_converted='0' OR link_id2_converted='0'");

//remove temporary fields
$db->query("ALTER TABLE `go_links` DROP `link_id1_converted`,  DROP `link_id2_converted`;");

if(in_array('custom_fields', $module_ids))
{
	$db->query("ALTER TABLE `cf_2` DROP `link_id_converted`");
	$db->query("ALTER TABLE `cf_3` DROP `link_id_converted`");
	$db->query("ALTER TABLE `cf_4` DROP `link_id_converted`");
	$db->query("ALTER TABLE `cf_5` DROP `link_id_converted`");
	$db->query("ALTER TABLE `cf_8` DROP `link_id_converted`");

	$db->query("ALTER TABLE `cf_2` ADD PRIMARY KEY ( `link_id` )");
	$db->query("ALTER TABLE `cf_3` ADD PRIMARY KEY ( `link_id` )");
	$db->query("ALTER TABLE `cf_4` ADD PRIMARY KEY ( `link_id` )");
	$db->query("ALTER TABLE `cf_5` ADD PRIMARY KEY ( `link_id` )");
	$db->query("ALTER TABLE `cf_8` ADD PRIMARY KEY ( `link_id` )");
}
$db->query("UPDATE `go_modules` SET `id` = 'customfields' WHERE  `id` ='custom_fields';");


for($link_type=1;$link_type<13;$link_type++)
{
	$sql = "CREATE TABLE IF NOT EXISTS `cf_$link_type` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

	$db->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `go_links_$link_type` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  KEY `link_id` (`link_id`,`link_type`),
  KEY `id` (`id`,`folder_id`)
)  DEFAULT CHARSET=utf8;";
	$db->query($sql);


	$sql = "CREATE TABLE IF NOT EXISTS `go_links_tmp_$link_type` (
	  `id` int(11) NOT NULL,
	  `link_id` int(11) NOT NULL,
	  `link_type` int(11) NOT NULL,
	  KEY `id` (`id`),
	  KEY `link_id` (`link_id`,`link_type`)
	);";
	$db->query($sql);

	$sql = "insert into `go_links_tmp_$link_type` (id, link_id, link_type) select link_id2, link_id1, type1 from go_links where type2=$link_type;";
	$db->query($sql);

	$sql = "insert into `go_links_tmp_$link_type` (id, link_id, link_type) select link_id1, link_id2, type2 from go_links where type1=$link_type;";
	$db->query($sql);


	$sql = "insert into `go_links_$link_type` (id, link_id, link_type) select distinct * FROM go_links_tmp_$link_type;";
	$db->query($sql);

	$sql = "DROP table go_links_tmp_$link_type";
	$db->query($sql);
}

$db->query("CREATE TABLE IF NOT EXISTS `go_link_folders` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `link_id` (`link_id`,`link_type`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");



//SYNC

echo 'Sync updates'.$line_break;
flush();



$db->query("ALTER TABLE `sync_events_maps` CHANGE `event_id` `server_id` INT( 11 ) NOT NULL DEFAULT '0'");
$db->query("ALTER TABLE `sync_events_maps` CHANGE `remote_id` `client_id` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
$db->query("ALTER TABLE `sync_contacts_maps` CHANGE `contact_id` `server_id` INT( 11 ) NOT NULL DEFAULT '0' ");
$db->query("ALTER TABLE `sync_contacts_maps` CHANGE `remote_id` `client_id` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
$db->query("CREATE TABLE IF NOT EXISTS `sync_tasks_maps` (
  `device_id` int(11) NOT NULL default '0',
  `server_id` int(11) NOT NULL default '0',
  `client_id` varchar(255) NOT NULL,
  PRIMARY KEY  (`device_id`,`server_id`,`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$db->query("CREATE TABLE IF NOT EXISTS `sync_tasks_syncs` (
  `device_id` int(11) NOT NULL default '0',
  `local_last_anchor` int(11) NOT NULL default '0',
  `remote_last_anchor` char(32) NOT NULL default '',
  PRIMARY KEY  (`device_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$db->query("insert into sync_tasks_maps(select device_id, server_id, client_id from sync_events_maps where todo='1');");
$db->query("delete from sync_events_maps where todo='1';");
$db->query("ALTER TABLE `sync_events_maps` DROP `todo` ;");
$db->query("insert into sync_tasks_syncs(select * from sync_events_syncs);");
$db->query("ALTER TABLE `sync_settings` ADD `tasklist_id` INT NOT NULL AFTER `calendar_id` , ADD `note_category_id` INT NOT NULL AFTER `tasklist_id` ;");


$module['id']='sync';
$module['sort_order'] = count($GLOBALS['GO_MODULES']->modules)+1;
$module['acl_read']=$GLOBALS['GO_SECURITY']->copy_acl($GLOBALS['GO_MODULES']->modules['calendar']['acl_read']);
$module['acl_write']=$GLOBALS['GO_SECURITY']->copy_acl($GLOBALS['GO_MODULES']->modules['calendar']['acl_write']);
$db->insert_row('go_modules', $module);



/*
 *
 *
 *

CREATE TABLE IF NOT EXISTS `go_links` (
  `type1` tinyint(4) NOT NULL default '0',
  `link_id1` int(11) NOT NULL default '0',
  `type2` tinyint(4) NOT NULL default '0',
  `link_id2` int(11) NOT NULL default '0',
  PRIMARY KEY  (`type1`,`link_id1`,`type2`,`link_id2`)
);

insert into go_links2 (SELECT DISTINCT * FROM go_links);

$db->query('DROP TABLE `go_links` ;');
$db->query('RENAME TABLE `go_links2`  TO `go_links` ;');


ALTER TABLE `go_search_cache` DROP PRIMARY KEY
ALTER TABLE `go_search_cache` DROP INDEX `name`


ALTER TABLE `go_search_cache` CHANGE `keywords` `keywords` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL

 ALTER TABLE `go_search_cache` ADD INDEX ( `keywords` )


 ALTER TABLE `go_links` ADD INDEX ( `link_id2`, `type2` );
ALTER TABLE `go_links` ADD INDEX ( `link_id1`, `type1` );
 */




echo 'Clearing search cache'.$line_break;

require_once($GLOBALS['GO_CONFIG']->class_path.'base/search.class.inc.php');
$search = new search();
$search->reset();
flush();




/*
 * Manual
 *
 * update bs_orders set customer_salutation=concat('Geachte ',customer_salutation) where language_id=1;
 * update bs_status_languages set email_template=replace(email_template,  'Dear %customer_salutation%',  '%customer_salutation%');
 *
 */

$db->query("update ab_contacts set salutation=CONCAT('".$lang['common']['default_salutation']['M']." ',LTRIM(CONCAT(middle_name,' ',last_name))) where sex='M' and salutation='';");
$db->query("update ab_contacts set salutation=CONCAT('".$lang['common']['default_salutation']['F']." ',LTRIM(CONCAT(middle_name,' ',last_name))) where sex='F' and salutation='';");


//lot of people didn't have latest 2.18
$db->query("alter table go_users add auth_md5_pass varchar(100) not null;");

$timezone = date_default_timezone_get();
if(empty($timezone))
{
	$timezone = 'GMT';
}

$db->query("UPDATE go_users SET timezone='$timezone'");

//for 3.3 specific
//$db->query("ALTER TABLE `go_acl` ADD `level` TINYINT NOT NULL DEFAULT '1'");


echo 'Done'.$line_break.$line_break;

//require('../../modules/tools/dbcheck.php');

if(isset($_SERVER['SERVER_NAME']))
{
	echo '<a target="_blank" href="../index.php">Click here to run the installer and complete the whole proces</a>';

	echo '<br /><br /><a target="_blank" href="../../modules/tools/checkmodules.php">After that run a database check to rebuild the search index</a>';
}else
{
	echo 'Now run the installer in a browser at: '.$GLOBALS['GO_CONFIG']->host.'install/'."\n\n";
	echo 'After that run the database check to rebuild the search index at: '.$GLOBALS['GO_CONFIG']->host.'modules/tools/checkmodules.php'."\n\n";
}

?>
