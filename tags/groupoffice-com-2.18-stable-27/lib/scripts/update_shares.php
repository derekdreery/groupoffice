<?php
/**
 * @copyright Copyright &copy; Intermesh 2006
 * @version $Revision: 1.1 $ $Date: 2006/09/14 11:03:18 $
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
   
   
 * This script will convert the old activities list to the new links system. This should be done
 * by the update process already but due to some bugs in early 2.15 versions this may
 * have failed. This script will correct that.


 * @package Framework
 * @subpackage Scripts
 */

require('../../Group-Office.php');

/*
 *	1=cal_events
 * 2=ab_contacts
 * 3=ab_companies
 * 4=no_notes
 * 5=pmProjects
 * 6=folders
 * 7=bs_orders
*/

$old_path='/var/www/intermesh.nl';
$new_path='/var/www/intermesh.group-office.com';


if($GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
{
	$db1 = new db();
	$db2 = new db();
	
	$sql = "SELECT * FROM fs_shares";
	$db1->query($sql);
	while($db1->next_record())
	{
		$path = str_replace($old_path, $new_path, $db1->f('path'));
		
		$sql = "UPDATE fs_shares SET path='".addslashes($path)."' WHERE path='".addslashes($db1->f('path'))."'";
		$db2->query($sql);
	}
	
	echo 'All done!';
}else
{
	echo 'Please log in as administrator to use this script';
}
