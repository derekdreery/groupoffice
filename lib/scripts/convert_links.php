<?php
/**
 * @copyright Copyright &copy; Intermesh 2006
 * @version $Revision: 1.4 $ $Date: 2006/04/27 07:08:45 $
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

	if(isset($GO_MODULES->modules['projects']))
	{
		require_once($GO_MODULES->modules['projects']['class_path'].'projects.class.inc');
		$projects = new projects();
	}

	if(isset($GO_MODULES->modules['addressbook']))
	{
		require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
		$ab = new addressbook();
	}

	//start of note linking
	if(isset($GO_MODULES->modules['notes']))
	{
		$db1->query("SELECT * FROM no_notes WHERE project_id>0 OR company_id>0 OR contact_id>0");
		while($db1->next_record())
		{
			$note=array();	
			$note['link_id'] = $db1->f('link_id');
			if(empty($note['link_id']))
			{
				$note['id'] = $db1->f('id');
				$note['link_id'] = $GO_LINKS->get_link_id();
				$db2->update_row('no_notes', 'id', $note);
			}
			
			if($db1->f('project_id') > 0)
			{		
				$remote_link_type=5;		
				
				if($project = $projects->get_project($db1->f('project_id')))
				{	
					if($project['link_id'] < 1)
					{
						$update_project = array();
						$update_project['id'] = $db1->f('project_id');
						$update_project['link_id'] = $project['link_id'] = $GO_LINKS->get_link_id();
						$db2->update_row('pmProjects', 'id', $update_project);
					}
					$remote_link_id = $project['link_id'];			
				}
			}
			
			if($db1->f('contact_id') > 0)
			{		
				$remote_link_type=2;		
				
				if($contact = $ab->get_contact($db1->f('contact_id')))
				{	
					if($contact['link_id'] < 1)
					{
						$update_contact = array();
						$update_contact['id'] = $db1->f('contact_id');
						$update_contact['link_id'] = $contact['link_id'] = $GO_LINKS->get_link_id();
						$db2->update_row('ab_contacts', 'id', $update_contact);
					}
					$remote_link_id = $contact['link_id'];			
				}
			}
			
			if($db1->f('company_id') > 0)
			{		
				$remote_link_type=3;		
				
				if($company = $ab->get_company($db1->f('company_id')))
				{	
					if($company['link_id'] < 1)
					{
						$update_company = array();
						$update_company['id'] = $db1->f('company_id');
						$update_company['link_id'] = $company['link_id'] = $GO_LINKS->get_link_id();
						$db2->update_row('ab_companies', 'id', $update_company);
					}
					$remote_link_id = $company['link_id'];			
				}
			}		
			
			if($remote_link_id >0 && !$GO_LINKS->link_exists($note['link_id'], $remote_link_id))
			{
				$GO_LINKS->add_link($note['link_id'], 4, $remote_link_id, $remote_link_type);	
				echo 'Creating note link to type '.$remote_link_type.'<br />';
			}
		}
	}
	//End notes

	//Start events
	if(isset($GO_MODULES->modules['calendar']))
	{
		$db1->query("SELECT * FROM cal_events WHERE project_id>0 OR company_id>0 OR contact_id>0");
		while($db1->next_record())
		{
			$event=array();
			$event['link_id'] = $db1->f('link_id');		
			if(empty($event['link_id']))
			{
				$event['id'] = $db1->f('id');
				$event['link_id'] = $GO_LINKS->get_link_id();
				$db2->update_row('cal_events', 'id', $event);
			}
			
			if($db1->f('project_id') > 0)
			{		
				$remote_link_type=5;		
				
				if($project = $projects->get_project($db1->f('project_id')))
				{	
					if($project['link_id'] < 1)
					{
						$update_project = array();
						$update_project['id'] = $db1->f('project_id');
						$update_project['link_id'] = $project['link_id'] = $GO_LINKS->get_link_id();
						$db2->update_row('pmProjects', 'id', $update_project);
					}
					$remote_link_id = $project['link_id'];			
				}
			}
			
			if($db1->f('contact_id') > 0)
			{		
				$remote_link_type=2;		
				
				if($contact = $ab->get_contact($db1->f('contact_id')))
				{	
					if($contact['link_id'] < 1)
					{
						$update_contact = array();
						$update_contact['id'] = $db1->f('contact_id');
						$update_contact['link_id'] = $contact['link_id'] = $GO_LINKS->get_link_id();
						$db2->update_row('ab_contacts', 'id', $update_contact);
					}
					$remote_link_id = $contact['link_id'];			
				}
			}
			
			if($db1->f('company_id') > 0)
			{		
				$remote_link_type=3;		
				
				if($company = $ab->get_company($db1->f('company_id')))
				{	
					if($company['link_id'] < 1)
					{
						$update_company = array();
						$update_company['id'] = $db1->f('company_id');
						$update_company['link_id'] = $company['link_id'] = $GO_LINKS->get_link_id();
						$db2->update_row('ab_companies', 'id', $update_company);
					}
					$remote_link_id = $company['link_id'];			
				}
			}
			if($remote_link_id >0)
			{
				if($GO_LINKS->link_exists($event['link_id'], $remote_link_id))
				{
					$GO_LINKS->delete_link($event['link_id'], $remote_link_id);
				}
				$GO_LINKS->add_link($event['link_id'], 1, $remote_link_id, $remote_link_type);	
				echo 'Creating event link to type '.$remote_link_type.'<br />';
			}
		}
	}
	//End events

	//Start projects

	if(isset($GO_MODULES->modules['projects']))
	{
		$db1->query("SELECT * FROM pmProjects WHERE project_id>0 OR company_id>0 OR contact_id>0");
		while($db1->next_record())
		{
			$project=array();
			$project['link_id'] = $db1->f('link_id');		
			if(empty($project['link_id']))
			{
				$project['id'] = $db1->f('id');
				$project['link_id'] = $GO_LINKS->get_link_id();
				$db2->update_row('pmProjects', 'id', $project);
			}
			
			if($db1->f('project_id') > 0)
			{		
				$remote_link_type=5;		
				
				if($linked_project = $projects->get_project($db1->f('project_id')))
				{	
					if($linked_project['link_id'] < 1)
					{
						$update_project = array();
						$update_project['id'] = $db1->f('project_id');
						$update_project['link_id'] = $linked_project['link_id'] = $GO_LINKS->get_link_id();
						$db2->update_row('pmProjects', 'id', $update_project);
					}
					$remote_link_id = $linked_project['link_id'];			
				}
			}
			
			if($db1->f('contact_id') > 0)
			{		
				$remote_link_type=2;		
				
				if($contact = $ab->get_contact($db1->f('contact_id')))
				{	
					if($contact['link_id'] < 1)
					{
						$update_contact = array();
						$update_contact['id'] = $db1->f('contact_id');
						$update_contact['link_id'] = $contact['link_id'] = $GO_LINKS->get_link_id();
						$db2->update_row('ab_contacts', 'id', $update_contact);
					}
					$remote_link_id = $contact['link_id'];			
				}
			}
			
			if($db1->f('company_id') > 0)
			{		
				$remote_link_type=3;		
				
				if($company = $ab->get_company($db1->f('company_id')))
				{	
					if($company['link_id'] < 1)
					{
						$update_company = array();
						$update_company['id'] = $db1->f('company_id');
						$update_company['link_id'] = $company['link_id'] = $GO_LINKS->get_link_id();
						$db2->update_row('ab_companies', 'id', $update_company);
					}
					$remote_link_id = $company['link_id'];			
				}
			}

			if($remote_link_id >0)
			{
				if($GO_LINKS->link_exists($project['link_id'], $remote_link_id))
				{
					$GO_LINKS->delete_link($project['link_id'], $remote_link_id);
				}
				$GO_LINKS->add_link($project['link_id'], 5, $remote_link_id, $remote_link_type);	
				echo 'Creating project link to type '.$remote_link_type.'<br />';
			}		
		}
	}
	//End Projects

	echo 'All done!';
}else
{
	echo 'Please log in as administrator to use this script';
}
