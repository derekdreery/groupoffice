	
	/**
	 * Add a {friendly_single_ucfirst}
	 *
	 * @param Array ${friendly_single} Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_{friendly_single}(${friendly_single})
	{
		<gotpl if="$mtime">
		${friendly_single}['ctime']=${friendly_single}['mtime']=time();
		</gotpl>
		
		${friendly_single}['id']=$this->nextid('{prefix}_{friendly_multiple}');
		if($this->insert_row('{prefix}_{friendly_multiple}', ${friendly_single}))
		{
			<gotpl if="$link_type &gt; 0">
			$this->cache_{friendly_single}(${friendly_single}['id']);
			</gotpl>
			
			return ${friendly_single}['id'];
		}
		return false;
	}

	/**
	 * Update a {friendly_single_ucfirst}
	 *
	 * @param Array ${friendly_single} Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_{friendly_single}(${friendly_single})
	{
		<gotpl if="$mtime">
		${friendly_single}['mtime']=time();
		</gotpl>
		$r = $this->update_row('{prefix}_{friendly_multiple}', 'id', ${friendly_single});
		
		<gotpl if="$link_type &gt; 0">
		$this->cache_{friendly_single}(${friendly_single}['id']);
		</gotpl>
		
		return $r;
	}
	
	<gotpl if="$link_type &gt; 0">/**
	 * Adds or updates a note in the search cache table
	 *
	 * @param int $note_id
	 */
	
	private function cache_{friendly_single}(${friendly_single}_id)
	{
		global $GO_CONFIG, $GO_LANGUAGE;
		
		require_once($GLOBALS['GO_CONFIG']->class_path.'/base/search.class.inc.php');
		$search = new search();
		
		require($GLOBALS['GO_LANGUAGE']->get_language_file('{module}'));
		
		<gotpl if="$authenticate_relation">
		$sql = "SELECT i.*,r.acl_id FROM {prefix}_{friendly_multiple} i INNER JOIN {prefix}_{related_friendly_multiple} r ON r.id=i.{related_field_id} WHERE i.id=?";
		</gotpl>
		<gotpl if="$authenticate">
		$sql = "SELECT * FROM {prefix}_{friendly_multiple} i WHERE i.id=?";
		</gotpl>
		$this->query($sql, 'i', ${friendly_single}_id);
		$record = $this->next_record();
		
		if($record)
		{		
			$cache['id']=$record['id'];
			$cache['user_id']=$record['user_id'];
			$cache['module']='{module}';
			$cache['name'] = htmlspecialchars($record['name'], ENT_QUOTES, 'utf-8');
			$cache['link_type']={link_type};
			$cache['description']='';			
			$cache['type']=$lang['{module}']['{friendly_single}'];
			$cache['keywords']=$search->record_to_keywords($record).','.$cache['type'];
			$cache['mtime']=$record['mtime'];
			$cache['acl_id']=$record['acl_id'];

	 		$search->cache_search_result($cache);
		}
	}

	</gotpl>
	/**
	 * Delete a {friendly_single_ucfirst}
	 *
	 * @param Int ${friendly_single}_id ID of the {friendly_single}
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_{friendly_single}(${friendly_single}_id)
	{
		<gotpl if="$link_type &gt; 0">
		global $GO_CONFIG;
		
		require_once($GLOBALS['GO_CONFIG']->class_path.'base/search.class.inc.php');
		$search = new search();
		$search->delete_search_result(${friendly_single}_id, {link_type});
		
		require_once($GLOBALS['GO_CONFIG']->class_path.'filesystem.class.inc');
		$fs = new filesystem();
		if(file_exists($GLOBALS['GO_CONFIG']->file_storage_path.'{friendly_multiple}/'.${friendly_single}_id.'/'))
		{
			$fs->delete($GLOBALS['GO_CONFIG']->file_storage_path.'{friendly_multiple}/'.${friendly_single}_id.'/');
		}
				
		</gotpl>		
		
		return $this->query("DELETE FROM {prefix}_{friendly_multiple} WHERE id=?", 'i', ${friendly_single}_id);
	}


	/**
	 * Gets a {friendly_single_ucfirst} record
	 *
	 * @param Int ${friendly_single}_id ID of the {friendly_single}
	 *
	 * @access public
	 * @return Array Record properties
	 */

	function get_{friendly_single}(${friendly_single}_id)
	{
		$this->query("SELECT * FROM {prefix}_{friendly_multiple} WHERE id=?", 'i', ${friendly_single}_id);
		return $this->next_record();		
	}

	/**
	 * Gets a {friendly_single_ucfirst} record by the name field
	 *
	 * @param String $name Name of the {friendly_single}
	 *
	 * @access public
	 * @return Array Record properties
	 */

	function get_{friendly_single}_by_name($name)
	{
		$this->query("SELECT * FROM {prefix}_{friendly_multiple} WHERE name=?", 's', $name);
		return $this->next_record();		
	}


	/**
	 * Gets all {friendly_multiple_ucfirst}
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_{friendly_multiple}(<gotpl if="$relation">${related_field_id}, </gotpl>$query='', $sortfield='id', $sortorder='ASC', $start=0, $offset=0)
	{
		$sql = "SELECT ";		
		if($offset>0)
		{
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}		
		$sql .= "* FROM {prefix}_{friendly_multiple} <gotpl if="$relation">WHERE {related_field_id}=?</gotpl>";
		
		$types='';
		$params=array();<gotpl if="$relation">
		$types .= 'i';
		$params[]=${related_field_id};</gotpl>
		
		if(!empty($query))
 		{
 			$sql .= " <gotpl if="$relation">AND</gotpl><gotpl if="!$relation">WHERE</gotpl> name LIKE ?";
 			
 			$types .= 's';
 			$params[]=$query;
 		} 		
		
		$sql .= " ORDER BY ".$this->escape($sortfield.' '.$sortorder);	

		if($offset>0)
		{
			$sql .= " LIMIT ".intval($start).",".intval($offset);
		}
		
		$this->query($sql, $types, $params);
		return $offset>0 ? $this->found_rows() : $this->num_rows();
	}
	
	<gotpl if="$authenticate">
	/**
	 * Gets all {friendly_multiple_ucfirst} where the user has access for
	 *
	 * @param String $auth_type Can be 'read' or 'write' to fetch readable or writable {friendly_multiple_ucfirst}
	 * @param Int $user_id First record of the total record set to return</gotpl><gotpl if="$authenticate && $relation">${related_field_id}, 
	 * @param Int ${related_field_id}</gotpl><gotpl if="$authenticate">
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	 
	function get_authorized_{friendly_multiple}($auth_type, $user_id, <gotpl if="$relation">${related_field_id}, </gotpl>$query, $sortfield='name', $sortorder='ASC', $start=0, $offset=0)
	{		
		$sql = "SELECT ";		
		if($offset>0)
		{
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}		
		$sql .= "DISTINCT {prefix}_{friendly_multiple}.* FROM {prefix}_{friendly_multiple} ".
 		"INNER JOIN go_acl a ON ";
		
		switch($auth_type)
		{
			case 'read':
				$sql .= "{prefix}_{friendly_multiple}.acl_id = a.acl_id ";
				break;
				
			case 'write':
				$sql .= "({prefix}_{friendly_multiple}.acl_id = a.acl_id AND a.level>".GO_SECURITY::READ_PERMISSION.") ";
				break;
		}		
		
 		$sql .= "LEFT JOIN go_users_groups ug ON (a.group_id = ug.group_id) WHERE ((".
 		"ug.user_id = ?) OR (a.user_id = ?)) <gotpl if="$relation">AND {related_field_id}=?</gotpl>";
 		
 		$types='ii';
 		$params=array($user_id, $user_id); 		
 		<gotpl if="$relation">
		$types .= 'i';
		$params[]=${related_field_id};</gotpl>
 		
 		if(!empty($query))
 		{
 			$sql .= " AND name LIKE ?";
 			
 			$types .= 's';
 			$params[]=$query;
 		} 		

		$sql .= " ORDER BY ".$this->escape($sortfield.' '.$sortorder);	

		if($offset>0)
		{
			$sql .= " LIMIT ".intval($start).",".intval($offset);
		}
		
		return $this->query($sql, $types, $params);
	}
	</gotpl>

	
	