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

	public function __on_load_listeners($events){
		$events->add_listener('check_database', __FILE__, 'files', 'check_database');
		$events->add_listener('user_delete', __FILE__, 'files', 'user_delete');
		$events->add_listener('user_delete', __FILE__, 'files', 'user_delete');
		$events->add_listener('add_user', __FILE__, 'files', 'add_user');
		$events->add_listener('build_search_index', __FILE__, 'files', 'build_search_index');
		$events->add_listener('login', __FILE__, 'files', 'login');
	}
	

	function get_thumb_url($path)
	{
		global $GO_THEME, $GO_CONFIG;

		$extension = File::get_extension($path);

		switch($extension)
		{
			case 'jpg':
			case 'jpeg';
			case 'png';
			case 'gif';
			return phpThumbURL('src='.$path.'&w=100&h=100&zc=1');
			break;
				
			case 'pdf':
				return $GO_THEME->image_url.'128x128/filetypes/pdf.png';
				break;

			case 'tar':
			case 'tgz':
			case 'gz':
			case 'bz2':
			case 'zip':
				return $GO_THEME->image_url.'128x128/filetypes/zip.png';
				break;
			case 'odt':
			case 'docx':
			case 'doc':
				return $GO_THEME->image_url.'128x128/filetypes/doc.png';
				break;
					
			case 'odc':
			case 'xls':
			case 'xlsx':
				return $GO_THEME->image_url.'128x128/filetypes/spreadsheet.png';
				break;
					
			case 'odp':
			case 'pps':
			case 'pptx':
				return $GO_THEME->image_url.'128x128/filetypes/pps.png';
				break;

			case 'htm':
				return $GO_THEME->image_url.'128x128/filetypes/doc.png';
				break;
					
			default:
				if(file_exists($GO_THEME->theme_path.'images/128x128/filetypes/'.$extension.'.png'))
				{
					return $GO_THEME->image_url.'128x128/filetypes/'.$extension.'.png';
				}else
				{
					return $GO_THEME->image_url.'128x128/filetypes/unknown.png';
				}
				break;
					
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
		return $this->query($sql, 'si', array($path, $user_id));
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
		if($this->next_record())
		{
			return true;
		}else
		{
			return false;
		}
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
				$swift->set_from($GO_CONFIG->webmaster_email, $GO_CONFIG->title);
				$swift->sendmail();
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

	function add_template($template, $types='')
	{
		if(!empty($types) && !isset($template['id']))
		{
			$types.='i';
		}

		$template['id']=$this->nextid('fs_templates');

		if($this->insert_row('fs_templates', $template, $types))
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

	function update_template($template, $types='')
	{
		return $this->update_row('fs_templates', 'id', $template, $types);
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
		global $GO_CONFIG;
		if($this->is_sub_dir($source_path, $GO_CONFIG->file_storage_path))
		{
			$file = $this->get_file($this->strip_server_path($source_path));
			$this->delete_file($GO_CONFIG->file_storage_path.$destination_path);
				
			$up_file['id']=$file['id'];
			$up_file['path']=$this->strip_server_path($destination_path);
			$this->update_file($up_file);
				
		}elseif($this->is_sub_dir($destination_path, $GO_CONFIG->file_storage_path))
		{
			$file = $this->get_file($this->strip_server_path($destination_path));
			$up_file['id']=$file['id'];
			$up_file['path']=$this->strip_server_path($destination_path);
			$this->update_file($up_file);
		}
	}

	function move_folder($source_path, $destination_path)
	{
		global $GO_CONFIG;
		if($this->is_sub_dir($source_path, $GO_CONFIG->file_storage_path))
		{
			$this->delete_folder($destination_path);
				
			$source_path=$this->strip_server_path($source_path);
			$destination_path=$this->strip_server_path($destination_path);
				
			$folder = $this->get_folder($source_path);
			$up_folder['id']=$folder['id'];
			$up_folder['path']=$destination_path;
			$this->update_folder($up_folder);
				
			$this->move_notifications($source_path, $destination_path);
		}
	}

	function delete_file($path)
	{
		global $GO_CONFIG;

		if($this->is_sub_dir($path, $GO_CONFIG->file_storage_path))
		{
			$path = $this->strip_server_path($path);
				
			require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
			$search = new search();
				
			$file = $this->get_file($path);
			$search->delete_search_result($file['id'], 6);
				
			$sql = "DELETE FROM fs_files WHERE path=?";
			$this->query($sql, 's', $path);
			
			$this->delete_new_filelink($file['id']);
		}
	}

	function add_file($file)
	{
		global $GO_CONFIG;

		if(!is_array($file))
		{
			$file = array('path'=>$file);
		}

		$file['ctime']=$file['mtime']=time();
		$file['id']=$this->nextid('fs_files');
		$file['user_id']=$GLOBALS['GO_SECURITY']->user_id;
		$this->insert_row('fs_files', $file);

		$this->cache_file($GO_CONFIG->file_storage_path.$file['path']);	

		$this->add_new_filelink($file);
		
		return $file['id'];
	}	
	
	function update_file($file)
	{
		if(isset($file['id']))
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



	// NEW FUNCTIONS
	function add_new_filelink($file)
	{
		$users = $this->get_users_in_share($file['path']);
	
		for($i=0; $i<count($users); $i++)
		{			
			if($users[$i] != $file['user_id'])
			{
				$this->insert_row('fs_new_files', array('file_id' => $file['id'], 'user_id' => $users[$i]));
			}
		}	
	}
	
	function check_existing_filelink($file_id, $user_id)
	{
		$this->query("SELECT * FROM fs_new_files WHERE user_id = ? AND file_id = ?", 'ii', array($user_id, $file_id));
		return $this->num_rows();
	}
	
	function delete_all_new_filelinks($user_id)
	{
		$this->query("DELETE FROM fs_new_files WHERE user_id = ?", 'i', $user_id);
		return $this->num_rows();
	}
	function delete_new_filelink($file_id, $user_id=0)
	{
		if($user_id > 0)
		{
			$this->query("DELETE FROM fs_new_files WHERE file_id = ? AND user_id = ?", 'ii', array($file_id, $user_id));
		} else {
			$this->query("DELETE FROM fs_new_files WHERE file_id = ?", 'i', $file_id);
		}
	}
	
	function get_num_new_files($user_id)
	{
		$this->query("SELECT id FROM fs_new_files AS fn, fs_files AS ff WHERE fn.file_id = ff.id AND fn.user_id = ?", 'i', $user_id);
		return $this->num_rows();
	}
	
	function get_new_files($user_id, $sort='name', $dir='DESC')
	{		
		global $GO_CONFIG;

		$this->query("SELECT path FROM fs_new_files AS fn, fs_files AS ff WHERE fn.file_id = ff.id AND fn.user_id = ?", 'i', $user_id);
					
		$files = array();
		while($item = $this->next_record())
		{			
			$file = array();
			$file['path'] = $GO_CONFIG->file_storage_path.$item['path'];
			$file['name'] = utf8_basename($file['path']);
			$file['mtime'] = filemtime($file['path']);
			$file['size'] = filesize($file['path']);
			$file['type'] = File::get_mime($file['path']);

			$files[] = $file;
		}

		if(count($files) > 1)
		{
			// only sort when there is something to sort
			return $this->sksort($files, $sort, $dir);
		} else
		{
			return $files;
		}
	}

	public static function login($username, $password, $user)
	{		
		// Default timeout: 30 days
		$timeout = 60*60*24*30;
		$deltime = time() - $timeout;

		$fs = new files();
		
		$fs->query("SELECT ff.id FROM fs_new_files AS fn, fs_files AS ff 
			WHERE fn.file_id = ff.id AND ctime < ? AND fn.user_id = ?", 'ii', array($deltime, $user['id']));
		
		$files = array();
		if($fs->num_rows() > 0)
		{
			while($file = $fs->next_record())
			{
				$files[] = $file['id'];
			}
			$fs->query("DELETE FROM fs_new_files WHERE file_id IN (".implode(',', $files).") ");
		}	
	}
	
	function sksort($array, $sort='name', $dir='DESC') 
	{
	    if (count($array)) 
	    {
	        $temp_array[key($array)] = array_shift($array);
	    }
	
	    foreach($array as $key => $val)
	    {
	        $offset = 0;
	        $found = false;
	        foreach($temp_array as $tmp_key => $tmp_val)
	        {
	            if(!$found and strtolower($val[$sort]) > strtolower($tmp_val[$sort]))
	            {
	                $temp_array = array_merge((array)array_slice($temp_array,0,$offset), array($key => $val), array_slice($temp_array,$offset));
	                $found = true;
	            }
	            $offset++;
	        }
	        if(!$found)
	        {
	        	$temp_array = array_merge($temp_array, array($key => $val));
	        }
	    }
	
	    $array = ($dir == 'DESC') ? $temp_array : array_reverse($temp_array);
	   
	    return $array;
	}
	// END NEW FUNCTIONS
	
	

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

	
	public static function check_database()
	{
		global $GO_USERS, $GO_CONFIG, $GO_SECURITY;

		$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";
		
		$fs = new files();

		$fs2 = new files();
		echo 'Deleting invalid folders in database'.$line_break;
		$sql = "SELECT * FROM fs_folders";
		$fs->query($sql);
		while($folder = $fs->next_record())
		{
			$full_path = $GO_CONFIG->file_storage_path.$folder['path'];
			if(!is_dir($full_path))
			{
				$fs2->delete_folder($full_path);
			}
		}

		echo 'Deleting invalid files in database'.$line_break;

		$sql = "SELECT * FROM fs_files";
		$fs->query($sql);
		while($file = $fs->next_record())
		{
			$full_path = $GO_CONFIG->file_storage_path.$file['path'];
			if(!file_exists($full_path))
			{
				$fs2->delete_file($full_path);
			}
		}

		echo "Checking user home directories$line_break";

		$GO_USERS->get_users();

		while($GO_USERS->next_record())
		{
			$home_dir = $GO_CONFIG->file_storage_path.'users/'.$GO_USERS->f('username');
			if(!is_dir($home_dir))
			{
				mkdir($home_dir, $GO_CONFIG->folder_create_mode,true);
				echo "Creating users/".$GO_USERS->f('username').$line_break;
			}
				
			$folder = $fs->get_folder($fs->strip_server_path($home_dir));
				
			if(empty($folder['acl_read']))
			{
				echo "Sharing users/".$GO_USERS->f('username').$line_break;

				$up_folder['id']=$folder['id'];
				$up_folder['user_id']=$GO_USERS->f('id');
				$up_folder['acl_read']=$GO_SECURITY->get_new_acl('files', $GO_USERS->f('id'));
				$up_folder['acl_write']=$GO_SECURITY->get_new_acl('files', $GO_USERS->f('id'));
				$up_folder['visible']='1';

				$fs->update_folder($up_folder);
			}
		}

		echo 'Correcting id=0'.$line_break;

		$sql = "SELECT path FROM fs_folders WHERE id=0";
		$fs->query($sql);
		while($r = $fs->next_record())
		{
			$r['id']=$fs2->nextid('fs_folders');
			$fs2->update_row('fs_folders', 'path', $r);
		}
	}

	function crawl($path)
	{
		$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";

		echo 'Crawling folder '.$path.$line_break;
		$files = $this->get_files($path);
		while($file = array_shift($files))
		{
			$this->get_file($this->strip_server_path($file['path']));
		}

		$this->get_folder($this->strip_server_path($path));

		$folders = $this->get_folders($path);
		while($folder = array_shift($folders))
		{
			$this->crawl($folder['path']);
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

	public static function add_user($user)
	{
		global $GO_CONFIG, $GO_SECURITY;
		
		$fs = new files();

		$userdir = $GO_CONFIG->file_storage_path.'users/'.$user['username'];

		if(!is_dir($userdir))
		{
			mkdir($userdir, $GO_CONFIG->folder_create_mode, true);
		}
			
		$folder = $fs->get_folder('users/'.$user['username']);
		if(empty($folder['acl_read']))
		{
			$up_folder['id']=$folder['id'];
			$up_folder['user_id']=$user['id'];
			$up_folder['acl_read']=$GO_SECURITY->get_new_acl('files', $user['id']);
			$up_folder['acl_write']=$GO_SECURITY->get_new_acl('files', $user['id']);
			$up_folder['visible']='1';
				
			$fs->update_folder($up_folder);
		}
	}

	function user_delete($user)
	{
		global $GO_CONFIG;
		
		$fs = new files();

		$fs->delete($GO_CONFIG->file_storage_path.'users/'.$user['username']);
		$fs->delete($GO_CONFIG->file_storage_path.'users/'.$user['id']);
	}

	function cache_file($path, $index='path')
	{
		global $GO_CONFIG, $GO_LANGUAGE;
		require_once($GO_CONFIG->class_path.'/base/search.class.inc.php');
		$search = new search();

		require($GO_LANGUAGE->get_language_file('files'));

		$fs = new files();

		$path = $this->strip_server_path($path);

		$sql = "SELECT * FROM fs_files WHERE $index=?;";
		$this->query($sql, 's', $path);
		$file = $this->next_record();
			
		$share = $fs->find_share(dirname($path));

		if($file && $share)
		{
			$cache['id']=$file['id'];
			$cache['user_id']=$file['user_id'];
			$cache['name'] = htmlspecialchars(utf8_basename($path), ENT_QUOTES, 'utf-8');
			$cache['link_type']=6;
			$cache['description']=$path;
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
	public static function build_search_index()
	{
		global $GO_CONFIG;
		
		$fs = new files();
		
		$sql = "SELECT path FROM fs_files";
		$fs->query($sql);
		$fs1 = new files();
		while($record = $fs->next_record())
		{
			$fs1->cache_file($GO_CONFIG->file_storage_path.$record['path']);
		}
	}
}
