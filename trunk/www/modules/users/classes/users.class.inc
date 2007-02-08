<?php
/**
 * @copyright Copyright &copy; Intermesh 2003
 * @version $Revision: 1.44 $ $Date: 2006/11/14 12:56:56 $
 *
 * @author Merijn Schering <mschering@intermesh.nl>

   This file is part of Group-Office.

   Group-Office is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Group-Office is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Group-Office; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 * @package Users

 */

class users extends db
{
	function __on_search($user_id,$last_sync_time=0)
	{
		global $GO_MODULES, $GO_LANGUAGE, $GO_USERS;

		//require($GO_LANGUAGE->get_language_file('addressbook'));
		
		$sql = "SELECT DISTINCT users.*  FROM users  INNER JOIN acl ON users.acl_id = acl.acl_id ".
			"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id WHERE (acl.user_id=$user_id ".
			"OR users_groups.user_id=$user_id) AND mtime>$last_sync_time AND registration_time>$last_sync_time";

		$this->query($sql);

		$db = new db();
		while($this->next_record())
		{
			$cache['table']='users';
			$cache['id']=$this->f('id');
			$cache['user_id']=$user_id;
			$cache['name'] = addslashes(format_name($this->f('last_name'),$this->f('first_name'),$this->f('middle_name')));
			$cache['link_id'] = $this->f('link_id');
			$cache['link_type']=8;
			$cache['description']='';
			$cache['url']=$GO_MODULES->modules['users']['url'].'user.php?user_id='.$this->f('id');
			$cache['type']=$GLOBALS['strUser'];
			$cache['keywords']=addslashes(record_to_keywords($this->Record)).','.$cache['type'];
			if($this->f('registration_time')>$last_sync_time)
			{
				$db->insert_row('se_cache',$cache);
			}else {
				$db->update_row('se_cache',array('user_id','link_id'), $cache);
			}
		}
	}
}