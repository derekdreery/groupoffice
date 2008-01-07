<?php
/**
 * @copyright Copyright &copy; Intermesh 2006
 * @version $Revision: 1.1 $ $Date: 2006/04/24 08:07:16 $
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
   
   
 * This script will generate a new ACL id to use in the database somewhere.
 * Use only if you know what you are doing.


 * @package Framework
 * @subpackage Scripts
 */

require('../../Group-Office.php');

if($GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
{
	echo $GO_SECURITY->get_new_acl();
}else
{
	echo 'Please log in as administrator to use this script';
}
