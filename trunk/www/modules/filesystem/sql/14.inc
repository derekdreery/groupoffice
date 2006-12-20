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
$db1 = new db();
$db2 = new db();

$db1->query("SELECT * FROM fs_shares WHERE link_id>0");

while($db1->next_record())
{
	echo 'Processing '.$db1->f('path').'<br />';
	
	if(!file_exists($db1->f('path')))
	{
		echo 'Folder '.$db1->f('path').' doesn\'t exists<br />';
		
		$fs->delete_share(addslashes($db1->f('path')));
	}else
	{
		$files = $fs->get_files($db1->f('path'));
		$folders = $fs->get_folders($db1->f('path'));
		
		if(!count($files) && !count($folders))
		{
			$fs->delete_share(addslashes($db1->f('path')));
			echo 'Folder '.$db1->f('path').' is empty<br />';
		}else
		{
		
			$links = $GO_LINKS->get_links($db1->f('link_id'));
			while($link = array_shift($links))
			{			
				$user = $GO_USERS->get_user($db1->f('user_id'));
				
				$home_path = $GO_CONFIG->file_storage_path.'users/'.$user['username'];
				switch($db1->f('type'))
				{
					case 'event':
						require_once($GO_LANGUAGE->get_language_file('calendar'));
						$dest_dir = $home_path.'/'.$sc_events;
						if(!is_dir($dest_dir))
						{
							mkdir($dest_dir, $GO_CONFIG->create_mode);
						}					
						
						$sql = "SELECT name FROM cal_events WHERE link_id='".$link['link_id']."'";
						$db2->query($sql);
						if($db2->next_record())
						{			
							$name = $db2->f('name');
							
							$dest_name = $dest_dir.'/'.$name;
							$x=0;
							while(file_exists($dest_name))
							{
								$x++;
								$dest_name = $dest_dir.'/'.$name.' ('.$x.')';
							}				
							
							echo 'Moving items into '.$dest_name.'<br />';
							
							$fs->move($db1->f('path'), $dest_name);
							
							$files = $fs->get_files($dest_name);
							foreach($files as $file)
							{
								$link_id = $fs->get_link_id(addslashes($file['path']));
								$GO_LINKS->add_link($link['link_id'], 1, $link_id, 6);
							}
							
							$folders = $fs->get_folders($dest_name);
							foreach($folders as $folder)
							{
								$link_id = $fs->get_link_id(addslashes($folder['path']));
								$GO_LINKS->add_link($link['link_id'], 1, $link_id, 6);
							}				
						}
						
					break;
					
					case 'contact':
						require_once($GO_LANGUAGE->get_language_file('addressbook'));
						$dest_dir = $home_path.'/'.$contacts_contacts;
						if(!is_dir($dest_dir))
						{
							mkdir($dest_dir, $GO_CONFIG->create_mode);
						}
						
						$sql = "SELECT last_name, first_name, middle_name FROM ab_contacts WHERE link_id='".$link['link_id']."'";
						$db2->query($sql);
						if($db2->next_record())
						{			
							$name = format_name($db2->f('last_name'), $db2->f('first_name'), $db2->f('middle_name'));
							
							$dest_name = $dest_dir.'/'.$name;
							$x=0;
							while(file_exists($dest_name))
							{
								$x++;
								$dest_name = $dest_dir.'/'.$name.' ('.$x.')';
							}				
							echo 'Moving items into '.$dest_name.'<br />';
							$fs->move($db1->f('path'), $dest_name);
							
							$files = $fs->get_files($dest_name);
							foreach($files as $file)
							{
								$link_id = $fs->get_link_id(addslashes($file['path']));
								$GO_LINKS->add_link($link['link_id'], 2, $link_id, 6);
							}
							
							$folders = $fs->get_folders($dest_name);
							foreach($folders as $folder)
							{
								$link_id = $fs->get_link_id(addslashes($folder['path']));
								$GO_LINKS->add_link($link['link_id'], 2, $link_id, 6);
							}				
						}
					break;		
					
					case 'company':
						require_once($GO_LANGUAGE->get_language_file('addressbook'));
						$dest_dir = $home_path.'/'.$ab_companies;
						if(!is_dir($dest_dir))
						{
							mkdir($dest_dir, $GO_CONFIG->create_mode);
						}
						
						$sql = "SELECT name FROM ab_companies WHERE link_id='".$link['link_id']."'";
						$db2->query($sql);
						if($db2->next_record())
						{			
							$name = $db2->f('name');
							
							$dest_name = $dest_dir.'/'.$name;
							$x=0;
							while(file_exists($dest_name))
							{
								$x++;
								$dest_name = $dest_dir.'/'.$name.' ('.$x.')';
							}				
							echo 'Moving items into '.$dest_name.'<br />';
							$fs->move($db1->f('path'), $dest_name);
							
							$files = $fs->get_files($dest_name);
							foreach($files as $file)
							{
								$link_id = $fs->get_link_id(addslashes($file['path']));
								$GO_LINKS->add_link($link['link_id'], 3, $link_id, 6);
							}
							
							$folders = $fs->get_folders($dest_name);
							foreach($folders as $folder)
							{
								$link_id = $fs->get_link_id(addslashes($folder['path']));
								$GO_LINKS->add_link($link['link_id'], 3, $link_id, 6);
							}				
						}
					
					break;		
					
					case 'projects':
						require_once($GO_LANGUAGE->get_language_file('projects'));
						$dest_dir = $home_path.'/'.$pm_projects;
						if(!is_dir($dest_dir))
						{
							mkdir($dest_dir, $GO_CONFIG->create_mode);
						}
						
						$sql = "SELECT name FROM pmProjects WHERE link_id='".$link['link_id']."'";
						$db2->query($sql);
						if($db2->next_record())
						{			
							$name = $db2->f('name');
							
							$dest_name = $dest_dir.'/'.$name;
							$x=0;
							while(file_exists($dest_name))
							{
								$x++;
								$dest_name = $dest_dir.'/'.$name.' ('.$x.')';
							}				
							echo 'Moving items into '.$dest_name.'<br />';
							$fs->move($db1->f('path'), $dest_name);
							
							$files = $fs->get_files($dest_name);
							foreach($files as $file)
							{
								$link_id = $fs->get_link_id(addslashes($file['path']));
								$GO_LINKS->add_link($link['link_id'], 5, $link_id, 6);
							}
							
							$folders = $fs->get_folders($dest_name);
							foreach($folders as $folder)
							{
								$link_id = $fs->get_link_id(addslashes($folder['path']));
								$GO_LINKS->add_link($link['link_id'], 5, $link_id, 6);
							}				
						}
					break;		
				}
			}
		}
	}
	$GO_LINKS->delete_link($db1->f('link_id'));
	//$db2->query("UPDATE fs_shares SET link_id='' WHERE path='".addslashes($dest_name)."'");
}
?>
