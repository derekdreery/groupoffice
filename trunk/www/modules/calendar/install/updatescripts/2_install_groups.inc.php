<?php
require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc.php');
$cal = new calendar();

if(!$db->table_exists('cal_groups_old')){
	$group['user_id']=1;
	$group['name']='Calendars';
	$group['acl_admin']=$GO_SECURITY->get_new_acl('resource_group', 1);
	$group['id']=1;

	$cal->nextid('cal_groups');

	$cal->add_group($group);
}else
{
	echo "Found resource groups from version 2.x".$line_break;
	$db->query("SELECT * FROM cal_groups_old");
	while($r=$db->next_record()){
		$group['user_id']=1;
		$group['name']=$r['name'];
		$group['id']=$r['id'];
		$group['acl_admin']=$r['acl_write'];

		$cal->add_group($group);

		echo "Added resource group ".$group['name'].$line_break;
	}

	$db->query("SELECT a.user_id, g.acl_write FROM cal_group_admins a INNER JOIN cal_groups_old g ON g.id=a.group_id");
	while($r=$db->next_record()){
		if(!$GO_SECURITY->user_in_acl($r['user_id'], $r['acl_write'])){
			$GO_SECURITY->add_user_to_acl($r['user_id'], $r['acl_write']);
		}
	}
}
?>
