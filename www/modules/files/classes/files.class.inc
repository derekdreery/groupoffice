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

require_once($GLOBALS['GO_CONFIG']->class_path.'filesystem.class.inc');

class files extends filesystem
{
	
	var $reabable_paths = array();
	var $writable_paths = array();
	
	function __construct()
	{
		global $GO_CONFIG;
		
		parent::__construct();
		
		$this->readable_paths = array(
			$GO_CONFIG->tmpdir,				
			$GO_CONFIG->file_storage_path.'public/'
		);
		
		$this->writeable_paths = array(
			$GO_CONFIG->tmpdir
		);

		
		if(!empty($_SESSION['GO_SESSION']['username']))
		{	
			$this->readable_paths[] = $GO_CONFIG->file_storage_path.'users/'.$_SESSION['GO_SESSION']['username'].'/';
			$this->writeable_paths[] = $GO_CONFIG->file_storage_path.'users/'.$_SESSION['GO_SESSION']['username'].'/';
		}		
	}	
	
	/**
   * Check if a path is the user's home path
   *
   * @param int $user_id Group-Office user ID
   * @param string $path The path to check
   *
   * @access public
   * @return bool 
   */
	function is_home_path($user_id, $path)
	{
		global $GO_CONFIG, $GO_USERS;

		if ($user = $GO_USERS->get_user($user_id))
		{
			$home_path = $GO_CONFIG->file_storage_path.'users/'.$user['username'];

			if (dirname($path).utf8_basename($path) == dirname($home_path).utf8_basename($home_path))
			{
				return true;
			}
		}
		return false;
	}
	
	/**
   * Check if a user owns a path
   *
   * @param int $user_id Group-Office user ID
   * @param string $path The path to check
   *
   * @access public
   * @return bool 
   */
	function is_owner($user_id, $path)
	{
		global $GO_CONFIG, $GO_USERS;

		if(!empty($_SESSION['GO_SESSION']['username']))
		{
			$home_path = 'users/'.$_SESSION['GO_SESSION']['username'];

			if (strpos($path, $home_path) === 0)
			{
				return true;
			}
		}
		return false;
	}
	
	function check_share($full_path, $user_id, $acl_read, $acl_write, $quiet=true)
	{
		if(!file_exists($full_path))
		{
			$this->mkdir_recursive($full_path);
		}	

		$path = $this->strip_server_path($full_path);
		$folder = $this->get_folder($path);
		if(!$folder)
		{
			$folder['user_id']=$user_id;
			$folder['path']=$path;
			$folder['visible']='0';
			$folder['acl_read']=$acl_read;
			$folder['acl_write']=$acl_write;

			$this->add_folder($folder);
			
			if(!$quiet)
				echo 'Adding '.$path.'<br />';
		}else
		{
			if($folder['acl_read']!=$acl_read || $folder['acl_write']!=$acl_write)
			{				
				$up_folder['id']=$folder['id'];
				$up_folder['user_id']=$user_id;
				$up_folder['acl_read']=$acl_read;
				$up_folder['acl_write']=$acl_write;
				
				$this->update_folder($up_folder);
				if(!$quiet)
					echo 'Updating '.$path.'<br />';
			}
		}
	}

	function add_notification($path, $user_id)
	{
		$notification['path']=$path;
		$notification['user_id']=$user_id;

		$this->insert_row('fs_notifications', $notification);
	}

	function remove_notification($path, $user_id)
	{	
		$sql = "DELETE FROM fs_notifications WHERE path=? AND user_id=?";
		return $this->query($sql, 'si', $path, $user_id);
	}
	
	function remove_notifications($path)
	{
		$sql = "DELETE FROM fs_notifications WHERE path='".$this->escape($path)."'";
		return $this->query($sql);
	}

	function is_notified($path, $user_id)
	{
		$sql = "SELECT * FROM fs_notifications WHERE path='".$this->escape($path)."' AND user_id=".$this->escape($user_id);
		$this->query($sql);
		return $this->next_record();
	}
	
