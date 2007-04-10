<?php
/**
 * @copyright Copyright &copy; Intermesh 2003
 * @version $Revision: 1.71 $ $Date: 2006/12/01 13:58:21 $
 * 
 * @author Markus Schabel <markus.schabel@tgm.ac.at>
 * @author Merijn Schering <mschering@intermesh.nl>

   This file is part of Group-Office.

   Group-Office is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Group-Office is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Group-Office; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 * @package Framework
 * @subpackage Filesystem
 * @category Filesystem
 */

/**
 * Basic functions for authentication mechanisms.
 * 
 * This class provides general functions that are used to handle files on the OS 
 * filesystem. The class takes Group-Office permissions into account.
 * This class als can generate the link id's for the multilinking features.
 * 
 * @package Framework
 * @subpackage Usermanagement
 * @category Authentication
 * 
 * @access protected
 * 
 * @uses db
 */

class filesystem extends db
{
	/**
   * The results of a search operation are stored in this var
   *
   * @var     Array
   * @access  private
   */
	var $search_results = array();

	/**
   * Disable Group-Office permissons when fetching files and folders
   *
   * @var     bool
   * @access  private
   */
	var $disable_go_permissions = false;

	/**
   * Holds an error message if one occured.
   *
   * @var     mixed
   * @access  private
   */
	var $action_result = false;

	/**
   * Constructor. Inititates parent db class and defines if Group-Office permissions should
   * be used. In most cases they should be used but when you just want to fetch some
   * files as an administrator you can disable them.
   *
   * @param bool $disable_go_permissions Disable Group-Office permission system
   *
   * @access public
   * @return void
   */
	function filesystem($disable_go_permissions=false)
	{
		$this->db();
		$this->disable_go_permissions = $disable_go_permissions;
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
		$status['ctime']=get_gmt_time();
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
	
	

	function is_viewable_in_browser($path)
	{
		$extension = get_extension($path);
		$supported = array('gif', 'jpg', 'jpeg', 'png', 'swf','htm','html');

		return in_array($extension, $supported);
	}

	function get_settings($user_id)
	{
		$this->query("SELECT * FROM fs_settings WHERE user_id='$user_id'");
		if ($this->next_record(MYSQL_ASSOC))
		{
			return $this->Record;
		}else
		{
			$this->query("INSERT INTO fs_settings (user_id) VALUES ('$user_id')");
			return $this->get_settings($user_id);
		}
	}

	function update_settings($settings)
	{
		if(!isset($settings['user_id']))
		{
			global $GO_SECURITY;
			$settings['user_id'] = $GO_SECURITY->user_id;
		}
		return $this->update_row('fs_settings', 'user_id', $settings);
	}

	/**
   * Get the unique link_id for a path to link it to any other linkable item.
   *	See links.class.inc for more information about linkable items.
   *
   * @param string $path The full filesystem path of a file or folder
   *
   * @access public
   * @return int Unique link_id 
   */

	function get_link_id($path)
	{
		$sql = "SELECT link_id FROM fs_links WHERE path='$path';";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->f('link_id');
		}else
		{
			global $GO_LINKS;

			$file['path'] = $path;
			$file['link_id'] = $GO_LINKS->get_link_id();

			$this->insert_row('fs_links', $file);

			return $file['link_id'];
		}
	}
	
