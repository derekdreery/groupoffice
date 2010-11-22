		
		${module}1 = new {module}();
		
		$sql = "SELECT id FROM {prefix}_{friendly_multiple}";		
		${module}1->query($sql);
		
		${module}2 = new {module}();
		while($record = ${module}1->next_record())
		{
			${module}2->cache_{friendly_single}($record['id']);
		}
		
		