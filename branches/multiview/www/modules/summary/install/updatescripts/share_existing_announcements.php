<?php
if(GO::modules()->isInstalled('summary')){ 
	
	$announcements = GO_Summary_Model_Announcement::model()->find();
	
	foreach($announcements as $announcement) {
		
		echo "Sharing ".$announcement->title."\n";
		$acl = $announcement->setNewAcl($announcement->user_id);
		$acl->addGroup(GO::config()->group_everyone, \GO\Base\Model\Acl::READ_PERMISSION);
		$announcement->save();
	}
}