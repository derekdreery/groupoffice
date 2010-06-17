<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/
class bookmarks extends db {


	function thumbdir_images() {
	$handler = opendir("icons");
	$images = array();

	while ($file = readdir($handler)) {
	    if ($file != '.' && $file != '..') {

			$image = new thumbimage();
			$image -> filename = $file;

			$images[] = $image;
		}
	}
	closedir($handler);

	return $images;

	}


	function get_usercats($user) {
		return $this->query("SELECT * FROM bm_categories WHERE user_id = ?", 'i', $user);
	}

 function get_one_bookmark($id) {
		$this->query("SELECT * FROM bm_bookmarks WHERE id=".$this->escape($id));
		return $this->next_record();
	}


	function get_bookmarks($category) {
		
		return $this->query("SELECT * FROM bm_bookmarks WHERE category_id = ?", 'i', $category);

	}

	function add_bookmark($bookmark) {
		$bookmark['id']=$this->nextid('bm_bookmarks');
		if($this->insert_row('bm_bookmarks', $bookmark)) {
			return $bookmark['id'];
		}
		return false;
	}


	function update_bookmark($bookmark) {
		return $this->update_row('bm_bookmarks', 'id', $bookmark);
	}

	function delete_bookmark($bookmark_id) {
		//var_dump($bookmark_id);
		return $this->query("DELETE FROM bm_bookmarks WHERE id=".$this->escape($bookmark_id));


	}



	function get_category($category_id) {
		$this->query("SELECT * FROM bm_categories WHERE id=".$this->escape($category_id));
		return $this->next_record();
	}

	function delete_category($category_id) {
		$category = $this->get_category($category_id);



		$bookmarks = new bookmarks();
		$this->query("SELECT * FROM bm_bookmarks WHERE category_id=".$this->escape($category_id));
		while($bookmark = $this->next_record()) {
			$bookmarks->delete_bookmark($bookmark['id']);
		}

		global $GO_SECURITY;
		$GO_SECURITY->delete_acl($category['acl_id']);

		return $this->query("DELETE FROM bm_categories WHERE id=".$this->escape($category_id));
	}

	function get_authorized_bookmarks($user_id,$query,$start=0,$offset=0,$category=0) {

			$sql = "SELECT ";
		if($offset>0)
		{
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}
		$sql .= " DISTINCT c.name AS category_name, c.acl_id, b.*
           FROM bm_categories c
           INNER JOIN bm_bookmarks b ON c.id = b.category_id
					 LEFT JOIN go_acl a ON a.acl_id = c.acl_id					 
           LEFT JOIN go_users_groups ug ON ( a.group_id = ug.group_id ) ";
	
		$sql .= "WHERE
					 (c.user_id= ".$user_id. "
					 OR ug.user_id =  ".$user_id. "
					 OR a.user_id =  ".$user_id. ")";

		if ($category>0) $sql .= "AND c.id= ".$category;

		if(!empty($query))
 		{
 			$sql .= " AND b.name LIKE '".$this->escape($query)."'";
 		}

		$sql .= " ORDER BY category_name ASC , b.name ASC";
		     


					
//	 $this->query($sql);

//	return $this->num_rows();

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

	function get_authorized_categories($auth_type, $user_id, $query, $sort='name', $direction='ASC', $start=0, $offset=0) {
		$user_id=$this->escape($user_id);


		$sql = "SELECT DISTINCT bm_categories.* FROM bm_categories ".

						"LEFT JOIN go_acl a ON ";

		switch($auth_type) {
			case 'read':
				$sql .= "(bm_categories.acl_id = a.acl_id)";
				break;

			case 'write':
				$sql .= "(bm_categories.acl_id = a.acl_id AND a.level>1)" ;
				break;
		}


		$sql .= "LEFT JOIN go_users_groups ug ON (a.group_id = ug.group_id) ".
						"WHERE bm_categories.user_id = ".$user_id. "
			OR ug.user_id = ".$user_id. "
	  	OR a.user_id = ".$user_id;


		if(!empty($query)) {
			$sql .= " AND name LIKE '".$this->escape($query)."'";
		}



		$sql .= " ORDER BY ".$this->escape($sort." ".$direction);


		$this->query($sql);

		$count = $this->num_rows();


		if ($offset > 0) {
			$sql .=" LIMIT ".$this->escape($start.",".$offset);

			go_log(LOG_DEBUG, $sql);

			$this->query($sql);
			return $count;

		}else {

			return $count;
		}
	}

	function add_category($category) {
		$category['id']=$this->nextid('bm_categories');
		if($this->insert_row('bm_categories', $category)) {
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

	function update_category($category, $old_category) {

		global $GO_SECURITY;
		//user id of the category changed. Change the owner of the ACL as well
		if(isset($category['user_id']) && $old_category['user_id'] != $category['user_id']) {
			$GO_SECURITY->chown_acl($old_category['acl_id'], $category['user_id']);
		}

		return $this->update_row('bm_categories', 'id', $category);
	}



function bm_thumb_url($path, $w=100,$h=100,$zc=1) {
		global $GO_THEME, $GO_CONFIG;

		$extension = File::get_extension($path);

		switch($extension) {
			case 'jpg':
			case 'jpeg';
			case 'png';
			case 'gif';
		
			
			return'modules/bookmarks/bmthumb.php?src='.$path.'&w='.$w.'&h='.$h.'&zc='.$zc;
				break;

			default:
				if(file_exists($GO_THEME->theme_path.'images/128x128/filetypes/'.$extension.'.png')) {
					return $GO_THEME->image_url.'128x128/filetypes/'.$extension.'.png';
				}else {
					return $GO_THEME->image_url.'128x128/filetypes/unknown.png';
				}
				break;

		}
}



}

?>
