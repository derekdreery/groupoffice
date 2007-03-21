<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

define('NORMAL_DISPLAY', 0);
define('MULTIPAGE_DISPLAY', 1);

class cms extends db
{
	function cms()
	{
		$this->db();
	}
	
	function get_styles($style)
	{
		$styles = array();
		$style_name = '';
		$in_style = false;

		$style = str_replace("\r", '', $style);
		$style = str_replace("\n", '', $style);
		$style = str_replace(' ', '', $style);
		for ($i=0; $i<strlen($style); $i++)
		{
			$char = $style[$i];

			if ($char == '{' || $char == ',')
			{
				$in_style = false;
				if (trim($style_name) != '')
				{
					$styles[] = $style_name;
				}
				$style_name = '';
			}elseif($char == '.')
			{
				if ($style_name != '')
				{
					$style_name = '';
					$in_style = false;
				}else
				{
					$in_style = true;
				}
			}elseif($char == ':')
			{
				$style_name = '';
				$in_style = false;
			}elseif($char == '}')
			{
				$in_style = false;
				$style_name = '';
			}else
			{
				if ($in_style)
				{
					$style_name .= $char;
				}
			}
		}

		return $styles;
	}
	
	function get_plugins()
	{
		global $GO_MODULES, $GO_CONFIG;
		
		$plugins = array();
		
		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem();
		
		$files = $fs->get_files($GO_MODULES->modules['cms']['path'].'plugins');
		foreach($files as $file)
		{
			$plugins[] = str_replace('.class.inc', '', $file['name']);	
		}
		return $plugins;		
	}

	function get_settings($user_id)
	{
		$this->query("SELECT * FROM cms_settings WHERE user_id='$user_id'");
		if ($this->next_record())
		{
			return $this->Record;
		}else
		{
			$this->query("INSERT INTO cms_settings ".
			"(user_id, sort_field, sort_order) VALUES ".
			"('$user_id', 'cms_files.priority', 'ASC')");
			return $this->get_settings($user_id);
		}
	}

	function set_sorting($user_id, $sort_field, $sort_order)
	{
		$sql = "UPDATE cms_settings SET sort_field='$sort_field' , ".
		"sort_order='$sort_order' WHERE user_id='$user_id'";
		return $this->query($sql);
	}

	function resolve_url($path, $root_folder_id)
	{
		$path = explode('/', $path);

		$last_folder_id = $root_folder_id;
		$count = count($path);
		for($i=0;$i<$count;$i++)
		{
			if ($path[$i] != '')
			{
				if(!$new_folder_id = $this->folder_exists($last_folder_id, $path[$i]))
				{
					$file_id = $this->file_exists($last_folder_id, $path[$i]);
					if(!$file_id = $this->file_exists($last_folder_id, $path[$i]))
					{
						echo $i.': '.$path[$i].' last folder id: '.$last_folder_id.'<br />';
						return false;
					}
				}else
				{
					$last_folder_id = $new_folder_id;
				}
			}
		}
		$path['folder_id'] = $last_folder_id;
		if (isset($file_id))
		{
			$path['file_id'] = $file_id;
		}
		return $path;

	}

	function move_file($file_id, $new_folder_id)
	{
		//if the name exists add (1) behind it.
		if($file = $this->get_file($file_id))
		{
			$name = $file['name'];
			$x=0;
			while ($this->file_exists($new_folder_id, $name))
			{
				$x++;
				$name = strip_extension($file['name']).' ('.$x.').'.get_extension($file['name']);
			}
			$priority = $this->get_next_priority($new_folder_id);
			return $this->query("UPDATE cms_files SET folder_id='$new_folder_id', name='$name', priority='$priority' WHERE id='$file_id'");
		}
		return false;
	}

	function move_folder($folder_id, $new_parent_id)
	{
		if ($folder = $this->get_folder($folder_id))
		{
			if ($folder['parent_id'] != $new_parent_id)
			{
				//don't move folders into thier own path
				if (!$this->is_in_path($folder_id, $new_parent_id))
				{
					//if the name exists add (1) behind it.
					$name = $folder['name'];
					$x=0;
					while ($this->folder_exists($new_parent_id, $name))
					{
						$x++;
						$name = $folder['name'].' ('.$x.')';
					}
					$priority = $this->get_next_priority($new_parent_id);
					return $this->query("UPDATE cms_folders SET parent_id='$new_parent_id', name='$name', priority='$priority' WHERE id='$folder_id'");
				}
			}
		}
		return false;
	}

	function copy_file($file_id, $new_folder_id)
	{
		//if the name exists add (1) behind it.
		if($file = $this->get_file($file_id))
		{
			$name = $file['name'];
			$x=0;
			while ($this->file_exists($new_folder_id, $name))
			{
				$x++;
				$name = strip_extension($file['name']).' ('.$x.').'.get_extension($file['name']);
			}
			return $this->add_file($new_folder_id, addslashes($name), addslashes($file['content']), addslashes($file['title']), addslashes($file['description']), addslashes($file['keywords']), $file['template_item_id'], $file['auto_meta']);
			

		}
		return false;
	}

