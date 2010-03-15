<?php
/*
 *
 * disable because doesn't work well
if(isset($GO_MODULES->modules['tasks'])){
	require_once($GO_MODULES->modules['tasks']['class_path'].'tasks.class.inc.php');
	$tasks = new tasks();
	$db1 = new db();
	$db2 = new db();

	if($db1->table_exists('ta_settings')){

		$db1->query("SELECT default_tasklist_id, user_id FROM ta_settings");

		while($settings = $db1->next_record())
		{
			$tasklist = $tasks->get_tasklist($settings['default_tasklist_id'], $db1->f('user_id'));
			if($tasklist){
				$db2->query('REPLACE INTO su_visible_lists(tasklist_id, user_id) VALUES("'.$tasklist['id'].'", "'.$db1->f('user_id').'")');
			}
		}
	}
}*/