	function move_notifications($old_path, $new_path)
	{
		$sql = "UPDATE fs_notifications SET path='".$this->escape($new_path)."' WHERE path='".$this->escape($old_path)."'";
		return $this->query($sql);		
	}


	function get_users_to_notify($path)
	{
		$sql = "SELECT user_id FROM fs_notifications WHERE path='".$this->escape($path)."'";
		$this->query($sql);
		return $this->num_rows();
	}
	
	function notify_users($path, $modified_by_user_id, $modified=array(), $new=array(), $deleted=array())
	{
		global $GO_USERS, $GO_LANGUAGE, $GO_CONFIG, $GO_SECURITY;
		
		require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
		require($GO_LANGUAGE->get_language_file('files'));
		
		$user = $GO_USERS->get_user($modified_by_user_id);		
		$modified_by_user_name = String::format_name($user);		
		
		
		$changes = '';
		if(count($new))
		{
			$changes .= $lang['files']['new'].":\n".implode("\n", $new)."\n\n";
		}
		if(count($modified))
		{
			$changes .= $lang['files']['modified'].":\n".implode("\n", $modified)."\n\n";
		}
		
		if(count($deleted))
		{
			$changes .= $lang['files']['deleted'].":\n".implode("\n", $deleted)."\n\n";
		}
		
		$body = sprintf($lang['files']['folder_modified_body'], 
			$path,
			$modified_by_user_name,
			$changes);
			
		
		$users=array();
		$this->get_users_to_notify($path);
		while($this->next_record())
		{
			if($this->f('user_id')!=$GO_SECURITY->user_id)
			{
				$user = $GO_USERS->get_user($this->f('user_id'));
						
				$swift = new GoSwift($user['email'], $lang['files']['folder_modified_subject'],0,'3',$body);			
				$swift->sendmail($GO_CONFIG->webmaster_email, $GO_CONFIG->title);
			}
		}
	}



	/**
	 * Add a template
	 *
	 * @param Array $template Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_template($template)
	{
		$template['id']=$this->nextid('fs_templates');


		if($this->insert_row('fs_templates', $template))
		{
			return $template['id'];
		}
		return false;
	}

	/**
	 * Update a template
	 *
	 * @param Array $template Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_template($template)
	{

		return $this->update_row('fs_templates', 'id', $template);
	}


	/**
	 * Delete a template
	 *
	 * @param Int $template_id ID of the template
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_template($template_id)
	{
		return $this->query("DELETE FROM fs_templates WHERE id=".$this->escape($template_id));
	}


	/**
	 * Gets a template record
	 *
	 * @param Int $template_id ID of the template
	 *
	 * @access public
	 * @return Array Record properties
	 */

