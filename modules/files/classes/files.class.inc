<?php

require_once($GLOBALS['GO_CONFIG']->class_path.'filesystem.class.inc');

class files extends filesystem
{
	function __construct()
	{
		parent::__construct();

		$this->disable_go_permissions=false;

	}

	function add_notification($path, $user_id)
	{
		$notification['path']=$path;
		$notification['user_id']=$user_id;

		$this->insert_row('fs_notifications', $notification);
	}

	function remove_notification($path, $user_id)
	{
		$sql = "DELETE FROM fs_notifications WHERE path='$path' AND user_id=$user_id";
		return $this->query($sql);
	}

	function is_notified($path, $user_id)
	{
		$sql = "SELECT * FROM fs_notifications WHERE path='$path' AND user_id=$user_id";
		$this->query($sql);
		return $this->next_record();
	}


	function get_users_to_notify($path)
	{
		$users_in_share=$this->get_users_in_share($path);
		$users=array();

		foreach($users_in_share as $user_id)
		{
			$fs_settings = $this->get_settings($user_id);
			if($fs_settings['notify']=='1' || $this->is_notified(addslashes($path), $user_id))
			{
				$users[]=$user_id;
			}
		}
		return $users;

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
		return $this->query("DELETE FROM fs_templates WHERE id=$template_id");
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
		$this->query("SELECT $fields FROM fs_templates WHERE id=$template_id");
		if($this->next_record())
		{
			return $this->Record;
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
		$this->query("SELECT * FROM fs_templates WHERE template_name='$name'");
		if($this->next_record())
		{
			return $this->Record;
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
		$sql = "SELECT DISTINCT t.id, t.user_id, t.name, t.extension FROM fs_templates t ".
			"INNER JOIN go_acl a ON (a.acl_id=t.acl_read OR a.acl_id=t.acl_write) ".
			"LEFT JOIN go_users_groups ug ON a.group_id=ug.group_id ".
			"WHERE (a.user_id=$user_id OR ug.user_id=$user_id) ORDER BY $sortfield $sortorder";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
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
		$sql = "SELECT DISTINCT t.id, t.user_id, t.name, t.extension FROM fs_templates t ".
			"INNER JOIN go_acl a ON a.acl_id=t.acl_write ".
			"LEFT JOIN go_users_groups ug ON a.group_id=ug.group_id ".
			"WHERE (a.user_id=$user_id OR ug.user_id=$user_id) ORDER BY $sortfield $sortorder";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;
	}



	function get_status_history($link_id)
	{
		$sql = "SELECT fs_status_history.*, fs_statuses.name AS status_name FROM ".
		"fs_status_history  ".
		"INNER JOIN fs_statuses ON fs_statuses.id=fs_status_history.status_id".
		" WHERE link_id='$link_id' ORDER BY ctime ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_status_name($status_id)
	{
		$sql = "SELECT name FROM fs_statuses WHERE id=$status_id";
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
		$status['ctime']=gmmktime();
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
		$sql = "SELECT * FROM fs_files WHERE path='$path';";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}else
		{
			global $GO_LINKS;

			$file['path'] = $path;
			$file['ctime']=filectime($path);
			$file['mtime']=filemtime($path);

			$this->add_file($file);

			return $this->get_file($path);
		}
	}

	function delete_file($path)
	{
		$sql = "DELETE FROM fs_files WHERE path='$path'";
		$this->query($sql);
	}

	function add_file($file)
	{
		$file['ctime']=$file['mtime']=gmmktime();
		$file['id']=$this->nextid('fs_files');
		$this->insert_row('fs_files', $file);
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

		$file['mtime']=gmmktime();
		$this->update_row('fs_files', $index, $file);
	}
















	function get_folder($path)
	{
		$sql = "SELECT * FROM fs_folders WHERE path='".$path."';";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
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
		$sql = "DELETE FROM fs_folders WHERE path='$path'";
		$this->query($sql);
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
		//ORDER BY PATH important so higher order shares come first
		$sql = "SELECT DISTINCT f.* FROM fs_folders f ".
		"INNER JOIN go_acl a ON (f.acl_read=a.acl_id OR f.acl_write=a.acl_id) ".
		"LEFT JOIN go_users_groups ug ON (a.group_id=ug.group_id) ".
		"WHERE (ug.user_id=$user_id OR a.user_id=$user_id) AND f.user_id!=$user_id ";
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
		$sql = "SELECT * FROM fs_shares WHERE user_id='$user_id' ORDER BY path ASC";
		$this->query($sql);
		return $this->num_rows();
	}








	function find_share($path)
	{
		$path = dirname($path).'/'.basename($path);

		$this->count++;

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
		global $GO_CONFIG;

		if ($this->disable_go_permissions || $this->is_owner($user_id, $path) || $path == $GO_CONFIG->file_storage_path)
		{
			return is_readable($path);
		}else
		{

			//go_log(LOG_DEBUG, var_export($path, true));
			if ($share = $this->find_share($path))
			{

				global $GO_SECURITY;
				if($GO_SECURITY->has_permission($user_id, $share['acl_read']) || $GO_SECURITY->has_permission($user_id, $share['acl_write']))
				{
					return is_readable($path);
				}
			}
			global $GO_CONFIG;
			if (strpos($path, $GO_CONFIG->tmpdir) === 0)
			{
				return is_readable($path);
			}
		}
		return false;
	}

	function has_write_permission($user_id, $path)
	{
		global $GO_CONFIG;

		if ($this->disable_go_permissions || $this->is_owner($user_id, $path))
		{
			return is_writable($path);
		}else
		{

			global $GO_SECURITY;
			if ($share = $this->find_share($path))
			{
				if($GO_SECURITY->has_permission($user_id, $share['acl_write']))
				{
					return is_writable($path);
				}
			}

			if (strpos($path, $GO_CONFIG->tmpdir) === 0)
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


	function get_content_json($path, $sort='basename', $dir='ASC', $filter=null)
	{

		$results = array();

		$folders = $this->get_folders_sorted($path, 'basename', $dir);
		foreach($folders as $folder)
		{
			$db_folder = $this->get_folder(addslashes($folder['path']));
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
			$folder['name'] = basename($path);
			$folder['mtime'] = filemtime($path);
			$folder['size'] = filesize($path);
			$folder['type'] = mime_content_type($path);

			if (stristr(basename($path), $keyword) && $modified_later_then < $folder['mtime'] && $modified_earlier_then > $folder['mtime'])
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
		global $GO_CONFIG;
		
		$user=$params['user'];

		$userdir = $GO_CONFIG->file_storage_path.'users/'.stripslashes($user['username']);
		
		if(!is_dir($userdir))
			mkdir($userdir, 0755, true);
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

	function __on_search($last_sync_time=0)
	{
		global $GO_MODULES, $GO_LANGUAGE;

		//require($GO_LANGUAGE->get_language_file('files'));

		$sql = "SELECT * FROM fs_files WHERE mtime>$last_sync_time";
		$this->query($sql);
		$fs = new files();
		$search = new search();

		while($this->next_record())
		{

			$cache['id']=$this->f('id');
			$cache['user_id']=$this->f('user_id');
			$cache['name'] = addslashes(basename($this->f('path')));
			$cache['link_type']=6;
			$cache['description']=$fs->f('comments');
			$cache['type']='File';
			$cache['keywords']=$cache['name'].','.$cache['type'];
			$cache['mtime']=$this->f('mtime');

			$share = $fs->find_share(addslashes($this->f('path')));
			if($share)
			{
				$cache['acl_read']=$share['acl_read'];
				$cache['acl_write']=$share['acl_write'];
			}

			$search->cache_search_result($cache);

		}
	}

	

}