	function get_file($path)
	{
		$sql = "SELECT * FROM fs_links WHERE path='$path';";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}else
		{
			global $GO_LINKS;

			$file['path'] = $path;
			$file['link_id'] = $GO_LINKS->get_link_id();

			$this->insert_row('fs_links', $file);

			return $this->get_file($path);
		}
	}

	/**
   * Get the linked folders and files path's
   *
   * @param Array $links The link_id's.
   *
   * @access public
   * @return int Number linked path's found.
   */	
	function get_linked_items($links)
	{
		$sql = "SELECT * FROM fs_links WHERE link_id IN (".implode(',',$links).") ORDER BY path ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	/**
   * Get the users quota of the local filesystem
   *
   * @param string $username The OS system username
   *
   * @access public
   * @return Array with keys used and total size on disk.
   */
	function get_quota($username=null)
	{
		global $GO_CONFIG;

		if(!empty($GO_CONFIG->cmd_quota))
		{
			if(!isset($username))
			{
				$username = str_replace(strstr($_SESSION['GO_SESSION']['username'], "@"), "", $_SESSION['GO_SESSION']['username']);
			}
			exec(escapeshellcmd($GO_CONFIG->cmd_sudo.' '.$GO_CONFIG->cmd_quota.' '.$username), $quota_out);
			if(isset($quota_out[0]))
			{
				foreach($quota_out as $line)
				{
					if (ereg(")*none", $line))
					{
						return false;
					}else
					{
						while(strpos($line, "  "))
						{
							$line = ereg_replace("  "," ", $line);
						}
	
						$numbers = explode(" ", $line);
						foreach($numbers as $number)
						{
							if(is_numeric($number))
							{
								if(!isset($arr['used']))
								{
									$arr['used'] = $number*1024;
								}else {
									$arr['total'] = $number*1024;
									return $arr;
								}
							}
						}						
					}
				}
			}
		}
		return false;
	}


	/**
   * Check if a path is a subdirectory of another path.
   *
   * @param string $sub_path The subdirectory path to check
   * @param string $parent_path The parent path
   *
   * @access public
   * @return bool 
   */
	function is_sub_dir($sub_path, $parent_path)
	{
		if(substr($sub_path,-1,1)!='/')
		{
			$sub_path+='/';
		}

		if(strpos($sub_path, $parent_path)===0)
		{
			return true;
		}else
		{
			return false;
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

			if (dirname($path).basename($path) == dirname($home_path).basename($home_path))
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

		if ($user = $GO_USERS->get_user($user_id))
		{
			$home_path = $GO_CONFIG->file_storage_path.'users/'.$user['username'];

			if (strpos($path, $home_path) === 0)
			{
				return true;
			}
		}
		return false;
	}

	/**
   * Get the shares owned by a user.
   *
   * @param int $user_id Group-Office user ID
   *
   * @access public
   * @return int Number of shares found, 
   */
	function get_shares($user_id)
	{
		//ORDER BY PATH important so higher order shares come first
		$sql = "SELECT * FROM fs_shares WHERE user_id='$user_id' AND type='filesystem' ORDER BY path ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	/**
   * Get an array of user ID's that have shared a folder with the current logged in user.
   *
   * @access public
   * @return Array Array of user ID's that share a folder
   */	
	function get_my_shares()
	{
		global $GO_SECURITY;
		$sql = "SELECT DISTINCT fs_shares.user_id FROM fs_shares INNER JOIN acl ".
		"ON (fs_shares.acl_read=acl.acl_id OR fs_shares.acl_write=acl.acl_id) ".
		"LEFT JOIN users_groups ON (acl.group_id=users_groups.group_id) ".
		"WHERE (users_groups.user_id=".$GO_SECURITY->user_id." OR ".
		"acl.user_id=".$GO_SECURITY->user_id.") AND fs_shares.type='filesystem'";
		$this->query($sql);
		$list = array();
		while ( $this->next_record() ) {
			$list[] = $this->f('user_id');
		}
		return $list;
	}

	/**
   * Enables Group-Office permissions for a folder path. 
   *
   * @param int $user_id Group-Office user ID
   * @param string $path Filesystem path of the folder
   * @param string $type Type of share (Deprecated)
   * @param int $acl_read The ACL id to control read permissions
   * @param int $acl_write The ACL id to control write permissions
   *
   * @access public
   * @return bool True on succes
   */	
	function add_share($user_id, $path, $type, $acl_read=0, $acl_write=0)
	{
		global $GO_CONFIG;

		$path = dirname($path).'/'.basename($path);
		$path = addslashes($path);
		global $GO_SECURITY;
		if($acl_read == 0)
		{
			$acl_read = $GO_SECURITY->get_new_acl('read: '.$path);
		}
		if($acl_write == 0)
		{
			$acl_write = $GO_SECURITY->get_new_acl('write: '.$path);
			$GO_SECURITY->add_user_to_acl($user_id, $acl_write);
		}
		if($acl_read && $acl_write)
		{
			$sql = "INSERT INTO fs_shares (user_id, path, type, acl_read, acl_write) ".
			"VALUES ('$user_id', '$path', '$type', '$acl_read', '$acl_write')";

			global $GO_CONFIG;
			if($GO_CONFIG->dav_switch) {
				global $GO_DAV;
				$GO_DAV->add_share($user_id, $path);
			}

			return $this->query($sql);
		}else
		{
			$GO_SECURITY->delete_acl($acl_read);
			$GO_SECURITY->delete_acl($acl_write);
		}

		return false;
	}

	/**
   * Disables Group-Office permissions for a folder path. 
   *
   * @param string $path Filesystem path of the folder
   *
   * @access public
   * @return bool True on succes
   */	
	function delete_share($path)
	{
		global $GO_CONFIG;
		$path = dirname($path).'/'.basename($path);

		if ($share = $this->get_share($path))
		{
			$path = addslashes($path);
			global $GO_SECURITY;
			$GO_SECURITY->delete_acl($share['acl_read']);
			$GO_SECURITY->delete_acl($share['acl_write']);

			$sql = "DELETE FROM fs_shares WHERE path='$path'";

			global $GO_CONFIG;
			if($GO_CONFIG->dav_switch) {
				global $GO_DAV;
				$GO_DAV->delete_share($path);
			}

			return $this->query($sql);
		}

		return false;
	}

	/**
   * Move a share to another location.
   *
   * @param string $old_path Old Filesystem path of the folder
   * @param string $new_path New Filesystem path of the folder
   *
   * @access public
   * @return bool True on succes
   */	
	function update_share($old_path, $new_path)
	{
		global $GO_CONFIG;

		$new_path = addslashes(dirname($new_path).'/'.basename($new_path));
		$old_path = addslashes(dirname($old_path).'/'.basename($old_path));

		$sql = "UPDATE fs_shares SET path='$new_path' WHERE path='$old_path'";

		global $GO_CONFIG;
		if($GO_CONFIG->dav_switch) {
			global $GO_DAV;
			$GO_DAV->update_share($old_path, $new_path);
		}

		return $this->query($sql);
	}

	/**
   * Get the properties of a share in an array.
   *
   * @param string $path Filesystem path of the folder
   *
   * @access public
   * @return Array Share properties
   */	
	function get_share($path)
	{
		global $GO_CONFIG;

		$path = dirname($path).'/'.basename($path);
		$path = addslashes($path);
		$sql = "SELECT * FROM fs_shares WHERE path='$path'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function find_share($path)
	{
		$path = dirname($path).'/'.basename($path);
		if ($share = $this->get_share($path))
		{
			return $share;
		}else
		{
			global $GO_CONFIG;
			$parent = dirname($path);
			if ($parent == '' || $parent == $GO_CONFIG->slash)
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
			global $GO_CONFIG;
			if (strpos($path, $GO_CONFIG->tmpdir) === 0)
			{
				return is_writable($path);
			}
		}
		return false;
	}

	function size($path)
	{
		if (is_dir($path))
		{
			$size = 0;
			$children = $this->get_folders($path);
			while ($child = array_shift($children))
			{
				$size += $this->size($child['path']);
			}

			$files = $this->get_files($path);
			while ($file = array_shift($files))
			{
				$size += $file['size'];
			}
			return $size;
		}else
		{
			return filesize($path);
		}
	}

	function move($source_path, $destination_path)
	{

		$source_path = dirname($source_path).'/'.basename($source_path);
		$destination_path = dirname($destination_path).'/'.basename($destination_path);

		global $GO_CONFIG, $GO_MODULES;
		//do not move into own path
		$source_dir_count = count(explode('/',$source_path));
		$destination_dir_count = count(explode('/',$destination_path));

		if ((strpos($destination_path, $source_path) === 0) && ($destination_dir_count > $source_dir_count))
		{
			return false;
		}elseif($source_path == $destination_path)
		{
			return true;
		}else
		{

			if (is_dir($source_path))
			{
				if (!file_exists($destination_path))
				{
					if (!mkdir($destination_path, $GO_CONFIG->create_mode))
					{
						return false;
					}else
					{
						if ($this->get_share($source_path))
						{
							$this->update_share($source_path, $destination_path);
						}
						
						$link['link_id'] = $this->get_link_id_by_path(addslashes($source_path));
						if($link['link_id'])
						{
							$link['path'] = addslashes($destination_path);

							$this->update_row('fs_links', 'link_id', $link);
						}
					}
				}
				$files = $this->get_files($source_path, true);
				while ($file = array_shift($files))
				{
					if(!$this->move($file['path'], $destination_path.'/'.$file['name']))
					{
						return false;
					}
				}
				$children = $this->get_folders($source_path, true);
				while ($child = array_shift($children))
				{
					if (!$this->move($child['path'], $destination_path.'/'.$child['name']))
					{
						return false;
					}else
					{
						if ($this->get_share($child['path']))
						{
							$this->update_share($child['path'], $destination_path.'/'.$child['name']);
						}

						$link['link_id'] = $this->get_link_id_by_path(addslashes($source_path));
						if($link['link_id'])
						{
							$link['path'] = addslashes($destination_path);
							$this->update_row('fs_links', 'link_id', $link);
						}
					}
				}

				//Here we have a little problem with WebDAV... Have to DELETE ALL files
				//and there are also hidden files in the folders...
				if($GO_CONFIG->dav_switch) {
					$cmd = "rm -rf $source_path";
					`$cmd`;
					return true;
				} else {
					return rmdir($source_path);
				}
			}else
			{
				//rename fails when moving accross partitions
				if($link['link_id'] = $this->get_link_id_by_path(addslashes($source_path)))
				{
					$link['path'] = addslashes($destination_path);
					$this->update_row('fs_links', 'link_id', $link);
				}

				if(isset($_SESSION['GO_SESSION']['username']) && strstr($source_path, $GO_CONFIG->file_storage_path.'users/'.$_SESSION['GO_SESSION']['username'].'/') &&
				strstr($destination_path, $GO_CONFIG->file_storage_path.'users/'.$_SESSION['GO_SESSION']['username'].'/')
				)
				{
					return rename($source_path, $destination_path);
				}else
				{
					if ($this->copy($source_path, $destination_path))
					{
						return unlink($source_path);
					}else
					{
						return false;
					}
				}
			}
		}
	}

	function copy($source_path, $destination_path)
	{
		$source_path = dirname($source_path).'/'.basename($source_path);
		$destination_path = dirname($destination_path).'/'.basename($destination_path);

		global $GO_CONFIG;
		//do not copy into own path
		if (strpos($destination_path, $source_path) === 0)
		{
			// TODO add translation
			$this->action_result = "source and destination path are the same";
			return false;
		}else
		{
			if (is_dir($source_path))
			{
				if (!file_exists($destination_path))
				{
					if (!mkdir($destination_path, $GO_CONFIG->create_mode))
					{
						// TODO add translation
						$this->action_result = "unable to create destination path";
						return false;
					}
				}
				$files = $this->get_files($source_path);
				while ($file = array_shift($files))
				{
					if(!$this->copy($file['path'], $destination_path.'/'.$file['name']))
					{
						// this->action_result is set by the call itself.
						return false;
					}
				}
				$children = $this->get_folders($source_path);
				while ($child = array_shift($children))
				{
					if (!$this->copy($child['path'], $destination_path.'/'.$child['name']))
					{
						// this->action_result is set by the call itself.
						return false;
					}
				}
				$this->action_result = true;
				return true;
			}else
			{
				//$this->get_usedspace($destination_path) + filesize( $source_path );
				// No directory. Check free space in destination_path.
				if ( $GO_CONFIG->user_quota != 0 && $usedspace = $this->get_usedspace($destination_path)) {

					if ( ( $usedspace + filesize( $source_path ) ) >= $GO_CONFIG->user_quota*1024 ) {
						// TODO add translation
						$this->action_result = "not enough space (over quota)";
						return false;
					}
				}

				return copy($source_path, $destination_path);
			}
		}
	}

	function get_usedspace($path)
	{
		global $GO_CONFIG;
		if ( strstr( $path, $GO_CONFIG->file_storage_path.'users/' ) )
		{
			$dest = substr( $path, strlen( $GO_CONFIG->file_storage_path.'users/' ) );
			$dest = substr( $dest, 0, strpos( $dest, "/" ) );
			//echo "du ".$GO_CONFIG->file_storage_path.'users/'.$dest." -s";
			exec("du ".$GO_CONFIG->file_storage_path.'users/'.$dest." -s", $retval );

			list($usedspace) = sscanf( $retval[0], "%d");
			return $usedspace*1024;

		}
		return false;
	}

	function get_link_id_by_path($path)
	{
		$path = dirname($path).'/'.basename($path);

		$sql = "SELECT link_id FROM fs_links WHERE path='".$path."'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->f('link_id');
		}
		return false;
	}

	function delete($path)
	{
		$path = dirname($path).'/'.basename($path);

		global $GO_SECURITY;

		if($link_id = $this->get_link_id_by_path(addslashes($path)))
		{
			$this->query("DELETE FROM fs_links WHERE link_id='$link_id'");
			global $GO_LINKS;

			$GO_LINKS->delete_link($link_id);
		}

		if (is_dir($path))
		{
			$children = $this->get_folders($path);
			while ($child = array_shift($children))
			{
				if (!$this->delete($child['path']))
				{
					return false;
				}
			}

			$files = $this->get_files($path);
			while ($file = array_shift($files))
			{
				if (!$this->delete($file['path']))
				{
					return false;
				}
			}
			if ($this->disable_go_permissions ||
			$this->has_write_permission($GO_SECURITY->user_id, $path))
			{
				if (!$this->disable_go_permissions &&  $this->get_share($path))
				{
					$this->delete_share($path);
				}

				global $GO_CONFIG;
				if($GO_CONFIG->dav_switch) {
					global $GO_DAV;
					if($GO_DAV->is_share($path))
					$GO_DAV->delete_share($path);
					$cmd = "rm -rf $path/.*";
					`$cmd`;
				}

				return @rmdir($path);
			}else
			{
				return false;
			}
		}else
		{
			if ($this->disable_go_permissions ||
			$this->has_write_permission($GO_SECURITY->user_id, $path))
			{
				return @unlink($path);
			}else
			{
				return false;
			}
		}
	}

	function get_parent_path($path)
	{
		$path = dirname($path).'/'.basename($path);

		$last_folder_pos = strrpos($path, '/');
		if (is_integer($last_folder_pos))
		{
			if ($last_folder_pos === 0)
			{
				return '/';
			}else
			{
				return substr($path, 0, $last_folder_pos);
			}
		}else
		{
			return false;
		}
	}

	//faster then get_folders_sorted
	function get_folders($path, $gethidden=false)
	{
		$path = dirname($path).'/'.basename($path).'/';

		global $GO_CONFIG;
		$slash = stristr(PHP_OS, 'Windows') ? '\\' : '/';
		if (substr($path, -1) != $slash) $path .= $slash;

		$folders = array();
		if($dir = opendir($path))
		{
			while($item=readdir($dir))
			{
				$folder_path = $path.$item;
				if (is_dir($folder_path) && $item != "." && $item != ".." &&
				$item != $GO_CONFIG->name_of_sharedir &&
				($gethidden || !(strpos($item,".") === 0) ))
				{
					$folder['path'] = $folder_path;
					$folder['name'] = basename($folder_path);
					$folder['mtime'] = filemtime($folder_path);
					$folder['size'] = filesize($folder_path);
					$folder['type'] = 'folder';
					$folders[] = $folder;
				}
			}
			closedir($dir);
		}
		return $folders;
	}

	#returns all subfolders of a folder sorted based on the result of a function
	#passed that is performed on the pathname. (For example filesize();)
	function get_folders_sorted($path,$sort_field='basename',$sort_direction='ASC', $gethidden=false)
	{
		$path = dirname($path).'/'.basename($path).'/';

		global $GO_CONFIG;
		$folders = array();
		$slash = stristr(PHP_OS, 'Windows') ? '\\' : '/';
		if (substr($path, -1) != $slash) $path .= $slash;
		if(strstr($path, $GO_CONFIG->root_path))
		{
			$url = str_replace($GO_CONFIG->root_path, $GO_CONFIG->host, $path);
			if ($slash == '\\')
			{
				$url = str_replace('\\','/',$url);
			}
		}
		$sort_field = function_exists($sort_field) ? $sort_field : 'basename';
		if (is_dir($path))
		{
			$sorted_list = array();

			if(@$dir = opendir($path))
			{
				while($item=readdir($dir))
				{
					$folder_path = $path.$item;

					if (is_dir($folder_path) && $item != "." && $item != ".." &&
					$item != $GO_CONFIG->name_of_sharedir &&
					($gethidden || !(strpos($item,".")===0) ))
					{
						$key_id = 0;
						$first_key = strtolower($sort_field($folder_path));
						$key = $first_key;
						while (array_key_exists($key, $sorted_list))
						{
							$key = $first_key.'_'.$key_id;
							$key_id++;
						}
						$sorted_list[$key] = $folder_path;
					}
				}
				closedir($dir);

				if ($sort_direction == 'ASC')
				{
					ksort($sorted_list);
				}else
				{
					krsort($sorted_list);
				}

				while ($item=array_shift($sorted_list))
				{
					$folder = array();
					$folder['path'] = $item;
					$folder['name'] = basename($item);
					$folder['mtime'] = filemtime($item);
					$folder['size'] = filesize($item);
					$folder['type'] = 'folder';
					if(isset($url))
					{
						$folder['url'] = $url.$folder['name'];
					}
					$folders[] = $folder;
				}
			}
		}
		return $folders;
	}

	//faster then get_files_sorted
	function get_files($path, $move=false, $gethidden=false)
	{
		$path = dirname($path).'/'.basename($path).'/';

		global $GO_CONFIG;
		$slash = stristr(PHP_OS, 'Windows') ? '\\' : '/';
		if (substr($path, -1) != $slash) $path .= $slash;
		$files = array();
		if($dir = @opendir($path))
		{
			while($item=readdir($dir))
			{
				$file_path = $path.$item;
				if (!is_dir($file_path) && $move) {
					$file['path'] = $file_path;
					$file['name'] = basename($file_path);
					$file['size'] = filesize($file_path);
					$file['mtime'] = filemtime($file_path);
					$file['type'] = mime_content_type($file_path);

					$files[] = $file;
				}
				if (!is_dir($file_path) && !$move &&
				$item != $GO_CONFIG->name_of_sharedir &&
				($gethidden || !(strpos($item,".") === 0)))
				{
					$file['path'] = $file_path;
					$file['name'] = basename($file_path);
					$file['size'] = filesize($file_path);
					$file['mtime'] = filemtime($file_path);
					$file['type'] = mime_content_type($file_path);

					$files[] = $file;
				}
			}
			closedir($dir);
		}
		return $files;
	}

	#returns all subfolders of a folder sorted based on the result of a function
	#passed that is performed on the pathname. (For example filesize();)
	function get_files_sorted($path,$sort_field='basename',$sort_direction='ASC', $gethidden=false)
	{
		$path = dirname($path).'/'.basename($path).'/';

		global $GO_CONFIG;
		$files = array();

		$slash = stristr(PHP_OS, 'Windows') ? '\\' : '/';
		if (substr($path, -1) != $slash) $path .= $slash;

		if(strstr($path, $GO_CONFIG->root_path))
		{
			$url = str_replace($GO_CONFIG->root_path, $GO_CONFIG->host, $path);
			if ($slash == '\\')
			{
				$url = str_replace('\\','/',$url);
			}
		}
		$sort_field = function_exists($sort_field) ? $sort_field : 'basename';
		if (is_dir($path))
		{
			$sorted_list = array();

			if($dir = @opendir($path))
			{
				while($item=readdir($dir))
				{
					$file = $path.$item;
					if (!is_dir($file) &&
					$item != $GO_CONFIG->name_of_sharedir &&
					($gethidden || !(strpos($item,".") === 0)))
					{
						$key_id = 0;
						$first_key = strtolower($sort_field($file));
						$key = $first_key;
						while (array_key_exists($key, $sorted_list))
						{
							$key = $first_key.'_'.$key_id;
							$key_id++;
						}
						$sorted_list[$key] = $file;
					}
				}
				closedir($dir);

				if ($sort_direction == 'ASC')
				{
					ksort($sorted_list);
				}else
				{
					krsort($sorted_list);
				}

				while ($item=array_shift($sorted_list))
				{
					$file = array();
					$file['path'] = $item;
					$file['name'] = basename($item);
					$file['mtime'] = filemtime($item);
					$file['size'] = filesize($item);
					$file['type'] = mime_content_type($item);

					if(isset($url))
					{
						$file['url'] = $url.$file['name'];
					}
					$files[] = $file;
				}
			}

		}
		return $files;
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
	function delete_user($user_id)
	{
		$fs = new filesystem();
		$this->get_shares($user_id);
		while($this->next_record())
		{
			$fs->delete_share($this->f('path'));
		}
		$this->query("DELETE FROM fs_settings WHERE user_id='$user_id'");
	}
}
