<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: class.tpl 2255 2008-07-02 11:47:50Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

class {module} extends db {

	public function __construct() {
		$this->db();
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
	
	/**
	 * When a global search action is performed this function will be called for each module
	 *
	 * @param int $last_sync_time The time this function was called last
	 */
	
	public function __on_search($last_sync_time=0)
	{
		global $GO_MODULES, $GO_LANGUAGE;
		
		require($GO_LANGUAGE->get_language_file('{module}'));
		
		/* {ON_SEARCH_FUNCTION} */
	}
	
}