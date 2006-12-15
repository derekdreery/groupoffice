<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 2 Januari 2004

Part of the Group-Office Professional license
*/

class summary extends db
{
	function summary()
	{
		$this->db();
	}

	function add_announcement($user_id, $due_time, $title, $content, $acl_id)
	{
		$announcement_id = $this->nextid("sum_announcements");

		if ($announcement_id > 0)
		{
			$sql = "INSERT INTO sum_announcements (id, user_id,".
							"due_time, title, content,".
							" ctime, mtime, acl_id) ".
							"VALUES ('$announcement_id', '$user_id', '$due_time',".
							" '$title', '$content', '".get_gmt_time()."', '".get_gmt_time().
							"', '$acl_id')";

			if($this->query($sql))
			{
				return $announcement_id;
			}
		}
		return false;
	}

	function get_announcement($announcement_id)
	{
		$sql = "SELECT * FROM sum_announcements WHERE id='$announcement_id'";
		$this->query($sql);
		if ($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function delete_announcement($announcement_id)
	{
		$sql = "DELETE FROM sum_announcements WHERE id='$announcement_id'";
		return $this->query($sql);
	}

	function update_announcement($announcement_id, $title, $content, $due_time)
	{
		$sql = "UPDATE sum_announcements SET ".
							"title='$title', ".
							"due_time='$due_time', ".
							"title='$title', content='$content', ".
							"mtime='".get_gmt_time()."' WHERE id='$announcement_id'";

		return $this->query($sql);
	}

	function get_announcements()
	{
		$sql = "SELECT DISTINCT sum_announcements.* FROM sum_announcements ".
				"INNER JOIN acl ON (sum_announcements.acl_id= acl.acl_id) ".
				"LEFT JOIN users_groups ON (acl.group_id = users_groups.group_id) ".
				"WHERE ((users_groups.user_id = ".$_SESSION['GO_SESSION']['user_id']." AND acl.user_id = 0 ) OR (".
				"acl.group_id = 0 AND acl.user_id = ".$_SESSION['GO_SESSION']['user_id'].")) ".
				"AND (sum_announcements.due_time>".get_gmt_time()." OR sum_announcements.due_time=0) ORDER BY ctime DESC";

		$this->query($sql);
		return $this->num_rows();
	}
	
	function get_all_announcements($start=0,	$offset=0)
	{
		$sql = "SELECT id, due_time, title, mtime FROM sum_announcements ";
		$sql .= "ORDER BY mtime DESC";

		if ($offset > 0)
		{
			$this->query($sql);

			$count = $this->num_rows();
			if ($count > 0)
			{
				$this->query($sql." LIMIT $start, $offset");
			}
			return $count;

		}else
		{
			$this->query($sql);
			return $this->num_rows();
		}
	}

	function delete_user($user_id)
	{
		$sql = "DELETE FROM sum_announcements WHERE user_id='$user_id'";
		$this->query($sql);
	}
}
?>