	function copy_folder($folder_id, $new_parent_id)
	{
		if ($folder = $this->get_folder($folder_id))
		{
			//don't move folders into thier own path
			if (!$this->is_in_path($folder_id, $new_parent_id))
			{
				//if the name exists add (1) behind it.
				$name = $folder['name'];
				$x=0;
				while ($this->folder_exists($new_parent_id, $name))
				{
					$x++;
					$name = $folder['name'].' ('.$x.')';
				}
				if($new_folder_id = $this->add_folder($new_parent_id, $name))
				{
					$cms = new cms();
					$this->get_files($folder_id);
					while($this->next_record())
					{
						if(!$cms->copy_file($this->f('id'), $new_folder_id))
						{
							return false;
						}
					}

					$this->get_folders($folder_id);
					while($this->next_record())
					{
						if(!$this->copy_folder($this->f('id'),$new_folder_id))
						{
							return false;
						}
					}
					return true;
				}
			}
		}
		return false;
	}

	/*
	Check if a folder is in the path of another folder.
	This is used to check if we can move a folder into another.
	*/
	function is_in_path($check_folder_id, $target_folder_id)
	{
		if($target_folder_id == 0)
		{
			return false;
		}elseif ($target_folder_id == $check_folder_id)
		{
			return true;
		}else
		{
			$folder = $this->get_folder($target_folder_id);
			return $this->is_in_path($check_folder_id, $folder['parent_id']);
		}
	}

	function file_exists($folder_id, $filename)
	{
		$this->query("SELECT id FROM cms_files WHERE folder_id='$folder_id' AND name='$filename'");
		if ($this->next_record())
		{
			return $this->f('id');
		}
		return false;
	}

	//builds the full path to this directory with links
	function get_path($folder_id)
	{
		$path = '';

		while($folder = $this->get_folder($folder_id))
		{
			$path = '/'.$folder['name'].$path;
			$folder_id = $folder['parent_id'];
		}
		return $path;
	}





	//functions for personal sites
	function add_site($user_id,  $domain, $webmaster, $acl_write, $template_id,$language)
	{
		$template_item_id=0;
		$this->get_template_items($template_id, true);
		if($this->next_record())
		{
			$template_item_id=$this->f('id');
		}
		//create a root folder for the new site
		if ($root_folder_id = $this->add_folder(0, $domain, false, $template_item_id))
		{
			$domain = $this->prepare_domain($domain);
			//create the site
			$site_id = $this->nextid('cms_sites');
			if ($site_id > 0)
			{
				if($this->query("INSERT INTO cms_sites (id, user_id, domain, webmaster,   acl_write, template_id,root_folder_id,language)  ".
				"VALUES ('$site_id', '$user_id', '$domain', '$webmaster', ".
				" '$acl_write', $template_id, $root_folder_id, $language)"))
				{
					return $site_id;
				}
			}
		}
		return false;
	}

	function delete_site($site_id)
	{
		$cms = new cms();

		if($site = $this->get_site($site_id))
		{
			$this->delete_folder($site['root_folder_id']);

			if($this->query("DELETE FROM cms_sites WHERE id='$site_id'"))
			{
				global $GO_SECURITY;
				$GO_SECURITY->delete_acl($site['acl_write']);
				return true;
			}
		}
		return false;
	}

	function update_site($site_id,$domain, $webmaster, $template_id, $start_file_id,$language)
	{
		$domain = $this->prepare_domain($domain);
		return $this->query("UPDATE cms_sites SET domain='$domain', ".
			"webmaster='$webmaster', ".
			"template_id=$template_id, ".
			"start_file_id='$start_file_id', ".
			"language='$language' ".
			"WHERE id='$site_id'");
	}

	function set_publishing($site_id, $publish_style, $publish_path)
	{
		return $this->query("UPDATE cms_sites SET publish_style='$publish_style', publish_path='$publish_path' WHERE id='$site_id'");
	}

	function get_site_by_publish_path($path)
	{
		$this->query("SELECT * FROM cms_sites WHERE publish_path='$path'");
		if ($this->next_record())
		{
			return $this->Record;
		}else
		{
			return false;
		}
	}

