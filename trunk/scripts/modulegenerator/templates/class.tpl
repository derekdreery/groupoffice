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

	public function __on_load_listeners($events){
		$events->add_listener('on_user_delete', __FILE__, '{module}', 'user_delete');
		$events->add_listener('on_build_search_index', __FILE__, '{module}', 'build_search_index');
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
	 
	public function on_user_delete($user)
	{
		
	}
	
	/**
	 * When a database check is performed this function will be called for each module
	 * it will rebuild the search results.
	 */
	
	public function on_build_search_index()
	{				
/* {ON_BUILD_SEARCH_INDEX_FUNCTION} */
	}
	
}