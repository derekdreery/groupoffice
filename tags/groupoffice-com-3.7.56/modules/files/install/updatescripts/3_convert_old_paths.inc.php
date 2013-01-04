<?php
/*if(isset($argv[1]))
{
    define('CONFIG_FILE', $argv[1]);
}

chdir(dirname(__FILE__));

require('../../../../Group-Office.php');

$db = new db();
$db->halt_on_error='report';*/

ini_set('memory_limit','500M');

$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";

echo "Converting old paths...".$line_break;
flush();

require_once($GO_CONFIG->class_path.'filesystem.class.inc');

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
				$file['size']=@filesize($GO_CONFIG->file_storage_path.$path);
        $fsdb->update_row('fs_files', 'id', $file);

        return $file['id'];
    }else
    {
        $file['path']=$path;
        $file['name']=utf8_basename($path);
        $file['ctime']=@filectime($GO_CONFIG->file_storage_path.$path);
        $file['mtime']=@filemtime($GO_CONFIG->file_storage_path.$path);
        $file['size']=@filesize($GO_CONFIG->file_storage_path.$path);
        $file['folder_id']=$parent_id;
				$file['id']=$fsdb->nextid('fs_files');
				$file['user_id']=$GLOBALS['GO_SECURITY']->user_id;
				$file['extension']=File::get_extension($file['name']);
				$fsdb->insert_row('fs_files', $file);


        return $file['id'];

    }
}

function get_folder($path, $parent_id)
{
    global $fs, $fsdb, $GO_CONFIG, $line_break;


    echo 'Getting folder '.$path.$line_break;

    $sql = "SELECT * FROM fs_folders WHERE path='".$fsdb->escape($path)."';";
    $fsdb->query($sql);
    if($folder = $fsdb->next_record())
    {
        $folder['name']=utf8_basename($path);
        $folder['parent_id']=$parent_id;
        $folder['ctime']=@filectime($GO_CONFIG->file_storage_path.$path);
        $folder['mtime']=time();
				$fsdb->update_row('fs_folders', 'id', $folder);

        return $folder['id'];
    }else
    {
        $folder['path']=$path;
        $folder['name']=utf8_basename($path);
        $folder['ctime']=@filectime($GO_CONFIG->file_storage_path.$path);
        $folder['parent_id']=$parent_id;
				$folder['id']=$fsdb->nextid('fs_folders');

				$fsdb->insert_row('fs_folders', $folder);
        return $folder['id'];
    }
}



function crawl($path, $parent_id)
{
    global $fs, $fsdb;

    $line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";
    echo 'Crawling folder '.$path.$line_break;
		flush();

    $folder_id = get_folder($fsdb->strip_server_path($path), $parent_id);

    $folders = $fs->get_folders($path);
    //var_dump($folders);
    while($folder = array_shift($folders))
    {
			if(basename($path)=='billing' && $folder['name']=='notifications')
			{
				continue;
			}
      crawl($folder['path'], $folder_id);
    }


    $files = $fs->get_files($path);
    while($file = array_shift($files))
    {
        get_file($fsdb->strip_server_path($file['path']),$folder_id);
    }
}

$db->query("replace into go_db_sequence select 'fs_files', max(id) from fs_files");

$db->query("ALTER TABLE `fs_files` ADD `extension` VARCHAR( 4 ) NOT NULL ,ADD INDEX ( extension )");

/*if(!isset($_REQUEST['skip_crawl']))
{
	$db->query("ALTER TABLE `fs_files` ADD INDEX ( `path` ) ");
	$db->query("ALTER TABLE `fs_folders` ADD INDEX ( `path` ) ");

	$folders = $fs->get_folders($GO_CONFIG->file_storage_path);

	foreach($folders as $folder)
	{
			crawl($folder['path'], 0);
	}

	$fsdb->query("DELETE FROM fs_folders WHERE name=''");
}*/


