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
class comments extends db {
		/**
	 * Add a Comment
	 *
	 * @param Array $comment Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_comment($comment)
	{
		$comment['ctime']=$comment['mtime']=time();
		$comment['id']=$this->nextid('co_comments');
		if($this->insert_row('co_comments', $comment))
		{
			return $comment['id'];
		}
		return false;
	}
	/**
	 * Update a Comment
	 *
	 * @param Array $comment Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_comment($comment)
	{
		$comment['mtime']=time();
		return $this->update_row('co_comments', 'id', $comment);
	}
	/**
	 * Delete a Comment
	 *
	 * @param Int $comment_id ID of the comment
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_comment($comment_id)
	{
		return $this->query("DELETE FROM co_comments WHERE id=".intval($comment_id));
	}
	/**
	 * Gets a Comment record
	 *
	 * @param Int $comment_id ID of the comment
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_comment($comment_id)
	{
		$this->query("SELECT * FROM co_comments WHERE id=".intval($comment_id));
		if($this->next_record())
		{
			return $this->record;
		}else
		{
			throw new DatabaseSelectException();
		}
	}
	/**
	 * Gets a Comment record by the name field
	 *
	 * @param String $name Name of the comment
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_comment_by_name($name)
	{
		$this->query("SELECT * FROM co_comments WHERE name='".$this->escape($name)."'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}
	/**
	 * Gets all Comments
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_comments($link_id, $link_type, $query, $sortfield='id', $sortorder='ASC', $start=0, $offset=0)
	{
		$sql = "SELECT * FROM co_comments WHERE link_id='".$this->escape($link_id)."' AND link_type='".$this->escape($link_type)."'";
		if(!empty($query))
 		{
 			$sql .= " AND comments LIKE '".$this->escape($query)."'";
 		} 		
		$sql .= " ORDER BY ".$this->escape($sortfield." ".$sortorder);
		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".intval($start).",".intval($offset);
			$this->query($sql);
		}
		return $count;
	}
	
	function get_comments_json($link_id, $link_type)
	{
		global $GO_CONFIG;

		require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();
		
		$comments = array();
		$this->get_comments($link_id, $link_type, '', 'ctime', 'DESC', 0, 5);
		while($this->next_record())
		{
			$this->record['user_name']=$GO_USERS->get_user_realname($this->f('user_id'));
			$this->record['ctime']=Date::get_timestamp($this->record['ctime']);
			$this->record['comments']=String::text_to_html($this->record['comments']);
			$comments[]=$this->record;
		}
		
		return $comments;
	}
}
