<?php
/**
 * @copyright Copyright &copy; Intermesh 2006
 * @version $Revision: 1.10 $ $Date: 2006/11/28 10:42:43 $
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

 * @package Framework
 * @subpackage Links
 * @category Item linking
 */

/**
 * Functions to create links between items in Group-Office
 * 
 * This class provides functions to create links between items in Group-Office such as
 * tasks, projects, notes, appointments, files etc.
 *
 * Link types are static ints to improve perfomance. The table below is a type 
 * reference:
 *
 *	1=cal_events
 * 2=ab_contacts
 * 3=ab_companies
 * 4=no_notes
 * 5=pmProjects
 * 6=folders & files
 * 7=bs_orders
 * 8=tr_projects
 * 9=em_links
 * 
 * @package Framework
 * @subpackage GO_LINKS
 * @category Item linking
 * 
 * @access protected
 * 
 * @uses db
 */
class GO_LINKS extends db
{
	function GO_LINKS() 
	{
		$this->db();
	}
	
	function get_link_id()
	{
		return $this->nextid('links');
	}
	
	function activate_linking($link_id, $link_type, $description='', $return_to='')
	{
		$_SESSION['GO_SESSION']['link_type']=$link_type;
		$_SESSION['GO_SESSION']['link_id']=$link_id;		
		$_SESSION['GO_SESSION']['link_description']=$description;		
		$_SESSION['GO_SESSION']['link_return_to']= empty($return_to) ? $_SERVER['REQUEST_URI'] : $return_to;
	}
	
	function deactivate_linking()
	{
		unset($_SESSION['GO_SESSION']['link_description'],$_SESSION['GO_SESSION']['link_type'], $_SESSION['GO_SESSION']['link_id']);
	}
	
	function linking_is_active()
	{
		return isset($_SESSION['GO_SESSION']['link_id']);
	}
	
	function get_active_link()
	{
		if(isset($_SESSION['GO_SESSION']['link_id']))
		{
			$link['id']=$_SESSION['GO_SESSION']['link_id'];
			$link['type']=$_SESSION['GO_SESSION']['link_type'];
			$link['return_to']=$_SESSION['GO_SESSION']['link_return_to'];
			return $link;
		}else
		{
			return false;
		}
	}
	
	function search_link($link_id, $link_type, $opener_action='')
	{
		global $GO_CONFIG;
		
		return "javascript:popup('".$GO_CONFIG->control_url."select/global_select.php?multiselect=true&handler=".base64_encode($GO_CONFIG->control_url.'/select/link.php?link_id='.$link_id.'&link_type='.$link_type.'&opener_action='.base64_encode($opener_action))."','600','400');";
	}
	
	function add_link($id1, $type1, $id2, $type2)
	{
		$link['link_id1'] = $id1;
		$link['type1'] = $type1;
		$link['link_id2'] = $id2;
		$link['type2'] = $type2;
			
		$this->insert_row('links',$link);
	}	
	
	function link_exists($link_id1, $link_id2)
	{
		$sql = "SELECT * FROM links WHERE ".
			"(`link_id1`=$link_id1 AND `link_id2`=$link_id2) ".
			"OR ".
			"(`link_id1`=$link_id2 AND `link_id2`=$link_id1)";
		$this->query($sql);
		return $this->next_record();
	}
	
	function delete_link($link_id1, $link_id2=0)
	{		
		if($link_id1>0)
		{
			if($link_id2==0)
			{
				$sql = "DELETE FROM links WHERE link_id1=$link_id1 OR link_id2=$link_id1;";
			}else
			{
				$sql = "DELETE FROM links WHERE (link_id1=$link_id1 AND link_id2=$link_id2) OR (link_id2=$link_id1 AND link_id1=$link_id2);";
			}
		
			return $this->query($sql);
		}
		return false;
	}
	
	function has_links($link_id)
	{
		if($link_id > 0)
		{
			$sql = "SELECT * FROM links WHERE link_id1=$link_id OR link_id2=$link_id;";
			$this->query($sql);
			return $this->next_record();
		}
		return false;
	}
	
	function copy_links($src_link_id, $dst_link_id, $dst_link_type)
	{
		$GO_LINKS2 = new GO_LINKS();
		
		$links = $this->get_links($src_link_id);
		foreach($links as $link)
		{
			$GO_LINKS2->add_link	($dst_link_id, $dst_link_type, $link['link_id'], $link['type']);
		}
	}
	
	function get_links($link_id, $type=null)
	{		
		$links=array();
		if($link_id > 0)
		{
			if(isset($type))
			{
				$sql = "SELECT * FROM links WHERE (link_id1=$link_id AND type2=$type) OR (link_id2=$link_id AND type1=$type);";
				
				if($this->query($sql))
				{
					while($this->next_record())
					{
						if($this->f('link_id2') == $link_id)
						{
							$links[] = $this->f('link_id1');
						}else
						{
							$links[] = $this->f('link_id2');
						}
					}
				}
			}else
			{
				$sql = "SELECT * FROM links WHERE link_id1=$link_id OR link_id2=$link_id;";
				
				if($this->query($sql))
				{
					while($this->next_record())
					{
						if($this->f('link_id1') == $link_id)
						{
							$links[] = array('link_id'=>$this->f('link_id2'), 'type'=>$this->f('type2'));
						}else
						{
							$links[] = array('link_id'=>$this->f('link_id1'), 'type'=>$this->f('type1'));
						}
					}
				}
			}
		}
		return $links;
	}
}
