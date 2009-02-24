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

class notes extends db {
		
	/**
	 * Add a Category
	 *
	 * @param Array $category Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_category($category)
	{
		
		
		$category['id']=$this->nextid('no_categories');
		if($this->insert_row('no_categories', $category))
		{
			return $category['id'];
		}
		return false;
	}

	/**
	 * Update a Category
	 *
	 * @param Array $category Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_category($category)
	{
		
		return $this->update_row('no_categories', 'id', $category);
	}


	/**
	 * Delete a Category
	 *
	 * @param Int $category_id ID of the category
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_category($category_id)
	{				
		$this->query("DELETE FROM no_notes WHERE category_id=".$this->escape($category_id));
		return $this->query("DELETE FROM no_categories WHERE id=".$this->escape($category_id));
	}


	/**
	 * Gets a Category record
	 *
	 * @param Int $category_id ID of the category
	 *
	 * @access public
	 * @return Array Record properties
	 */

	function get_category($category_id=0)
	{
		if($category_id>0)
		{
			$this->query("SELECT * FROM no_categories WHERE id=".$this->escape($category_id));
			return $this->next_record();

		}else
		{
			global $GO_SECURITY;

			$category = $this->get_default_category($GO_SECURITY->user_id);
			if ($category)
			{
				return $category;
			}else
			{
				global $GO_USERS;

				$category['user_id']=$GO_SECURITY->user_id;
				$user = $GO_USERS->get_user($GO_SECURITY->user_id);
				$task_name = String::format_name($user['last_name'], $user['first_name'], $user['middle_name'], 'last_name');
				$category['name'] = $task_name;
				$category['acl_read']=$GO_SECURITY->get_new_acl();
				$category['acl_write']=$GO_SECURITY->get_new_acl();
				$x = 1;
				while($this->get_category_by_name($category['name']))
				{
					$category['name'] = $task_name.' ('.$x.')';
					$x++;
				}

				if (!$category_id = $this->add_category($category))
				{
					throw new DatabaseInsertException();
				}else
				{
					return $this->get_category($category_id);
				}
			}
		}
		
	}
	
	function get_default_category($user_id)
	{
		$sql = "SELECT * FROM no_categories WHERE user_id='".$this->escape($user_id)."' LIMIT 0,1";
		$this->query($sql);
		return $this->next_record();
	}

	/**
	 * Gets a Category record by the name field
	 *
	 * @param String $name Name of the category
	 *
	 * @access public
	 * @return Array Record properties
	 */

