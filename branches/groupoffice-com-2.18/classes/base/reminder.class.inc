<?php
class reminder extends db
{
	/**
	* Add a ticket
	*
	* @param Array $ticket Associative array of record fields
	*
	* @access public
	* @return int New record ID created
	*/
	   
	function add_reminder($reminder)
	{
		$reminder['id']=$this->nextid('reminders');
		if($this->insert_row('reminders', $reminder))
		{
			return $reminder['id'];
		}
		return false;
	}
	
	/**
	* Update a reminder
	*
	* @param Array $reminder Associative array of record fields
	*
	* @access public
	* @return bool True on success
	*/
	
	function update_reminder($reminder)
	{			
		return $this->update_row('reminders', 'id', $reminder);
	}
	
	
	/**
	* Delete a reminder
	*
	* @param Int $reminder_id ID of the reminder
	*
	* @access public
	* @return bool True on success
	*/
	
	function delete_reminder($reminder_id)
	{
		return $this->query("DELETE FROM reminders WHERE id=$reminder_id");
	}
	
	/**
	* Delete all reminders for a user ID
	*
	* @param Int $user_id ID of the user
	*
	* @access public
	* @return bool True on success
	*/
	
	function delete_reminders($user_id)
	{
		return $this->query("DELETE FROM reminders WHERE user_id=$user_id");
	}
	
	
	/**
	* Gets a reminder record
	*
	* @param Int $reminder_id ID of the reminder
	*
	* @access public
	* @return Array Record properties
	*/
	
	function get_reminder($reminder_id)
	{
		$this->query("SELECT * FROM reminders WHERE id=$reminder_id");
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}
	
	/**
	* Gets a reminder record by the name field
	*
	* @param String $name Name of the reminder
	*
	* @access public
	* @return Array Record properties
	*/
	
	function get_reminder_by_name($name)
	{
		$this->query("SELECT * FROM reminders WHERE reminder_name='$name'");
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}
	
	
	/**
	* Gets all reminders
	*
	* @param Int $start First record of the total record set to return
	* @param Int $offset Number of records to return
	* @param String $sortfield The field to sort on
	* @param String $sortorder The sort order
	*
	* @access public
	* @return Int Number of records found
	*/
	function get_reminders($user_id)
	{
		//echo date('Ymd G:i', get_gmt_time());
	 	$sql = "SELECT * FROM reminders WHERE user_id=$user_id AND time<".get_gmt_time();
		$this->query($sql);

		return $this->num_rows();
		
	}
}