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
	
	public function __on_load_listeners($events){
		$events->add_listener('user_delete', __FILE__, 'notes', 'user_delete');
		$events->add_listener('add_user', __FILE__, 'notes', 'add_user');
		$events->add_listener('build_search_index', __FILE__, 'notes', 'build_search_index');
		$events->add_listener('check_database', __FILE__, 'notes', 'check_database');
	}
	
	public static function check_database(){
		global $GO_CONFIG, $GO_MODULES, $GO_LANGUAGE;

		$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";

		echo 'Note folders'.$line_break;

		if(isset($GO_MODULES->modules['files']))
		{
			$notes = new notes();
			$db = new db();

			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();

			$sql = "SELECT * FROM no_categories";
			$db->query($sql);
			while($category = $db->next_record())
			{
				try{
					$files->check_share('notes/'.$category['name'], $category['user_id'], $category['acl_id'], false);
				}
				catch(Exception $e){
					echo $e->getMessage().$line_break;
				}
			}

			$db->query("SELECT c.*,a.name AS category_name,a.acl_id FROM no_notes c INNER JOIN no_categories a ON a.id=c.category_id");
			while($note = $db->next_record())
			{
				try{
					$path = $notes->build_note_files_path($note, array('name'=>$note['category_name']));
					$up_note['files_folder_id']=$files->check_folder_location($note['files_folder_id'], $path);
	
					if($up_note['files_folder_id']!=$note['files_folder_id']){
						$up_note['id']=$note['id'];
						$notes->update_row('no_notes', 'id', $up_note);
					}
					$files->set_readonly($up_note['files_folder_id']);
				}
				catch(Exception $e){
					echo $e->getMessage().$line_break;
				}
			}
		}

		if(isset($GO_MODULES->modules['customfields'])){
			$db = new db();
			echo "Deleting non existing custom field records".$line_break.$line_break;
			$db->query("delete from cf_4 where link_id not in (select id from no_notes);");
		}
		echo 'Done'.$line_break.$line_break;

	}
		
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
		
		global $GO_MODULES;
		if(isset($GO_MODULES->modules['files']))
		{
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();			
			$files->check_share('notes/'.File::strip_invalid_chars($category['name']),$category['user_id'], $category['acl_id']);
		}		
			
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

	function update_category($category, $old_category)
	{		
		global $GO_MODULES;
		if(isset($GO_MODULES->modules['files']) && $category['name']!=$old_category['name'])
		{
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();			
			$files->move_by_paths('notes/'.File::strip_invalid_chars($old_category['name']), 'notes/'.File::strip_invalid_chars($category['name']));
		}
		
		global $GO_SECURITY;
		//user id of the category changed. Change the owner of the ACL as well
		if(isset($category['user_id']) && $old_category['user_id'] != $category['user_id'])
		{
			$GO_SECURITY->chown_acl($old_category['acl_id'], $category['user_id']);
		}
		
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
		$category = $this->get_category($category_id);
		
		global $GO_MODULES;
		if(isset($GO_MODULES->modules['files']))
		{
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();
			
			$folder = $files->resolve_path('notes/'.File::strip_invalid_chars($category['name']));			
			if($folder){
				$files->delete_folder($folder);
			}
		}	
		
		$notes = new notes();
		$this->query("SELECT * FROM no_notes WHERE category_id=".intval($category_id));
		while($note = $this->next_record())
		{
			$notes->delete_note($note['id']);
		}

		global $GO_SECURITY;
		$GO_SECURITY->delete_acl($category['acl_id']);

		return $this->query("DELETE FROM no_categories WHERE id=".intval($category_id));
	}
	
	/**
	 * Add a Note
	 *
	 * @param Array $note Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_note($note, $category=false)
	{
		if(!isset($note['ctime']))
			$note['ctime']=time();
		
		if(!isset($note['mtime']))
			$note['mtime']=time();
		
		global $GO_MODULES;
		if(!isset($note['files_folder_id']) && isset($GO_MODULES->modules['files']))
		{
			if(!$category)
			{
				$category = $this->get_category($note['category_id']);				
			}
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();			

			$new_path = $this->build_note_files_path($note, $category);			
			if($folder=$files->create_unique_folder($new_path))
			{
				$note['files_folder_id']=$folder['id'];
			}
		}
		
		$note['id']=$this->nextid('no_notes');
		if($this->insert_row('no_notes', $note))
		{
			$this->cache_note($note['id']);
			
			return $note['id'];
		}
		return false;
	}
	
	function build_note_files_path($note, $category)
	{
		return 'notes/'.File::strip_invalid_chars($category['name']).'/'.date('Y', $note['ctime']).'/'.File::strip_invalid_chars($note['name']);
	}
	
	/**
	 * Update a Note
	 *
	 * @param Array $note Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_note($note, $category=false)
	{		
		if(!isset($note['mtime']))
			$note['mtime']=time();
		
		global $GO_MODULES;
		if(isset($GO_MODULES->modules['files']) && isset($note['category_id']))
		{			
			if(!$category)
			{
				$category = $this->get_category($note['category_id']);				
			}
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();			
			
			$old_note = $this->get_note($note['id']);
			$note['ctime']=$old_note['ctime'];
			$new_path = $this->build_note_files_path($note, $category);			
			$note['files_folder_id']=$files->check_folder_location($old_note['files_folder_id'], $new_path);			
		}
		
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
		
		global $GO_MODULES;
		if(isset($GO_MODULES->modules['files']))
		{
			$note = $this->get_note($note_id);
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();
			try{
				$files->delete_folder($note['files_folder_id']);
			}
			catch(Exception $e){}
		}				
		
		return $this->query("DELETE FROM no_notes WHERE id=".intval($note_id));
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
			$this->query("SELECT * FROM no_categories WHERE id=".intval($category_id));
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
				global $GO_CONFIG;

				require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
				$GO_USERS = new GO_USERS();

				$category['user_id']=$GO_SECURITY->user_id;
				$user = $GO_USERS->get_user($GO_SECURITY->user_id);
				$task_name = String::format_name($user['last_name'], $user['first_name'], $user['middle_name'], 'last_name');
				$category['name'] = $task_name;
				$category['acl_id']=$GO_SECURITY->get_new_acl();

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
		$sql = "SELECT * FROM no_categories WHERE user_id='".intval($user_id)."' LIMIT 0,1";
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
	function get_categories($sortfield='id', $sortorder='ASC', $start=0, $offset=0, $user_id=0)
	{
		$sql = "SELECT * FROM no_categories ";
		
		if($user_id>0)
		{
			$sql .= "WHERE user_id=$user_id ";
		}
		
		$sql .= "ORDER BY ".$this->escape($sortfield." ".$sortorder);

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT ".intval($start).",".intval($offset);
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
	 
	function get_authorized_categories($auth_type, $user_id, $query='', $sort='name', $direction='ASC', $start=0, $offset=0)
	{
		$user_id=intval($user_id);
		
		$sql = "SELECT no_categories.* FROM no_categories ".
		
		"INNER JOIN go_acl a ON (no_categories.acl_id = a.acl_id";
		if($auth_type=='write'){
			$sql .= " AND a.level>".GO_SECURITY::READ_PERMISSION;
		}
		$sql .= " AND (a.user_id=".intval($user_id)." OR a.group_id IN (".implode(',',$GLOBALS['GO_SECURITY']->get_user_group_ids($user_id))."))) ";
		
 		
 		if(!empty($query))
 		{
 			$sql .= " AND name LIKE '".$this->escape($query)."'";
 		}

		$sql .= " GROUP BY no_categories.id ORDER BY ".$this->escape($sort." ".$direction);
	
		$sql = $this->add_limits_to_query($sql, $start, $offset);
		$this->query($sql);

		return $this->limit_count();
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
		$this->query("SELECT * FROM no_notes WHERE id=".intval($note_id));
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

	function get_notes($query, $categories=array(), $sortfield='id', $sortorder='ASC', $start=0, $offset=0, $search_field='')
	{
		global $GO_MODULES;
		
		$sql = "SELECT n.*, c.name AS category_name";

		if($GO_MODULES->has_module('customfields')) {
			$sql .= " ,cf_4.*";
		}

		$sql .=  " FROM no_notes n";
		$sql .= " INNER JOIN no_categories c ON n.category_id=c.id";

		if($GO_MODULES->has_module('customfields')) {
			$sql .= " LEFT JOIN cf_4 ON cf_4.link_id=n.id";
		}
		
		if(count($categories))
		{
			 $sql .= " WHERE n.category_id IN (".implode(',', $categories).")";
		}else
		{
			global $GO_SECURITY;

			$sql .= " INNER JOIN go_acl a ON (c.acl_id = a.acl_id";
			$sql .= " AND (a.user_id=".$GO_SECURITY->user_id." OR a.group_id IN (".implode(',',$GLOBALS['GO_SECURITY']->get_user_group_ids($user_id))."))) ";
		}

		if(!empty($query))
		{
			//$sql .= " AND (n.name LIKE '".$this->escape($query)."' OR MATCH (n.content) AGAINST ('".$this->escape($query)."')) ";
			if(empty($search_field)){
				$sql .= " AND (n.name LIKE '".$this->escape($query)."' OR n.content LIKE '".$this->escape($query)."') ";
			}  else {
				$sql .= " AND $search_field LIKE '".$this->escape($query)."' ";
			}
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
	
	public static function user_delete($user)
	{
		global $GO_SECURITY;
		
		$notes = new notes();
		$notes2 = new notes();

		$notes->get_categories('id','ASC', 0,0, $user['id']);
		while($notes->next_record())
		{
			$notes2->delete_category($notes->f('id'));
		}	
	}

	
	public static function add_user($user)
	{
		global $GO_SECURITY;
		
		$notes = new notes();
		
		if(!empty($user['first_name']) && !empty($user['last_name']))
		{			
			$category['name']=String::format_name($user);
			$category['user_id']=$user['id'];
			$category['acl_id']=$GO_SECURITY->get_new_acl('category',$user['id']);
			
			$notes->add_category($category);
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
		
		$sql = "SELECT i.*,r.acl_id FROM no_notes i ".
			"INNER JOIN no_categories r ON r.id=i.category_id WHERE i.id=?";
		
		$this->query($sql, 'i', $note_id);
		$record = $this->next_record();
		if($record)
		{		
			$cache['id']=$this->f('id');
			$cache['user_id']=$this->f('user_id');
			$cache['module']='notes';
			$cache['name'] = htmlspecialchars($this->f('name'), ENT_QUOTES, 'utf-8');
			$cache['link_type']=4;
			$cache['description']='';			
			$cache['type']=$lang['notes']['note'];
			$cache['keywords']=$search->record_to_keywords($this->record).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');
			$cache['acl_id']=$this->f('acl_id');
 			
			$search->cache_search_result($cache);
		}

	}
	
	/**
	 * When a global search action is performed this function will be called for each module
	 *
	 * @param int $last_sync_time The time this function was called last
	 */

	public function build_search_index()
	{
		$notes = new notes();
		
		$sql = "SELECT id FROM no_notes";
		$notes->query($sql);	
		
		$notes2= new notes();
		while($record=$notes->next_record())
		{
			$notes2->cache_note($record['id']);
		}
	}
	
}