	function get_category_by_name($name)
	{
		$this->query("SELECT * FROM no_categories WHERE name='".$this->escape($name)."'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}


	/**
	 * Gets all Categories
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_categories($sortfield='id', $sortorder='ASC', $start=0, $offset=0)
	{
		$sql = "SELECT * FROM no_categories ORDER BY ".$this->escape($sortfield." ".$sortorder);

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}
	
	
	/**
	 * Gets all Categories where the user has access for
	 *
	 * @param String $auth_type Can be 'read' or 'write' to fetch readable or writable Categories
	 * @param Int $user_id First record of the total record set to return
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	 
	function get_authorized_categories($auth_type, $user_id, $query, $sort='name', $direction='ASC', $start=0, $offset=0)
	{
		$user_id=$this->escape($user_id);
		
		$sql = "SELECT DISTINCT no_categories.* FROM no_categories ".
 		"INNER JOIN go_acl a ON ";
		
		switch($auth_type)
		{
			case 'read':
				$sql .= "(no_categories.acl_read = a.acl_id OR no_categories.acl_write = a.acl_id) ";	
				break;
				
			case 'write':
				$sql .= "no_categories.acl_write = a.acl_id ";
				break;
		}
		
		
 		$sql .= "LEFT JOIN go_users_groups ug ON (a.group_id = ug.group_id) WHERE ((".
 		"ug.user_id = ".$user_id.") OR (a.user_id = ".$user_id.")) ";
 		
 		if(!empty($query))
 		{
 			$sql .= " AND name LIKE '".$this->escape($query)."'";
 		}

		$sql .= " ORDER BY ".$this->escape($sort." ".$direction);
		
		$this->query($sql);
		$count = $this->num_rows();
		
		

		if ($offset > 0)
		{
			$sql .=" LIMIT ".$this->escape($start.",".$offset);
			
			go_log(LOG_DEBUG, $sql);

			$this->query($sql);
			return $count;

		}else
		{
			return $count;
		}
	}	
		
	/**
	 * Add a Note
	 *
	 * @param Array $note Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_note($note)
	{		
		$note['ctime']=$note['mtime']=time();		
		
		$note['id']=$this->nextid('no_notes');
		if($this->insert_row('no_notes', $note))
		{
			$this->cache_note($note['id']);
			
			return $note['id'];
		}
		return false;
	}

	/**
	 * Update a Note
	 *
	 * @param Array $note Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_note($note)
	{		
		$note['mtime']=time();
		
		$r = $this->update_row('no_notes', 'id', $note);
		
		$this->cache_note($note['id']);
		
		return $r;
	}


	/**
	 * Delete a Note
	 *
	 * @param Int $note_id ID of the note
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_note($note_id)
	{
		
		global $GO_CONFIG;
		
		require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
		$search = new search();
		$search->delete_search_result($note_id, 4);
				
		
		return $this->query("DELETE FROM no_notes WHERE id=".$this->escape($note_id));
	}


	/**
	 * Gets a Note record
	 *
	 * @param Int $note_id ID of the note
	 *
	 * @access public
	 * @return Array Record properties
	 */

	function get_note($note_id)
	{
		$this->query("SELECT * FROM no_notes WHERE id=".$this->escape($note_id));
		if($this->next_record())
		{
			return $this->record;
		}else
		{
			throw new DatabaseSelectException();
		}
	}

	/**
	 * Gets a Note record by the name field
	 *
	 * @param String $name Name of the note
	 *
	 * @access public
	 * @return Array Record properties
	 */

	function get_note_by_name($name)
	{
		$this->query("SELECT * FROM no_notes WHERE name='".$this->escape($name)."'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}


	/**
	 * Gets all Notes
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_notes($query, $category_id, $sortfield='id', $sortorder='ASC', $start=0, $offset=0)
	{
		$sql = "SELECT n.* FROM no_notes n";
		
		if($category_id>0)
		{
			 $sql .= " WHERE n.category_id=".$this->escape($category_id);
		}else
		{
			global $GO_SECURITY;
			
			$sql .= " INNER JOIN no_categories c ON n.category_id=c.id ".
 				"INNER JOIN go_acl a ON (c.acl_read = a.acl_id OR c.acl_write = a.acl_id) ".	
				"LEFT JOIN go_users_groups ug ON (a.group_id = ug.group_id) WHERE ((".
 				"ug.user_id = ".$GO_SECURITY->user_id.") OR (a.user_id = ".$GO_SECURITY->user_id."))";
		}

		if(!empty($query))
		{
			//$sql .= " AND (n.name LIKE '".$this->escape($query)."' OR MATCH (n.content) AGAINST ('".$this->escape($query)."')) ";
			$sql .= " AND (n.name LIKE '".$this->escape($query)."' OR n.content LIKE '".$this->escape($query)."') ";
		}
		$sql .= " ORDER BY n.".$this->escape($sortfield." ".$sortorder);
		
		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}
	
	

	
	/* {CLASSFUNCTIONS} */
	
	
	/**
	 * When a an item gets deleted in a panel with links. Group-Office attempts
	 * to delete the item by finding the associated module class and this function
	 *
	 * @param int $id The id of the linked item
	 * @param int $link_type The link type of the item. See /classes/base/links.class.inc
	 */
	
	function __on_delete_link($id, $link_type)
	{		
		
		if($link_type==4)
		{
			$this->delete_note($id);
		}
		
		/* {ON_DELETE_LINK_FUNCTION} */	
	}
	
	/**
	 * This function is called when a user is deleted	
	 *
	 * @param int $user_id
	 */
	 
	public function __on_user_delete($user)
	{
		
	}
	
	public function __on_add_user($params)
	{
		global $GO_SECURITY;
		
		$user = $params['user'];
		
		if(!empty($user['first_name']) && !empty($user['last_name']))
		{			
			$category['name']=String::format_name($user);
			$category['user_id']=$user['id'];
			$category['acl_read']=$GO_SECURITY->get_new_acl('category',$user['id']);
			$category['acl_write']=$GO_SECURITY->get_new_acl('category',$user['id']);
			
			$this->add_category($category);
		}
	}
	
	/**
	 * When a global search action is performed this function will be called for each module
	 *
	 * @param int $last_sync_time The time this function was called last
	 */
	
	private function cache_note($note_id)
	{
		global $GO_CONFIG, $GO_LANGUAGE;
		
		require_once($GO_CONFIG->class_path.'/base/search.class.inc.php');
		$search = new search();
		
		require($GO_LANGUAGE->get_language_file('notes'));
		
		$sql = "SELECT i.*,r.acl_read,r.acl_write FROM no_notes i ".
			"INNER JOIN no_categories r ON r.id=i.category_id WHERE i.id=?";
		
		$this->query($sql, 'i', $note_id);
		$record = $this->next_record();
		if($record)
		{		
			$cache['id']=$this->f('id');
			$cache['user_id']=$this->f('user_id');
			$cache['module']='notes';
			$cache['name'] = $this->f('name');
			$cache['link_type']=4;
			$cache['description']='';			
			$cache['type']=$lang['notes']['note'];
			$cache['keywords']=$search->record_to_keywords($this->record).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');
			$cache['acl_read']=$this->f('acl_read');
 			$cache['acl_write']=$this->f('acl_write');	
 			
			$search->cache_search_result($cache);
		}

	}
	
	/**
	 * When a global search action is performed this function will be called for each module
	 *
	 * @param int $last_sync_time The time this function was called last
	 */

	public function __on_build_search_index()
	{
		$sql = "SELECT id FROM no_notes";
		$this->query($sql);	
		
		$notes = new notes();
		while($record=$this->next_record())
		{
			$notes->cache_note($record['id']);
		}

		/* {ON_BUILD_SEARCH_INDEX_FUNCTION} */
	}
	
	function __on_check_database(){
		/*global $GO_CONFIG, $GO_MODULES, $GO_LANGUAGE;
		
		echo 'Checking notes folder permissions<br />';

		if(isset($GO_MODULES->modules['files']))
		{
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$fs = new files();

			$sql = "SELECT e.name,e.id, c.acl_read, c.acl_write, c.user_id FROM no_notes e INNER JOIN no_categories c ON c.id=e.category_id";
			$this->query($sql);
			while($this->next_record())
			{
				echo 'Checking '.$this->f('name').'<br />';				
				$full_path = $GO_CONFIG->file_storage_path.'notes/'.$this->f('id');
				$fs->check_share($full_path, $this->f('user_id'), $this->f('acl_read'), $this->f('acl_write'));
			}
		}
		echo 'Done<br /><br />';*/
	}
	
}