if(isset($GO_MODULES->modules['addressbook']))
{
    $db->query("ALTER TABLE `ab_contacts` ADD `files_folder_id` INT NOT NULL;");
    $db->query("SELECT c.*,a.name AS addressbook_name FROM ab_contacts c INNER JOIN ab_addressbooks a ON a.id=c.addressbook_id");
    while($contact = $db->next_record())
    {
        try{
            $old_path = 'contacts/'.$contact['id'];
            $folder = $fsdb->resolve_path($old_path);

            $new_folder_name = File::strip_invalid_chars(String::format_name($contact));

            if($folder && !empty($new_folder_name))
            {
                $last_part = strtoupper($contact['last_name'][0]);
                $new_path = 'contacts/'.File::strip_invalid_chars($contact['addressbook_name']);
                if(!empty($last_part))
                {
                    $new_path .= '/'.$last_part;
                }else
								{
									$new_path .= '/0 no last name';
								}

                //echo $new_path."\n";
                $destination = $fsdb->resolve_path($new_path, true, 1);


                $fs->mkdir_recursive($GO_CONFIG->file_storage_path.$new_path);

                $fs->move($GO_CONFIG->file_storage_path.$old_path, $GO_CONFIG->file_storage_path.$new_path.'/'.$new_folder_name);
                $new_folder_id = $fsdb->move_folder($folder, $destination);

                $up_folder['id']=$new_folder_id;
                $up_folder['name']=File::strip_invalid_chars(String::format_name($contact));
                //$up_folder['acl_id']=0;
                $up_folder['readonly']='1';

                $fsdb->update_row('fs_folders', 'id', $up_folder);

                $up_contact['id']=$contact['id'];
                $up_contact['files_folder_id']=$new_folder_id;

                $fsdb->update_row('ab_contacts', 'id', $up_contact);
            }
        }
        catch(Exception $e)
        {
            echo $e->getMessage().$line_break;
        }
    }

    $db->query("ALTER TABLE `ab_companies` ADD `files_folder_id` INT NOT NULL;");
    $db->query("SELECT c.*,a.name AS addressbook_name FROM ab_companies c INNER JOIN ab_addressbooks a ON a.id=c.addressbook_id");
    while($company = $db->next_record())
    {
        try{
            $old_path = 'companies/'.$company['id'];
            $folder = $fsdb->resolve_path($old_path);

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
                //$up_folder['acl_id']=0;
                $up_folder['readonly']='1';

                $fsdb->update_row('fs_folders', 'id', $up_folder);

                $up_company['id']=$company['id'];
                $up_company['files_folder_id']=$new_folder_id;

                $fsdb->update_row('ab_companies', 'id', $up_company);
            }
        }

        catch(Exception $e)
        {
            echo $e->getMessage().$line_break;
        }
    }
}

if(isset($GO_MODULES->modules['notes']))
{
    $db->query("ALTER TABLE `no_notes` ADD `files_folder_id` INT NOT NULL;");
    $db->query("SELECT n.*,c.name AS category_name FROM no_notes n INNER JOIN no_categories c ON c.id=n.category_id");
    while($note = $db->next_record())
    {
        try{
            $old_path = 'notes/'.$note['id'];
            $folder = $fsdb->resolve_path($old_path);

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
                $up_folder['name']=File::strip_invalid_chars($note['name']);
                //$up_folder['acl_id']=0;
                $up_folder['readonly']='1';

                $fsdb->update_row('fs_folders', 'id', $up_folder);

                $up_note['id']=$note['id'];
                $up_note['files_folder_id']=$new_folder_id;

                $fsdb->update_row('no_notes', 'id', $up_note);
            }
        }
        catch(Exception $e)
        {
            echo $e->getMessage().$line_break;
        }
    }
}


if(isset($GO_MODULES->modules['tasks']))
{
    $db->query("ALTER TABLE `ta_tasks` ADD `files_folder_id` INT NOT NULL;");
    $db->query("SELECT t.*,l.name AS tasklist_name FROM ta_tasks t INNER JOIN ta_lists l ON l.id=t.tasklist_id");
    while($task = $db->next_record())
    {
        try{
            $old_path = 'tasks/'.$task['id'];
            $folder = $fsdb->resolve_path($old_path);

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
                $up_folder['name']=File::strip_invalid_chars($task['name']);
                //$up_folder['acl_id']=0;

                $up_folder['readonly']='1';

                $fsdb->update_row('fs_folders', 'id', $up_folder);

                $up_task['id']=$task['id'];
                $up_task['files_folder_id']=$new_folder_id;

                $fsdb->update_row('ta_tasks', 'id', $up_task);
            }
        }
        catch(Exception $e)
        {
            echo $e->getMessage().$line_break;
        }
    }
}



