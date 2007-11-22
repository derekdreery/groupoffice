<?php
/*
Copyright Intermesh 2004
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003
Version: 2.0 Release date: 12 March 2004

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

class bookmarks extends db
{
	function bookmarks()
	{
		$this->db();
	}

	function add_bookmark($user_id, $catagory_id, $URL, $name, $new_window, $acl_read, $acl_write)
	{
		if ($new_window == true)
		{
			$new_window = 1;
		}else
		{
			$new_window = 0;
		}

		$bookmark_id = $this->nextid("bm_bookmarks");
		if ($bookmark_id > 0)
		{
			$sql = "INSERT INTO bm_bookmarks (id, user_id, catagory_id, URL, name, ".
							"new_window, acl_read, acl_write) VALUES ('$bookmark_id', '$user_id', ".
							"'$catagory_id', '$URL', ".
							"'$name', '$new_window', '$acl_read', '$acl_write')";
			if ($this->query($sql))
			{
				return $bookmark_id;
			}

		}
		return false;
	}

	function update_bookmark($bookmark_id, $catagory_id, $URL, $name, $new_window)
	{
		if ($new_window == true)
		{
			$new_window = 1;
		}else
		{
			$new_window = 0;
		}

		$sql = "UPDATE bm_bookmarks SET catagory_id='$catagory_id', ".
						"URL='$URL', ".
						"name='$name', ".
						"new_window='$new_window' WHERE id='$bookmark_id'";

		return ($this->query($sql));

	}
	function move_bookmark($bookmark_id, $catagory_id)
	{
		$sql = "UPDATE bm_bookmarks SET catagory_id='$catagory_id' ".
								"WHERE id='$bookmark_id'";
		return ($this->query($sql));
	}

	function delete_bookmark($bookmark_id)
	{
		global $GO_SECURITY;
		
		$sql = "DELETE FROM bm_bookmarks WHERE id='$bookmark_id'";
		return $this->query($sql);

	}

	function get_bookmarks($user_id, $catagory_id)
	{
		$sql = "SELECT DISTINCT bm_bookmarks.* FROM bm_bookmarks ".
						"INNER JOIN acl ON (bm_bookmarks.acl_read = acl.acl_id OR ".
						"bm_bookmarks.acl_write = acl.acl_id) ".
						"LEFT JOIN users_groups ON (acl.group_id=users_groups.group_id) ".
						"WHERE (users_groups.user_id=".$user_id." ".
						"OR acl.user_id = ".$user_id.") ".
						"AND catagory_id='$catagory_id' ORDER BY name ASC";


		$this->query($sql);
		return $this->num_rows();
	}

	function get_user_bookmarks($user_id, $catagory_id=-1)
	{
		$sql = "SELECT * FROM bm_bookmarks WHERE user_id='$user_id'";

		if ($catagory_id > -1)
		{
			$sql .= " AND catagory_id='$catagory_id'";
		}
		$this->query($sql);
		return $this->next_record();
	}

	function get_bookmark($bookmark_id)
	{
		$sql = "SELECT * FROM bm_bookmarks WHERE id='$bookmark_id'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function add_catagory($user_id, $name, $acl_read, $acl_write)
	{
		$catagory_id = $this->nextid("bm_catagories");
		if ($catagory_id > 0)
		{
			if($this->query("INSERT INTO bm_catagories ".
							"(id, name, acl_read, acl_write) VALUES ".
							"('$catagory_id', '$name', '$acl_read', ".
							"'$acl_write')"))
			{
				return $catagory_id;
			}
		}
		return false;
	}

	function update_catagory($catagory_id, $name)
	{
		return $this->query("UPDATE bm_catagories SET ".
						"name='$name' WHERE ".
						"id='$catagory_id'");
	}

	function delete_catagory($catagory_id)
	{
		global $GO_SECURITY;

		if($catagory = $this->get_catagory($catagory_id))
		{
			
			$this->query("SELECT * FROM bm_bookmarks WHERE catagory_id='$catagory_id'");

			$bookmarks = new bookmarks();
	
			while($this->next_record())
			{
				$GO_SECURITY->delete_acl($this->f('acl_read'));
				$GO_SECURITY->delete_acl($this->f('acl_write'));
				$bookmarks->delete_bookmark($this->f('id'));			
			}

			$GO_SECURITY->delete_acl($catagory['acl_read']);
			$GO_SECURITY->delete_acl($catagory['acl_write']);
			
			return $this->query("DELETE FROM bm_catagories WHERE ".			
												"id='$catagory_id'");
		}
	}
		
		
	function get_catagories($user_id, $only_writable=false)
	{
		$sql = "SELECT DISTINCT bm_catagories.* FROM bm_catagories ".
						"INNER JOIN acl ON (bm_catagories.acl_write=acl.acl_id ";
		if (!$only_writable)
		{
			$sql .= "OR bm_catagories.acl_read=acl.acl_id";
		}

		$sql .= ") LEFT JOIN users_groups ON (acl.group_id=users_groups.group_id) ".
						"WHERE ((users_groups.user_id=".$user_id." AND acl.user_id=0) ".
						"OR (acl.group_id = 0 AND acl.user_id = ".$user_id.")) ".
						"ORDER BY name ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_catagory($catagory_id)
	{
		$sql = "SELECT * FROM bm_catagories WHERE id='$catagory_id'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function get_catagory_by_name($name)
	{
		$sql = "SELECT * FROM bm_catagories WHERE name='$name'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function __on_user_delete($user_id)
	{
		$this->get_user_bookmarks($user_id);
		$del = new bookmarks();
		while ($this->next_record())
		{
			$del->delete_bookmark($user_id);
		}
	}
}
