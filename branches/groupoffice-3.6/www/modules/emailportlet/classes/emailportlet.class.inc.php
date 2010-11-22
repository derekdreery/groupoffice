<?php

class emailportlet extends db {

	function exists_on_summary($user_id, $folder_id)
	{
		$this->query("SELECT * FROM emp_folders WHERE folder_id=? AND user_id=?", 'ii', array($folder_id, $user_id));
		return ($this->num_rows()) ? true : false;
	}

	function get_folders_on_summary($user_id)
	{
		$this->query("SELECT * FROM emp_folders WHERE user_id=? ORDER BY mtime ASC", 'i', $user_id);
		return $this->num_rows();
	}

	function insert_on_summary($user_id, $folder_id)
	{
		return $this->insert_row('emp_folders', array('folder_id' => $folder_id, 'user_id'=>$user_id, 'mtime' => time()));
	}
	
	function delete_on_summary($user_id, $folder_id)
	{
		return $this->query("DELETE FROM emp_folders WHERE folder_id=? AND user_id=?", 'ii', array($folder_id, $user_id));
	}	

}