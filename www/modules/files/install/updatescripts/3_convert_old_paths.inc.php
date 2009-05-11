<?php 
if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}

chdir(dirname(__FILE__));


$updates[]="ALTER TABLE `fs_files` ADD `folder_id` INT NOT NULL AFTER `id`";
$updates[]="ALTER TABLE `fs_files` ADD `name` VARCHAR( 255 ) NOT NULL AFTER `folder_id`";
$updates[]="ALTER TABLE `fs_files` ADD `size` INT NOT NULL AFTER `mtime`";
$updates[]="ALTER TABLE `fs_folders` ADD `parent_id` INT( 11 ) NOT NULL AFTER `id`;";
$updates[]="ALTER TABLE `fs_folders` ADD `name` VARCHAR( 255 ) NOT NULL AFTER `parent_id`";
$updates[]="ALTER TABLE `fs_folders` ADD `ctime` INT NOT NULL";  

$updates[]="ALTER TABLE `fs_files` DROP PRIMARY KEY ,ADD PRIMARY KEY ( `id` )"; 
$updates[]="ALTER TABLE `fs_folders` DROP PRIMARY KEY ,ADD PRIMARY KEY ( `id` ) ";
$updates[]="ALTER TABLE `fs_folders` DROP INDEX `link_id_2`"; 
$updates[]="ALTER TABLE `fs_folders` DROP INDEX `visible`"; 

$updates[]="ALTER TABLE `fs_notifications` ADD `folder_id` INT NOT NULL FIRST";
$updates[]="update fs_notifications n set folder_id=(select path from fs_folders where path=n.path);";

$updates[]="ALTER TABLE `fs_notifications` DROP PRIMARY KEY , ADD PRIMARY KEY ( `folder_id` , `user_id` ) ;";



require('../../../../Group-Office.php');

$db = new db();
$db->halt_on_error='report';

$fs = new filesystem();

require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
$fsdb = new files();

function get_file($path, $parent_id)
{
	global $fs, $fsdb, $GO_CONFIG;
	
	$sql = "SELECT * FROM fs_files WHERE path='".$fsdb->escape($path)."';";
	$fsdb->query($sql);
	if($file = $fsdb->next_record())
	{
		$file['name']=utf8_basename($path);
		$file['folder_id']=$parent_id;		
		$fsdb->update_file($file);
		
		return $file['id'];
	}else
	{
		$file['path']=$path;
		$file['name']=utf8_basename($path);
		$file['ctime']=@filectime($GO_CONFIG->file_storage_path.$path);
		$file['mtime']=@filemtime($GO_CONFIG->file_storage_path.$path);
		$file['size']=@filesize($GO_CONFIG->file_storage_path.$path);
		$file['folder_id']=$parent_id;		
		
		return $fsdb->add_file($file);

	}
}

function get_folder($path, $parent_id)
{
	global $fs, $fsdb, $GO_CONFIG;
	
	$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";
	echo 'Getting folder '.$path.$line_break;
	
	$sql = "SELECT * FROM fs_folders WHERE path='".$fsdb->escape($path)."';";
	$fsdb->query($sql);
	if($folder = $fsdb->next_record())
	{
		$folder['name']=utf8_basename($path);
		$folder['parent_id']=$parent_id;
		$folder['ctime']=@filectime($GO_CONFIG->file_storage_path.$path);
		$fsdb->update_folder($folder);
				
		return $folder['id'];
	}else
	{
		$folder['path']=$path;
		$folder['name']=utf8_basename($path);
		$folder['ctime']=@filectime($GO_CONFIG->file_storage_path.$path);
		$folder['parent_id']=$parent_id;
		return $fsdb->add_folder($folder);
	}
}



function crawl($path, $parent_id)
{
	global $fs, $fsdb;
	
	$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";
	echo 'Crawling folder '.$path.$line_break;
	
	$folder_id = get_folder($fsdb->strip_server_path($path), $parent_id);
	
	$folders = $fs->get_folders($path);
	//var_dump($folders);
	while($folder = array_shift($folders))
	{
		crawl($folder['path'], $folder_id);
	}

	
	$files = $fs->get_files($path);
	while($file = array_shift($files))
	{
		get_file($fsdb->strip_server_path($file['path']),$folder_id);
	}
}

/*
$folders = $fs->get_folders($GO_CONFIG->file_storage_path);

foreach($folders as $folder)
{
	crawl($folder['path'], 0);
}

$fsdb->query("DELETE FROM fs_folders WHERE name=''");
*/

