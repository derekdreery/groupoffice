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
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('tools',true);

require($GO_THEME->theme_path.'header.inc');


$db1 = new db();
$db2 = new db();

$db1->query('SHOW TABLES');

while($db1->next_record())
{
	$db2->query("SHOW FIELDS FROM `".$db1->f(0)."`;");
	while($db2->next_record(MYSQL_ASSOC))
	{
		foreach($db2->Record as $key=>$value)
		{
			echo '$tables[\''.$db1->f(0).'\'][\'fields\'][][\''.$key.'\']=\''.$value.'\';<br />';
		}
		echo '<br />';
	}
}


load_basic_controls();
$button = new button($cmdClose, "javascript:document.location='index.php';");
echo $button->get_html();
require($GO_THEME->theme_path.'footer.inc');