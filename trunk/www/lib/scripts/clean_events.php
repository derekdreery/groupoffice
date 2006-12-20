<?php
/**
 * @copyright Copyright &copy; Intermesh 2006
 * @version $Revision: 1.2 $ $Date: 2006/04/24 08:07:16 $
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
   
   
 * This script will delete all events that are not visible to anyone


 * @package Framework
 * @subpackage Scripts
 */

require('../../Group-Office.php');
$db = new db();
$db->Halt_On_Error = 'no';

require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
$cal = new calendar();

$sql = "SELECT cal_events.id
FROM cal_events
LEFT JOIN acl_items ON ( cal_events.acl_read = acl_items.id
OR cal_events.acl_write = acl_items.id )
WHERE acl_items.id IS NULL";

$db->query($sql);
while($db->next_record())
{
	$cal->delete_event($db->f('id'));
}
?>