if(isset($GO_MODULES->modules['addressbook']))
{
	$db->query("ALTER TABLE `ab_contacts` ADD `files_folder_id` INT NOT NULL;");
	$db->query("SELECT c.*,a.name AS addressbook_name,a.acl_read,a.acl_write FROM ab_contacts c INNER JOIN ab_addressbooks a ON a.id=c.addressbook_id");
	while($contact = $db->next_record())
	{
		$old_path = 'contacts/'.$contact['id'];
		$folder = $fsdb->resolve_path('contacts/'.$contact['id']);

		$new_folder_name = File::strip_invalid_chars(String::format_name($contact));
		
		if($folder && !empty($new_folder_name))
		{
			$last_part = strtoupper($contact['last_name'][0]);
			$new_path = 'contacts/'.File::strip_invalid_chars($contact['addressbook_name']);
			if(!empty($last_part))
			{
				$new_path .= '/'.$last_part;
			}
						
			//echo $new_path."\n";
			$destination = $fsdb->resolve_path($new_path, true, 1);
			
			
			$fs->mkdir_recursive($GO_CONFIG->file_storage_path.$new_path);
			
			$fs->move($GO_CONFIG->file_storage_path.$old_path, $GO_CONFIG->file_storage_path.$new_path.'/'.$new_folder_name);
			$new_folder_id = $fsdb->move_folder($folder, $destination);
			
			$up_folder['id']=$new_folder_id;
			$up_folder['name']=File::strip_invalid_chars(String::format_name($contact));
			$up_folder['acl_read']=0;
			$up_folder['acl_write']=0;

			$fsdb->update_folder($up_folder);
			
			$up_contact['id']=$contact['id'];
			$up_contact['files_folder_id']=$new_folder_id;
			
			$fsdb->update_row('ab_contacts', 'id', $up_contact);
		}		
	}
	
	$db->query("ALTER TABLE `ab_companies` ADD `files_folder_id` INT NOT NULL;");
	$db->query("SELECT c.*,a.name AS addressbook_name,a.acl_read,a.acl_write FROM ab_companies c INNER JOIN ab_addressbooks a ON a.id=c.addressbook_id");
	while($company = $db->next_record())
	{
		$old_path = 'companies/'.$company['id'];
		$folder = $fsdb->resolve_path('companies/'.$company['id']);

		$new_folder_name = File::strip_invalid_chars($company['name']);
		
		if($folder && !empty($new_folder_name))
		{
			$last_part = strtoupper($company['name'][0]);
			$new_path = 'companies/'.File::strip_invalid_chars($company['addressbook_name']);
			if(!empty($last_part))
			{
				$new_path .= '/'.$last_part;
			}
						
			$destination = $fsdb->resolve_path($new_path, true, 1);
			
			$fs->move($GO_CONFIG->file_storage_path.$old_path, $GO_CONFIG->file_storage_path.$new_path.'/'.$new_folder_name);
			$new_folder_id = $fsdb->move_folder($folder, $destination);
			
			$up_folder['id']=$new_folder_id;
			$up_folder['name']=File::strip_invalid_chars($company['name']);
			$up_folder['acl_read']=0;
			$up_folder['acl_write']=0;
			
			$fsdb->update_folder($up_folder);
			
			$up_company['id']=$company['id'];
			$up_company['files_folder_id']=$new_folder_id;
			
			$fsdb->update_row('ab_companies', 'id', $up_company);
		}		
	}
}

if(isset($GO_MODULES->modules['notes']))
{
	$db->query("ALTER TABLE `no_notes` ADD `files_folder_id` INT NOT NULL;");
	$db->query("SELECT n.*,c.name AS category_name,c.acl_read,c.acl_write FROM no_notes n INNER JOIN no_categories c ON c.id=n.category_id");
	while($note = $db->next_record())
	{
		$old_path = 'notes/'.$note['id'];
		$folder = $fsdb->resolve_path('notes/'.$note['id']);

		$new_folder_name = File::strip_invalid_chars($note['name']);
		
		if($folder && !empty($new_folder_name))
		{			
			$new_path = 'notes/'.File::strip_invalid_chars($note['category_name']).'/'.date('Y', $note['ctime']);
						
			//echo $new_path."\n";
			$destination = $fsdb->resolve_path($new_path, true, 1);
			
			
			$fs->mkdir_recursive($GO_CONFIG->file_storage_path.$new_path);
			
			$fs->move($GO_CONFIG->file_storage_path.$old_path, $GO_CONFIG->file_storage_path.$new_path.'/'.$new_folder_name);
			$new_folder_id = $fsdb->move_folder($folder, $destination);
			
			$up_folder['id']=$new_folder_id;
			$up_folder['name']=File::strip_invalid_chars(String::format_name($note));
			$up_folder['acl_read']=0;
			$up_folder['acl_write']=0;

			$fsdb->update_folder($up_folder);
			
			$up_note['id']=$note['id'];
			$up_note['files_folder_id']=$new_folder_id;
			
			$fsdb->update_row('ab_notes', 'id', $up_note);
		}		
	}
}


if(isset($GO_MODULES->modules['tasks']))
{
	$db->query("ALTER TABLE `ta_tasks` ADD `files_folder_id` INT NOT NULL;");
	$db->query("SELECT t.*,l.name AS tasklist_name,l.acl_read,l.acl_write FROM ta_tasks t INNER JOIN ta_lists l ON l.id=t.tasklist_id");
	while($task = $db->next_record())
	{
		$old_path = 'tasks/'.$task['id'];
		$folder = $fsdb->resolve_path('tasks/'.$task['id']);

		$new_folder_name = File::strip_invalid_chars($task['name']);
		
		if($folder && !empty($new_folder_name))
		{			
			$new_path = 'tasks/'.File::strip_invalid_chars($task['tasklist_name']).'/'.date('Y', $task['due_time']);
						
			//echo $new_path."\n";
			$destination = $fsdb->resolve_path($new_path, true, 1);			
			
			$fs->mkdir_recursive($GO_CONFIG->file_storage_path.$new_path);
			
			$fs->move($GO_CONFIG->file_storage_path.$old_path, $GO_CONFIG->file_storage_path.$new_path.'/'.$new_folder_name);
			$new_folder_id = $fsdb->move_folder($folder, $destination);
			
			$up_folder['id']=$new_folder_id;
			$up_folder['name']=File::strip_invalid_chars(String::format_name($task));
			$up_folder['acl_read']=0;
			$up_folder['acl_write']=0;

			$fsdb->update_folder($up_folder);
			
			$up_task['id']=$task['id'];
			$up_task['files_folder_id']=$new_folder_id;
			
			$fsdb->update_row('ta_tasks', 'id', $up_task);
		}		
	}
}

?>