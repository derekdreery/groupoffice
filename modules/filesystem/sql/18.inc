<?php
/*
*	1=cal_events
* 2=ab_contacts
* 3=ab_companies
* 4=no_notes
* 5=pmProjects
* 6=folders & files
* 7=bs_orders
*/
require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem(true);
$fs2 = new filesystem(true);

$fs->get_latest_files();

while($fs->next_record())
{
	if(file_exists($fs->f('path')))
	{
		$file['link_id']=$fs->f('link_id');
		$file['mtime']=filemtime($fs->f('path'));
		$file['ctime']=filectime($fs->f('path'));

		$fs2->update_row('fs_links','link_id',$file);
	}else {
		$fs2->query("DELETE FROM fs_links WHERE link_id=".$fs->f('link_id'));
	}
}