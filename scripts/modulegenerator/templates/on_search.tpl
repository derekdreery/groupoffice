		
		$sql = "SELECT id FROM {prefix}_{friendly_multiple}";		
		${module} = new {module}();
		while($record = $this->next_record())
		{
			${module}->cache_{friendly_single}($record['id']);
		}
		
		