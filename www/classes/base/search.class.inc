<?php
/**
 * @copyright Intermesh 2006
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.00 $ $Date: 2006/12/05 11:37:30 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

class search extends db {

	function search() {
		$this->db();
	}
	
	function global_search($user_id, $query, $start, $offset)
	{
		global $GO_MODULES;
		
		$xml = '<?xml version="1.0" ?>'."\n<results>";
		
		foreach($GO_MODULES->modules as $module)
		{
			if($module['id'] != 'search' && file_exists($module['class_path'].$module['id'].'.class.inc'))
			{
				require($module['class_path'].$module['id'].'.class.inc');
				$class = new $module['id'];
				
				if(method_exists($class, 'global_search'))
				{
					$xml .= $class->global_search($user_id, $query, $start, $offset);
				}
			}
		}

		return $xml.'</results>';	
	}
}
?>