if(isset($GO_MODULES->modules['calendar']))
{
    $db->query("ALTER TABLE `cal_events` ADD `files_folder_id` INT NOT NULL;");
    $db->query("SELECT e.*,c.name AS calendar_name FROM cal_events e INNER JOIN cal_calendars c ON c.id=e.calendar_id");
    while($event = $db->next_record())
    {
        try{
            $old_path = 'events/'.$event['id'];
            $folder = $fsdb->resolve_path($old_path);

            $new_folder_name = File::strip_invalid_chars($event['name']);

            if($folder && !empty($new_folder_name))
            {
                $new_path = 'events/'.File::strip_invalid_chars($event['calendar_name']).'/'.date('Y', $event['start_time']);

                //echo $new_path."\n";
                $destination = $fsdb->resolve_path($new_path, true, 1);

                $fs->mkdir_recursive($GO_CONFIG->file_storage_path.$new_path);

                $fs->move($GO_CONFIG->file_storage_path.$old_path, $GO_CONFIG->file_storage_path.$new_path.'/'.$new_folder_name);
                $new_folder_id = $fsdb->move_folder($folder, $destination);

                $up_folder['id']=$new_folder_id;
                $up_folder['name']=File::strip_invalid_chars($event['name']);
                //$up_folder['acl_id']=0;
                $up_folder['readonly']='1';

                $fsdb->update_row('fs_folders', 'id', $up_folder);

                $up_event['id']=$event['id'];
                $up_event['files_folder_id']=$new_folder_id;

                $fsdb->update_row('cal_events', 'id', $up_event);
            }
        }
        catch(Exception $e)
        {
            echo $e->getMessage().$line_break;
        }
    }
}


if(isset($GO_MODULES->modules['billing']))
{
    $db->query("ALTER TABLE `bs_orders` ADD `files_folder_id` INT NOT NULL;");
    $db->query("SELECT e.*,c.name AS book_name FROM bs_orders e INNER JOIN bs_books c ON c.id=e.book_id");
    while($order = $db->next_record())
    {
        try{
            $old_path = 'billing/'.$order['id'];
            $folder = $fsdb->resolve_path($old_path);

            $new_folder_name = File::strip_invalid_chars($order['id'].' '.$order['customer_name']);

            if($folder && !empty($new_folder_name))
            {
                $new_path = 'billing/'.File::strip_invalid_chars($order['book_name']).'/'.date('Y', $order['btime']);

                $destination = $fsdb->resolve_path($new_path, true, 1);

                $fs->mkdir_recursive($GO_CONFIG->file_storage_path.$new_path);

                $fs->move($GO_CONFIG->file_storage_path.$old_path, $GO_CONFIG->file_storage_path.$new_path.'/'.$new_folder_name);
                $new_folder_id = $fsdb->move_folder($folder, $destination);

                $up_folder['id']=$new_folder_id;
                $up_folder['name']=File::strip_invalid_chars($order['id'].' '.$order['customer_name']);
                //$up_folder['acl_id']=0;
                $up_folder['readonly']='1';

								$fsdb->update_row('fs_folders', 'id', $up_folder);

                $up_order['id']=$order['id'];
                $up_order['files_folder_id']=$new_folder_id;

                $fsdb->update_row('bs_orders', 'id', $up_order);
            }
        }
        catch(Exception $e)
        {
            echo $e->getMessage().$line_break;
        }
    }
}

