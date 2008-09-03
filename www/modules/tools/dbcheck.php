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


 * @package Tools
 * @subpackage DB check
 */

require_once("../../Group-Office.php");
//$GO_SECURITY->html_authenticate('tools');



$db2 = new db();
$db3 = new db();

$db = new db();
$db->Halt_On_Error = 'no';




echo 'Checking ACL...<br />';

$sql = "SELECT * FROM go_acl_items";
$db->query($sql);
while($db->next_record())
{
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
echo 'Done<br /><br />';

echo 'Resetting DB sequence...<br />';

$db->query("SHOW TABLES");

$tables = array();

while($db->next_record(MYSQL_BOTH))
{
	if($db->f(0) != 'go_db_sequence')
	{
		$db2->query("SHOW FIELDS FROM `".$db->f(0)."`");
		while($db2->next_record())
		{
			if($db2->f('Field')=='id')
			{
				$tables[]=$db->f(0);
				break;
			}
		}
	}
}

foreach($tables as $table)
{
	$max=0;
	$sql = "SELECT max(id) FROM `$table`";
	$db->query($sql);
	$db->next_record(MYSQL_BOTH);
	$max = $db->f(0);
//echo $table.':'.$max.'<br />';	
	if(!empty($max))
	{
		$sql = "REPLACE INTO go_db_sequence VALUES ('$table', '$max');";
		$db->query($sql);

		echo 'Setting '.$table.'='.$max.'<br />';
	}
}
echo 'Done<br /><br />';

echo 'Optimizing tables<br />';

$db->query("SHOW TABLES");

$tables = array();

while($db->next_record(MYSQL_BOTH))
{
	echo 'Optimizing: '.$db->f(0).'<br />';
	$db2->query('OPTIMIZE TABLE `'.$db->f(0).'`');
}
echo 'Done<br /><br />';



echo 'Clearing search cache<br />';

require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
$search = new search();
$search->reset();
flush();

echo 'Building search cache<br />';

$search->update_search_cache(true);

echo 'Done<br /><br />';

echo 'Start of module checks<br />';

$GO_MODULES->fire_event('check_database');

echo 'All Done!<br />';

