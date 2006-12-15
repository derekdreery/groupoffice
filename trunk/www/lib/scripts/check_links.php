<?php
/**
 * @copyright Copyright &copy; Intermesh 2006
 * @version $Revision: 1.1 $ $Date: 2006/04/27 07:08:45 $
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




if($GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
{
	$db1 = new db();
	$db2 = new db();

	$db1->query("SELECT * FROM links");
	
	while($db1->next_record())
	{
		switch($db1->f('type1'))
		{
			case '1':
				$sql = "SELECT id FROM cal_events WHERE link_id='".$db1->f('link_id1')."';";
				$db2->query($sql);
				if(!$db2->next_record())
				{
					$db2->query("DELETE FROM links WHERE type1='".$db1->f('type1')."' AND link_id1='".$db1->f('link_id1')."';");
					echo 'Deleting link_id '.$db1->f('link_id1').' with type '.$db1->f('type1').'<br />';
				}			
			break;
			
			case '2':
				$sql = "SELECT id FROM ab_contacts WHERE link_id='".$db1->f('link_id1')."';";
				$db2->query($sql);
				if(!$db2->next_record())
				{
					$db2->query("DELETE FROM links WHERE type1='".$db1->f('type1')."' AND link_id1='".$db1->f('link_id1')."';");
					echo 'Deleting link_id '.$db1->f('link_id1').' with type '.$db1->f('type1').'<br />';
				}			
			break;
			
			case '3':
				$sql = "SELECT id FROM pmProjects WHERE link_id='".$db1->f('link_id1')."';";
				$db2->query($sql);
				if(!$db2->next_record())
				{
					$db2->query("DELETE FROM links WHERE type1='".$db1->f('type1')."' AND link_id1='".$db1->f('link_id1')."';");
					echo 'Deleting link_id '.$db1->f('link_id1').' with type '.$db1->f('type1').'<br />';
				}			
			break;
			
			case '4':
				$sql = "SELECT id FROM no_notes WHERE link_id='".$db1->f('link_id1')."';";
				$db2->query($sql);
				if(!$db2->next_record())
				{
					$db2->query("DELETE FROM links WHERE type1='".$db1->f('type1')."' AND link_id1='".$db1->f('link_id1')."';");
					echo 'Deleting link_id '.$db1->f('link_id1').' with type '.$db1->f('type1').'<br />';
				}			
			break;
			
			case '5':
				$sql = "SELECT id FROM no_notes WHERE link_id='".$db1->f('link_id1')."';";
				$db2->query($sql);
				if(!$db2->next_record())
				{
					$db2->query("DELETE FROM links WHERE type1='".$db1->f('type1')."' AND link_id1='".$db1->f('link_id1')."';");
					echo 'Deleting link_id '.$db1->f('link_id1').' with type '.$db1->f('type1').'<br />';
				}			
			break;
			
			case '6':
				$sql = "SELECT path FROM fs_links WHERE link_id='".$db1->f('link_id1')."';";
				$db2->query($sql);
				if(!$db2->next_record())
				{
					$db2->query("DELETE FROM links WHERE type1='".$db1->f('type1')."' AND link_id1='".$db1->f('link_id1')."';");
					echo 'Deleting link_id '.$db1->f('link_id1').' with type '.$db1->f('type1').'<br />';
				}			
			break;
			
			case '7':
				$sql = "SELECT id FROM bs_orders WHERE link_id='".$db1->f('link_id1')."';";
				$db2->query($sql);
				if(!$db2->next_record())
				{
					$db2->query("DELETE FROM links WHERE type1='".$db1->f('type1')."' AND link_id1='".$db1->f('link_id1')."';");
					echo 'Deleting link_id '.$db1->f('link_id1').' with type '.$db1->f('type1').'<br />';
				}			
			break;
		}
		
		
		
		
		switch($db1->f('type2'))
		{
			case '1':
				$sql = "SELECT id FROM cal_events WHERE link_id='".$db1->f('link_id2')."';";
				$db2->query($sql);
				if(!$db2->next_record())
				{
					$db2->query("DELETE FROM links WHERE type2='".$db1->f('type2')."' AND link_id2='".$db1->f('link_id2')."';");
					echo 'Deleting link_id '.$db1->f('link_id2').' with type '.$db1->f('type2').'<br />';
				}			
			break;
			
			case '2':
				$sql = "SELECT id FROM ab_contacts WHERE link_id='".$db1->f('link_id2')."';";
				$db2->query($sql);
				if(!$db2->next_record())
				{
					$db2->query("DELETE FROM links WHERE type2='".$db1->f('type2')."' AND link_id2='".$db1->f('link_id2')."';");
					echo 'Deleting link_id '.$db1->f('link_id2').' with type '.$db1->f('type2').'<br />';
				}			
			break;
			
			case '3':
				$sql = "SELECT id FROM pmProjects WHERE link_id='".$db1->f('link_id2')."';";
				$db2->query($sql);
				if(!$db2->next_record())
				{
					$db2->query("DELETE FROM links WHERE type2='".$db1->f('type2')."' AND link_id2='".$db1->f('link_id2')."';");
					echo 'Deleting link_id '.$db1->f('link_id2').' with type '.$db1->f('type2').'<br />';
				}			
			break;
			
			case '4':
				$sql = "SELECT id FROM no_notes WHERE link_id='".$db1->f('link_id2')."';";
				$db2->query($sql);
				if(!$db2->next_record())
				{
					$db2->query("DELETE FROM links WHERE type2='".$db1->f('type2')."' AND link_id2='".$db1->f('link_id2')."';");
					echo 'Deleting link_id '.$db1->f('link_id2').' with type '.$db1->f('type2').'<br />';
				}			
			break;
			
			case '5':
				$sql = "SELECT id FROM no_notes WHERE link_id='".$db1->f('link_id2')."';";
				$db2->query($sql);
				if(!$db2->next_record())
				{
					$db2->query("DELETE FROM links WHERE type2='".$db1->f('type2')."' AND link_id2='".$db1->f('link_id2')."';");
					echo 'Deleting link_id '.$db1->f('link_id2').' with type '.$db1->f('type2').'<br />';
				}			
			break;
			
			case '6':
				$sql = "SELECT path FROM fs_links WHERE link_id='".$db1->f('link_id2')."';";
				$db2->query($sql);
				if(!$db2->next_record())
				{
					$db2->query("DELETE FROM links WHERE type2='".$db1->f('type2')."' AND link_id2='".$db1->f('link_id2')."';");
					echo 'Deleting link_id '.$db1->f('link_id2').' with type '.$db1->f('type2').'<br />';
				}			
			break;
			
			case '7':
				$sql = "SELECT id FROM bs_orders WHERE link_id='".$db1->f('link_id2')."';";
				$db2->query($sql);
				if(!$db2->next_record())
				{
					$db2->query("DELETE FROM links WHERE type2='".$db1->f('type2')."' AND link_id2='".$db1->f('link_id2')."';");
					echo 'Deleting link_id '.$db1->f('link_id2').' with type '.$db1->f('type2').'<br />';
				}			
			break;
		}
	}
	
	echo 'All done!';
}else
{
	echo 'Please log in as administrator to use this script';
}

?>
