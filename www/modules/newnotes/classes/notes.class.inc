<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 2 Januari 2004

Part of the Group-Office Professional license
*/

define('SHOW_ALL_NOTES', 1);
define('SHOW_OWN_NOTES', 2);
define('SHOW_RESPONSIBLE_NOTES', 3);

class notes extends db
{
	function notes()
	{
		$this->db();
	}

	function copy_note($note_id, $new_values=array())
	{
		global $GO_SECURITY;

		if($src_note = $dst_note = $this->get_note($note_id))
		{
			unset($dst_note['id']);

			$dst_note['acl_read'] = $GO_SECURITY->get_new_acl('event read');
			$dst_note['acl_write'] = $GO_SECURITY->get_new_acl('event write');

			$GO_SECURITY->copy_acl($src_note['acl_read'], $dst_note['acl_read']);
			$GO_SECURITY->copy_acl($src_note['acl_write'], $dst_note['acl_write']);

			foreach($new_values as $key=>$value)
			{
				$dst_note[$key] = $value;
			}

			$dst_note = array_map('addslashes', $dst_note);

			return $this->_add_note($dst_note);
		}
		return false;
	}

	function get_settings($user_id)
	{
		$this->query("SELECT * FROM no_settings WHERE user_id='$user_id'");
		if ($this->next_record())
		{
			return $this->Record;
		}else
		{
			$this->query("INSERT INTO no_settings ".
			"(user_id) VALUES ".
			"('$user_id')");
			return $this->get_settings($user_id);
		}
	}

	function set_sorting($user_id, $sort_field, $sort_order)
	{
		$sql = "UPDATE no_settings SET sort_field='$sort_field' , ".
		"sort_order='$sort_order' WHERE user_id='$user_id'";
		return $this->query($sql);
	}

	function add_note($note)
	{
		$note['id'] = $this->nextid("no_notes");
		$note['ctime'] = $note['mtime'] = get_gmt_time();

		if ($note['id'] > 0)
		{
			if($this->insert_row('no_notes', $note))
			{
				return $note['id'];
			}
		}
		return false;
	}

	function get_note($note_id)
	{
		$sql = "SELECT * FROM no_notes WHERE id='$note_id'";
		$this->query($sql);
		if ($this->next_record(MYSQL_ASSOC))
		{
			return $this->Record;
		}
		return false;
	}

	function delete_note($note_id)
	{
		global $GO_LINKS;

		$note=$this->get_note($note_id);

		$GO_LINKS->delete_link($note['link_id']);


		$sql = "DELETE FROM no_notes WHERE id='$note_id'";
		return $this->query($sql);
	}


	function update_note($note)
	{
		$note['mtime'] = get_gmt_time();
		return $this->update_row('no_notes','id', $note);
	}

	function get_note_by_name($name)
	{
		$sql = "SELECT * FROM no_notes WHERE name='$name'";
		$this->query($sql);
		if ($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function get_new_notes()
	{
		/*$sql = "SELECT DISTINCT no_notes.* FROM no_notes ".
		"INNER JOIN acl ON (no_notes.acl_read = acl.acl_id OR no_notes.acl_write = acl.acl_id) ".
		"LEFT JOIN users_groups ON (acl.group_id = users_groups.group_id) ".
		" WHERE ((users_groups.user_id = ".$_SESSION['GO_SESSION']['user_id']." AND acl.user_id = 0 ) OR (".
		"acl.group_id = 0 AND acl.user_id = ".$_SESSION['GO_SESSION']['user_id'].")) ".
		"AND mtime>".$_SESSION['GO_SESSION']['lastlogin'];

		$this->query($sql);
		return $this->num_rows();*/
	}
	
	
	

	function get_notes($user_id=0, $links=false,
	$sort='name', $direction='ASC', $start=0,
	$offset=0)
	{
		$sql = "SELECT * FROM no_notes";

		if($user_id != 0)
		{
			$where = true;
			$sql .= " WHERE user_id='$user_id'";
		}

		if (is_array($links))
		{
			if(!count($links))
			{
				return 0;
			}
			if(!isset($where))
			{
				$where = true;
				$sql .= " WHERE ";
			}else
			{
				$sql .= " AND ";
			}
			$sql .= "link_id IN (".implode(',', $links).")";
		}

		$sql .= " ORDER BY $sort $direction";

		$this->query($sql);
		$count = $this->num_rows();

		if ($offset > 0 && $count > $offset)
		{
			$this->query($sql." LIMIT $start, $offset");
		}
		return $count;
	}

	function __on_delete_user($user_id)
	{
		$sql = "DELETE FROM no_notes WHERE user_id='$user_id'";
		$this->query($sql);

		$this->query("DELETE FROM no_settings WHERE user_id='$user_id'");
	}
	
	function __on_search($user_id,$last_sync_time=0)
	{
		global $GO_MODULES, $GO_LANGUAGE;

		require($GO_LANGUAGE->get_language_file('notes'));

		$sql = "SELECT * FROM no_notes WHERE user_id='$user_id' AND mtime>$last_sync_time";

		$this->query($sql);

		$db = new db();
		while($this->next_record())
		{
			$cache['table']='no_notes';
			$cache['id']=$this->f('id');
			$cache['user_id']=$user_id;
			$cache['name'] = addslashes($this->f('name'));
			$cache['link_id'] = $this->f('link_id');
			$cache['link_type']=4;
			$cache['description']=addslashes($this->f('description'));
			$cache['url']=$GO_MODULES->modules['notes']['url'].'note.php?note_id='.$this->f('id');
			$cache['type']=$no_note;
			$cache['keywords']=addslashes(record_to_keywords($this->Record)).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');
			if($this->f('ctime')>$last_sync_time)
			{
				$db->insert_row('se_cache',$cache);
			}else {
				$db->update_row('se_cache',array('user_id','link_id'), $cache);
			}
		}
	}
}
