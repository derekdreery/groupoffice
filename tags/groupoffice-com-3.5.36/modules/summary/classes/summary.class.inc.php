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
class summary extends db{
	
	public function __on_load_listeners($events){
		$events->add_listener('add_user', __FILE__, 'summary', 'add_user');
		$events->add_listener('user_delete', __FILE__, 'summary', 'user_delete');
	}
	
	public static function add_user($user)
	{
		global $GO_SECURITY, $GO_LANGUAGE, $GO_CONFIG;

		require($GO_LANGUAGE->get_language_file('summary',$user['language']));
		
		$feed['url'] = $lang['summary']['default_rss_url'];
		$feed['title'] = $lang['summary']['default_rss_title'];
		$feed['user_id'] = $user['id'];
		$feed['summary'] = 1;
		
		$su = new summary();
		
		$su->add_feed($feed);
	}
	
	public static function user_delete($user)
	{			
		$su = new summary();
		$su->query("DELETE FROM su_rss_feeds WHERE user_id=".$user['id']);		
		$su->query("DELETE FROM su_notes WHERE user_id=".$user['id']);
	}
	
	
	function get_note($user_id)
	{
		
		$sql = "SELECT * FROM su_notes WHERE user_id='".intval($user_id)."'";
		$this->query($sql);
		
		if(!$this->next_record())
		{
			$note['user_id']=$user_id;
			$this->insert_row('su_notes', $note);
			return $this->get_note($user_id);
		}else
		{
			return $this->f('text');
		}						
	}
	
	function update_note($note)
	{
		return $this->update_row('su_notes','user_id', $note);
	}
	
	
	
	function get_feed($user_id)
	{
		
		$sql = "SELECT * FROM su_rss_feeds WHERE user_id='".intval($user_id)."'";
		$this->query($sql);
		
		$this->next_record();
		return $this->f('url');
	}

	function get_feeds($user_id)
	{

		$sql = "SELECT * FROM su_rss_feeds WHERE user_id='".intval($user_id)."' ORDER BY id";
		$this->query($sql);
		$urls = array();

		while($this->next_record())
			$urls[] = $this->record;
		return $urls;
	}
	
	function add_feed($feed)
	{
		if($this->insert_row('su_rss_feeds', $feed))
		{
			return $this->insert_id();
		}
		return false;
	}
	
	function update_feed($feed)
	{
		return $this->update_row('su_rss_feeds', 'id', $feed);
	}

	function delete_other_feeds($user_id, $ids)
	{
		$sql = "DELETE FROM su_rss_feeds WHERE user_id=".intval($user_id);
		if(count($ids))
		{
			$sql .= " AND id NOT IN (".implode(',', $ids).")";
		}
		$this->query($sql);
	}

	/**
	 * Add a Announcement
	 *
	 * @param Array $announcement Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_announcement($announcement)
	{
		$announcement['ctime']=$announcement['mtime']=time();
		$announcement['id']=$this->nextid('su_announcements');
		if($this->insert_row('su_announcements', $announcement))
		{
			return $announcement['id'];
		}
		return false;
	}
	/**
	 * Update a Announcement
	 *
	 * @param Array $announcement Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_announcement($announcement)
	{
		$announcement['mtime']=time();
		return $this->update_row('su_announcements', 'id', $announcement);
	}
	/**
	 * Delete a Announcement
	 *
	 * @param Int $announcement_id ID of the announcement
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_announcement($announcement_id)
	{
		return $this->query("DELETE FROM su_announcements WHERE id='".$this->escape($announcement_id)."'");
	}
	/**
	 * Gets a Announcement record
	 *
	 * @param Int $announcement_id ID of the announcement
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_announcement($announcement_id)
	{
		$this->query("SELECT * FROM su_announcements WHERE id='".$this->escape($announcement_id)."'");
		if($this->next_record())
		{
			return $this->record;
		}else
		{
			throw new DatabaseSelectException();
		}
	}
	/**
	 * Gets a Announcement record by the name field
	 *
	 * @param String $name Name of the announcement
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_announcement_by_name($name)
	{
		$this->query("SELECT * FROM su_announcements WHERE name='".$this->escape($name)."'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}
	/**
	 * Gets all Announcements
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_announcements($query, $sortfield='id', $sortorder='ASC', $start=0, $offset=0)
	{
		$sql = "SELECT * FROM su_announcements ";
		if(!empty($query))
 		{
 			$sql .= " WHERE name LIKE '".$this->escape($query)."'";
 		} 		
		$sql .= " ORDER BY ".$this->escape($sortfield)." ".$this->escape($sortorder)."";
		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start).",".$this->escape($offset);
			$this->query($sql);
		}
		return $count;
	}
	
/**
	 * Gets all active announcements
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_active_announcements($sortfield='id', $sortorder='ASC', $start=0, $offset=0)
	{
		$time = mktime(0,0,0);
		
		$sql = "SELECT * FROM su_announcements WHERE due_time=0 OR due_time >=$time";
		$sql .= " ORDER BY ".$this->escape($sortfield)." ".$this->escape($sortorder)."";
		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT (".$this->escape($start).",".$this->escape($offset).")";
			$this->query($sql);
		}
		return $count;
	}

	/**
	 * Gets all webfeeds
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_webfeeds($query, $sortfield='id', $sortorder='ASC', $start=0, $offset=0, $user_id)
	{
		$sql = "SELECT * FROM su_rss_feeds WHERE user_id = $user_id ORDER BY ".$this->escape($sortfield)." ".$this->escape($sortorder)."";
		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start).",".$this->escape($offset);
			$this->query($sql);
		}
		return $count;
	}

/**
	 * Gets all active webfeeds
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_active_webfeeds($sortfield='url', $sortorder='ASC', $start=0, $offset=0, $user_id)
	{
		$sql = "SELECT * FROM su_rss_feeds WHERE due_time=0 OR due_time > UNIX_TIMESTAMP() AND user_id = $user_id ORDER BY ".$this->escape($sortfield)." ".$this->escape($sortorder)."";
		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT (".$this->escape($start).",".$this->escape($offset).")";
			$this->query($sql);
		}
		return $count;
	}
/* {CLASSFUNCTIONS} */
}