if(isset($GO_MODULES->modules['projects']))
{
    require_once($GO_MODULES->modules['projects']['class_path'].'projects.class.inc.php');
    $projects = new projects();

    $db->query("ALTER TABLE `pm_projects` ADD `files_folder_id` INT NOT NULL;");
    $db->query("SELECT e.*,c.name AS type_name FROM pm_projects e INNER JOIN pm_types c ON c.id=e.type_id");
    while($project = $db->next_record())
    {
        try{
            $old_path = 'projects/'.$project['id'];
            $folder = $fsdb->resolve_path($old_path);

            $new_folder_name = File::strip_invalid_chars($project['name']);

            if($folder && !empty($new_folder_name))
            {
                $new_path = dirname($projects->build_project_files_path($project, array('name'=>$project['type_name'])));

                $destination = $fsdb->resolve_path($new_path, true, 1);

                $fs->mkdir_recursive($GO_CONFIG->file_storage_path.$new_path);

                $fs->move($GO_CONFIG->file_storage_path.$old_path, $GO_CONFIG->file_storage_path.$new_path.'/'.$new_folder_name);
                $new_folder_id = $fsdb->move_folder($folder, $destination);

                $up_folder['id']=$new_folder_id;
                $up_folder['name']=$new_folder_name;
                //$up_folder['acl_id']=0;
                $up_folder['readonly']='1';

                $fsdb->update_row('fs_folders', 'id', $up_folder);

                $up_project['id']=$project['id'];
                $up_project['files_folder_id']=$new_folder_id;

                $fsdb->update_row('pm_projects', 'id', $up_project);
            }
        }
        catch(Exception $e)
        {
            echo $e->getMessage().$line_break;
        }
    }
}

if(isset($GO_MODULES->modules['cms']))
{
    $db->query("ALTER TABLE `cms_sites` ADD `files_folder_id` INT NOT NULL");
    $db->query("ALTER TABLE `cms_files` ADD `files_folder_id` INT NOT NULL");
    $folder = $fsdb->resolve_path('public/cms',true,1);
    $sql = "SELECT * FROM cms_sites";
    $db->query($sql);
    $db2 = new db();
    while($site = $db->next_record())
    {
        try{
            $new_path = 'public/cms/'.$site['name'];
            $old_path = 'public/cms/'.$site['id'];


            if(is_dir($GO_CONFIG->file_storage_path.$old_path))
            {
                $fs->mkdir_recursive($GO_CONFIG->file_storage_path.dirname($new_path));
                $fs->move($GO_CONFIG->file_storage_path.$old_path, $GO_CONFIG->file_storage_path.$new_path);
            }
            $up_site['files_folder_id']=$fsdb->import_folder($GO_CONFIG->file_storage_path.$new_path, $folder['id']);
            $up_site['id']=$site['id'];

            $db2->update_row('cms_sites','id', $up_site);
        }
        catch(Exception $e)
        {
            echo $e->getMessage().$line_break;
        }
    }


}

require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

$db->query("ALTER TABLE `go_users` ADD `files_folder_id` INT NOT NULL;");
$GO_USERS->get_users();
while($user = $GO_USERS->next_record())
{
    try{
        $old_path = 'users/'.$user['id'];
        $folder = $fsdb->resolve_path($old_path);

        $new_folder_name = $user['username'];

        if($folder && !empty($new_folder_name))
        {
            $new_path = 'adminusers/'.$user['username'];

            $destination = $fsdb->resolve_path($new_path, true, 1);

            $fs->mkdir_recursive($GO_CONFIG->file_storage_path.$new_path);

            $fs->move($GO_CONFIG->file_storage_path.$old_path, $GO_CONFIG->file_storage_path.$new_path.'/'.$new_folder_name);
            $new_folder_id = $fsdb->move_folder($folder, $destination);

            $up_folder['id']=$new_folder_id;
            $up_folder['name']=$new_folder_name;
            //$up_folder['acl_id']=0;
            $up_folder['readonly']='1';

						$fsdb->update_row('fs_folders', 'id', $up_folder);

            $up_user['id']=$user['id'];
            $up_user['files_folder_id']=$new_folder_id;

            $fsdb->update_row('go_users', 'id', $up_user);
        }
    }
    catch(Exception $e)
    {
        echo $e->getMessage().$line_break;
    }
}


//the installer will check the database
$CHECK_MODULES=true;
?>