	function get_template($template_id, $with_content=false)
	{
		if($with_content)
		{
			$fields = '*';
		}else
		{
			$fields = 'id, name, user_id, extension, acl_read, acl_write';
		}
		$this->query("SELECT $fields FROM fs_templates WHERE id=".$this->escape($template_id));
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets a template record by the name field
	 *
	 * @param String $name Name of the template
	 *
	 * @access public
	 * @return Array Record properties
	 */

	function get_template_by_name($name)
	{
		$this->query("SELECT * FROM fs_templates WHERE template_name='".$this->escape($name)."'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}


	/**
	 * Gets authorized templates
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_authorized_templates($user_id, $start=0, $offset=0, $sortfield='id', $sortorder='ASC')
	{
		$user_id = $this->escape($user_id);
		$sql = "SELECT DISTINCT t.id, t.user_id, t.name, t.extension FROM fs_templates t ".
			"INNER JOIN go_acl a ON (a.acl_id=t.acl_read OR a.acl_id=t.acl_write) ".
			"LEFT JOIN go_users_groups ug ON a.group_id=ug.group_id ".
			"WHERE (a.user_id=".$this->escape($user_id)." OR ug.user_id=".$this->escape($user_id).") ORDER BY ".$this->escape($sortfield." ".$sortorder);

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}


	/**
	 * Gets writable templates
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_writable_templates($user_id, $start=0, $offset=0, $sortfield='id', $sortorder='ASC')
	{
		$user_id = $this->escape($user_id);
		
		$sql = "SELECT DISTINCT t.id, t.user_id, t.name, t.extension FROM fs_templates t ".
			"INNER JOIN go_acl a ON a.acl_id=t.acl_write ".
			"LEFT JOIN go_users_groups ug ON a.group_id=ug.group_id ".
			"WHERE (a.user_id=".$this->escape($user_id)." OR ug.user_id=".$this->escape($user_id).") ORDER BY ".$this->escape($sortfield." ".$sortorder);

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}



	function get_status_history($link_id)
	{
		$sql = "SELECT fs_status_history.*, fs_statuses.name AS status_name FROM ".
		"fs_status_history  ".
		"INNER JOIN fs_statuses ON fs_statuses.id=fs_status_history.status_id".
		" WHERE link_id='".$this->escape($link_id)."' ORDER BY ctime ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_status_name($status_id)
	{
		$sql = "SELECT name FROM fs_statuses WHERE id=".$this->escape($status_id);
		$this->query($sql);
		if($this->next_record())
		{
			return $this->f('name');
		}
		return false;
	}

	function get_statuses()
	{
		$sql = "SELECT * FROM fs_statuses";
		$this->query($sql);
		return $this->num_rows();
	}

	function change_status($link_id, $status_id, $comments)
	{
		global $GO_SECURITY;
		$link['link_id']=$link_id;
		$link['status_id']=$status_id;

		$this->update_row('fs_links','link_id',$link);

		$status['id']=$this->nextid('fs_status_history');
		$status['link_id']=$link_id;
		$status['status_id']=$status_id;
		$status['ctime']=time();
		$status['user_id']=$GO_SECURITY->user_id;
		$status['comments']=$comments;

		$this->insert_row('fs_status_history',$status);
	}

	function get_users_in_share($path)
	{
		global $GO_SECURITY;

		$users=array();
		$share= $this->find_share($path);
		if($share)
		{
			$users = $GO_SECURITY->get_authorized_users_in_acl($share['acl_read']);
			$write_users = $GO_SECURITY->get_authorized_users_in_acl($share['acl_write']);
			while($user_id = array_shift($write_users))
			{
				if(!in_array($user_id, $users))
				{
					$users[]=$user_id;
				}
			}
		}
		return $users;
	}


	function get_latest_files()
	{
		$sql = "SELECT * FROM fs_files ORDER BY mtime DESC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_file($path)
	{
		$sql = "SELECT * FROM fs_files WHERE path='".$this->escape($path)."';";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->record;
		}else
		{
			global $GO_CONFIG;

			$file['path'] = $path;
			$file['ctime']=@filectime($GO_CONFIG->file_storage_path.$path);
			$file['mtime']=@filemtime($GO_CONFIG->file_storage_path.$path);

			$this->add_file($file);

			return $this->get_file($path);
		}
	}
	
	function move_file($source_path, $destination_path)
	{
		$source_path=$this->strip_server_path($source_path);
		$destination_path=$this->strip_server_path($destination_path);
		
		$file = $this->get_file($source_path);
		$up_file['id']=$file['id'];
		$up_file['path']=$destination_path;
		$this->update_file($up_file);			
	}
	
	function move_folder($source_path, $destination_path)
	{
		$source_path=$this->strip_server_path($source_path);
		$destination_path=$this->strip_server_path($destination_path);
		
		$folder = $this->get_folder($source_path);
		$up_folder['id']=$folder['id'];
		$up_folder['path']=$destination_path;
		$this->update_folder($up_folder);
						
		$this->move_notifications($source_path, $destination_path);			
	}
	
	function delete_file($path)
	{
		global $GO_CONFIG;
		
		$path = $this->strip_server_path($path);
				
		require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
		$search = new search();
		
		$file = $this->get_file($path);
		$search->delete_search_result($file['id'], 6);		
		
		$sql = "DELETE FROM fs_files WHERE path=?";
		$this->query($sql, 's', $path);
	}

	function add_file($file)
	{
		if(!is_array($file))
		{
			$file = array('path'=>$file);
		}
		
		$file['ctime']=$file['mtime']=time();
		$file['id']=$this->nextid('fs_files');
		$this->insert_row('fs_files', $file);
		
		$this->cache_file($file['path']);
		
		return $file['id'];
	}

	function update_file($file)
	{
		if(isset($folder['id']))
		{
			$index = 'id';
		}else
		{
			$index=  'path';
		}
		
		$this->cache_file($file['path'], $index);

		$file['mtime']=time();
		$this->update_row('fs_files', $index, $file);
	}

	function get_folder($path)
	{
		$sql = "SELECT * FROM fs_folders WHERE path='".$this->escape($path)."';";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->record;
		}else
		{
			global $GO_LINKS;

			$folder['path'] = $path;

			$this->add_folder($folder);

			return $this->get_folder($path);
		}		
	}

	function add_folder($folder)
	{
		$folder['id']=$this->nextid('fs_folders');
		if(!isset($folder['user_id']))
		{
			global $GO_SECURITY;
			$folder['user_id']=$GO_SECURITY->user_id;
		}

		$this->insert_row('fs_folders', $folder);
	}

	function update_folder($folder)
	{
		if(isset($folder['id']))
		{
			$index = 'id';
		}else
		{
			$index=  'path';
		}


		$this->update_row('fs_folders', $index, $folder);
	}

	function delete_folder($path)
	{
		$path = $this->strip_server_path($path);
		
		$this->remove_notifications($path);
		$sql = "DELETE FROM fs_folders WHERE path=?";
		$this->query($sql, 's', $path);
	}
	
	/**
	 * Get the shares owned by a user.
	 *
	 * @param int $user_id Group-Office user ID
	 *
	 * @access public
	 * @return int Number of shares found,
	 */
	function get_authorized_shares($user_id, $visible_only=true)
	{
		$user_id=$this->escape($user_id);
		//ORDER BY PATH important so higher order shares come first
		$sql = "SELECT DISTINCT f.* FROM fs_folders f ".
		"INNER JOIN go_acl a ON (f.acl_read=a.acl_id OR f.acl_write=a.acl_id) ".
		"LEFT JOIN go_users_groups ug ON (a.group_id=ug.group_id) ".
		"WHERE (ug.user_id=".$this->escape($user_id)." OR a.user_id=".$this->escape($user_id).") AND f.user_id!=$user_id ";
		if($visible_only)
		{
			$sql .= "AND f.visible='1' ";
		}
		$sql .= "ORDER BY path ASC";

		$this->query($sql);
		return $this->num_rows();
		
	}

	/**
	 * Get the shares owned by a user.
	 *
	 * @param int $user_id Group-Office user ID
	 *
	 * @access public
	 * @return int Number of shares found,
	 */
	function get_user_shares($user_id)
	{
		//ORDER BY PATH important so higher order shares come first
		$sql = "SELECT * FROM fs_shares WHERE user_id='".$this->escape($user_id)."' ORDER BY path ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function find_share($path)
	{
		$dir = dirname($path);
		if($dir != '/')
		{
			$dir .= '/';
		}
		$path = $dir.utf8_basename($path);

		
		$folder = $this->get_folder($path);

		if ($folder && $folder['acl_read']>0)
		{
			return $folder;
		}else
		{
			global $GO_CONFIG;
			$parent = dirname($path);

			if ($parent == '' || $parent == '/' || substr($parent,1)==':\\' || $parent =='.')
			{
				return false;
			}else
			{
				return $this->find_share($parent);
			}
		}
	}

	function has_read_permission($user_id, $path)
	{
		$path = $this->check_path($path);
		
		foreach($this->readable_paths as $readable_path)
		{			
			if($path==$readable_path || $this->is_sub_dir($path, $readable_path))
			{
				return true;
			}
		}
	
		if ($share = $this->find_share($this->strip_server_path($path)))
		{
			global $GO_SECURITY;
			
			if($GO_SECURITY->has_permission($user_id, $share['acl_read']) || $GO_SECURITY->has_permission($user_id, $share['acl_write']))
			{
				return is_readable($path);
			}
		}
		
		return false;
	}

	function has_write_permission($user_id, $path)
	{
		$path = $this->check_path($path);
				
		foreach($this->writeable_paths as $writeable_path)
		{
			if($path==$writeable_path || $this->is_sub_dir($path, $writeable_path))
			{
				return true;
			}
		}
		
		global $GO_SECURITY;
		
		if ($share = $this->find_share($this->strip_server_path($path)))
		{			
			if($GO_SECURITY->has_permission($user_id, $share['acl_write']))
			{
				return is_writable($path);
			}
		}
		
		return false;
	}
	
	


	function strip_server_path($path)
	{
		global $GO_CONFIG;
		return substr($path, strlen($GO_CONFIG->file_storage_path));
	}


	function get_content_json($path, $sort='utf8_basename', $dir='ASC', $filter=null)
	{
		$results = array();

		$folders = $this->get_folders_sorted($path, 'utf8_basename', $dir);
		foreach($folders as $folder)
		{
			$db_folder = $this->get_folder($folder['path']);
			if($db_folder['acl_read']>0)
			{
				$class='folder-shared';
			}else
			{
				$class='filetype-folder';
			}

			$folder['path']=$this->strip_server_path($folder['path']);
			$folder['grid_display']='<div class="go-grid-icon '.$class.'">'.$folder['name'].'</div>';
			$folder['type']='Folder';
			$folder['mtime']=Date::get_timestamp($folder['mtime']);
			$folder['size']='-';
			$folder['extension']='folder';
			$results[]=$folder;
		}


		if(isset($filter))
		{
			$extensions = explode(',',$filter);
		}


		$files = $this->get_files_sorted($path, $sort, $dir);
		foreach($files as $file)
		{
			$extension = File::get_extension($file['name']);

			if(!isset($extensions) || in_array($extension, $extensions))
			{
				$file['extension']=$extension;
				$file['path']=$this->strip_server_path($file['path']);
				$file['grid_display']='<div class="go-grid-icon filetype filetype-'.$extension.'">'.$file['name'].'</div>';
				$file['type']=File::get_filetype_description($extension);
				$file['mtime']=Date::get_timestamp($file['mtime']);
				$file['size']=Number::format_size($file['size']);
				$results[]=$file;
			}
		}

		return $results;


	}



	function __on_check_database()
	{
		global $GO_USERS, $GO_CONFIG, $GO_SECURITY;
		
		$fs2 = new files();
		echo 'Deleting invalid folders in database<br />';
		$sql = "SELECT * FROM fs_folders";
		$this->query($sql);
		while($folder = $this->next_record())
		{
			$full_path = $GO_CONFIG->file_storage_path.$folder['path'];
			if(!is_dir($full_path))
			{
				$fs2->delete_folder($full_path);
			}
		}
		
		echo 'Deleting invalid files in database<br />';
		
		$sql = "SELECT * FROM fs_files";
		$this->query($sql);
		while($file = $this->next_record())
		{
			$full_path = $GO_CONFIG->file_storage_path.$file['path'];
			if(!file_exists($full_path))
			{
				$fs2->delete_file($full_path);
			}
		}
		
		echo "Checking user home directories<br />";
		
		$GO_USERS->get_users();
		
		while($GO_USERS->next_record())
		{
			$home_dir = $GO_CONFIG->file_storage_path.'users/'.$GO_USERS->f('username');
			if(!is_dir($home_dir))
			{
				mkdir($home_dir, $GO_CONFIG->create_mode,true);
				echo "Creating users/".$GO_USERS->f('username')."<br />";
			}
			
			$folder = $this->get_folder($this->strip_server_path($home_dir));
			
			if(empty($folder['acl_read']))
			{
				echo "Sharing users/".$GO_USERS->f('username')."<br />";
				
				$up_folder['id']=$folder['id'];
				$up_folder['user_id']=$GO_USERS->f('id');
				$up_folder['acl_read']=$GO_SECURITY->get_new_acl('files', $GO_USERS->f('id'));
				$up_folder['acl_write']=$GO_SECURITY->get_new_acl('files', $GO_USERS->f('id'));
				$up_folder['visible']='1';
				
				$this->update_folder($up_folder);
			}	
		}
	}



	function search($path, $keyword, $modified_later_then=0, $modified_earlier_then=0)
	{
		global $GO_SECURITY;

		if ($modified_earlier_then == 0)
		{
			$modified_earlier_then = time();
		}

		if($this->has_read_permission($GO_SECURITY->user_id, $path))
		{
			$folders = $this->get_folders($path);
			while ($folder = array_shift($folders))
			{
				$this->search($folder['path'], $keyword, $modified_later_then, $modified_earlier_then);
			}

			$folder['path'] = $path;
			$folder['name'] = utf8_basename($path);
			$folder['mtime'] = filemtime($path);
			$folder['size'] = filesize($path);
			$folder['type'] = mime_content_type($path);

			if (stristr(utf8_basename($path), $keyword) && $modified_later_then < $folder['mtime'] && $modified_earlier_then > $folder['mtime'])
			{
				$this->search_results[] = $folder;
			}

			$files = $this->get_files($path);
			while ($file = array_shift($files))
			{
				if (stristr($file['name'], $keyword) && $modified_later_then < $file['mtime'] && $modified_earlier_then > $file['mtime'])
				{
					$this->search_results[] = $file;
				}
			}
		}
		return $this->search_results;
	}
	
	function __on_add_user($params)
	{
		global $GO_CONFIG, $GO_SECURITY;
		
		$user=$params['user'];

		$userdir = $GO_CONFIG->file_storage_path.'users/'.$user['username'];
		
		if(!is_dir($userdir))
			mkdir($userdir, 0755, true);
			
		$folder = $this->get_folder('users/'.$user['username']);		
		if(empty($folder['acl_read']))
		{
			$up_folder['id']=$folder['id'];
			$up_folder['user_id']=$user['id'];
			$up_folder['acl_read']=$GO_SECURITY->get_new_acl('files', $user['id']);
			$up_folder['acl_write']=$GO_SECURITY->get_new_acl('files', $user['id']);
			$up_folder['visible']='1';
			
			$this->update_folder($up_folder);
		}
	}
	
	
	function __on_delete_user($user_id)
	{
		$fs = new filesystem();
		$this->get_all_shares($user_id);
		while($this->next_record())
		{
			$fs->delete_share($this->f('path'));
		}
		
		system('rm -Rf '.$GO_CONFIG->file_storage_path.'users/'.$username);
	}
	
	function cache_file($path, $index='path')
	{
		global $GO_CONFIG, $GO_LANGUAGE;
		require_once($GO_CONFIG->class_path.'/base/search.class.inc.php');
		$search = new search();
		
		require($GO_LANGUAGE->get_language_file('files'));

		$fs = new files();
		
		$sql = "SELECT * FROM fs_files WHERE $index=?;";
		$this->query($sql, 's', $path);
		$file = $this->next_record();
			
		$share = $fs->find_share(dirname($path));

		if($file && $share)
		{
			$cache['id']=$file['id'];
			$cache['user_id']=$file['user_id'];
			$cache['name'] = utf8_basename($path);
			$cache['link_type']=6;
			$cache['description']=substr($path, strlen($GO_CONFIG->file_storage_path));
			$cache['type']=$lang['files']['file'];
			$cache['module']='files';
			$cache['keywords']=$file['comments'].','.$cache['name'].','.$cache['type'];
			$cache['mtime']=$file['mtime'];
			$cache['acl_read']=$share['acl_read'];
			$cache['acl_write']=$share['acl_write'];
				
			$search->cache_search_result($cache);
		}
	}
	
	/**
	 * When a global search action is performed this function will be called for each module
	 */
	public function __on_build_search_index()
	{				
		$sql = "SELECT path FROM fs_files";		
		$this->query($sql);
		$fs = new files();
		while($record = $this->next_record())
		{
			$fs->cache_file($record['path']);
		}
/* {ON_BUILD_SEARCH_INDEX_FUNCTION} */
	}
}
