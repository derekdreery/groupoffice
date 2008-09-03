		
		<gotpl if="$authenticate_relation">
		$sql = "SELECT i.*,r.acl_read,r.acl_write FROM {prefix}_{friendly_multiple} i INNER JOIN {prefix}_{related_friendly_multiple} r ON r.id=i.{related_field_id} WHERE mtime>$last_sync_time";
		</gotpl>
		<gotpl if="$authenticate">
		$sql = "SELECT * FROM {prefix}_{friendly_multiple} WHERE mtime>$last_sync_time";
		</gotpl>

		$this->query($sql);

		$search = new search();
		while($this->next_record())
		{
			$cache['id']=$this->f('id');
			$cache['user_id']=$this->f('user_id');
			$cache['module']='{module}';
			$cache['name'] = addslashes($this->f('name'));
			$cache['link_type']={link_type};
			$cache['description']='';			
			$cache['type']=addslashes($lang['{module}']['{friendly_single}']);
			$cache['keywords']=addslashes($search->record_to_keywords($this->Record)).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');
			$cache['acl_read']=$this->f('acl_read');
 			$cache['acl_write']=$this->f('acl_write');	
			$search->cache_search_result($cache);
		}
		
		