	function get_site($site_id)
	{
		$this->query("SELECT * FROM cms_sites WHERE id='$site_id'");
		if ($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function prepare_domain($domain)
	{
		$domain = preg_replace("/http(s?):\/\//i", '', $domain);
		if (substr($domain, -1)=='/')
		{
			$domain = substr($domain,0, -1);
		}
		return $domain;
	}

	function get_site_by_domain($domain, $recurse=false)
	{
		$domain = $this->prepare_domain($domain);
		$this->query("SELECT * FROM cms_sites WHERE domain='$domain'");
		if ($this->next_record())
		{
			return $this->Record;
		}elseif($recurse)
		{
			while($pos = strpos($domain,'.'))
			{
				$domain = substr($domain, $pos+1);
				return $this->get_site_by_domain($domain, true);
			}
		}
		return false;
	}

	function get_sites()
	{
		$this->query("SELECT * FROM cms_sites");
		return $this->num_rows();
	}

	function get_authorized_sites($user_id)
	{
		/*$sql = "SELECT DISTINCT cms_sites.* FROM cms_sites, acl, users_groups WHERE ".
		"cms_sites.acl_write = acl.acl_id".
		" AND (( acl.group_id = users_groups.group_id AND users_groups.user_id = ".$user_id." AND acl.user_id = 0 ) OR (".
		"acl.group_id = 0 AND acl.user_id = ".$user_id." ) ) ORDER BY cms_sites.domain ASC";*/
		
		$sql = "SELECT DISTINCT cms_sites. *".
				"FROM cms_sites ".
				"INNER JOIN acl ON cms_sites.acl_write = acl.acl_id ".
				"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id ".
				"WHERE acl.user_id=$user_id ".
				"OR users_groups.user_id=$user_id".
				"	ORDER BY cms_sites.domain ASC";
				
				
		$this->query($sql);
		return $this->num_rows();
	}

	function get_subscribed_sites($user_id)
	{
		$this->query("SELECT cms_sites.* FROM cms_sites LEFT JOIN cms_subscribed ON (cms_subscribed.site_id=cms_sites.id) WHERE cms_subscribed.user_id='$user_id' ORDER BY cms_sites.domain ASC");
		return $this->num_rows();
	}


	function subscribe_site($user_id, $site_id)
	{
		return $this->query("INSERT INTO cms_subscribed (user_id, site_id) VALUES ('$user_id', '$site_id')");
	}

	function unsubscribe_site($user_id, $site_id)
	{
		return $this->query("DELETE FROM cms_subscribed WHERE user_id='$user_id' AND site_id='$site_id'");
	}

	function is_subscribed($user_id, $site_id)
	{
		$this->query("SELECT * FROM cms_subscribed WHERE user_id='$user_id' AND site_id='$site_id'");
		return $this->next_record();
	}


	//functions for files and folders of sites
	function get_files($folder_id, $sort = "priority", $direction = "ASC")
	{
		$sql = "SELECT * FROM cms_files WHERE folder_id='$folder_id' ORDER BY $sort $direction";
		$this->query($sql);
		return $this->num_rows();
	}
	
	function get_authorized_files($folder_id, $user_id, $hot_items=false)
	{
		global $GO_SECURITY;
		
		$files=array();
		if($hot_items)
		{
			$this->get_hot_items($folder_id);
		}else {
			$this->get_files($folder_id);
		}
		while($this->next_record())
		{
			if($this->f('acl')==0 || $GO_SECURITY->has_permission($user_id, $this->f('acl')))
			{
				$files[]=$this->Record;			
			}		
		}
		return $files;
	}

	function get_hot_items($folder_id)
	{
		$sql = "SELECT * FROM cms_files WHERE folder_id='$folder_id' AND hot_item='1' ORDER BY priority ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_visible_folders($folder_id)
	{
		$this->query("SELECT * FROM cms_folders WHERE parent_id='$folder_id' AND disabled='0' ORDER BY priority ASC");
		return $this->num_rows();
	}

	function get_folders($folder_id, $sort = "priority", $direction = "ASC")
	{
		$this->query("SELECT * FROM cms_folders WHERE parent_id='$folder_id' ORDER BY '$sort' $direction");
		return $this->num_rows();
	}
	
	function get_authorized_folders($folder_id, $user_id, $only_visible=false)
	{
		global $GO_SECURITY;
		$folders=array();
		if($only_visible)
		{
			$this->get_visible_folders($folder_id);
		}else
		{
			$this->get_folders($folder_id);
		}
		while($this->next_record())
		{
			if($this->f('acl')==0 || $GO_SECURITY->has_permission($user_id, $this->f('acl')))
			{
				$folders[]=$this->Record;			
			}		
		}
		return $folders;
	}
	
	function get_authorized_items($folder_id, $user_id, $only_visible=false)
	{
		$items = array();
		$folders = $this->get_authorized_folders($folder_id, $user_id, $only_visible);
		foreach($folders as $folder)
		{
			$items[$folder['priority']] = $folder;
		}
		$files = $this->get_authorized_files($folder_id, $user_id);
		foreach($files as $file)
		{
			$items[$file['priority']] = $file;
		}
		ksort($items);
		return $items;
	}

	function get_items($folder_id, $only_visible=false)
	{
		$items = array();
		if($only_visible)
		{
			$this->get_visible_folders($folder_id);
		}else
		{
			$this->get_folders($folder_id);
		}
		while($this->next_record())
		{
			$items[$this->f('priority')] = $this->Record;
		}
		$this->get_files($folder_id);
		while($this->next_record())
		{
			$items[$this->f('priority')] = $this->Record;
		}
		ksort($items);
		return $items;
	}

	function add_folder($parent_id, $name, $disabled=false, $template_item_id=0)
	{
		$disabled = $disabled ? '1' : '0';
		$folder_id = $this->nextid('cms_folders');
		if ($folder_id > 0)
		{
			$priority = $this->get_next_priority($parent_id);
			$mtime = $ctime = get_gmt_time();
			if($this->query("INSERT INTO cms_folders (id, parent_id, name, ctime, mtime, priority, ".
				"disabled, template_item_id) VALUES ('$folder_id', '$parent_id', '$name', '$ctime', ".
				"'$mtime', '$priority', '$disabled', '$template_item_id')"))
			{
				return $folder_id;
			}
		}
		return false;
	}

	function get_folder($folder_id)
	{
		$this->query("SELECT * FROM cms_folders WHERE id='$folder_id';");
		if ($this->next_record())
		{
			return $this->Record;
		}
		return false;

	}

	function update_folder($folder_id, $name, $disabled, $multipage=false, $template_item_id=0, $acl=0)
	{
		$sql = "UPDATE cms_folders SET name='$name', mtime='".get_gmt_time().
		"', disabled='$disabled', ".
		"multipage='$multipage', template_item_id='$template_item_id', acl='$acl' WHERE id='$folder_id'";
		return $this->query($sql);
	}

	function folder_exists($parent_id, $name)
	{
		$this->query("SELECT id FROM cms_folders WHERE parent_id='$parent_id' AND name='$name'");
		if($this->next_record())
		{
			return $this->f('id');
		}
		return false;
	}

	function delete_folder($folder_id)
	{
		if ($folder_id > 0)
		{
			//add a second cms object for simultanious select and delete from the db
			$cms2 = new cms();

			//get all folders
			$this->get_folders($folder_id);
			while($this->next_record())
			{
				if (!$cms2->delete_folder($this->f('id')))
				{
					return false;
				}
			}

			$this->get_files($folder_id);
			while ($this->next_record())
			{
				if(!$cms2->delete_file($this->f('id')))
				{
					return false;
				}
			}

			return $this->query("DELETE FROM cms_folders WHERE id='$folder_id'");
		}else
		{
			return false;
		}
	}

	function delete_file($file_id)
	{
		return $this->query("DELETE FROM cms_files WHERE id='$file_id'");
	}

	function get_file($file_id)
	{
		$this->query("SELECT * FROM cms_files WHERE id='$file_id'");
		if ($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function get_next_priority($folder_id)
	{
		$sql = "SELECT max(priority) FROM cms_folders WHERE parent_id='$folder_id'";
		$this->query($sql);
		if($this->next_record())
		{
			$max_folder_priority = $this->f(0);
		}
		$sql = "SELECT max(priority) FROM cms_files WHERE folder_id='$folder_id'";
		$this->query($sql);
		if($this->next_record())
		{
			$max_file_priority = $this->f(0);
		}
		$priority = $max_file_priority > $max_folder_priority ? $max_file_priority : $max_folder_priority;
		return $priority+1;
	}

	function update_file_priority($file_id, $priority)
	{
		$sql = "UPDATE cms_files SET priority='$priority' WHERE id='$file_id'";
		return $this->query($sql);
	}

	function update_folder_priority($folder_id, $priority)
	{
		$sql = "UPDATE cms_folders SET priority='$priority' WHERE id='$folder_id'";
		return $this->query($sql);
	}

	function move_folder_up($folder_id)
	{
		if($folder = $this->get_folder($folder_id))
		{
			$old_priority = $folder['priority'];
			$new_priority = $folder['priority']-1;

			$hp = $this->find_priority($folder['parent_id'], $new_priority);

			$sql = "UPDATE cms_folders SET priority='$new_priority' WHERE id='$folder_id'";
			$this->query($sql);

			if($hp)
			{
				$sql = "UPDATE cms_".$hp['type']."s SET priority='$old_priority' WHERE id='".$hp['id']."'";
				$this->query($sql);
			}
		}
	}
	function move_file_up($file_id)
	{
		if($file = $this->get_file($file_id))
		{
			$old_priority = $file['priority'];
			$new_priority = $file['priority']-1;

			$hp = $this->find_priority($file['folder_id'], $new_priority);

			$sql = "UPDATE cms_files SET priority='$new_priority' WHERE id='$file_id'";
			$this->query($sql);

			if($hp)
			{
				$sql = "UPDATE cms_".$hp['type']."s SET priority='$old_priority' WHERE id='".$hp['id']."'";
				$this->query($sql);
			}
		}
	}

	function find_priority($folder_id, $new_priority)
	{
		$sql = "SELECT id FROM cms_folders WHERE priority='$new_priority' AND parent_id='$folder_id'";
		$this->query($sql);
		if($this->next_record())
		{
			return array('type'=>'folder', 'id'=>$this->f('id'));
		}else
		{
			$sql = "SELECT id FROM cms_files WHERE priority='$new_priority' AND folder_id='$folder_id'";
			$this->query($sql);
			if($this->next_record())
			{
				return array('type'=>'file', 'id'=>$this->f('id'));
			}
		}
		return false;
	}

	function add_file($folder_id, $name, $content, $title='', $description='', $keywords='', $template_item_id, $auto_meta='1', $hot_item=0, $acl=0)
	{
		$file_id = $this->nextid('cms_files');
		if ($file_id > 0)
		{
			$priority = $this->get_next_priority($folder_id);
			$size = strlen($content);
			$extension = get_extension($name);
			$mtime =$ctime = get_gmt_time();

			$sql  = "INSERT INTO cms_files (id, folder_id, name, extension, ctime, mtime, size, content, title, description, keywords, priority, auto_meta, template_item_id, hot_item, acl) ";
			$sql .= "VALUES ('$file_id', '$folder_id', '$name', '$extension', '$ctime', '$mtime', '$size', '$content', '$title', '$description', '$keywords', '$priority', '$auto_meta', '$template_item_id', '$hot_item', '$acl')";
			if ($this->query($sql))
			{
				return $file_id;
			}
		}
		return false;
	}

	function update_file($file_id, $name, $content, $auto_meta, $title='', $description='', $keywords='', $hot_item='0', $template_item_id, $acl, $unregistered_comments='0',$registered_comments='0')
	{
		$size = strlen($content);
		$extension = get_extension($name);
		$mtime = get_gmt_time();

		$sql = "UPDATE cms_files SET name='$name', extension='$extension', ".
		"mtime='$mtime', size='$size', content='$content', ".
		"title='$title', description='$description', ".
		"keywords='$keywords', hot_item='$hot_item', auto_meta='$auto_meta', template_item_id='$template_item_id', acl='$acl' ".
		"WHERE id='$file_id'";
		return $this->query($sql);
	}
	
	function add_comment($comment)
	{
		$comment['id']=$this->nextid('cms_comments');
		$comment['ctime']=get_gmt_time();
		if($this->insert_row('cms_comments', $comment))
		{
			return $comment['id'];
		}
		return false;
	}
	
	function get_comments($file_id)
	{
		$sql = "SELECT * FROM cms_comments WHERE file_id=$file_id ORDER BY ctime DESC";
		$this->query($sql);
		return $this->num_rows();
	}
	
	function delete_comment($comment_id)
	{
		$sql = "DELETE FROM cms_comments WHERE id=$comment_id";
		return $this->query($sql);
	}
	
	function __update_file($file)
	{
		$file['mtime']=get_gmt_time();
		return $this->update_row('cms_files', 'id', $file);	
	}

	//template functions

	function add_template($template)
	{
		//create the template
		$template['id'] = $this->nextid('cms_templates');
		if ($template['id'] > 0)
		{
			if($this->insert_row('cms_templates', $template))
			{
				return $template['id'];			
			}
		}

		return false;
	}

	function update_template($template)
	{
		return $this->update_row('cms_templates','id', $template);
	}

	function get_templates()
	{
		$this->query("SELECT * FROM cms_templates");
		return $this->num_rows();
	}

	function get_authorized_templates($user_id)
	{		
		$sql = "SELECT DISTINCT cms_templates.*".
				"FROM cms_templates ".
				"	INNER JOIN acl ON ( cms_templates.acl_read = acl.acl_id ".
				"OR cms_templates.acl_write = acl.acl_id ) ".
				"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id ".
				"WHERE acl.user_id=$user_id ".
				"OR users_groups.user_id=$user_id".
				"	ORDER BY cms_templates.name ASC";
				
		$this->query($sql);
		return $this->num_rows();
	}

	function get_template($template_id)
	{
		$this->query("SELECT * FROM cms_templates WHERE id='$template_id'");
		if ($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function get_template_by_name($user_id, $name)
	{
		$this->query("SELECT * FROM cms_templates WHERE user_id='$user_id' AND name='$name'");
		if ($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function delete_template($template_id)
	{
		global $GO_SECURITY;
		
		if($template = $this->get_template($template_id))
		{
			$GO_SECURITY->delete_acl($template['acl_read']);
			$GO_SECURITY->delete_acl($template['acl_write']);
				
			$this->query("DELETE FROM cms_template_items WHERE template_id='$template_id'");
			return $this->query("DELETE FROM cms_templates WHERE id='$template_id'");
		}	
	}

	function add_template_item($template_id, $name, $content, $page='0')
	{
		//create the template
		$template_item_id = $this->nextid('cms_template_items');
		if ($template_item_id > 0)
		{
			if($this->query("INSERT INTO cms_template_items (id, template_id, name, content, page) VALUES ('$template_item_id', '$template_id', '$name', '$content', '$page')"))
			{
				return $template_item_id;
			}
		}

		return false;
	}

	function update_template_item($template_item_id, $name, $content, $page)
	{
		return $this->query("UPDATE cms_template_items SET page='$page', name='$name',content='$content' WHERE id='$template_item_id'");
	}

	function get_template_items($template_id, $page_templates_only=false)
	{
		$sql = "SELECT * FROM cms_template_items WHERE template_id='$template_id'";
		
		if($page_templates_only)
		{
			$sql .= " AND page='1'";
		}
		
		$this->query($sql);
		return $this->num_rows();
	}

	function get_template_item($template_item_id)
	{
		$this->query("SELECT * FROM cms_template_items WHERE id='$template_item_id'");
		if ($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function delete_template_item($template_item_id)
	{
		return $this->query("DELETE FROM cms_template_items WHERE id='$template_item_id'");
	}
	function get_template_item_by_name($template_id, $name)
	{
		$this->query("SELECT * FROM cms_template_items WHERE template_id='$template_id' AND name='$name'");
		if ($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function replace_template_items($template_id)
	{
		$cms = new cms();
		$this->get_template_items($template_id);
		while($this->next_record())
		{
			$content = $this->replace_template_files($template_id, $this->f('content'));
			$cms->update_template_item($this->f('id'), $this->f('name'), $content);
		}
	}

	function replace_template_files($template_id, $content)
	{
		global $GO_MODULES, $GO_CONFIG;

		$cms = new cms();
		$cms_module = $GO_MODULES->get_module('cms');

		$attributes[] = 'src';
		$attributes[] = 'href';
		$attributes[] = 'url(';

		$length = strlen($content);
		$replacements = array();

		while($attribute = array_shift($attributes))
		{
			$offset = 0;
			$url = '';
			$end=false;
			
			while($pos = strpos($content, $attribute, $offset))
			{
				$in_value = false;
				for($offset=$pos;$offset<$length;$offset++)
				{
					$char = $content[$offset];
					switch ($char)
					{
						case '"':
						if($in_value && $escape_char == '"')
						{
							if(file_exists($GO_CONFIG->local_path.'cms/templates/'.$template_id.'/'.basename($url)))
							{								
								$replacement['old'] = $url;
								$replacement['new'] = $GO_CONFIG->local_url.'cms/templates/'.$template_id.'/'.basename($url);								

								$replacements[] = $replacement;
							}
							$url = '';
							$end=true;
							break;
						}elseif(!$in_value)
						{
							$escape_char = '"';
							$in_value = true;
						}
						break;

						case "'":
						if($in_value && $escape_char == "'")
						{
							if(file_exists($GO_CONFIG->local_path.'cms/templates/'.$template_id.'/'.basename($url)))
							{								
								$replacement['old'] = $url;								
								$replacement['new'] = $GO_CONFIG->local_url.'cms/templates/'.$template_id.'/'.basename($url);
								$replacements[] = $replacement;
							}
							$url = '';
							$end=true;
							break;
						}elseif(!$in_value)
						{
							$escape_char = "'";
							$in_value = true;
						}
						break;

						default:
						if($in_value)
						{
							$url .= $char;
						}
						break;
					}
					if($end)
					{
							$end=false;
							break;
					}
				}
			}
		}
		while($replacement = array_shift($replacements))
		{
			//echo $replacement['old'].'- >'.$replacement['new'].'<br>';
			$content = str_replace($replacement['old'], $replacement['new'], $content);
		}
		return $content;
	}




	function get_template_file($template_file_id)
	{
		$this->query("SELECT * FROM cms_template_files WHERE id='$template_file_id'");
		if ($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}
	
	
	function search_files($folder_id, $search_word)
	{
		
		//$search_word = strtoupper($search_word);
		//$search_word = '%'.$search_word.'%';
		$cms = new cms();

		$files = array();
		/*
		$sql = "SELECT * FROM cms_files WHERE (extension='html' OR extension='htm') AND folder_id='$folder_id' AND (UPPER(content) REGEXP '[[:<:]]".$search_word."[[:>:]]'";

		$search_word2 = htmlspecialchars($search_word);
		if($search_word2 != $search_word)
		{
			$sql .= " OR UPPER(content) REGEXP '[[:<:]]".$search_word2."[[:>:]]')";
		}else
		{
			$sql .= ")";
		}
*/
		
		$sql = "SELECT * FROM cms_files WHERE (extension='html' OR extension='htm') AND folder_id='$folder_id'";
		
		$allkeywords=array();
		$keywords= explode(' ', $search_word);
		foreach($keywords as $keyword)
		{
			
			$keyword2 = htmlspecialchars($keyword);
			$sql_str='content LIKE \'%'.$keyword.'%\'';
			if($keyword2 != $keyword)
			{
				$sql_str='('.$sql_str.' OR content LIKE \'%'.$keyword2.'%\')';
			}
			$allkeywords[]=$sql_str;
		}
		
		if(count($allkeywords))
		{
			$sql .= ' AND '.implode(' AND ',$allkeywords);
		}
	

		$this->query($sql);

		while ($this->next_record())
		{
			$files[] = $this->Record;
		}

		$this->get_folders($folder_id);
		while($this->next_record())
		{
			$files = array_merge($files, $cms->search_files($this->f('id'), $search_word));
		}
		return $files;
	}

	function get_body($html)
	{
		$to_removed_array = array (
		"'<html[^>]*>'si",
		"'</html>'si",
		"'<body[^>]*>'si",
		"'</body>'si",
		"'<head[^>]*>.*?</head>'si",
		"'<style[^>]*>.*?</style>'si",
		"'<object[^>]*>.*?</object>'si",
		);
		$html = preg_replace($to_removed_array, '', $html);
		//$html = preg_replace("/class\s*=\s*[\"']?.*?[\"']?\s/is", '', $html);
		//$html = preg_replace("/style\s*=\s*[\"'].*?[\"']\s/is", '', $html);
		//werkt min of meer$html = preg_replace("/class.*?\s/is", '', $html);
		//$html = preg_replace("/style.*?\s/is", '', $html);


		return $html;

	}

	function remove_styles($html)
	{
		$to_removed_array = array (
		"'class=[\w]*\b'si",
		"'class=\"[^\"]*\"'si",
		"'class=\'[^\']*\''si",
		"'style=\"[^\"]*\"'si",
		"'style=\'[^\']*\''si",
		);
		//$html = preg_replace("/class\s*=\s*.*?\s/is", '', $html);
		//$html = preg_replace("/class\s*=\s*.*?\s/is", '', $html);
		$html = preg_replace($to_removed_array, '', $html);
		return $html;
	}


	function extract_styles($style)
	{
		$styles = array();
		$style_name = '';
		$in_style = true;

		$style = str_replace("\r", '', $style);
		$style = str_replace("\n", '', $style);
		$style = str_replace(' ', '', $style);

		for ($i=0; $i<strlen($style); $i++)
		{
			$char = $style[$i];

			if ($char == '{' || $char == ',')
			{
				$in_style = false;
				if (trim($style_name) != '')
				{
					$styles[] = $style_name;
				}
				$style_name = '';
			}elseif($char == '.')
			{
				if ($style_name != '')
				{
					$style_name = '';
					$in_style = false;
				}else
				{
					$in_style = true;
				}
			}elseif($char == ':')
			{
				$style_name = '';
				$in_style = false;
			}elseif($char == '}')
			{
				$in_style = true;
				$style_name = '';
			}else
			{
				if ($in_style)
				{
					$style_name .= $char;
				}
			}
		}

		return $styles;
	}

	function clean_up_html($html)
	{
		// remove escape slashes
		$html = stripslashes($html);

		// strip tags, still leaving attributes, second variable is allowable tags
		$html = strip_tags($html, '<p><b><br><img><i><u><a><h1><h2><h3><h4><h4><h5><h6><table><tr><td>');

		/*
		// removes the attributes for allowed tags, use separate replace for heading tags since a
		// heading tag is two characters
		$html = ereg_replace("<([p|b|i|u])[^>]*>", "<\\1>", $html);
		$html = ereg_replace("<([h1|h2|h3|h4|h5|h6][1-6])[^>]*>", "<\\1>", $html);
		*/
		// remove all class and style attributes from the remaining tags
		$to_removed_array = array (
		"'class=[\w]*\b'si",
		"'class=\"[^\"]*\"'si",
		"'class=\'[^\']*\''si",
		"'style=\"[^\"]*\"'si",
		"'style=\'[^\']*\''si",
		);
		//$html = preg_replace("/class\s*=\s*.*?\s/is", '', $html);
		//$html = preg_replace("/class\s*=\s*.*?\s/is", '', $html);
		$html = preg_replace($to_removed_array, '', $html);
		return $html;
	}
	function strip_for_multipage($html)
	{
		$html = str_replace("\r", '', $html);
		$html = str_replace("\n",' ', $html);
		
		$html = str_replace('<p>&nbsp;</p>','', $html);
	
		$to_removed_array = array (
			"'<h1[^>]*>.*?</h1>'si",
			"'<h2[^>]*>.*?</h2>'si",
			"'<h3[^>]*>.*?</h3>'si",
			"'<h4[^>]*>.*?</h4>'si",
			"'<h5[^>]*>.*?</h5>'si",
			"'<h6[^>]*>.*?</h6>'si");
	
		$html = preg_replace($to_removed_array, '', $html);
	
		return strip_tags($html);
	}
	
	function get_title_from_html($html, $title='')
	{
		global $GO_CONFIG;
		
		$important_tags = array(
		'//h1', '//h2', '//h3', '//h4', '//h5', '//h6', '//strong', '//b', "//*[@style='font-weight: bold;']", '//i', "//*[@style='font-style: italic;']"
		);
		

		
		require_once($GO_CONFIG->class_path.'xml/xpath.class.inc');
		$xpath = new xpath();
		$html = str_replace('>', '> ', $html);
		$html = str_replace('\r','', $html);
		$html = str_replace('\n',' ', $html);
		$html = preg_replace('/&[^;]*;/', '', $html);
		$html = '<html>'.strip_tags($html, '<h1><h2><h3><h4><h5><h6><strong><span><ul><ol><li><b><i>').'</html>';

		
		if($xpath->importFromString($html))
		{
			foreach($important_tags as $important_tag)
			{
				$nodes = $xpath->match($important_tag);
				foreach($nodes as $node)
				{
					if(strlen($title) == 0)
					{
						$title = trim(strip_tags($xpath->getData($node)));
					}else
					{
						$important_words = $this->get_keywords_from_string($xpath->getData($node));					
						
						foreach($important_words as $word)
						{
							if(strlen($title.' '.$word) > 90)
							{
								return htmlspecialchars($title);
							}elseif(!stristr($title, $word))
							{							
								if(!isset($first))
								{
									$title .= ' - ';
									$first = true;
								}else
								{
									$title .= ' ';
								}
								$title .= trim($word);
							}					 
						}
					}
				}			
			}	
		}
		return htmlspecialchars($title);		
	}
	
	function get_keywords_from_string($string)
	{
		$words = explode(' ', $string);
			
		$important_words = array();
		foreach($words as $word)
		{			
			$word = $this->strip_unwanted_chars($word);
			if(strlen($word) > 0)
			{
				if(
				(strlen($word) > 5  || strtoupper($word) == $word)		&& 
					!in_array(strtolower($word), $important_words))
				{
					$important_words[] = strtolower($word);
				}
			}
		}			
		return $important_words;
	}
	
	function get_description_from_html($html, $description='')
	{
		global $GO_CONFIG;
		
		$important_tags = array(
		'//h1', '//h2', '//h3', '//h4', '//h5', '//h6', '//strong', '//b', "//*[@style='font-weight: bold;']", '//i', "//*[@style='font-style: italic;']"
		);
		
		require_once($GO_CONFIG->class_path.'xml/xpath.class.inc');
		$xpath = new xpath();

		$html = str_replace('>', '> ', $html);
		$html = str_replace('\r','', $html);
		$html = str_replace('\n',' ', $html);
		$html = preg_replace('/&[^;]*;/', '', $html);
		
		$html = '<html>'.strip_tags($html, '<h1><h2><h3><h4><h5><h6><strong><span><ul><ol><li><b><i>').'</html>';

		
	
		if($xpath->importFromString($html))
		{
			foreach($important_tags as $important_tag)
			{
				$nodes = $xpath->match($important_tag);
				foreach($nodes as $node)
				{
					$important_words = $this->get_keywords_from_string($xpath->getData($node));					
					
					foreach($important_words as $word)
					{
						if(strlen($description.' '.$word) > 250)
						{
							return htmlspecialchars($description);
						}elseif(!stristr($description, $word))
						{							
							if(strlen($description) > 0)
							{
								$description .= ' ';
							}
							$description .= trim($word);
						}					 
					}			
				}			
			}
		}
		return htmlspecialchars($description);
	}
	
	function get_keywords_from_html($html, $keywords='')
	{
		$keywordsArr = $this->get_keywords_from_html_in_array($html);
		foreach($keywordsArr as $keyword)
		{
			if(!stristr($keywords, $keyword))
			{
				if($keywords != '')
				{
					$keywords .= ', ';
				}
				$keywords .= trim($keyword);
			}
		}		
		return htmlspecialchars($keywords);
	}
	
	function strip_unwanted_chars($word)
	{
		//cannot yet handle MBCS in html_entity_decode BUG
		//global $charset;		
		//$word = html_entity_decode($word, ENT_QUOTES, 'UTF-8');
		
		//Workaround:
		$word = str_replace('&nbsp;',' ', $word);
		$word = str_replace('&amp;','&', $word);
		
		$word = trim($word);
		$word = str_replace('(','', $word);
		$word = str_replace(')','', $word);
		$word = str_replace('.','', $word);
		$word = str_replace('!','', $word);
		$word = str_replace('?','', $word);
		$word = str_replace(':','', $word);
		$word = str_replace(',','', $word);
		
		return $word;		
	}
	
	
	function get_keywords_from_html_in_array($html)
	{
		global $GO_CONFIG;
		
		$important_tags = array(
		'//h1', '//h2', '//h3', '//h4', '//h5', '//h6', '//strong', '//b', "//*[@style='font-weight: bold;']", '//i', "//*[@style='font-style: italic;']"
		);
		
		require_once($GO_CONFIG->class_path.'xml/xpath.class.inc');
		$xpath = new xpath();

		$html = str_replace('\r','', $html);
		$html = str_replace('\n',' ', $html);		
		$html = str_replace('>','> ', $html);
		$html = preg_replace('/&[^;]*;/', '', $html);	
		$html = '<html>'.strip_tags($html, '<h1><h2><h3><h4><h5><h6><strong><span><ul><ol><li><b><i>').'</html>';
		
		$important_words = array();
		if($xpath->importFromString($html))
		{		
			$strings = array();
			foreach($important_tags as $important_tag)
			{
				$nodes = $xpath->match($important_tag);
				foreach($nodes as $node)
				{
					$strings[] = strip_tags($xpath->getData($node));
				}			
			}			
			
			$string = implode(' ', $strings);
			$important_words = array_merge($important_words, $this->get_keywords_from_string($string));			
			
			//Words that occur more then once
			$text = strip_tags($html);		
			
			$words = explode(' ', $text);
			
			foreach($words as $word)
			{			
				$word = $this->strip_unwanted_chars($word);
				if(strlen($word) > 0)
				{
					if(
					(strlen($word) > 6  || strtoupper($word) == $word)		&& 
					count(array_keys($words, $word)) > 1 &&
						!in_array(strtolower($word), $important_words))
					{
						$important_words[] = strtolower($word);
					}
				}
			}
		}
		return $important_words;	
	}

	function __on_user_delete($user_id)
	{
		$cms = new cms();
		$sql = "SELECT id FROM cms_sites WHERE user_id='$user_id'";
		$this->query($sql);
		while($this->next_record())
		{
			$cms->delete_site($this->f('id'));
		}

		$sql = "SELECT id FROM cms_templates WHERE user_id='$user_id'";
		$this->query($sql);
		while($this->next_record())
		{
			$cms->delete_template($this->f('id'));
		}
		$this->query("DELETE FROM cms_settings WHERE user_id='$user_id'");
	}
}
