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

/** 
 * This class contains functions for filesytem operations
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package go.utils
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
	var $disable_go_permissions = true;

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
	function filesystem()
	{
		global $GO_MODULES;
		
		$this->db();

	}
	


	
	
	
	 /**
	 * Creates a directory recursively
	 *
	 * @param	string $path
	 * @access public
	 * @return bool True on success
	 */
	
	function mkdir_recursive($path) {
	  global $GO_CONFIG;
	
	  $dirs_to_create = array ();
	  while (!is_writable($path)) {
	    $dirs_to_create[] = $path;
	    if(dirname($path) == $path)
	    {
	     	exit('Failed to create folder(s) '.var_export($dirs_to_create, true));
	    }
	    $path = dirname($path);
	  }
	  $old_umask = umask(000);	
	  while ($dir = array_pop($dirs_to_create)) {
	    if (@ !mkdir($dir, $GO_CONFIG->create_mode)) {
	      die("Failed creating required directory ".$dir);
	    }
	  }
	  umask($old_umask);
	  return true;
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
		if(substr($parent_path,-1,1)!='/')
		{
			$parent_path.='/';
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

		$source_path = dirname($source_path).'/'.utf8_basename($source_path);
		$destination_path = dirname($destination_path).'/'.utf8_basename($destination_path);
		
		

		//throw new Exception($source_path.' -> '.$destination_path);

		global $GO_CONFIG, $GO_MODULES;
		//do not move into own path
	//	$source_dir_count = count(explode('/',$source_path));
		//$destination_dir_count = count(explode('/',$destination_path));

		//if ((strpos($destination_path, $source_path) === 0) && ($destination_dir_count > $source_dir_count))
		if (strpos($destination_path.'/', $source_path.'/')===0)
		{
			throw new Exception('The destination is located inside the source directory');
		}elseif($source_path == $destination_path)
		{
			throw new Exception('The source and destionation are the same');
		}else
		{

			if (is_dir($source_path))
			{
				if (!file_exists($destination_path))
				{
					if (!mkdir($destination_path, $GO_CONFIG->create_mode))
					{
						throw new Exception('Failed to create direcory');
					}
				}
				
				if(!$this->disable_go_permissions)
				{				
					$folder = $this->get_folder(addslashes($source_path));
					$up_folder['id']=$folder['id'];
					$up_folder['path']=addslashes($destination_path);
					$this->update_folder($up_folder);
									
					$this->move_notifications(addslashes($source_path), addslashes($destination_path));
				}			
				
				
				$files = $this->get_files($source_path, true);
				while ($file = array_shift($files))
				{
					$this->move($file['path'], $destination_path.'/'.$file['name']);				
				}
				$children = $this->get_folders($source_path, true);
				while ($child = array_shift($children))
				{
					$this->move($child['path'], $destination_path.'/'.$child['name']);
				}
				
				rmdir($source_path);
			}else
			{
				
				$file = $this->get_file(addslashes($source_path));
				$up_file['id']=$file['id'];
				$up_file['path']=addslashes($destination_path);
				$this->update_file($up_file);
				
				//rename fails when moving accross partitions
				if(isset($_SESSION['GO_SESSION']['username']) && strstr($source_path, $GO_CONFIG->file_storage_path.'users/'.$_SESSION['GO_SESSION']['username'].'/') &&
				strstr($destination_path, $GO_CONFIG->file_storage_path.'users/'.$_SESSION['GO_SESSION']['username'].'/')
				)
				{
					return rename($source_path, $destination_path);
				}else
				{
					$this->copy($source_path, $destination_path);
					unlink($source_path);				
				}
			}
		}
	}

	function copy($source_path, $destination_path)
	{
		$source_path = dirname($source_path).'/'.utf8_basename($source_path);
		$destination_path = dirname($destination_path).'/'.utf8_basename($destination_path);

		//echo $source_path.' -> '.$destination_path;
		
		global $GO_CONFIG;
		
		if($source_path == $destination_path)
		{
			return true;
		}elseif (strpos($destination_path, $source_path) === 0)
		{
			throw new Exception("Destination path is inside the source path");
		}else
		{
			if (is_dir($source_path))
			{
				if (!file_exists($destination_path))
				{
					if (!mkdir($destination_path, $GO_CONFIG->create_mode))
					{
						throw new Exception('Failed to create direcory');
					}
				}
				$files = $this->get_files($source_path);
				while ($file = array_shift($files))
				{
					$this->copy($file['path'], $destination_path.'/'.$file['name']);
				}
				$children = $this->get_folders($source_path);
				while ($child = array_shift($children))
				{
					$this->copy($child['path'], $destination_path.'/'.$child['name']);
				}
				return true;
			}else
			{
				//go_log(LOG_DEBUG, $source_path.' -> '.$destination_path);
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




	function delete($path)
	{
		$path = dirname($path).'/'.utf8_basename($path);

		global $GO_SECURITY, $GO_MODULES;

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
				if (!$this->disable_go_permissions)
				{
					$this->delete_folder(addslashes($path));
				}

				global $GO_CONFIG;

				return rmdir($path);
			}else
			{
				return false;
			}
		}else
		{

			if (!$this->disable_go_permissions)
			{
				$file = $this->get_file(addslashes($path));
				$this->delete_file(addslashes($path));
				
				global $GO_CONFIG;
				
				require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
				$search = new search();
				$search->delete_search_result($file['id'], 6);
			}
				
			if ($this->disable_go_permissions ||
			$this->has_write_permission($GO_SECURITY->user_id, $path))
			{
				return unlink($path);
			}else
			{
				return false;
			}
		}
	}

	function get_parent_path($path)
	{
		$path = dirname($path).'/'.utf8_basename($path);

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
		$path = dirname($path).'/'.utf8_basename($path).'/';

		global $GO_CONFIG;

		$folders = array();
		if($dir = opendir($path))
		{
			while($item=readdir($dir))
			{
				$folder_path = $path.$item;
				if (is_dir($folder_path) && $item != "." && $item != ".." &&
				($gethidden || !(strpos($item,".") === 0) ))
				{
					$folder['path'] = $folder_path;
					$folder['name'] = utf8_basename($folder_path);
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
	function get_folders_sorted($path,$sort_field='utf8_basename',$sort_direction='ASC', $gethidden=false)
	{
		$path = dirname($path).'/'.utf8_basename($path).'/';

		global $GO_CONFIG;
		
		$folders=array();

		if(strstr($path, $GO_CONFIG->root_path))
		{
			$url = str_replace($GO_CONFIG->root_path, $GO_CONFIG->host, $path);
		}
		$sort_field = function_exists($sort_field) ? $sort_field : 'utf8_basename';
		if (is_dir($path))
		{
			$sorted_list = array();

			if(@$dir = opendir($path))
			{
				while($item=readdir($dir))
				{
					$folder_path = $path.$item;

					if (is_dir($folder_path) && $item != "." && $item != ".." &&
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
					$folder['name'] = utf8_basename($item);
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
		$path = dirname($path).'/'.utf8_basename($path).'/';

		global $GO_CONFIG;

		$files = array();
		if($dir = @opendir($path))
		{
			while($item=readdir($dir))
			{
				$file_path = $path.$item;
				if (!is_dir($file_path) && $move) {
					$file['path'] = $file_path;
					$file['name'] = utf8_basename($file_path);
					$file['size'] = filesize($file_path);
					$file['mtime'] = filemtime($file_path);
					$file['type'] = mime_content_type($file_path);

					$files[] = $file;
				}
				if (!is_dir($file_path) && !$move &&
				($gethidden || !(strpos($item,".") === 0)))
				{
					$file['path'] = $file_path;
					$file['name'] = utf8_basename($file_path);
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
	function get_files_sorted($path,$sort_field='utf8_basename',$sort_direction='ASC', $gethidden=false)
	{
		$path = dirname($path).'/'.utf8_basename($path).'/';

		global $GO_CONFIG;
		$files = array();


		if(strstr($path, $GO_CONFIG->root_path))
		{
			$url = str_replace($GO_CONFIG->root_path, $GO_CONFIG->host, $path);
		}
		$sort_field = function_exists($sort_field) ? $sort_field : 'utf8_basename';
		if (is_dir($path))
		{
			$sorted_list = array();

			if($dir = @opendir($path))
			{
				while($item=readdir($dir))
				{
					$file = $path.$item;
					if (!is_dir($file) &&
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
					$file['name'] = utf8_basename($item);
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

	
}
