<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package go.basic
 */


/**
 * class to set reminders in Group-Office.
 * 
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id$
 * @copyright Copyright Intermesh
 * @package go.basic
 * @since Group-Office 2.17
 * 
 * @uses db
 */

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
		$reminder['id']=$this->nextid('go_reminders');
		if($this->insert_row('go_reminders', $reminder))
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
	
	function update_reminder($reminder, $reset_mail_send=true)
	{
		if($reset_mail_send)
			$reminder['mail_send'] = 0;
			
		return $this->update_row('go_reminders', 'id', $reminder);
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
		return $this->query("DELETE FROM go_reminders WHERE id=".$this->escape($reminder_id));
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
		return $this->query("DELETE FROM go_reminders WHERE user_id=".$this->escape($user_id));
	}
	
/**
	* Gets a reminder record by a link ID
	*
	* @param Int $link_id ID of the reminder
	*
	* @access public
	* @return Array Record properties
	*/
	
	function delete_reminders_by_link_id($link_id, $link_type)
	{
		return $this->query("DELETE FROM go_reminders WHERE link_id=".$this->escape($link_id)." AND link_type=".$this->escape($link_type));		
	}
	
	/**
	* Gets a reminder record by a link ID
	*
	* @param Int $link_id ID of the reminder
	*
	* @access public
	* @return Array Record properties
	*/
	
	function get_reminder_by_link_id($user_id, $link_id, $link_type)
	{
		$this->query("SELECT * FROM go_reminders WHERE user_id=".$this->escape($user_id)." AND link_id=".$this->escape($link_id)." AND link_type=".$this->escape($link_type));
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}
	
 /**
	* Get a reminders record by a link ID
	*
	* @param Int $link_id ID of the reminder
	*
	* @access public
	* @return Array Record properties
	*/
	
	function get_reminders_by_link_id($link_id, $link_type)
	{
		$this->query("SELECT * FROM go_reminders WHERE link_id=".$this->escape($link_id)." AND link_type=".$this->escape($link_type));
		return $this->num_rows();
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
		$this->query("SELECT * FROM go_reminders WHERE id=".$this->escape($reminder_id));
		if($this->next_record())
		{
			return $this->record;
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
		$this->query("SELECT * FROM go_reminders WHERE reminder_name='".$this->escape($name)."'");
		if($this->next_record())
		{
			return $this->record;
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
	function get_reminders($user_id, $not_mailed=false)
	{
		//echo date('Ymd G:i', time());
	 	$sql = "SELECT * FROM go_reminders WHERE user_id=".$this->escape($user_id)." AND time<".time();
		if($not_mailed)
		{
			$sql .= ' AND mail_send = 0';
		}
		$this->query($sql);

		return $this->num_rows();
		
	}
}