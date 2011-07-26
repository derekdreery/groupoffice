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

		if(isset($GLOBALS['GO_MODULES']->modules['files']))
		{
			$notes = new notes();
			$db = new db();

			require_once($GLOBALS['GO_MODULES']->modules['files']['class_path'].'files.class.inc.php');
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

		if(isset($GLOBALS['GO_MODULES']->modules['customfields'])){
			$db = new db();
			echo "Deleting non existing custom field records".$line_break.$line_break;
			$db->query("delete from cf_4 where link_id not in (select id from no_notes);");
		}
		echo 'Done'.$line_break.$line_break;

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
			$category['acl_id']=$GLOBALS['GO_SECURITY']->get_new_acl('category',$user['id']);
			
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
		
		require_once($GLOBALS['GO_CONFIG']->class_path.'/base/search.class.inc.php');
		$search = new search();
		
		require($GLOBALS['GO_LANGUAGE']->get_language_file('notes'));
		
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