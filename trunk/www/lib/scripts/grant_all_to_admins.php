<?php
/**
 * @copyright Copyright &copy; Intermesh 2006
 * @version $Revision: 1.4 $ $Date: 2006/04/24 08:07:16 $
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
   
   
 * This script will make sure admins and owners have access to all items.


 * @package Framework
 * @subpackage Scripts
 */

require('../../Group-Office.php');

if($GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
{
	$db = new db();
	$db->Halt_On_Error = 'no';

	$sql = "SELECT * FROM acl_items";

	$db->query($sql);
	while($db->next_record())
	{
		echo 'Processing '.$db->f('id').'<br />';
		if(!$GO_SECURITY->group_in_acl($GO_CONFIG->group_root, $db->f('id')))
		{
			echo 'Adding admin group to '.$db->f('id').'<br />';
			$GO_SECURITY->add_group_to_acl($GO_CONFIG->group_root, $db->f('id'));
		}
		if(!$GO_SECURITY->user_in_acl($db->f('user_id'), $db->f('id')))
		{
			echo 'Adding owner to '.$db->f('id').'<br />';
			$GO_SECURITY->add_user_to_acl($db->f('user_id'), $db->f('id'));
		}	
	}
}else
{
	echo 'Please log in as administrator to use this script';
}

?>
