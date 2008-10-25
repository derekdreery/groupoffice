		
		<gotpl if="$authenticate_relation">
		$sql = "SELECT i.*,r.acl_read,r.acl_write FROM {prefix}_{friendly_multiple} i INNER JOIN {prefix}_{related_friendly_multiple} r ON r.id=i.{related_field_id} WHERE mtime>".$this->escape($last_sync_time);
		</gotpl>
		<gotpl if="$authenticate">
		$sql = "SELECT * FROM {prefix}_{friendly_multiple} WHERE mtime>".$this->escape($last_sync_time);
		</gotpl>

		$this->query($sql);

		$search = new search();
		while($record = $this->next_record())
		{
			$cache['id']=$record['id'];
			$cache['user_id']=$record['user_id'];
			$cache['module']='{module}';
			$cache['name'] = $record['name'];
			$cache['link_type']={link_type};
			$cache['description']='';			
			$cache['type']=$lang['{module}']['{friendly_single}'];
			$cache['keywords']=$search->record_to_keywords(record).','.$cache['type'];
			$cache['mtime']=$record['mtime'];
			$cache['acl_read']=$record['acl_read'];
 			$cache['acl_write']=$record['acl_write'];	
			$search->cache_search_result($cache);
		}
		
		